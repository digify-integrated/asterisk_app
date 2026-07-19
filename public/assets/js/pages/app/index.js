'use strict';

import { AppModule } from '../../class/AppModule.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new AppModule();
    manager.init();
});