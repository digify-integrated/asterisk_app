import { DataTableOrchestrator } from '../../util/dataTableOrchestrator.js';
import { AuditLogManager } from '../../util/auditLogManager.js';

document.addEventListener('DOMContentLoaded', () => {
    const manager = new DataTableOrchestrator();

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
});