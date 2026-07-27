'use strict';

import { FormEnvironmentManager } from './formEnvironmentManager.js';
import { errorHandler } from './errorHandler.js';
import { Toast } from './notifications.js';

const inflightRequestsRegistry = new Map();

export class DetailFetcher {
    /**
     * Resolves inputs/elements to actual DOM element nodes.
     * @private
     */
    static _resolveElements(targets) {
        if (!targets) return [];
        const items = Array.isArray(targets) ? targets : [targets];
        
        return items.flatMap(item => {
            if (typeof item === 'string') {
                return Array.from(document.querySelectorAll(item));
            }
            if (item instanceof HTMLElement) {
                return [item];
            }
            return [];
        });
    }

    /**
     * Toggles UI elements between loading and active states.
     * @private
     */
    static _toggleUIState(formEl, hideElements, isLoading) {
        // 1. Toggle Form Inputs (disable/enable)
        if (formEl) {
            const inputs = formEl.querySelectorAll('input, select, textarea, button');
            
            inputs.forEach(el => {
                el.disabled = isLoading;

                // Handle Select2 instances if present
                if (el.tagName === 'SELECT' && (el.classList.contains('select 2-hidden-accessible') || el.dataset.select2Id)) {
                    if (window.jQuery && jQuery(el).data('select2')) {
                        jQuery(el).prop('disabled', isLoading).trigger('change.select2');
                    }
                }
            });
        }

        // 2. Toggle Visibility for specified target elements
        hideElements.forEach(el => {
            if (isLoading) {
                el.classList.add('d-none');
            } else {
                el.classList.remove('d-none');
            }
        });
    }

    static async fetch({
        url,
        otherData = {},
        detailIdKey = 'detailId',
        detailIdValue = null,
        formSelector = null,   // Single selector or HTMLElement for the form
        hideOnFetch = null,    // Single selector/element OR Array of selectors/elements to hide
        onSuccess = () => {},
        onNotExist = null,
        onFailureMessage = null,
        cancelInflight = true
    } = {}) {
        if (!url) {
            throw new Error('[DetailFetcher Fail]: Endpoint URL string context cannot be omitted.');
        }

        // Resolve Form Element
        const formEl = typeof formSelector === 'string' 
            ? document.querySelector(formSelector) 
            : formSelector;

        // Resolve elements that need to be hidden during fetch
        let elementsToHide = this._resolveElements(hideOnFetch);

        // Auto-detect submit button linked to the form if hideOnFetch was omitted
        if (elementsToHide.length === 0 && formEl?.id) {
            const defaultSubmitBtn = document.querySelector(`button[type="submit"][form="${formEl.id}"], #${formEl.id} button[type="submit"]`);
            if (defaultSubmitBtn) elementsToHide.push(defaultSubmitBtn);
        }

        if (cancelInflight && inflightRequestsRegistry.has(url)) {
            inflightRequestsRegistry.get(url).abort();
            inflightRequestsRegistry.delete(url);
        }

        const controller = new AbortController();
        if (cancelInflight) inflightRequestsRegistry.set(url, controller);

        // Lock Form & Hide UI Targets
        this._toggleUIState(formEl, elementsToHide, true);

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

            // Restore Form Inputs & Unhide UI Targets
            this._toggleUIState(formEl, elementsToHide, false);
        }
    }
}