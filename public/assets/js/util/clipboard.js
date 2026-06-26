'use strict';

import { Toast } from './notifications.js';

export class ClipboardManager {
    static DEFAULT_MESSAGES = {
        success: 'Copied successfully to your clipboard.',
        empty: 'There is no textual content available to copy.',
        failure: 'Copy operation rejected or unsupported by the browser.',
    };

    /**
     * Executes copy procedures safely from parameters or specific DOM references.
     * @param {Object} params
     * @param {string} [params.text] - Literal string content to write
     * @param {string|HTMLElement} [params.target] - Target selector or input node to read from
     * @param {boolean} [params.notify=true] - Direct toast delivery flag
     * @param {Object} [params.messages] - Overriding message mappings
     */
    static async copy({ text, target, notify = true, messages = {} } = {}) {
        const msg = { ...this.DEFAULT_MESSAGES, ...messages };
        const value = this._resolveValue(text, target);

        if (!value) {
            if (notify) Toast.show(msg.empty, 'warning');
            return { ok: false, value: '', error: new Error('Empty payload') };
        }

        const useAsync = !!(navigator.clipboard && window.isSecureContext);
        
        try {
            if (useAsync) {
                await navigator.clipboard.writeText(value);
            } else {
                this._executeLegacyCopy(value);
            }

            if (notify) Toast.show(msg.success, 'success');
            return { ok: true, value };
        } catch (err) {
            console.error('[ClipboardManager Fail]:', err);
            if (notify) Toast.show(msg.failure, 'error');
            return { ok: false, value, error: err };
        }
    }

    static _resolveValue(text, target) {
        if (typeof text === 'string' && text) return text;
        if (!target) return '';

        const el = typeof target === 'string' ? document.querySelector(target) : target;
        if (!el) return '';

        if (el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement || el instanceof HTMLSelectElement) {
            return el.value;
        }
        return el.textContent || '';
    }

    static _executeLegacyCopy(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        
        // Prevent layout shift/scrolling while keeping text hidden but selectable
        textArea.style.position = 'fixed';
        textArea.style.top = '-9999px';
        textArea.style.left = '-9999px';
        
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        const success = document.execCommand('copy');
        document.body.removeChild(textArea);

        if (!success) throw new Error('Legacy command returned false status');
    }
}