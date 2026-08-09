'use strict';

import { SystemAction } from '../../class/SystemAction.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new SystemAction();
    manager.init();
});