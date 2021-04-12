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
	 * Session object.
	 *
	 * @var Session|null
	 */
	protected /*?Session*/ $session = null;

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
		if ($this->db && $loggedInKey && $loggedInSalt) {
			$this->session = new Session(
				$this->db,
				$loggedInKey,
				$loggedInSalt
			);
		}
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
	 * Get WordPress session object if configured.
	 *
	 * @return Session|null Session object or null.
	 */
	public function getSession(): ?Session {
		return $this->session;
	}

	/**
	 * Get WordPress nonce object if configured.
	 *
	 * @param string|null $wpUserID WordPress user ID for the nonce.
	 * @param string|null $wpCookie WordPress session cookie value.
	 * @return Nonce|null The WordPress nonce object or null.
	 */
	public function getNonce(
		?string $wpUserID,
		?string $wpCookie
	): ?Nonce {
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
}
