'use strict';

export class FormLoadingState {
    constructor({ form, hideTargets = [] }) {
        this.formEl = typeof form === 'string' ? document.querySelector(form) : form;
        this.hideTargets = hideTargets.map(t => typeof t === 'string' ? document.querySelector(t) : t).filter(Boolean);
        this.disabledElements = [];
    }

    start() {
        this.hideTargets.forEach(el => el.classList.add('d-none'));

        if (!this.formEl) return;

        const controls = this.formEl.querySelectorAll('input, select, textarea, button:not(.btn-close)');
        this.disabledElements = [];

        controls.forEach(control => {
            if (!control.disabled) {
                control.disabled = true;
                this.disabledElements.push(control);
            }
        });

        this.formEl.classList.add('position-relative', 'overlay', 'overlay-block');
    }

    stop() {
        this.hideTargets.forEach(el => el.classList.remove('d-none'));
        
        this.disabledElements.forEach(control => {
            if (control) control.disabled = false;
        });
        
        this.disabledElements = [];
        if (this.formEl) {
            this.formEl.classList.remove('position-relative', 'overlay', 'overlay-block');
        }
    }
}