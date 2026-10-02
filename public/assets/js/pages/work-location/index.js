'use strict';

import { WorkLocation } from '../../class/WorkLocation.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new WorkLocation();
    manager.init();
});