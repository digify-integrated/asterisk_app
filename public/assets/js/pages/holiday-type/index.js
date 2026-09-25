'use strict';

import { HolidayType } from '../../class/HolidayType.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new HolidayType();
    manager.init();
});