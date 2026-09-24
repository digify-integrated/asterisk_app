'use strict';

import { Apps } from '../../class/Apps.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new Apps();
    manager.init();
});