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
        parentOption: '/navigation/generate-option',
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
        //this.initForm();
        //this.initDelete();
        this.initDropdownOption();
        //this.registerGlobalListeners();
        
        //AuditLogManager.attachLogNotesClassHandler(CONFIG.selectors.logNotesTrigger, 'apps');
    }

    initTable() {
        this.orchestrator.initialize({
            selector: CONFIG.selectors.table,
            url: CONFIG.endpoints.tableData,
            colVisContainer: CONFIG.selectors.tableColumn,
            order: [[2, 'asc']],
            exportColumns: [2, 3, 4],
            addons: { 
                controls: true, 
                export: true,
                columnVisibility: true
            },
            columnDefs: [
                { width: '5%', bSortable: false, targets: 0 },
                { width: '5%', bSortable: false, targets: 1 },
                { width: '15%', targets: 2 },
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
                    data: 'logo',
                    render: (data, type, row) => `<img src="${escapeHtml(row.logo_url)}" alt="App Logo" width="45" onerror="this.src='/assets/media/svg/brand-logos/abstract.svg';" />`
                },
                { 
                    data: 'name',
                    title: 'App',
                    render: (data, type, row) => `<h6 class="mb-0">${escapeHtml(row.name)}</h6>`
                },
                { 
                    data: 'description',
                    title: 'Description',
                    render: (desc) => `<div class="text-gray-800 text-wrap">${escapeHtml(desc)}</div>`
                },
                { 
                    data: 'order_sequence',
                    title: 'Sequence',
                    render: (seq) => escapeHtml(seq)
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
        initValidation({
            forms: [
                {
                    selector: CONFIG.selectors.form,
                    rules: {
                        name: { required: true },
                        description: { required: true },
                        order_sequence: { required: true }
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
                this.initDropdownOption();
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

                // Ensure options exist before binding values
                await this.initDropdownOption();

                const targetFields = {
                    'navigation_menu_id': referenceId,
                    'name': data.name,
                    'order_sequence': data.order_sequence,
                    'description': data.description,
                    'app_id': data.app_id,
                    'parent_id': data.parent_id
                };

                Object.entries(targetFields).forEach(([name, val]) => {
                    const field = this.dom.form.elements[name];
                    if (field) {
                        field.value = val ?? '';
                        // Triggers Select2 UI update via change event
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }
        });
    }
}