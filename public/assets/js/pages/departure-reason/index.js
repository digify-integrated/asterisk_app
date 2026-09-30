'use strict';

import { DepartureReason } from '../../class/DepartureReason.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new DepartureReason();
    manager.init();
});