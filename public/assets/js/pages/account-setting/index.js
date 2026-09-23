'use strict';

import { AccountSetting } from '../../class/AccountSetting.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new AccountSetting();
    manager.init();
});