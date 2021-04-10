<?php

namespace AlexanderOMara\FlarumWPUsers\Extenders;

use Flarum\Extend\Routes;
use Flarum\Extension\Extension;
use Illuminate\Contracts\Container\Container;

use AlexanderOMara\FlarumWPUsers\Controller;

/**
 * RoutesForum class.
 */
class RoutesForum extends Routes {
	/**
	 * RoutesForum class.
	 */
	public function __construct() {
		parent::__construct('forum');
	}

	/**
	 * Extend method.
	 *
	 * @param Container $container Container object.
	 * @param Extension|null $extension Extension object.
	 */
	public function extend(Container $container, Extension $extension = null) {
		$this->remove('POST', 'register');
		$this->post(
			'/register',
			'register',
			Controller\RegisterController::class
		);
		return parent::extend($container, $extension);
	}
}
