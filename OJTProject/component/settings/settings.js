const launcherBtn = document.getElementById("launcherBtn");
const ubuntuMenu = document.getElementById("ubuntuMenu");

launcherBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  ubuntuMenu.classList.toggle("active");
});

document.addEventListener("click", () => {
  ubuntuMenu.classList.remove("active");
});

function confirmLogout() {
  if (confirm("Are you sure you want to logout?")) {
    window.location.href = "/OJTProject/component/settings/logout.php";
  }
}

function openAddTechModal() {
  console.log("Add Technician clicked");
  window.location.href = "/OJTProject/pages/addUser/addUser.php";
}
