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

export function methodOverride(object, method, replacement) {
	const desc = Object.getOwnPropertyDescriptor(object, method);
	const {value} = desc;
	if (typeof value !== 'function') {
		throw new Error(`Method is not a function: ${method}`);
	}
	desc.value = replacement(value);
	Object.defineProperty(object, method, desc);
}
