<?php

namespace AlexanderOMara\FlarumWPUsers\Listener;

use Flarum\User\Event\Saving;
use Illuminate\Contracts\Events\Dispatcher;

use AlexanderOMara\FlarumWPUsers\Core;

/**
 * User Saving event hook.
 */
class AddUserSaving {
	/**
	 * Subscribe handler.
	 *
	 * @param Saving $events Events dispatcher.
	 */
	public function subscribe(Dispatcher $events): void {
		$events->listen(Saving::class, [$this, 'onSaving']);
	}

	/**
	 * Saving callback.
	 *
	 * @param Saving $event Saving event.
	 */
	public function onSaving(Saving $event): void {
		// If no id set, user is being registered.
		if ($event->user->id === null) {
			$this->onSavingRegistering($event);
		}
		else {
			$this->onSavingUpdating($event);
		}
	}

	/**
	 * Saving callback, on registering.
	 *
	 * @param Saving $event Saving event.
	 */
	protected function onSavingRegistering(Saving $event): void {
		// Only an admin may register accounts bypassing extension.
		// What RegisterUserHandler would do if allow_sign_up is false.
		$event->actor->assertAdmin();
	}

	/**
	 * Saving callback, on updating.
	 *
	 * @param Saving $event Saving event.
	 */
	protected function onSavingUpdating(Saving $event): void {
		// A managed user should not have their managed properties edited.
		// Only an admin may change those managed properties.
		if (!Core::allowedChangeCheck($event->user)) {
			$event->actor->assertAdmin();
		}
	}
}
