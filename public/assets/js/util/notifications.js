'use strict';

const VALID_TYPES = new Set(['success', 'info', 'warning', 'error']);
const STORAGE_KEY = 'app_notification_payload';

const DEFAULT_CONFIG = {
    closeButton: true,
    progressBar: true,
    preventDuplicates: false,
    positionClass: 'toastr-top-right',
    timeOut: 2000,
};

/**
 * Safely fetches and initializes the global Toastr instance.
 * @returns {Object|null}
 */
const getToastrInstance = () => {
    const instance = window.toastr || window.jQuery?.fn?.toastr;
    if (instance && !instance._configured) {
        instance.options = { ...DEFAULT_CONFIG };
        instance._configured = true;
    }
    return instance;
};

export const Toast = {
    /**
     * Display an immediate notification.
     * @param {string} message - The alert text.
     * @param {'success'|'info'|'warning'|'error'} type - The severity level.
     * @param {number} [timeOut] - Optional custom duration in milliseconds.
     */
    show(message, type = 'error', timeOut) {
        const t = getToastrInstance();
        if (!t) {
            console.warn('[Toast] Toastr.js missing. Falling back to console.');
            console.log(`[${type.toUpperCase()}]: ${message}`);
            return;
        }

        const validatedType = VALID_TYPES.has(type) ? type : 'info';
        const overrides = typeof timeOut === 'number' ? { timeOut } : {};

        t[validatedType](String(message ?? ''), '', overrides);
    },

    /**
     * Queue a notification in sessionStorage to be displayed after the next page load.
     */
    queue(message, type = 'info', timeOut) {
        try {
            const payload = { message, type, timeOut };
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
        } catch (error) {
            console.error('[Toast] Failed to queue notification:', error);
        }
    },

    /**
     * Check for and render any queued notifications. Call this on app boot.
     */
    check() {
        try {
            const raw = sessionStorage.getItem(STORAGE_KEY);
            if (!raw) return;

            sessionStorage.removeItem(STORAGE_KEY);
            const { message, type, timeOut } = JSON.parse(raw) ?? {};
            
            if (message) {
                this.show(message, type, timeOut);
            }
        } catch (error) {
            console.error('[Toast] Failed to parse queued notification:', error);
        }
    }
};