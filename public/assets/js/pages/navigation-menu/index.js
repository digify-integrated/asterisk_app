'use strict';

import { NavigationMenu } from '../../class/NavigationMenu.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new NavigationMenu();
    manager.init();
});