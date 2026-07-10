'use strict';

import { DataTableOrchestrator } from '../../util/dataTableOrchestrator.js';
import { AuditLogManager } from '../../util/auditLogManager.js';
import { initValidation } from '../../util/validator.js';
import { FormEnvironmentManager } from '../../util/formEnvironmentManager.js';
import { errorHandler } from '../../util/errorHandler.js';
import { ButtonStateManager } from '../../util/buttonManager.js';
import { DetailFetcher } from '../../util/detailFetcher.js';
import { initConfirmAction } from '../../util/confirmationAction.js';

class BaseAppController {
    constructor(config) {
        this.config = config;
        this.initializedTables = new Map();
        this._abortController = new AbortController();
        this.escapeHtml = typeof window.e === 'function' ? window.e : (str) => {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        };
    }

    init() {
        AuditLogManager.attachLogNotesClassHandler('.view-log-notes', this.config.moduleKey);
        
        this.buildDataTables();
        this.buildFormValidators();
        this.buildConfirmationHandlers();
        this.registerGlobalUIInteractions();
    }

    destroy() {
        this._abortController.abort();
        this.initializedTables.clear();
    }

    buildDataTables() {
        if (!Array.isArray(this.config.tables)) return;

        this.config.tables.forEach(tableCfg => {
            const orchestrator = new DataTableOrchestrator();
            
            const columns = typeof tableCfg.columns === 'function' 
                ? tableCfg.columns(this) 
                : tableCfg.columns;

            orchestrator.initialize({
                selector: tableCfg.selector,
                url: tableCfg.endpoint,
                serverSide: tableCfg.serverSide ?? false, 
                pageLength: tableCfg.pageLength ?? 10,
                actionDropdown: '.action-dropdown',
                masterCheckbox: '.datatable-checkbox-master',
                lengthSelector: '.datatable-length',
                searchSelector: '.datatable-search',
                columns: columns,
                columnDefs: tableCfg.columnDefs || [],
                addons: tableCfg.addons || {}
            });

            this.initializedTables.set(tableCfg.selector, orchestrator);
        });
    }

    buildFormValidators() {
        if (!Array.isArray(this.config.forms)) return;

        const validatedFormsArray = this.config.forms.map(formCfg => ({
            selector: formCfg.formSelector,
            rules: formCfg.rules,
            submitHandler: async (formElement) => {
                const btn = formCfg.submitBtnSelector;
                ButtonStateManager.disable(btn, { loadingText: 'Saving...', showLoader: true });

                try {
                    const response = await fetch(formCfg.saveEndpoint, {
                        method: 'POST',
                        headers: { 
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(formElement)
                    });

                    if (await errorHandler.handleResponse(response, btn)) return;

                    ButtonStateManager.enable(btn);
                    FormEnvironmentManager.resetForm(formElement);
                    
                    const modalEl = document.querySelector(formCfg.modalSelector);
                    if (modalEl && window.jQuery) {
                        window.jQuery(modalEl).modal('hide');
                    }

                    this.initializedTables.forEach(orchestrator => {
                        if (typeof orchestrator.reload === 'function') {
                            orchestrator.reload();
                        }
                    });

                } catch (error) {
                    ButtonStateManager.enable(btn);
                    await errorHandler.handle(error, 'network_failure', 'Transactional pipeline error.');
                }
            }
        }));

        initValidation({ forms: validatedFormsArray });
    }

    async hydrateRecordForm(recordId) {
        const cfg = this.config.fetchers;
        if (!cfg) return;
        
        await DetailFetcher.fetch({
            url: cfg.endpoint,
            detailIdKey: cfg.detailIdKey,
            detailIdValue: recordId,
            onSuccess: (response) => {
                const data = response?.data || response;
                Object.entries(cfg.fieldMappings).forEach(([elementId, resolveValue]) => {
                    const element = document.getElementById(elementId);
                    if (element) {
                        element.value = resolveValue(recordId, data);
                        element.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }
        });
    }

    buildConfirmationHandlers() {
        if (!Array.isArray(this.config.confirmations)) return;

        this.config.confirmations.forEach(action => {
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

    registerGlobalUIInteractions() {
        const { signal } = this._abortController;

        document.addEventListener('click', (event) => {
            const target = event.target;
            const updateTrigger = target.closest('.update-details');
            const createTrigger = target.closest('.new-button');
            
            if (updateTrigger) {
                this.hydrateRecordForm(updateTrigger.dataset.referenceId);
            }

            if (createTrigger && Array.isArray(this.config.forms)) {
                this.config.forms.forEach(formCfg => {
                    const cleanId = formCfg.formSelector.replace('#', '');
                    FormEnvironmentManager.resetForm(cleanId);
                });
            }
        }, { signal });
    }
}

const appController = new BaseAppController({
    moduleKey: 'apps',
    tables: [
        {
            selector: '#app-table',
            endpoint: '/app/generate-table',
            pageLength: 10,
            columnDefs: [
                { width: '5%', bSortable: false, targets: 0 },
                { width: '10%', bSortable: false, targets: 4 },
            ],
            addons: { controls: true, export: true },
            columns: (ctx) => [
                { 
                    data: 'id',
                    render: (id) => `
                        <div class="form-check form-check-sm ms-5">
                            <input class="form-check-input datatable-checkbox-children" type="checkbox" value="${ctx.escapeHtml(id)}">
                        </div>`
                },
                { 
                    data: null,
                    render: (row) => {
                        const safeName = ctx.escapeHtml(row.name); 
                        const safeUrl = ctx.escapeHtml(row.logo_url);
                        return `
                            <div class="d-flex align-items-center">
                                <img src="${safeUrl}" alt="logo" width="45" onerror="this.src='/assets/media/svg/brand-logos/abstract.svg';" />
                                <div class="ms-3"><h6 class="mb-0">${safeName}</h6></div>
                            </div>`;
                    }
                },
                { 
                    data: 'description',
                    render: (desc) => `<div class="text-gray-800 text-wrap">${ctx.escapeHtml(desc)}</div>`
                },
                { 
                    data: 'order_sequence',
                    render: (seq) => ctx.escapeHtml(seq)
                },
                { 
                    data: null, 
                    render: (data, type, row, meta) => {
                        const globalPerms = meta.settings.json?.permissions;
                        const canWrite = globalPerms ? globalPerms.write : row.permissions?.can_write;
                        const canLogs = globalPerms ? globalPerms.logs : row.permissions?.can_logs;
                        const canDelete = globalPerms ? globalPerms.delete : row.permissions?.can_delete;

                        const formTarget = ctx.config.forms[0]?.modalSelector || '#form-modal';
                        const safeId = ctx.escapeHtml(row.id);

                        const updateBtn = canWrite ? `<button class="btn btn-sm btn-icon btn-light-primary update-details" data-bs-toggle="modal" data-bs-target="${formTarget}" data-reference-id="${safeId}" title="Edit"><i class="ki-outline ki-eye fs-5 m-0"></i></button>` : '';
                        const logBtn = canLogs ? `<button class="btn btn-sm btn-icon btn-light-warning view-log-notes" data-reference-id="${safeId}" data-bs-toggle="modal" data-bs-target="#log-notes-modal" title="Logs"><i class="ki-outline ki-shield-search fs-5 m-0"></i></button>` : '';
                        const deleteBtn = canDelete ? `<button class="btn btn-sm btn-icon btn-light-danger delete-details" data-reference-id="${safeId}" title="Delete"><i class="ki-outline ki-trash fs-5 m-0"></i></button>` : '';

                        return `<div class="d-flex justify-content-end gap-2 me-5">${updateBtn} ${logBtn} ${deleteBtn}</div>`;
                    }
                }
            ]
        }
    ],
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
    fetchers: {
        endpoint: '/app/fetch',
        detailIdKey: 'app_id',
        fieldMappings: {
            'app_id': (recordId, data) => recordId,
            'name': (recordId, data) => data.name,
            'order_sequence': (recordId, data) => data.order_sequence,
            'description': (recordId, data) => data.description
        }
    },
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
});

document.addEventListener('DOMContentLoaded', () => appController.init());