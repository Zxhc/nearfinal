document.addEventListener("DOMContentLoaded", function () {
  const listBtn = document.getElementById("listView");
  const gridBtn = document.getElementById("gridView");
  const container = document.getElementById("mainContainer");

  // --- CHECK SAVED VIEW ON LOAD ---
  const savedView = localStorage.getItem("prs_view_preference") || "list-mode";

  if (savedView === "grid-mode") {
    applyGridView();
  } else {
    applyListView();
  }

  // --- GRID BUTTON CLICK ---
  gridBtn.addEventListener("click", function (e) {
    e.preventDefault();
    applyGridView();
  });

  // --- LIST BUTTON CLICK ---
  listBtn.addEventListener("click", function (e) {
    e.preventDefault();
    applyListView();
  });

  function applyGridView() {
    container.classList.add("grid-mode");
    container.classList.remove("list-mode");
    gridBtn.classList.add("active");
    listBtn.classList.remove("active");
    localStorage.setItem("prs_view_preference", "grid-mode"); // Save preference
  }

  function applyListView() {
    container.classList.add("list-mode");
    container.classList.remove("grid-mode");
    listBtn.classList.add("active");
    gridBtn.classList.remove("active");
    localStorage.setItem("prs_view_preference", "list-mode"); // Save preference
  }
});
