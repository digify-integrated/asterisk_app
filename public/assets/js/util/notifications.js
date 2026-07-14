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

const getToastrInstance = () => {
    const t = window.toastr || window.jQuery?.fn?.toastr;
    if (!t) return null;

    if (!t._configured) {
        t.options = Object.assign({}, t.options, DEFAULT_CONFIG);
        t._configured = true;
    }
    return t;
};

export const Toast = {
    show(message, type = 'error', timeOut) {
        const t = getToastrInstance();
        const validatedType = VALID_TYPES.has(type) ? type : 'info';

        if (!t) {
            console.warn(`[Toast] Toastr.js library missing. Fallback executed.`);
            console.log(`[%c${validatedType.toUpperCase()}%c]: ${message}`, 'font-weight: bold; color: orange;', '');
            return;
        }

        const optionsOverride = typeof timeOut === 'number' ? { timeOut } : {};
        
        t[validatedType](String(message ?? ''), '', optionsOverride);
    },

    queue(message, type = 'info', timeOut) {
        try {
            const payload = { message, type, timeOut };
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
        } catch (error) {
            console.error('[Toast] Session storage allocation restricted:', error);
        }
    },

    check() {
        try {
            const raw = sessionStorage.getItem(STORAGE_KEY);
            if (!raw) return;

            sessionStorage.removeItem(STORAGE_KEY);
            
            const payload = JSON.parse(raw);
            if (payload && payload.message) {
                this.show(payload.message, payload.type, payload.timeOut);
            }
        } catch (error) {
            console.error('[Toast] Failed to parse queued pipeline notification state:', error);
        }
    },

    success(msg, timeOut) { this.show(msg, 'success', timeOut); },
    error(msg, timeOut)   { this.show(msg, 'error', timeOut); },
    info(msg, timeOut)    { this.show(msg, 'info', timeOut); },
    warning(msg, timeOut) { this.show(msg, 'warning', timeOut); },

    queueSuccess(msg, timeOut) { this.queue(msg, 'success', timeOut); },
    queueError(msg, timeOut)   { this.queue(msg, 'error', timeOut); }
};