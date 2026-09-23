'use strict';

import { initValidation } from '../util/validator.js';
import { errorHandler } from '../util/errorHandler.js';
import { ButtonStateManager } from '../util/buttonManager.js';
import { DetailFetcher } from '../util/detailFetcher.js';
import { PasswordToggle } from '../util/passwordToggle.js';
import { ImagePreview } from '../util/imagePreview.js';

const CONFIG = {
    selectors: {
        profileForm: '#account_profile_form',
        securityForm: '#account_security_form',
        submitProfileButton: '#submit-profile',
        submitPasswordButton: '#submit-password',
    },
    endpoints: {
        saveProfile: '/user/save-profile',
        savePassword: '/user/save-password',
        fetch: '/user/fetch'
    }
};
    
export class AccountSetting {
    constructor() {
        this.abortController = new AbortController();        
        this.passwordToggle = new PasswordToggle();
    }

    init() {
        this.initForm();
        ImagePreview.autoInit();
    }

    initForm() {
        initValidation({
            forms: [
                {
                    selector: CONFIG.selectors.profileForm,
                    rules: {
                        name: { required: true },
                        email: { 
                            required: true,
                            typeEmail: true
                        }
                    },
                    submitHandler: async (formElement) => this.handleFormSubmission(formElement, CONFIG.selectors.submitProfileButton, CONFIG.endpoints.saveProfile)
                },
                {
                    selector: CONFIG.selectors.securityForm,
                    rules: {
                        current_password: { required: true },
                        new_password: { 
                            required: true,
                            passwordStrength: 'medium'
                        },
                        new_password_confirmation: { 
                            required: true,
                            equalTo: 'new_password'
                        },
                    },
                    messages: {
                        new_password_confirmation: {
                            equalTo: 'Your passwords do not match. Please check again.'
                        }
                    },
                    submitHandler: async (formElement) => this.handleFormSubmission(formElement, CONFIG.selectors.submitPasswordButton, CONFIG.endpoints.savePassword)
                }
            ]
        });
    }

    async handleFormSubmission(formElement, btn, endpoint) {
        ButtonStateManager.disable(btn, { loadingText: 'Saving...' });

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest', 
                    'Accept': 'application/json' 
                },
                body: new FormData(formElement),
                signal: this.abortController.signal
            });

            if (await errorHandler.handleResponse(response, btn)) return;

        } catch (error) {
            if (error.name === 'AbortError') return; 
            ButtonStateManager.enable(btn);
            await errorHandler.handle(error, 'network_failure', 'Transactional pipeline error.');
        }
    }

    async handleFetchWorkflow(referenceId) {
        await DetailFetcher.fetch({
            url: CONFIG.endpoints.fetch,
            detailIdKey: CONFIG.selectors.detailId,
            detailIdValue: referenceId,
            formSelector: CONFIG.selectors.form,
            submitBtnSelector: CONFIG.selectors.submitButton,
            signal: this.abortController.signal,
            onSuccess: (response) => {
                const data = response?.data || response;
                if (!this.dom.form) return;

                const targetFields = {
                    'user_id': referenceId,
                    'name': data.name,
                    'email': data.email,
                    'status': data.status
                };

                Object.entries(targetFields).forEach(([name, val]) => {
                    const field = this.dom.form.elements[name];
                    if (field) {
                        field.value = val ?? '';
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }
        });
    }
}