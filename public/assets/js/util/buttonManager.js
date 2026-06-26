'use strict';

const DATA_ORIGINAL = 'btnOriginalHtml';
const DATA_LOADING = 'btnIsLoading';

const DEFAULT_SPINNER_HTML = `
    <span class="btn-spinner-wrapper" style="margin-right: 8px;">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width: 1rem; height: 1rem; border-width: 0.2em; animation: btn-spin 0.75s linear infinite;"></span>
    </span>
`;

// Simple spinner spin keyframe (no positional masking overrides needed anymore)
if (typeof document !== 'undefined' && !document.getElementById('btn-manager-styles')) {
    const style = document.createElement('style');
    style.id = 'btn-manager-styles';
    style.textContent = `@keyframes btn-spin { to { transform: rotate(360deg); } }`;
    document.head.appendChild(style);
}

export class ButtonStateManager {
    /**
     * Resolve mixed targets safely into an iterable Array of HTMLElements
     * @param {string|HTMLElement|NodeList|Array} targets 
     * @returns {HTMLElement[]}
     */
    static resolve(targets) {
        if (!targets) return [];
        if (typeof targets === 'string') return Array.from(document.querySelectorAll(targets));
        if (targets instanceof HTMLElement) return [targets];
        if (targets instanceof NodeList || Array.isArray(targets)) return Array.from(targets);
        return [];
    }

    /**
     * Disables buttons and performs a clean content replacement.
     * * @param {string|HTMLElement|NodeList|Array} targets - Elements to process
     * @param {Object} options
     * @param {string|null} [options.loadingText=null] - Custom text to display when disabled
     * @param {boolean} [options.showLoader=true] - Toggle visibility of the spinner icon
     * @param {string} [options.spinnerHtml] - Custom spinner template override
     * @param {boolean} [options.setAriaBusy=true] - Toggle ARIA accessibility flag
     */
    static disable(targets, options = {}) {
        const config = {
            loadingText: null,
            showLoader: true,
            spinnerHtml: DEFAULT_SPINNER_HTML,
            setAriaBusy: true,
            ...options
        };

        const buttons = this.resolve(targets);
        if (!buttons.length) return;

        buttons.forEach(btn => {
            if (btn.dataset[DATA_LOADING] === 'true') {
                if (!btn.disabled) btn.disabled = true;
                if (config.setAriaBusy) btn.setAttribute('aria-busy', 'true');
                return;
            }

            // Cache original HTML content
            btn.dataset[DATA_ORIGINAL] = btn.innerHTML;
            btn.dataset[DATA_LOADING] = 'true';
            
            btn.disabled = true;
            if (config.setAriaBusy) btn.setAttribute('aria-busy', 'true');

            // Build out the clean replacement layout content
            let nextContent = '';

            if (config.showLoader) {
                nextContent += config.spinnerHtml;
            }

            if (config.loadingText) {
                nextContent += `<span>${config.loadingText}</span>`;
            } else if (!config.showLoader) {
                // Fallback to original text if loader is off and no custom text is provided
                nextContent += btn.innerHTML;
            }

            // Hard swap content replacement with NO layout min-width injections
            btn.innerHTML = nextContent;
        });
    }

    /**
     * Restores buttons back to their natural layout and text.
     */
    static enable(targets, options = {}) {
        const config = {
            setAriaBusy: true,
            ...options
        };

        const buttons = this.resolve(targets);
        
        buttons.forEach(btn => {
            btn.disabled = false;
            if (config.setAriaBusy) btn.removeAttribute('aria-busy');

            const originalHtml = btn.dataset[DATA_ORIGINAL];
            if (originalHtml != null) {
                btn.innerHTML = originalHtml;
                delete btn.dataset[DATA_ORIGINAL];
            }

            delete btn.dataset[DATA_LOADING];
        });
    }
}