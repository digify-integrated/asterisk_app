'use strict';

import { LoginModule } from '../class/LoginModule.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new LoginModule();
    manager.init();
});