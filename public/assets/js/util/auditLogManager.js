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
            <div class="d-flex flex-column justify-content-center align-items-center py-12 w-100">
                <div class="spinner-border text-primary-active mb-4" role="status" style="width: 2.25rem; height: 2.25rem; border-width: 0.2rem; --bs-spinner-border-width: 3px;"></div>
                <span class="text-gray-500 fw-medium fs-7 tracking-wide">Retrieving history trail...</span>
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

                if (!response.ok) {
                    throw new Error(`Failed to fetch log notes. HTTP status: ${response.status}`);
                }

                const data = await response.json();

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

    static _buildTimelineHtml(logs) {
        const timelineItems = logs.map((item, logIndex) => {
            const { title, changes } = this._parseLogContent(item.raw_log);
            const action = title.toLowerCase();

            let icon = 'ki-pencil';
            let badgeBg = 'bg-light-primary';
            let iconColor = 'text-primary';

            if (action.includes('create') || action.includes('added')) {
                icon = 'ki-plus';
                badgeBg = 'bg-light-success';
                iconColor = 'text-success';
            } else if (action.includes('delete') || action.includes('remove')) {
                icon = 'ki-trash';
                badgeBg = 'bg-light-danger';
                iconColor = 'text-danger';
            } else if (action.includes('approve') || action.includes('confirm')) {
                icon = 'ki-check';
                badgeBg = 'bg-light-success';
                iconColor = 'text-success';
            } else if (action.includes('reject') || action.includes('decline')) {
                icon = 'ki-cross';
                badgeBg = 'bg-light-danger';
                iconColor = 'text-danger';
            } else if (action.includes('archive')) {
                icon = 'ki-archive';
                badgeBg = 'bg-light-warning';
                iconColor = 'text-warning';
            }

            const visibleLimit = 5;
            const hasMore = changes.length > visibleLimit;
            let changesHtml = '';

            if (changes.length) {
                changesHtml = `
                    <div class="mt-4 p-5 rounded-3 bg-light-soft border border-gray-200">
                        ${changes.map((change, index) => {
                            const hiddenClass = hasMore && index >= visibleLimit ? 'audit-hidden-change d-none' : '';
                            
                            // Dynamically remove border-bottom for the last item in the changes list
                            const isLastChange = index === changes.length - 1;
                            const borderClass = isLastChange ? '' : 'border-bottom border-gray-100';

                            if (change.type === 'note') {
                                return `
                                    <div class="alert alert-custom py-3 px-4 mb-2 bg-white border shadow-xs text-gray-700 fs-7 rounded-2 ${hiddenClass}">
                                        ${this._escapeHtml(change.text)}
                                    </div>
                                `;
                            }

                            if (change.type === 'create') {
                                return `
                                    <div class="row g-2 py-2 align-items-center ${borderClass} ${hiddenClass}">
                                        <div class="col-lg-3">
                                            <span class="text-uppercase text-muted fw-bold fs-9 tracking-wider">${this._escapeHtml(change.field)}</span>
                                        </div>
                                        <div class="col-lg-9">
                                            <span class="fw-semibold text-break text-gray-800 fs-7">${this._escapeHtml(change.value)}</span>
                                        </div>
                                    </div>
                                `;
                            }

                            return `
                                <div class="row g-2 py-2 align-items-center ${borderClass} ${hiddenClass}">
                                    <div class="col-lg-3">
                                        <span class="text-uppercase text-muted fw-bold fs-9 tracking-wider">${this._escapeHtml(change.field)}</span>
                                    </div>
                                    <div class="col-lg-4">
                                        ${change.before ? `<span class="text-muted text-break fs-7 text-decoration-line-through">${this._escapeHtml(change.before)}</span>` : `<span class="badge badge-light-secondary fs-9 rounded-pill">Null</span>`}
                                    </div>
                                    <div class="col-lg-1 text-center d-none d-lg-flex justify-content-center">
                                        <i class="ki-outline ki-arrow-right fs-6 text-gray-400"></i>
                                    </div>
                                    <div class="col-lg-4">
                                        ${change.after ? `<span class="fw-semibold text-break text-gray-900 fs-7">${this._escapeHtml(change.after)}</span>` : `<span class="badge badge-light-secondary fs-9 rounded-pill">Null</span>`}
                                    </div>
                                </div>
                            `;
                        }).join('')}

                        ${hasMore ? `
                            <div class="text-center pt-3">
                                <button class="btn btn-sm btn-link text-primary fw-bold text-decoration-none py-1 fs-7 audit-expand-btn">
                                    Show ${changes.length - visibleLimit} more changes...
                                </button>
                            </div>
                        ` : ''}
                    </div>
                `;
            }

            const safeImgUrl = this._escapeAttribute(item.profile_picture || '');
            const userName = this._escapeHtml(item.user_name || 'System User');
            
            const initials = userName
                .split(' ')
                .map(n => n[0])
                .join('')
                .substring(0, 2)
                .toUpperCase();

            const avatarHtml = safeImgUrl && safeImgUrl !== '#' 
                ? `<img src="${safeImgUrl}" class="rounded-circle w-100 h-100 object-fit-cover" alt="Profile" onerror="this.parentElement.innerHTML='<span class=\'fw-bold text-primary\' style=\'font-size: 7px; line-height: 1;\'>${initials}</span>';">`
                : `<span class="fw-bold text-primary" style="font-size: 7px; line-height: 1;">${initials}</span>`;

            return `
                <div class="timeline-item d-flex align-items-start position-relative pb-8">
                    ${logIndex !== logs.length - 1 ? '<div class="timeline-line position-absolute start-20px top-40px bottom-0 border-start border-2 border-gray-200"></div>' : ''}
                    
                    <div class="timeline-icon me-4 position-relative z-index-1">
                        <div class="symbol symbol-40px">
                            <div class="symbol-label ${badgeBg} rounded-circle border border-white border-2 shadow-sm">
                                <i class="ki-outline ${icon} fs-5 ${iconColor}"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex-grow-1" style="min-width: 0;">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-1">
                            <h5 class="fw-bold text-gray-900 m-0 fs-6">${this._escapeHtml(title)}</h5>
                            <span class="fs-8 text-muted pt-1 pt-sm-0">${this._escapeHtml(item.time_relative)}</span>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="symbol position-relative" style="width: 18px; height: 18px;">
                                <div class="symbol-label bg-light-primary rounded-circle d-flex align-items-center justify-content-center overflow-hidden border border-gray-300" style="width: 18px; height: 18px;">
                                    ${avatarHtml}
                                </div>
                            </div>
                            <span class="fs-8 fw-bold text-gray-700 hover-primary cursor-pointer">${userName}</span>
                        </div>

                        ${changesHtml}
                    </div>
                </div>
            `;
        }).join('');

        return `
            <div class="timeline-container px-2 py-2">
                ${timelineItems}
            </div>
        `;
    }

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
            <div class="notice d-flex bg-light-danger rounded-3 border-danger border border-dashed p-5 w-100">
                <i class="ki-outline ki-information-5 fs-1 text-danger me-4"></i>
                <div class="d-flex flex-stack flex-grow-1">
                    <div class="fw-semibold">
                        <h4 class="text-gray-900 fw-bold fs-7 mb-1">Retrieval Error</h4>
                        <div class="fs-8 text-gray-600">${this._escapeHtml(message)}</div>
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