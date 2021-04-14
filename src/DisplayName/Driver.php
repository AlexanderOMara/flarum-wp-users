<?php

namespace AlexanderOMara\FlarumWPUsers\DisplayName;

use Flarum\User\DisplayName\DriverInterface;
use Flarum\User\User;

use AlexanderOMara\FlarumWPUsers\Core;

/**
 * Driver class.
 */
class Driver implements DriverInterface {
	/**
	 * Core object.
	 *
	 * @var Core
	 */
	protected /*Core*/ $core;

	/**
	 * Driver class.
	 *
	 * @param Core Core object.
	 */
	public function __construct(Core $core) {
		$this->core = $core;
	}

	/**
	 * Get display name for a user.
	 *
	 * @param User $user User object.
	 * @return string Display name.
	 */
	public function displayName(User $user): string {
		// Use WordPress display name if possible.
		$displayName = $this->core->getDisplayNameCached($user);
		return $displayName !== null ? $displayName : $user->username;
	}
}
