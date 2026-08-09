'use strict';

import { SystemParameter } from '../../class/SystemParameter.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new SystemParameter();
    manager.init();
});