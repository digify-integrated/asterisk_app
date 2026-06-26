'use strict';

const BOUND_FLAG = 'passwordToggleBound';

export class PasswordToggle {
    static DEFAULTS = {
        eyeClass: 'ki-eye',
        eyeOffClass: 'ki-eye-slash',
        label: 'Toggle password visibility',
    };

    /**
     * @param {HTMLElement|Document} rootElement - Scope container for events
     * @param {Object} options 
     */
    constructor(rootElement = document, options = {}) {
        this.root = rootElement;
        this.config = { ...PasswordToggle.DEFAULTS, ...options };
        this._abortController = new AbortController();

        this._init();
    }

    _init() {
        const { signal } = this._abortController;

        // Initialize any fields present immediately on load
        this.syncUI();

        // Delegate interactions globally or within root context
        this.root.addEventListener('click', (e) => this._handleEvent(e), { signal });
        this.root.addEventListener('keydown', (e) => this._handleEvent(e), { signal });
    }

    /** Cleans up the event delegation listeners */
    destroy() {
        this._abortController.abort();
    }

    /** Finds and ensures all visible password elements have structural accessibility attributes */
    syncUI() {
        const toggles = this.root.querySelectorAll('[data-password-toggle], .password-addon');
        toggles.forEach(toggle => {
            const input = this._resolveInput(toggle);
            if (input) this._applyA11y(toggle, input);
        });
    }

    _handleEvent(e) {
        const toggle = e.target.closest('[data-password-toggle], .password-addon');
        if (!toggle) return;

        const input = this._resolveInput(toggle);
        if (!input) return;

        // Initialize state configuration on physical interaction if missed
        if (!toggle.dataset[BOUND_FLAG]) {
            this._applyA11y(toggle, input);
        }

        if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') return;

        e.preventDefault();
        this.toggle(toggle, input);
    }

    /**
     * Imperatively toggle the target field's masking state
     * @param {HTMLElement} toggleEl 
     * @param {HTMLInputElement} inputEl 
     */
    toggle(toggleEl, inputEl) {
        const isCurrentlyMasked = inputEl.type === 'password';
        inputEl.type = isCurrentlyMasked ? 'text' : 'password';

        // Accessibility Update
        toggleEl.setAttribute('aria-pressed', String(isCurrentlyMasked));

        // UI Icon Class Sync
        const icon = toggleEl.querySelector('i, svg') || toggleEl;
        if (icon && icon.classList) {
            icon.classList.toggle(this.config.eyeClass, !isCurrentlyMasked);
            icon.classList.toggle(this.config.eyeOffClass, isCurrentlyMasked);
        }

        // Return focus smoothly to preserve user tracking
        inputEl.focus({ preventScroll: true });
    }

    _resolveInput(toggleEl) {
        const selector = toggleEl.getAttribute('data-target');
        const element = selector ? this.root.querySelector(selector) : toggleEl.previousElementSibling;
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