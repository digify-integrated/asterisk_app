'use strict';

import { Toast } from './notifications.js';

/**
 * Enterprise Clipboard Operations Manager
 * Hardened for silent layout focus protection, trimming normalization, and cross-browser handling.
 */
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

        // Check modern Clipboard API availability and Secure Context invariant constraints
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

    /**
     * Resolves raw context strings or DOM input pointers safely into standardized text payloads.
     */
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

        // Enterprise Normalization: Drop trailing whitespaces or carriage breaks caused by layout indentations
        return this._sanitizePayload(resolved);
    }

    /**
     * Executes legacy document command triggers while strictly preserving user focus layouts.
     */
    static _executeLegacyCopy(text) {
        // Cache active focus descriptor element before running mutations to protect user context
        const originalActiveElement = document.activeElement;

        const textArea = document.createElement('textarea');
        textArea.value = text;
        
        // Lock dimensions completely down to eliminate visible viewport hopping
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
        
        // Run clean programmatic text selection ranges
        textArea.select();
        textArea.setSelectionRange(0, text.length);

        const success = document.execCommand('copy');
        document.body.removeChild(textArea);

        // A11y Focus Restoration: Return active state context to original interacting target element cleanly
        if (originalActiveElement && typeof originalActiveElement.focus === 'function') {
            originalActiveElement.focus({ preventScroll: true });
        }

        if (!success) {
            throw new Error('Legacy command execution rejected by agent environment layout controls.');
        }
    }

    /**
     * Normalizes white space structures and mitigates command-injection payload tricks.
     */
    static _sanitizePayload(str) {
        if (!str) return '';
        
        // 1. Strip structural spacing formatting anomalies entirely
        let sanitized = str.trim();

        // 2. Mitigate Clipboard-based command injection exploits (RTLO characters, hidden terminal characters)
        // This blocks malicious strings often used to hide commands behind a fake benign string path
        sanitized = sanitized.replace(/[\u202E\u200B\uFEFF]/g, '');

        return sanitized;
    }
}