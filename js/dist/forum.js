module.exports=function(r){var e={};function n(t){if(e[t])return e[t].exports;var o=e[t]={i:t,l:!1,exports:{}};return r[t].call(o.exports,o,o.exports,n),o.l=!0,o.exports}return n.m=r,n.c=e,n.d=function(r,e,t){n.o(r,e)||Object.defineProperty(r,e,{enumerable:!0,get:t})},n.r=function(r){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(r,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(r,"__esModule",{value:!0})},n.t=function(r,e){if(1&e&&(r=n(r)),8&e)return r;if(4&e&&"object"==typeof r&&r&&r.__esModule)return r;var t=Object.create(null);if(n.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:r}),2&e&&"string"!=typeof r)for(var o in r)n.d(t,o,function(e){return r[e]}.bind(null,o));return t},n.n=function(r){var e=r&&r.__esModule?function(){return r.default}:function(){return r};return n.d(e,"a",e),e},n.o=function(r,e){return Object.prototype.hasOwnProperty.call(r,e)},n.p="",n(n.s=7)}([function(r,e){r.exports=flarum.core.compat.app},function(r,e,n){"use strict";n.d(e,"a",(function(){return t}));var t="alexanderomara-wp-users"},function(r,e){r.exports=flarum.core.compat["components/LogInModal"]},function(r,e){r.exports=flarum.core.compat["components/SignUpModal"]},function(r,e){r.exports=flarum.core.compat["components/ChangeEmailModal"]},function(r,e){r.exports=flarum.core.compat["components/ChangePasswordModal"]},function(r,e,n){"use strict";n.d(e,"a",(function(){return g}));var t=n(0),o=n.n(t),u=n(2),a=n.n(u),c=n(3),i=n.n(c),l=n(4),f=n.n(l),p=n(5),s=n.n(p),d=n(1);function m(){return o.a.data[d.a]}function y(r,e,n){var t=function(r,e){if(Object.getOwnPropertyDescriptor(r,e))return r;for(var n=null==(t=Object.getPrototypeOf(r))?void 0:t.constructor;n;n=Object.getPrototypeOf(n)){var t,o=n.prototype;if(!o)break;if(Object.getOwnPropertyDescriptor(o,e))return o}return null}(r,e);if(!t)throw new Error("Property not found: "+e);var o=Object.getOwnPropertyDescriptor(t,e),u=o.value;if("function"!=typeof u)throw new Error("Property not function: "+e);o.value=n(u),Object.defineProperty(r,e,o)}function v(r){return r?function(r,e,n){var t=r.split("#"),o=t[0].indexOf("?")<0?"?":"&";return t[0]+=""+o+encodeURIComponent(e)+"="+encodeURIComponent(n),t.join("#")}(r,"redirect_to",location.href):r}function b(){return/#localuser$/i.test(location.href)}function O(r){switch(r){case a.a:return v(m().loginUrl);case i.a:return v(m().registerUrl);case f.a:var e;return null!=(e=m().allowedChanges)&&e.email?null:m().profileUrl;case s.a:var n;return null!=(n=m().allowedChanges)&&n.password?null:m().profileUrl}return null}function g(){y(o.a.modal,"show",(function(r){return function(e,n){var t=b()?null:O(e);if(!t)return r.apply(this,arguments);location.href=t}}))}},function(r,e,n){"use strict";n.r(e);var t=n(0),o=n.n(t),u=n(1),a=n(6);o.a.initializers.add(u.a,(function(){Object(a.a)()}))}]);
//# sourceMappingURL=forum.js.map