import {ID} from '../config';

export function data() {
	return app.data[ID];
}

export function queryAdd(url, key, value) {
	const parts = url.split('#');
	const c = parts[0].indexOf('?') < 0 ? '?' : '&';
	parts[0] += `${c}${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
	return parts.join('#');
}
