import app from 'flarum/app';

import {ID} from '../config';
import {intercept} from '../shared/intercept';

app.initializers.add(ID, () => {
	intercept();
});
