import app from 'flarum/app';

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

function findProperty(object, property) {
	if (Object.getOwnPropertyDescriptor(object, property)) {
		return object;
	}
	for (
		let constructor = Object.getPrototypeOf(object)?.constructor;
		constructor;
		constructor = Object.getPrototypeOf(constructor)
	) {
		const {prototype} = constructor;
		if (!prototype) {
			break;
		}
		if (Object.getOwnPropertyDescriptor(prototype, property)) {
			return prototype;
		}
	}
	return null;
}

export function methodOverride(object, method, replacement) {
	const o = findProperty(object, method);
	if (!o) {
		throw new Error(`Property not found: ${method}`);
	}
	const desc = Object.getOwnPropertyDescriptor(o, method);
	const {value} = desc;
	if (typeof value !== 'function') {
		throw new Error(`Property not function: ${method}`);
	}
	desc.value = replacement(value);
	Object.defineProperty(object, method, desc);
}
