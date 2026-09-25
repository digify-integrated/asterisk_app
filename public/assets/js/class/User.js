'use strict';

import { PageInitializer } from '../util/pageInitializer.js';
import { DataTableOrchestrator } from '../util/dataTableOrchestrator.js';
import { AuditLogManager } from '../util/auditLogManager.js';
import { initValidation } from '../util/validator.js';
import { FormEnvironmentManager } from '../util/formEnvironmentManager.js';
import { errorHandler } from '../util/errorHandler.js';
import { ButtonStateManager } from '../util/buttonManager.js';
import { DetailFetcher } from '../util/detailFetcher.js';
import { initConfirmAction } from '../util/confirmationAction.js';
import { ComponentRegistry } from '../util/componentRegistry.js';
import { TableFilterManager } from '../util/tableFilterManager.js';
import { SaveFilterManager } from '../util/saveFilterManager.js';
import { escapeHtml } from '../util/sanitize.js';
import { PasswordToggle } from '../util/passwordToggle.js';

const CONFIG = {
    selectors: {
        table: '#user-table',
        tableColumn: '#user-table-column-dropdown',
        form: '#user_form',
        formId: 'user_form',
        detailId: 'user_id',
        submitButton: '#submit-data',
        modal: '#form-modal',
        logNotesModal: '#log-notes-modal',
        logNotesTrigger: '.view-log-notes',
        deleteMultipleTrigger: '#delete-data',
        deleteTrigger: '.delete-details',
        updateTrigger: '.update-details',
        createTrigger: '.new-button',
        checkboxes: '.datatable-checkbox-children:checked',
        filterCollapse: 'user-filter-collapse',
        filterCreatedDate: '#filter_created_date',
        filterStatus: '#filter_status'
    },
    classes: {
        logNotesTrigger: 'view-log-notes',
        deleteTrigger: 'delete-details',
        updateTrigger: 'update-details'
    },
    endpoints: {
        tableData: '/user/generate-table',
        save: '/user/save',
        delete: '/user/delete',
        deleteMultiple: '/user/delete-multiple',
        fetch: '/user/fetch'
    }
};
    
export class User {
    constructor() {
        this.orchestrator = new DataTableOrchestrator();
        this.abortController = new AbortController();

        this.filterManager = new TableFilterManager({
            containerId: CONFIG.selectors.filterCollapse,
            orchestrator: this.orchestrator,
            tableSelector: CONFIG.selectors.table
        });

        this.saveFilterManager = new SaveFilterManager({
            filterManager: this.filterManager
        });
        
        this.dom = {
            table: document.querySelector(CONFIG.selectors.table),
            form: document.querySelector(CONFIG.selectors.form),
            modal: $(CONFIG.selectors.modal),
            filterDate: document.querySelector(CONFIG.selectors.filterCreatedDate),
            filterStatus: document.querySelector(CONFIG.selectors.filterStatus)
        };

        this.passwordToggle = new PasswordToggle();
    }

    async init() {
        return PageInitializer.run(async () => {
            await this.saveFilterManager.checkAndApplyDefaultFilter();
            this.initTable();

            await Promise.all([
                this.initForm(),
                this.initDelete(),
                this.initDateRangePicker(),
                this.registerGlobalListeners()
            ]);
                                                    
            AuditLogManager.attachLogNotesClassHandler(CONFIG.selectors.logNotesTrigger, 'users');
        });
    }

    destroy() {
        this.abortController.abort();
    }

    initTable() {
        this.orchestrator.initialize({
            selector: CONFIG.selectors.table,
            url: CONFIG.endpoints.tableData,
            ajaxData: (d) => Object.assign({}, d, {
                filter_status: $(CONFIG.selectors.filterStatus).val() || [],
                filter_created_date: this.dom.filterDate?.value || '',
            }),
            colVisContainer: CONFIG.selectors.tableColumn,
            order: [[2, 'asc']],
            exportColumns: [2, 3, 4],
            addons: { 
                controls: true, 
                export: true,
                columnVisibility: true
            },
            columnDefs: [
                { width: '5%', orderable: false, targets: 0 },
                { width: '5%', orderable: false, targets: 1 },
                { width: '10%', orderable: false, targets: 6 },
            ],
            columns: [
                { 
                    data: 'id',
                    render: (id) => `
                        <div class="form-check form-check-sm ms-5">
                            <input class="form-check-input datatable-checkbox-children" type="checkbox" value="${escapeHtml(id)}">
                        </div>`
                },
                { 
                    data: 'profile_picture',
                    render: (data, type, row) => `<img src="${escapeHtml(row.profile_picture)}" alt="User Profile Picture" width="45" onerror="this.src='/assets/media/default/default-avatar.jpg';" />`
                },
                { 
                    data: 'name',
                    title: 'User',
                },
                { 
                    data: 'email',
                    title: 'Email',
                },
                { 
                    data: 'status',
                    title: 'Status',
                    render: (status) => {
                        const statusVal = (status || '').toString();
                        const isSuccess = statusVal.toLowerCase() === 'active';
                        const badgeClass = isSuccess ? 'badge-light-success' : 'badge-light-danger';
                        
                        return `<span class="badge ${badgeClass} fw-bold px-3 py-2">${escapeHtml(statusVal)}</span>`;
                    }
                },
                { 
                    data: 'created_at',
                    title: 'Created At',
                    visible: false
                },
                { 
                    data: null, 
                    title: '&nbsp;',
                    render: (data, type, row, meta) => {
                        const perms = meta.settings.json?.permissions || row.permissions || {};
                        const safeId = escapeHtml(row.id);

                        const canWrite = perms.write;
                        const canLogs = perms.logs;
                        const canDelete = perms.delete;

                        return `
                        <div class="d-flex justify-content-end gap-2 me-5">
                            ${canWrite ? `<button class="btn btn-sm btn-icon btn-light-primary ${CONFIG.classes.updateTrigger}" data-bs-toggle="modal" data-bs-target="${CONFIG.selectors.modal}" data-reference-id="${safeId}" title="Edit"><i class="ki-outline ki-eye fs-5 m-0"></i></button>` : ''}
                            ${canLogs ? `<button class="btn btn-sm btn-icon btn-light-warning ${CONFIG.classes.logNotesTrigger}" data-reference-id="${safeId}" data-bs-toggle="modal" data-bs-target="${CONFIG.selectors.logNotesModal}" title="Logs"><i class="ki-outline ki-shield-search fs-5 m-0"></i></button>` : ''}
                            ${canDelete ? `<button class="btn btn-sm btn-icon btn-light-danger ${CONFIG.classes.deleteTrigger}" data-reference-id="${safeId}" title="Delete"><i class="ki-outline ki-trash fs-5 m-0"></i></button>` : ''}
                        </div>`;
                    }
                }
            ]
        });
    }

    initForm() {
        initValidation({
            forms: [
                {
                    selector: CONFIG.selectors.form,
                    rules: {
                        name: { required: true },
                        email: { 
                            required: true,
                            typeEmail: true
                        },
                        password: { 
                            requiredIf: {
                                selector: '[name="user_id"]',
                                value: ''
                            },
                            passwordStrength: 'medium' 
                        },
                        status: { required: true }
                    },
                    messages: {
                        password: {
                            requiredIf: 'Password is required when creating a new user.'
                        }
                    },
                    submitHandler: async (formElement) => this.handleFormSubmission(formElement)
                }
            ]
        });
    }

    async handleFormSubmission(formElement) {
        const btn = CONFIG.selectors.submitButton;
        ButtonStateManager.disable(btn, { loadingText: 'Saving...' });

        try {
            const response = await fetch(CONFIG.endpoints.save, {
                method: 'POST',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest', 
                    'Accept': 'application/json' 
                },
                body: new FormData(formElement),
                signal: this.abortController.signal
            });

            if (await errorHandler.handleResponse(response, btn)) return;

            this.dom.modal.modal('hide');
            FormEnvironmentManager.resetForm(formElement);
            this.orchestrator.reload(CONFIG.selectors.table);
        } catch (error) {
            if (error.name === 'AbortError') return; 
            ButtonStateManager.enable(btn);
            await errorHandler.handle(error, 'network_failure', 'Transactional pipeline error.');
        }
    }

    initDelete() {
        initConfirmAction({
            trigger: CONFIG.selectors.deleteTrigger,
            url: CONFIG.endpoints.delete,
            method: 'DELETE',
            payload: { user_id: (el) => el.dataset.referenceId },
            swalTitle: 'Delete Record?',
            swalText: 'This action will permanently delete this record and cannot be undone.',
            confirmButtonText: 'Delete Record',
            confirmButtonClass: 'danger',
            onSuccess: () => this.orchestrator.reload(CONFIG.selectors.table)
        });

        initConfirmAction({
            trigger: CONFIG.selectors.deleteMultipleTrigger,
            url: CONFIG.endpoints.deleteMultiple,
            method: 'DELETE',
            payload: { 
                'user_id': () => {
                    const checked = this.dom.table.querySelectorAll(CONFIG.selectors.checkboxes);
                    return Array.from(checked, cb => Number(cb.value)).join(',');
                }
            },
            swalTitle: 'Delete Multiple Records?',
            swalText: 'This action will permanently delete the selected records and cannot be undone.',
            confirmButtonText: 'Delete Records',
            confirmButtonClass: 'danger',
            onSuccess: () => this.orchestrator.reload(CONFIG.selectors.table)
        });
    }

    initDateRangePicker() {
        ComponentRegistry.initializeDateRangePicker({
            selector: CONFIG.selectors.filterCreatedDate
        });
    }

    registerGlobalListeners() {
        document.addEventListener('click', async (event) => {
            const { target } = event;
            
            const updateTrigger = target.closest(CONFIG.selectors.updateTrigger);
            if (updateTrigger) {
                FormEnvironmentManager.resetForm(CONFIG.selectors.formId);
                this.handleFetchWorkflow(updateTrigger.dataset.referenceId);
                return;
            }
            
            const createTrigger = target.closest(CONFIG.selectors.createTrigger);
            if (createTrigger) {
                FormEnvironmentManager.resetForm(CONFIG.selectors.formId);
            }
        }, { signal: this.abortController.signal });
    }

    async handleFetchWorkflow(referenceId) {
        await DetailFetcher.fetch({
            url: CONFIG.endpoints.fetch,
            detailIdKey: CONFIG.selectors.detailId,
            detailIdValue: referenceId,
            formSelector: CONFIG.selectors.form,
            submitBtnSelector: CONFIG.selectors.submitButton,
            signal: this.abortController.signal,
            onSuccess: (response) => {
                const data = response?.data || response;
                if (!this.dom.form) return;

                const targetFields = {
                    'user_id': referenceId,
                    'name': data.name,
                    'email': data.email,
                    'status': data.status
                };

                Object.entries(targetFields).forEach(([name, val]) => {
                    const field = this.dom.form.elements[name];
                    if (field) {
                        field.value = val ?? '';
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }
        });
    }
}