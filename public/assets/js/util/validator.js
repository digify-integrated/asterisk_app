'use strict';

import { Toast } from './notifications.js';

export class FormValidator {
    static DEFAULTS = {
        toastType: 'error',
        toastDuration: 2000,
        notifyOnFieldInvalid: false,
        focusFirstInvalid: true,
        scrollToFirstInvalid: true,
        toastEachError: true,
        toastDelayStepMs: 140,
        maxToastsPerSubmit: 5,
        rules: {},
        messages: {},
        submitHandler: null,
        getMessage: null,
        runInferredRulesEvenWhenCustomProvided: true,
    };

    static RULE_ORDER = [
        'required', 'requiredIf', 'typeEmail', 'typeUrl', 'typeTel', 
        'typeNumber', 'minlength', 'maxlength', 'min', 'max', 'step', 'pattern', 'equalTo'
    ];

    constructor(formElement, options = {}) {
        if (!(formElement instanceof HTMLFormElement)) {
            throw new TypeError('[FormValidator] Target element must be a valid HTMLFormElement.');
        }

        this.form = formElement;
        this.config = Object.assign({}, FormValidator.DEFAULTS, options);
        this._abortController = new AbortController();

        this._init();
    }

    _init() {
        if (this.form.dataset.validationBound === 'true') return;
        this.form.dataset.validationBound = 'true';

        this._bindSelect2Events();
        const { signal } = this._abortController;

        if (this.config.notifyOnFieldInvalid) {
            this.form.addEventListener('invalid', (e) => this._handleInvalidField(e.target), { capture: true, signal });
        }

        this.form.addEventListener('input', (e) => this._clearInvalidState(e.target), { signal });
        this.form.addEventListener('change', (e) => this._clearInvalidState(e.target), { signal });
        this.form.addEventListener('submit', (e) => this._handleSubmit(e), { signal });
    }

    destroy() {
        this._abortController.abort();
        delete this.form.dataset.validationBound;
        delete this.form.dataset.select2InvalidBound;
    }

    _handleInvalidField(field) {
        if (!this._isValidatableField(field)) return;
        const errors = this.validateField(field);
        if (!errors.length) return;

        this._setFieldInvalidState(field, true);
        this._toastErrors(errors);
    }

    _clearInvalidState(field) {
        if (this._isValidatableField(field)) {
            this._setFieldInvalidState(field, false);
        }
    }

    async _handleSubmit(event) {
        if (window.jQuery) {
            window.jQuery(this.form).find('select.select2-hidden-accessible').each((_, el) => {
                el.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }

        const { valid, errors } = this.validateForm();

        if (!valid) {
            event.preventDefault();
            event.stopPropagation();

            errors.forEach(err => this._setFieldInvalidState(err.field, true));

            const targetedToasts = this.config.toastEachError ? errors : [errors[0]].filter(Boolean);
            this._toastErrors(targetedToasts);

            const firstInvalid = errors[0]?.field;
            if (firstInvalid) {
                this._focusFieldSmart(firstInvalid, {
                    scroll: this.config.scrollToFirstInvalid,
                    focus: this.config.focusFirstInvalid,
                });
            }

            this.form.classList.add('was-validated');
            return;
        }

        this.form.classList.add('was-validated');

        if (typeof this.config.submitHandler === 'function') {
            event.preventDefault();
            try {
                await this.config.submitHandler(this.form);
            } catch (err) {
                console.error('[FormValidator] Error inside custom submitHandler Execution: ', err);
            }
        }
    }

    validateForm() {
        const fields = Array.from(this.form.querySelectorAll('input, select, textarea')).filter(f => this._isValidatableField(f));
        const errors = [];

        for (const field of fields) {
            const fieldErrors = this.validateField(field);
            if (fieldErrors.length) errors.push(...fieldErrors);
        }

        return { valid: errors.length === 0, errors };
    }

    validateField(field) {
        const fieldKey = field.getAttribute('name') || field.id || null;
        const errors = [];

        const inferred = this._inferRules(field);
        const custom = fieldKey ? (this.config.rules?.[fieldKey] || null) : null;
        const effectiveRules = this._buildEffectiveRules(inferred, custom);

        const orderedRules = Object.entries(effectiveRules).sort((a, b) => {
            const ai = FormValidator.RULE_ORDER.indexOf(a[0]);
            const bi = FormValidator.RULE_ORDER.indexOf(b[0]);
            return (ai === -1 ? 999 : ai) - (bi === -1 ? 999 : bi);
        });

        for (const [ruleName, ruleValue] of orderedRules) {
            if (ruleValue === false || ruleValue == null) continue;

            const passed = this._runRule(ruleName, ruleValue, field);
            if (!passed) {
                const msg = this._resolveMessage({ field, fieldKey, ruleName, ruleValue });
                errors.push({ field, rule: ruleName, message: msg });
                break;
            }
        }

        if (errors.length === 0 && orderedRules.length === 0 && !field.checkValidity()) {
            const nativeMsg = (field.validationMessage || 'Invalid value').trim();
            errors.push({ field, rule: 'native', message: this._prefixWithLabel(field, nativeMsg) });
        }

        return errors;
    }

    _buildEffectiveRules(inferred, custom) {
        const inf = inferred || {};
        const cust = custom || {};
        if (!this.config.runInferredRulesEvenWhenCustomProvided && Object.keys(cust).length) {
            return { ...cust };
        }
        return Object.assign({}, inf, cust);
    }

    _runRule(ruleName, ruleValue, field) {
        const value = this._getFieldValue(field);
        const type = (field.getAttribute('type') || '').toLowerCase();

        switch (ruleName) {
            case 'required':
                if (type === 'checkbox') return field.checked;
                if (type === 'radio') {
                    if (!field.name) return field.checked;
                    return !!this.form.querySelector(`input[type="radio"][name="${this._cssEscape(field.name)}"]:checked`);
                }
                return value !== null && String(value).trim() !== '';

            case 'requiredIf': {
                if (!this._evaluateCondition(ruleValue, field)) return true;
                return value !== null && String(value).trim() !== '';
            }
            case 'typeEmail':
                return value === '' || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value));
            case 'typeUrl':
                if (value === '') return true;
                try {
                    const u = new URL(String(value));
                    return u.protocol === 'http:' || u.protocol === 'https:';
                } catch { return false; }
            case 'typeTel':
                return value === '' || /^[+()\d\s\-\.]{6,}$/.test(String(value));
            case 'typeNumber':
                return value === '' || Number.isFinite(Number(value));
            case 'minlength':
                return value === '' || String(value).length >= Number(ruleValue);
            case 'maxlength':
                return value === '' || String(value).length <= Number(ruleValue);
            case 'pattern':
                if (value === '') return true;
                const re = ruleValue instanceof RegExp ? ruleValue : new RegExp(`^(?:${String(ruleValue)})$`);
                return re.test(String(value));
            case 'min':
            case 'max': {
                if (value === '') return true;
                const n = Number(value), b = Number(ruleValue);
                if (!Number.isFinite(n) || !Number.isFinite(b)) return String(value) >= String(ruleValue);
                return ruleName === 'min' ? n >= b : n <= b;
            }
            case 'step': {
                if (value === '') return true;
                const n = Number(value), s = Number(ruleValue);
                if (!Number.isFinite(n) || !Number.isFinite(s) || s <= 0) return true;
                return Math.abs((n / s) - Math.round(n / s)) < 1e-10;
            }
            case 'equalTo': {
                const other = this._resolveOtherField(ruleValue);
                return !other || String(value) === String(this._getFieldValue(other));
            }
            default:
                return true;
        }
    }

    _inferRules(field) {
        const rules = {};
        if (field.required) rules.required = true;

        const type = (field.getAttribute('type') || '').toLowerCase();
        if (['email', 'url', 'tel', 'number'].includes(type)) {
            rules[`type${type.charAt(0).toUpperCase() + type.slice(1)}`] = true;
        }

        const minLen = field.getAttribute('minlength');
        if (minLen) rules.minlength = Number(minLen);

        const maxLen = field.getAttribute('maxlength');
        if (maxLen) rules.maxlength = Number(maxLen);

        const pattern = field.getAttribute('pattern');
        if (pattern) rules.pattern = pattern;

        const min = field.getAttribute('min');
        if (min) rules.min = min;

        const max = field.getAttribute('max');
        if (max) rules.max = max;

        const step = field.getAttribute('step');
        if (step && step !== 'any') rules.step = step;

        const eq = field.getAttribute('data-rule-equal-to');
        if (eq) rules.equalTo = eq;

        return rules;
    }

    _evaluateCondition(condition, field) {
        if (typeof condition === 'function') return !!condition(this.form, field);
        if (!condition || typeof condition !== 'object') return false;

        const other = condition.selector ? this.form.querySelector(condition.selector) : this._resolveOtherField(condition.field);
        if (!other) return false;

        const otherVal = this._getFieldValue(other);
        const isEmpty = otherVal === null || String(otherVal).trim() === '';

        if (condition.notEmpty) return !isEmpty;
        if ('value' in condition) {
            return Array.isArray(condition.value) 
                ? condition.value.map(String).includes(String(otherVal)) 
                : String(otherVal) === String(condition.value);
        }
        return !isEmpty;
    }

    _resolveOtherField(identifier) {
        if (!identifier) return null;
        if (typeof identifier === 'string' && /[#.\[]/.test(identifier)) return this.form.querySelector(identifier);
        return this.form.querySelector(`[name="${this._cssEscape(String(identifier))}"]`) || this.form.querySelector(`#${this._cssEscape(String(identifier))}`);
    }

    _toastErrors(errors) {
        const seen = new Set();
        let count = 0;

        for (const e of errors) {
            const msg = String(e.message || '').trim();
            if (!msg || seen.has(msg)) continue;
            seen.add(msg);

            const delay = count * this.config.toastDelayStepMs;
            if (delay === 0) Toast.show(msg, this.config.toastType, this.config.toastDuration);
            else setTimeout(() => Toast.show(msg, this.config.toastType, this.config.toastDuration), delay);

            count++;
            if (count >= this.config.maxToastsPerSubmit) break;
        }
    }

    _resolveMessage({ field, fieldKey, ruleName, ruleValue }) {
        const custom = fieldKey ? this.config.messages?.[fieldKey]?.[ruleName] : null;
        const name = this._getFieldLabelText(field) || field.getAttribute('aria-label') || field.name || 'Field';
        
        let fallback = `${name} is invalid.`;
        switch (ruleName) {
            case 'required': fallback = `Please enter ${name}.`; break;
            case 'typeEmail': fallback = `Please enter a valid email for ${name}.`; break;
            case 'minlength': fallback = `${name} must be at least ${ruleValue} characters.`; break;
            case 'equalTo': fallback = `${name} values do not match.`; break;
        }

        const chosen = typeof custom === 'string' && custom.trim() ? custom : fallback;

        if (typeof this.config.getMessage === 'function') {
            return this.config.getMessage({ field, fieldKey, ruleName, ruleValue, defaultMessage: chosen }) || chosen;
        }
        return chosen;
    }

    _getFieldLabelText(field) {
        let labelEl = null;
        if (field.id) {
            labelEl = this.form.querySelector(`label[for="${this._cssEscape(field.id)}"]`);
        }
        if (!labelEl) {
            labelEl = field.closest('label');
        }
        if (!labelEl) return '';

        return Array.from(labelEl.childNodes)
            .filter(node => node.nodeType === Node.TEXT_NODE)
            .map(node => node.textContent.trim())
            .join(' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    _getFieldValue(field) {
        if (field.getAttribute('type') === 'checkbox') return field.checked ? (field.value || 'on') : '';
        if (field.getAttribute('type') === 'radio') {
            return this.form.querySelector(`input[type="radio"][name="${this._cssEscape(field.name)}"]:checked`)?.value || '';
        }
        
        if (field.tagName === 'SELECT') {
            const val = field.value;
            if (val === '--' || val === null) return '';
            return val;
        }
        
        return field.value;
    }

    _isValidatableField(el) {
        return el && !el.disabled && el.tagName !== 'BUTTON' && !['hidden', 'submit', 'reset', 'button'].includes(el.type);
    }

    _prefixWithLabel(field, msg) {
        const label = this._getFieldLabelText(field) || 'Field';
        return `${label}: ${msg}`;
    }

    _cssEscape(val) {
        return window.CSS?.escape ? window.CSS.escape(val) : String(val).replace(/["\\#.;?+*~':!^$[\]()=>|/@]/g, '\\$&');
    }

    _isSelect2(field) { return field?.tagName === 'SELECT' && field.classList.contains('select2-hidden-accessible'); }
    
    _getSelect2Target(field) {
        return this._isSelect2(field) ? field.nextElementSibling?.querySelector('.select2-selection') || field : field;
    }

    _setFieldInvalidState(field, on = true) {
        field.classList.toggle('is-invalid', !!on);
        const target = this._getSelect2Target(field);
        if (target !== field) target.classList.toggle('is-invalid', !!on);
    }

    _bindSelect2Events() {
        if (!window.jQuery) return;
        const $ = window.jQuery;
        $(this.form).find('select.select2-hidden-accessible').each((_, el) => {
            if (el.dataset.select2InvalidBound === 'true') return;
            el.dataset.select2InvalidBound = 'true';
            $(el).on('select2:select select2:unselect select2:clear select2:close', () => this._setFieldInvalidState(el, false));
        });
    }

    _focusFieldSmart(field, { scroll, focus }) {
        const isS2 = this._isSelect2(field);
        const container = isS2 ? field.nextElementSibling : field;

        if (scroll && container) container.scrollIntoView({ behavior: 'smooth', block: 'center' });
        if (focus) {
            setTimeout(() => {
                if (isS2 && window.jQuery) {
                    try { window.jQuery(field).select2('open'); } catch { container.querySelector('.select2-selection')?.focus(); }
                } else {
                    field.focus({ preventScroll: true });
                }
            }, 50);
        }
    }
}

export function initValidation(masterConfig = {}) {
    const instances = [];
    const formsArray = masterConfig.forms || [];

    const initializeFormElement = (element, config) => {
        if (element.dataset.validationBound === 'true') return null;
        const validator = new FormValidator(element, config.options || config);
        instances.push(validator);
        return validator;
    };

    for (const formSetup of formsArray) {
        document.querySelectorAll(formSetup.selector).forEach(element => {
            initializeFormElement(element, formSetup);
        });
    }

    if (masterConfig.observeDynamicDOM !== false) {
        document.addEventListener('shown.bs.modal', (e) => {
            formsArray.forEach(formSetup => {
                const innerForm = e.target.querySelector(formSetup.selector);
                if (innerForm) initializeFormElement(innerForm, formSetup);
            });
        });
    }

    return instances;
}