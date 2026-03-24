function liveSearch() {
  const input = document.getElementById("inventorySearch");
  if (!input) return;

  const filter = input.value.toLowerCase();
  const table = document.getElementById("inventoryTable");
  if (!table) return;

  const tr = table.getElementsByTagName("tr");

  let visibleCount = 0;
  for (let i = 1; i < tr.length; i++) {
    let textContent = tr[i].textContent.toLowerCase();

    if (textContent.includes(filter)) {
      tr[i].style.display = "";
      visibleCount++;
    } else {
      tr[i].style.display = "none";
    }
  }
}

function modalLiveSearch() {
  const input = document.getElementById("modalInventorySearch");
  if (!input) return;
  const filter = input.value.toLowerCase();
  const container = document.getElementById("modal-table-container");
  if (!container) return;
  const table = container.querySelector("table");
  if (!table) return;
  const tr = table.getElementsByTagName("tr");

  for (let i = 1; i < tr.length; i++) {
    let textContent = tr[i].textContent.toLowerCase();

    if (textContent.includes(filter)) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }
  }
}
function historyLiveSearch() {
  const input = document.getElementById("historySearch");
  if (!input) return;

  const filter = input.value.toLowerCase();
  const groups = document.querySelectorAll(".item-group-wrapper");

  groups.forEach((group) => {
    const table = group.querySelector("table");
    if (!table) return;

    const tr = table.getElementsByTagName("tr");
    let groupHasVisibleRow = false;

    for (let i = 1; i < tr.length; i++) {
      const titleText = group
        .querySelector(".item-title")
        .textContent.toLowerCase();
      const rowText = tr[i].textContent.toLowerCase();

      const combinedText = titleText + " " + rowText;

      if (combinedText.includes(filter)) {
        tr[i].style.display = "";
        groupHasVisibleRow = true;
      } else {
        tr[i].style.display = "none";
      }
    }
    if (groupHasVisibleRow) {
      group.style.display = "";
    } else {
      if (!group.querySelector(".material-symbols-outlined")) {
        group.style.display = "none";
      }
    }
  });
}

function filterAdminTable() {
  const input = document.getElementById("adminSearch");
  const filter = input.value.toLowerCase();
  const table = document.getElementById("adminDataTable");
  const tr = table.getElementsByTagName("tr");
  const noResults = document.getElementById("noResultsRow");

  let visibleCount = 0;

  for (let i = 1; i < tr.length; i++) {
    if (tr[i].id === "noResultsRow") continue;

    const td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      const txtValue = td.textContent || td.innerText;
      if (txtValue.toLowerCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        visibleCount++;
      } else {
        tr[i].style.display = "none";
      }
    }
  }

  if (visibleCount === 0 && filter !== "") {
    noResults.style.display = "table-row";
  } else {
    noResults.style.display = "none";
  }
}


