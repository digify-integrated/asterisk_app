'use strict';

import { initValidation } from '../util/validator.js';
import { errorHandler } from '../util/errorHandler.js';
import { PasswordToggle } from '../util/passwordToggle.js';
import { ButtonStateManager } from '../util/buttonManager.js';

const CONFIG = {
    selectors: {
        form: '#login_form',
        submitButton: '#signin'
    },
    endpoints: {
        authenticate: '/auth/authenticate'
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const passComponent = new PasswordToggle();
    
    const abortController = new AbortController();
    const { signal } = abortController;

    initValidation({
        forms: [
            {
                selector: CONFIG.selectors.form,
                rules: {
                    email: { required: true, typeEmail: true },
                    password: { required: true }
                },
                submitHandler: async (formElement) => {
                    const btn = CONFIG.selectors.submitButton;
                    
                    ButtonStateManager.disable(btn, {
                        loadingText: 'Signing you in...',
                        showLoader: true
                    });

                    try {
                        const response = await fetch(CONFIG.endpoints.authenticate, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: new URLSearchParams(new FormData(formElement)),
                            signal
                        });

                        if (await errorHandler.handleResponse(response, btn)) return;

                        const data = await response.json();
                        
                        if (data?.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            ButtonStateManager.enable(btn);
                        }

                    } catch (error) {
                        if (error.name === 'AbortError') return;
                        
                        ButtonStateManager.enable(btn);
                        await errorHandler.handle(error, 'network_failure', 'Authentication request failed.');
                    }
                }
            }
        ]
    });

    window.addEventListener('unload', () => abortController.abort(), { once: true });
});