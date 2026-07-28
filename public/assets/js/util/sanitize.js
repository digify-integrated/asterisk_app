'use strict';

const ESCAPE_REGEX = /[&<>"']/g;
const ESCAPE_MAP = Object.freeze({ 
    '&': '&amp;', 
    '<': '&lt;', 
    '>': '&gt;', 
    '"': '&quot;', 
    "'": '&#39;' 
});

/**
 * Escapes HTML characters to prevent XSS attacks.
 * Uses `window.e` if defined globally, otherwise falls back to standard regex replacement.
 * 
 * @param {string|number|null|undefined} str - The string to escape.
 * @returns {string} The escaped HTML string.
 */
export const escapeHtml = (str) => {
    if (typeof window !== 'undefined' && typeof window.e === 'function') {
        return window.e(str);
    }
    return str == null ? '' : String(str).replace(ESCAPE_REGEX, m => ESCAPE_MAP[m]);
};