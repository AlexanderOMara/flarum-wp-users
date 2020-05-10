<?php

namespace AlexanderOMara\FlarumWPUsers\Middleware;

use Flarum\Http\SessionAuthenticator;
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
	 */
	protected /*EventsDispatcher*/ $events;

	/**
	 * Authenticator object.
	 */
	protected /*SessionAuthenticator*/ $authenticator;

	/**
	 * Settings object.
	 */
	protected /*SettingsRepositoryInterface*/ $settings;

	/**
	 * LoggedOut user.
	 */
	protected /*?User*/ $loggedOut = null;

	/**
	 * Authentication middleware.
	 *
	 * @param EventsDispatcher $events Events dispatcher.
	 * @param SessionAuthenticator $authenticator Authenticator object.
	 * @param SettingsRepositoryInterface $settings Settings object.
	 */
	public function __construct(
		EventsDispatcher $events,
		SessionAuthenticator $authenticator,
		SettingsRepositoryInterface $settings
	) {
		$this->events = $events;
		$this->authenticator = $authenticator;
		$this->settings = $settings;
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
		$actor = $request->getAttribute('actor');
		$session = $request->getAttribute('session');

		// If Flarum does not recognize user, try to authenticate here.
		$wpActor = (!$actor || $actor->isGuest()) ?
			$this->getActor($request) :
			null;
		if ($wpActor) {
			// Set the session on the actor and replace the actor.
			// Do not set user_id on the session object.
			// That would make authentication persist in Flarum itself.
			// Which would prevent logout when WP cookie gone.
			// Based on AuthenticateWithSession->process().
			$wpActor->setSession($session);
			$request = $request->withAttribute('actor', $wpActor);
		}

		// If user changes then clear session so that nothing lingers.
		if ($session) {
			// If not a managed user, user ID should be null.
			$sessionUserId = $wpActor ? $wpActor->id : null;
			if ($sessionUserId !== Core::sessionUserIdGet($session)) {
				$this->authenticator->logOut($session);
				if ($sessionUserId !== null) {
					// If a managed user, run login, but with a null user ID.
					// This avoids setting user_id on the session object.
					// That would make authentication persist in Flarum itself.
					// Which would prevent logout when WP cookie gone.
					$this->authenticator->logIn($session, null);
				}
				Core::sessionUserIdSet($session, $sessionUserId);
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

		// If user not manager, no need to intercept.
		$wpUserID = Core::userManagedGet($user);
		if ($wpUserID === null) {
			return $response;
		}

		// Find location to go after logout (redirect or same location).
		$destination = $response->getHeader('location')[0] ??
			$request->getUri()->__toString();

		// Get the logout URL if configured, else no way to intercept it.
		$logoutUrl = Core::getLogoutUrl(
			$this->settings,
			$destination,
			$wpUserID,
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
		$wpUser = $this->getWordPressUser($cookie, $hasGracePeriod);
		if (!$wpUser) {
			return null;
		}

		// Get the Flarum user for the WP user if possible.
		$actor = Core::ensureUser($wpUser);

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
		$cookieName = Core::getCookieName($this->settings);
		return $cookieName ?
			($request->getCookieParams()[$cookieName] ?? null) :
			null;
	}

	/**
	 * Get WordPress user from session cookie if configured.
	 *
	 * @param string $cookie Cookie value.
	 * @param bool $hasGracePeriod Should include grace period.
	 * @return array|null The WordPress user data or null.
	 */
	protected function getWordPressUser(
		string $cookie,
		bool $hasGracePeriod
	): ?array {
		$wps = Core::getWordPressSession($this->settings);
		return $wps ? $wps->getUser($cookie, $hasGracePeriod) : null;
	}

	/**
	 * Check if a request has expiration grade period.
	 *
	 * @param Request $request Request object.
	 * @return bool True of request should have the grace period.
	 */
	protected function requestHasGracePeriod(Request $request): bool {
		return in_array(strtoupper($request->getMethod()), ['GET', 'HEAD']);
	}
}
