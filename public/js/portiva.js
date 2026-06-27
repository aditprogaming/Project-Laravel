document.addEventListener('DOMContentLoaded', () => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

  const flashContainer = document.getElementById('flashContainer');
  const showFlash = (message, type = 'success') => {
    if (!flashContainer || !message) {
      return;
    }

    const colors = {
      success: 'bg-emerald-500',
      error: 'bg-red-500',
      warning: 'bg-amber-500',
    };

    const node = document.createElement('div');
    node.className = `px-4 py-3 rounded-lg text-white shadow ${colors[type] || colors.success}`;
    node.textContent = message;
    flashContainer.appendChild(node);

    window.setTimeout(() => node.remove(), 3500);
  };

  const sendForm = async (form, url, method) => {
    const formData = new FormData(form);
    const httpMethod = (method || form.getAttribute('method') || 'POST').toUpperCase();
    const requestMethod = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'].includes(httpMethod) ? httpMethod : 'POST';

    if (httpMethod !== 'POST') {
      formData.set('_method', httpMethod);
    }

    const response = await fetch(url, {
      method: requestMethod,
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      body: formData,
    });

    const payload = await response.json().catch(() => ({}));

    if (!response.ok || payload.success === false) {
      const message = payload.message || 'Terjadi kesalahan saat memproses data.';
      throw new Error(message);
    }

    return payload;
  };

  document.querySelectorAll('form[data-portiva-api-form="portfolio"]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();

      const submitButton = form.querySelector('[type="submit"]');
      const originalText = submitButton?.textContent;

      try {
        if (submitButton) {
          submitButton.disabled = true;
          submitButton.textContent = 'Menyimpan...';
        }

        const payload = await sendForm(
          form,
          form.dataset.portivaApiUrl,
          form.dataset.portivaApiMethod || 'POST'
        );

        showFlash(payload.message || 'Data portfolio berhasil disimpan.', 'success');

        if (payload.redirect_to) {
          window.location.href = payload.redirect_to;
          return;
        }

        window.location.reload();
      } catch (error) {
        showFlash(error.message, 'error');
      } finally {
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = originalText || 'Simpan';
        }
      }
    });
  });

  document.querySelectorAll('form[data-portiva-api-delete="true"]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();

      const confirmMessage = form.dataset.confirmMessage || 'Hapus portfolio ini?';
      if (!window.confirm(confirmMessage)) {
        return;
      }

      const submitButton = form.querySelector('[type="submit"]');
      const originalText = submitButton?.textContent;

      try {
        if (submitButton) {
          submitButton.disabled = true;
          submitButton.textContent = 'Menghapus...';
        }

        const payload = await sendForm(form, form.dataset.portivaApiUrl, 'DELETE');

        showFlash(payload.message || 'Portfolio berhasil dihapus.', 'success');

        if (payload.redirect_to) {
          window.location.href = payload.redirect_to;
          return;
        }

        window.location.reload();
      } catch (error) {
        showFlash(error.message, 'error');
      } finally {
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = originalText || 'Hapus';
        }
      }
    });
  });

  const toggleModal = (id, open) => {
    const modal = document.getElementById(id);
    if (!modal) {
      return;
    }

    modal.classList.toggle('hidden', !open);
    modal.classList.toggle('flex', open);
  };

  const openBindings = [
    ['settingsBtn', 'settingsModal'],
    ['avatarContainer', 'uploadPhotoModal'],
    ['deleteAccountBtn', 'deleteAccountModal'],
  ];

  const closeBindings = [
    ['closeModal', 'settingsModal'],
    ['closeUploadModal', 'uploadPhotoModal'],
    ['cancelUploadBtn', 'uploadPhotoModal'],
    ['closeDeleteModal', 'deleteAccountModal'],
    ['cancelDeleteBtn', 'deleteAccountModal'],
  ];

  openBindings.forEach(([triggerId, modalId]) => {
    const trigger = document.getElementById(triggerId);
    if (!trigger) {
      return;
    }

    trigger.addEventListener('click', (event) => {
      event.preventDefault();
      toggleModal(modalId, true);
    });
  });

  closeBindings.forEach(([triggerId, modalId]) => {
    const trigger = document.getElementById(triggerId);
    if (!trigger) {
      return;
    }

    trigger.addEventListener('click', (event) => {
      event.preventDefault();
      toggleModal(modalId, false);
    });
  });

  [
    'settingsModal',
    'uploadPhotoModal',
    'deleteAccountModal',
  ].forEach((modalId) => {
    const modal = document.getElementById(modalId);
    if (!modal) {
      return;
    }

    modal.addEventListener('click', (event) => {
      if (event.target === modal) {
        toggleModal(modalId, false);
      }
    });
  });
});