<?php

namespace AlexanderOMara\FlarumWPUsers\Controller;

use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

use AlexanderOMara\FlarumWPUsers\Core;
use AlexanderOMara\FlarumWPUsers\Response\NullResponse;

/**
 * Forgot password intercept controller.
 */
class ForgotPasswordController implements Handler {
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
			return new NullResponse();
		}

		// Ignore if no user with that email exists.
		$user = User::where(['email' => $email])->first();
		if (!$user) {
			return new NullResponse();
		}

		// Ignore if user is allowed to change password.
		if (Core::allowedChangeList($user)['password']) {
			return new NullResponse();
		}

		// User should not be able to change password.
		throw new PermissionDeniedException();
	}
}
