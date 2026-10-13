'use strict';

import { PageInitializer } from '../util/pageInitializer.js';
import { initValidation } from '../util/validator.js';
import { FormEnvironmentManager } from '../util/formEnvironmentManager.js';
import { errorHandler } from '../util/errorHandler.js';
import { ButtonStateManager } from '../util/buttonManager.js';
import { ComponentRegistry } from '../util/componentRegistry.js';
import { Toast } from '../util/notifications.js';

const CONFIG = {
    selectors: {
        form: '#import_form',
        databaseTableDropdown: '#database_table',
        importFileInput: '#import_file',
        configureMappingBtn: '#configure-mapping',
        backToConfigBtn: '#back-to-config-btn',
        testImportBtn: '#test-import-btn',
        configCard: '#config-card',
        mappingCard: '#mapping-card',
        mappingTableBody: '#mapping-table-body',
        downloadTemplateLink: '#download-template-link',
        templateListContainer: '#dynamic-template-list',
        submitButton: '#submit-btn'
    },
    endpoints: {
        databaseTableOption: '/navigation-menu-database-table/generate-option',
        parseCsv: '/import/parse-csv',
        testImport: '/import/test-import',
        save: '/import/commit' // Matches commit() method in ImportController
    }
};
    
export class Import {
    constructor() {
        this.abortController = new AbortController();
        
        this.dom = {
            form: document.querySelector(CONFIG.selectors.form),
            configCard: document.querySelector(CONFIG.selectors.configCard),
            mappingCard: document.querySelector(CONFIG.selectors.mappingCard),
            databaseTable: document.querySelector(CONFIG.selectors.databaseTableDropdown),
            importFile: document.querySelector(CONFIG.selectors.importFileInput),
            mappingTableBody: document.querySelector(CONFIG.selectors.mappingTableBody)
        };
    }

    async init() {
        return PageInitializer.run(async () => {      
            await this.initDropdownOption();
            this.populateTemplateList();
            this.initForm();
            this.registerGlobalListeners();
        });
    }

    populateTemplateList() {
        const dropdown = document.querySelector(CONFIG.selectors.databaseTableDropdown);
        const container = document.querySelector(CONFIG.selectors.templateListContainer);
        
        if (!dropdown || !container) return;

        const updateList = () => {
            container.innerHTML = '';
            const options = Array.from(dropdown.options).filter(opt => opt.value !== '');

            if (options.length === 0) {
                container.innerHTML = '<div class="text-muted fs-8 py-2">No tables available.</div>';
                return;
            }

            options.forEach(opt => {
                const tableName = opt.value;
                const label = opt.text;

                const item = document.createElement('a');
                item.href = `/import/template/${tableName}`;
                item.className = 'd-flex align-items-center justify-content-between p-3 border border-gray-200 rounded text-decoration-none bg-hover-light transition-all text-gray-800';
                item.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <i class="ki-duotone ki-file fs-4 text-gray-500"><span class="path1"></span><span class="path2"></span></i>
                        <span class="fw-medium fs-7">${label}</span>
                    </div>
                    <span class="fs-8 text-gray-500 fw-semibold d-flex align-items-center gap-1">
                        Download .csv 
                        <i class="ki-duotone ki-arrow-right fs-7 text-gray-400"><span class="path1"></span><span class="path2"></span></i>
                    </span>
                `;
                
                container.appendChild(item);
            });
        };

        updateList();
        $(dropdown).on('change select2:updated', () => updateList());
    }

    initForm() {
        initValidation({
            forms: [
                {
                    selector: CONFIG.selectors.form,
                    rules: {
                        database_table: { required: true },
                        import_file: { required: true },
                    },
                    submitHandler: async (formElement) => this.handleFormSubmission(formElement)
                }
            ]
        });
    }

    registerGlobalListeners() {
        // Step 1 -> Step 2: Configure Mapping Button
        const configureBtn = document.querySelector(CONFIG.selectors.configureMappingBtn);
        if (configureBtn) {
            configureBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                await this.handleConfigureMapping();
            }, { signal: this.abortController.signal });
        }

        // Step 2 -> Step 1: Change File / Table Button
        const backBtn = document.querySelector(CONFIG.selectors.backToConfigBtn);
        if (backBtn) {
            backBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchToConfigView();
            }, { signal: this.abortController.signal });
        }

        // Step 2: Test Import Button
        const testImportBtn = document.querySelector(CONFIG.selectors.testImportBtn);
        if (testImportBtn) {
            testImportBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                await this.handleTestImport();
            }, { signal: this.abortController.signal });
        }

        const templateLink = document.querySelector(CONFIG.selectors.downloadTemplateLink);
        if (templateLink) {
            templateLink.addEventListener('click', (e) => {
                e.preventDefault();
                const tableVal = this.dom.databaseTable.value;
                if (!tableVal) {
                    Toast.error('Please select a target table first to download its template.');
                    return;
                }
                window.location.href = `/import/template/${tableVal}`;
            }, { signal: this.abortController.signal });
        }
    }

    async handleConfigureMapping() {
        const tableVal = this.dom.databaseTable.value;
        const fileVal = this.dom.importFile.files[0];

        if (!tableVal || !fileVal) {
            Toast.error('Please select a target table and a CSV file first.');
            return;
        }

        const btn = CONFIG.selectors.configureMappingBtn;
        ButtonStateManager.disable(btn, { loadingText: 'Analyzing File...' });

        try {
            const formData = new FormData();
            formData.append('database_table', tableVal);
            formData.append('import_file', fileVal);

            const response = await fetch(CONFIG.endpoints.parseCsv, {
                method: 'POST',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: formData,
                signal: this.abortController.signal
            });

            if (await errorHandler.handleResponse(response, btn)) return;

            const result = await response.json();
            
            this.renderMappingTable(result.headers, result.preview, result.tableFields);

            this.dom.configCard.classList.add('d-none');
            this.dom.mappingCard.classList.remove('d-none');

            ButtonStateManager.enable(btn);
        } catch (error) {
            if (error.name === 'AbortError') return;
            ButtonStateManager.enable(btn);
            await errorHandler.handle(error, 'network_failure', 'Failed to parse CSV file.');
        }
    }

    async handleTestImport() {
        const btn = CONFIG.selectors.testImportBtn;
        ButtonStateManager.disable(btn, { loadingText: 'Testing...' });

        try {
            const response = await fetch(CONFIG.endpoints.testImport, {
                method: 'POST',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: new FormData(this.dom.form),
                signal: this.abortController.signal
            });

            if (await errorHandler.handleResponse(response, btn)) return;

            const result = await response.json();
            ButtonStateManager.enable(btn);
        } catch (error) {
            if (error.name === 'AbortError') return;
            ButtonStateManager.enable(btn);
            await errorHandler.handle(error, 'network_failure', 'Test import verification failed.');
        }
    }

    renderMappingTable(headers, preview, tableFields) {
        this.dom.mappingTableBody.innerHTML = '';

        headers.forEach((header, index) => {
            const sampleValue = preview[index] ?? '';
            
            let optionsHtml = '<option value="ignore">— Do Not Import —</option>';
            tableFields.forEach(field => {
                const cleanHeader = header.toLowerCase().replace(/\s+/g, '_');
                const cleanField = field.toLowerCase();
                
                // Match exact or common variations (e.g. "created date" -> "created_at")
                const isMatch = cleanField === cleanHeader || 
                    (cleanHeader.includes('date') && cleanField.includes('date')) ||
                    (cleanHeader.includes('time') && cleanField.includes('time'));

                const selected = isMatch ? 'selected' : '';
                optionsHtml += `<option value="${field}" ${selected}>${field}</option>`;
            });

            const row = `
                <tr>
                    <td class="ps-4">
                        <span class="fw-bold text-gray-800">${header}</span>
                        <input type="hidden" name="mapping[${index}][column]" value="${header}">
                    </td>
                    <td>
                        <select name="mapping[${index}][field]" class="form-select form-select-sm" data-control="select2">
                            ${optionsHtml}
                        </select>
                    </td>
                    <td class="pe-4">
                        <span class="fs-8 bg-danger px-2 py-1 rounded">${sampleValue}</span>
                    </td>
                </tr>
            `;
            this.dom.mappingTableBody.insertAdjacentHTML('beforeend', row);
        });

        if (window.jQuery && window.jQuery().select2) {
            $(this.dom.mappingTableBody).find('[data-control="select2"]').select2();
        }
    }

    switchToConfigView() {
        this.dom.mappingCard.classList.add('d-none');
        this.dom.configCard.classList.remove('d-none');
    }

    async handleFormSubmission(formElement) {
        const btn = CONFIG.selectors.submitButton;
        ButtonStateManager.disable(btn, { loadingText: 'Importing...' });

        try {
            const response = await fetch(CONFIG.endpoints.save, {
                method: 'POST',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: new FormData(formElement),
                signal: this.abortController.signal
            });

            if (await errorHandler.handleResponse(response, btn)) return;

            const result = await response.json();
            FormEnvironmentManager.resetForm(formElement);
            this.switchToConfigView();
        } catch (error) {
            if (error.name === 'AbortError') return; 
            ButtonStateManager.enable(btn);
            await errorHandler.handle(error, 'network_failure', 'Transactional pipeline error.');
        }
    }

    initDropdownOption() {
        const ctx = FormEnvironmentManager.getPageContext();

        return ComponentRegistry.generateDropdownOptions({
            url: CONFIG.endpoints.databaseTableOption,
            dropdownSelector: [CONFIG.selectors.databaseTableDropdown], 
            data: { navigationMenuId: ctx.navigationMenuId }
        });
    }
}