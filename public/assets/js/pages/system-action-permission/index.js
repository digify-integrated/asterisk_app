'use strict';

import { SystemActionPermission } from '../../class/SystemActionPermission.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new SystemActionPermission();
    manager.init();
});