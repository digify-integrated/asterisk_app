'use strict';

const BOUND_FLAG = 'imagePreviewBound';

export class ImagePreview {
    static DEFAULTS = {
        defaultSrc: '', // Fallback image source when reset
    };

    constructor(rootElement = document, options = {}) {
        this.root = rootElement;
        this.config = Object.assign({}, ImagePreview.DEFAULTS, options);
        this._abortController = new AbortController();

        this._init();
    }

    static autoInit(options = {}) {
        return new ImagePreview(document, options);
    }

    _init() {
        const { signal } = this._abortController;

        this.syncUI();

        // Listen for file changes and reset button clicks using event delegation
        this.root.addEventListener('change', (e) => this._processChangeEvent(e), { signal });
        this.root.addEventListener('click', (e) => this._processClickEvent(e), { signal });
    }

    destroy() {
        this._abortController.abort();
    }

    syncUI() {
        const inputs = this.root.querySelectorAll('input[type="file"][data-image-preview], .image-preview-input');
        inputs.forEach(input => {
            const previewEl = this._resolvePreview(input);
            if (previewEl) {
                input.dataset[BOUND_FLAG] = 'true';
                // Store initial default source dynamically if not explicitly passed
                if (!input.dataset.defaultSrc && previewEl instanceof HTMLImageElement) {
                    input.dataset.defaultSrc = previewEl.getAttribute('src') || this.config.defaultSrc;
                }
            }
        });
    }

    _processChangeEvent(e) {
        const input = e.target.closest('input[type="file"][data-image-preview], .image-preview-input');
        if (!input) return;

        const previewEl = this._resolvePreview(input);
        if (!previewEl) return;

        const file = input.files?.[0];
        if (file) {
            this.updatePreview(previewEl, file);
        }
    }

    _processClickEvent(e) {
        const resetBtn = e.target.closest('[data-image-reset], .image-reset-btn');
        if (!resetBtn) return;

        e.preventDefault();

        // Resolve the associated input using data-target or a shared container wrapper
        const input = this._resolveInputFromReset(resetBtn);
        if (!input) return;

        const previewEl = this._resolvePreview(input);
        if (!previewEl) return;

        this.resetPreview(input, previewEl);
    }

    updatePreview(previewEl, fileOrSrc) {
        if (fileOrSrc instanceof File) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this._setSource(previewEl, e.target.result);
            };
            reader.readAsDataURL(fileOrSrc);
        } else if (typeof fileOrSrc === 'string') {
            this._setSource(previewEl, fileOrSrc);
        }
    }

    resetPreview(inputEl, previewEl) {
        inputEl.value = ''; // Clear file input value
        const defaultSrc = inputEl.dataset.defaultSrc || this.config.defaultSrc;
        this._setSource(previewEl, defaultSrc);
    }

    _setSource(previewEl, src) {
        if (previewEl instanceof HTMLImageElement) {
            previewEl.src = src;
        } else {
            previewEl.style.backgroundImage = `url('${src}')`;
        }
    }

    _resolvePreview(inputEl) {
        const selector = inputEl.getAttribute('data-target') || inputEl.getAttribute('data-preview-target');
        if (selector) {
            return this.root.querySelector(selector);
        }
        const container = inputEl.closest('.d-flex, .image-input, .mb-7, form') || this.root;
        return container.querySelector('img, [data-preview]');
    }

    _resolveInputFromReset(resetBtn) {
        const selector = resetBtn.getAttribute('data-target') || resetBtn.getAttribute('data-input-target');
        if (selector) {
            return this.root.querySelector(selector);
        }
        const container = resetBtn.closest('.d-flex, .image-input, .mb-7, form') || this.root;
        return container.querySelector('input[type="file"][data-image-preview], .image-preview-input');
    }
}