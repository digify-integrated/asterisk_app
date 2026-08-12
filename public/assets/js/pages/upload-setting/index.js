'use strict';

import { UploadSetting } from '../../class/UploadSetting.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new UploadSetting();
    manager.init();
});