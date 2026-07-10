'use strict';

import { FormEnvironmentManager } from './formEnvironmentManager.js';
import { errorHandler } from './errorHandler.js';
import { Toast } from './notifications.js';

/**
 * Tracks inflight requests by unique URL signatures to block racing states.
 * @type {Map<string, AbortController>}
 */
const inflightRequestsRegistry = new Map();

export class DetailFetcher {
    /**
     * Executes an optimized, secure detail-fetching request with race mitigation.
     * @param {Object} params
     * @param {string} params.url - Endpoint target
     * @param {Object} [params.otherData={}] - Supplementary key/value parameters to bind
     * @param {string} [params.detailIdKey='detailId'] - Parameter name mapping for target record ID
     * @param {string|number|null} [params.detailIdValue=null] - Overriding target record ID string
     * @param {Function} [params.onSuccess] - Standard success execution lifecycle hook
     * @param {Function|null} [params.onNotExist=null] - Model failure lifecycle override callback
     * @param {Function|null} [params.onFailureMessage=null] - Soft logic error custom handler callback
     * @param {boolean} [params.cancelInflight=true] - If true, drops existing requests to this endpoint
     */
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

        // Cancel inflight requests to the same endpoint to mitigate race conditions
        if (cancelInflight && inflightRequestsRegistry.has(url)) {
            inflightRequestsRegistry.get(url).abort();
            inflightRequestsRegistry.delete(url);
        }

        const controller = new AbortController();
        if (cancelInflight) inflightRequestsRegistry.set(url, controller);

        try {
            const context = FormEnvironmentManager.getPageContext() || {};
            
            // Defensively create a null-prototype object to eliminate Prototype Pollution threats
            const payload = Object.create(null);
            
            // Build out base payload mapping parameters securely
            payload[String(detailIdKey)] = detailIdValue ?? context.detailId ?? '';
            payload.appId = context.appId ?? '';
            payload.navigationMenuId = context.navigationMenuId ?? '';

            // Securely clean, flatten, and assign auxiliary custom data components
            if (otherData && typeof otherData === 'object') {
                for (const [key, val] of Object.entries(otherData)) {
                    if (key === '__proto__' || key === 'constructor') continue; // Prototype protection lock
                    
                    if (val !== null && typeof val === 'object') {
                        payload[key] = JSON.stringify(val); // Clean deep structures into standard strings
                    } else if (val !== undefined) {
                        payload[key] = String(val);
                    }
                }
            }

            // Convert normalized flat payload dictionary to query text
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
            // Verify data.errors directly while letting data.message fall through ONLY if data.success is false
            if (data?.errors || (data?.message && data.success === false)) {
                if (typeof onFailureMessage === 'function') {
                    onFailureMessage(data);
                } else {
                    Toast.show(data.message || 'Request failed to evaluate.', 'error');
                }
                return data;
            }

            // Case 3: Perfect standard response
            await onSuccess(data);
            return data;

        } catch (error) {
            // Silence standard framework abort errors from bubbling out into console noise
            if (error.name === 'AbortError') {
                return { aborted: true };
            }

            errorHandler.handle(error, 'fetch_failed', `Details retrieval failed: ${error.message}`);
            throw error;
        } finally {
            // Clean up registry if this controller is still current
            if (cancelInflight && inflightRequestsRegistry.get(url) === controller) {
                inflightRequestsRegistry.delete(url);
            }
        }
    }
}