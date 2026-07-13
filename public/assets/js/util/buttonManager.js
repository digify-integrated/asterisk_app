'use strict';

const DATA_ORIGINAL = 'btnOriginalHtml';
const DATA_LOADING = 'btnIsLoading';

const DEFAULT_SPINNER_HTML = `
    <span class="btn-spinner-wrapper" style="margin-right: 8px;">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width: 1rem; height: 1rem; border-width: 0.2em; animation: btn-spin 0.75s linear infinite;"></span>
    </span>
`;

if (typeof document !== 'undefined' && !document.getElementById('btn-manager-styles')) {
    const style = document.createElement('style');
    style.id = 'btn-manager-styles';
    style.textContent = `@keyframes btn-spin { to { transform: rotate(360deg); } }`;
    document.head.appendChild(style);
}

export class ButtonStateManager {
    static resolve(targets) {
        if (!targets) return [];
        if (typeof targets === 'string') return Array.from(document.querySelectorAll(targets));
        if (targets instanceof HTMLElement) return [targets];
        if (targets instanceof NodeList || Array.isArray(targets)) return Array.from(targets);
        return [];
    }

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

            btn.dataset[DATA_ORIGINAL] = btn.innerHTML;
            btn.dataset[DATA_LOADING] = 'true';
            
            btn.disabled = true;
            if (config.setAriaBusy) btn.setAttribute('aria-busy', 'true');

            let nextContent = '';

            if (config.showLoader) {
                nextContent += config.spinnerHtml;
            }

            if (config.loadingText) {
                nextContent += `<span>${config.loadingText}</span>`;
            } else if (!config.showLoader) {
                nextContent += btn.innerHTML;
            }

            btn.innerHTML = nextContent;
        });
    }

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