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
        formId: 'page_permission_form',
        detailId: 'page_permission_id',
        submitButton: '#submit-data',
        modal: '#form-modal',
        logNotesModal: '#log-notes-modal',
        logNotesTrigger: '.view-log-notes',
        deleteMultipleTrigger: '#delete-data',
        deleteTrigger: '.delete-details',
        updateTrigger: '.update-details',
        createTrigger: '.new-button',
        checkboxes: '.datatable-checkbox-children:checked',
        roleDropdown: '#role_id',
        navigationMenuDropdown: '#navigation_menu_id',
        filterDrawer: 'page-permission-filter-drawer',
        filterNavigationMenuDropdown: '#filter_navigation_menu_id',
        filterRoleDropdown: '#filter_role_id',
        filterReadAccess: '#filter_read_access',
        filterWriteAccess: '#filter_write_access',
        filterCreateAccess: '#filter_create_access',
        filterDeleteAccess: '#filter_delete_access',
        filterImportAccess: '#filter_import_access',
        filterExportAccess: '#filter_export_access',
        filterLogsAccess: '#filter_logs_access',
        filterCreatedDate: '#filter_created_date'
    },
    classes: {
        logNotesTrigger: 'view-log-notes',
        deleteTrigger: 'delete-details',
        updateTrigger: 'update-details'
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
            containerId: CONFIG.selectors.filterDrawer,
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
            filterRole: document.querySelector(CONFIG.selectors.filterRoleDropdown),
            filterNavigationMenu: document.querySelector(CONFIG.selectors.filterNavigationMenuDropdown),
            filterRead: document.querySelector(CONFIG.selectors.filterReadAccess),
            filterWrite: document.querySelector(CONFIG.selectors.filterWriteAccess),
            filterCreate: document.querySelector(CONFIG.selectors.filterCreateAccess),
            filterDelete: document.querySelector(CONFIG.selectors.filterDeleteAccess),
            filterImport: document.querySelector(CONFIG.selectors.filterImportAccess),
            filterExport: document.querySelector(CONFIG.selectors.filterExportAccess),
            filterLogs: document.querySelector(CONFIG.selectors.filterLogsAccess),
            filterDate: document.querySelector(CONFIG.selectors.filterCreatedDate)
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

    destroy() {
        this.abortController.abort();
    }
    
    initTable() {
        this.orchestrator.initialize({
            selector: CONFIG.selectors.table,
            url: CONFIG.endpoints.tableData,
            ajaxData: (d) => Object.assign({}, d, {
                filter_role_id: $(this.dom.filterRole).val() || [],
                filter_navigation_menu_id: $(this.dom.filterNavigationMenu).val() || [],
                filter_read_access: $(this.dom.filterRead).val() || [],
                filter_write_access: $(this.dom.filterWrite).val() || [],
                filter_create_access: $(this.dom.filterCreate).val() || [],
                filter_delete_access: $(this.dom.filterDelete).val() || [],
                import_access: $(this.dom.filterImport).val() || [],
                filter_export_access: $(this.dom.filterExport).val() || [],
                filter_logs_access: $(this.dom.filterLogs).val() || [],
                filter_created_date: this.dom.filterDate?.value || ''
            }),
            colVisContainer: CONFIG.selectors.tableColumn,
            exportColumns: [1, 2, 3, 4, 5, 6, 7, 8, 9],
            order: [[1, 'asc']],
            addons: { 
                controls: true, 
                export: true,
                columnVisibility: true
            },
            columnDefs: [
                { width: '5%', orderable: false, targets: 0 },
                { orderable: false, targets: [3, 4, 5, 6, 7, 8, 9] },
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
                {
                    data: 'role',
                    column: 'Role',
                    title: 'Role'
                },
                {
                    data: 'page', 
                    column: 'Page',
                    title: 'Page' 
                },
                ...['read_access', 'write_access', 'create_access', 'delete_access', 'import_access', 'export_access', 'logs_access'].map(field => ({
                    data: field,
                    column: field.replace('_access', ' ').replace(/\b\w/g, l => l.toUpperCase()),
                    title: field.replace('_access', ' ').replace(/\b\w/g, l => l.toUpperCase()),
                    render: (data, type, row) => {
                        const isChecked = Boolean(data);
                        const safeId = escapeHtml(row.id);
                        
                        return `
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input ${CONFIG.classes.updateTrigger} h-20px w-30px" 
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
                    column: 'Created Date',
                    title: 'Created Date',
                    visible: false
                },
                {
                    data: null,
                    column: 'Action',
                    title: '',
                    render: (_, __, row, meta) => {
                        const perms = meta.settings.json?.permissions || row.permissions || {};
                        const items = [];

                        if (perms.logs || perms.delete) {
                            const safeId = escapeHtml(row.id);

                            if (perms.logs) {
                                items.push(`
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 ${CONFIG.classes.logNotesTrigger}" data-bs-toggle="modal" data-bs-target="${CONFIG.selectors.logNotesModal}" data-reference-id="${safeId}">
                                        <span class="menu-icon me-2"><i class="ki-duotone ki-shield-search fs-5"></i></span>
                                        <span class="menu-title">Audit Logs</span>
                                    </a>
                                </div>`);
                            }

                            if (perms.delete) {
                                items.push(`
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 text-danger ${CONFIG.classes.deleteTrigger}" data-reference-id="${safeId}" data-kt-users-table-filter="delete_row">
                                        <span class="menu-icon me-2"><i class="ki-duotone ki-trash fs-5 text-danger"></i></span>
                                        <span class="menu-title text-danger">Delete</span>
                                    </a>
                                </div>`);
                            }
                        }

                        if (items.length === 0) return '';

                        return `
                        <div class="text-center me-10">
                            <a href="#" class="btn btn-light-dark btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                Actions
                                <i class="ki-duotone ki-down fs-5 ms-1"></i>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                                ${items.join('')}
                            </div>
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
                        'import_access': { required: true },
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
            data: { pageType: ['single_page', 'multi_page'] }
        });
    }

    registerGlobalListeners() {
        document.addEventListener('click', async (event) => {
            const { target } = event;
            
            const createTrigger = target.closest(CONFIG.selectors.createTrigger);
            if (createTrigger) {
                FormEnvironmentManager.resetForm(CONFIG.selectors.formId);
            }
        }, { signal: this.abortController.signal });
    }
}