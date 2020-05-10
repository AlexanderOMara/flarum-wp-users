<?php

namespace AlexanderOMara\FlarumWPUsers\Middleware;

use FastRoute\Dispatcher;
use Flarum\Http\RouteCollection;
use Flarum\Http\RouteHandlerFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

use AlexanderOMara\FlarumWPUsers\Response\NullResponse;

/**
 * Intercept middleware.
 */
class Intercept implements Middleware {
	/**
	 * Route object.
	 */
	protected /*RouteHandlerFactory*/ $route;

	/**
	 * Dispatcher object.
	 */
	protected /*?Dispatcher*/ $dispatcher = null;

	/**
	 * Intercept middleware.
	 *
	 * @param RouteHandlerFactory $route Route object.
	 */
	public function __construct(RouteHandlerFactory $route) {
		$this->route = $route;
	}

	/**
	 * Process method.
	 *
	 * @param Request $request Request object.
	 * @param Handler $handler Request handler.
	 * @return Response Response object.
	 */
	public function process(Request $request, Handler $handler): Response {
		// Maybe hijack the response with some custom controllers.
		return $this->maybeHijack($request) ?: $handler->handle($request);
	}

	/**
	 * Setup routes.
	 *
	 * @param RouteCollection $routes Routes collection.
	 */
	protected function routes(RouteCollection $routes): void {
		// Subclass to add routes.
	}

	/**
	 * Maybe hijack response controller.
	 *
	 * @param Request $request Request object.
	 * @return Response|null Response object or null.
	 */
	protected function maybeHijack(Request $request): ?Response {
		$response = null;

		// Routing logic similar to DispatchRoute.
		// Get the method and URI, dispatch, and handle if found.
		$method = $request->getMethod();
		$uri = $request->getUri()->getPath() ?: '/';
		$routeInfo = $this->getDispatcher()->dispatch($method, $uri);
		if ($routeInfo[0] === Dispatcher::FOUND) {
			$handler = $routeInfo[1];
			$parameters = $routeInfo[2];
			$response = $handler($request, $parameters);
		}

		// If a null response, return null.
		return ($response instanceof NullResponse) ? null : $response;
	}

	/**
	 * Get the route dispatcher.
	 *
	 * @return Dispatcher\GroupCountBased Route dispatcher.
	 */
	protected function getDispatcher(): Dispatcher\GroupCountBased {
		if (!$this->dispatcher) {
			$this->dispatcher = new Dispatcher\GroupCountBased(
				$this->createRoutes()->getRouteData()
			);
		}
		return $this->dispatcher;
	}

	/**
	 * Create the route collection.
	 *
	 * @return RouteCollection Route collection.
	 */
	protected function createRoutes(): RouteCollection {
		$routes = new RouteCollection();
		$this->routes($routes);
		return $routes;
	}
}
