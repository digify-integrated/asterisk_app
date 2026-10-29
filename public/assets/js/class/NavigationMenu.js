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

const CONFIG = {
    selectors: {
        table: '#navigation-menu-table',
        tableColumn: '#navigation-menu-table-column-dropdown',
        form: '#navigation_menu_form',
        formId: 'navigation_menu_form',
        detailId: 'navigation_menu_id',
        submitButton: '#submit-data',
        modal: '#form-modal',
        logNotesModal: '#log-notes-modal',
        logNotesTrigger: '.view-log-notes',
        deleteMultipleTrigger: '#delete-data',
        deleteTrigger: '.delete-details',
        updateTrigger: '.update-details',
        createTrigger: '.new-button',
        checkboxes: '.datatable-checkbox-children:checked',
        appDropdown: '#app_id',
        parentDropdown: '#parent_id',
        databaseTableDropdown: '#database_table',
        filterDrawer: 'navigation-menu-filter-drawer',
        filterAppDropdown: '#filter_app_id',
        filterParentDropdown: '#filter_parent_id',
        filterDatabaseTableDropdown: '#filter_database_table',
        filterPageType: '#filter_page_type',
        filterCreatedDate: '#filter_created_date'
    },
    classes: {
        logNotesTrigger: 'view-log-notes',
        deleteTrigger: 'delete-details',
        updateTrigger: 'update-details'
    },
    endpoints: {
        tableData: '/navigation-menu/generate-table',
        save: '/navigation-menu/save',
        delete: '/navigation-menu/delete',
        deleteMultiple: '/navigation-menu/delete-multiple',
        fetch: '/navigation-menu/fetch',
        appOption: '/app/generate-option',
        parentOption: '/navigation-menu/generate-option',
        databaseTableOption: '/database-table/generate-option'
    }
};
    
export class NavigationMenu {
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
            filterDate: document.querySelector(CONFIG.selectors.filterCreatedDate),
            filterApp: document.querySelector(CONFIG.selectors.filterAppDropdown),
            filterParent: document.querySelector(CONFIG.selectors.filterParentDropdown),
            filterDatabaseTable: document.querySelector(CONFIG.selectors.filterDatabaseTableDropdown),
            filterPageType: document.querySelector(CONFIG.selectors.filterPageType)
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
                    
            AuditLogManager.attachLogNotesClassHandler(CONFIG.selectors.logNotesTrigger, 'navigation_menus');
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
                filter_parent_id: $(this.dom.filterParent).val() || [],
                filter_app_id: $(this.dom.filterApp).val() || [],
                filter_page_type: $(this.dom.filterPageType).val() || [],
                filter_database_table: $(this.dom.filterDatabaseTable).val() || [],
                filter_created_date: this.dom.filterDate?.value || ''
            }),
            colVisContainer: CONFIG.selectors.tableColumn,
            exportColumns: [1, 2, 3, 4, 5, 6, 7],
            order: [[1, 'asc']],
            addons: { 
                controls: true, 
                export: true,
                columnVisibility: true
            },
            columnDefs: [
                { width: '5%', bSortable: false, targets: 0 },
                { width: '20%', targets: [1, 2, 4, 5] },
                { width: '10%', bSortable: false, targets: 7 },
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
                    data: 'name',
                    column: 'Navigation Menu',
                    title: 'Navigation Menu',
                },
                { 
                    data: 'apps',
                    column: 'Apps',
                    title: 'Apps',
                    render: (apps) => {
                        if (!Array.isArray(apps) || apps.length === 0) {
                            return `<span class="badge badge-light-secondary text-muted">No Apps</span>`;
                        }

                        const badges = apps.map((app) => {
                            return `<span class="badge badge-light-primary">${escapeHtml(app.name)}</span>`;
                        }).join('');

                        return `<div class="d-flex flex-wrap align-items-center gap-1">${badges}</div>`;
                    }
                },
                { 
                    data: 'parent',
                    column: 'Parent',
                    title: 'Parent',
                },
                { 
                    data: 'page_type',
                    column: 'Page Type',
                    title: 'Page Type',
                },
                { 
                    data: 'database_tables',
                    column: 'Import/Export Table',
                    title: 'Import/Export Table',
                    render: (database_tables) => {
                        if (!Array.isArray(database_tables) || database_tables.length === 0) {
                            return `<span class="badge badge-light-secondary">No Import/Export Tables</span>`;
                        }

                        const badges = database_tables.map((table) => {
                            return `<span class="badge badge-light-warning">${escapeHtml(table.name)}</span>`;
                        }).join('');

                        return `<div class="d-flex flex-wrap align-items-center gap-1">${badges}</div>`;
                    }
                },
                { 
                    data: 'order_sequence',
                    column: 'Sequence',
                    title: 'Sequence',
                },
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

                        if (perms.write || perms.logs || perms.delete) {
                            const safeId = escapeHtml(row.id);

                            if (perms.write) {
                                items.push(`
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 ${CONFIG.classes.updateTrigger}" data-bs-toggle="modal" data-bs-target="${CONFIG.selectors.modal}" data-reference-id="${safeId}">
                                        <span class="menu-icon me-2"><i class="ki-duotone ki-eye fs-5"></i></span>
                                        <span class="menu-title">Edit</span>
                                    </a>
                                </div>`);
                            }

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
        const isNotMenuPage = (form) => {
            const pageType = form.querySelector('[name="page_type"]')?.value;
            return Boolean(pageType && pageType !== 'menu');
        };

        const isMultiPage = (form) => {
            const pageType = form.querySelector('[name="page_type"]')?.value;
            return Boolean(pageType && pageType == 'multi_page');
        };

        initValidation({
            forms: [
                {
                    selector: CONFIG.selectors.form,
                    rules: {
                        name: { required: true },
                        app_id: { required: true },
                        page_type: { required: true },
                        order_sequence: { required: true },
                        index_view_file: { requiredIf: isNotMenuPage },
                        index_js_file: { requiredIf: isNotMenuPage },
                        manage_view_file: { requiredIf: isMultiPage },
                        manage_js_file: { requiredIf: isMultiPage }
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
            this.initDropdownOption();
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
            payload: { navigation_menu_id: (el) => el.dataset.referenceId },
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
                'navigation_menu_id': () => {
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
            url: CONFIG.endpoints.appOption,
            dropdownSelector: [CONFIG.selectors.appDropdown, CONFIG.selectors.filterAppDropdown]
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.databaseTableOption,
            dropdownSelector: [CONFIG.selectors.databaseTableDropdown, CONFIG.selectors.filterDatabaseTableDropdown]
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.parentOption,
            dropdownSelector: [CONFIG.selectors.filterParentDropdown]
        });
    }

    initParentDropdownOption(navigationMenuId) {
        return ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.parentOption,
            dropdownSelector: [CONFIG.selectors.parentDropdown],
            data: { navigationMenuId: navigationMenuId }
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
                await this.initParentDropdownOption(null);
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
                
                await this.initParentDropdownOption(referenceId);

                const targetFields = {
                    'navigation_menu_id': referenceId,
                    'name': data.name,
                    'page_type': data.page_type,
                    'icon': data.icon,
                    'parent_id': data.parent_id,
                    'order_sequence': data.order_sequence,
                    'app_id': data.app_ids || [],
                    'database_table': data.database_tables || [],
                    'index_view_file': data.index_view_file,
                    'index_js_file': data.index_js_file,
                    'manage_view_file': data.manage_view_file,
                    'manage_js_file': data.manage_js_file,
                };

                Object.entries(targetFields).forEach(([name, val]) => {
                    const $field =$(this.dom.form).find(`[name="${name}"], [name="${name}[]"]`);
                    
                    if ($field.length) {$field.val(val ?? '').trigger('change');
                    }
                });
            }
        });
    }
}