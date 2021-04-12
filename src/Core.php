<?php

namespace AlexanderOMara\FlarumWPUsers;

use Flarum\Frontend\Document;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Event\EmailChangeRequested;
use Flarum\User\LoginProvider;
use Flarum\User\User;
use Illuminate\Contracts\Session\Session;

use AlexanderOMara\FlarumWPUsers\WordPress\WordPress;

/**
 * Core functionality.
 */
class Core {
	/**
	 * Extension identifier.
	 *
	 * @var string
	 */
	public const ID = 'alexanderomara-wp-users';

	/**
	 * User property mappings with conflict fallback sprintf format.
	 *
	 * @var array
	 */
	protected static $userMap = [
		'username' => [
			'key' => 'user_login',
			'setter' => 'rename',
			'conflict' => 'user-%s-%s'
		],
		'email' => [
			'key' => 'user_email',
			'setter' => 'changeEmail',
			'conflict' => 'user-%s-%s@0.0.0.0'
		]
	];

	/**
	 * Settings object.
	 *
	 * @var SettingsRepositoryInterface
	 */
	protected /*SettingsRepositoryInterface*/ $settings;

	/**
	 * WordPress object.
	 *
	 * @var WordPress
	 */
	protected /*WordPress*/ $wp;

	/**
	 * Core functionality.
	 *
	 * @param SettingsRepositoryInterface $settings Settings object.
	 */
	public function __construct(SettingsRepositoryInterface $settings) {
		$this->settings = $settings;

		$this->wp = new WordPress(
			$this->setting('db_host'),
			$this->setting('db_user'),
			$this->setting('db_pass'),
			$this->setting('db_name'),
			$this->setting('db_charset'),
			$this->setting('db_pre'),
			$this->setting('logged_in_key'),
			$this->setting('logged_in_salt'),
			$this->setting('nonce_key'),
			$this->setting('nonce_salt'),
			$this->setting('cookie_name'),
			$this->setting('login_url'),
			$this->setting('profile_url')
		);
	}

	/**
	 * Get setting value for this extension.
	 *
	 * @param string $key Setting key.
	 * @return string|null Setting value.
	 */
	public function setting(string $key): ?string {
		return $this->settings->get(static::ID . '.' . $key);
	}

	/**
	 * The the WordPress object.
	 *
	 * @return WordPress WordPress object.
	 */
	public function getWP(): WordPress {
		return $this->wp;
	}

	/**
	 * Add payload to document.
	 *
	 * @param Document $view Document view.
	 * @param User $user User object.
	 */
	public function addPayload(Document $view, ?User $user): void {
		$view->payload[static::ID] = [
			'loginUrl' => $this->wp->getLoginUrl(),
			'registerUrl' => $this->wp->getRegisterUrl(),
			'profileUrl' => $this->wp->getProfileUrl(),
			'allowedChanges' => $user ? static::allowedChangeList($user) : null
		];
	}

	/**
	 * Get session value user ID key.
	 *
	 * @return string Setting key.
	 */
	public static function sessionUserIdKey(): string {
		return static::ID . '.user_id';
	}

	/**
	 * Get session user ID if set.
	 *
	 * @param Session $session Session object.
	 * @return int User ID or null.
	 */
	public static function sessionUserIdGet(Session $session): ?int {
		return $session->get(static::sessionUserIdKey());
	}

	/**
	 * Set session user ID.
	 *
	 * @param Session $session Session object.
	 * @param int|null $id User ID or null.
	 */
	public static function sessionUserIdSet(Session $session, ?int $id): void {
		if ($id === null) {
			$session->remove(static::sessionUserIdKey());
		}
		else {
			$session->put(static::sessionUserIdKey(), $id);
		}
	}

	/**
	 * Get Flarum user for WordPress user, creating user if necessary.
	 * If an unmanaged user with the same email exists, user cannot be created.
	 * If new user cannot be created, null is returned.
	 *
	 * @param array $wpUser WordPress user.
	 * @return User|null Flarum user.
	 */
	public static function ensureUser(array $wpUser): ?User {
		// Lookup managed user if already exists.
		$user = LoginProvider::logIn(static::ID, $wpUser['ID']);

		// If no managed user, check for unmanaged user with the same email.
		if (!$user) {
			// If a match and unmanaged make it so and continue with it.
			$userSameEmail = User::where([
				'email' => $wpUser[static::$userMap['email']['key']]
			])->first();
			if ($userSameEmail && !static::userManagedHas($userSameEmail)) {
				static::userManagedCreate($userSameEmail, $wpUser['ID']);
				$user = $userSameEmail;
			}
		}

		// Before updating or creating user ensure unique properties.
		// Change any dupes to keep the unique table column integrity.
		foreach (static::$userMap as $k=>$v) {
			$value = $wpUser[$v['key']];

			// If user already exists and property is unchanged, skip check.
			if ($user && $user->{$k} === $value) {
				continue;
			}

			// Check for user with dupe property.
			$dupe = User::where([$k => $value])->first();
			if (!$dupe) {
				continue;
			}

			// If account is not managed by this extension, cannot continue.
			if (!static::userManagedHas($dupe)) {
				return null;
			}

			// Replace with temporary and unique value.
			// The correct value will be set on their next login.
			$dupe->{$v['setter']}(
				sprintf($v['conflict'], $dupe->id, static::uid())
			);
			$dupe->save();
		}

		// If user exists, check for changes, and save if different.
		if ($user) {
			$changed = false;
			foreach (static::$userMap as $k=>$v) {
				if ($user->{$k} !== $wpUser[$v['key']]) {
					$user->{$v['setter']}($wpUser[$v['key']]);
					$changed = true;
				}
			}
			if ($changed) {
				$user->save();
			}
			return $user;
		}

		// Otherwise must register new user with empty password.
		// An empty password is an invalid hash and will not match.
		// Therefore all logins for that user must go through this extension.
		$user = User::register(
			$wpUser[static::$userMap['username']['key']],
			$wpUser[static::$userMap['email']['key']],
			''
		);
		$user->activate();
		$user->save();
		static::userManagedCreate($user, $wpUser['ID']);
		return $user;
	}

	/**
	 * Set identifier of user managed by this extension.
	 *
	 * @param User $user User object.
	 * @param string The WordPress user ID.
	 */
	public static function userManagedCreate(User $user, string $id): void {
		$user->loginProviders()->create([
			'provider' => static::ID,
			'identifier' => $id
		]);
	}

	/**
	 * Get identifier of user managed by this extension.
	 *
	 * @param User $user User object.
	 * @return string|null The WordPress user ID, or null if provider not set.
	 */
	public static function userManagedGet(User $user): ?string {
		$provider = !$user->isGuest() ?
			$user->loginProviders()->where('provider', static::ID)->first() :
			null;
		return $provider ? $provider->identifier : null;
	}

	/**
	 * Check if user managed by this extension.
	 *
	 * @param User $user User object.
	 * @return bool True if user managed.
	 */
	public static function userManagedHas(User $user): bool {
		return static::userManagedGet($user) !== null;
	}

	/**
	 * Get list of properties that are allowed to change for a user.
	 *
	 * @param User $user User object.
	 * @return array Properties list.
	 */
	public static function allowedChangeList(User $user): array {
		$guest = $user->isGuest();
		$managed = $guest ? false : static::userManagedHas($user);
		return [
			'email' => !$guest && !$managed,
			'username' => !$guest && !$managed,
			'password' => !$guest && (
				!$managed || $user->getOriginal('password')
			)
		];
	}

	/**
	 * Check user object for any changes not allowed.
	 * Gets original user to check against, else allowed.
	 *
	 * @param User $user User object.
	 * @return bool True if no disallowed changes to the user.
	 */
	public static function allowedChangeCheck(User $user): bool {
		$changeable = static::allowedChangeList($user);

		// Check for any values not supposed to change but did.
		foreach ($changeable as $k=>$v) {
			if (!$v && $user->{$k} !== $user->getOriginal($k)) {
				return false;
			}
		}

		// If email cannot change, check for EmailChangeRequested events.
		// This event will defer changing the email value.
		if (!$changeable['email']) {
			// Read all the events, and add them back.
			$events = $user->releaseEvents();
			foreach ($events as $event) {
				$user->raise($event);
			}

			// If an EmailChangeRequested event, not allowed.
			foreach ($events as $event) {
				if ($event instanceof EmailChangeRequested) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Generate unique ID with 128 strong pseudo-random bits.
	 *
	 * @return string Unique ID.
	 */
	protected static function uid() {
		// The openssl extension is also a Flarum dependency.
		return bin2hex(openssl_random_pseudo_bytes(16));
	}
}
