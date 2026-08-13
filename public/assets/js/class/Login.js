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

export class Login {
    constructor() {
        this.abortController = new AbortController();

        this.dom = {
            form: document.querySelector(CONFIG.selectors.form),
            submitButton: document.querySelector(CONFIG.selectors.submitButton)
        };

        this.passwordToggle = new PasswordToggle();
    }

    init() {
        this.initForm();
    }

    initForm() {
        initValidation({
            forms: [
                {
                    selector: CONFIG.selectors.form,
                    rules: {
                        email: {
                            required: true,
                            typeEmail: true
                        },
                        password: {
                            required: true
                        }
                    },
                    submitHandler: (formElement) => this.authenticate(formElement)
                }
            ]
        });
    }

    async authenticate(formElement) {
        const button = CONFIG.selectors.submitButton;

        ButtonStateManager.disable(button, {
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
                signal: this.abortController.signal
            });

            if (await errorHandler.handleResponse(response, button)) {
                return;
            }

            const data = await response.json();

            if (data?.redirect) {
                window.location.href = data.redirect;
                return;
            }

            ButtonStateManager.enable(button);

        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            ButtonStateManager.enable(button);

            await errorHandler.handle(
                error,
                'network_failure',
                'Authentication request failed.'
            );
        }
    }

    destroy() {
        this.abortController.abort();
        this.passwordToggle.destroy();
    }
}