'use strict';

import { FormEnvironmentManager } from './formEnvironmentManager.js';
import { Toast } from './notifications.js';
import { errorHandler } from './errorHandler.js';

const activeTriggers = new Set();

export const activateTriggers = ({
    trigger,
    url,
    payload = {},
    method = 'POST',
    onSuccess,
    onError
}) => {
    if (activeTriggers.has(trigger)) return;
    activeTriggers.add(trigger);

    // Listen to 'change' events instead of 'click' for checkboxes
    document.addEventListener('change', async (e) => {
        const element = e.target.closest(trigger);
        if (!element) return;

        // Briefly disable element during network request to prevent double-toggles
        element.disabled = true;

        try {
            const csrf = FormEnvironmentManager.getCsrfToken();
            const ctx = FormEnvironmentManager.getPageContext();

            const formData = new URLSearchParams();
            formData.append('detailId', ctx.detailId ?? '');
            formData.append('appId', ctx.appId ?? '');
            formData.append('navigationMenuId', ctx.navigationMenuId ?? '');

            // Automatically pass checkbox checked status if applicable
            if (element.type === 'checkbox' || element.type === 'radio') {
                formData.append('is_checked', element.checked ? 1 : 0);
            }

            // Append custom payload attributes
            Object.entries(payload).forEach(([key, value]) => {
                const val = typeof value === 'function' ? value(element) : value;
                formData.append(key, val ?? '');
            });

            // Append data-* attributes
            if (element.dataset) {
                Object.entries(element.dataset).forEach(([key, value]) => {
                    const sanitizedKey = key.replace(/([A-Z])/g, '_$1').toLowerCase();
                    if (!formData.has(sanitizedKey)) {
                        formData.append(sanitizedKey, value ?? '');
                    }
                });
            }

            // Handle HTTP Method spoofing
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

            if (typeof onSuccess === 'function') {
                onSuccess(data, element);
            }

            Toast.success(data.message || 'Updated successfully.');

        } catch (error) {
            // Revert checkbox state if the request failed
            if (element.type === 'checkbox') {
                element.checked = !element.checked;
            }

            if (typeof onError === 'function') {
                onError(error, element);
            } else {
                errorHandler.handle(error, 'action_failed', 'Failed to update record.');
            }
        } finally {
            element.disabled = false;
        }
    });
};