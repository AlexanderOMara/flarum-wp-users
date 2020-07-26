import ModalManager from 'flarum/components/ModalManager';
import LogInModal from 'flarum/components/LogInModal';

import {matches} from './dom';
import {data, queryAdd, methodOverride} from './util';

function redirectThrough(url) {
	return url ? queryAdd(url, 'redirect_to', location.href) : url;
}

function bypass() {
	// Add #localuser to URL to bypass the hijacking.
	return /#localuser$/i.test(location.href);
}

function eventHandler(e) {
	if (bypass()) {
		return;
	}

	let redirect = null;
	const {target} = e;
	if (matches(target, '.item-logIn *')) {
		redirect = redirectThrough(data().loginUrl);
	}
	else if (matches(target, '.item-signUp *')) {
		redirect = redirectThrough(data().registerUrl);
	}
	else if (matches(target, '.item-changePassword *')) {
		const {allowedChanges} = data();
		if (allowedChanges && !allowedChanges.password) {
			redirect = data().profileUrl;
		}
	}
	else if (matches(target, '.item-changeEmail *')) {
		const {allowedChanges} = data();
		if (allowedChanges && !allowedChanges.email) {
			redirect = data().profileUrl;
		}
	}

	if (redirect) {
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		location.href = redirect;
	}
}

export function intercept() {
	window.addEventListener('click', eventHandler, true);

	// Hijack the method that shows the login modal.
	methodOverride(ModalManager.prototype, 'show', show => {
		return function(modal) {
			if (modal && !bypass()) {
				if (LogInModal && modal instanceof LogInModal) {
					location.href = redirectThrough(data().loginUrl);
					return;
				}
			}
			return show.apply(this, arguments);
		};
	});
}
