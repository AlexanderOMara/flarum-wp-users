<?php

namespace AlexanderOMara\FlarumWPUsers\WordPress;

use AlexanderOMara\FlarumWPUsers\WordPress\Db;
use AlexanderOMara\FlarumWPUsers\WordPress\Util;

/**
 * WordPress session lookup class.
 */
class Session {
	/**
	 * Database connection.
	 */
	protected Db $db;

	/**
	 * Logged in key.
	 */
	protected string $loggedInKey;

	/**
	 * Logged in salt.
	 */
	protected string $loggedInSalt;

	/**
	 * WordPress session lookup class.
	 *
	 * @param Db $db Database connection.
	 * @param string $loggedInKey Logged in key.
	 * @param string $loggedInSalt Logged in salt.
	 */
	public function __construct(
		Db $db,
		string $loggedInKey,
		string $loggedInSalt
	) {
		$this->db = $db;
		$this->loggedInKey = $loggedInKey;
		$this->loggedInSalt = $loggedInSalt;
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
	public function getUser(string $cookie, bool $hasGracePeriod): ?array {
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
		$algo = function_exists('hash') ? 'sha256' : 'sha1';
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
