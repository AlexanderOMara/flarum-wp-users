<?php
namespace AlexanderOMara\FlarumWPUsers\Compat\Extend;

use Flarum\Extend\Middleware as ExtendMiddleware;
use Flarum\Extension\Extension;
use Illuminate\Contracts\Container\Container;

/**
 * Backported Beta 13 insertBefore and insertAfter compatibility layer.
 */
class Middleware extends ExtendMiddleware {
	private $_frontent;
	private $_insertBeforeMiddlewares = [];
	private $_insertAfterMiddlewares = [];

	public function __construct(string $frontend) {
		parent::__construct($frontend);

		$this->_frontend = $frontend;
	}

	public function insertBefore($originalMiddleware, $newMiddleware) {
		$this->_insertBeforeMiddlewares[$originalMiddleware] = $newMiddleware;
		return $this;
	}

	public function insertAfter($originalMiddleware, $newMiddleware) {
		$this->_insertAfterMiddlewares[$originalMiddleware] = $newMiddleware;
		return $this;
	}

	public function extend(Container $container, Extension $extension = null) {
		parent::extend($container, $extension);

		$container->extend("flarum.{$this->_frontend}.middleware", function (
			$existingMiddleware
		) {
			foreach (
				$this->_insertBeforeMiddlewares as
				$originalMiddleware => $newMiddleware
			) {
				array_splice(
					$existingMiddleware,
					array_search($originalMiddleware, $existingMiddleware),
					0,
					$newMiddleware
				);
			}

			foreach (
				$this->_insertAfterMiddlewares as
				$originalMiddleware => $newMiddleware
			) {
				array_splice(
					$existingMiddleware,
					array_search($originalMiddleware, $existingMiddleware) + 1,
					0,
					$newMiddleware
				);
			}

			return $existingMiddleware;
		});
	}
}
