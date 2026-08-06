/**
 * Dynamic DataTables Filter Manager
 * Handles multi-modal setups, granular per-value status tags, chip dismissals,
 * minimal UI status bar, and DataTables reloads.
 */
export class TableFilterManager {
    /**
     * @param {Object} options
     * @param {string} options.modalId - ID of the filter modal
     * @param {Object} options.orchestrator - DataTables orchestrator instance
     * @param {string} options.tableSelector - DataTables table selector string
     * @param {string} [options.statusContainerSelector] - Selector for status bar placement
     * @param {Function} [options.onApply] - Optional callback triggered after applying filters
     * @Function} [options.onReset] - Optional callback triggered after resetting filters
     */
    constructor(options = {}) {
        this.modalId = options.modalId;
        this.orchestrator = options.orchestrator;
        this.tableSelector = options.tableSelector || '#table';
        this.statusContainerSelector = options.statusContainerSelector || null;
        this.onApply = options.onApply || null;
        this.onReset = options.onReset || null;

        // DOM References scoped to this specific modal instance
        this.modal = document.getElementById(this.modalId);
        if (!this.modal) {
            console.warn(`TableFilterManager: Modal with ID "#${this.modalId}" was not found.`);
            return;
        }

        this.form = this.modal.querySelector('form') || this.modal.querySelector('.modal-body');
        
        // Find badge associated with this modal
        this.badge = document.querySelector(`[data-filter-badge="${this.modalId}"]`) || 
                     document.querySelector(`[data-bs-target="#${this.modalId}"] .filter-count-badge`);

        // Setup container for Active Filter Status bar
        this.statusContainer = this.initStatusContainer();

        this.currentFilters = {};
        this.initEvents();
    }

    /**
     * Initialize or locate the status container bar with clean inline layout
     */
    initStatusContainer() {
        if (this.statusContainerSelector) {
            return document.querySelector(this.statusContainerSelector);
        }

        const tableEl = document.querySelector(this.tableSelector);
        if (!tableEl) return null;

        const containerId = `${this.modalId}_status_bar`;
        let statusEl = document.getElementById(containerId);

        if (!statusEl) {
            statusEl = document.createElement('div');
            statusEl.id = containerId;
            // Clean, borderless horizontal flow
            statusEl.className = 'table-filter-status-bar d-none mb-3 py-1 d-flex align-items-center justify-content-between flex-wrap gap-2';
            
            const parent = tableEl.closest('.card') || tableEl.parentElement;
            parent.parentNode.insertBefore(statusEl, parent);
        }

        return statusEl;
    }

    /**
     * Bind click events inside the modal and status bar
     */
    initEvents() {
        // Modal buttons
        this.modal.addEventListener('click', (e) => {
            if (e.target.closest('#apply-filter') || e.target.closest('.btn-apply-filter')) {
                e.preventDefault();
                this.apply();
            } else if (e.target.closest('#reset-filter') || e.target.closest('.btn-reset-filter')) {
                e.preventDefault();
                this.reset();
            }
        });

        // Delegate individual tag removal & clear all button on the status bar
        if (this.statusContainer) {
            this.statusContainer.addEventListener('click', (e) => {
                const removeTagBtn = e.target.closest('[data-remove-filter]');
                if (removeTagBtn) {
                    const fieldName = removeTagBtn.dataset.removeFilter;
                    const valueToRemove = removeTagBtn.dataset.filterValue;
                    this.removeGranularFilter(fieldName, valueToRemove);
                    return;
                }

                if (e.target.closest('.btn-clear-all-filters')) {
                    e.preventDefault();
                    this.reset();
                }
            });
        }
    }

    /**
     * Extract active filter inputs and construct granular chip metadata
     */
    getFilterData() {
        if (!this.form) return { raw: {}, formatted: [] };

        const filters = {};
        const formatted = [];
        const elements = this.form.querySelectorAll('input, select, textarea');

        elements.forEach((element) => {
            const name = element.getAttribute('name');
            if (!name || element.disabled) return;

            const cleanName = name.replace('[]', '');
            const type = element.type;
            const label = this.getFieldLabel(element);

            // 1. Checkboxes
            if (type === 'checkbox') {
                if (element.checked) {
                    filters[cleanName] = filters[cleanName] || [];
                    filters[cleanName].push(element.value);

                    const textVal = this.getOptionText(element) || element.value;
                    formatted.push({
                        key: cleanName,
                        label: label,
                        displayValue: textVal,
                        rawValue: element.value
                    });
                }
                return;
            }

            // 2. Radios
            if (type === 'radio') {
                if (element.checked) {
                    filters[cleanName] = element.value;
                    const textVal = this.getOptionText(element) || element.value;
                    formatted.push({
                        key: cleanName,
                        label: label,
                        displayValue: textVal,
                        rawValue: element.value
                    });
                }
                return;
            }

            // 3. Multi-Select & Standard Selects
            if (element.tagName === 'SELECT') {
                const selectedOptions = Array.from(element.selectedOptions)
                    .filter(opt => opt.value !== '');

                if (selectedOptions.length > 0) {
                    if (element.multiple) {
                        filters[cleanName] = selectedOptions.map(opt => opt.value);
                        selectedOptions.forEach(opt => {
                            formatted.push({
                                key: cleanName,
                                label: label,
                                displayValue: opt.text.trim(),
                                rawValue: opt.value
                            });
                        });
                    } else {
                        filters[cleanName] = selectedOptions[0].value;
                        formatted.push({
                            key: cleanName,
                            label: label,
                            displayValue: selectedOptions[0].text.trim(),
                            rawValue: selectedOptions[0].value
                        });
                    }
                }
                return;
            }

            // 4. Flatpickr / Date Ranges & Standard Text Inputs
            const value = element.value ? element.value.trim() : '';
            if (value !== '') {
                if (element.dataset.range === 'true' && value.includes(' to ')) {
                    const [startDate, endDate] = value.split(' to ');
                    filters[`${cleanName}_from`] = startDate.trim();
                    filters[`${cleanName}_to`] = endDate.trim();
                } else {
                    filters[cleanName] = value;
                }

                formatted.push({
                    key: cleanName,
                    label: label,
                    displayValue: value,
                    rawValue: value
                });
            }
        });

        return { raw: filters, formatted: formatted };
    }

    /**
     * Resolve human-readable field label
     */
    getFieldLabel(element) {
        if (element.id) {
            const labelEl = this.form.querySelector(`label[for="${element.id}"]`);
            if (labelEl) return labelEl.textContent.replace('*', '').trim();
        }

        const parentLabel = element.closest('.form-group, .mb-3, .mb-5')?.querySelector('label');
        if (parentLabel) return parentLabel.textContent.replace('*', '').trim();

        const placeholder = element.getAttribute('placeholder');
        if (placeholder) return placeholder.trim();

        const name = element.getAttribute('name').replace('[]', '');
        return name.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    /**
     * Resolve text label of a checkbox or radio option
     */
    getOptionText(element) {
        if (element.id) {
            const labelEl = this.form.querySelector(`label[for="${element.id}"]`);
            if (labelEl) return labelEl.textContent.trim();
        }
        return element.parentElement ? element.parentElement.textContent.trim() : null;
    }

    /**
     * Apply active filters to the DataTable & render status bar chips
     */
    apply() {
        const { raw, formatted } = this.getFilterData();
        this.currentFilters = raw;
        const activeCount = formatted.length;

        // 1. Update active filter badge counter
        this.updateBadge(activeCount);

        // 2. Render Active Filters Status Bar
        this.renderStatusBar(formatted);

        // 3. Close modal
        const modalInstance = bootstrap.Modal.getInstance(this.modal);
        if (modalInstance) {
            modalInstance.hide();
        }

        // 4. Reload DataTables passing extracted filter payload
        if (this.orchestrator && typeof this.orchestrator.reload === 'function') {
            this.orchestrator.reload(this.tableSelector, this.currentFilters);
        }

        if (typeof this.onApply === 'function') {
            this.onApply(this.currentFilters);
        }
    }

    /**
     * Render linear, minimal filter chips with clear micro-interactions
     */
    renderStatusBar(formattedFilters = []) {
        if (!this.statusContainer) return;

        if (formattedFilters.length === 0) {
            this.statusContainer.classList.add('d-none');
            this.statusContainer.innerHTML = '';
            return;
        }

        // Clean subtle filter label & container
        let html = `
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="text-muted opacity-75 fw-medium me-1 style-label" style="font-size: 0.75rem; letter-spacing: 0.03em;">FILTERED BY</span>
                <div class="d-flex flex-wrap align-items-center gap-2">
        `;

        // Modern, sleek pill design
        formattedFilters.forEach(item => {
            html += `
                <div class="filter-chip-item d-inline-flex align-items-center border border-subtle rounded-2 px-2 py-1" style="font-size: 0.8125rem;">
                    <span class="text-muted fw-normal me-1">${this.escapeHtml(item.label)}:</span>
                    <span class="fw-semibold text-body me-1">${this.escapeHtml(item.displayValue)}</span>
                    <button type="button" 
                            class="btn-chip-remove border-0 bg-transparent p-0 ms-1 d-inline-flex align-items-center justify-content-center opacity-50 opacity-100-hover text-body cursor-pointer"
                            data-remove-filter="${item.key}" 
                            data-filter-value="${this.escapeHtml(item.rawValue)}"
                            aria-label="Remove filter ${this.escapeHtml(item.displayValue)}"
                            title="Remove filter"
                            style="width: 16px; height: 16px; border-radius: 50%; transition: all 0.15s ease;">
                        <svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <line x1="2" y1="2" x2="10" y2="10"></line>
                            <line x1="10" y1="2" x2="2" y2="10"></line>
                        </svg>
                    </button>
                </div>
            `;
        });

        html += `</div></div>`;

        // Subtle ghost link for Clear All
        html += `
            <button type="button" class="btn-clear-all-filters btn btn-link p-0 text-muted link-danger fw-medium ms-auto" style="font-size: 0.75rem;">
                CLEAR ALL
            </button>
        `;

        this.statusContainer.innerHTML = html;
        this.statusContainer.classList.remove('d-none');
    }

    /**
     * Remove a single target value from multi-selects/checkboxes dynamically
     */
    removeGranularFilter(fieldName, valueToRemove) {
        if (!this.form) return;

        const $form = $(this.form);
        const elements = $form.find(`[name="${fieldName}"], [name="${fieldName}[]"]`);

        elements.each((_, el) => {
            const $el = $(el);

            if (el.type === 'checkbox' || el.type === 'radio') {
                if (String(el.value) === String(valueToRemove)) {
                    $el.prop('checked', false);
                }
            } 
            else if (el.tagName === 'SELECT' && el.multiple) {
                let currentValues = $el.val() || [];
                const updatedValues = currentValues.filter(val => String(val) !== String(valueToRemove));
                $el.val(updatedValues).trigger('change.select2').trigger('change');
            } 
            else {
                $el.val('');
                if (el.tagName === 'SELECT') $el.trigger('change.select2').trigger('change');
                if (el._flatpickr) el._flatpickr.clear();
            }
        });

        this.apply();
    }

    /**
     * Reset all inputs
     */
    reset() {
        if (!this.form) return;

        const $form = $(this.form);

        if (this.form.tagName === 'FORM') {
            this.form.reset();
        }

        $form.find('input[type="text"], input[type="number"], input[type="date"], input[type="search"], textarea').val('');
        $form.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);

        $form.find('select[data-control="select2"]').each((_, select) => {
            $(select).val(null).trigger('change.select2');
        });

        $form.find('.flatpickr-input').each((_, input) => {
            if (input._flatpickr) {
                input._flatpickr.clear();
            }
        });

        this.currentFilters = {};
        this.updateBadge(0);
        this.renderStatusBar([]);

        if (this.orchestrator && typeof this.orchestrator.reload === 'function') {
            this.orchestrator.reload(this.tableSelector, {});
        }

        if (typeof this.onReset === 'function') {
            this.onReset();
        }
    }

    /**
     * Update filter badge visibility and number
     */
    updateBadge(count) {
        if (!this.badge) return;

        if (count > 0) {
            this.badge.textContent = count;
            this.badge.classList.remove('d-none');
        } else {
            this.badge.classList.add('d-none');
            this.badge.textContent = '0';
        }
    }

    /**
     * Sanitize output HTML
     */
    escapeHtml(str) {
        if (typeof str !== 'string') return str;
        return str.replace(/[&<>"']/g, (m) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[m]);
    }

    /**
     * Export active filter data
     */
    exportFilters() {
        return {
            modalId: this.modalId,
            appliedCount: Object.keys(this.currentFilters).length,
            filters: { ...this.currentFilters }
        };
    }
}