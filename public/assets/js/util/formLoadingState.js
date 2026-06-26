'use strict';

export class FormLoadingState {
    /**
     * @param {Object} options 
     * @param {HTMLElement|string} options.form - The form wrapper node or selector
     * @param {Array<string|HTMLElement>} [options.hideTargets=[]] - Elements to hide while busy
     */
    constructor({ form, hideTargets = [] }) {
        this.formEl = typeof form === 'string' ? document.querySelector(form) : form;
        this.hideTargets = hideTargets.map(t => typeof t === 'string' ? document.querySelector(t) : t).filter(Boolean);
        this.disabledElements = [];
    }

    /** Activates loading visuals instantly */
    start() {
        // Toggle elements visibility
        this.hideTargets.forEach(el => el.classList.add('d-none'));

        if (!this.formEl) return;

        // Find all interactive form controls that aren't already disabled
        const controls = this.formEl.querySelectorAll('input, select, textarea, button:not(.btn-close)');
        this.disabledElements = [];

        controls.forEach(control => {
            if (!control.disabled) {
                control.disabled = true;
                this.disabledElements.push(control);
            }
        });

        // Add a smooth Metronic/Bootstrap style indicator or fade class if desired
        this.formEl.classList.add('position-relative', 'overlay', 'overlay-block');
    }

    /** Reverts form state completely back to normal */
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