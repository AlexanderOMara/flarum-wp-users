<?php

namespace AlexanderOMara\FlarumWPUsers\Provider;

use Flarum\Foundation\AbstractServiceProvider;

use AlexanderOMara\FlarumWPUsers\Core;

/**
 * CoreProvider functionality.
 */
class CoreProvider extends AbstractServiceProvider {
	/**
	 * Register method.
	 */
	public function register() {
		$this->container->singleton(Core::class);
	}
}
