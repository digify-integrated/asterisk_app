'use strict';

import { FormEnvironmentManager } from './formEnvironmentManager.js';
import { errorHandler } from './errorHandler.js';
import { Toast } from './notifications.js';
import { ComponentRegistry } from './componentRegistry.js';

const instanceRegistry = new WeakMap();
const tableMetadataRegistry = new WeakMap();
let globalListenersConfigured = false;
let globalResizeObserver = null;

export class DataTableOrchestrator {
    constructor() {
        this._resizeTimeout = null;
        this._boundHandlers = new Map();
    }

    static getAPI(selectorOrNode) {
        const node = typeof selectorOrNode === 'string' ? document.querySelector(selectorOrNode) : selectorOrNode;
        if (!node) return null;

        if (instanceRegistry.has(node) && window.jQuery?.fn?.DataTable?.isDataTable(node)) {
            const cached = instanceRegistry.get(node);
            return typeof cached.table === 'function' ? cached : new window.jQuery.fn.dataTable.Api(node);
        }
        
        if (!window.jQuery?.fn?.DataTable?.isDataTable(node)) return null;
        
        const dtInstance = new window.jQuery.fn.dataTable.Api(node);
        instanceRegistry.set(node, dtInstance);
        return dtInstance;
    }

    initialize(options = {}) {
        const config = {
            selector: null,
            url: null,
            ajaxData: {},
            columns: [],
            columnDefs: [],
            order: [[1, 'asc']],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            onRowClick: null,
            addons: { controls: false, subControls: false },
            serverSide: true,
            pageLength: 25,
            searchDelay: 400,
            scrollX: true,
            responsive: false,
            processing: false,
            actionDropdown: '.action-dropdown',
            masterCheckbox: '#datatable-checkbox',
            ...options
        };

        const tableNode = typeof config.selector === 'string' ? document.querySelector(config.selector) : config.selector;
        if (!tableNode) return null;

        this.destroy(tableNode);
        this.resetSelectionState(config);

        if (!config.url) {
            Toast.show(`Data feed URL context omitted for target: ${config.selector}`, 'error');
            return null;
        }

        const exportWrapper = document.querySelector('.table-export');
        const enableExport = !!exportWrapper || config.addons?.export;
        const self = this;

        const dt = window.jQuery(tableNode).DataTable({
            serverSide: config.serverSide,
            processing: config.processing,
            deferRender: true,
            autoWidth: false,
            orderClasses: false,
            searchDelay: config.searchDelay,
            responsive: config.responsive,
            scrollX: config.scrollX,
            scrollCollapse: true,
            paging: true,
            pageLength: config.pageLength,
            lengthChange: false,
            order: config.order,
            columns: config.columns,
            columnDefs: config.columnDefs,
            lengthMenu: config.lengthMenu,
            buttons: enableExport ? this._buildExportConfig() : [],
            ajax: {
                url: config.url,
                type: 'GET',
                dataType: 'json',
                data: (d) => {
                    const extra = typeof config.ajaxData === 'function' ? config.ajaxData(d) : config.ajaxData;
                    return { ...d, ...extra, ...FormEnvironmentManager.getPageContext() };
                },
                dataSrc: 'data',
                error: (xhr, status, err) => errorHandler.handle(xhr, status, err)
            },
            language: {
                emptyTable: 'No records found for the selected evaluation parameters.',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                loadingRecords: 'Loading records...',
                processing: 'Processing data...',
                zeroRecords: 'No records match the current filters.'
            },
            drawCallback: () => {
                self.resetSelectionState(config);
            },
            initComplete: function () {
                dt.columns.adjust();

                if (enableExport) self._bindExportControls(dt, tableNode);
                
                if (config.addons?.controls) {
                    const reference = typeof config.addons.controls === 'object' && config.addons.controls?.table 
                        ? config.addons.controls.table 
                        : config.selector;
                    self.bindControls(reference, config);
                }

                if (config.addons?.export) {
                    const exportRef = typeof config.addons.export === 'object' && config.addons.export?.table 
                        ? config.addons.export.table 
                        : config.addons.export;
                    if (typeof ComponentRegistry.generateDualListBox === 'function') {
                        ComponentRegistry.generateDualListBox({
                            trigger: '#export-data',
                            url: '/export/export-list',
                            selectSelector: exportRef
                        });
                    }
                }

                if (config.addons?.subControls && typeof config.addons.subControls === 'object') {
                    const { searchSelector, lengthSelector, table: subRef = config.selector } = config.addons.subControls;
                    if (searchSelector && lengthSelector) {
                        self._bindSubControls(searchSelector, lengthSelector, subRef);
                    }
                }

                // Safely add runtime wrapper nodes to the global ResizeObserver tracking index
                if (globalResizeObserver) {
                    const wrapper = tableNode.closest('.dataTables_wrapper');
                    if (wrapper) globalResizeObserver.observe(wrapper);
                }
            }
        });

        instanceRegistry.set(tableNode, dt);
        this._configureGlobalAdjustmentListeners();

        if (typeof config.onRowClick === 'function') {
            this._bindRowClickHandlers(tableNode, dt, config.onRowClick);
        }

        return dt;
    }

    reload(selectorOrNode) {
        const dt = DataTableOrchestrator.getAPI(selectorOrNode);
        if (dt) dt.ajax.reload(null, false);
    }

    destroy(selectorOrNode) {
        const node = typeof selectorOrNode === 'string' ? document.querySelector(selectorOrNode) : selectorOrNode;
        if (!node) return;

        const dt = DataTableOrchestrator.getAPI(node);
        if (!dt) return;

        const tbody = node.tBodies?.[0];
        if (tbody) {
            window.jQuery(tbody).off(`.dtRowClick_${node.id || 'orchestrated'}`);
        }

        window.jQuery(document).off(`.export_${node.id || 'orchestrated'}`);

        if (this._boundHandlers.has(node)) {
            const handlers = this._boundHandlers.get(node);
            handlers.forEach(({ element, event, callback }) => {
                element?.removeEventListener(event, callback);
            });
            this._boundHandlers.delete(node);
        }

        if (globalResizeObserver) {
            const wrapper = node.closest('.dataTables_wrapper');
            if (wrapper) globalResizeObserver.unobserve(wrapper);
        }

        dt.clear();
        dt.destroy();
        instanceRegistry.delete(node);
        tableMetadataRegistry.delete(node);
    }

    bindControls(selectorOrNode, config) {
        const node = typeof selectorOrNode === 'string' ? document.querySelector(selectorOrNode) : selectorOrNode;
        let dt = DataTableOrchestrator.getAPI(node);
        if (dt && typeof dt.api === 'function') dt = dt.api(); 
        if (!dt) return;

        const container = node.closest('.table-container') || document;
        const lengthEl = container.querySelector(config.lengthSelector) || document.getElementById('datatable-length');
        const searchEl = container.querySelector(config.searchSelector) || document.getElementById('datatable-search');

        if (!this._boundHandlers.has(node)) this._boundHandlers.set(node, []);
        const handlers = this._boundHandlers.get(node);

        if (lengthEl) {
            const lengthHandler = () => {
                const limit = Number.parseInt(lengthEl.value, 10);
                const targetApi = typeof dt.page?.len === 'function' ? dt : window.jQuery(node).DataTable();
                targetApi.page.len(Number.isFinite(limit) ? limit : 10).draw();
            };
            lengthEl.addEventListener('change', lengthHandler);
            handlers.push({ element: lengthEl, event: 'change', callback: lengthHandler });
        }

        if (searchEl) {
            const debouncedSearch = this._debounce((val) => {
                const targetApi = typeof dt.search === 'function' ? dt : window.jQuery(node).DataTable();
                targetApi.search(val).draw();
            }, 400);
            const searchHandler = () => debouncedSearch(searchEl.value);
            searchEl.addEventListener('input', searchHandler);
            handlers.push({ element: searchEl, event: 'input', callback: searchHandler });
        }

        this._bindCheckboxInteractions(node, dt, config);
    }

    _bindCheckboxInteractions(node, dt, config) {
        const wrapper = node.closest('.dataTables_wrapper') || (typeof dt.table === 'function' ? dt.table().container() : null);
        const container = node.closest('.table-container') || document;
        const dropdown = container.querySelector(config.actionDropdown);
        const master = container.querySelector(config.masterCheckbox);

        if (!wrapper || !dropdown) return;

        if (!tableMetadataRegistry.has(node)) {
            tableMetadataRegistry.set(node, { checkedCount: 0 });
        }

        const updateMasterCheckboxState = () => {
            const totalChildren = wrapper.querySelectorAll('.datatable-checkbox-children').length;
            const checkedChildren = wrapper.querySelectorAll('.datatable-checkbox-children:checked').length;
            
            const meta = tableMetadataRegistry.get(node);
            meta.checkedCount = checkedChildren;

            if (master) {
                master.checked = checkedChildren === totalChildren && totalChildren > 0;
                master.indeterminate = checkedChildren > 0 && checkedChildren < totalChildren;
            }

            dropdown.classList.toggle('d-none', checkedChildren === 0);
        };

        window.jQuery(wrapper).off('click.dtSelector').on('click.dtSelector', '.datatable-checkbox-children', () => {
            updateMasterCheckboxState();
        });

        if (typeof dt.on === 'function') {
            dt.off('draw.dtSelector').on('draw.dtSelector', () => {
                updateMasterCheckboxState();
            });
        }

        if (master) {
            const masterHandler = (e) => {
                const shouldCheckAll = master.indeterminate ? true : e.target.checked;
                const matches = wrapper.querySelectorAll('.datatable-checkbox-children');
                
                matches.forEach(el => { el.checked = shouldCheckAll; });
                
                master.indeterminate = false;
                master.checked = shouldCheckAll;

                const meta = tableMetadataRegistry.get(node);
                meta.checkedCount = shouldCheckAll ? matches.length : 0;
                
                dropdown.classList.toggle('d-none', meta.checkedCount === 0);
            };
            master.addEventListener('click', masterHandler);
            
            if (!this._boundHandlers.has(node)) this._boundHandlers.set(node, []);
            this._boundHandlers.get(node).push({ element: master, event: 'click', callback: masterHandler });
        }
    }

    resetSelectionState(config) {
        const dropdown = document.querySelector(config.actionDropdown || '.action-dropdown');
        const master = document.querySelector(config.masterCheckbox || '#datatable-checkbox');

        if (dropdown) dropdown.classList.add('d-none');
        if (master) {
            master.checked = false;
            master.indeterminate = false;
        }

        document.querySelectorAll('.datatable-checkbox-children').forEach(el => {
            el.checked = false;
        });
    }

    _bindSubControls(searchSel, lengthSel, tableRef) {
        const dt = DataTableOrchestrator.getAPI(tableRef);
        if (!dt) return;

        const debouncedSearch = this._debounce((val) => dt.search(val).draw(), 400);

        window.jQuery(document)
            .off('change.dtSubControls', lengthSel)
            .on('change.dtSubControls', lengthSel, function() {
                const val = Number.parseInt(this.value, 10);
                dt.page.len(Number.isFinite(val) ? val : 25).draw();
            })
            .off('input.dtSubControls', searchSel)
            .on('input.dtSubControls', searchSel, function() {
                debouncedSearch(this.value);
            });
    }

    _bindRowClickHandlers(tableNode, dt, callback) {
        const tbody = tableNode.tBodies?.[0];
        if (!tbody) return;

        const ns = `.dtRowClick_${tableNode.id || 'orchestrated'}`;
        window.jQuery(tbody).off(`click${ns}`).on(`click${ns}`, 'td:nth-child(n+2)', function() {
            const tr = this.closest('tr');
            if (tr) {
                const rowData = dt.row(tr).data();
                if (rowData) callback(rowData);
            }
        });
    }

    _bindExportControls(dt, tableNode) {
        const ns = `.export_${tableNode.id || 'orchestrated'}`;
        
        window.jQuery(document)
            .off(`click${ns}`)
            .on(`click${ns}`, '.export-csv', (e) => { e.preventDefault(); dt.button('.buttons-csv').trigger(); })
            .on(`click${ns}`, '.export-pdf', (e) => { e.preventDefault(); dt.button('.buttons-pdf').trigger(); })
            .on(`click${ns}`, '.export-excel', (e) => { e.preventDefault(); dt.button('.buttons-excel').trigger(); })
            .on(`click${ns}`, '.export-print', (e) => { e.preventDefault(); dt.button('.buttons-print').trigger(); });
    }

    _buildExportConfig() {
        const titleStrategy = () => `${document.title} Export ${new Date().toISOString().split('T')[0]}`;
        const opts = { columns: ':visible' };

        return [
            { extend: 'csvHtml5', title: titleStrategy, exportOptions: opts, className: 'd-none buttons-csv' },
            { extend: 'pdfHtml5', title: titleStrategy, exportOptions: opts, className: 'd-none buttons-pdf', orientation: 'landscape', pageSize: 'A4' },
            { extend: 'excelHtml5', title: titleStrategy, exportOptions: opts, className: 'd-none buttons-excel' },
            { extend: 'print', title: titleStrategy, exportOptions: opts, className: 'd-none buttons-print' }
        ];
    }

    _configureGlobalAdjustmentListeners() {
        if (globalListenersConfigured) return;
        globalListenersConfigured = true;

        const triggerAdjustments = () => {
            window.jQuery('.dataTable').each(function() {
                if (window.jQuery.fn.DataTable.isDataTable(this)) {
                    window.jQuery(this).DataTable().columns.adjust();
                }
            });
        };

        ['shown.bs.tab', 'shown.bs.modal', 'shown.bs.collapse'].forEach(ev => {
            document.addEventListener(ev, triggerAdjustments, { passive: true });
        });

        if (window.ResizeObserver) {
            let timeoutToken;
            globalResizeObserver = new ResizeObserver(() => {
                clearTimeout(timeoutToken);
                timeoutToken = setTimeout(triggerAdjustments, 120);
            });
        }
    }

    _debounce(fn, delay) {
        let t;
        return function(...args) {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), delay);
        };
    }
}