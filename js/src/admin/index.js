import app from 'flarum/app';

import {ID} from '../config';
import {intercept} from '../shared/intercept';
import {WPUsersSettingsModal} from './components/WPUsersSettingsModal';

app.initializers.add(ID, () => {
	intercept();

	app.extensionSettings[ID] = () => app.modal.show(
		new WPUsersSettingsModal()
	);
});
