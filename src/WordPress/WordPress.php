<?php

namespace AlexanderOMara\FlarumWPUsers\WordPress;

/**
 * WordPress class.
 */
class WordPress {
	/**
	 * Database connection.
	 *
	 * @var Db|null
	 */
	protected /*?Db*/ $db = null;

	/**
	 * Logged in key.
	 *
	 * @var string
	 */
	protected /*string*/ $loggedInKey;

	/**
	 * Logged in salt.
	 *
	 * @var string
	 */
	protected /*string*/ $loggedInSalt;

	/**
	 * Nonce key.
	 *
	 * @var string|null
	 */
	protected /*?string*/ $nonceKey = null;

	/**
	 * Nonce salt.
	 *
	 * @var string|null
	 */
	protected /*?string*/ $nonceSalt = null;

	/**
	 * Cookie name.
	 *
	 * @var string|null
	 */
	protected /*?string*/ $cookieName = null;

	/**
	 * Login URL.
	 *
	 * @var string|null
	 */
	protected /*?string*/ $loginUrl = null;

	/**
	 * Profile URL.
	 *
	 * @var string|null
	 */
	protected /*?string*/ $profileUrl = null;

	/**
	 * WordPress class.
	 *
	 * @param string|null $dbHost Database host.
	 * @param string|null $dbUser Database user.
	 * @param string|null $dbPass Database password.
	 * @param string|null $dbName Database name.
	 * @param string|null $dbCharset Database charset.
	 * @param string|null $dbPrefix Database table prefix.
	 * @param string|null $loggedInKey Config value.
	 * @param string|null $loggedInSalt Config value.
	 * @param string|null $nonceKey Config value.
	 * @param string|null $nonceSalt Config value.
	 * @param string|null $cookieName Cookie name.
	 * @param string|null $loginUrl Login URL.
	 * @param string|null $profileUrl Profile URL.
	 */
	public function __construct(
		?string $dbHost,
		?string $dbUser,
		?string $dbPass,
		?string $dbName,
		?string $dbCharset,
		?string $dbPrefix,
		?string $loggedInKey,
		?string $loggedInSalt,
		?string $nonceKey,
		?string $nonceSalt,
		?string $cookieName,
		?string $loginUrl,
		?string $profileUrl
	) {
		if (
			$dbHost &&
			$dbUser &&
			$dbPass &&
			$dbName &&
			$dbCharset &&
			$dbPrefix
		) {
			$this->db = new Db(
				$dbHost,
				$dbUser,
				$dbPass,
				$dbName,
				$dbCharset,
				$dbPrefix
			);
		}
		$this->loggedInKey = $loggedInKey;
		$this->loggedInSalt = $loggedInSalt;
		$this->nonceKey = $nonceKey;
		$this->nonceSalt = $nonceSalt;
		$this->cookieName = $cookieName;
		$this->loginUrl = $loginUrl;
		$this->profileUrl = $profileUrl;
	}

	/**
	 * Get WordPress DB connection if configured.
	 *
	 * @return Db|null Db object or null.
	 */
	public function getDb(): ?Db {
		return $this->db;
	}

	/**
	 * Get WordPress nonce object if configured.
	 *
	 * @param string|null $wpUserID WordPress user ID for the nonce.
	 * @param string|null $wpCookie WordPress session cookie value.
	 * @return Nonce|null The WordPress nonce object or null.
	 */
	public function getNonce(?string $wpUserID, ?string $wpCookie): ?Nonce {
		if ($this->nonceKey && $this->nonceSalt) {
			return new Nonce(
				$this->nonceKey,
				$this->nonceSalt,
				$wpUserID,
				$wpCookie
			);
		}
		return null;
	}

	/**
	 * Get cookie name from the settings.
	 *
	 * @return string|null Cookie name if configured.
	 */
	public function getCookieName(): ?string {
		return $this->cookieName;
	}

	/**
	 * Get login URL from the settings.
	 *
	 * @param string|null $destination Destination redirect.
	 * @return string|null Login URL if configured.
	 */
	public function getLoginUrl(?string $destination = null): ?string {
		$url = $this->loginUrl;
		return $url ? Util::addQueryArgs($url, [
			'redirect_to' => $destination
		]) : null;
	}

	/**
	 * Get register URL from the settings.
	 *
	 * @param string|null $destination Destination redirect.
	 * @return string|null Register URL if configured.
	 */
	public function getRegisterUrl(?string $destination = null): ?string {
		$url = $this->getLoginUrl();
		return $url ? Util::addQueryArgs($url, [
			'action' => 'register',
			'redirect_to' => $destination
		]) : null;
	}

	/**
	 * Get logout URL from the settings.
	 *
	 * @param string|null $destination Destination redirect.
	 * @param string|null $wpUserID WordPress user ID for the nonce.
	 * @param string|null $wpCookie WordPress session cookie value.
	 * @return string|null Logout URL if configured.
	 */
	public function getLogoutUrl(
		?string $destination = null,
		?string $wpUserID = null,
		?string $wpCookie = null
	): ?string {
		$url = $this->getLoginUrl();
		if (!$url) {
			return null;
		}

		// Create a nonce object if configured.
		$nonce = $this->getNonce($wpUserID, $wpCookie);

		// Create URL with nonce if possible.
		return Util::addQueryArgs($url, [
			'action' => 'logout',
			'redirect_to' => $destination,
			'_wpnonce' => $nonce ? $nonce->create('log-out') : null
		]);
	}

	/**
	 * Get profile URL from the settings.
	 *
	 * @return string|null Profile URL if configured.
	 */
	public function getProfileUrl(): ?string {
		return $this->profileUrl;
	}

	/**
	 * Similar to get_user_by but returns an associative array.
	 *
	 * @param string $key Search field.
	 * @param string $value Search value.
	 * @return array|null Associative array if found or null.
	 */
	protected function getUserBy(string $key, string $value): ?array {
		$keyMap = [
			'id' => 'ID',
			'ID' => 'ID',
			'slug' => 'user_nicename',
			'email' => 'user_email',
			'login' => 'user_login'
		];
		$col = $keyMap[$key] ?? null;
		if (!$col) {
			throw new \Exception("Unknown user key: {$key}");
		}
		$tbl = $this->db->table('users');
		$stmt = $this->db->prepare(
			"SELECT * FROM `{$tbl}` WHERE `{$col}`=? LIMIT 1"
		);
		$stmt->execute([$value]);
		$row = $stmt->fetch();
		return $row ? $row : null;
	}

	/**
	 * Compatibile subset of WP_Session_Tokens->hash_token.
	 *
	 * @param string $token Session token.
	 * @return string Hashed token.
	 */
	protected function hashToken(string $token): string {
		return function_exists('hash') ? hash('sha256', $token) : sha1($token);
	}

	/**
	 * Compatibile subset of WP_User_Meta_Session_Tokens->get_sessions.
	 *
	 * @param string $userID User ID.
	 * @param string $verifier Session key.
	 * @return array Associative array of sessions.
	 */
	protected function sessionTokenGetSessions(
		string $userID,
		string $verifier
	): array {
		$tbl = $this->db->table('usermeta');
		$stmt = $this->db->prepare(
			"SELECT * FROM `{$tbl}` WHERE `user_id`=? AND `meta_key`=? LIMIT 1"
		);
		$stmt->execute([$userID, 'session_tokens']);
		$row = $stmt->fetch();
		$value = $row ? unserialize($row['meta_value']) : null;
		return is_array($value) ? $value : [];
	}

	/**
	 * Compatibile subset of WP_User_Meta_Session_Tokens->get_session.
	 *
	 * @param string $userID User ID.
	 * @param string $verifier Session key.
	 * @return array|null Associative array if valid or null.
	 */
	protected function sessionTokenGetSession(
		string $userID,
		string $verifier
	): ?array {
		$sessions = $this->sessionTokenGetSessions($userID, $verifier);
		return $sessions[$verifier] ?? null;
	}

	/**
	 * Compatibile subset of WP_Session_Tokens->verify.
	 *
	 * @param string $userID User ID.
	 * @param string $token Session token.
	 * @return bool True if user has session token.
	 */
	protected function sessionTokenVerify(
		string $userID,
		string $token
	): bool {
		$verifier = $this->hashToken($token);
		return (bool)$this->sessionTokenGetSession($userID, $verifier);
	}

	/**
	 * Get user for cookie, optionally have grace period for expiration.
	 * Validation compatible with subset of wp_validate_auth_cookie.
	 *
	 * @param string $cookie Cookie value.
	 * @param bool $hasGracePeriod Option to include the grace period.
	 * @return array|null Associative array if valid or null.
	 */
	public function validateAuthCookie(
		string $cookie,
		bool $hasGracePeriod
	): ?array {
		if (!$this->loggedInKey || !$this->loggedInSalt) {
			return null;
		}

		// Parse cookie if valid.
		$elements = Util::parseAuthCookie($cookie);
		if (!$elements) {
			return null;
		}

		// Extract the cookie components.
		$username = $elements['username'];
		$hmac = $elements['hmac'];
		$token = $elements['token'];
		$expired = $elements['expiration'];
		$expiration = $elements['expiration'];

		// If should have grace period add the 1 hour WP does.
		if ($hasGracePeriod) {
			$expired += 60 * 60 * 1;
		}

		// If the cookie does not even claim to be valid it is expired.
		if ($expired < time()) {
			return null;
		}

		// Look up the user in the WP database.
		$user = $this->getUserBy('login', $username);
		if (!$user) {
			return null;
		}

		// Generate the key and hash for this user and session.
		$key = Util::hash(implode('|', [
			$username,
			substr($user['user_pass'], 8, 4),
			$expiration,
			$token
		]), $this->loggedInKey . $this->loggedInSalt);
		$algo = strlen($hmac) === 64 ? 'sha256' : 'sha1';
		$hash = hash_hmac($algo, implode('|', [
			$username,
			$expiration,
			$token
		]), $key);

		// Check if the cookie hash matches.
		if (!hash_equals($hash, $hmac)) {
			return null;
		}

		// Verify session token in user meta.
		if (!$this->sessionTokenVerify($user['ID'], $token)) {
			return null;
		}

		return $user;
	}
}
