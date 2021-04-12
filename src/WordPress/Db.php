<?php

namespace AlexanderOMara\FlarumWPUsers\WordPress;

use PDO;

/**
 * WordPress database class.
 */
class Db extends PDO {
	/**
	 * Table prefix.
	 *
	 * @var string
	 */
	protected /*string*/ $prefix;

	/**
	 * WordPress database class.
	 *
	 * @param string $host Database host.
	 * @param string $user Database user.
	 * @param string $pass Database password.
	 * @param string $name Database name.
	 * @param string $charset Database charset.
	 * @param string $prefix Database table prefix.
	 */
	public function __construct(
		string $host,
		string $user,
		string $pass,
		string $name,
		string $charset,
		string $prefix
	) {
		parent::__construct(
			implode(';', [
				"mysql:host={$host}",
				"dbname={$name}",
				"charset={$charset}"
			]),
			$user,
			$pass,
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
			]
		);
		$this->prefix = $prefix;
	}

	/**
	 * Get prefixed table name.
	 *
	 * @param string $name Table name
	 * @return string Table name with prefix.
	 */
	public function table(string $name): string {
		return "{$this->prefix}{$name}";
	}
}
