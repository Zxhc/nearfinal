const masterCheck = document.getElementById("selectAllAlerts");
if (masterCheck) {
  masterCheck.addEventListener("change", function () {
    const checks = document.querySelectorAll(".alert-checkbox");
    checks.forEach((c) => (c.checked = this.checked));
  });
}
document.addEventListener("DOMContentLoaded", function () {
  const observerOptions = {
    root: document.querySelector("#history-content"),
    threshold: 0.5, 
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("reveal");
      }
    });
  }, observerOptions);

  const rows = document.querySelectorAll(".history-table tbody tr");
  rows.forEach((row) => observer.observe(row));
});
