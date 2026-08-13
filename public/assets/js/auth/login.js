'use strict';

import { Login } from '../class/Login.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new Login();
    manager.init();
});