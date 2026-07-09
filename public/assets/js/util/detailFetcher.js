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
            
            // Build lean payload object
            const payload = {
                [detailIdKey]: detailIdValue ?? context.detailId ?? '',
                appId: context.appId ?? '',
                navigationMenuId: context.navigationMenuId ?? '',
                ...otherData
            };

            // Convert payload object into a query string and append to the URL
            const queryString = new URLSearchParams(payload).toString();
            const requestUrl = queryString ? `${url}?${queryString}` : url;

            const response = await fetch(requestUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': FormEnvironmentManager.getCsrfToken()
                }
            });

            if (!response.ok) throw new Error(`HTTP Error Status: ${response.status}`);

            const data = await response.json();

            // Case 1: Model completely missing on server context
            if (data?.notExist) {
                if (typeof onNotExist === 'function') {
                    onNotExist(data);
                } else {
                    Toast.show(data.message || 'Record not found.', 'warning');
                    if (data.redirect_link) window.location.replace(data.redirect_link);
                }
                return data;
            }

            // Case 2: Validation or explicit business logic failure messages
            if (data?.errors || data?.message) {
                if (typeof onFailureMessage === 'function') {
                    onFailureMessage(data);
                } else {
                    Toast.show(data.message || 'Request failed.', 'error');
                }
                return data;
            }

            // Case 3: Perfect standard response (Fallback for pure resources/data objects)
            await onSuccess(data);
            return data;

        } catch (error) {
            errorHandler.handle(error, 'fetch_failed', `Details retrieval failed: ${error.message}`);
            throw error;
        }
    }
}