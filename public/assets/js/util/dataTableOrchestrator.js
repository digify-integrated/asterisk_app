'use strict';

import { FormEnvironmentManager } from './formEnvironmentManager.js';
import { errorHandler } from './errorHandler.js';
import { Toast } from './notifications.js';
import { ComponentRegistry } from './componentRegistry.js';

// Central shared private cache to tracking underlying platform instances across components
const instanceRegistry = new WeakMap();
let globalListenersConfigured = false;
let globalResizeObserver = null;

export class DataTableOrchestrator {
    constructor() {
        this.checkedCountCache = 0;
        this._resizeTimeout = null;
    }

    /**
     * Resolves and extracts an underlying DataTables API target.
     * @param {string|HTMLElement} selectorOrNode 
     */
    static getAPI(selectorOrNode) {
        const node = typeof selectorOrNode === 'string' ? document.querySelector(selectorOrNode) : selectorOrNode;
        if (!node) return null;

        if (instanceRegistry.has(node) && window.jQuery?.fn?.DataTable?.isDataTable(node)) {
            return instanceRegistry.get(node);
        }
        
        if (!window.jQuery?.fn?.DataTable?.isDataTable(node)) return null;
        const dt = window.jQuery(node).DataTable();
        instanceRegistry.set(node, dt);
        return dt;
    }

    /**
     * Entry Initializer: Configures data streams and wires ecosystem plug-ins.
     */
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

        // Ensure proper lifecycle resets before running new operations
        this.destroy(tableNode);
        this.resetSelectionState(config);

        if (!config.url) {
            Toast.show(`Data feed URL context omitted for target: ${config.selector}`, 'error');
            return null;
        }

        const exportWrapper = document.querySelector('.table-export');
        const enableExport = !!exportWrapper || config.addons?.export;
        const self = this;

        // Initialize jQuery DataTables core
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
                dataSrc: config.serverSide ? 'data' : '',
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
                    // Interlocking module connection safely verified
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
            }
        });

        instanceRegistry.set(tableNode, dt);
        this._configureGlobalAdjustmentListeners();

        if (typeof config.onRowClick === 'function') {
            this._bindRowClickHandlers(tableNode, dt, config.onRowClick);
        }

        return dt;
    }

    /**
     * Forces immediate asynchronous layout resets across records.
     */
    reload(selectorOrNode) {
        const dt = DataTableOrchestrator.getAPI(selectorOrNode);
        if (dt) dt.ajax.reload(null, false);
    }

    /**
     * Purges runtime allocations and event handles safely.
     */
    destroy(selectorOrNode) {
        const node = typeof selectorOrNode === 'string' ? document.querySelector(selectorOrNode) : selectorOrNode;
        if (!node) return;

        const dt = DataTableOrchestrator.getAPI(node);
        if (!dt) return;

        const tbody = node.tBodies?.[0];
        if (tbody) {
            window.jQuery(tbody).off(`.dtRowClick_${node.id || 'orchestrated'}`);
        }

        // Clean custom contextual export selectors out of DOM memory profiles
        window.jQuery(document).off(`.export_${node.id || 'orchestrated'}`);

        dt.clear();
        dt.destroy();
        instanceRegistry.delete(node);
    }

    bindControls(selectorOrNode, config) {
        const dt = DataTableOrchestrator.getAPI(selectorOrNode);
        if (!dt) return;

        const lengthEl = document.getElementById('datatable-length');
        const searchEl = document.getElementById('datatable-search');

        if (lengthEl) {
            lengthEl.onchange = () => {
                const limit = Number.parseInt(lengthEl.value, 10);
                dt.page.len(Number.isFinite(limit) ? limit : 10).draw(false);
            };
        }

        if (searchEl) {
            const debouncedSearch = this._debounce((val) => dt.search(val).draw(false), 400);
            searchEl.oninput = () => debouncedSearch(searchEl.value);
        }

        this._bindCheckboxInteractions(dt, config);
    }

    resetSelectionState(config) {
        this.checkedCountCache = 0;
        const dropdown = document.querySelector(config.actionDropdown || '.action-dropdown');
        const master = document.querySelector(config.masterCheckbox || '#datatable-checkbox');

        if (dropdown) dropdown.classList.add('d-none');
        if (master) master.checked = false;

        document.querySelectorAll('.datatable-checkbox-children').forEach(el => {
            el.checked = false;
        });
    }

    _bindCheckboxInteractions(dt, config) {
        const wrapper = dt.table().container();
        const dropdown = document.querySelector(config.actionDropdown || '.action-dropdown');
        const master = document.querySelector(config.masterCheckbox || '#datatable-checkbox');

        if (!wrapper || !dropdown) return;

        // Avoid global window delegation. Target interactions strictly within this specific dataset container.
        window.jQuery(wrapper).off('click.dtSelector').on('click.dtSelector', '.datatable-checkbox-children', (e) => {
            this.checkedCountCache += e.target.checked ? 1 : -1;
            if (this.checkedCountCache < 0) this.checkedCountCache = 0;
            dropdown.classList.toggle('d-none', this.checkedCountCache === 0);
        });

        if (master) {
            master.onclick = (e) => {
                const isChecked = e.target.checked;
                const matches = wrapper.querySelectorAll('.datatable-checkbox-children');
                
                this.checkedCountCache = isChecked ? matches.length : 0;
                matches.forEach(el => { el.checked = isChecked; });
                dropdown.classList.toggle('d-none', this.checkedCountCache === 0);
            };
        }
    }

    _bindSubControls(searchSel, lengthSel, tableRef) {
        const dt = DataTableOrchestrator.getAPI(tableRef);
        if (!dt) return;

        const debouncedSearch = this._debounce((val) => dt.search(val).draw(false), 400);

        window.jQuery(document)
            .off('change.dtSubControls', lengthSel)
            .on('change.dtSubControls', lengthSel, function() {
                const val = Number.parseInt(this.value, 10);
                dt.page.len(Number.isFinite(val) ? val : 25).draw(false);
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
            
            // Watch structural grid wrappers dynamically without creating redundant loop instances
            document.querySelectorAll('.dataTables_wrapper').forEach(el => globalResizeObserver.observe(el));
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