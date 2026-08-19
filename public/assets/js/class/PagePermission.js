'use strict';

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
import { escapeHtml } from '../util/sanitize.js';

const CONFIG = {
    selectors: {
        table: '#page-permission-table',
        tableColumn: '#page-permission-table-column-dropdown',
        form: '#page_permission_form',
        detailId: 'page_permission_id',
        submitButton: '#submit-data',
        modal: '#form-modal',
        logNotesTrigger: '.view-log-notes',
        deleteMultipleTrigger: '#delete-data',
        deleteTrigger: '.delete-details',
        createTrigger: '.new-button',
        checkboxes: '.datatable-checkbox-children:checked',
        filterCollapse: 'page-permission-filter-collapse',
        filterCreatedDate: '#filter_created_date'
    },
    endpoints: {
        tableData: '/page-permission/generate-table',
        save: '/page-permission/save',
        delete: '/page-permission/delete',
        deleteMultiple: '/page-permission/delete-multiple',
        fetch: '/page-permission/fetch',
    }
};
    
export class PagePermission {
    constructor() {
        this.orchestrator = new DataTableOrchestrator();
        this.abortController = new AbortController();

        this.filterManager = new TableFilterManager({
            containerId: CONFIG.selectors.filterCollapse,
            orchestrator: this.orchestrator,
            tableSelector: CONFIG.selectors.table
        });
        
        this.dom = {
            table: document.querySelector(CONFIG.selectors.table),
            form: document.querySelector(CONFIG.selectors.form),
            modal: $(CONFIG.selectors.modal)
        };
    }

    init() {
        this.initTable();
        this.initForm();
        this.initDelete();
        this.initDateRangePicker();
        this.initRoleOption();
        this.initNavigationMenuOption();
        this.registerGlobalListeners();
        
        AuditLogManager.attachLogNotesClassHandler(CONFIG.selectors.logNotesTrigger, 'page_permissions');
    }

    initTable() {
        this.orchestrator.initialize({
            selector: CONFIG.selectors.table,
            url: CONFIG.endpoints.tableData,
            ajaxData: (d) => {
                return Object.assign({}, d, {
                    filter_created_date: $('#filter_created_date').val()
                });
            },
            colVisContainer: CONFIG.selectors.tableColumn,
            order: [[1, 'asc']],
            exportColumns: [2, 3, 4],
            addons: { 
                controls: true, 
                export: true,
                columnVisibility: true
            },
            columnDefs: [
                { width: '5%', bSortable: false, targets: 0 },
                { bSortable: false, targets: 3 },
                { bSortable: false, targets: 4 },
                { bSortable: false, targets: 5 },
                { bSortable: false, targets: 6 },
                { bSortable: false, targets: 7 },
                { bSortable: false, targets: 8 },
                { width: '10%', bSortable: false, targets: 9 },
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
                    data: 'role',
                    title: 'Role',
                },
                { 
                    data: 'page',
                    title: 'Page',
                },
                { 
                    data: 'read_access',
                    title: 'Read Access',
                },
                { 
                    data: 'write_access',
                    title: 'Write Access',
                },
                { 
                    data: 'create_access',
                    title: 'Create Access',
                },
                { 
                    data: 'delete_access',
                    title: 'Delete Access',
                },
                { 
                    data: 'export_access',
                    title: 'Export Access',
                },
                { 
                    data: 'logs_access',
                    title: 'Logs Access',
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

                        return `
                        <div class="d-flex justify-content-end gap-2 me-5">
                            ${perms.logs ? `<button class="btn btn-sm btn-icon btn-light-warning ${CONFIG.selectors.logNotesTrigger.slice(1)}" data-reference-id="${safeId}" data-bs-toggle="modal" data-bs-target="#log-notes-modal" title="Logs"><i class="ki-outline ki-shield-search fs-5 m-0"></i></button>` : ''}
                            ${perms.delete ? `<button class="btn btn-sm btn-icon btn-light-danger ${CONFIG.selectors.deleteTrigger.slice(1)}" data-reference-id="${safeId}" title="Delete"><i class="ki-outline ki-trash fs-5 m-0"></i></button>` : ''}
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
                        description: { required: true },
                        value: { required: true },
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
            payload: { page_permission_id: (el) => el.dataset.referenceId },
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
                'page_permission_id': () => {
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

    initRoleOption() {
        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.parentOption,
            dropdownSelector: [CONFIG.selectors.parentDropdown, CONFIG.selectors.filterParentDropdown],
            data: {navigationMenuId : navigationMenuId}
        });
    }

    initNavigationMenuOption() {
        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.parentOption,
            dropdownSelector: [CONFIG.selectors.parentDropdown, CONFIG.selectors.filterParentDropdown],
            data: {navigationMenuId : navigationMenuId}
        });
    }

    registerGlobalListeners() {
        document.addEventListener('click', async (event) => {
            const { target } = event;
            
            const createTrigger = target.closest(CONFIG.selectors.createTrigger);
            if (createTrigger) {
                FormEnvironmentManager.resetForm(CONFIG.selectors.form.slice(1));
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
            onSuccess: async (response) => {
                const data = response?.data || response;
                if (!this.dom.form) return;

                const targetFields = {
                    'page_permission_id': referenceId,
                    'name': data.name,
                    'description': data.description,
                    'value': data.value,
                };

                Object.entries(targetFields).forEach(([name, val]) => {
                    const $field = $(this.dom.form).find(`[name="${name}"], [name="${name}[]"]`);
                    
                    if ($field.length) {
                        $field.val(val ?? '').trigger('change');
                    }
                });
            }
        });
    }
}