/**
 * @fileoverview Orchestrates data grid workflows, validation schemas, 
 * and transactional record mutations for the Application Management Module.
 * @version 2.0.0
 */

'use strict';

import { DataTableOrchestrator } from '../../util/dataTableOrchestrator.js';
import { AuditLogManager } from '../../util/auditLogManager.js';
import { initValidation } from '../../util/validator.js';
import { FormEnvironmentManager } from '../../util/formEnvironmentManager.js';
import { errorHandler } from '../../util/errorHandler.js';
import { ButtonStateManager } from '../../util/buttonManager.js';
import { DetailFetcher } from '../../util/detailFetcher.js';
import { initConfirmAction } from '../../util/confirmationAction.js';

/** @constant {Object} */
const CONFIG = {
    selectors: {
        table: '#app-table',
        form: '#app_form',
        submitButton: '#submit-data',
        modal: '#form-modal',
        logNotesTrigger: '.view-log-notes',
        deleteMultipleTrigger: '#delete-data',
        deleteTrigger: '.delete-details',
        updateTrigger: '.update-details',
        createTrigger: '.new-button',
        checkboxes: '.datatable-checkbox-children:checked'
    },
    endpoints: {
        tableData: '/app/generate-table',
        save: '/app/save',
        delete: '/app/delete',
        deleteMultiple: '/app/delete-multiple',
        fetch: '/app/fetch'
    }
};

// Compile string mutations once at standard module initialization scale
const ESCAPE_REGEX = /[&<>"']/g;
const ESCAPE_MAP = Object.freeze({ 
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' 
});

/**
 * Escapes unsafe string values to prevent Cross-Site Scripting (XSS) vectors.
 * @param {string|null|undefined} str 
 * @returns {string}
 */
const escapeHtml = typeof window.e === 'function' 
    ? window.e 
    : (str) => str == null ? '' : String(str).replace(ESCAPE_REGEX, m => ESCAPE_MAP[m]);

/**
 * Encapsulated Core Module controller managing feature context lifecycle.
 */
class AppModuleManager {
    constructor() {
        this.orchestrator = new DataTableOrchestrator();
        this.abortController = new AbortController();
        
        // Caching key nodes globally across feature lifecycles
        this.dom = {
            table: document.querySelector(CONFIG.selectors.table),
            form: document.querySelector(CONFIG.selectors.form),
            modal: $(CONFIG.selectors.modal) // Cached jQuery context if required by bootstrap bridge
        };
    }

    /**
     * Initializes core subsystems and sets context listeners.
     */
    init() {
        this.initTable();
        this.initForm();
        this.initDelete();
        this.registerGlobalListeners();
        
        AuditLogManager.attachLogNotesClassHandler(CONFIG.selectors.logNotesTrigger, 'apps');
    }

    /**  
     * Deploys the presentation layer data grid orchestrator instance.
     */
    initTable() {
        this.orchestrator.initialize({
            selector: CONFIG.selectors.table,
            url: CONFIG.endpoints.tableData,
            columnDefs: [
                { width: '5%', bSortable: false, targets: 0 },
                { width: '20%', targets: 1 },
                { width: '15%', targets: 3 },
                { width: '10%', bSortable: false, targets: 4 },
            ],
            addons: { controls: true, export: true },
            columns: [
                { 
                    data: 'id',
                    render: (id) => `
                        <div class="form-check form-check-sm ms-5">
                            <input class="form-check-input datatable-checkbox-children" type="checkbox" value="${escapeHtml(id)}">
                        </div>`
                },
                { 
                    data: null,
                    render: (row) => `
                        <div class="d-flex align-items-center">
                            <img src="${escapeHtml(row.logo_url)}" alt="logo" width="45" onerror="this.src='/assets/media/svg/brand-logos/abstract.svg';" />
                            <div class="ms-3"><h6 class="mb-0">${escapeHtml(row.name)}</h6></div>
                        </div>`
                },
                { 
                    data: 'description',
                    render: (desc) => `<div class="text-gray-800 text-wrap">${escapeHtml(desc)}</div>`
                },
                { 
                    data: 'order_sequence',
                    render: (seq) => escapeHtml(seq)
                },
                { 
                    data: null, 
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

    /**
     * Builds declarative schema validation hooks mapping form inputs to request pipes.
     */
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

    /**
     * Processing pipeline handling secure mutations for record saving.
     * @param {HTMLFormElement} formElement 
     */
    async handleFormSubmission(formElement) {
        const btn = CONFIG.selectors.submitButton;
        ButtonStateManager.disable(btn, { loadingText: 'Saving...', showLoader: true });

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

    /**
     * Binds downstream configuration interceptors for safe structural removals.
     */
    initDelete() {
        initConfirmAction({
            trigger: CONFIG.selectors.deleteTrigger,
            url: CONFIG.endpoints.delete,
            method: 'DELETE',
            payload: { app_id: (el) => el.dataset.referenceId },
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
                'app_id': () => {
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

    /**
     * Maps user click tracking schemas cleanly using explicit single element tracking vectors.
     */
    registerGlobalListeners() {
        document.addEventListener('click', async (event) => {
            const { target } = event;
            
            // Branch Path 1: Element Fetch Update Vector
            const updateTrigger = target.closest(CONFIG.selectors.updateTrigger);
            if (updateTrigger) {
                this.handleFetchWorkflow(updateTrigger.dataset.referenceId);
                return;
            }

            // Branch Path 2: Reset / Fresh Entity State Call
            if (target.closest(CONFIG.selectors.createTrigger)) {
                FormEnvironmentManager.resetForm(CONFIG.selectors.form.slice(1));
            }
        }, { signal: this.abortController.signal });
    }

    /**
     * Executes asynchronous fetch updates to populate current targets across fields.
     * @param {string} referenceId 
     */
    async handleFetchWorkflow(referenceId) {
        await DetailFetcher.fetch({
            url: CONFIG.endpoints.fetch,
            detailIdKey: 'app_id',
            detailIdValue: referenceId,
            signal: this.abortController.signal,
            onSuccess: (response) => {
                const data = response?.data || response;
                if (!this.dom.form) return;

                const targetFields = {
                    'app_id': referenceId,
                    'name': data.name,
                    'order_sequence': data.order_sequence,
                    'description': data.description
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