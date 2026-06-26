'use strict';

export class FormEnvironmentManager {
    /**
     * Resets a form completely, handles Select2 UI bindings, clears validation states, 
     * and clears hidden elements while strictly preserving critical framework tokens.
     * @param {string|HTMLFormElement} formTarget - Element ID string or the Form DOM Element itself
     */
    static resetForm(formTarget) {
        const form = typeof formTarget === 'string' ? document.getElementById(formTarget) : formTarget;
        if (!(form instanceof HTMLFormElement)) return;

        // 1. Trigger native form reset mechanism
        form.reset();

        // 2. Clear out framework validation error styling states
        const invalidElements = form.querySelectorAll('.is-invalid, .has-error');
        invalidElements.forEach(el => el.classList.remove('is-invalid', 'has-error'));

        // 3. Clear hidden inputs but selectively protect core security/routing structures
        const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
        hiddenInputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name !== '_token' && name !== 'import_table_name') {
                input.value = '';
            }
        });

        // 4. Force synchronization across Select2 components or standard selects
        const selects = form.querySelectorAll('select, .form-select');
        selects.forEach(select => {
            const $select = window.jQuery ? window.jQuery(select) : null;
            
            if ($select && $select.data('select2')) {
                // If Select2 exists, use its native plugin API to clear selections cleanly
                $select.val(null).trigger('change.select2');
            } else {
                // Standalone select element processing
                select.value = '';
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }

    /**
     * Securely fetches the global CSRF token string from the HTML head meta layout templates.
     * @param {string} [metaName='csrf-token'] 
     * @param {Document} [doc=document] 
     * @returns {string} Token hash or empty fallback string
     */
    static getCsrfToken(metaName = 'csrf-token', doc = document) {
        return doc.querySelector(`meta[name="${metaName}"]`)?.getAttribute('content') ?? '';
    }

    /**
     * Resolves layout attributes directly from the main application shell framework wrapper.
     * Returns an immutable frozen metadata profile.
     * @returns {Object} 
     */
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