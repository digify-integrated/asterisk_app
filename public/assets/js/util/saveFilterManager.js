import { FormEnvironmentManager } from './formEnvironmentManager.js';

/**
 * SavedFilterManager
 * Manages fetching, saving, applying, and deleting user saved filter presets
 * using FormEnvironmentManager for automatic page identification.
 */
export class SavedFilterManager {
    /**
     * @param {Object} options
     * @param {TableFilterManager} options.filterManager - Instance of TableFilterManager
     * @param {string} [options.pageKey] - Optional override for navigation menu ID
     * @param {Object} options.endpoints - API route endpoints { index, store, destroy }
     */
    constructor(options = {}) {
        this.filterManager = options.filterManager;
        this.endpoints = options.endpoints || {};

        // Automatically fetch page context via FormEnvironmentManager
        const ctx = FormEnvironmentManager.getPageContext() || {};
        this.pageKey = options.pageKey || ctx.navigationMenuId || '';

        if (!this.filterManager || !this.filterManager.container) {
            console.warn('SavedFilterManager: A valid TableFilterManager instance is required.');
            return;
        }

        if (!this.pageKey) {
            console.warn('SavedFilterManager: navigationMenuId could not be resolved from FormEnvironmentManager.');
        }

        this.container = this.filterManager.container;
        this.initElements();
        this.initEvents();
        this.loadSavedFilters();
        this.checkAndApplyDefaultFilter();
    }

    /**
     * Cache references to elements inside the filter panel dropdown
     */
    initElements() {
        this.listContainer = this.container.querySelector('#saved_filters_list');
        this.nameInput = this.container.querySelector('#save_filter_name');
        this.defaultCheckbox = this.container.querySelector('#set_as_default_checkbox');
        this.saveBtn = this.container.querySelector('#save_filter_btn');
    }

    /**
     * Bind click and change events
     */
    initEvents() {
        if (this.saveBtn) {
            this.saveBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.saveCurrentFilter();
            });
        }

        if (this.listContainer) {
            this.listContainer.addEventListener('click', (e) => {
                const loadBtn = e.target.closest('[data-load-filter-id]');
                const deleteBtn = e.target.closest('[data-delete-filter-id]');

                if (loadBtn) {
                    e.preventDefault();
                    this.loadPreset(loadBtn.dataset.loadFilterId);
                }

                if (deleteBtn) {
                    e.preventDefault();
                    this.deletePreset(deleteBtn.dataset.deleteFilterId);
                }
            });
        }
    }

    /**
     * Fetch all saved filters for this navigation menu ID
     */
    async loadSavedFilters() {
        if (!this.endpoints.index || !this.listContainer) return;

        try {
            const url = `${this.endpoints.index}?navigationMenuId=${encodeURIComponent(this.pageKey)}`;
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Failed to load saved filters.');

            const data = await response.json();
            this.renderDropdownList(data.filters || data);
        } catch (error) {
            console.error('SavedFilterManager Error:', error);
        }
    }

    /**
     * Render items inside the Metronic dropdown list
     */
    renderDropdownList(filters) {
        if (!this.listContainer) return;

        if (!filters || filters.length === 0) {
            this.listContainer.innerHTML = `<span class="text-muted fs-8 px-2">No saved filters yet</span>`;
            return;
        }

        let html = '';
        filters.forEach(preset => {
            const defaultBadge = preset.is_default ? `<span class="badge badge-light-success fs-8 ms-auto">Default</span>` : '';
            html += `
                <div class="menu-item px-2 mb-1 d-flex align-items-center justify-content-between group">
                    <a href="#" class="menu-link px-2 py-1 flex-grow-1 text-truncate" data-load-filter-id="${preset.id}" title="${this.escapeHtml(preset.name)}">
                        <span class="fs-8 text-gray-800 fw-medium">${this.escapeHtml(preset.name)}</span>
                        ${defaultBadge}
                    </a>
                    <button type="button" class="btn btn-icon btn-sm btn-active-color-danger ms-1 p-0 h-20px w-20px" data-delete-filter-id="${preset.id}" title="Delete preset">
                        <i class="ki-outline ki-trash fs-7"></i>
                    </button>
                </div>
            `;
        });

        this.listContainer.innerHTML = html;
        
        if (typeof KTMenu !== 'undefined') {
            KTMenu.createInstances();
        }
    }

    /**
     * Save the current form filter state as a new preset
     */
    async saveCurrentFilter() {
        if (!this.endpoints.store || !this.nameInput) return;

        const name = this.nameInput.value.trim();
        if (!name) {
            alert('Please provide a name for your saved filter preset.');
            return;
        }

        const { raw: filterData } = this.filterManager.getFilterData();
        const isDefault = this.defaultCheckbox ? this.defaultCheckbox.checked : false;

        const payload = {
            navigationMenuId: this.pageKey,
            name: name,
            filters: filterData,
            is_default: isDefault ? 1 : 0
        };

        try {
            this.saveBtn.setAttribute('data-kt-indicator', 'on');
            this.saveBtn.disabled = true;

            const response = await fetch(this.endpoints.store, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) throw new Error('Failed to save filter preset.');

            this.nameInput.value = '';
            if (this.defaultCheckbox) this.defaultCheckbox.checked = false;
            await this.loadSavedFilters();

        } catch (error) {
            console.error('Save Filter Error:', error);
            alert('An error occurred while saving the filter.');
        } finally {
            this.saveBtn.removeAttribute('data-kt-indicator');
            this.saveBtn.disabled = false;
        }
    }

    /**
     * Load a specific preset and apply it to inputs & table
     */
    async loadPreset(id) {
        try {
            const url = `${this.endpoints.index}/${id}`;
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Failed to fetch filter preset.');

            const data = await response.json();
            this.applyFiltersToForm(data.filters || data);
            this.filterManager.apply();

        } catch (error) {
            console.error('Load Preset Error:', error);
        }
    }

    /**
     * Automatically load default filter preset on page initialization
     */
    async checkAndApplyDefaultFilter() {
        if (!this.endpoints.index) return;
        try {
            const url = `${this.endpoints.index}?navigationMenuId=${encodeURIComponent(this.pageKey)}&default=true`;
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!response.ok) return;

            const data = await response.json();
            const defaultPreset = Array.isArray(data) ? data.find(p => p.is_default) : data;

            if (defaultPreset && defaultPreset.filters) {
                this.applyFiltersToForm(defaultPreset.filters);
                this.filterManager.apply();
            }
        } catch (error) {
            // Silently fail if no default exists
        }
    }

    /**
     * Hydrate form inputs from stored filter values
     */
    applyFiltersToForm(filters = {}) {
        if (!this.filterManager.form) return;
        const $form =$(this.filterManager.form);

        this.filterManager.reset();

        Object.entries(filters).forEach(([key, value]) => {
            const $elements =$form.find(`[name="${key}"], [name="${key}[]"]`);
            if ($elements.length === 0) return;

            const el = $elements[0];

            if (el.type === 'checkbox' || el.type === 'radio') {
                const values = Array.isArray(value) ? value.map(String) : [String(value)];
                $elements.each((_, checkbox) => {
                    if (values.includes(String(checkbox.value))) {
                        $(checkbox).prop('checked', true);
                    }
                });
            } else if (el.tagName === 'SELECT' && el.multiple) {
                const values = Array.isArray(value) ? value : [value];
                $el.val(values).trigger('change.select2').trigger('change');
            } else {
                $el.val(value);
                if (el.tagName === 'SELECT') {
                    $el.trigger('change.select2').trigger('change');
                }
                if (el._flatpickr) {
                    el._flatpickr.setDate(value, true);
                }
            }
        });
    }

    /**
     * Delete a saved filter preset
     */
    async deletePreset(id) {
        if (!confirm('Are you sure you want to delete this saved filter preset?')) return;

        try {
            const url = `${this.endpoints.index}/${id}`;
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            if (!response.ok) throw new Error('Failed to delete preset.');

            await this.loadSavedFilters();
        } catch (error) {
            console.error('Delete Preset Error:', error);
        }
    }

    escapeHtml(str) {
        if (typeof str !== 'string') return str;
        return str.replace(/[&<>"']/g, (m) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        })[m]);
    }
}