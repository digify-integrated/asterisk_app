'use strict';

import { FormEnvironmentManager } from './formEnvironmentManager.js';
import { Toast } from './notifications.js';
import { errorHandler } from './errorHandler.js';

// Global memory registry to prevent redundant listener stacking across shared triggers
const activeTriggers = new Set();

/**
 * Initializes a declarative confirmation action bound to a SweetAlert2 modal pipeline.
 * Safely guards against duplicate event listeners on identical triggers.
 * * @param {Object} options
 * @param {string} options.trigger - CSS Selector to watch for click events
 * @param {string} options.url - Endpoint destination url target
 * @param {Object} [options.payload={}] - Explicit extra payload parameter keys map
 * @param {string} [options.method='POST'] - Virtual HTTP verb modification type (POST|DELETE|PUT|PATCH)
 * @param {string} options.swalTitle - SweetAlert2 prompt title text
 * @param {string} options.swalText - SweetAlert2 descriptive message body text
 * @param {string} [options.swalIcon='warning'] - Severity layout type context indicator
 * @param {string} options.confirmButtonText - Confirm action click button label text
 * @param {string} [options.confirmButtonClass='primary'] - Core theme styling class suffix append (e.g. 'danger')
 */
export const initConfirmAction = ({
    trigger,
    url,
    payload = {},
    method = 'POST', 
    swalTitle,
    swalText,
    swalIcon = 'warning',
    confirmButtonText,
    confirmButtonClass = 'primary',
}) => {
    // Structural Guard: If this exact trigger has already been initialized globally, skip out
    if (activeTriggers.has(trigger)) return;
    activeTriggers.add(trigger);

    document.addEventListener('click', async (e) => {
        const element = e.target.closest(trigger);
        if (!element) return; 

        e.preventDefault();

        // Fallback protection validation if SweetAlert2 script delivery failed
        if (!window.Swal) {
            console.warn('[ConfirmAction] SweetAlert2 library missing. Falling back to native confirmation window.');
            if (!confirm(`${swalTitle}\n\n${swalText}`)) return;
        } else {
            const result = await window.Swal.fire({
                title: swalTitle,
                text: swalText,
                icon: swalIcon,
                showCancelButton: true,
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: `btn btn-${confirmButtonClass}`,
                    cancelButton: 'btn btn-secondary m-1',
                },
                buttonsStyling: false,
            });

            if (!result.isConfirmed) return;
        }

        try {
            // Leverage internal utility managers instead of untracked window variables
            const csrf = FormEnvironmentManager.getCsrfToken();
            const ctx = FormEnvironmentManager.getPageContext();

            const formData = new URLSearchParams();
            
            // Populate framework context metrics tracking values
            formData.append('detailId', ctx.detailId ?? '');
            formData.append('appId', ctx.appId ?? '');
            formData.append('navigationMenuId', ctx.navigationMenuId ?? '');

            // Process user-configured overrides payloads
            Object.entries(payload).forEach(([key, value]) => {
                if (typeof value === 'function') {
                    formData.append(key, value(element) ?? '');
                } else {
                    formData.append(key, value ?? '');
                }
            });

            // Inherit dataset properties bound directly on the clicked DOM Element node
            if (element.dataset) {
                Object.entries(element.dataset).forEach(([key, value]) => {
                    // Convert camelCase dataset key strings to standard snake_case field mappings
                    const sanitizedKey = key.replace(/([A-Z])/g, '_$1').toLowerCase();
                    if (!formData.has(sanitizedKey)) {
                        formData.append(sanitizedKey, value ?? '');
                    }
                });
            }

            // Inject REST method overriding values for backend routing parsers
            const upperMethod = method.toUpperCase();
            if (['DELETE', 'PUT', 'PATCH'].includes(upperMethod)) {
                formData.append('_method', upperMethod);
            }

            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'Accept': 'application/json',
                    ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
                },
            });

            if (!response.ok) throw response;

            const data = await response.json();

            if (data.success) {
                if (data.redirect_link) {
                    // Queue up message execution cleanly inside sessionStorage to run safely on redirect landing load
                    Toast.queueSuccess(data.message || 'Action executed successfully.');
                    window.location.replace(data.redirect_link);
                } else {
                    Toast.success(data.message || 'Action completed.');
                }
            } else {
                Toast.error(data.message || 'An application processing error occurred.');
            }
        } catch (error) {
            errorHandler.handle(error, 'action_failed', 'Request dispatch transmission failed.');
        }
    });
};