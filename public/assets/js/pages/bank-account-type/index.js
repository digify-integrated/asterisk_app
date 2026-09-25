'use strict';

import { BankAccountType } from '../../class/BankAccountType.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new BankAccountType();
    manager.init();
});