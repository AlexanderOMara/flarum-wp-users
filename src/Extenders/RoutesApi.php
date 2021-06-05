<?php

namespace AlexanderOMara\FlarumWPUsers\Extenders;

use Flarum\Extend\Routes;
use Flarum\Extension\Extension;
use Illuminate\Contracts\Container\Container;

use AlexanderOMara\FlarumWPUsers\Controller;

/**
 * RoutesApi class.
 */
class RoutesApi extends Routes {
	/**
	 * RoutesForum class.
	 */
	public function __construct() {
		parent::__construct('api');
	}

	/**
	 * Extend method.
	 *
	 * @param Container $container Container object.
	 * @param Extension|null $extension Extension object.
	 */
	public function extend(Container $container, Extension $extension = null) {
		$this->remove('forgot');
		$this->post(
			'/forgot',
			'forgot',
			Controller\ForgotPasswordController::class
		);
		return parent::extend($container, $extension);
	}
}
