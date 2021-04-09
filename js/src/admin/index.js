import app from 'flarum/app';

import {ID} from '../config';
import {intercept} from '../shared/intercept';

app.initializers.add(ID, app => {
	intercept();

	const ext = app.extensionData.for(ID);
	const code = (key, name, info) => {
		ext.registerSetting(function() {
			return (
				<div className="Form-group">
					<label>{name} (<code>{info}</code>)</label>
					<code>
						<input
							type='text'
							className='FormControl'
							style={{maxWidth: 'none'}}
							bidi={this.setting(`${ID}.${key}`)}
						/>
					</code>
				</div>
			);
		});
	};

	code('login_url', 'Login URL', '.../wp-login.php');
	code('profile_url', 'Profile URL', '.../wp-admin/profile.php');
	code('cookie_name', 'Cookie Name', 'wordpress_logged_in_...');
	code('db_host', 'Database Host', 'DB_HOST');
	code('db_user', 'Database User', 'DB_USER');
	code('db_pass', 'Database Password', 'DB_PASSWORD');
	code('db_name', 'Database Name', 'DB_NAME');
	code('db_charset', 'Database Charset', 'DB_CHARSET');
	code('db_pre', 'Database Prefix', '$table_prefix');
	code('logged_in_key', 'Logged In Key', 'LOGGED_IN_KEY');
	code('logged_in_salt', 'Logged In Salt', 'LOGGED_IN_SALT');
	code('nonce_key', 'Nonce Key', 'NONCE_KEY');
	code('nonce_salt', 'Nonce Salt', 'NONCE_SALT');
});
