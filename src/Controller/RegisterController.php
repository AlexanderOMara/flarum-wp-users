<?php

namespace AlexanderOMara\FlarumWPUsers\Controller;

use Flarum\Forum\Controller\RegisterController as Base;
use Flarum\User\Exception\PermissionDeniedException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Register intercept controller.
 */
class RegisterController extends Base {
	/**
	 * Request handler.
	 *
	 * @param Request $request Request object.
	 * @return Response Response object.
	 */
	public function handle(Request $request): Response {
		// Users should not be able to register.
		throw new PermissionDeniedException();
	}
}
