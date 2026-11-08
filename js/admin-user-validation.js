document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("[data-admin-user-form]");
  if (!form) return;

  form.addEventListener("submit", (event) => {
    const password = form.elements.PASSWORD.value;
    if (password !== form.elements.PASSWORD2.value) {
      event.preventDefault();
      form.elements.PASSWORD2.setCustomValidity("Passwords do not match.");
      form.reportValidity();
      return;
    }
    form.elements.PASSWORD2.setCustomValidity("");
  });

  form.elements.PASSWORD2.addEventListener("input", () => {
    form.elements.PASSWORD2.setCustomValidity("");
  });
});
