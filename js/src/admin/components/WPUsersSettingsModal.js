import SettingsModal from 'flarum/components/SettingsModal';

import {ID} from '../../config';

export class WPUsersSettingsModal extends SettingsModal {
	className() {
		return 'WPUsersSettingsModal Modal--large';
	}

	title() {
		return 'WP Users Settings';
	}

	form() {
		const setting = key => this.setting(`${ID}.${key}`);
		return [
			<div className="Form-group">
				<label>Login URL (<code>.../wp-login.php</code>)</label>
				<code><input className="FormControl" bidi={setting('login_url')}/></code>
			</div>
			,
			<div className="Form-group">
				<label>Profile URL (<code>.../wp-admin/profile.php</code>)</label>
				<code><input className="FormControl" bidi={setting('profile_url')}/></code>
			</div>
			,
			<div className="Form-group">
				<label>Cookie Name (<code>wordpress_logged_in_*</code>)</label>
				<code><input className="FormControl" bidi={setting('cookie_name')}/></code>
			</div>
			,
			<div className="Form-group">
				<label>Database Host (<code>DB_HOST</code>)</label>
				<code><input className="FormControl" bidi={setting('db_host')}/></code>
			</div>
			,
			<div className="Form-group">
				<label>Database User (<code>DB_USER</code>)</label>
				<code><input className="FormControl" bidi={setting('db_user')}/></code>
			</div>
			,
			<div className="Form-group">
				<label>Database Password (<code>DB_PASSWORD</code>)</label>
				<code><input className="FormControl" bidi={setting('db_pass')}/></code>
			</div>
			,
			<div className="Form-group">
				<label>Database Name (<code>DB_NAME</code>)</label>
				<code><input className="FormControl" bidi={setting('db_name')}/></code>
			</div>
			,
			<div className="Form-group">
				<label>Database Charset (<code>DB_CHARSET</code>)</label>
				<code><input className="FormControl" bidi={setting('db_charset')}/></code>
			</div>
			,
			<div className="Form-group">
				<label>Database Prefix (<code>$table_prefix</code>)</label>
				<code><input className="FormControl" bidi={setting('db_pre')}/></code>
			</div>
			,
			<div className="Form-group">
				<label>Logged In Key (<code>LOGGED_IN_KEY</code>)</label>
				<code><input className="FormControl" bidi={setting('logged_in_key')}/></code>
			</div>
			,
			<div className="Form-group">
				<label>Logged In Salt (<code>LOGGED_IN_SALT</code>)</label>
				<code><input className="FormControl" bidi={setting('logged_in_salt')}/></code>
			</div>
			,
			<div className="Form-group">
				<label>Nonce Key (<code>NONCE_KEY</code>)</label>
				<code><input className="FormControl" bidi={setting('nonce_key')}/></code>
			</div>
			,
			<div className="Form-group">
				<label>Nonce Salt (<code>NONCE_SALT</code>)</label>
				<code><input className="FormControl" bidi={setting('nonce_salt')}/></code>
			</div>
		];
	}
}
