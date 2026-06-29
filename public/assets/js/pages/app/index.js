import { DataTableOrchestrator } from '../../util/dataTableOrchestrator.js';

document.addEventListener('DOMContentLoaded', () => {
    const userGrid = new DataTableOrchestrator();

    userGrid.initialize({
        selector: '#app-table',
        url: '/api/v1/users/fetch',
        columns: [
            { data: 'checkbox', orderable: false },
            { data: 'name' },
            { data: 'email' },
            { data: 'role' }
        ],
        addons: {
            controls: true,
            export: true
        },
        onRowClick: (user) => {
            window.location.assign(`/admin/users/view/${user.id}`);
        }
    });
});