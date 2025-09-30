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


document.addEventListener('DOMContentLoaded', function() {
    const toast = document.getElementById('feedbackToast');
    if (toast) {
        // Show toast
        toast.style.display = 'block';
        // Auto-hide after 5 seconds
        setTimeout(() => {
            toast.style.display = 'none';
        }, 5000);
        // Close button functionality
        const closeButton = toast.querySelector('.toast-close');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                toast.style.display = 'none';
            });
        }
    }
});