module.exports=function(e){var o={};function l(n){if(o[n])return o[n].exports;var t=o[n]={i:n,l:!1,exports:{}};return e[n].call(t.exports,t,t.exports,l),t.l=!0,t.exports}return l.m=e,l.c=o,l.d=function(e,o,n){l.o(e,o)||Object.defineProperty(e,o,{enumerable:!0,get:n})},l.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.t=function(e,o){if(1&o&&(e=l(e)),8&o)return e;if(4&o&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(l.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&o&&"string"!=typeof e)for(var t in e)l.d(n,t,function(o){return e[o]}.bind(null,t));return n},l.n=function(e){var o=e&&e.__esModule?function(){return e.default}:function(){return e};return l.d(o,"a",o),o},l.o=function(e,o){return Object.prototype.hasOwnProperty.call(e,o)},l.p="",l(l.s=4)}([function(e,o,l){"use strict";l.d(o,"a",(function(){return n}));var n="alexanderomara-wp-users"},function(e,o){e.exports=flarum.core.compat.app},function(e,o,l){"use strict";l.d(o,"a",(function(){return u}));l(1);function n(e){return Array.prototype.indexOf.call((this.ownerDocument||this.document).querySelectorAll(e),this)>-1}function t(e,o){return(e.matches||e.matchesSelector||e.webkitMatchesSelector||e.mozMatchesSelector||e.msMatchesSelector||e.oMatchesSelector||n).call(e,o)}var r=l(0);function a(){return app.data[r.a]}function i(e){return e?function(e,o,l){var n=e.split("#"),t=n[0].indexOf("?")<0?"?":"&";return n[0]+=""+t+encodeURIComponent(o)+"="+encodeURIComponent(l),n.join("#")}(e,"redirect_to",location.href):e}function m(e){if(!/#localuser$/i.test(location.href)){var o=null,l=e.target;if(t(l,".item-logIn *"))o=i(a().loginUrl);else if(t(l,".item-signUp *"))o=i(a().registerUrl);else if(t(l,".item-changePassword *")){var n=a().allowedChanges;n&&!n.password&&(o=a().profileUrl)}else if(t(l,".item-changeEmail *")){var r=a().allowedChanges;r&&!r.email&&(o=a().profileUrl)}o&&(e.preventDefault(),e.stopPropagation(),e.stopImmediatePropagation(),location.href=o)}}function u(){window.addEventListener("click",m,!0)}},function(e,o){e.exports=flarum.core.compat["components/SettingsModal"]},function(e,o,l){"use strict";l.r(o);var n=l(1),t=l.n(n),r=l(0),a=l(2);var i=l(3);var u=function(e){var o,l;l=e,(o=t).prototype=Object.create(l.prototype),o.prototype.constructor=o,o.__proto__=l;var n;n=t;function t(){return e.apply(this,arguments)||this}var a=t.prototype;return a.className=function(){return"WPUsersSettingsModal Modal--large"},a.title=function(){return"WP Users Settings"},a.form=function(){var e=this,o=function(o){return e.setting(r.a+"."+o)};return[m("div",{className:"Form-group"},m("label",null,"Login URL (",m("code",null,".../wp-login.php"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("login_url")}))),m("div",{className:"Form-group"},m("label",null,"Profile URL (",m("code",null,".../wp-admin/profile.php"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("profile_url")}))),m("div",{className:"Form-group"},m("label",null,"Cookie Name (",m("code",null,"wordpress_logged_in_*"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("cookie_name")}))),m("div",{className:"Form-group"},m("label",null,"Database Host (",m("code",null,"DB_HOST"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("db_host")}))),m("div",{className:"Form-group"},m("label",null,"Database User (",m("code",null,"DB_USER"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("db_user")}))),m("div",{className:"Form-group"},m("label",null,"Database Password (",m("code",null,"DB_PASSWORD"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("db_pass")}))),m("div",{className:"Form-group"},m("label",null,"Database Name (",m("code",null,"DB_NAME"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("db_name")}))),m("div",{className:"Form-group"},m("label",null,"Database Charset (",m("code",null,"DB_CHARSET"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("db_charset")}))),m("div",{className:"Form-group"},m("label",null,"Database Prefix (",m("code",null,"$table_prefix"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("db_pre")}))),m("div",{className:"Form-group"},m("label",null,"Logged In Key (",m("code",null,"LOGGED_IN_KEY"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("logged_in_key")}))),m("div",{className:"Form-group"},m("label",null,"Logged In Salt (",m("code",null,"LOGGED_IN_SALT"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("logged_in_salt")}))),m("div",{className:"Form-group"},m("label",null,"Nonce Key (",m("code",null,"NONCE_KEY"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("nonce_key")}))),m("div",{className:"Form-group"},m("label",null,"Nonce Salt (",m("code",null,"NONCE_SALT"),")"),m("code",null,m("input",{className:"FormControl",bidi:o("nonce_salt")})))]},t}(l.n(i).a);t.a.initializers.add(r.a,(function(){Object(a.a)(),t.a.extensionSettings[r.a]=function(){return t.a.modal.show(new u)}}))}]);
//# sourceMappingURL=admin.js.map