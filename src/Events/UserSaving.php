<?php

namespace AlexanderOMara\FlarumWPUsers\Events;

use Flarum\User\Event\Saving;

use AlexanderOMara\FlarumWPUsers\Core;

class UserSaving {
	/**
	 * Event handler.
	 *
	 * @param Saving $event Saving event.
	 */
	public function handle(Saving $event) {
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
