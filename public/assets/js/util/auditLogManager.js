'use strict';

import { FormEnvironmentManager } from './formEnvironmentManager.js';
import { errorHandler } from './errorHandler.js';
import { Toast } from './notifications.js';

export class AuditLogManager {
    static _registeredSelectors = new Set();
    static _activeRequests = new Map();

    static attachLogNotesHandler() {
        const selector = '#log-notes-main';
        if (this._registeredSelectors.has(selector)) return;
        this._registeredSelectors.add(selector);

        document.addEventListener('click', async (e) => {
            const btn = e.target.closest(selector);
            if (!btn) return;

            const ctx = FormEnvironmentManager.getPageContext();
            if (ctx?.databaseTable && ctx?.detailId) {
                await this.logNotes(ctx.databaseTable, ctx.detailId);
            }
        });
    }

    static attachLogNotesClassHandler(trigger, databaseTable) {
        if (this._registeredSelectors.has(trigger)) return;
        this._registeredSelectors.add(trigger);

        document.addEventListener('click', async (e) => {
            const btn = e.target.closest(trigger);
            if (!btn) return;

            const referenceId = btn.dataset.referenceId;
            if (referenceId) {
                await this.logNotes(databaseTable, referenceId);
            }
        });
    }

    static async logNotes(databaseTable, referenceId) {
        const logNotesContainer = document.getElementById('log-notes');
        const emptyStateContainer = document.getElementById('log-notes-empty');

        if (!logNotesContainer) {
            console.warn('AuditLogManager: #log-notes container element not found.');
            return;
        }

        const requestKey = `${databaseTable}_${referenceId}`;
        if (this._activeRequests.has(requestKey)) {
            return this._activeRequests.get(requestKey);
        }

        logNotesContainer.innerHTML = `
            <div class="d-flex flex-column justify-content-center align-items-center py-10 w-100">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 2.5rem; height: 2.5rem; border-width: 0.25rem;"></div>
                <span class="text-gray-500 fw-semibold fs-7">Loading log history...</span>
            </div>
        `;
        logNotesContainer.classList.remove('d-none');
        if (emptyStateContainer) emptyStateContainer.classList.add('d-none');

        const fetchPromise = (async () => {
            try {
                const ctx = FormEnvironmentManager.getPageContext() || {};
                const params = new URLSearchParams({
                    appId: ctx.appId ?? '',
                    navigationMenuId: ctx.navigationMenuId ?? '',
                    databaseTable: databaseTable ?? '',
                    referenceId: referenceId ?? ''
                });

                const response = await fetch(`/audit-log/fetch?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                });

                // 🔑 Catch 4xx/5xx failures securely right here
                if (!response.ok) {
                    throw new Error(`Failed to fetch log notes. HTTP status: ${response.status}`);
                }

                const data = await response.json();

                // 🔑 REVISION: Route explicitly using data structural availability and specific session flags instead of data.success
                if (data.invalid_session) {
                    Toast.show(data.message || 'Session expired.', 'warning');
                    if (data.redirect_link) window.location.href = data.redirect_link;
                } else if (data.logs !== undefined) {
                    if (Array.isArray(data.logs) && data.logs.length > 0) {
                        logNotesContainer.innerHTML = this._buildTimelineHtml(data.logs);
                        this._attachExpandButtonListeners(logNotesContainer);
                    } else {
                        logNotesContainer.innerHTML = '';
                        logNotesContainer.classList.add('d-none');
                        if (emptyStateContainer) emptyStateContainer.classList.remove('d-none');
                    }
                } else {
                    Toast.show(data.message || 'Unable to load audit trail records.', 'error');
                    this._showInlineError(logNotesContainer, 'Unable to display records.');
                }
            } catch (error) {
                this._showInlineError(logNotesContainer, 'Failed to connect to the log service. Please try again.');
                errorHandler.handle(error, 'audit_fetch_failed');
            } finally {
                this._activeRequests.delete(requestKey);
            }
        })();

        this._activeRequests.set(requestKey, fetchPromise);
        return fetchPromise;
    }

    /**
     * Safely breaks down text-based change instructions into functional data tokens
     */
    static _parseLogContent(rawLog) {
        if (!rawLog) {
            return { title: 'Record updated', changes: [] };
        }

        const lines = rawLog
            .replace(/<br\s*\/?>/gi, '\n')
            .split('\n')
            .map(l => l.trim())
            .filter(Boolean);

        if (!lines.length) {
            return { title: 'Record updated', changes: [] };
        }

        const title = lines.shift();
        const changes = [];

        for (const line of lines) {
            const updateMatch = line.match(/^(.+?):\s*(.*?)\s*(?:->|→)\s*(.*)$/);

            if (updateMatch) {
                changes.push({
                    type: 'update',
                    field: updateMatch[1].trim(),
                    before: this._stripQuotes(updateMatch[2].trim()),
                    after: this._stripQuotes(updateMatch[3].trim())
                });
            } else {
                const creationMatch = line.match(/^([^:]+):\s*(.*)$/);
                if (creationMatch) {
                    changes.push({
                        type: 'create',
                        field: creationMatch[1].trim(),
                        value: this._stripQuotes(creationMatch[2].trim())
                    });
                } else {
                    changes.push({
                        type: 'note',
                        text: line
                    });
                }
            }
        }

        return { title, changes };
    }

    /**
     * Translates dataset records into secure XSS-proof safe markup environments
     */
    static _buildTimelineHtml(logs) {
        return logs.map((item) => {
            const { title, changes } = this._parseLogContent(item.raw_log);
            const action = title.toLowerCase();

            let icon = 'ki-pencil';
            if (action.includes('create') || action.includes('added')) icon = 'ki-plus';
            else if (action.includes('delete')) icon = 'ki-trash';
            else if (action.includes('approve')) icon = 'ki-check';
            else if (action.includes('reject')) icon = 'ki-cross';
            else if (action.includes('archive')) icon = 'ki-archive';

            const visibleLimit = 8;
            const hasMore = changes.length > visibleLimit;
            let changesHtml = '';

            if (changes.length) {
                changesHtml = `
                    <div class="border-top mt-6 pt-5">
                        ${changes.map((change, index) => {
                            const hiddenClass = hasMore && index >= visibleLimit ? 'audit-hidden-change d-none' : '';

                            if (change.type === 'note') {
                                return `
                                    <div class="alert alert-light-secondary py-3 px-4 mb-3 ${hiddenClass}">
                                        ${this._escapeHtml(change.text)}
                                    </div>
                                `;
                            }

                            if (change.type === 'create') {
                                return `
                                    <div class="row g-3 py-3 align-items-start border-bottom ${hiddenClass}">
                                        <div class="col-lg-3">
                                            <div class="text-uppercase text-muted fw-semibold fs-8">${this._escapeHtml(change.field)}</div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="fw-semibold text-break text-gray-800">${this._escapeHtml(change.value)}</div>
                                        </div>
                                    </div>
                                `;
                            }

                            return `
                                <div class="row g-3 py-3 align-items-start border-bottom ${hiddenClass}">
                                    <div class="col-lg-3">
                                        <div class="text-uppercase text-muted fw-semibold fs-8">${this._escapeHtml(change.field)}</div>
                                    </div>
                                    <div class="col-lg-4">
                                        ${change.before ? `<div class="text-muted text-break">${this._escapeHtml(change.before)}</div>` : `<span class="badge badge-light">Empty</span>`}
                                    </div>
                                    <div class="col-lg-1 text-center d-none d-lg-flex justify-content-center">
                                        <i class="ki-outline ki-arrow-right fs-5 text-gray-400"></i>
                                    </div>
                                    <div class="col-lg-4">
                                        ${change.after ? `<div class="fw-semibold text-break text-gray-900">${this._escapeHtml(change.after)}</div>` : `<span class="badge badge-light">Empty</span>`}
                                    </div>
                                </div>
                            `;
                        }).join('')}

                        ${hasMore ? `
                            <div class="text-center pt-5">
                                <button class="btn btn-sm btn-light-primary audit-expand-btn">
                                    Show ${changes.length - visibleLimit} more changes
                                </button>
                            </div>
                        ` : ''}
                    </div>
                `;
            }

            const safeImgUrl = this._escapeAttribute(item.profile_picture || '');

            return `
                <div class="card shadow-none border mb-6">
                    <div class="card-body p-7">
                        <div class="d-flex align-items-start">
                            <div class="symbol symbol-45px me-5">
                                <div class="symbol-label bg-light">
                                    <i class="ki-outline ${icon} fs-3 text-primary"></i>
                                </div>
                            </div>
                            <div class="grow" style="flex: 1; min-width: 0;">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start">
                                    <div>
                                        <div class="fw-bold fs-5 text-gray-900 mb-1">${this._escapeHtml(title)}</div>
                                        <div class="d-flex flex-wrap align-items-center gap-2 fs-7 text-muted">
                                            <div class="symbol symbol-20px">
                                                <img src="${safeImgUrl}" class="rounded-circle" alt="User Profile" onerror="this.src='assets/media/avatars/blank.png';">
                                            </div>
                                            <span class="fw-semibold text-gray-800">${this._escapeHtml(item.user_name)}</span>
                                            <span>•</span>
                                            <span>${this._escapeHtml(item.time_relative)}</span>
                                        </div>
                                    </div>
                                </div>
                                ${changesHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    /**
     * Binds a local interaction handler directly to the newly injected timeline show-more triggers
     */
    static _attachExpandButtonListeners(container) {
        const expandBtn = container.querySelector('.audit-expand-btn');
        if (!expandBtn) return;

        expandBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const hiddenChanges = container.querySelectorAll('.audit-hidden-change');
            hiddenChanges.forEach(el => el.classList.remove('d-none'));
            expandBtn.parentElement.remove(); 
        });
    }

    static _showInlineError(container, message) {
        if (!container) return;
        container.innerHTML = `
            <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-6 w-100">
                <i class="ki-outline ki-information-5 fs-2tx text-danger me-4"></i>
                <div class="d-flex flex-stack flex-grow-2">
                    <div class="fw-semibold">
                        <h4 class="text-gray-900 fw-bold fs-6 mb-1">System Error</h4>
                        <div class="fs-7 text-gray-700">${this._escapeHtml(message)}</div>
                    </div>
                </div>
            </div>
        `;
    }

    static _stripQuotes(str) {
        return str ? str.replace(/^"|"$/g, '') : '';
    }

    static _escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    static _escapeAttribute(str) {
        if (!str) return '';
        if (/^(javascript:|data:|vbscript:)/i.test(str.trim())) {
            return '#';
        }
        return this._escapeHtml(str);
    }
}