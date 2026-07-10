'use strict';

import { ComponentRegistry } from './componentRegistry.js';
import { ButtonStateManager } from './buttonManager.js';
import { errorHandler } from './errorHandler.js';
import { Toast } from './notifications.js';
import { FormEnvironmentManager } from './formEnvironmentManager.js';

export class TableExportManager {
    /**
     * @param {string} tableName - Target data scheme mapping identifier
     * @param {Object} [options={}] - Custom configuration mapping selectors
     */
    constructor(tableName, options = {}) {
        this.tableName = tableName;
        this.config = Object.assign({
            selectSelector: '#table_column',
            triggerSelector: '#export-data',
            submitSelector: '#submit-export',
            checkboxSelector: '.datatable-checkbox-children:checked',
            formatRadioSelector: 'input[name="export_to"]:checked',
        }, options);

        this.selectedColumnsOrder = [];
        this.dualListInitialized = false;
        this.isProcessingExport = false; // Concurrency Execution Guard

        this._listAbortController = null;
        this._eventAbortController = new AbortController();

        this._initListeners();
    }

    /** Unbinds structural document events to clean up memory footprints */
    destroy() {
        this._eventAbortController.abort();
        if (this._listAbortController) this._listAbortController.abort();
    }

    /**
     * Set up listener attachments
     * @private
     */
    _initListeners() {
        const { signal } = this._eventAbortController;

        document.addEventListener('click', async (e) => {
            // Context Router 1: Load export field schemas
            if (e.target.closest(this.config.triggerSelector)) {
                e.preventDefault();
                await this._loadExportColumns();
            }
            
            // Context Router 2: Confirm download action execution
            if (e.target.closest(this.config.submitSelector)) {
                e.preventDefault();
                await this._executeExportDownload();
            }
        }, { signal });
    }

    /**
     * Pulls active columns lists via JSON streams
     * @private
     */
    async _loadExportColumns() {
        const select = document.querySelector(this.config.selectSelector);
        if (!select) return;

        if (this._listAbortController) this._listAbortController.abort();
        this._listAbortController = new AbortController();

        try {
            const data = await this._jsonFetch('/export/export-list', {
                signal: this._listAbortController.signal,
                bodyObj: { table_name: this.tableName }
            });

            // Rebuild selection DOM options efficiently via standard DocumentFragments
            select.options.length = 0;
            const frag = document.createDocumentFragment();
            data.forEach(opt => {
                frag.appendChild(new Option(opt.text, opt.id));
            });
            select.appendChild(frag);

            this._initializeDualListBoxOnce();
            this.selectedColumnsOrder = []; // Clean preceding state mapping trackers
            this._refreshDualListBox();

        } catch (err) {
            if (err?.name === 'AbortError') return;
            errorHandler.handle(err, 'export_load_failed', 'Failed to load export column manifest.');
        } finally {
            this._listAbortController = null;
        }
    }

    /**
     * Prepares parameters and processes binary payload returns
     * @private
     */
    async _executeExportDownload() {
        // Concurrency Guard Check: Kill parallel clicks before hitting long task execution threads
        if (this.isProcessingExport) return;

        const exportTo = document.querySelector(this.config.formatRadioSelector)?.value;
        const exportIds = Array.from(
            document.querySelectorAll(this.config.checkboxSelector),
            el => el.value
        );

        if (!exportIds.length) return Toast.warning('Choose the data rows you want to export.');
        if (!this.selectedColumnsOrder.length) return Toast.warning('Choose the explicit target columns you want to export.');
        if (!exportTo) return Toast.warning('Choose an export format.');

        // Establish locking threshold state parameters
        this.isProcessingExport = true;
        ButtonStateManager.disable(this.config.submitSelector);

        try {
            const { blob, headerFilename } = await this._blobFetch('/export/export', {
                bodyObj: {
                    export_id: exportIds,
                    export_to: exportTo,
                    table_column: this.selectedColumnsOrder,
                    table_name: this.tableName,
                }
            });

            const fallbackName = `${this.tableName}-export.${exportTo}`;
            this._triggerBlobDownload(blob, headerFilename || fallbackName);

        } catch (err) {
            errorHandler.handle(err, 'export_execution_failed', 'Binary stream generation failed.');
        } finally {
            // Release locking thresholds 
            this.isProcessingExport = false;
            ButtonStateManager.enable(this.config.submitSelector);
        }
    }

    /**
     * Attaches third party bootstrap dual list interface wrappers
     * @private
     */
    _initializeDualListBoxOnce() {
        if (this.dualListInitialized || !window.jQuery) return;

        const $el = window.jQuery(this.config.selectSelector);
        if (!$el.length) return;

        $el.bootstrapDualListbox({
            nonSelectedListLabel: 'Available Fields',
            selectedListLabel: 'Selected Fields',
            preserveSelectionOnMove: 'moved',
            moveOnSelect: false,
            helperSelectNamePostfix: false,
            sortByInputOrder: true,
        });

        $el.off('change.export').on('change.export', () => {
            this.selectedColumnsOrder = $el.find('option:selected')
                .map((_, opt) => opt.value)
                .get();
        });

        ComponentRegistry.initializeDualListBoxIcon();
        this.dualListInitialized = true;
    }

    /**
     * Synchronization update hook for dual list layouts
     * @private
     */
    _refreshDualListBox() {
        if (!window.jQuery) return;
        const $el = window.jQuery(this.config.selectSelector);
        if ($el.length) {
            $el.bootstrapDualListbox('refresh', true);
        }
        ComponentRegistry.initializeDualListBoxIcon();
    }

    /**
     * Standardized payload AJAX configuration wrappers
     * @private
     */
    async _jsonFetch(url, { signal, bodyObj } = {}) {
        const res = await fetch(url, {
            method: 'POST',
            signal,
            headers: { 
                'Content-Type': 'application/json', 
                'Accept': 'application/json',
                'X-CSRF-TOKEN': FormEnvironmentManager.getCsrfToken() // Automatically secure request headers
            },
            body: JSON.stringify(bodyObj),
            credentials: 'same-origin'
        });
        if (!res.ok) throw res;
        return res.json();
    }

    /**
     * Standardized binary blob data pipeline wrappers
     * @private
     */
    async _blobFetch(url, { bodyObj } = {}) {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': FormEnvironmentManager.getCsrfToken()
            },
            body: JSON.stringify(bodyObj),
            credentials: 'same-origin'
        });
        if (!res.ok) throw res;

        const cd = res.headers.get('Content-Disposition');
        const headerFilename = this._parseFilename(cd);
        const blob = await res.blob();

        return { blob, headerFilename };
    }

    /**
     * Parses filenames from content-disposition header schemas cleanly
     * @private
     */
    _parseFilename(contentDisposition) {
        if (!contentDisposition) return '';

        const starMatch = contentDisposition.match(/filename\*\s*=\s*([^;]+)/i);
        if (starMatch?.[1]) {
            const parts = starMatch[1].trim().split("''");
            const encoded = parts.length === 2 ? parts[1] : parts[0];
            const cleaned = encoded.replace(/^["']|["']$/g, '');
            try { return decodeURIComponent(cleaned); } catch { return cleaned; }
        }

        const nameMatch = contentDisposition.match(/filename\s*=\s*([^;]+)/i);
        if (nameMatch?.[1]) {
            return nameMatch[1].trim().replace(/^["']|["']$/g, '');
        }
        return '';
    }

    /**
     * Injects a temporary download element channel to push binaries down to browser space
     * @private
     */
    _triggerBlobDownload(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = filename;
        
        document.body.appendChild(a);
        a.click();
        
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
}