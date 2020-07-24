module.exports=function(e){var o={};function n(t){if(o[t])return o[t].exports;var r=o[t]={i:t,l:!1,exports:{}};return e[t].call(r.exports,r,r.exports,n),r.l=!0,r.exports}return n.m=e,n.c=o,n.d=function(e,o,t){n.o(e,o)||Object.defineProperty(e,o,{enumerable:!0,get:t})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,o){if(1&o&&(e=n(e)),8&o)return e;if(4&o&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(n.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&o&&"string"!=typeof e)for(var r in e)n.d(t,r,function(o){return e[o]}.bind(null,r));return t},n.n=function(e){var o=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(o,"a",o),o},n.o=function(e,o){return Object.prototype.hasOwnProperty.call(e,o)},n.p="",n(n.s=6)}([function(e,o,n){"use strict";n.d(o,"a",(function(){return t}));var t="alexanderomara-wp-users"},function(e,o){e.exports=flarum.core.compat.app},function(e,o){e.exports=flarum.core.compat["components/ModalManager"]},function(e,o){e.exports=flarum.core.compat["components/LogInModal"]},function(e,o,n){"use strict";n.d(o,"a",(function(){return f}));var t=n(2),r=n.n(t),l=n(3),a=n.n(l);function i(e){return Array.prototype.indexOf.call((this.ownerDocument||this.document).querySelectorAll(e),this)>-1}function u(e,o){return(e.matches||e.matchesSelector||e.webkitMatchesSelector||e.mozMatchesSelector||e.msMatchesSelector||e.oMatchesSelector||i).call(e,o)}var c=n(0);function m(){return app.data[c.a]}function s(e){return e?function(e,o,n){var t=e.split("#"),r=t[0].indexOf("?")<0?"?":"&";return t[0]+=""+r+encodeURIComponent(o)+"="+encodeURIComponent(n),t.join("#")}(e,"redirect_to",location.href):e}function d(){return/#localuser$/i.test(location.href)}function p(e){if(!d()){var o=null,n=e.target;if(u(n,".item-logIn *"))o=s(m().loginUrl);else if(u(n,".item-signUp *"))o=s(m().registerUrl);else if(u(n,".item-changePassword *")){var t=m().allowedChanges;t&&!t.password&&(o=m().profileUrl)}else if(u(n,".item-changeEmail *")){var r=m().allowedChanges;r&&!r.email&&(o=m().profileUrl)}o&&(e.preventDefault(),e.stopPropagation(),e.stopImmediatePropagation(),location.href=o)}}function f(){window.addEventListener("click",p,!0),function(e,o,n){var t=Object.getOwnPropertyDescriptor(e,o),r=t.value;if("function"!=typeof r)throw new Error("Method is not a function: "+o);t.value=n(r),Object.defineProperty(e,o,t)}(r.a.prototype,"show",(function(e){return function(o){if(d()||!(o instanceof a.a))return e.apply(this,arguments);location.href=s(m().loginUrl)}}))}},function(e,o){e.exports=flarum.core.compat["components/SettingsModal"]},function(e,o,n){"use strict";n.r(o);var t=n(1),r=n.n(t),l=n(0),a=n(4);var i=n(5),u=function(e){var o,n;function t(){return e.apply(this,arguments)||this}n=e,(o=t).prototype=Object.create(n.prototype),o.prototype.constructor=o,o.__proto__=n;var r=t.prototype;return r.className=function(){return"WPUsersSettingsModal Modal--large"},r.title=function(){return"WP Users Settings"},r.form=function(){var e=this,o=function(o){return e.setting(l.a+"."+o)};return[m("div",{className:"Form-group"},m("label",null,"Login URL (",m("code",null,".../wp-login.php"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("login_url")}))),m("div",{className:"Form-group"},m("label",null,"Profile URL (",m("code",null,".../wp-admin/profile.php"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("profile_url")}))),m("div",{className:"Form-group"},m("label",null,"Cookie Name (",m("code",null,"wordpress_logged_in_*"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("cookie_name")}))),m("div",{className:"Form-group"},m("label",null,"Database Host (",m("code",null,"DB_HOST"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("db_host")}))),m("div",{className:"Form-group"},m("label",null,"Database User (",m("code",null,"DB_USER"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("db_user")}))),m("div",{className:"Form-group"},m("label",null,"Database Password (",m("code",null,"DB_PASSWORD"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("db_pass")}))),m("div",{className:"Form-group"},m("label",null,"Database Name (",m("code",null,"DB_NAME"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("db_name")}))),m("div",{className:"Form-group"},m("label",null,"Database Charset (",m("code",null,"DB_CHARSET"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("db_charset")}))),m("div",{className:"Form-group"},m("label",null,"Database Prefix (",m("code",null,"$table_prefix"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("db_pre")}))),m("div",{className:"Form-group"},m("label",null,"Logged In Key (",m("code",null,"LOGGED_IN_KEY"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("logged_in_key")}))),m("div",{className:"Form-group"},m("label",null,"Logged In Salt (",m("code",null,"LOGGED_IN_SALT"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("logged_in_salt")}))),m("div",{className:"Form-group"},m("label",null,"Nonce Key (",m("code",null,"NONCE_KEY"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("nonce_key")}))),m("div",{className:"Form-group"},m("label",null,"Nonce Salt (",m("code",null,"NONCE_SALT"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("nonce_salt")})))]},t}(n.n(i).a);r.a.initializers.add(l.a,(function(){Object(a.a)(),r.a.extensionSettings[l.a]=function(){return r.a.modal.show(new u)}}))}]);
//# sourceMappingURL=admin.js.map