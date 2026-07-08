import { DataTableOrchestrator } from '../../util/dataTableOrchestrator.js';
import { AuditLogManager } from '../../util/auditLogManager.js';
import { initValidation } from '../../util/validator.js';
import { FormEnvironmentManager } from '../../util/formEnvironmentManager.js';
import { errorHandler } from '../../util/errorHandler.js';
import { ButtonStateManager } from '../../util/buttonManager.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new DataTableOrchestrator();
    const submitBtn = '#submit-data';

    AuditLogManager.attachLogNotesClassHandler('.view-log-notes', 'apps');

    manager.initialize({
        selector: '#app-table',
        url: '/app/generate-table',
        serverSide: false, 
        pageLength: 10,
        actionDropdown: '.action-dropdown',
        masterCheckbox: '.datatable-checkbox-master',
        lengthSelector: '.datatable-length',
        searchSelector: '.datatable-search',
        columns: [
            { 
                data: 'id',
                render: function (id) {
                    return `
                        <div class="form-check form-check-sm ms-5">
                            <input class="form-check-input datatable-checkbox-children" type="checkbox" value="${id}">
                        </div>`;
                }
            },
            { 
                data: null,
                render: function (row) {
                    const safeName = typeof e === 'function' ? e(row.name) : row.name; 
                    return `
                        <div class="d-flex align-items-center">
                            <img src="${row.logo_url}" alt="app-logo" width="45" />
                            <div class="ms-3">
                                <div class="user-meta-info">
                                    <h6 class="mb-0">${safeName}</h6>
                                </div>
                            </div>
                        </div>`;
                }
            },
            { 
                data: 'description',
                render: function (desc) {
                    const safeDesc = typeof e === 'function' ? e(desc) : desc;
                    return `<div class="text-gray-800 text-wrap">${safeDesc}</div>`;
                }
            },
            { 
                data: null, 
                render: function (data, type, row, meta) {
                    const globalPerms = meta.settings.json.permissions;
                    
                    const canWrite = globalPerms ? globalPerms.write : row.permissions.can_write;
                    const canLogs = globalPerms ? globalPerms.logs : row.permissions.can_logs;
                    const canDelete = globalPerms ? globalPerms.delete : row.permissions.can_delete;

                    const updateBtn = canWrite ? `<button class="btn btn-sm btn-icon btn-light-primary update-details" data-bs-toggle="modal" data-bs-target="#form-modal" data-reference-id="${row.id}" title="Update App"><i class="ki-outline ki-eye fs-5 m-0"></i></button>` : '';
                    const logBtn = canLogs ? `<button class="btn btn-sm btn-icon btn-light-warning view-log-notes" data-reference-id="${row.id}" data-bs-toggle="modal" data-bs-target="#log-notes-modal" title="View System Audit Trail"><i class="ki-outline ki-shield-search fs-5 m-0"></i></button>` : '';
                    const deleteBtn = canDelete ? `<button class="btn btn-sm btn-icon btn-light-danger delete-details" data-reference-id="${row.id}" title="Delete App"><i class="ki-outline ki-trash fs-5 m-0"></i></button>` : '';

                    return `<div class="d-flex justify-content-end gap-2 me-5">${updateBtn} ${logBtn} ${deleteBtn}</div>`;
                }
            }
        ],
        columnDefs: [
            { width: '5%', bSortable: false, targets: 0 },
            { width: 'auto', targets: 1 },
            { width: 'auto', targets: 2 },
            { width: '15%', bSortable: false, targets: 3 },
        ],
        addons: {
            controls: true, 
            export: true
        }
    });

    initValidation({
        forms: [
            {
                selector: '#app_form',
                rules: {
                    name: { required: true },
                    description: { required: true },
                    order_sequence: { required: true }
                },
                submitHandler: async (form) => {
                    ButtonStateManager.disable(submitBtn, {
                        loadingText: 'Saving...',
                        showLoader: true
                    });

                    try {
                        const formData = new FormData(form);

                        const response = await fetch('/app/save', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: formData
                        });

                        if (await errorHandler.handleResponse(response, submitBtn)) {
                            return;
                        }

                        ButtonStateManager.enable(submitBtn);

                        FormEnvironmentManager.resetForm(form);

                        $('#form-modal').modal('hide');

                        manager.reload('#app-table');

                    } catch (error) {
                        ButtonStateManager.enable(submitBtn);
                        await errorHandler.handle(error, 'network_failure', 'App save request failed.');
                    }
                }
            }
        ]
    });
});