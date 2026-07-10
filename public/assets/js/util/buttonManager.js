'use strict';

const DATA_ORIGINAL_WIDTH = 'btnOriginalWidth';
const DATA_LOADING = 'btnIsLoading';

// Clean, layout-stable indicator overrides using semantic CSS positioning
if (typeof document !== 'undefined' && !document.getElementById('btn-manager-styles')) {
    const style = document.createElement('style');
    style.id = 'btn-manager-styles';
    style.textContent = `
        .btn-loading-state-active {
            position: relative !important;
            text-shadow: none !important;
            color: transparent !important;
            pointer-events: none !important;
        }
        .btn-loading-state-active .btn-spinner-overlay {
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        @keyframes btn-spin-kf { to { transform: rotate(360deg); } }
    `;
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
     * Toggles buttons into a loading state without altering layouts or dropping accessibility properties.
     * @param {string|HTMLElement|NodeList|Array} targets
     * @param {Object} options
     * @param {string|null} [options.loadingText=null] - Optional replacement text overlay
     * @param {boolean} [options.showLoader=true] - Toggle spinner visibility
     * @param {boolean} [options.preserveWidth=true] - Lock dimension sizes to completely eliminate page thrashing
     */
    static disable(targets, options = {}) {
        const config = {
            loadingText: null,
            showLoader: true,
            preserveWidth: true,
            ...options
        };

        const buttons = this.resolve(targets);
        if (!buttons.length) return;

        buttons.forEach(btn => {
            if (btn.dataset[DATA_LOADING] === 'true') return;
            btn.dataset[DATA_LOADING] = 'true';

            // Secure Layout Preservation: Cache bounding metrics to block page layout shunts
            if (config.preserveWidth) {
                const rect = btn.getBoundingClientRect();
                btn.dataset[DATA_ORIGINAL_WIDTH] = btn.style.width;
                btn.style.width = `${rect.width}px`;
            }

            // Secure Accessibility Management: Use aria-disabled rather than a native 'disabled' property
            // This prevents screen-readers from resetting focus back to the top of the body element
            btn.setAttribute('aria-disabled', 'true');
            btn.setAttribute('aria-busy', 'true');
            btn.classList.add('btn-loading-state-active');

            // Build structural overlay elements
            const overlay = document.createElement('span');
            overlay.className = 'btn-spinner-overlay';

            if (config.showLoader) {
                overlay.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" 
                          style="width: 1.15rem; height: 1.15rem; border-width: 0.2em; animation: btn-spin-kf 0.75s linear infinite; ${config.loadingText ? 'margin-right: 6px;' : ''}">
                    </span>
                `;
            }

            if (config.loadingText) {
                const textSpan = document.createElement('span');
                textSpan.className = 'fs-7 fw-semibold';
                textSpan.innerText = config.loadingText; // Protection against structural XSS text injection vectors
                overlay.appendChild(textSpan);
            }

            btn.appendChild(overlay);

            // Safety catch: Block physical form execution triggers
            const interceptClick = (e) => {
                if (btn.dataset[DATA_LOADING] === 'true') {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                }
            };
            btn.addEventListener('click', interceptClick, { capture: true });
            btn._btnInterceptRef = interceptClick;
        });
    }

    /**
     * Restores buttons back to their natural layout and text gracefully.
     */
    static enable(targets) {
        const buttons = this.resolve(targets);
        if (!buttons.length) return;

        buttons.forEach(btn => {
            if (btn.dataset[DATA_LOADING] !== 'true') return;

            // Remove lifecycle overlay nodes safely
            const overlay = btn.querySelector('.btn-spinner-overlay');
            if (overlay) overlay.remove();

            // Clear intercept click protections safely
            if (btn._btnInterceptRef) {
                btn.removeEventListener('click', btn._btnInterceptRef, { capture: true });
                delete btn._btnInterceptRef;
            }

            // Revert state configurations
            btn.removeAttribute('aria-disabled');
            btn.removeAttribute('aria-busy');
            btn.classList.remove('btn-loading-state-active');

            // Restore layout geometry settings
            if (btn.dataset[DATA_ORIGINAL_WIDTH] !== undefined) {
                btn.style.width = btn.dataset[DATA_ORIGINAL_WIDTH];
                delete btn.dataset[DATA_ORIGINAL_WIDTH];
            }

            delete btn.dataset[DATA_LOADING];
        });
    }
}