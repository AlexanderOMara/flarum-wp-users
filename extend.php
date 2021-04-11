<?php

use Flarum\Extend;
use Flarum\Http\Middleware as HttpMiddleware;
use Illuminate\Contracts\Events\Dispatcher;

use AlexanderOMara\FlarumWPUsers\Extenders;
use AlexanderOMara\FlarumWPUsers\Listener;
use AlexanderOMara\FlarumWPUsers\Middleware;
use AlexanderOMara\FlarumWPUsers\Provider;

return [
	// Register core class as a service (a singleton).
	(new Extend\ServiceProvider())
		->register(Provider\CoreProvider::class),

	// Client-side code.
	(new Extend\Frontend('forum'))
		->js(__DIR__ . '/js/dist/forum.js')
		->content(Listener\AddData::class),
	(new Extend\Frontend('admin'))
		->js(__DIR__ . '/js/dist/admin.js')
		->content(Listener\AddData::class),

	// Middleware.
	(new Extend\Middleware('forum'))
		->insertAfter(
			HttpMiddleware\AuthenticateWithSession::class,
			Middleware\Authenticate::class
		),
	(new Extend\Middleware('admin'))
		->insertAfter(
			HttpMiddleware\AuthenticateWithSession::class,
			Middleware\Authenticate::class
		),
	(new Extend\Middleware('api'))
		->insertAfter(
			HttpMiddleware\AuthenticateWithHeader::class,
			Middleware\Authenticate::class
		),

	// Extenders.
	new Extenders\RoutesApi(),
	new Extenders\RoutesForum(),

	// Events.
	function (Dispatcher $events) {
		$events->subscribe(Listener\AddUserSaving::class);
	}
];
