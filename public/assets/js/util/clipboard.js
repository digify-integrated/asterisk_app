'use strict';

import { Toast } from './notifications.js';

export class ClipboardManager {
    static DEFAULT_MESSAGES = {
        success: 'Copied successfully to your clipboard.',
        empty: 'There is no textual content available to copy.',
        failure: 'Copy operation rejected or unsupported by the browser.',
    };

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
        if (typeof text === 'string' && text) return text.trim();
        if (!target) return '';

        const el = typeof target === 'string' ? document.querySelector(target) : target;
        if (!el) return '';

        let resolved = '';
        if (el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement || el instanceof HTMLSelectElement) {
            resolved = el.value;
        } else {
            resolved = el.textContent || '';
        }

        return this._sanitizePayload(resolved);
    }

    static _executeLegacyCopy(text) {
        const originalActiveElement = document.activeElement;

        const textArea = document.createElement('textarea');
        textArea.value = text;
        
        textArea.setAttribute('readonly', '');
        textArea.style.position = 'fixed';
        textArea.style.top = '0';
        textArea.style.left = '-9999px';
        textArea.style.width = '2em';
        textArea.style.height = '2em';
        textArea.style.padding = '0';
        textArea.style.border = 'none';
        textArea.style.outline = 'none';
        textArea.style.boxShadow = 'none';
        textArea.style.background = 'transparent';
        
        document.body.appendChild(textArea);
        
        textArea.select();
        textArea.setSelectionRange(0, text.length);

        const success = document.execCommand('copy');
        document.body.removeChild(textArea);

        if (originalActiveElement && typeof originalActiveElement.focus === 'function') {
            originalActiveElement.focus({ preventScroll: true });
        }

        if (!success) {
            throw new Error('Legacy command execution rejected by agent environment layout controls.');
        }
    }

    static _sanitizePayload(str) {
        if (!str) return '';
        
        let sanitized = str.trim();

        sanitized = sanitized.replace(/[\u202E\u200B\uFEFF]/g, '');

        return sanitized;
    }
}