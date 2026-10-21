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
                <div class="spinner-border text-primary mb-3" role="status" style="width: 2rem; height: 2rem; border-width: 0.15rem;"></div>
                <span class="text-gray-500 fw-semibold fs-7">Verifying and retrieving audit history...</span>
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
                        this._attachEventListeners(logNotesContainer, data.logs);
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
        const timelineItems = logs.map((item) => {
            const { title, changes } = this._parseLogContent(item.raw_log);
            const action = title.toLowerCase();

            const defaults = { icon: 'ki-pencil', category: 'update', actionVerb: 'Updated' };
            const mappings = [
                { keywords: ['create', 'added', 'approve', 'confirm'], icon: 'ki-plus-circle', category: 'create', actionVerb: 'Added' },
                { keywords: ['delete', 'remove', 'reject', 'decline'], icon: 'ki-trash', category: 'delete', actionVerb: 'Deleted' },
                { keywords: ['archive', 'lock'], icon: 'ki-archive', category: 'archive', actionVerb: 'Archived' }
            ];

            const match = mappings.find(m => m.keywords.some(k => action.includes(k))) || defaults;
            const icon = match.icon;

            const isCreation = match.category === 'create';
            const headerTitle = isCreation ? 'Initial Values' : 'Field Modifications';
            const badgeText = `${changes.length} ${changes.length === 1 ? (isCreation ? 'field' : 'change') : (isCreation ? 'fields' : 'changes')}`;

            const visibleLimit = 4;
            const hasMore = changes.length > visibleLimit;
            let changesHtml = '';

            if (changes.length) {
                changesHtml = `
                    <div class="w-100 pb-3">
                        <div class="d-flex flex-column border border-dashed border-gray-300 rounded w-100 overflow-hidden mb-0 bg-body">
                            <!-- Refined Header Bar -->
                            <div class="bg-light px-4 py-3 border-bottom border-gray-300 d-flex justify-content-between align-items-center">
                                <div class="fw-bold fs-7 text-gray-800 d-flex align-items-center">
                                    <i class="ki-duotone ki-data text-primary me-2 fs-5"></i>
                                    <span>${headerTitle}</span>
                                </div>
                                <span class="badge badge-light-primary fw-bold fs-8 px-2.5 py-1">${badgeText}</span>
                            </div>

                            <div class="audit-changes-list w-100 p-4">
                                ${changes.map((change, index) => {
                                    const hiddenClass = hasMore && index >= visibleLimit ? 'audit-hidden-change d-none' : '';
                                    const isLastChange = index === changes.length - 1;
                                    const borderClass = isLastChange ? '' : 'border-bottom border-gray-200 mb-3 pb-3';

                                    if (change.type === 'note') {
                                        return `
                                            <div class="alert bg-light-primary border-0 py-2 px-3 mb-2 text-gray-700 fs-7 rounded text-wrap text-break ${hiddenClass}">
                                                <i class="ki-duotone ki-information fs-6 text-primary me-2"></i>${this._escapeHtml(change.text)}
                                            </div>
                                        `;
                                    }

                                    if (change.type === 'create') {
                                        return `
                                            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center ${borderClass}${hiddenClass}">
                                                <div class="fw-bold fs-8 text-uppercase text-gray-800 me-sm-4 mb-1 mb-sm-0 text-wrap text-break" style="min-width: 140px; max-width: 200px;">
                                                    ${this._escapeHtml(change.field)}
                                                </div>
                                                <div class="flex-grow-1 w-100">
                                                    <div class="p-2 rounded bg-light-success border border-success border-opacity-50 text-gray-800 fs-7 text-wrap text-break">
                                                        <div class="text-success fw-bold fs-9 text-uppercase mb-1">Value</div>
                                                        ${this._escapeHtml(change.value)}
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    }

                                    return `
                                        <div class="d-flex flex-column flex-sm-row align-items-start ${borderClass}${hiddenClass}">
                                            <div class="fw-bold fs-8 text-uppercase text-gray-800 me-sm-4 mb-2 mb-sm-0 pt-1 text-wrap text-break" style="min-width: 140px; max-width: 200px;">
                                                ${this._escapeHtml(change.field)}
                                            </div>
                                            <div class="row g-2 flex-grow-1 w-100">
                                                <div class="col-12 col-md-6">
                                                    <div class="p-2 rounded bg-light-danger border border-danger border-opacity-25 h-100">
                                                        <div class="text-danger fw-bold fs-9 text-uppercase mb-1">Previous</div>
                                                        <div class="text-gray-800 fs-7 text-wrap text-break">${change.before ? this._escapeHtml(change.before) : '<span class="text-muted fst-italic">Empty / Null</span>'}</div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="p-2 rounded bg-light-success border border-success border-opacity-50 h-100">
                                                        <div class="text-success fw-bold fs-9 text-uppercase mb-1">New</div>
                                                        <div class="text-gray-800 fs-7 text-wrap text-break">${change.after ? this._escapeHtml(change.after) : '<span class="text-muted fst-italic">Cleared</span>'}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>

                            ${hasMore ? `
                                <div class="bg-light px-3 py-2 text-center border-top border-gray-200">
                                    <button class="btn btn-sm btn-link text-primary fw-bold text-decoration-none py-0 fs-8 audit-expand-btn"
                                        data-show-text="Show ${changes.length - visibleLimit} more ${isCreation ? 'field(s)' : 'changes'}..."
                                        data-hide-text="Show fewer ${isCreation ? 'fields' : 'changes'}">
                                        Show ${changes.length - visibleLimit} more ${isCreation ? 'fields' : 'changes'}...
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            }

            const safeImgUrl = this._escapeAttribute(item.profile_picture || '');
            const userName = this._escapeHtml(item.user_name || 'System User');
            const exactTimestamp = this._escapeHtml(item.timestamp || item.created_at || '');
            const relativeTime = this._escapeHtml(item.time_relative || '');

            const initials = userName
                .split(' ')
                .map(n => n[0])
                .join('')
                .substring(0, 2)
                .toUpperCase();

            const avatarHtml = safeImgUrl && safeImgUrl !== '#' 
                ? `<img src="${safeImgUrl}" alt="${userName}" class="w-10 h-10 object-fit-cover">`
                : `<div class="symbol-label fs-8 fw-semibold bg-primary text-inverse-primary">${initials}</div>`;

            return `
                <div class="timeline-item" data-category="${match.category}">
                    <div class="timeline-line"></div>
                    <div class="timeline-icon me-4">
                        <i class="ki-duotone ${icon} fs-2 text-gray-500">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        </i>
                    </div>
                    <div class="timeline-content mb-8 mt-n1 w-100 pe-3">
                        <div class="mb-4">
                            <div class="fs-5 fw-semibold text-gray-900 mb-1 text-wrap text-break">${this._escapeHtml(title)}</div>
                            <div class="d-flex align-items-center flex-wrap fs-6">
                                <span class="text-muted me-2 fs-7">${match.actionVerb} at ${relativeTime || exactTimestamp} by</span>
                                <div class="symbol symbol-circle symbol-25px me-2" data-bs-toggle="tooltip" title="${userName}">
                                    ${avatarHtml}
                                </div>
                                <span class="fw-semibold text-gray-800 fs-7">${userName}</span>
                            </div>
                        </div>
                        ${changesHtml}
                    </div>
                </div>
            `;
        }).join('');

        return `
            <div class="timeline timeline-border-dashed">
                ${timelineItems}
            </div>
        `;
    }

    static _attachEventListeners(container, logs) {
        const expandBtns = container.querySelectorAll('.audit-expand-btn');
        expandBtns.forEach(expandBtn => {
            expandBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const wrapper = expandBtn.closest('.rounded');
                if (!wrapper) return;
                const hiddenChanges = wrapper.querySelectorAll('.audit-hidden-change');
                if (!hiddenChanges.length) return;

                const isExpanded = !hiddenChanges[0].classList.contains('d-none');
                hiddenChanges.forEach(el => el.classList.toggle('d-none', isExpanded));
                expandBtn.textContent = isExpanded ? expandBtn.getAttribute('data-show-text') : expandBtn.getAttribute('data-hide-text');
            });
        });
    }

    static _showInlineError(container, message) {
        if (!container) return;
        container.innerHTML = `
            <div class="notice d-flex bg-light-danger rounded-3 border-danger border border-dashed p-5 w-100">
                <i class="ki-duotone ki-information-5 fs-1 text-danger me-4"></i>
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