// Placeholder for login page specific JS
// Add any interactive behaviors you need for the login page here.

// Example: toggle password visibility when an element is clicked (if added later)
(function () {
  // Autofocus first input if not already focused by HTML attribute
  window.addEventListener('DOMContentLoaded', () => {
    const active = document.activeElement;
    if (!active || active === document.body) {
      const firstInput = document.querySelector('form input, form textarea, form select');
      if (firstInput) firstInput.focus();
    }
  });
})();
