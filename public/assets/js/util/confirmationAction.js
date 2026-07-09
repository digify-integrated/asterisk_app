export const initConfirmAction = ({
  trigger,
  url,
  payload = {},
  method = 'POST', 
  swalTitle,
  swalText,
  swalIcon = 'warning',
  confirmButtonText,
  confirmButtonClass = 'primary',
}) => {
  document.addEventListener('click', async (e) => {
    const element = e.target.closest(trigger);
    if (!element) return; 

    e.preventDefault();

    const result = await Swal.fire({
      title: swalTitle,
      text: swalText,
      icon: swalIcon,
      showCancelButton: true,
      confirmButtonText: confirmButtonText,
      cancelButtonText: 'Cancel',
      customClass: {
        confirmButton: `btn btn-${confirmButtonClass}`,
        cancelButton: 'btn btn-secondary m-1',
      },
      buttonsStyling: false,
    });

    if (!result.isConfirmed) return;

    try {
      const csrf = typeof getCsrfToken === 'function' ? getCsrfToken() : '';
      const ctx = typeof getPageContext === 'function' ? getPageContext() : {};

      const formData = new URLSearchParams();
      
      formData.append('detailId', ctx.detailId ?? '');
      formData.append('appId', ctx.appId ?? '');
      formData.append('navigationMenuId', ctx.navigationMenuId ?? '');

      Object.entries(payload).forEach(([key, value]) => {
        if (typeof value === 'function') {
          formData.append(key, value(element) ?? '');
        } else {
          formData.append(key, value ?? '');
        }
      });

      if (element.dataset) {
        Object.entries(element.dataset).forEach(([key, value]) => {
          if (!formData.has(key)) {
            formData.append(key, value ?? '');
          }
        });
      }

      const upperMethod = method.toUpperCase();
      if (['DELETE', 'PUT', 'PATCH'].includes(upperMethod)) {
        formData.append('_method', upperMethod);
      }

      const response = await fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'Accept': 'application/json',
          ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
        },
      });

      if (!response.ok) throw new Error(`Server status: ${response.status}`);

      const data = await response.json();

      if (data.success) {
        if (typeof setNotification === 'function') setNotification(data.message, 'success');
        if (data.redirect_link) window.location.replace(data.redirect_link);
      } else {
        if (typeof showNotification === 'function') showNotification(data.message || 'An error occurred.');
      }
    } catch (error) {
      if (typeof handleSystemError === 'function') {
        handleSystemError(error, 'action_failed', `Request failed: ${error.message}`);
      } else {
        console.error('Action Failed:', error);
      }
    }
  });
};