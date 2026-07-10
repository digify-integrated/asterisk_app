'use strict';

export class FormEnvironmentManager {
    // Whitelist of hidden input names that should NEVER be wiped during form resets
    static PROTECTED_HIDDEN_FIELDS = ['_token', '_method', 'import_table_name'];

    /**
     * Resets a form completely, cleans third-party UI bindings, clears validation tracking styles,
     * and clears targeted hidden fields while strictly protecting structural framework states.
     * @param {string|HTMLFormElement} formTarget - Element ID string or the Form DOM Element node itself
     */
    static resetForm(formTarget) {
        const form = typeof formTarget === 'string' ? document.getElementById(formTarget) : formTarget;
        if (!(form instanceof HTMLFormElement)) return;

        // 1. Trigger native browser form reset mechanism
        form.reset();

        // 2. Clear application-wide validation error styling states and messages
        form.querySelectorAll('.is-invalid, .has-error, .invalid-feedback').forEach(el => {
            el.classList.remove('is-invalid', 'has-error');
            // If validation plugin appends custom text nodes, optionally clear or hide them
            if (el.classList.contains('invalid-feedback')) {
                el.style.display = 'none'; 
            }
        });

        // 3. Clear hidden inputs unless explicitly declared inside the protection list array
        form.querySelectorAll('input[type="hidden"]').forEach(input => {
            const fieldName = input.getAttribute('name');
            if (!this.PROTECTED_HIDDEN_FIELDS.includes(fieldName)) {
                input.value = '';
            }
        });

        // 4. Reset select dropdown states and force clean plugin-to-native sync updates
        form.querySelectorAll('select, .form-select').forEach(select => {
            const $select = window.jQuery ? window.jQuery(select) : null;
            
            if ($select && $select.data('select2')) {
                // Wipe Select2 instance cache value
                $select.val(null).trigger('change.select2');
            } else {
                select.value = '';
            }

            // CRITICAL: Force a native DOM event bubble dispatch so standard listeners 
            // registered outside of jQuery architectures can catch the form clear event.
            select.dispatchEvent(new Event('change', { bubbles: true, cancelable: true }));
        });
    }

    /**
     * Securely fetches the active runtime CSRF token hash from the document header.
     * @param {string} [metaName='csrf-token'] 
     * @param {Document} [doc=document] 
     * @returns {string} Token hash layout signature or empty fallback string
     */
    static getCsrfToken(metaName = 'csrf-token', doc = document) {
        return doc.querySelector(`meta[name="${metaName}"]`)?.getAttribute('content') ?? '';
    }

    /**
     * Resolves context state values directly from the application container shell.
     * Returns an immutable frozen metadata layout dictionary map.
     * @returns {Readonly<{appId: string|null, databaseTable: string|null, navigationMenuId: string|null, detailId: string|null}>} 
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