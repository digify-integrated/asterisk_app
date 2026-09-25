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
        table: '#company-table',
        tableColumn: '#company-table-column-dropdown',
        form: '#company_form',
        formId: 'company_form',
        detailId: 'company_id',
        submitButton: '#submit-data',
        modal: '#form-modal',
        logNotesModal: '#log-notes-modal',
        logNotesTrigger: '.view-log-notes',
        deleteMultipleTrigger: '#delete-data',
        deleteTrigger: '.delete-details',
        updateTrigger: '.update-details',
        createTrigger: '.new-button',
        checkboxes: '.datatable-checkbox-children:checked',
        cityDropdown: '#city_id',
        currencyDropdown: '#currency_id',
        dateRegistered: '#date_registered',
        filterDateRegistered: '#filter_date_registered',
        filterCollapse: 'company-filter-collapse',
        filterCityDropdown: '#filter_city_id',
        filterStateDropdown: '#filter_state_id',
        filterCountryDropdown: '#filter_country_id',
        filterCurrencyDropdown: '#filter_currency_id',
        filterCreatedDate: '#filter_created_date',
        filterEntityType: '#filter_entity_type',
        filterVatStatus: '#filter_vat_status',
        filterFiscalYearStartMonth: '#filter_fiscal_year_start_month'
    },
    classes: {
        logNotesTrigger: 'view-log-notes',
        deleteTrigger: 'delete-details',
        updateTrigger: 'update-details'
    },
    endpoints: {
        tableData: '/company/generate-table',
        save: '/company/save',
        delete: '/company/delete',
        deleteMultiple: '/company/delete-multiple',
        fetch: '/company/fetch',
        cityOption: '/city/generate-option',
        stateOption: '/state/generate-option',
        countryOption: '/country/generate-option',
        currencyOption: '/currency/generate-option',
    }
};
    
export class Company {
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
            filterEntityType: document.querySelector(CONFIG.selectors.filterEntityType),
            filterVatStatus: document.querySelector(CONFIG.selectors.filterVatStatus),
            filterCity: document.querySelector(CONFIG.selectors.filterCityDropdown),
            filterState: document.querySelector(CONFIG.selectors.filterStateDropdown),
            filterCountry: document.querySelector(CONFIG.selectors.filterCountryDropdown),
            filterCurrency: document.querySelector(CONFIG.selectors.filterCurrencyDropdown),
            filterFiscalMonth: document.querySelector(CONFIG.selectors.filterFiscalYearStartMonth),
            filterDateRegistered: document.querySelector(CONFIG.selectors.filterDateRegistered),
            filterCreatedDate: document.querySelector(CONFIG.selectors.filterCreatedDate)
        };

        this.passwordToggle = new PasswordToggle();
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
                this.initDatePicker(),
                this.registerGlobalListeners()
            ]);
            
            AuditLogManager.attachLogNotesClassHandler(CONFIG.selectors.logNotesTrigger, 'companies');
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
                filter_entity_type: $(this.dom.filterEntityType).val() || [],
                filter_vat_status: $(this.dom.filterVatStatus).val() || [],
                filter_city_id: $(this.dom.filterCity).val() || [],
                filter_state_id: $(this.dom.filterState).val() || [],
                filter_country_id: $(this.dom.filterCountry).val() || [],
                filter_currency_id: $(this.dom.filterCurrency).val() || [],
                filter_fiscal_year_start_month: $(this.dom.filterFiscalMonth).val() || [],
                filter_date_registered: this.dom.filterDateRegistered?.value || '',
                filter_created_date: this.dom.filterCreatedDate?.value || '',
            }),
            colVisContainer: CONFIG.selectors.tableColumn,
            order: [[2, 'asc']],
            exportColumns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
            addons: { 
                controls: true, 
                export: true,
                columnVisibility: true
            },
            columnDefs: [
                { width: '5%', bSortable: false, targets: 0 },
                { width: '5%', bSortable: false, targets: 1 },
                { width: '10%', bSortable: false, targets: 19 },
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
                    render: (data, type, row) => `<img src="${escapeHtml(row.logo)}" alt="Company Logo" width="45" onerror="this.src='/assets/media/default/default-company-logo.png';" />`
                },
                { data: 'legal_name', title: 'Legal Name' },
                { data: 'trade_name', title: 'Trade Name' },
                { data: 'address', title: 'Address' },
                { data: 'tin', title: 'TIN', visible: false },
                { data: 'branch_code', title: 'Branch Code', visible: false },
                { data: 'entity_type', title: 'Entity Type', visible: false },
                { data: 'sec_dti_registration_no', title: 'SEC/DTI Registration No.', visible: false },
                { data: 'date_registered', title: 'Date Registered', visible: false },
                { data: 'psic_code', title: 'PSIC Code', visible: false },
                { data: 'line_of_business', title: 'Line of Business', visible: false },
                { data: 'vat_status', title: 'VAT Status', visible: false },
                { data: 'currency', title: 'Currency', visible: false },
                { data: 'phone', title: 'Phone', visible: false },
                { data: 'email', title: 'Email', visible: false },
                { data: 'website', title: 'Website', visible: false },
                { data: 'contact_person', title: 'Contact Person', visible: false },
                { data: 'created_at', title: 'Created At', visible: false },
                { 
                    data: null, 
                    title: '&nbsp;',
                    render: (data, type, row, meta) => {
                        const perms = meta.settings.json?.permissions || row.permissions || {};
                        const safeId = escapeHtml(row.id);

                        return `
                        <div class="d-flex justify-content-end gap-2 me-5">
                            ${perms.write ? `<button class="btn btn-sm btn-icon btn-light-primary ${CONFIG.classes.updateTrigger}" data-bs-toggle="modal" data-bs-target="${CONFIG.selectors.modal}" data-reference-id="${safeId}" title="Edit"><i class="ki-outline ki-eye fs-5 m-0"></i></button>` : ''}
                            ${perms.logs ? `<button class="btn btn-sm btn-icon btn-light-warning ${CONFIG.classes.logNotesTrigger}" data-reference-id="${safeId}" data-bs-toggle="modal" data-bs-target="${CONFIG.selectors.logNotesModal}" title="Logs"><i class="ki-outline ki-shield-search fs-5 m-0"></i></button>` : ''}
                            ${perms.delete ? `<button class="btn btn-sm btn-icon btn-light-danger ${CONFIG.classes.deleteTrigger}" data-reference-id="${safeId}" title="Delete"><i class="ki-outline ki-trash fs-5 m-0"></i></button>` : ''}
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
                        legal_name: { required: true },
                        trade_name: { required: true },
                        entity_type: { required: true },
                        vat_status: { required: true },
                        street_1: { required: true },
                        city_id: { required: true },
                        email: { typeEmail: true },
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
            payload: { company_id: (el) => el.dataset.referenceId },
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
                'company_id': () => {
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
            selector: [CONFIG.selectors.filterCreatedDate, CONFIG.selectors.filterDateRegistered]
        });
    }

    initDatePicker() {
        ComponentRegistry.initializeDatePicker({
            selector: [CONFIG.selectors.dateRegistered]
        });
    }

    initDropdownOption() {
        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.cityOption,
            dropdownSelector: [CONFIG.selectors.cityDropdown]
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.cityOption,
            dropdownSelector: [CONFIG.selectors.filterCityDropdown],
            data: { type: 'city_only' }
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.stateOption,
            dropdownSelector: [CONFIG.selectors.filterStateDropdown]
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.countryOption,
            dropdownSelector: [CONFIG.selectors.filterCountryDropdown]
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.currencyOption,
            dropdownSelector: [CONFIG.selectors.currencyDropdown, CONFIG.selectors.filterCurrencyDropdown]
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
                    'company_id': referenceId,
                    'legal_name': data.legal_name,
                    'trade_name': data.trade_name,
                    'tin': data.tin,
                    'branch_code': data.branch_code,
                    'rdo_code': data.rdo_code,
                    'entity_type': data.entity_type,
                    'sec_dti_registration_no': data.sec_dti_registration_no,
                    'date_registered': data.date_registered,
                    'psic_code': data.psic_code,
                    'line_of_business': data.line_of_business,
                    'vat_status': data.vat_status,
                    'fiscal_year_start_month': data.fiscal_year_start_month,
                    'street_1': data.street_1,
                    'street_2': data.street_2,
                    'barangay': data.barangay,
                    'city_id': data.city_id,
                    'currency_id': data.currency_id,
                    'phone': data.phone,
                    'email': data.email,
                    'website': data.website,
                    'contact_person': data.contact_person
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