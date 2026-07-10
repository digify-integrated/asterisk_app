'use strict';

import { ClipboardManager } from './clipboard.js';
import { Toast } from './notifications.js';
import { ButtonStateManager } from './buttonManager.js';

export class SystemErrorHandler {
    constructor(modalId = 'system-error-modal') {
        this.modalEl = document.getElementById(modalId);
        if (!this.modalEl) {
            console.warn(`[SystemErrorHandler] Modal target #${modalId} not found in DOM.`);
            return;
        }

        this.bodyContainer = this.modalEl.querySelector('#error-dialog');
        this.copyButton = this.modalEl.querySelector('#copy-error-message');
        
        this.modalInstance = window.bootstrap?.Modal?.getOrCreateInstance(this.modalEl) || null;
        
        // Use a clean local instance registry tracking individual modal presentation states
        this._currentDiagnosticBuffer = '';
        this._bindCopyEvent();
    }

    /**
     * Unified response handler interceptor.
     * Evaluates response status, releases button UI states, and routes to either Toast or Modal.
     * @param {Response} response - Fetch response stream.
     * @param {string|HTMLElement|null} [submitBtn=null] - Optional button selector to auto-enable.
     * @returns {Promise<boolean>} - Returns true if an error was handled, false otherwise.
     */
    async handleResponse(response, submitBtn = null) {
        if (!response || response.ok) return false;

        // 1. Automatically restore submission button interaction states
        if (submitBtn) {
            ButtonStateManager.enable(submitBtn);
        }

        // 2. Safely capture stream clone to prevent structural extraction crashes
        const softClone = response.clone();

        // 3. Intercept Client-Facing Soft Failures (Auth, Validation, Throttling)
        if ([401, 422, 429].includes(response.status)) {
            try {
                const data = await softClone.json();
                Toast.show(data.message || 'Request failed processing.', 'error');
            } catch {
                try {
                    const fallbackText = await softClone.text();
                    Toast.show(fallbackText.slice(0, 200) || 'An unexpected client error occurred.', 'error');
                } catch {
                    Toast.show('An unexpected client error occurred.', 'error');
                }
            }
            return true;
        }

        // 4. Fall back directly to systemic structural breakdown modal (500, etc.)
        const modalClone = response.clone();
        await this.handle(modalClone);
        return true;
    }

    _bindCopyEvent() {
        if (!this.copyButton) return;
        this.copyButton.addEventListener('click', async () => {
            if (!this._currentDiagnosticBuffer) {
                Toast.show('No diagnostic reports found to copy.', 'warning');
                return;
            }
            await ClipboardManager.copy({
                text: this._currentDiagnosticBuffer,
                messages: { success: 'System diagnostics copied to clipboard!' }
            });
        });
    }

    /**
     * Internal Core Engine: Parses dynamic error variants into the debug layout modal.
     */
    async handle(xhr, status = 'error', error = '') {
        const diagnosticReport = [];
        
        // Transaction Isolation Strategy: Reset buffer for each execution pass
        let runtimeBuffer = '';

        const appendRow = (label, value) => {
            const rowNode = this._createRowNode(label, value);
            diagnosticReport.push(rowNode);
            runtimeBuffer += `${label}: ${value}\n`;
        };

        const appendBlock = (title, data) => {
            const blockNode = this._createBlockNode(title, data);
            diagnosticReport.push(blockNode);
            runtimeBuffer += `\n--- ${title} ---\n${data}\n`;
        };

        try {
            if (xhr instanceof Error) {
                appendRow('Status', status);
                appendRow('Error', error || xhr.message || 'Unknown runtime error');
                appendRow('Error Name', xhr.name);
                if (xhr.stack) appendBlock('Stack Trace', xhr.stack);
            } 
            else if (xhr instanceof Response) {
                appendRow('HTTP Status', `${xhr.status} ${xhr.statusText}`);
                appendRow('URL', xhr.url);

                const body = await this._readResponseBody(xhr);

                if (body.kind === 'json') {
                    const data = body.data || {};
                    const msg = data.message || error || xhr.statusText || 'Request failed';

                    appendRow('Message', msg);
                    if (data.errors) {
                        const { node, text } = this._formatLaravelErrors(data.errors);
                        diagnosticReport.push(node);
                        runtimeBuffer += text;
                    }
                    if (data.exception) appendRow('Exception', data.exception);
                    if (data.file) appendRow('File', `${data.file} : L${data.line || '?'}`);

                    appendBlock('Response JSON', JSON.stringify(data, null, 2));
                } else {
                    appendRow('Message', error || xhr.statusText || 'Server Error');
                    appendBlock('Response Diagnostics / Page Output', body.data || 'No response details available.');
                }
            } 
            else {
                const passedStatus = xhr?.status ?? status;
                appendRow('Status', passedStatus);
                appendRow('Error', error || xhr?.message || 'Unknown context error');

                const text = xhr?.responseText ?? (typeof xhr === 'object' ? JSON.stringify(xhr) : String(xhr));
                if (text) appendBlock('Context Payload Data', text);
            }
        } catch (criticalMappingError) {
            appendRow('Parser Failure', criticalMappingError.message);
        }

        // Apply state safely back to the instance layer
        this._currentDiagnosticBuffer = runtimeBuffer;
        this._renderAndShow(diagnosticReport);
    }

    _renderAndShow(elementsArray) {
        if (!this.bodyContainer) return;
        
        this.bodyContainer.replaceChildren(...elementsArray);

        if (this.modalInstance) {
            this.modalInstance.show();
        } else if (window.jQuery) {
            window.jQuery(this.modalEl).modal('show');
        }
    }

    _createRowNode(label, text) {
        const div = document.createElement('div');
        div.className = 'mb-2';
        
        const strong = document.createElement('strong');
        strong.className = 'text-gray-800';
        strong.textContent = `${label}: `; // Secure Text Assignment Vector
        
        const span = document.createElement('span');
        span.className = 'text-gray-700';
        span.textContent = text;
        
        div.appendChild(strong);
        div.appendChild(span);
        return div;
    }

    _createBlockNode(title, data) {
        const wrap = document.createElement('div');
        wrap.className = 'mt-3 mb-2';
        
        const header = document.createElement('div');
        header.className = 'fw-bold text-muted small text-uppercase mb-1';
        header.textContent = `${title}: `;
        
        const pre = document.createElement('pre');
        pre.className = 'p-4 bg-light-danger text-danger rounded fs-7 m-0 border border-dashed border-danger';
        pre.style.overflowX = 'auto';
        pre.style.whiteSpace = 'pre-wrap';
        pre.textContent = data;
        
        wrap.appendChild(header);
        wrap.appendChild(pre);
        return wrap;
    }

    _formatLaravelErrors(errors) {
        const wrap = document.createElement('div');
        wrap.className = 'alert alert-sm alert-light-danger p-3 mt-2 rounded';
        
        const ul = document.createElement('ul');
        ul.className = 'm-0 ps-3 fs-7 text-danger';

        let textBuffer = '\nValidation Errors:\n';

        Object.entries(errors).forEach(([field, messages]) => {
            const list = Array.isArray(messages) ? messages : [messages];
            list.forEach(m => {
                const li = document.createElement('li');
                
                const strong = document.createElement('strong');
                strong.textContent = `${field}: `;
                
                const txt = document.createTextNode(m);
                
                li.appendChild(strong);
                li.appendChild(txt);
                ul.appendChild(li);

                textBuffer += ` - ${field}: ${m}\n`;
            });
        });

        wrap.appendChild(ul);
        return { node: wrap, text: textBuffer };
    }

    async _readResponseBody(response) {
        try {
            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                const data = await response.json();
                return { kind: 'json', data };
            }
            
            const textData = await response.text();
            const trimmedText = textData.trim();

            if (trimmedText.includes('<!DOCTYPE html>') || trimmedText.includes('<html')) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(trimmedText, 'text/html');
                const exceptionMessage = doc.querySelector('.exception-message, #trace-box, .message, title')?.innerText;
                
                if (exceptionMessage) {
                    return { kind: 'text', data: `[Server Exception]: ${exceptionMessage.trim()}` };
                }

                const bodyText = doc.body?.innerText?.trim();
                if (bodyText) {
                    return { kind: 'text', data: bodyText.slice(0, 1500) + '\n\n...[Truncated Response Output]...' };
                }
            }

            return { kind: 'text', data: trimmedText || 'No response body payload available.' };
        } catch (readError) {
            return { kind: 'opaque', data: `Failed to stream response contents: ${readError.message}` };
        }
    }
}

export const errorHandler = new SystemErrorHandler();