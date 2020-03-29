<?php

namespace AlexanderOMara\FlarumWPUsers\Middleware;

use Flarum\Http\RouteCollection;

use AlexanderOMara\FlarumWPUsers\Controller;

/**
 * Forum Intercept middleware.
 */
class InterceptForum extends Intercept {
	/**
	 * Setup routes.
	 *
	 * @param RouteCollection $routes Routes collection.
	 */
	protected function routes(RouteCollection $routes): void {
		$routes->post(
			'/register',
			'register',
			$this->route->toController(Controller\RegisterController::class)
		);
	}
}
