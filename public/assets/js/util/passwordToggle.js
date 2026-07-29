'use strict';

const BOUND_FLAG = 'passwordToggleBound';

export class PasswordToggle {
    static DEFAULTS = {
        eyeClass: 'ki-outline ki-eye',
        eyeOffClass: 'ki-outline ki-eye-slash',
        label: 'Toggle password visibility',
    };

    constructor(rootElement = document, options = {}) {
        this.root = rootElement;
        this.config = Object.assign({}, PasswordToggle.DEFAULTS, options);
        this._abortController = new AbortController();

        this._init();
    }

    static autoInit(options = {}) {
        return new PasswordToggle(document, options);
    }

    _init() {
        const { signal } = this._abortController;

        this.syncUI();

        this.root.addEventListener('click', (e) => this._processDOMEvent(e), { signal });
        this.root.addEventListener('keydown', (e) => this._processDOMEvent(e), { signal });
    }

    destroy() {
        this._abortController.abort();
    }

    syncUI() {
        const triggers = this.root.querySelectorAll('[data-password-toggle], .password-addon');
        triggers.forEach(trigger => {
            const input = this._resolveInput(trigger);
            if (input) this._applyA11y(trigger, input);
        });
    }

    _processDOMEvent(e) {
        const trigger = e.target.closest('[data-password-toggle], .password-addon');
        if (!trigger) return;

        e.stopPropagation();

        const input = this._resolveInput(trigger);
        if (!input) return;

        if (!trigger.dataset[BOUND_FLAG]) {
            this._applyA11y(trigger, input);
        }

        if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') return;

        e.preventDefault();
        this.toggle(trigger, input);
    }

    toggle(toggleEl, inputEl) {
        const isCurrentlyMasked = inputEl.type === 'password';
        
        inputEl.type = isCurrentlyMasked ? 'text' : 'password';

        console.log('[PasswordToggle] After click:', {
            type: inputEl.type,
            computedSecurity: window.getComputedStyle(inputEl).getPropertyValue('-webkit-text-security')
        });

        toggleEl.setAttribute('aria-pressed', String(isCurrentlyMasked));

        const icon = toggleEl.querySelector('i, svg');
        if (icon) {
            const eyeClasses = this.config.eyeClass.split(' ').filter(Boolean);
            const eyeOffClasses = this.config.eyeOffClass.split(' ').filter(Boolean);

            if (isCurrentlyMasked) {
                icon.classList.remove(...eyeClasses);
                icon.classList.add(...eyeOffClasses);
            } else {
                icon.classList.remove(...eyeOffClasses);
                icon.classList.add(...eyeClasses);
            }
        }

        inputEl.focus({ preventScroll: true });
    }

    _resolveInput(toggleEl) {
        const selector = toggleEl.getAttribute('data-target');
        let element = null;

        if (selector) {
            element = this.root.querySelector(selector);
        } else {
            element = toggleEl.previousElementSibling || toggleEl.closest('.input-group')?.querySelector('input');
        }

        return element instanceof HTMLInputElement ? element : null;
    }

    _applyA11y(toggleEl, inputEl) {
        if (!toggleEl.hasAttribute('role')) toggleEl.setAttribute('role', 'button');
        if (!toggleEl.hasAttribute('tabindex')) toggleEl.setAttribute('tabindex', '0');
        if (!toggleEl.hasAttribute('aria-label')) toggleEl.setAttribute('aria-label', this.config.label);
        
        toggleEl.setAttribute('aria-pressed', String(inputEl.type !== 'password'));
        if (inputEl.id) toggleEl.setAttribute('aria-controls', inputEl.id);
        
        toggleEl.dataset[BOUND_FLAG] = 'true';
    }
}