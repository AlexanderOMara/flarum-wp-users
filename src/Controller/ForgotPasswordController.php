<?php

namespace AlexanderOMara\FlarumWPUsers\Controller;

use Flarum\Api\Controller\ForgotPasswordController as Base;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use AlexanderOMara\FlarumWPUsers\Core;

/**
 * Forgot password intercept controller.
 */
class ForgotPasswordController extends Base {
	/**
	 * Request handler.
	 *
	 * @param Request $request Request object.
	 * @return Response Response object.
	 */
	public function handle(Request $request): Response {
		// Ignore if no email was passed.
		$email = Arr::get($request->getParsedBody(), 'email');
		if ($email === null) {
			return parent::handle($request);
		}

		// Ignore if no user with that email exists.
		$user = User::where(['email' => $email])->first();
		if (!$user) {
			return parent::handle($request);
		}

		// Ignore if user is allowed to change password.
		if (Core::allowedChangeList($user)['password']) {
			return parent::handle($request);
		}

		// User should not be able to change password.
		throw new PermissionDeniedException();
	}
}
