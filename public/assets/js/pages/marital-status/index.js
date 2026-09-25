'use strict';

import { MaritalStatus } from '../../class/MaritalStatus.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new MaritalStatus();
    manager.init();
});