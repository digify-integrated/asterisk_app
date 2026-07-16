'use strict';

import { DataTableOrchestrator } from '../../util/dataTableOrchestrator.js';
import { AuditLogManager } from '../../util/auditLogManager.js';
import { initValidation } from '../../util/validator.js';
import { FormEnvironmentManager } from '../../util/formEnvironmentManager.js';
import { errorHandler } from '../../util/errorHandler.js';
import { ButtonStateManager } from '../../util/buttonManager.js';
import { DetailFetcher } from '../../util/detailFetcher.js';
import { initConfirmAction } from '../../util/confirmationAction.js';

const CONFIG = {
    selectors: {
        table: '#app-table',
        form: '#app_form',
        submitButton: '#submit-data',
        modal: '#form-modal',
        logNotesTrigger: '.view-log-notes',
        deleteTrigger: '.delete-details',
        updateTrigger: '.update-details',
        createTrigger: '.new-button'
    },
    endpoints: {
        tableData: '/app/generate-table',
        save: '/app/save',
        delete: '/app/delete',
        fetch: '/app/fetch'
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const orchestrator = new DataTableOrchestrator();
    const abortController = new AbortController();
    const { signal } = abortController;

    const escapeHtml = typeof window.e === 'function' 
        ? window.e 
        : (str) => str == null ? '' : String(str).replace(/[&<>"']/g, m => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
          }[m]));

    AuditLogManager.attachLogNotesClassHandler(CONFIG.selectors.logNotesTrigger, 'apps');
   
    orchestrator.initialize({
        selector: CONFIG.selectors.table,
        url: CONFIG.endpoints.tableData,
        columnDefs: [
            { width: '5%', bSortable: false, targets: 0 },
            { width: '20%', targets: 1 },
            { width: '15%', targets: 3 },
            { width: '10%', bSortable: false, targets: 4 },
        ],
        addons: { controls: true, export: true },
        columns: [
            { 
                data: 'id',
                render: (id) => `
                    <div class="form-check form-check-sm ms-5">
                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="${escapeHtml(id)}">
                    </div>`
            },
            { 
                data: null,
                render: (row) => `
                    <div class="d-flex align-items-center">
                        <img src="${escapeHtml(row.logo_url)}" alt="logo" width="45" onerror="this.src='/assets/media/svg/brand-logos/abstract.svg';" />
                        <div class="ms-3"><h6 class="mb-0">${escapeHtml(row.name)}</h6></div>
                    </div>`
            },
            { 
                data: 'description',
                render: (desc) => `<div class="text-gray-800 text-wrap">${escapeHtml(desc)}</div>`
            },
            { 
                data: 'order_sequence',
                render: (seq) => escapeHtml(seq)
            },
            { 
                data: null, 
                render: (data, type, row, meta) => {
                    const perms = meta.settings.json?.permissions || row.permissions || {};
                    const safeId = escapeHtml(row.id);

                    return `
                    <div class="d-flex justify-content-end gap-2 me-5">
                        ${perms.write ? `<button class="btn btn-sm btn-icon btn-light-primary ${CONFIG.selectors.updateTrigger.slice(1)}" data-bs-toggle="modal" data-bs-target="${CONFIG.selectors.modal}" data-reference-id="${safeId}" title="Edit"><i class="ki-outline ki-eye fs-5 m-0"></i></button>` : ''}
                        ${perms.logs ? `<button class="btn btn-sm btn-icon btn-light-warning ${CONFIG.selectors.logNotesTrigger.slice(1)}" data-reference-id="${safeId}" data-bs-toggle="modal" data-bs-target="#log-notes-modal" title="Logs"><i class="ki-outline ki-shield-search fs-5 m-0"></i></button>` : ''}
                        ${perms.delete ? `<button class="btn btn-sm btn-icon btn-light-danger ${CONFIG.selectors.deleteTrigger.slice(1)}" data-reference-id="${safeId}" title="Delete"><i class="ki-outline ki-trash fs-5 m-0"></i></button>` : ''}
                    </div>`;
                }
            }
        ]
    });

    initValidation({
        forms: [
            {
                selector: CONFIG.selectors.form,
                rules: {
                    name: { required: true },
                    description: { required: true },
                    order_sequence: { required: true }
                },
                submitHandler: async (formElement) => {
                    const btn = CONFIG.selectors.submitButton;
                    ButtonStateManager.disable(btn, { loadingText: 'Saving...', showLoader: true });

                    try {
                        const response = await fetch(CONFIG.endpoints.save, {
                            method: 'POST',
                            headers: { 
                                'X-Requested-With': 'XMLHttpRequest', 
                                'Accept': 'application/json' 
                            },
                            body: new FormData(formElement),
                            signal
                        });

                        if (await errorHandler.handleResponse(response, btn)) return;

                        FormEnvironmentManager.resetForm(formElement);
                        
                        $(CONFIG.selectors.modal).modal('hide');
                        
                        orchestrator.reload(CONFIG.selectors.table);
                    } catch (error) {
                        if (error.name === 'AbortError') return; 
                        ButtonStateManager.enable(btn);
                        await errorHandler.handle(error, 'network_failure', 'Transactional pipeline error.');
                    }
                }
            }
        ]
    });

    initConfirmAction({
        trigger: CONFIG.selectors.deleteTrigger,
        url: CONFIG.endpoints.delete,
        method: 'DELETE',
        payload: { app_id: (el) => el.dataset.referenceId },
        swalTitle: 'Delete Record?',
        swalText: 'This action will permanently delete this record and cannot be undone.',
        confirmButtonText: 'Delete Record',
        confirmButtonClass: 'danger',
        onSuccess: () => {
            orchestrator.reload(CONFIG.selectors.table);
        }
    });

    document.addEventListener('click', async (event) => {
        const target = event.target;
        const updateTrigger = target.closest(CONFIG.selectors.updateTrigger);
        const createTrigger = target.closest(CONFIG.selectors.createTrigger);
        
        if (updateTrigger) {
            const refId = updateTrigger.dataset.referenceId;
            
            await DetailFetcher.fetch({
                url: CONFIG.endpoints.fetch,
                detailIdKey: 'app_id',
                detailIdValue: refId,
                signal,
                onSuccess: (response) => {
                    const data = response?.data || response;
                    
                    const fields = {
                        'app_id': refId,
                        'name': data.name,
                        'order_sequence': data.order_sequence,
                        'description': data.description
                    };
                    
                    Object.entries(fields).forEach(([id, val]) => {
                        const el = document.getElementById(id);
                        if (el) {
                            el.value = val ?? '';
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                }
            });
        }

        if (createTrigger) {
            FormEnvironmentManager.resetForm(CONFIG.selectors.form.slice(1));
        }

    }, { signal });
});