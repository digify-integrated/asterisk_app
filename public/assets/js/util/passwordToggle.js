'use strict';

const BOUND_FLAG = 'passwordToggleBound';

export class PasswordToggle {
    static DEFAULTS = {
        eyeClass: 'ki-outline ki-eye',
        eyeOffClass: 'ki-outline ki-eye-slash',
        label: 'Toggle password visibility',
    };

    /**
     * @param {HTMLElement|Document} rootElement - Scope container area for event monitoring
     * @param {Object} options - Property setting overrides
     */
    constructor(rootElement = document, options = {}) {
        this.root = rootElement;
        this.config = Object.assign({}, PasswordToggle.DEFAULTS, options);
        this._abortController = new AbortController();

        this._init();
    }

    /**
     * Automatic Boot Strapper Core
     * Automatically watches the DOM and attaches to active triggers on script inclusion.
     * @param {Object} [options]
     * @returns {PasswordToggle}
     */
    static autoInit(options = {}) {
        return new PasswordToggle(document, options);
    }

    /**
     * Set up listener attachments
     * @private
     */
    _init() {
        const { signal } = this._abortController;

        // Apply accessibility markers to static structural elements instantly on render load
        this.syncUI();

        // Single passive global event capture pipeline handling 
        this.root.addEventListener('click', (e) => this._processDOMEvent(e), { signal });
        this.root.addEventListener('keydown', (e) => this._processDOMEvent(e), { signal });
    }

    /** Cleans up the event delegation processes completely */
    destroy() {
        this._abortController.abort();
    }

    /** Scans layout tree to enforce semantic A11y states */
    syncUI() {
        const triggers = this.root.querySelectorAll('[data-password-toggle], .password-addon');
        triggers.forEach(trigger => {
            const input = this._resolveInput(trigger);
            if (input) this._applyA11y(trigger, input);
        });
    }

    /**
     * Standardizes dynamic cross-browser interaction events
     * @private
     * @param {Event} e 
     */
    _processDOMEvent(e) {
        const trigger = e.target.closest('[data-password-toggle], .password-addon');
        if (!trigger) return;

        const input = this._resolveInput(trigger);
        if (!input) return;

        // Fallback protection binding if elements are loaded dynamically over AJAX downstream
        if (!trigger.dataset[BOUND_FLAG]) {
            this._applyA11y(trigger, input);
        }

        // Keyboard navigation usability execution validation
        if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') return;

        e.preventDefault();
        this.toggle(trigger, input);
    }

    /**
     * Imperatively toggle the target field's masking state
     * @param {HTMLElement} toggleEl 
     * @param {HTMLInputElement} inputEl 
     */
    toggle(toggleEl, inputEl) {
        const isCurrentlyMasked = inputEl.type === 'password';
        inputEl.type = isCurrentlyMasked ? 'text' : 'password';

        // Update accessibility layer
        toggleEl.setAttribute('aria-pressed', String(isCurrentlyMasked));

        // UI Icon Class Synchronization Core
        const icon = toggleEl.querySelector('i, svg');
        
        if (icon) {
            // Split strings to safely handle spaces in complex utility frameworks (like Keen / KeenIcons)
            const activeClasses = this.config.eyeClass.split(' ');
            const hiddenClasses = this.config.eyeOffClass.split(' ');

            if (isCurrentlyMasked) {
                icon.classList.remove(...hiddenClasses);
                icon.classList.add(...activeClasses);
            } else {
                icon.classList.remove(...activeClasses);
                icon.classList.add(...hiddenClasses);
            }
        }

        // Return focus smoothly to preserve user typing flow tracking
        inputEl.focus({ preventScroll: true });
    }

    /**
     * Locates target input relative to trigger selectors
     * @private
     */
    _resolveInput(toggleEl) {
        const selector = toggleEl.getAttribute('data-target');
        const element = selector ? this.root.querySelector(selector) : toggleEl.previousElementSibling;
        return element instanceof HTMLInputElement ? element : null;
    }

    /**
     * Writes semantic elements down onto DOM tree references
     * @private
     */
    _applyA11y(toggleEl, inputEl) {
        if (!toggleEl.hasAttribute('role')) toggleEl.setAttribute('role', 'button');
        if (!toggleEl.hasAttribute('tabindex')) toggleEl.setAttribute('tabindex', '0');
        if (!toggleEl.hasAttribute('aria-label')) toggleEl.setAttribute('aria-label', this.config.label);
        
        toggleEl.setAttribute('aria-pressed', String(inputEl.type !== 'password'));
        if (inputEl.id) toggleEl.setAttribute('aria-controls', inputEl.id);
        
        toggleEl.dataset[BOUND_FLAG] = 'true';
    }
}

// Automatically initiate on module evaluation script parsing loops
PasswordToggle.autoInit();