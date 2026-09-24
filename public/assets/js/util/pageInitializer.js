export class PageInitializer {
    static async run(init, {
        onError = null,
        loadingClass = 'page-loading',
        loadingAttribute = 'data-kt-app-page-loading'
    } = {}) {
        try {
            return await init();
        } catch (error) {
            if (onError) {
                await onError(error);
            } else {
                console.error('Page initialization failed:', error);
            }

            throw error;
        } finally {
            document.body.classList.remove(loadingClass);
            document.body.removeAttribute(loadingAttribute);
        }
    }
}