'use strict';

import { PageInitializer } from '../util/pageInitializer.js';
import { DataTableOrchestrator } from '../util/dataTableOrchestrator.js';
import { initConfirmAction } from '../util/confirmationAction.js';
import { ComponentRegistry } from '../util/componentRegistry.js';
import { TableFilterManager } from '../util/tableFilterManager.js';
import { SaveFilterManager } from '../util/saveFilterManager.js';
import { escapeHtml } from '../util/sanitize.js';

const CONFIG = {
    selectors: {
        table: '#employee-table',
        tableColumn: '#employee-table-column-dropdown',
        formId: 'employee_form',
        detailId: 'employee_id',
        submitButton: '#submit-data',
        deleteMultipleTrigger: '#delete-data',
        deleteTrigger: '.delete-details',
        createTrigger: '.new-button',
        checkboxes: '.datatable-checkbox-children:checked',
        filterOnboardDate: '#filter_onboard_date',
        filterOffboardDate: '#filter_offboard_date',
        filterCreatedDate: '#filter_created_date',
        filterDrawer: 'employee-filter-drawer',
        filterCompanyDropdown: '#filter_company_id',
        filterEmploymentTypeDropdown: '#filter_employment_type_id',
        filterDepartmentDropdown: '#filter_department_id',
        filterEmploymentStatusDropdown: '#filter_employment_status_id',
        filterJobPositionDropdown: '#filter_job_position_id',
        filterManagerDropdown: '#filter_manager_id',
        filterMaritalStatusDropdown: '#filter_marital_status_id',
        filterGenderDropdown: '#filter_gender_id',
        filterReligionDropdown: '#filter_religion_id',
        filterBloodTypeDropdown: '#filter_blood_type_id',
    },
    classes: {
        deleteTrigger: 'delete-details',
    },
    endpoints: {
        tableData: '/employee/generate-table',
        save: '/employee/save',
        delete: '/employee/delete',
        deleteMultiple: '/employee/delete-multiple',
        fetch: '/employee/fetch',
        companyOption: '/company/generate-option',
        employmentTypeOption: '/employment-type/generate-option',
        departmentOption: '/department/generate-option',
        employmentStatusOption: '/employment-status/generate-option',
        jobPositionOption: '/job-position/generate-option',
        managerOption: '/employee/generate-option',
        maritalStatusOption: '/marital-status/generate-option',
        religionOption: '/religion/generate-option',
        genderOption: '/gender/generate-option',
        bloodTypeOption: '/blood-type/generate-option',
    }
};
    
export class Employee {
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
            filterCompany: document.querySelector(CONFIG.selectors.filterCompanyDropdown),
            filterEmploymentType: document.querySelector(CONFIG.selectors.filterEmploymentTypeDropdown),
            filterDepartment: document.querySelector(CONFIG.selectors.filterDepartmentDropdown),
            filterEmploymentStatus: document.querySelector(CONFIG.selectors.filterEmploymentStatusDropdown),
            filterJobPosition: document.querySelector(CONFIG.selectors.filterJobPositionDropdown),
            filterManager: document.querySelector(CONFIG.selectors.filterManagerDropdown),
            filterMaritalStatus: document.querySelector(CONFIG.selectors.filterMaritalStatusDropdown),
            filterGender: document.querySelector(CONFIG.selectors.filterGenderDropdown),
            filterReligion: document.querySelector(CONFIG.selectors.filterReligionDropdown),
            filterBloodType: document.querySelector(CONFIG.selectors.filterBloodTypeDropdown),
            filterOnboardDate: document.querySelector(CONFIG.selectors.filterOnboardDate),
            filterOffboardDate: document.querySelector(CONFIG.selectors.filterOffboardDate),
            filterCreatedDate: document.querySelector(CONFIG.selectors.filterCreatedDate)
        };
    }

    async init() {
        return PageInitializer.run(async () => {
            this.initDropdownOption();
            await this.saveFilterManager.checkAndApplyDefaultFilter();
            //this.initTable();
            
            await Promise.all([
                this.initDelete(),
                this.initDateRangePicker()
            ]);
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
                filter_company: $(this.dom.filterCompany).val() || [],
                filter_employment_type: $(this.dom.filterEmploymentType).val() || [],
                filter_department: $(this.dom.filterDepartment).val() || [],
                filter_employment_status: $(this.dom.filterEmploymentStatus).val() || [],
                filter_job_position: $(this.dom.filterJobPosition).val() || [],
                filter_manager: $(this.dom.filterManager).val() || [],
                filter_marital_status: $(this.dom.filterMaritalStatus).val() || [],
                filter_gender: $(this.dom.filterGender).val() || [],
                filter_religion: $(this.dom.filterReligion).val() || [],
                filter_blood_type: $(this.dom.filterBloodType).val() || [],
                filter_onboard_date: this.dom.filterOnboardDate?.value || '',
                filter_offboard_date: this.dom.filterOffboardDate?.value || '',
                filter_created_date: this.dom.filterCreatedDate?.value || '',
            }),
            colVisContainer: CONFIG.selectors.tableColumn,
            exportColumns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
            order: [[2, 'asc']],
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
                    column: 'Logo',
                    render: (data, type, row) => `<img src="${escapeHtml(row.logo)}" alt="Employee Logo" width="45" onerror="this.src='/assets/media/default/default-employee-logo.png';" />`
                },
                { 
                    data: 'legal_name',
                    column: 'Legal Name',
                    title: 'Legal Name'
                },
                { 
                    data: 'trade_name',
                    column: 'Trade Name',
                    title: 'Trade Name'
                },
                { 
                    data: 'address',
                    column: 'Address',
                    title: 'Address'
                },
                { 
                    data: 'tin',
                    column: 'TIN',
                    title: 'TIN',
                    visible: false 
                },
                { 
                    data: 'branch_code',
                    column: 'Branch Code',
                    title: 'Branch Code',
                    visible: false
                },
                { 
                    data: 'entity_type',
                    column: 'Entity Type',
                    title: 'Entity Type',
                    visible: false
                },
                {
                    data: 'sec_dti_registration_no',
                    column: 'SEC/DTI Registration No.',
                    title: 'SEC/DTI Registration No.',
                    visible: false
                },
                {
                    data: 'date_registered',
                    column: 'Date Registered',
                    title: 'Date Registered',
                    visible: false
                },
                { 
                    data: 'psic_code',
                    column: 'PSIC Code',
                    title: 'PSIC Code',
                    visible: false
                },
                { 
                    data: 'line_of_business',
                    column: 'Line of Business',
                    title: 'Line of Business',
                    visible: false
                },
                { 
                    data: 'vat_status',
                    column: 'VAT Status',
                    title: 'VAT Status',
                    visible: false
                },
                { 
                    data: 'currency',
                    column: 'Currency',
                    title: 'Currency',
                    visible: false 
                },
                { 
                    data: 'phone',
                    column: 'Phone',
                    title: 'Phone',
                    visible: false },
                { 
                    data: 'email',
                    column: 'Email',
                    title: 'Email',
                    visible: false
                },
                { 
                    data: 'website',
                    column: 'Website',
                    title: 'Website',
                    visible: false
                },
                {
                    data: 'contact_person',
                    column: 'Contact Person',
                    title: 'Contact Person',
                    visible: false
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
                        const safeId = escapeHtml(row.id);
                
                        return `
                        <div class="text-center me-10">
                            <a href="#" class="btn btn-light-dark btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                Actions
                                <i class="ki-duotone ki-down fs-5 ms-1"></i>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                                                
                
                                ${perms.delete ? `
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 text-danger ${CONFIG.classes.deleteTrigger}" data-reference-id="${safeId}" data-kt-users-table-filter="delete_row">
                                        <span class="menu-icon me-2"><i class="ki-duotone ki-trash fs-5 text-danger"></i></span>
                                        <span class="menu-title text-danger">Delete</span>
                                    </a>
                                </div>` : ''}
                            </div>
                        </div>`;
                    }
                }
            ]
        });
    }

    initDelete() {
        initConfirmAction({
            trigger: CONFIG.selectors.deleteMultipleTrigger,
            url: CONFIG.endpoints.deleteMultiple,
            method: 'DELETE',
            payload: { 
                'employee_id': () => {
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
            selector: [CONFIG.selectors.filterCreatedDate, CONFIG.selectors.filterOnboardDate, CONFIG.selectors.filterOffboardDate]
        });
    }

    initDropdownOption() {
        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.companyOption,
            dropdownSelector: [CONFIG.selectors.filterCompanyDropdown]
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.employmentTypeOption,
            dropdownSelector: [CONFIG.selectors.filterEmploymentTypeDropdown]
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.departmentOption,
            dropdownSelector: [CONFIG.selectors.filterDepartmentDropdown]
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.employmentStatusOption,
            dropdownSelector: [CONFIG.selectors.filterEmploymentStatusDropdown]
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.jobPositionOption,
            dropdownSelector: [CONFIG.selectors.filterJobPositionDropdown]
        });

        /*ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.managerOption,
            dropdownSelector: [CONFIG.selectors.filterManagerDropdown]
        });*/

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.maritalStatusOption,
            dropdownSelector: [CONFIG.selectors.filterMaritalStatusDropdown]
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.religionOption,
            dropdownSelector: [CONFIG.selectors.filterReligionDropdown]
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.genderOption,
            dropdownSelector: [CONFIG.selectors.filterGenderDropdown]
        });

        ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.bloodTypeOption,
            dropdownSelector: [CONFIG.selectors.filterBloodTypeDropdown]
        });
    }
}