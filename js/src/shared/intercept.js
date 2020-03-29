import app from 'flarum/app';

import {matches} from './dom';
import {data, queryAdd} from './util';

function redirectThrough(url) {
	return url ? queryAdd(url, 'redirect_to', location.href) : url;
}

function eventHandler(e) {
	// Add #localuser to URL to bypass the hijacking.
	if (/#localuser$/i.test(location.href)) {
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
}
