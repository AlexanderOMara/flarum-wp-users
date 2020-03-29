<?php

namespace AlexanderOMara\FlarumWPUsers\Middleware;

use Flarum\Http\RouteCollection;

use AlexanderOMara\FlarumWPUsers\Controller;

/**
 * InterceptApi middleware.
 */
class InterceptApi extends Intercept {
	/**
	 * Setup routes.
	 *
	 * @param RouteCollection $routes Routes collection.
	 */
	protected function routes(RouteCollection $routes): void {
		$routes->post(
			'/forgot',
			'forgot',
			$this->route->toController(
				Controller\ForgotPasswordController::class
			)
		);
	}
}
