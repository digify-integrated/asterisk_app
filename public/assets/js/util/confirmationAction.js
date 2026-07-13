'use strict';

import { FormEnvironmentManager } from './formEnvironmentManager.js';
import { Toast } from './notifications.js';
import { errorHandler } from './errorHandler.js';

const activeTriggers = new Set();

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
    onSuccess
}) => {
    if (activeTriggers.has(trigger)) return;
    activeTriggers.add(trigger);

    document.addEventListener('click', async (e) => {
        const element = e.target.closest(trigger);
        if (!element) return; 

        e.preventDefault();

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
            const csrf = FormEnvironmentManager.getCsrfToken();
            const ctx = FormEnvironmentManager.getPageContext();

            const formData = new URLSearchParams();
            formData.append('detailId', ctx.detailId ?? '');
            formData.append('appId', ctx.appId ?? '');
            formData.append('navigationMenuId', ctx.navigationMenuId ?? '');

            Object.entries(payload).forEach(([key, value]) => {
                if (typeof value === 'function') {
                    formData.append(key, value(element) ?? '');
                } else {
                    formData.append(key, value ?? '');
                }
            });

            if (element.dataset) {
                Object.entries(element.dataset).forEach(([key, value]) => {
                    const sanitizedKey = key.replace(/([A-Z])/g, '_$1').toLowerCase();
                    if (!formData.has(sanitizedKey)) {
                        formData.append(sanitizedKey, value ?? '');
                    }
                });
            }

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

            if (data.redirect_link) {
                Toast.queueSuccess(data.message || 'Action executed successfully.');
                window.location.replace(data.redirect_link);
            } else {
                Toast.success(data.message || 'Action completed.');
            }

        } catch (error) {
            errorHandler.handle(error, 'action_failed', 'Request dispatch transmission failed.');
        }
    });
};