'use strict';

import { DataTableOrchestrator } from '../../util/dataTableOrchestrator.js';
import { AuditLogManager } from '../../util/auditLogManager.js';
import { initValidation } from '../../util/validator.js';
import { FormEnvironmentManager } from '../../util/formEnvironmentManager.js';
import { errorHandler } from '../../util/errorHandler.js';
import { ButtonStateManager } from '../../util/buttonManager.js';
import { DetailFetcher } from '../../util/detailFetcher.js';
import { initConfirmAction } from '../../util/confirmationAction.js';

/**
 * MODULE REGISTRY & FLEXIBLE CONFIGURATION
 * * To add new tables, forms, or actions, simply append a configuration object
 * to the respective array below. No need to touch the underlying engine logic.
 */
const CONTROLLER_CONFIG = {
    // 1. Global Module Tracking Key for Logs
    moduleKey: 'apps',

    // 2. DataTables Setup (Supports Multiple Tables)
    tables: [
        {
            selector: '#app-table',
            endpoint: '/app/generate-table',
            pageLength: 10,
            columnDefs: [
                { width: '5%', bSortable: false, targets: 0 },
                { width: '10%', bSortable: false, targets: 4 },
            ],
            addons: { controls: true, export: true }
        }
    ],

    // 3. Form Validation & Submissions (Supports Multiple Forms)
    forms: [
        {
            formSelector: '#app_form',
            modalSelector: '#form-modal',
            submitBtnSelector: '#submit-data',
            saveEndpoint: '/app/save',
            rules: {
                name: { required: true },
                description: { required: true },
                order_sequence: { required: true }
            }
        }
    ],

    // 4. Data Record Fetching / Editing Configurations
    fetchers: {
        endpoint: '/app/fetch',
        detailIdKey: 'app_id',
        // Map backend resource fields to frontend input element IDs
        fieldMappings: {
            'app_id': (recordId, data) => recordId, // Special handler for primary key assignment
            'name': (recordId, data) => data.name,
            'order_sequence': (recordId, data) => data.order_sequence,
            'description': (recordId, data) => data.description
        }
    },

    // 5. Destructive Confirmations (Supports Multiple Custom Actions)
    confirmations: [
        {
            triggerClass: '.delete-details',
            endpoint: '/app/delete',
            method: 'DELETE',
            payloadKey: 'app_id',
            swalTitle: 'Are you sure?',
            swalText: 'This will permanently delete this record!',
            confirmButtonText: 'Yes, delete it'
        }
    ]
};

// Internal Orchestration Engine Token Storage
const initializedTables = new Map();

/**
 * LIFECYCLE ENGINE INITIALIZATION
 * This parses the configuration arrays automatically.
 */
document.addEventListener('DOMContentLoaded', () => {
    // Start standard shared subsystems
    AuditLogManager.attachLogNotesClassHandler('.view-log-notes', CONTROLLER_CONFIG.moduleKey);
    
    // Register Dynamic Framework Components
    buildDataTables();
    buildFormValidators();
    buildConfirmationHandlers();
    registerGlobalUIInteractions();
});

/**
 * Engine Block: Builds and registers Datatable arrays cleanly
 */
function buildDataTables() {
    CONTROLLER_CONFIG.tables.forEach(tableCfg => {
        const orchestrator = new DataTableOrchestrator();
        orchestrator.initialize({
            selector: tableCfg.selector,
            url: tableCfg.endpoint,
            serverSide: false, 
            pageLength: tableCfg.pageLength,
            actionDropdown: '.action-dropdown',
            masterCheckbox: '.datatable-checkbox-master',
            lengthSelector: '.datatable-length',
            searchSelector: '.datatable-search',
            columns: getAppTableColumns(tableCfg), // Extracted for visibility clean up
            columnDefs: tableCfg.columnDefs,
            addons: tableCfg.addons
        });
        
        // Track references globally so they can be reloaded or purged anywhere
        initializedTables.set(tableCfg.selector, orchestrator);
    });
}

/**
 * Engine Block: Loops through forms array configuration dynamically
 */
function buildFormValidators() {
    const validatedFormsArray = CONTROLLER_CONFIG.forms.map(formCfg => {
        return {
            selector: formCfg.formSelector,
            rules: formCfg.rules,
            submitHandler: async (formElement) => {
                const btn = formCfg.submitBtnSelector;
                ButtonStateManager.disable(btn, { loadingText: 'Saving...', showLoader: true });

                try {
                    const response = await fetch(formCfg.saveEndpoint, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: new FormData(formElement)
                    });

                    if (await errorHandler.handleResponse(response, btn)) return;

                    ButtonStateManager.enable(btn);
                    FormEnvironmentManager.resetForm(formElement);
                    $(formCfg.modalSelector).modal('hide');
                    
                    // Reload all tracked data tables automatically upon save transactions
                    initializedTables.forEach((orchestrator, selector) => orchestrator.reload(selector));

                } catch (error) {
                    ButtonStateManager.enable(btn);
                    await errorHandler.handle(error, 'network_failure', 'Transactional pipeline error.');
                }
            }
        };
    });

    initValidation({ forms: validatedFormsArray });
}

/**
 * Engine Block: Hydrates form elements from API fetches dynamically
 */
async function hydrateRecordForm(recordId) {
    const cfg = CONTROLLER_CONFIG.fetchers;
    
    await DetailFetcher.fetch({
        url: cfg.endpoint,
        detailIdKey: cfg.detailIdKey,
        detailIdValue: recordId,
        onSuccess: (response) => {
            const data = response.data;
            
            // Loop through map to inject values into target fields safely
            Object.entries(cfg.fieldMappings).forEach(([elementId, resolveValue]) => {
                const element = document.getElementById(elementId);
                if (element) {
                    element.value = resolveValue(recordId, data);
                }
            });
        }
    });
}

/**
 * Engine Block: Loops and hooks confirm actions (Deletes, Toggles, etc.)
 */
function buildConfirmationHandlers() {
    CONTROLLER_CONFIG.confirmations.forEach(action => {
        initConfirmAction({
            trigger: action.triggerClass,
            url: action.endpoint,
            method: action.method,
            payload: { 
                [action.payloadKey]: (el) => el.dataset.referenceId 
            },
            swalTitle: action.swalTitle,
            swalText: action.swalText,
            confirmButtonText: action.confirmButtonText,
            confirmButtonClass: action.method === 'DELETE' ? 'danger' : 'primary'
        });
    });
}

/**
 * Engine Block: Central Event Delegation
 */
function registerGlobalUIInteractions() {
    document.addEventListener('click', (event) => {
        const target = event.target;
        const updateTrigger = target.closest('.update-details');
        const createTrigger = target.closest('.new-button');
        
        if (updateTrigger) {
            hydrateRecordForm(updateTrigger.dataset.referenceId);
        }

        if (createTrigger) {
            // Find forms mapped in config to wipe inputs safely without hardcoded IDs
            CONTROLLER_CONFIG.forms.forEach(formCfg => {
                const cleanId = formCfg.formSelector.replace('#', '');
                FormEnvironmentManager.resetForm(cleanId);
            });
        }
    });
}

/**
 * Column Blueprint Matrix Definition Helper
 */
function getAppTableColumns(tableConfig) {
    return [
        { 
            data: 'id',
            render: (id) => `
                <div class="form-check form-check-sm ms-5">
                    <input class="form-check-input datatable-checkbox-children" type="checkbox" value="${id}">
                </div>`
        },
        { 
            data: null,
            render: (row) => {
                const safeName = typeof e === 'function' ? e(row.name) : row.name; 
                return `
                    <div class="d-flex align-items-center">
                        <img src="${row.logo_url}" alt="logo" width="45" />
                        <div class="ms-3"><h6 class="mb-0">${safeName}</h6></div>
                    </div>`;
            }
        },
        { 
            data: 'description',
            render: (desc) => {
                const safeDesc = typeof e === 'function' ? e(desc) : desc;
                return `<div class="text-gray-800 text-wrap">${safeDesc}</div>`;
            }
        },
        { data: 'order_sequence' },
        { 
            data: null, 
            render: (data, type, row, meta) => {
                const globalPerms = meta.settings.json?.permissions;
                const canWrite = globalPerms ? globalPerms.write : row.permissions?.can_write;
                const canLogs = globalPerms ? globalPerms.logs : row.permissions?.can_logs;
                const canDelete = globalPerms ? globalPerms.delete : row.permissions?.can_delete;

                const formTarget = CONTROLLER_CONFIG.forms[0]?.modalSelector || '#form-modal';

                const updateBtn = canWrite ? `<button class="btn btn-sm btn-icon btn-light-primary update-details" data-bs-toggle="modal" data-bs-target="${formTarget}" data-reference-id="${row.id}" title="Edit"><i class="ki-outline ki-eye fs-5 m-0"></i></button>` : '';
                const logBtn = canLogs ? `<button class="btn btn-sm btn-icon btn-light-warning view-log-notes" data-reference-id="${row.id}" data-bs-toggle="modal" data-bs-target="#log-notes-modal" title="Logs"><i class="ki-outline ki-shield-search fs-5 m-0"></i></button>` : '';
                const deleteBtn = canDelete ? `<button class="btn btn-sm btn-icon btn-light-danger delete-details" data-reference-id="${row.id}" title="Delete"><i class="ki-outline ki-trash fs-5 m-0"></i></button>` : '';

                return `<div class="d-flex justify-content-end gap-2 me-5">${updateBtn} ${logBtn} ${deleteBtn}</div>`;
            }
        }
    ];
}