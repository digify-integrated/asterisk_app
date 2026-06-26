'use strict';

import { ClipboardManager } from './clipboard.js';
import { Toast } from './notifications.js';
import { ButtonStateManager } from './buttonManager.js';

export class SystemErrorHandler {
    constructor(modalId = 'system-error-modal', options = {}) {
        this.modalEl = document.getElementById(modalId);
        if (!this.modalEl) {
            console.warn(`[SystemErrorHandler] Modal target #${modalId} not found in DOM.`);
            return;
        }

        this.bodyContainer = this.modalEl.querySelector('#error-dialog');
        this.copyButton = this.modalEl.querySelector('#copy-error-message');
        
        this.modalInstance = window.bootstrap?.Modal?.getOrCreateInstance(this.modalEl) || null;
        
        this.rawErrorText = '';
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
        if (response.ok) return false;

        // 1. Automatically restore submission button interaction states
        if (submitBtn) {
            ButtonStateManager.enable(submitBtn);
        }

        // 2. Intercept Client-Facing Soft Failures (Auth, Validation, Throttling)
        if ([401, 422, 429].includes(response.status)) {
            try {
                const data = await response.json();
                Toast.show(data.message || 'Request failed processing.', 'error');
            } catch {
                Toast.show('An unexpected client error occurred.', 'error');
            }
            return true;
        }

        // 3. Fall back directly to systemic structural breakdown modal (500, etc.)
        const clonedResponse = response.clone();
        await this.handle(clonedResponse);
        return true;
    }

    _bindCopyEvent() {
        if (!this.copyButton) return;
        this.copyButton.addEventListener('click', async () => {
            await ClipboardManager.copy({
                text: this.rawErrorText,
                messages: { success: 'System diagnostics copied to clipboard!' }
            });
        });
    }

    /**
     * Internal Core Engine: Parses dynamic error variants into the debug layout modal.
     */
    async handle(xhr, status = 'error', error = '') {
        const diagnosticReport = [];
        this.rawErrorText = ''; 

        try {
            if (xhr instanceof Error) {
                diagnosticReport.push(
                    this._row('Status', status),
                    this._row('Error', error || xhr.message || 'Unknown runtime error'),
                    this._row('Error Name', xhr.name)
                );
                if (xhr.stack) diagnosticReport.push(this._preBlock('Stack Trace', xhr.stack));
            } 
            else if (xhr instanceof Response) {
                diagnosticReport.push(
                    this._row('HTTP Status', `${xhr.status} ${xhr.statusText}`),
                    this._row('URL', xhr.url)
                );

                const body = await this._readResponseBody(xhr);

                if (body.kind === 'json') {
                    const data = body.data || {};
                    const msg = data.message || error || xhr.statusText || 'Request failed';

                    diagnosticReport.push(this._row('Message', msg));
                    if (data.errors) diagnosticReport.push(this._formatLaravelErrors(data.errors));
                    if (data.exception) diagnosticReport.push(this._row('Exception', data.exception));
                    if (data.file) diagnosticReport.push(this._row('File', `${data.file} : L${data.line || '?'}`));

                    diagnosticReport.push(this._preBlock('Response JSON', JSON.stringify(data, null, 2)));
                } else {
                    diagnosticReport.push(
                        this._row('Message', error || xhr.statusText || 'Server Error'),
                        this._preBlock('Response Diagnostics / Page Output', body.data || 'No response details available.')
                    );
                }
            } 
            else {
                const passedStatus = xhr?.status ?? status;
                diagnosticReport.push(
                    this._row('Status', passedStatus),
                    this._row('Error', error || xhr?.message || 'Unknown context error')
                );

                const text = xhr?.responseText ?? (typeof xhr === 'object' ? JSON.stringify(xhr) : String(xhr));
                if (text) diagnosticReport.push(this._preBlock('Context Payload Data', text));
            }
        } catch (criticalMappingError) {
            diagnosticReport.push(this._row('Parser Failure', criticalMappingError.message));
        }

        this._renderAndShow(diagnosticReport);
    }

    _renderAndShow(elementsArray) {
        if (!this.bodyContainer) return;
        
        this.bodyContainer.innerHTML = '';
        elementsArray.forEach(el => this.bodyContainer.appendChild(el));

        if (this.modalInstance) {
            this.modalInstance.show();
        } else if (window.jQuery) {
            window.jQuery(this.modalEl).modal('show');
        }
    }

    _row(label, text) {
        const div = document.createElement('div');
        div.className = 'mb-2';
        div.innerHTML = `<strong class="text-gray-800">${this._escape(label)}:</strong> `;
        
        const span = document.createElement('span');
        span.className = 'text-gray-700';
        span.textContent = text;
        div.appendChild(span);

        this.rawErrorText += `${label}: ${text}\n`;
        return div;
    }

    _preBlock(title, data) {
        const wrap = document.createElement('div');
        wrap.className = 'mt-3 mb-2';
        wrap.innerHTML = `<div class="fw-bold text-muted small text-uppercase mb-1">${this._escape(title)}:</div>`;
        
        const pre = document.createElement('pre');
        pre.className = 'p-4 bg-light-danger text-danger rounded fs-7 m-0 border border-dashed border-danger';
        pre.style.overflowX = 'auto';
        pre.style.whiteSpace = 'pre-wrap';
        pre.textContent = data;
        
        wrap.appendChild(pre);

        this.rawErrorText += `\n--- ${title} ---\n${data}\n`;
        return wrap;
    }

    _formatLaravelErrors(errors) {
        const wrap = document.createElement('div');
        wrap.className = 'alert alert-sm alert-light-danger p-3 mt-2 rounded';
        
        const ul = document.createElement('ul');
        ul.className = 'm-0 ps-3 fs-7 text-danger';

        this.rawErrorText += `\nValidation Errors:\n`;

        Object.entries(errors).forEach(([field, messages]) => {
            const list = Array.isArray(messages) ? messages : [messages];
            list.forEach(m => {
                const li = document.createElement('li');
                li.innerHTML = `<strong>${this._escape(field)}:</strong> `;
                
                const txt = document.createTextNode(m);
                li.appendChild(txt);
                ul.appendChild(li);

                this.rawErrorText += ` - ${field}: ${m}\n`;
            });
        });

        wrap.appendChild(ul);
        return wrap;
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
                    return { kind: 'text', data: `[Server Exception Elements]: ${exceptionMessage.trim()}` };
                }

                const bodyText = doc.body?.innerText?.trim();
                if (bodyText) {
                    return { kind: 'text', data: bodyText.slice(0, 1500) + '\n\n...[Truncated for clarity]...' };
                }
            }

            return { kind: 'text', data: trimmedText || 'No response body payload available.' };
        } catch (readError) {
            return { kind: 'opaque', data: `Failed to stream response contents: ${readError.message}` };
        }
    }

    _escape(str) {
        return String(str ?? '').replace(/[&<>"']/g, m => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        })[m]);
    }
}

export const errorHandler = new SystemErrorHandler();