'use strict';

export class FormEnvironmentManager {
    static PROTECTED_HIDDEN_FIELDS = ['_token', '_method', 'import_table_name'];

    static resetForm(formTarget) {
        const form = typeof formTarget === 'string' ? document.getElementById(formTarget) : formTarget;
        if (!(form instanceof HTMLFormElement)) return;

        form.reset();

        form.querySelectorAll('.is-invalid, .has-error, .invalid-feedback').forEach(el => {
            el.classList.remove('is-invalid', 'has-error');
            if (el.classList.contains('invalid-feedback')) {
                el.style.display = 'none'; 
            }
        });

        form.querySelectorAll('input[type="hidden"]').forEach(input => {
            const fieldName = input.getAttribute('name');
            if (!this.PROTECTED_HIDDEN_FIELDS.includes(fieldName)) {
                input.value = '';
            }
        });

        form.querySelectorAll('select, .form-select').forEach(select => {
            const $select = window.jQuery ? window.jQuery(select) : null;
            
            if ($select && $select.data('select2')) {
                $select.val(null).trigger('change.select2');
            } else {
                select.value = '';
            }

            select.dispatchEvent(new Event('change', { bubbles: true, cancelable: true }));
        });
    }

    static getCsrfToken(metaName = 'csrf-token', doc = document) {
        return doc.querySelector(`meta[name="${metaName}"]`)?.getAttribute('content') ?? '';
    }

    static getPageContext() {
        const appShell = document.getElementById('kt_app_body');
        
        return Object.freeze({
            appId: appShell?.dataset?.appId ?? null,
            databaseTable: appShell?.dataset?.table ?? null,
            navigationMenuId: appShell?.dataset?.navigationMenuId ?? null,
            detailId: appShell?.dataset?.detailId ?? null,
        });
    }
}