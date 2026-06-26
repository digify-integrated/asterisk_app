'use strict';

import { FormEnvironmentManager } from './formEnvironmentManager.js';
import { errorHandler } from './errorHandler.js';
import { Toast } from './notifications.js';

export class DetailFetcher {
    /**
     * Executes an optimized detail-fetching request.
     */
    static async fetch({
        url,
        otherData = {},
        detailIdKey = 'detailId',
        detailIdValue = null,
        onSuccess = () => {},
        onNotExist = null,
        onFailureMessage = null
    } = {}) {
        try {
            const context = FormEnvironmentManager.getPageContext();
            
            // Build lean payload object mapping directly to JSON (much faster processing)
            const payload = {
                [detailIdKey]: detailIdValue ?? context.detailId ?? '',
                appId: context.appId ?? '',
                navigationMenuId: context.navigationMenuId ?? '',
                ...otherData
            };

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': FormEnvironmentManager.getCsrfToken()
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) throw new Error(`HTTP Error Status: ${response.status}`);

            const data = await response.json();

            // Case 1: Perfect standard response
            if (data?.success) {
                await onSuccess(data);
                return data;
            }

            // Case 2: Model completely missing on server context
            if (data?.notExist) {
                if (typeof onNotExist === 'function') {
                    onNotExist(data);
                } else {
                    Toast.show(data.message || 'Record not found.', 'warning');
                    if (data.redirect_link) window.location.replace(data.redirect_link);
                }
                return data;
            }

            // Case 3: Processed Validation or business logic failures
            if (typeof onFailureMessage === 'function') {
                onFailureMessage(data);
            } else {
                Toast.show(data?.message || 'Request failed.', 'error');
            }

            return data;

        } catch (error) {
            errorHandler.handle(error, 'fetch_failed', `Details retrieval failed: ${error.message}`);
            throw error;
        }
    }
}