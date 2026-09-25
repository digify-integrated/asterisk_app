import { FormEnvironmentManager } from './formEnvironmentManager.js';
import { ButtonStateManager } from '../util/buttonManager.js';
import { Toast } from './notifications.js';

export class SaveFilterManager {
    constructor(options = {}) {
        this.filterManager = options.filterManager;

        const ctx = FormEnvironmentManager.getPageContext() || {};
        this.pageKey = options.pageKey || ctx.navigationMenuId || '';

        this.endpoints = {
            load: '/filter/fetch',
            save: '/filter/save',
            delete: '/filter/delete',
            setDefault: '/filter/set-default'
        };

        if (!this.filterManager || !this.filterManager.container) {
            console.warn('SavedFilterManager: A valid TableFilterManager instance is required.');
            return;
        }

        this.container = this.filterManager.container;
        this.initElements();
        this.initEvents();
        this.loadSavedFilters();
    }

    initElements() {
        this.listContainer = this.container.querySelector('#saved_filters_list');
        this.nameInput = this.container.querySelector('#save_filter_name');
        this.defaultCheckbox = this.container.querySelector('#set_as_default_checkbox');
        this.saveBtn = this.container.querySelector('#save_filter_btn');
    }

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
                const defaultBtn = e.target.closest('[data-set-default-id]'); // Add this

                if (loadBtn) {
                    e.preventDefault();
                    this.loadPreset(loadBtn.dataset.loadFilterId);
                }

                if (deleteBtn) {
                    e.preventDefault();
                    this.deletePreset(deleteBtn.dataset.deleteFilterId);
                }

                if (defaultBtn) {
                    e.preventDefault();
                    this.setDefaultPreset(defaultBtn.dataset.setDefaultId);
                }
            });
        }
    }

    async loadSavedFilters() {
        if (!this.listContainer) return;

        try {
            const payload = { navigation_menu_id: this.pageKey };
            const queryString = new URLSearchParams(payload).toString();
            const requestUrl = queryString ? `${this.endpoints.load}?${queryString}` : this.endpoints.load;

            const response = await fetch(requestUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': FormEnvironmentManager.getCsrfToken() || ''
                }
            });

            if (!response.ok) throw new Error('Failed to load saved filters.');

            const data = await response.json();
            this.renderDropdownList(data.data || data.filters || data);
        } catch (error) {
            console.error('SavedFilterManager Load Error:', error);
        }
    }

    renderDropdownList(filters) {
        const listContainer = document.getElementById('saved_filters_list');
        const countBadge = document.getElementById('saved_filters_count');
        
        if (!listContainer) return;

        if (countBadge) {
            countBadge.textContent = filters ? filters.length : 0;
        }

        if (!filters || filters.length === 0) {
            listContainer.innerHTML = `<span class="text-muted fs-8 px-2 py-3 d-block text-center">No saved filters yet</span>`;
            return;
        }

        const sortedFilters = [...filters].sort((a, b) => {
            if (a.is_default && !b.is_default) return -1;
            if (!a.is_default && b.is_default) return 1;
            const timeA = new Date(a.updated_at || a.created_at || 0).getTime();
            const timeB = new Date(b.updated_at || b.created_at || 0).getTime();
            return timeB - timeA;
        });

        let html = '';
        sortedFilters.forEach(preset => {
            const defaultAction = preset.is_default 
                ? `<span class="badge badge-light-success fs-8 px-2 py-1 me-1">Default</span>` 
                : `<button type="button" class="btn btn-icon btn-sm btn-light-success h-26px w-26px me-1" data-set-default-id="${preset.id}" title="Set as default filter"><i class="ki-outline ki-check fs-6"></i></button>`;

            html += `
                <div class="d-flex align-items-center justify-content-between px-2 py-1.5 rounded hover-bg-light transition-base mb-1 border-bottom border-dashed border-gray-100">
                    <a href="#" class="text-gray-800 text-hover-primary fw-medium text-truncate flex-grow-1 me-2 text-decoration-none py-1" data-load-filter-id="${preset.id}" title="${this.escapeHtml(preset.name)}">
                        <span class="text-truncate fs-7">${this.escapeHtml(preset.name)}</span>
                    </a>
                    <div class="d-flex align-items-center flex-shrink-0">
                        ${defaultAction}
                        <button type="button" class="btn btn-icon btn-sm btn-light-danger h-26px w-26px" data-delete-filter-id="${preset.id}" title="Delete preset">
                            <i class="ki-outline ki-trash fs-6"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        listContainer.innerHTML = html;
        
        if (typeof KTMenu !== 'undefined') {
            KTMenu.createInstances();
        }
    }

    async saveCurrentFilter() {
        if (!this.nameInput) return;

        const name = this.nameInput.value.trim();
        if (!name) {
            Toast.show('Please provide a name for your saved filter preset.', 'error');
            return;
        }

        const { raw: filterData } = this.filterManager.getFilterData();
        const isDefault = this.defaultCheckbox ? this.defaultCheckbox.checked : false;

        const payload = {
            navigation_menu_id: this.pageKey,
            name: name,
            filters: filterData,
            is_default: isDefault ? 1 : 0
        };

        try {
            ButtonStateManager.disable(this.saveBtn, { loadingText: 'Saving...' });

            const response = await fetch(this.endpoints.save, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': FormEnvironmentManager.getCsrfToken() || ''
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok) {
                const errorMsg = data.message || Object.values(data.errors || {})?.[0]?.[0] || 'Failed to save filter preset.';
                Toast.show(errorMsg, 'error');
                return;
            }

            Toast.show('Filter saved successfully.', 'success');
            this.nameInput.value = '';
            if (this.defaultCheckbox) this.defaultCheckbox.checked = false;
            await this.loadSavedFilters();

        } catch (error) {
            console.error('Save Filter Error:', error);
            Toast.show('An error occurred while saving the filter.', 'error');
        } finally {
            ButtonStateManager.enable(this.saveBtn);
        }
    }

    async loadPreset(id) {
        try {
            const payload = { id: id };
            const queryString = new URLSearchParams(payload).toString();
            const requestUrl = `${this.endpoints.load}?${queryString}`;

            const response = await fetch(requestUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': FormEnvironmentManager.getCsrfToken() || ''
                }
            });

            if (!response.ok) throw new Error('Failed to fetch filter preset.');

            const responseData = await response.json();
            const presetData = responseData.data || responseData;
            const filterPayload = typeof presetData.filters === 'string' ? JSON.parse(presetData.filters) : presetData.filters;
            
            this.applyFiltersToForm(filterPayload);
            this.filterManager.apply();
            Toast.show('Filter preset applied.', 'success');

        } catch (error) {
            console.error('Load Preset Error:', error);
            Toast.show('Failed to load filter preset.', 'error');
        }
    }

    async checkAndApplyDefaultFilter() {
        try {
            const payload = { navigation_menu_id: this.pageKey, default: 'true' };
            const queryString = new URLSearchParams(payload).toString();
            const requestUrl = `${this.endpoints.load}?${queryString}`;

            const response = await fetch(requestUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': FormEnvironmentManager.getCsrfToken() || ''
                }
            });
            if (!response.ok) return;

            const responseData = await response.json();
            const items = responseData.data || responseData;
            const presets = Array.isArray(items) ? items : [items];
            
            const defaultPresets = presets.filter(p => p.is_default);

            if (defaultPresets.length > 0) {
                defaultPresets.sort((a, b) => {
                    const timeA = new Date(a.updated_at || a.created_at || 0).getTime();
                    const timeB = new Date(b.updated_at || b.created_at || 0).getTime();
                    if (timeB !== timeA) return timeB - timeA;
                    return (b.id || 0) - (a.id || 0);
                });

                const defaultPreset = defaultPresets[0];
                if (defaultPreset && defaultPreset.filters) {
                    const filterPayload = typeof defaultPreset.filters === 'string' ? JSON.parse(defaultPreset.filters) : defaultPreset.filters;
                    
                    // Apply values to form
                    this.applyFiltersToForm(filterPayload);

                    // 👇 CRITICAL: This triggers the status bar render & table reload just like loadPreset()
                    if (typeof this.filterManager.apply === 'function') {
                        this.filterManager.apply();
                    }
                }
            }
        } catch (error) {
            // Silently fail if no default exists
        }
    }

    applyFiltersToForm(filters = {}) {
        if (!this.filterManager.form) return;
        const $form =$(this.filterManager.form);

        this.filterManager.reset();

        // Recombine split date range keys (_from / _to) back into single range strings
        const processedFilters = { ...filters };
        const rangeKeys = Object.keys(processedFilters).filter(k => k.endsWith('_from'));
        
        rangeKeys.forEach(fromKey => {
            const baseKey = fromKey.replace('_from', '');
            const toKey = `${baseKey}_to`;
            if (processedFilters[toKey]) {
                processedFilters[baseKey] = `${processedFilters[fromKey]} to ${processedFilters[toKey]}`;
                delete processedFilters[fromKey];
                delete processedFilters[toKey];
            }
        });

        Object.entries(processedFilters).forEach(([key, value]) => {
            const safeKey = key.replace(/([\[\]])/g, '\\$1');
            const $elements =$form.find(`[name="${safeKey}"], [name="${safeKey}[]"]`);
            
            if ($elements.length === 0) return;

            const el = $elements[0];

            // 1. Checkboxes & Radios
            if (el.type === 'checkbox' || el.type === 'radio') {
                const values = Array.isArray(value) ? value.map(String) : [String(value)];
                $elements.each((_, checkbox) => {
                    if (values.includes(String(checkbox.value))) {
                        $(checkbox).prop('checked', true).trigger('change');
                    }
                });
            } 
            // 2. Multi-Select & Select2 Arrays
            else if (el.tagName === 'SELECT' && el.multiple) {
                const values = Array.isArray(value) ? value.map(String) : [String(value)];
                $elements.val(values).trigger('change.select2').trigger('change');
            } 
            // 3. Sliders (noUiSlider support)
            else if (el.classList.contains('noUi-target') || el.noUiSlider) {
                if (typeof el.noUiSlider?.set === 'function') {
                    el.noUiSlider.set(Array.isArray(value) ? value : [value]);
                }
            } 
            // 4. Standard Inputs (Text, Number, Range, Color Picker) & Single Selects / Datepickers
            else {
                const $el =$(el);
                const valToSet = Array.isArray(value) ? value[0] : value;
                
                $el.val(valToSet).trigger('input').trigger('change');

                // Single Select / Select2
                if (el.tagName === 'SELECT') {
                    $el.trigger('change.select2');
                }

                // Flatpickr / Daterangepicker integration
                if (el._flatpickr) {
                    el._flatpickr.setDate(valToSet, true);
                } 
                // Bootstrap Datepicker fallback
                else if (window.jQuery && $el.data('datepicker')) {$el.datepicker('setDate', valToSet);
                }
            }
        });
    }

    async deletePreset(id) {
        let confirmed = true;

        if (window.Swal) {
            const result = await window.Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to delete this saved filter preset.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary m-1',
                },
                buttonsStyling: false,
            });
            confirmed = result.isConfirmed;
        } else {
            confirmed = confirm('Are you sure you want to delete this saved filter preset?');
        }

        if (!confirmed) return;

        try {
            const payload = new URLSearchParams();
            payload.append('id', id);
            payload.append('_method', 'DELETE');

            const response = await fetch(this.endpoints.delete, {
                method: 'POST', 
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': FormEnvironmentManager.getCsrfToken() || ''
                },
                body: payload
            });

            if (!response.ok) throw new Error('Failed to delete preset.');

            Toast.show('Filter preset deleted successfully.', 'success');
            await this.loadSavedFilters();
        } catch (error) {
            console.error('Delete Preset Error:', error);
            Toast.show('Failed to delete filter preset.', 'error');
        }
    }

    async setDefaultPreset(id) {
        try {
            const payload = new URLSearchParams();
            payload.append('id', id);

            const response = await fetch(this.endpoints.setDefault, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': FormEnvironmentManager.getCsrfToken() || ''
                },
                body: payload
            });

            if (!response.ok) throw new Error('Failed to update default filter.');

            Toast.show('Default filter updated successfully.', 'success');
            await this.loadSavedFilters();
        } catch (error) {
            console.error('Set Default Error:', error);
            Toast.show('Failed to set default filter.', 'error');
        }
    }

    escapeHtml(str) {
        if (typeof str !== 'string') return str;
        return str.replace(/[&<>"']/g, (m) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        })[m]);
    }
}