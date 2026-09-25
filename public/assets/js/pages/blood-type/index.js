'use strict';

import { BloodType } from '../../class/BloodType.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new BloodType();
    manager.init();
});