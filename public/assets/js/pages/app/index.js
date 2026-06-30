import { DataTableOrchestrator } from '../../util/dataTableOrchestrator.js';

document.addEventListener('DOMContentLoaded', () => {
    // You only need one instance of the manager class orchestrator
    const manager = new DataTableOrchestrator();

    // 1. Configure the Main App Table
    manager.initialize({
        selector: '#app-table',
        url: '/app/generate-table',
        serverSide: false, 
        pageLength: 10,
        actionDropdown: '.action-dropdown', // scopes relative to your wrapper '.table-container'
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