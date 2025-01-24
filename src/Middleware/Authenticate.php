<?php

namespace AlexanderOMara\FlarumWPUsers\Middleware;

use Flarum\Http\AccessToken;
use Flarum\Http\RequestUtil;
use Flarum\Http\SessionAuthenticator;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Event\LoggedOut;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Laminas\Diactoros\Response\RedirectResponse;

use AlexanderOMara\FlarumWPUsers\Core;

/**
 * Authentication middleware.
 */
class Authenticate implements Middleware {
	/**
	 * Events object.
	 *
	 * @var EventsDispatcher
	 */
	protected /*EventsDispatcher*/ $events;

	/**
	 * Authenticator object.
	 *
	 * @var SessionAuthenticator
	 */
	protected /*SessionAuthenticator*/ $authenticator;

	/**
	 * Settings object.
	 *
	 * @var SettingsRepositoryInterface
	 */
	protected /*SettingsRepositoryInterface*/ $settings;

	/**
	 * UrlGenerator object.
	 *
	 * @var UrlGenerator
	 */
	protected /*UrlGenerator*/ $url;

	/**
	 * Core object.
	 *
	 * @var Core
	 */
	protected /*Core*/ $core;

	/**
	 * LoggedOut user.
	 *
	 * @var User|null
	 */
	protected /*?User*/ $loggedOut = null;

	/**
	 * Authentication middleware.
	 *
	 * @param EventsDispatcher $events Events dispatcher.
	 * @param SessionAuthenticator $authenticator Authenticator object.
	 * @param SettingsRepositoryInterface $settings Settings object.
	 * @param UrlGenerator $url URL object.
	 * @param Core $core Core object.
	 */
	public function __construct(
		EventsDispatcher $events,
		SessionAuthenticator $authenticator,
		SettingsRepositoryInterface $settings,
		UrlGenerator $url,
		Core $core
	) {
		$this->events = $events;
		$this->authenticator = $authenticator;
		$this->settings = $settings;
		$this->url = $url;
		$this->core = $core;
	}

	/**
	 * LoggedOut listener.
	 *
	 * @param LoggedOut $event LoggedOut event.
	 */
	public function onLoggedOut(LoggedOut $event): void {
		$this->loggedOut = $event->user;
	}

	/**
	 * Process method.
	 *
	 * @param Request $request Request object.
	 * @param Handler $handler Request handler.
	 * @return Response Response object.
	 */
	public function process(Request $request, Handler $handler): Response {
		$actor = RequestUtil::getActor($request);

		// If Flarum does not recognize user, try to authenticate here.
		$wpActor = $actor->isGuest() ? $this->getActor($request) : null;
		if ($wpActor) {
			// Replace the request actor with a managed user.
			// Based on AuthenticateWithSession->process().
			$request = RequestUtil::withActor($request, $wpActor);
			$session = $request->getAttribute('session');

			// Clear session if not tied to the current managed user.
			// Run the logout and login, but with dummy access token.
			// A real token would validate with Flarum directly.
			// That would make authentication persist in Flarum itself.
			// That would prevent logout when WP cookie gone.
			if (
				$session &&
				Core::sessionUserIdGet($session) !== $wpActor->id
			) {
				$this->authenticator->logOut($session);
				$this->authenticator->logIn($session, new AccessToken());
				Core::sessionUserIdSet($session, $wpActor->id);
			}
		}
		else {
			// Actor is a recognized Flarum user, including guest.
			$session = $request->getAttribute('session');

			// Clear session if tied to a different user.
			$sessionId = $session ? Core::sessionUserIdGet($session) : null;
			if (
				$sessionId !== null &&
				$sessionId !== ($actor->isGuest() ? null : $actor->id)
			) {
				$this->authenticator->logOut($session);
				Core::sessionUserIdSet($session, null);
			}
		}

		// Run the handler, but listen for any logouts.
		$this->loggedOut = null;
		$this->events->listen(LoggedOut::class, [$this, 'onLoggedOut']);
		return $this->maybeRedirectLogout($request, $handler->handle($request));
	}

	/**
	 * Maybe redirect response for logout response.
	 *
	 * @param Request $request Request object.
	 * @param Response $response Response object.
	 * @return Response Response object.
	 */
	protected function maybeRedirectLogout(
		Request $request,
		Response $response
	): Response {
		// If no user logout, no need to intercept.
		$user = $this->loggedOut;
		if (!$user) {
			return $response;
		}
		$this->loggedOut = null;

		// If user not managed, no need to intercept.
		$wpUserId = Core::userManagedGet($user);
		if ($wpUserId === null) {
			return $response;
		}

		// Find location to go after logout (redirect or same location).
		$destination = $response->getHeader('location')[0] ??
			$request->getUri()->__toString();

		// If redirecting to forum, make sure slash included.
		// This avoids an extra redirect hop.
		$forumUrlGen = $this->url->to('forum');
		if ($destination === $forumUrlGen->base()) {
			$destination = $forumUrlGen->path('');
		}

		// Get the logout URL if configured, else no way to intercept it.
		$logoutUrl = $this->core->getWP()->getLogoutUrl(
			$destination,
			$wpUserId,
			$this->getCookie($request)
		);
		if (!$logoutUrl) {
			return $response;
		}

		// Replace response with redirect to the logout URL.
		return new RedirectResponse($logoutUrl);
	}

	/**
	 * Get actor for request if WordPress account validates.
	 *
	 * @param Request $request Request object.
	 * @return User|null User object or null.
	 */
	protected function getActor(Request $request): ?User {
		// Get the WP cookie if configured and set.
		$cookie = $this->getCookie($request);
		if (!$cookie) {
			return null;
		}

		// Add an expiration grace period for some requests.
		$hasGracePeriod = $this->requestHasGracePeriod($request);

		// Lookup the WP user by session if configured and valid.
		$wpUser = $this->core->getWP()->validateAuthCookie(
			$cookie,
			$hasGracePeriod
		);
		if (!$wpUser) {
			return null;
		}

		// Get the Flarum user for the WP user if possible.
		$actor = $this->core->ensureUser($wpUser);

		// Update the last seen time on the actor if exists.
		// Based on AuthenticateWithSession->getActor().
		if ($actor && $actor->exists) {
			$actor->updateLastSeen()->save();
		}
		return $actor;
	}

	/**
	 * Get cookie value from the request if configured and set.
	 *
	 * @param Request $request Request object.
	 * @return string|null Cookie value or null.
	 */
	protected function getCookie(Request $request): ?string {
		$cookieName = $this->core->getWP()->getCookieName();
		if (!$cookieName) {
			return null;
		}
		$cookie = $request->getCookieParams()[$cookieName] ?? null;
		return $cookie ? $this->core->decodeCookie($cookie) : null;
	}

	/**
	 * Check if a request has expiration grace period.
	 *
	 * @param Request $request Request object.
	 * @return bool True of request should have the grace period.
	 */
	protected function requestHasGracePeriod(Request $request): bool {
		return in_array(strtoupper($request->getMethod()), ['GET', 'HEAD']);
	}
}
