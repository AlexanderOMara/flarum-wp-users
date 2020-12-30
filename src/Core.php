<?php

namespace AlexanderOMara\FlarumWPUsers;

use Flarum\Frontend\Document;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Event\EmailChangeRequested;
use Flarum\User\LoginProvider;
use Flarum\User\User;
use Illuminate\Contracts\Session\Session;

use AlexanderOMara\FlarumWPUsers\WordPress;

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
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @return array
	 */
	protected static function userMap(
		SettingsRepositoryInterface $settings
	): array {
		return ['username' => [
					'key' => static::getUsernameColumn($settings), // default: user_login
					'setter' => 'rename',
					'conflict' => 'user-%s-%s'
				],
				'email' => [
					'key' => 'user_email',
					'setter' => 'changeEmail',
					'conflict' => 'user-%s-%s@0.0.0.0'
				]];
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

	/**
	 * Get setting value for this extension.
	 *
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @param string $key Setting key.
	 * @return string|null Setting value.
	 */
	public static function setting(
		SettingsRepositoryInterface $settings,
		string $key
	): ?string {
		return $settings->get(static::ID . '.' . $key);
	}

	/**
	 * Get setting values for this extension, or null if not all set.
	 *
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @param array $values Setting keys.
	 * @return array|null Setting values.
	 */
	public static function settings(
		SettingsRepositoryInterface $settings,
		array $values
	): ?array {
		$r = [];
		foreach ($values as $k) {
			$value = static::setting($settings, $k);
			if ($value === null) {
				return null;
			}
			$r[$k] = $value;
		}
		return $r;
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
	 * Get username column from the settings.
	 *
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @return string username column if configured, defaults to user_login.
	 */
	public static function getUsernameColumn(
		SettingsRepositoryInterface $settings
	): string {
		$col = static::setting($settings, 'username_col');
		return $col ?? 'user_login';
	}

	/**
	 * Get cookie name from the settings.
	 *
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @return string|null Cookie name if configured.
	 */
	public static function getCookieName(
		SettingsRepositoryInterface $settings
	): ?string {
		return static::setting($settings, 'cookie_name');
	}

	/**
	 * Get login URL from the settings.
	 *
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @param string|null $destination Destination redirect.
	 * @return string|null Login URL if configured.
	 */
	public static function getLoginUrl(
		SettingsRepositoryInterface $settings,
		?string $destination = null
	): ?string {
		$url = static::setting($settings, 'login_url');
		return $url ? WordPress\Util::addQueryArgs($url, [
			'redirect_to' => $destination
		]) : null;
	}

	/**
	 * Get register URL from the settings.
	 *
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @param string|null $destination Destination redirect.
	 * @return string|null Register URL if configured.
	 */
	public static function getRegisterUrl(
		SettingsRepositoryInterface $settings,
		?string $destination = null
	): ?string {
		$url = static::getLoginUrl($settings);
		return $url ? WordPress\Util::addQueryArgs($url, [
			'action' => 'register',
			'redirect_to' => $destination
		]) : null;
	}

	/**
	 * Get profile URL from the settings.
	 *
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @return string|null Profile URL if configured.
	 */
	public static function getProfileUrl(
		SettingsRepositoryInterface $settings
	): ?string {
		return static::setting($settings, 'profile_url');
	}

	/**
	 * Get logout URL from the settings.
	 *
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @param string|null $destination Destination redirect.
	 * @param string|null $wpUserID WordPress user ID for the nonce.
	 * @param string|null $wpCookie WordPress session cookie value.
	 * @return string|null Logout URL if configured.
	 */
	public static function getLogoutUrl(
		SettingsRepositoryInterface $settings,
		?string $destination = null,
		?string $wpUserID = null,
		?string $wpCookie = null
	): ?string {
		$url = static::getLoginUrl($settings);
		if (!$url) {
			return null;
		}

		// Create a nonce object if configured.
		$nonce = static::getWordPressNonce($settings, $wpUserID, $wpCookie);

		// Create URL with nonce if possible.
		return WordPress\Util::addQueryArgs($url, [
			'action' => 'logout',
			'redirect_to' => $destination,
			'_wpnonce' => $nonce ? $nonce->create('log-out') : null
		]);
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
	 * Get WordPress nonce object if configured.
	 *
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @param string|null $wpUserID WordPress user ID for the nonce.
	 * @param string|null $wpCookie WordPress session cookie value.
	 * @return WordPress\Nonce|null The WordPress nonce object or null.
	 */
	public static function getWordPressNonce(
		SettingsRepositoryInterface $settings,
		?string $wpUserID,
		?string $wpCookie
	): ?WordPress\Nonce {
		// Load all the settings if set.
		$opts = static::settings($settings, [
			'nonce_key',
			'nonce_salt'
		]);
		return $opts ? new WordPress\Nonce(
			$opts['nonce_key'],
			$opts['nonce_salt'],
			$wpUserID,
			$wpCookie
		) : null;
	}

	/**
	 * Get WordPress sessions object if configured.
	 *
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @return WordPress\Session Sessions utility or null.
	 */
	public static function getWordPressSession(
		SettingsRepositoryInterface $settings
	): ?WordPress\Session {
		// Load all the settings if set.
		$opts = static::settings($settings, [
			'db_host',
			'db_user',
			'db_pass',
			'db_name',
			'db_charset',
			'db_pre',
			'logged_in_key',
			'logged_in_salt'
		]);
		return $opts ? new WordPress\Session(
			new WordPress\Db(
				$opts['db_host'],
				$opts['db_user'],
				$opts['db_pass'],
				$opts['db_name'],
				$opts['db_charset'],
				$opts['db_pre']
			),
			$opts['logged_in_key'],
			$opts['logged_in_salt']
		) : null;
	}

	/**
	 * Get Flarum user for WordPress user, creating user if necessary.
	 * If an unmanaged user with the same email exists, user cannot be created.
	 * If new user cannot be created, null is returned.
	 *
	 * @param array $wpUser WordPress user.
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @return User|null Flarum user.
	 */
	public static function ensureUser(array $wpUser, SettingsRepositoryInterface $settings): ?User {
		// Lookup managed user if already exists.
		$user = LoginProvider::logIn(static::ID, $wpUser['ID']);

		// If no managed user, check for unmanaged user with the same email.
		if (!$user) {
			// If a match and unmanaged make it so and continue with it.
			$userSameEmail = User::where([
				'email' => $wpUser[static::userMap($settings)['email']['key']]
			])->first();
			if ($userSameEmail && !static::userManagedHas($userSameEmail)) {
				static::userManagedCreate($userSameEmail, $wpUser['ID']);
				$user = $userSameEmail;
			}
		}

		// Before updating or creating user ensure unique properties.
		// Change any dupes to keep the unique table column integrity.
		foreach (static::userMap($settings) as $k=>$v) {
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
			foreach (static::userMap($settings) as $k=>$v) {
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
			$wpUser[static::userMap($settings)['username']['key']],
			$wpUser[static::userMap($settings)['email']['key']],
			''
		);
		$user->activate();
		$user->save();
		static::userManagedCreate($user, $wpUser['ID']);
		return $user;
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
				$user->getOriginal('password') || !$managed
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
		$changeable = Core::allowedChangeList($user);

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
	 * Add payload to document.
	 *
	 * @param Document $view Document view.
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @param User $user User object.
	 */
	public static function addPayload(
		Document $view,
		SettingsRepositoryInterface $settings,
		?User $user
	): void {
		$view->payload[static::ID] = [
			'loginUrl' => static::getLoginUrl($settings),
			'registerUrl' => static::getRegisterUrl($settings),
			'profileUrl' => static::getProfileUrl($settings),
			'allowedChanges' => $user ? static::allowedChangeList($user) : null
		];
	}
}
