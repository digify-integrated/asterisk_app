import { DataTableOrchestrator } from '../../util/dataTableOrchestrator.js';
import { AuditLogManager } from '../../util/auditLogManager.js';
import { initValidation } from '../../util/validator.js';
import { errorHandler } from '../../util/errorHandler.js';
import { ButtonStateManager } from '../../util/buttonManager.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new DataTableOrchestrator();
    const submitBtn = '#signin';

    AuditLogManager.attachLogNotesClassHandler('.view-log-notes', 'apps');

    manager.initialize({
        selector: '#app-table',
        url: '/app/generate-table',
        serverSide: false, 
        pageLength: 10,
        actionDropdown: '.action-dropdown',
        masterCheckbox: '.datatable-checkbox-master',
        lengthSelector: '.datatable-length',
        searchSelector: '.datatable-search',
        columns: [
            { data: 'CHECK_BOX' },
            { data: 'APP' },
            { data: 'ACTION' },
        ],
        columnDefs: [
            { width: '5%', bSortable: false, targets: 0 },
            { width: 'auto', targets: 1 },
            { width: '15%', bSortable: false, targets: 2 },
        ],
        addons: {
            controls: true, 
            export: true
        }
    });

    initValidation({
        forms: [
            {
                selector: '#app_form',
                rules: {
                    name: { required: true },
                    description: { required: true },
                    order_sequence: { required: true }
                },
                submitHandler: async (form) => {
                    ButtonStateManager.disable(submitBtn, {
                        loadingText: 'Saving...',
                        showLoader: true
                    });
    
                    try {
                        const response = await fetch('/app/save', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: new URLSearchParams(new FormData(form))
                        });
    
                        if (await errorHandler.handleResponse(response, submitBtn)) {
                            return;
                        }
    
                        const data = await response.json();
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
    
                    } catch (error) {
                        ButtonStateManager.enable(submitBtn);
                        await errorHandler.handle(error, 'network_failure', 'App save request failed.');
                    }
                }
            }
        ]
    });
});