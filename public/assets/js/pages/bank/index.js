'use strict';

import { Bank } from '../../class/Bank.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new Bank();
    manager.init();
});