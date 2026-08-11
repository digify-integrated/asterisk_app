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
        table: '#state-table',
        tableColumn: '#state-table-column-dropdown',
        form: '#state_form',
        detailId: 'state_id',
        submitButton: '#submit-data',
        modal: '#form-modal',
        logNotesTrigger: '.view-log-notes',
        deleteMultipleTrigger: '#delete-data',
        deleteTrigger: '.delete-details',
        updateTrigger: '.update-details',
        createTrigger: '.new-button',
        checkboxes: '.datatable-checkbox-children:checked',
        countryDropdown: '#country_id',
        filterCountryDropdown: '#filter_country_id',
        filterCollapse: 'state-filter-collapse',
        filterCreatedDate: '#filter_created_date'
    },
    endpoints: {
        tableData: '/state/generate-table',
        save: '/state/save',
        delete: '/state/delete',
        deleteMultiple: '/state/delete-multiple',
        fetch: '/state/fetch',
        countryOption: '/country/generate-option',
    }
};
    
export class State {
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
        this.initDropdownOption();
        this.initDateRangePicker();
        this.registerGlobalListeners();
        
        AuditLogManager.attachLogNotesClassHandler(CONFIG.selectors.logNotesTrigger, 'states');
    }

    initTable() {
        this.orchestrator.initialize({
            selector: CONFIG.selectors.table,
            url: CONFIG.endpoints.tableData,
            ajaxData: (d) => {
                return Object.assign({}, d, {
                    filter_country_id: $('#filter_country_id').val() || [],
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
                { width: '10%', bSortable: false, targets: 4 },
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
                    title: 'State',
                },
                { 
                    data: 'country',
                    title: 'Country',
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
                        country_id: { required: true },
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
            payload: { state_id: (el) => el.dataset.referenceId },
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
                'state_id': () => {
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
            url: CONFIG.endpoints.countryOption,
            dropdownSelector: [CONFIG.selectors.countryDropdown, CONFIG.selectors.filterCountryDropdown]
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
                    'state_id': referenceId,
                    'name': data.name,
                    'country_id': data.country_id,
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