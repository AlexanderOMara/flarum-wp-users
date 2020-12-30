import SettingsModal from 'flarum/components/SettingsModal';
import Select from 'flarum/components/Select';

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
		const option = (key, name, info) => (
			<div className="Form-group">
				<label>{name} (<code>{info}</code>)</label>
				<code>
					<input className="FormControl" bidi={setting(key)}/>
				</code>
			</div>
		);
		return [
			option('login_url', 'Login URL', '.../wp-login.php'),
			option('profile_url', 'Profile URL', '.../wp-admin/profile.php'),
			option('cookie_name', 'Cookie Name', 'wordpress_logged_in_...'),
			option('db_host', 'Database Host', 'DB_HOST'),
			option('db_user', 'Database User', 'DB_USER'),
			option('db_pass', 'Database Password', 'DB_PASSWORD'),
			option('db_name', 'Database Name', 'DB_NAME'),
			option('db_charset', 'Database Charset', 'DB_CHARSET'),
			option('db_pre', 'Database Prefix', '$table_prefix'),
			option('logged_in_key', 'Logged In Key', 'LOGGED_IN_KEY'),
			option('logged_in_salt', 'Logged In Salt', 'LOGGED_IN_SALT'),
			option('nonce_key', 'Nonce Key', 'NONCE_KEY'),
			option('nonce_salt', 'Nonce Salt', 'NONCE_SALT'),
			m('.Form-group', [
				<label>Get Username from (<i>Column Name of Wordpress users Table for Username mapping</i>)</label>,
                Select.component({
                    options: {
                        user_login: 'user_login',
                        user_nicename: 'user_nicename',
                        display_name: 'display_name',
                    },
                    value: setting('username_col')(),
                    onchange: setting('username_col'),
                }),
            ]),
		];
	}
}
