<?php

namespace AlexanderOMara\FlarumWPUsers\WordPress;

/**
 * WordPress nonce class.
 */
class Nonce {
	/**
	 * Nonce key.
	 *
	 * @var string
	 */
	protected /*string*/ $nonceKey;

	/**
	 * Nonce salt.
	 *
	 * @var string
	 */
	protected /*string*/ $nonceSalt;

	/**
	 * User ID.
	 *
	 * @var string|null
	 */
	protected /*?string*/ $userId;

	/**
	 * Session cookie.
	 *
	 * @var string|null
	 */
	protected /*?string*/ $cookie;

	/**
	 * WordPress nonce class.
	 *
	 * @param string $nonceKey Nonce key.
	 * @param string $nonceSalt Nonce salt.
	 * @param string|null $userId User ID.
	 * @param string|null $cookie Session cookie.
	 */
	public function __construct(
		string $nonceKey,
		string $nonceSalt,
		?string $userId,
		?string $cookie
	) {
		$this->nonceKey = $nonceKey;
		$this->nonceSalt = $nonceSalt;
		$this->userId = $userId;
		$this->cookie = $cookie;
	}

	/**
	 * Get current user ID, if null default zero.
	 *
	 * @return string User ID.
	 */
	protected function getUserId(): string {
		return $this->userId ?? '0';
	}

	/**
	 * Compatibile subset of wp_get_session_token.
	 *
	 * @return string Nonce token.
	 */
	protected function getSessionToken(): string {
		$cookie = $this->cookie ? Util::parseAuthCookie($this->cookie) : null;
		return !empty($cookie['token']) ? $cookie['token'] : '';
	}

	/**
	 * Compatibile subset of wp_nonce_tick.
	 *
	 * @return string Nonce tick.
	 */
	protected function nonceTick(): float {
		$life = 60 * 60 * 24;
		return (float)ceil(time() / ($life / 2));
	}

	/**
	 * Compatibile subset of wp_create_nonce.
	 *
	 * @param int|string $action A value to add context to the nonce.
	 * @return string Nonce token.
	 */
	public function create($action = -1): string {
		$uid = (int)$this->getUserId();
		$token = $this->getSessionToken();
		$tick = $this->nonceTick();
		return substr(Util::hash(
			implode('|', [$tick, $action, $uid, $token]),
			$this->nonceKey . $this->nonceSalt
		), -12, 10);
	}
}
