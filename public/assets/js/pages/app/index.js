import { DataTableOrchestrator } from '../../util/dataTableOrchestrator.js';

document.addEventListener('DOMContentLoaded', () => {
    const appTable = new DataTableOrchestrator();

    appTable.initialize({
        selector: '#app-table',
        url: '/app/generate-table',
        serverSide: false, 
        pageLength: 10,
        
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
            controls: true, // Connects your custom search & length dropdowns
            export: true
        }
    });
});