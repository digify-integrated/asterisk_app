'use strict';

import { PageInitializer } from '../util/pageInitializer.js';
import { DataTableOrchestrator } from '../util/dataTableOrchestrator.js';
import { AuditLogManager } from '../util/auditLogManager.js';
import { initValidation } from '../util/validator.js';
import { FormEnvironmentManager } from '../util/formEnvironmentManager.js';
import { errorHandler } from '../util/errorHandler.js';
import { ButtonStateManager } from '../util/buttonManager.js';
import { initConfirmAction } from '../util/confirmationAction.js';
import { ComponentRegistry } from '../util/componentRegistry.js';
import { TableFilterManager } from '../util/tableFilterManager.js';
import { SaveFilterManager } from '../util/saveFilterManager.js';
import { escapeHtml } from '../util/sanitize.js';
import { activateTriggers } from '../util/activateTriggers.js';

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
        updateTrigger: '.update-details',
        createTrigger: '.new-button',
        checkboxes: '.datatable-checkbox-children:checked',
        roleDropdown: '#role_id',
        navigationMenuDropdown: '#navigation_menu_id',
        filterCollapse: 'page-permission-filter-collapse',
        filterNavigationMenuDropdown: '#filter_navigation_menu_id',
        filterRoleDropdown: '#filter_role_id',
        filterCreatedDate: '#filter_created_date'
    },
    endpoints: {
        tableData: '/page-permission/generate-table',
        save: '/page-permission/save',
        update: '/page-permission/update',
        delete: '/page-permission/delete',
        deleteMultiple: '/page-permission/delete-multiple',
        fetch: '/page-permission/fetch',
        roleOption: '/role/generate-option',
        navigationMenuOption: '/navigation-menu/generate-option',
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

        this.saveFilterManager = new SaveFilterManager({
            filterManager: this.filterManager
        });
        
        this.dom = {
            table: document.querySelector(CONFIG.selectors.table),
            form: document.querySelector(CONFIG.selectors.form),
            modal: $(CONFIG.selectors.modal)
        };
    }

    async init() {
        return PageInitializer.run(async () => {
            this.initDropdownOption();

            await this.saveFilterManager.checkAndApplyDefaultFilter();

            this.initTable();

            await Promise.all([
                this.initForm(),
                this.initDelete(),
                this.initDateRangePicker(),
                this.registerGlobalListeners()
            ]);

            activateTriggers({
                trigger: CONFIG.selectors.updateTrigger,
                url: CONFIG.endpoints.update,
                payload: {
                    page_permission_id: (el) => el.dataset.id,
                    access_field: (el) => el.dataset.field,
                    access_value: (el) => el.checked ? 1 : 0
                }
            });

            AuditLogManager.attachLogNotesClassHandler(
                CONFIG.selectors.logNotesTrigger,
                'role_permissions'
            );
        });
    }
    
    initTable() {
        this.orchestrator.initialize({
            selector: CONFIG.selectors.table,
            url: CONFIG.endpoints.tableData,
            ajaxData: (d) => {
                return Object.assign({}, d, {
                    filter_role_id: $('#filter_role_id').val() || [],
                    filter_navigation_menu_id: $('#filter_navigation_menu_id').val() || [],
                    filter_read_access: $('#filter_read_access').val() || [],
                    filter_write_access: $('#filter_write_access').val() || [],
                    filter_create_access: $('#filter_create_access').val() || [],
                    filter_delete_access: $('#filter_delete_access').val() || [],
                    filter_export_access: $('#filter_export_access').val() || [],
                    filter_logs_access: $('#filter_logs_access').val() || [],
                    filter_created_date: $('#filter_created_date').val()
                });
            },
            colVisContainer: CONFIG.selectors.tableColumn,
            order: [[1, 'asc']],
            exportColumns: [1, 2, 8],
            addons: { 
                controls: true, 
                export: true,
                columnVisibility: true
            },
            columnDefs: [
                { width: '5%', orderable: false, targets: 0 },
                { orderable: false, targets: [3, 4, 5, 6, 7, 8] },
                { width: '10%', orderable: false, targets: 10 }
            ],
            columns: [
                { 
                    data: 'id',
                    render: (id) => `
                        <div class="form-check form-check-sm ms-5">
                            <input class="form-check-input datatable-checkbox-children" type="checkbox" value="${escapeHtml(id)}">
                        </div>`
                },
                { data: 'role', title: 'Role' },
                { data: 'page', title: 'Page' },
                ...['read_access', 'write_access', 'create_access', 'delete_access', 'export_access', 'logs_access'].map(field => ({
                    data: field,
                    title: field.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()),
                    render: (data, type, row) => {
                        const isChecked = Boolean(data);
                        const safeId = escapeHtml(row.id);
                        
                        return `
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input update-details h-20px w-30px" 
                                type="checkbox" 
                                value="1" 
                                data-id="${safeId}" 
                                data-field="${field}" 
                                ${isChecked ? 'checked="checked"' : ''} />
                        </div>`;
                    }
                })),
                { 
                    data: 'created_at',
                    title: 'Created At',
                    visible: false
                },
                { 
                    data: null, 
                    title: '&nbsp;',
                    render: (data, type, row, meta) => {
                        const globalPerms = meta.settings.json?.permissions || {};
                        const rowPerms = row.permissions || {};
                        
                        const canLogs = globalPerms.logs ?? rowPerms.logs ?? false;
                        const canDelete = globalPerms.delete ?? rowPerms.delete ?? false;
                        const safeId = escapeHtml(row.id);

                        if (!canLogs && !canDelete) {
                            return '';
                        }

                        return `
                        <div class="d-flex justify-content-end gap-2 me-5">
                            ${canLogs ? `<button class="btn btn-sm btn-icon btn-light-warning ${CONFIG.selectors.logNotesTrigger.slice(1)}" data-reference-id="${safeId}" data-bs-toggle="modal" data-bs-target="#log-notes-modal" title="Logs"><i class="ki-outline ki-shield-search fs-5 m-0"></i></button>` : ''}
                            ${canDelete ? `<button class="btn btn-sm btn-icon btn-light-danger ${CONFIG.selectors.deleteTrigger.slice(1)}" data-reference-id="${safeId}" title="Delete"><i class="ki-outline ki-trash fs-5 m-0"></i></button>` : ''}
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
                        'role_id[]': { required: true },
                        'navigation_menu_id[]': { required: true },
                        'read_access': { required: true },
                        'write_access': { required: true },
                        'create_access': { required: true },
                        'delete_access': { required: true },
                        'export_access': { required: true },
                        'logs_access': { required: true }
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

    initDropdownOption() {
        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.roleOption,
            dropdownSelector: [CONFIG.selectors.roleDropdown, CONFIG.selectors.filterRoleDropdown],
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.navigationMenuOption,
            dropdownSelector: [CONFIG.selectors.navigationMenuDropdown, CONFIG.selectors.filterNavigationMenuDropdown],
            data: {pageType : ['single_page', 'multi_page']}
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
}