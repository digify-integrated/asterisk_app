'use strict';

import { WorkSchedule } from '../../class/WorkSchedule.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new WorkSchedule();
    manager.init();
});