(()=>{var r={n:e=>{var o=e&&e.__esModule?()=>e.default:()=>e;return r.d(o,{a:o}),o},d:(e,o)=>{for(var n in o)r.o(o,n)&&!r.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:o[n]})},o:(r,e)=>Object.prototype.hasOwnProperty.call(r,e)};(()=>{"use strict";const e=flarum.core.compat.app;var o=r.n(e),n="alexanderomara-wp-users";const t=flarum.core.compat["components/LogInModal"];var a=r.n(t);const c=flarum.core.compat["components/SignUpModal"];var l=r.n(c);const u=flarum.core.compat["components/ChangeEmailModal"];var i=r.n(u);const p=flarum.core.compat["components/ChangePasswordModal"];var f=r.n(p);function s(){return o().data[n]}function d(r){return r?function(r,e,o){var n=r.split("#"),t=n[0].indexOf("?")<0?"?":"&";return n[0]+=""+t+encodeURIComponent("redirect_to")+"="+encodeURIComponent(o),n.join("#")}(r,0,location.href):r}function m(){!function(r,e,o){var n=function(r,e){if(Object.getOwnPropertyDescriptor(r,e))return r;for(var o=null==(n=Object.getPrototypeOf(r))?void 0:n.constructor;o;o=Object.getPrototypeOf(o)){var n,t=o.prototype;if(!t)break;if(Object.getOwnPropertyDescriptor(t,e))return t}return null}(r,e);if(!n)throw new Error("Property not found: "+e);var t=Object.getOwnPropertyDescriptor(n,e),a=t.value;if("function"!=typeof a)throw new Error("Property not function: "+e);t.value=o(a),Object.defineProperty(r,e,t)}(o().modal,"show",(function(r){return function(e,o){var n=/#localuser$/i.test(location.href)?null:function(r){switch(r){case a():return d(s().loginUrl);case l():return d(s().registerUrl);case i():var e;return null!=(e=s().allowedChanges)&&e.email?null:s().profileUrl;case f():var o;return null!=(o=s().allowedChanges)&&o.password?null:s().profileUrl}return null}(e);if(!n)return r.apply(this,arguments);location.href=n}}))}o().initializers.add(n,(function(){m()}))})(),module.exports={}})();
//# sourceMappingURL=forum.js.map