'use strict';

import { PagePermission } from '../../class/PagePermission.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new PagePermission();
    manager.init();
});