(()=>{var e={n:o=>{var r=o&&o.__esModule?()=>o.default:()=>o;return e.d(r,{a:r}),r},d:(o,r)=>{for(var n in r)e.o(r,n)&&!e.o(o,n)&&Object.defineProperty(o,n,{enumerable:!0,get:r[n]})},o:(e,o)=>Object.prototype.hasOwnProperty.call(e,o)};(()=>{"use strict";const o=flarum.core.compat.app;var r=e.n(o),n="alexanderomara-wp-users";const t=flarum.core.compat["components/LogInModal"];var a=e.n(t);const l=flarum.core.compat["components/SignUpModal"];var i=e.n(l);const s=flarum.core.compat["components/ChangeEmailModal"];var c=e.n(s);const u=flarum.core.compat["components/ChangePasswordModal"];var p=e.n(u);function d(){return r().data[n]}function f(e){return e?function(e,o,r){var n=e.split("#"),t=n[0].indexOf("?")<0?"?":"&";return n[0]+=""+t+encodeURIComponent("redirect_to")+"="+encodeURIComponent(r),n.join("#")}(e,0,location.href):e}function g(){!function(e,o,r){var n=function(e,o){if(Object.getOwnPropertyDescriptor(e,o))return e;for(var r=null==(n=Object.getPrototypeOf(e))?void 0:n.constructor;r;r=Object.getPrototypeOf(r)){var n,t=r.prototype;if(!t)break;if(Object.getOwnPropertyDescriptor(t,o))return t}return null}(e,o);if(!n)throw new Error("Property not found: "+o);var t=Object.getOwnPropertyDescriptor(n,o),a=t.value;if("function"!=typeof a)throw new Error("Property not function: "+o);t.value=r(a),Object.defineProperty(e,o,t)}(r().modal,"show",(function(e){return function(o,r){var n=/#localuser$/i.test(location.href)?null:function(e){switch(e){case a():return f(d().loginUrl);case i():return f(d().registerUrl);case c():var o;return null!=(o=d().allowedChanges)&&o.email?null:d().profileUrl;case p():var r;return null!=(r=d().allowedChanges)&&r.password?null:d().profileUrl}return null}(o);if(!n)return e.apply(this,arguments);location.href=n}}))}r().initializers.add(n,(function(e){g();var o,r=e.extensionData.for(n),t=function(e,o,t){r.registerSetting((function(){return m("div",{className:"Form-group"},m("label",null,o," (",m("code",null,t),")"),m("code",null,m("input",{type:"text",className:"FormControl",style:{maxWidth:"none"},bidi:this.setting(n+"."+e)})))}))};t("login_url","Login URL",".../wp-login.php"),t("profile_url","Profile URL",".../wp-admin/profile.php"),t("cookie_name","Cookie Name","wordpress_logged_in_..."),t("db_host","Database Host","DB_HOST"),t("db_user","Database User","DB_USER"),t("db_pass","Database Password","DB_PASSWORD"),t("db_name","Database Name","DB_NAME"),t("db_charset","Database Charset","DB_CHARSET"),t("db_pre","Database Prefix","$table_prefix"),t("logged_in_key","Logged In Key","LOGGED_IN_KEY"),t("logged_in_salt","Logged In Salt","LOGGED_IN_SALT"),t("nonce_key","Nonce Key","NONCE_KEY"),t("nonce_salt","Nonce Salt","NONCE_SALT"),void 0===o&&(o=null),r.registerSetting({setting:n+".decode_plus",label:m("label",null,"Decode + in cookie (for usernames with spaces from WordPress on PHP < 8)"),type:"boolean"})}))})(),module.exports={}})();
//# sourceMappingURL=admin.js.map