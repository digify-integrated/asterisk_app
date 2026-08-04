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
import { escapeHtml } from '../util/sanitize.js';

const CONFIG = {
    selectors: {
        table: '#navigation-menu-table',
        tableColumn: '#navigation-menu-table-column-dropdown',
        form: '#navigation_menu_form',
        submitButton: '#submit-data',
        modal: '#form-modal',
        logNotesTrigger: '.view-log-notes',
        deleteMultipleTrigger: '#delete-data',
        deleteTrigger: '.delete-details',
        updateTrigger: '.update-details',
        createTrigger: '.new-button',
        applyFilter: '#apply-filter',
        checkboxes: '.datatable-checkbox-children:checked',
        appDropdown: '#app_id',
        parentDropdown: '#parent_id',
        filterAppDropdown: '#filter_app_id',
        filterParentDropdown: '#filter_parent_id',
    },
    endpoints: {
        tableData: '/navigation-menu/generate-table',
        save: '/navigation-menu/save',
        delete: '/navigation-menu/delete',
        deleteMultiple: '/navigation-menu/delete-multiple',
        fetch: '/navigation-menu/fetch',
        appOption: '/app/generate-option',
        parentOption: '/navigation-menu/generate-option',
    }
};
    
export class NavigationMenu {
    constructor() {
        this.orchestrator = new DataTableOrchestrator();
        this.abortController = new AbortController();
        
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
        this.initDropdownOption();
        this.initParentDropdownOption();
        this.registerGlobalListeners();
        
        AuditLogManager.attachLogNotesClassHandler(CONFIG.selectors.logNotesTrigger, 'navigation_menus');
    }

    initTable() {
        this.orchestrator.initialize({
            selector: CONFIG.selectors.table,
            url: CONFIG.endpoints.tableData,
            ajaxData: (d) => {
                return Object.assign({}, d, {
                    filter_parent_id: $('#filter_parent_id').val() || [],
                    filter_app_id: $('#filter_app_id').val() || [],
                    filter_page_type: $('#filter_page_type').val() || []
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
                { width: '15%', targets: 4 },
                { width: '10%', bSortable: false, targets: 5 },
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
                    title: 'Navigation Menu',
                },
                { 
                    data: 'apps',
                    title: 'Apps',
                    bSortable: false,
                    render: (apps) => {
                        if (!Array.isArray(apps) || apps.length === 0) {
                            return `<span class="badge badge-light-secondary">No Apps</span>`;
                        }

                        return apps.map(app => 
                            `<span class="badge badge-light-primary me-1 mb-1">${escapeHtml(app.name)}</span>`
                        ).join('');
                    }
                },
                { 
                    data: 'parent',
                    title: 'Parent',
                },
                { 
                    data: 'page_type',
                    title: 'Page Type',
                },
                { 
                    data: 'order_sequence',
                    title: 'Sequence',
                },
                { 
                    data: null, 
                    title: '&nbsp;',
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
    }

    initForm() {
        const isNotMenuPage = (form) => {
            const pageType = form.querySelector('[name="page_type"]')?.value;
            return Boolean(pageType && pageType !== 'menu');
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
                        index_js_file: { requiredIf: isNotMenuPage }
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

            FormEnvironmentManager.resetForm(formElement);
            this.dom.modal.modal('hide');
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

    initDropdownOption() {
        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.appOption,
            dropdownSelector: [CONFIG.selectors.appDropdown, CONFIG.selectors.filterAppDropdown]
        });
    }

    initParentDropdownOption(navigationMenuId = null) {
        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.parentOption,
            dropdownSelector: [CONFIG.selectors.parentDropdown, CONFIG.selectors.filterParentDropdown],
            data: {navigationMenuId : navigationMenuId}
        });
    }

    registerGlobalListeners() {
        document.addEventListener('click', async (event) => {
            const { target } = event;
            
            const updateTrigger = target.closest(CONFIG.selectors.updateTrigger);
            if (updateTrigger) {
                FormEnvironmentManager.resetForm(CONFIG.selectors.form.slice(1));
                this.handleFetchWorkflow(updateTrigger.dataset.referenceId);
                return;
            }
            
            const createTrigger = target.closest(CONFIG.selectors.createTrigger);
            if (createTrigger) {
                FormEnvironmentManager.resetForm(CONFIG.selectors.form.slice(1));
                this.initParentDropdownOption();
            }
            
            const filterTrigger = target.closest(CONFIG.selectors.applyFilter);
            if (filterTrigger) {
                this.orchestrator.reload(CONFIG.selectors.table)
            }
        }, { signal: this.abortController.signal });
    }

    async handleFetchWorkflow(referenceId) {
        await DetailFetcher.fetch({
            url: CONFIG.endpoints.fetch,
            detailIdKey: 'navigation_menu_id',
            detailIdValue: referenceId,
            formSelector: CONFIG.selectors.form,
            submitBtnSelector: CONFIG.selectors.submitButton,
            signal: this.abortController.signal,
            onSuccess: async (response) => {
                const data = response?.data || response;
                if (!this.dom.form) return;
                
                await Promise.all([
                    this.initParentDropdownOption(referenceId),
                ]);

                // Map resource properties directly to form inputs
                const targetFields = {
                    'navigation_menu_id': referenceId,
                    'name': data.name,
                    'page_type': data.page_type,
                    'icon': data.icon,
                    'parent_id': data.parent_id,
                    'order_sequence': data.order_sequence,
                    'app_id': data.app_ids || [], // Multi-select array
                    'index_view_file': data.index_view_file,
                    'index_js_file': data.index_js_file,
                    'manage_view_file': data.manage_view_file,
                    'manage_js_file': data.manage_js_file,
                };

                Object.entries(targetFields).forEach(([name, val]) => {
                    const $field = $(this.dom.form).find(`[name="${name}"], [name="${name}[]"]`);
                    
                    if ($field.length) {
                        // Handles both standard inputs and Select2 controls
                        $field.val(val ?? '').trigger('change');
                    }
                });
            }
        });
    }
}