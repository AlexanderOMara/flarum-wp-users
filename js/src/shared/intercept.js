import app from 'flarum/app';
import LogInModal from 'flarum/components/LogInModal';
import SignUpModal from 'flarum/components/SignUpModal';
import ChangeEmailModal from 'flarum/components/ChangeEmailModal';
import ChangePasswordModal from 'flarum/components/ChangePasswordModal';

import {data, queryAdd, methodOverride} from './util';

function redirectThrough(url) {
	return url ? queryAdd(url, 'redirect_to', location.href) : url;
}

function bypass() {
	// Add #localuser to URL to bypass the hijacking.
	return /#localuser$/i.test(location.href);
}

function shouldRedirect(componentClass) {
	switch (componentClass) {
		case LogInModal: {
			return redirectThrough(data().loginUrl);
		}
		case SignUpModal: {
			return redirectThrough(data().registerUrl);
		}
		case ChangeEmailModal: {
			return data().allowedChanges?.email ?
				null :
				data().profileUrl;
		}
		case ChangePasswordModal: {
			return data().allowedChanges?.password ?
				null :
				data().profileUrl;
		}
	}
	return null;
}

export function intercept() {
	// Hijack the method that shows the different modals to redirect.
	methodOverride(app.modal, 'show', show => {
		return function(componentClass, _attrs) {
			const redirect = bypass() ? null : shouldRedirect(componentClass);
			if (redirect) {
				location.href = redirect;
				return;
			}
			return show.apply(this, arguments);
		};
	});
}
