<?php

namespace AlexanderOMara\FlarumWPUsers\WordPress;

/**
 * WordPress utility class.
 */
class Util {
	/**
	 * Similar to add_query_arg but much simpler.
	 *
	 * @param string $uri Original URI.
	 * @param array  $args New arguments.
	 * @return string Updated URI.
	 */
	public static function addQueryArgs(string $uri, array $args): string {
		$query = http_build_query($args);
		if ($query === '') {
			return $uri;
		}
		$hashI = strpos($uri, '#');
		$hash = $hashI !== false ? substr($uri, $hashI) : '';
		$base = $hashI !== false ? substr($uri, 0, $hashI) : $uri;
		$pre = strpos($base, '?') === false ? '?' : '&';
		if (strlen($base) && substr($base, strlen($base) - 1, 1) === '&') {
			$pre = '';
		}
		return "{$base}{$pre}{$query}{$hash}";
	}

	/**
	 * Compatibile subset of wp_hash which takes salt directly.
	 *
	 * @param string $data Data to be hashed.
	 * @param string $salt Hash salt.
	 * @return string Hashed data.
	 */
	public static function hash(string $data, string $salt): string {
		return hash_hmac('md5', $data, $salt);
	}

	/**
	 * Compatibile subset of wp_parse_auth_cookie.
	 *
	 * @param string $cookie Cookie data.
	 * @return array|null Associative array if valid or null.
	 */
	public static function parseAuthCookie(string $cookie): ?array {
		$elements = explode('|', $cookie);
		return count($elements) === 4 ? [
			'username' => $elements[0],
			'expiration' => $elements[1],
			'token' => $elements[2],
			'hmac' => $elements[3]
		] : null;
	}
}
