'use strict';

import { errorHandler } from './errorHandler.js';

export class ComponentRegistry {
    static _buildGetUrl(baseUrl, data = {}) {
        const urlObj = new URL(baseUrl, window.location.origin);
        Object.entries(data).forEach(([key, val]) => {
            urlObj.searchParams.append(key, String(val));
        });
        return urlObj.toString();
    }

    static async generateDropdownOptions({ url, dropdownSelector, data = {}, validateOnChange = true }) {
        try {
            const targetUrl = this._buildGetUrl(url, data);
            const response = await fetch(targetUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
            });

            if (!response.ok) {
                throw new Error(`Failed to fetch dropdown data. HTTP status: ${response.status}`);
            }

            const result = await response.json();
            const $dropdown = window.jQuery(dropdownSelector);
            if (!$dropdown.length) return;

            if ($dropdown.hasClass('select2-hidden-accessible')) {
                $dropdown.select2('destroy');
            }

            $dropdown.empty();

            const $modalParent = $dropdown.closest('.modal');
            const $offcanvasParent = $dropdown.closest('.offcanvas');
            const $menuParent = $dropdown.closest('[data-kt-menu="true"]');

            let dropdownParent = window.jQuery(document.body);
            if ($modalParent.length) dropdownParent = $modalParent;
            else if ($offcanvasParent.length) dropdownParent = $offcanvasParent;
            else if ($menuParent.length) dropdownParent = $menuParent;

            $dropdown.select2({
                data: result,
                dropdownParent,
                width: '100%',
                escapeMarkup: markup => markup,
                templateResult: data => data.text,
                templateSelection: data => data.text
            })
            .on('select2:open', () => this._focusSelect2Search())
            .on('select2:unselect select2:clear', function () {
                const $this = window.jQuery(this);
                setTimeout(() => $this.select2('close'), 0);
            });

            window.jQuery(document)
                .off('mousedown.select2-remove-close')
                .on('mousedown.select2-remove-close', '.select2-selection__choice__remove', function (e) {
                    const $container = window.jQuery(this).closest('.select2');
                    const $select = $container.prevAll('select').first();

                    if ($select.length && $select.data('select2')) {
                        e.stopPropagation();
                        setTimeout(() => $select.select2('close'), 0);
                    }
                });

            if (validateOnChange) {
                $dropdown.on('change', function () {
                    if (typeof window.jQuery(this).valid === 'function') {
                        window.jQuery(this).valid();
                    }
                });
            }

        } catch (error) {
            errorHandler.handle(error, 'dropdown_fetch_failed');
        }
    }

    static _focusSelect2Search() {
        setTimeout(() => {
            const searchField = document.querySelector('.select2-container--open .select2-search__field');
            if (searchField) searchField.focus();
        }, 100);
    }

    static async generateDualListBox({ trigger, url, selectSelector, data = {} }) {
        document.addEventListener('click', async (e) => {
            const btn = e.target.closest(trigger);
            if (!btn) return;

            try {
                const targetUrl = this._buildGetUrl(url, data);
                const response = await fetch(targetUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                });

                if (!response.ok) throw new Error(`Dual List Box data request rejected: ${response.status}`);

                const result = await response.json();
                const select = document.getElementById(selectSelector);
                if (!select) return;

                const $select = window.jQuery(select);
                if (window.jQuery.data(select, 'plugin_bootstrapDualListbox')) {
                    $select.bootstrapDualListbox('destroy');
                }

                select.options.length = 0;
                result.forEach(opt => {
                    select.appendChild(new Option(opt.text, opt.id));
                });

                $select.bootstrapDualListbox({
                    nonSelectedListLabel: 'Available Columns',
                    selectedListLabel: 'Selected Columns',
                    preserveSelectionOnMove: 'moved',
                    moveOnSelect: false,
                    helperSelectNamePostfix: false,
                    infoText: 'Showing all {0}',
                    infoTextEmpty: 'Empty list',
                });

                $select.bootstrapDualListbox('refresh', true);
                this.initializeDualListBoxIcon();

            } catch (error) {
                errorHandler.handle(error, 'duallistbox_fetch_failed');
            }
        });
    }

    static initializeDualListBoxIcon() {
        window.jQuery('.moveall i').removeClass().addClass('ki-duotone ki-right');
        window.jQuery('.removeall i').removeClass().addClass('ki-duotone ki-left');
        window.jQuery('.move i').removeClass().addClass('ki-duotone ki-right');
        window.jQuery('.remove i').removeClass().addClass('ki-duotone ki-left');

        window.jQuery('.moveall, .removeall, .move, .remove')
            .removeClass('btn-default')
            .addClass('btn-primary');
    }

    static initializeTinyMCE(tinyMceId, disabled = 0) {
        if (typeof tinymce === 'undefined') return;

        let options = {
            selector: tinyMceId,
            height: "350",
            toolbar: [
                'styleselect fontselect fontsizeselect',
                'undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify',
                'bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists charmap | preview | code | table tabledelete'
            ],
            plugins: 'advlist autolink link image lists charmap preview table code',
            license_key: 'gpl'
        };

        if (typeof KTThemeMode !== 'undefined' && KTThemeMode.getMode() === "dark") {
            options["skin"] = "oxide-dark";
            options["content_css"] = "dark";
        }

        tinymce.init(options).then(() => {
            if (disabled && tinymce.activeEditor) {
                tinymce.activeEditor.mode.set('readonly');
            }
        });
    }

    static initializeDatePicker({ selector, enableTime = false, dateFormat = "M d, Y" }) {
        const elements = document.querySelectorAll(selector);
        if (!elements.length || typeof flatpickr === 'undefined') return;
        
        flatpickr(elements, { enableTime, dateFormat });
    }

    static initializeDateRangePicker(selector, options = {}) {
        if (typeof window.jQuery === 'undefined' || typeof moment === 'undefined') return;

        const config = {
            startDate: null,
            endDate: null,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            callback: null,
            ...options
        };

        const pickerOptions = {
            autoUpdateInput: false,
            ranges: config.ranges,
            locale: { cancelLabel: 'Clear' }
        };

        if (config.startDate) pickerOptions.startDate = config.startDate;
        if (config.endDate) pickerOptions.endDate = config.endDate;

        const $element = window.jQuery(selector);
        if (!$element.length) return;

        $element.daterangepicker(pickerOptions, (start, end) => {
            if (typeof config.callback === 'function') config.callback(start, end);
        });

        $element.on('apply.daterangepicker', function (ev, picker) {
            window.jQuery(this).val(`${picker.startDate.format('MM/DD/YYYY')} - ${picker.endDate.format('MM/DD/YYYY')}`);
        });

        $element.on('cancel.daterangepicker', function () {
            window.jQuery(this).val('');
        });

        if (config.startDate && config.endDate) {
            if (typeof config.callback === 'function') config.callback(config.startDate, config.endDate);
            $element.val(`${config.startDate.format('MM/DD/YYYY')} - ${config.endDate.format('MM/DD/YYYY')}`);
        } else {
            $element.val('');
        }
    }
}