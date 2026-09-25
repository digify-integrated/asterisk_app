'use strict';

import { PageInitializer } from '../util/pageInitializer.js';
import { initValidation } from '../util/validator.js';
import { errorHandler } from '../util/errorHandler.js';
import { ButtonStateManager } from '../util/buttonManager.js';
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

        this.dom = {
            profileForm: document.querySelector(CONFIG.selectors.profileForm),
            securityForm: document.querySelector(CONFIG.selectors.securityForm)
        };
    }

    async init() {
        return PageInitializer.run(async () => {
            this.initForm();
            ImagePreview.autoInit();
        });
    }

    destroy() {
        this.abortController.abort();
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
                    submitHandler: async (formElement) => this.handleFormSubmission(
                        formElement, 
                        CONFIG.selectors.submitProfileButton, 
                        CONFIG.endpoints.saveProfile
                    )
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
                    submitHandler: async (formElement) => this.handleFormSubmission(
                        formElement, 
                        CONFIG.selectors.submitPasswordButton, 
                        CONFIG.endpoints.savePassword
                    )
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
}