'use strict';

import { DataTableOrchestrator } from '../../util/dataTableOrchestrator.js';
import { AuditLogManager } from '../../util/auditLogManager.js';
import { initValidation } from '../../util/validator.js';
import { FormEnvironmentManager } from '../../util/formEnvironmentManager.js';
import { errorHandler } from '../../util/errorHandler.js';
import { ButtonStateManager } from '../../util/buttonManager.js';
import { DetailFetcher } from '../../util/detailFetcher.js';
import { initConfirmAction } from '../../util/confirmationAction.js';

document.addEventListener('DOMContentLoaded', () => {
    const initializedTables = new Map();
    const abortController = new AbortController();

    // HTML Escaper helper
    const escapeHtml = typeof window.e === 'function' ? window.e : (str) => 
        str == null ? '' : String(str).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));

    // 1. Initialize Audit Logs
    AuditLogManager.attachLogNotesClassHandler('.view-log-notes', 'apps');

    // 2. Initialize Data Tables
    const orchestrator = new DataTableOrchestrator();
    orchestrator.initialize({
        selector: '#app-table',
        url: '/app/generate-table',
        serverSide: false, 
        pageLength: 10,
        actionDropdown: '.action-dropdown',
        masterCheckbox: '.datatable-checkbox-master',
        lengthSelector: '.datatable-length',
        searchSelector: '.datatable-search',
        columnDefs: [
            { width: '5%', bSortable: false, targets: 0 },
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
                        ${perms.write || perms.can_write ? `<button class="btn btn-sm btn-icon btn-light-primary update-details" data-bs-toggle="modal" data-bs-target="#form-modal" data-reference-id="${safeId}" title="Edit"><i class="ki-outline ki-eye fs-5 m-0"></i></button>` : ''}
                        ${perms.logs || perms.can_logs ? `<button class="btn btn-sm btn-icon btn-light-warning view-log-notes" data-reference-id="${safeId}" data-bs-toggle="modal" data-bs-target="#log-notes-modal" title="Logs"><i class="ki-outline ki-shield-search fs-5 m-0"></i></button>` : ''}
                        ${perms.delete || perms.can_delete ? `<button class="btn btn-sm btn-icon btn-light-danger delete-details" data-reference-id="${safeId}" title="Delete"><i class="ki-outline ki-trash fs-5 m-0"></i></button>` : ''}
                    </div>`;
                }
            }
        ]
    });

    initializedTables.set('#app-table', orchestrator);

    // 3. Initialize Form Validation & Form Submission Pipelines
    initValidation({
        forms: [
            {
                selector: '#app_form',
                rules: {
                    name: { required: true },
                    description: { required: true },
                    order_sequence: { required: true }
                },
                submitHandler: async (formElement) => {
                    const btn = '#submit-data';
                    ButtonStateManager.disable(btn, { loadingText: 'Saving...', showLoader: true });

                    try {
                        const response = await fetch('/app/save', {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            body: new FormData(formElement)
                        });

                        if (await errorHandler.handleResponse(response, btn)) return;

                        ButtonStateManager.enable(btn);
                        FormEnvironmentManager.resetForm(formElement);
                        
                        if (window.jQuery) {
                            window.jQuery('#form-modal').modal('hide');
                        }

                        initializedTables.forEach((orchestratorInstance, selectorKey) => {
                            orchestratorInstance.reload(selectorKey);
                        });
                    } catch (error) {
                        ButtonStateManager.enable(btn);
                        await errorHandler.handle(error, 'network_failure', 'Transactional pipeline error.');
                    }
                }
            }
        ]
    });

    // 4. Initialize Confirmation Triggers (e.g., Deletes)
    initConfirmAction({
        trigger: '.delete-details',
        url: '/app/delete',
        method: 'DELETE',
        payload: { app_id: (el) => el.dataset.referenceId },
        swalTitle: 'Are you sure?',
        swalText: 'This will permanently delete this record!',
        confirmButtonText: 'Yes, delete it',
        confirmButtonClass: 'danger',
        onSuccess: () => {
            initializedTables.get('#app-table')?.reload('#app-table');
        }
    });

    // 5. Global Event Delegations (Edit Hydration / Create Reset)
    document.addEventListener('click', async (event) => {
        const target = event.target;
        const updateTrigger = target.closest('.update-details');
        const createTrigger = target.closest('.new-button');
        
        if (updateTrigger) {
            await DetailFetcher.fetch({
                url: '/app/fetch',
                detailIdKey: 'app_id',
                detailIdValue: updateTrigger.dataset.referenceId,
                onSuccess: (response) => {
                    const data = response?.data || response;
                    const fields = {
                        'app_id': updateTrigger.dataset.referenceId,
                        'name': data.name,
                        'order_sequence': data.order_sequence,
                        'description': data.description
                    };
                    
                    Object.entries(fields).forEach(([id, val]) => {
                        const el = document.getElementById(id);
                        if (el) {
                            el.value = val;
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                }
            });
        }

        if (createTrigger) {
            FormEnvironmentManager.resetForm('app_form');
        }

    }, { signal: abortController.signal });
});