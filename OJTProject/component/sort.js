function sortTable(n) {
  const table = document.getElementById("inventoryTable");
  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr"));

  const header = table.querySelectorAll("th")[n];
  const isAscending = header.classList.contains("th-sort-asc");
  const direction = isAscending ? -1 : 1;

  table.querySelectorAll("th").forEach((th) => {
    th.classList.remove("th-sort-asc", "th-sort-desc");
    const icon = th.querySelector(".material-symbols-outlined");
    if (icon) icon.textContent = "unfold_more";
  });

  const sortedRows = rows.sort((a, b) => {
    const aColText = a.cells[n].textContent.trim();
    const bColText = b.cells[n].textContent.trim();

    const aValue = parseValue(aColText);
    const bValue = parseValue(bColText);

    if (aValue > bValue) return 1 * direction;
    if (aValue < bValue) return -1 * direction;
    return 0;
  });

  header.classList.toggle("th-sort-asc", !isAscending);
  header.classList.toggle("th-sort-desc", isAscending);

  const currentIcon = header.querySelector(".material-symbols-outlined");
  if (currentIcon) {
    currentIcon.textContent = isAscending ? "expand_more" : "expand_less";
  }

  tbody.append(...sortedRows);
}

function parseValue(value) {
  const cleanValue = value.replace(/[₱,]/g, "");
  return isNaN(cleanValue) || cleanValue === ""
    ? value.toLowerCase()
    : parseFloat(cleanValue);
}
