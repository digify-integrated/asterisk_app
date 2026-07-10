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
 * Safely fetches and extends the global Toastr library instance.
 * @returns {Object|null}
 */
const getToastrInstance = () => {
    // Check window space or jQuery extension registries natively
    const t = window.toastr || window.jQuery?.fn?.toastr;
    if (!t) return null;

    if (!t._configured) {
        // Explicitly extend instead of replacing the configuration reference object shell
        t.options = Object.assign({}, t.options, DEFAULT_CONFIG);
        t._configured = true;
    }
    return t;
};

export const Toast = {
    /**
     * Display an immediate alert notification toast.
     * @param {string} message - The text content to display.
     * @param {'success'|'info'|'warning'|'error'} type - The severity indicator level.
     * @param {number} [timeOut] - Optional dynamic duration limit override in milliseconds.
     */
    show(message, type = 'error', timeOut) {
        const t = getToastrInstance();
        const validatedType = VALID_TYPES.has(type) ? type : 'info';

        if (!t) {
            console.warn(`[Toast] Toastr.js library missing. Fallback executed.`);
            console.log(`[%c${validatedType.toUpperCase()}%c]: ${message}`, 'font-weight: bold; color: orange;', '');
            return;
        }

        const optionsOverride = typeof timeOut === 'number' ? { timeOut } : {};
        
        // Ensure string conversion safely to avoid layout rendering breakdowns
        t[validatedType](String(message ?? ''), '', optionsOverride);
    },

    /**
     * Queue an alert notification to fire instantly on the next page lifecycle initialization.
     * @param {string} message 
     * @param {'success'|'info'|'warning'|'error'} type 
     * @param {number} [timeOut] 
     */
    queue(message, type = 'info', timeOut) {
        try {
            const payload = { message, type, timeOut };
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
        } catch (error) {
            console.error('[Toast] Session storage allocation restricted:', error);
        }
    },

    /**
     * Parse, drain, and execute any pending stored notification messages inside the queue.
     * Call this inside your primary core layout layout shell bootstrap step.
     */
    check() {
        try {
            const raw = sessionStorage.getItem(STORAGE_KEY);
            if (!raw) return;

            // Always clear the storage tracking immediately before parsing to prevent 
            // infinite render loops if subsequent steps throw exceptions.
            sessionStorage.removeItem(STORAGE_KEY);
            
            const payload = JSON.parse(raw);
            if (payload && payload.message) {
                this.show(payload.message, payload.type, payload.timeOut);
            }
        } catch (error) {
            console.error('[Toast] Failed to parse queued pipeline notification state:', error);
        }
    },

    /* ==========================================================================
       DEVELOPER-FRIENDLY FLUENT ALIASES (Prevents manual string type entry errors)
       ========================================================================== */

    success(msg, timeOut) { this.show(msg, 'success', timeOut); },
    error(msg, timeOut)   { this.show(msg, 'error', timeOut); },
    info(msg, timeOut)    { this.show(msg, 'info', timeOut); },
    warning(msg, timeOut) { this.show(msg, 'warning', timeOut); },

    queueSuccess(msg, timeOut) { this.queue(msg, 'success', timeOut); },
    queueError(msg, timeOut)   { this.queue(msg, 'error', timeOut); }
};