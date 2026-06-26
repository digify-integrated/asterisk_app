'use strict';

import { FormEnvironmentManager } from './formEnvironmentManager.js';
import { errorHandler } from './errorHandler.js';
import { Toast } from './notifications.js';

export class AuditLogManager {
    /**
     * Wires up the default primary layout document log listeners.
     */
    static attachLogNotesHandler() {
        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('#log-notes-main');
            if (!btn) return;

            const ctx = FormEnvironmentManager.getPageContext();
            await this.logNotes(ctx.databaseTable, ctx.detailId);
        });
    }

    /**
     * Wires up contextual class action buttons referencing custom element IDs.
     * @param {string} trigger 
     * @param {string} databaseTable 
     */
    static attachLogNotesClassHandler(trigger, databaseTable) {
        document.addEventListener('click', async (e) => {
            const btn = e.target.closest(trigger);
            if (!btn) return;

            const referenceId = btn.dataset.referenceId;
            await this.logNotes(databaseTable, referenceId);
        });
    }

    /**
     * Fetches and renders audit log histories inside a dynamic Metronic timeline view.
     */
    static async logNotes(databaseTable, referenceId) {
        const logNotesContainer = document.getElementById('log-notes');
        const emptyStateContainer = document.getElementById('log-notes-empty');

        if (!logNotesContainer) {
            console.warn('AuditLogManager: #log-notes container element not found.');
            return;
        }

        logNotesContainer.innerHTML = `
            <div class="d-flex flex-column justify-content-center align-items-center py-10 w-100">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 2.5rem; height: 2.5rem; border-width: 0.25rem;"></div>
                <span class="text-gray-500 fw-semibold fs-7">Loading log history...</span>
            </div>
        `;
        logNotesContainer.classList.remove('d-none');

        if (emptyStateContainer) {
            emptyStateContainer.classList.add('d-none');
        }

        try {
            const ctx = FormEnvironmentManager.getPageContext();

            // Native GET query parameter assembly to fix the payload body breaking bug
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

            if (data.success) {
                if (data.logs && data.logs.length > 0) {
                    logNotesContainer.innerHTML = this._buildTimelineHtml(data.logs);
                } else {
                    logNotesContainer.innerHTML = '';
                    logNotesContainer.classList.add('d-none');
                    if (emptyStateContainer) emptyStateContainer.classList.remove('d-none');
                }
            } else if (data.invalid_session) {
                Toast.show(data.message, 'warning');
                if (data.redirect_link) window.location.href = data.redirect_link;
            } else {
                Toast.show(data.message || 'Unable to load audit trail records.', 'error');
                this._showInlineError(logNotesContainer, 'Unable to display records.');
            }
        } catch (error) {
            this._showInlineError(logNotesContainer, 'Failed to connect to the log service. Please try again.');
            errorHandler.handle(error, 'audit_fetch_failed');
        }
    }

    static _parseLogContent(rawLog) {
        if (!rawLog) return { title: 'Record modified', changes: [] };

        const cleanLog = rawLog.replace(/<br\s*\/?>/gi, '\n');
        const lines = cleanLog.split('\n').map(line => line.trim()).filter(line => line.length > 0);
        
        if (lines.length === 0) return { title: 'Record modified', changes: [] };

        const title = lines[0];
        const changes = [];

        for (let i = 1; i < lines.length; i++) {
            const line = lines[i];
            if (line.includes(':') && line.includes('->')) {
                const colonIndex = line.indexOf(':');
                const fieldName = line.substring(0, colonIndex).trim();
                const valuesPart = line.substring(colonIndex + 1);
                
                const arrowIndex = valuesPart.indexOf('->');
                const oldValue = valuesPart.substring(0, arrowIndex).trim();
                const newValue = valuesPart.substring(arrowIndex + 2).trim();

                changes.push({ field: fieldName, old: oldValue, new: newValue });
            } else {
                changes.push({ field: null, text: line });
            }
        }

        return { title, changes };
    }

    static _buildTimelineHtml(logs) {
        return logs.map((item) => {
            const { title, changes } = this._parseLogContent(item.raw_log);
            
            const isCreation = title.toLowerCase().includes('create') || title.toLowerCase().includes('added');
            const iconClass = isCreation ? 'ki-plus' : 'ki-pencil';

            let changesHtml = '';
            if (changes.length > 0) {
                changesHtml = `
                    <div class="rounded p-4 mt-3 border border-gray-200">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-7 gy-2 mb-0">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-8 text-uppercase gs-0">
                                        <th class="min-w-100px">Field</th>
                                        <th class="min-w-100px">Before</th>
                                        <th class="min-w-50px text-center"></th>
                                        <th class="min-w-100px">After</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-700">
                                    ${changes.map(change => {
                                        if (change.field) {
                                            return `
                                                <tr>
                                                    <td><span class="badge badge-light-dark fw-bold">${this._escapeHtml(change.field)}</span></td>
                                                    <td class="text-gray-500 text-decoration-line-through text-break">${this._escapeHtml(change.old || '[Empty]')}</td>
                                                    <td class="text-center"><i class="ki-outline ki-arrow-right fs-6 text-gray-400"></i></td>
                                                    <td class="text-gray-900 fw-bold text-break">${this._escapeHtml(change.new || '[Empty]')}</td>
                                                </tr>
                                            `;
                                        }
                                        return `
                                            <tr>
                                                <td colspan="4" class="text-gray-600 italic fs-7">${this._escapeHtml(change.text)}</td>
                                            </tr>
                                        `;
                                    }).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            }

            return `
                <div class="timeline-item">
                    <div class="timeline-line mt-2"></div>
                    <div class="timeline-icon mt-1">
                        <span class="badge symbol symbol-circle symbol-30px d-flex justify-content-center align-items-center bg-transparent" style="width:30px; height:30px;">
                            <i class="ki-outline ${iconClass} fs-5"></i>
                        </span>
                    </div>
                    <div class="timeline-content mt-n1 shadow-none">
                        <div class="pe-3">
                            <div class="d-flex flex-stack flex-wrap mb-1">
                                <div class="fs-6 fw-bold text-gray-900 me-2">${this._escapeHtml(title)}</div>
                                <div class="text-gray-500 fs-7 fw-semibold min-w-100px text-end">${this._escapeHtml(item.time_relative)}</div>
                            </div>
                            <div class="d-flex align-items-center mt-1 fs-7">
                                <span class="text-muted me-2">Executed by:</span>
                                <div class="symbol symbol-circle symbol-20px me-2">
                                    <img src="${this._escapeHtml(item.profile_picture)}" alt="User Profile" />
                                </div>
                                <span class="text-primary fw-bold fs-7">${this._escapeHtml(item.user_name)}</span>
                            </div>
                            ${changesHtml}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
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

    static _escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
}