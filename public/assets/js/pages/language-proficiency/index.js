'use strict';

import { LanguageProficiency } from '../../class/LanguageProficiency.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new LanguageProficiency();
    manager.init();
});