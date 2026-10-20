'use strict';

import { EmploymentStatus } from '../../class/EmploymentStatus.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new EmploymentStatus();
    manager.init();
});