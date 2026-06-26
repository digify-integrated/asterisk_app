import { initValidation } from '../util/validator.js';
import { errorHandler } from '../util/errorHandler.js';
import { PasswordToggle } from '../util/passwordToggle.js';
import { ButtonStateManager } from '../util/buttonManager.js';

document.addEventListener('DOMContentLoaded', () => {
    const passComponent = new PasswordToggle();
    const submitBtn = '#signin';

    initValidation({
        forms: [
            {
                selector: '#login_form',
                rules: {
                    email: { required: true, typeEmail: true },
                    password: { required: true }
                },
                submitHandler: async (form) => {
                    ButtonStateManager.disable(submitBtn, {
                        loadingText: 'Signing you in...',
                        showLoader: true
                    });

                    try {
                        const response = await fetch('/auth/authenticate', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: new URLSearchParams(new FormData(form))
                        });

                        if (await errorHandler.handleResponse(response, submitBtn)) {
                            return;
                        }

                        const data = await response.json();
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }

                    } catch (error) {
                        ButtonStateManager.enable(submitBtn);
                        await errorHandler.handle(error, 'network_failure', 'Authentication request failed.');
                    }
                }
            }
        ]
    });
});