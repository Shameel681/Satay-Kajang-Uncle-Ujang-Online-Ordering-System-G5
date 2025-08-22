// Handles toast show/hide ONLY. Does not intercept form submit.
document.addEventListener('DOMContentLoaded', function () {
  const toast = document.getElementById('feedbackToast');
  if (toast) {
    // Auto-hide after 4 seconds
    setTimeout(() => {
      toast.classList.add('hide');
      // Remove from DOM after transition
      setTimeout(() => { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 500);
    }, 4000);

    // Manual close
    const closeBtn = toast.querySelector('.toast-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        toast.classList.add('hide');
        setTimeout(() => { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 300);
      });
    }
  }
});