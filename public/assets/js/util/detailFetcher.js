'use strict';

import { FormEnvironmentManager } from './formEnvironmentManager.js';
import { errorHandler } from './errorHandler.js';
import { Toast } from './notifications.js';

const inflightRequestsRegistry = new Map();

export class DetailFetcher {
    static async fetch({
        url,
        otherData = {},
        detailIdKey = 'detailId',
        detailIdValue = null,
        onSuccess = () => {},
        onNotExist = null,
        onFailureMessage = null,
        cancelInflight = true
    } = {}) {
        if (!url) {
            throw new Error('[DetailFetcher Fail]: Endpoint URL string context cannot be omitted.');
        }

        if (cancelInflight && inflightRequestsRegistry.has(url)) {
            inflightRequestsRegistry.get(url).abort();
            inflightRequestsRegistry.delete(url);
        }

        const controller = new AbortController();
        if (cancelInflight) inflightRequestsRegistry.set(url, controller);

        try {
            const context = FormEnvironmentManager.getPageContext() || {};
            
            const payload = Object.create(null);
        
            payload[String(detailIdKey)] = detailIdValue ?? context.detailId ?? '';
            payload.appId = context.appId ?? '';
            payload.navigationMenuId = context.navigationMenuId ?? '';

            if (otherData && typeof otherData === 'object') {
                for (const [key, val] of Object.entries(otherData)) {
                    if (key === '__proto__' || key === 'constructor') continue;
                    
                    if (val !== null && typeof val === 'object') {
                        payload[key] = JSON.stringify(val);
                    } else if (val !== undefined) {
                        payload[key] = String(val);
                    }
                }
            }

            const queryString = new URLSearchParams(payload).toString();
            const requestUrl = queryString ? `${url}?${queryString}` : url;

            const response = await fetch(requestUrl, {
                method: 'GET',
                signal: controller.signal,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': FormEnvironmentManager.getCsrfToken() || ''
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP Error Status: ${response.status}`);
            }

            const data = await response.json();

            if (data?.notExist) {
                if (typeof onNotExist === 'function') {
                    onNotExist(data);
                } else {
                    Toast.show(data.message || 'Record not found.', 'warning');
                    if (data.redirect_link) window.location.replace(data.redirect_link);
                }
                return data;
            }

            if (data?.errors || (data?.message && data.success === false)) {
                if (typeof onFailureMessage === 'function') {
                    onFailureMessage(data);
                } else {
                    Toast.show(data.message || 'Request failed to evaluate.', 'error');
                }
                return data;
            }

            await onSuccess(data);
            return data;

        } catch (error) {
            if (error.name === 'AbortError') {
                return { aborted: true };
            }

            errorHandler.handle(error, 'fetch_failed', `Details retrieval failed: ${error.message}`);
            throw error;
        } finally {
            if (cancelInflight && inflightRequestsRegistry.get(url) === controller) {
                inflightRequestsRegistry.delete(url);
            }
        }
    }
}