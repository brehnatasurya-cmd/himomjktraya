/**
 * HIMO Mojokerto Raya — Admin Dashboard Scripts
 * Handles modal dialogs, CSRF token attachment, async form submits, and feedback
 */

(function () {
  'use strict';

  // Helper: Open Modal
  window.openModal = function (modalId) {
    const el = document.getElementById(modalId);
    if (el) {
      el.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  };

  // Helper: Close Modal
  window.closeModal = function (modalId) {
    const el = document.getElementById(modalId);
    if (el) {
      el.classList.remove('show');
      document.body.style.overflow = '';
    }
  };

  // Close modals on clicking backdrop
  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal-backdrop')) {
      e.target.classList.remove('show');
      document.body.style.overflow = '';
    }
  });

  // Close modals on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-backdrop.show').forEach(function (m) {
        m.classList.remove('show');
      });
      document.body.style.overflow = '';
    }
  });

  // Generic Async Form Submitter
  window.handleAdminForm = function (formElement, successCallback) {
    formElement.addEventListener('submit', async function (e) {
      e.preventDefault();

      const submitBtn = formElement.querySelector('[type="submit"]');
      const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
      }

      const formData = new FormData(formElement);
      const action = formElement.getAttribute('action') || window.location.href;

      try {
        const response = await fetch(action, {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          if (typeof successCallback === 'function') {
            successCallback(result);
          } else {
            alert(result.message || 'Operasi berhasil.');
            window.location.reload();
          }
        } else {
          alert('Peringatan: ' + (result.message || 'Gagal memproses data.'));
        }
      } catch (err) {
        alert('Terjadi kendala jaringan saat menghubungi server.');
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        }
      }
    });
  };

})();
