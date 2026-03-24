document.addEventListener("DOMContentLoaded", () => {
  const openBtn = document.getElementById("openBtn");
  const openBtnEmpty = document.getElementById("openBtnEmpty");
  const closeBtn = document.getElementById("closeBtn");
  const modal = document.getElementById("modal");
  const tableContainer = document.getElementById("modal-table-container");
  const actionContainer = document.getElementById("modal-actions");
  const mainTable = document.getElementById("inventoryTable");

  const triggerModal = () => {
    if (!modal) return;
    modal.classList.add("show");

    if (!mainTable) {
      tableContainer.innerHTML =
        "<p style='padding:20px; text-align:center;'>No data to edit.</p>";
      return;
    }

    tableContainer.innerHTML = "";
    actionContainer.innerHTML = "";

    const tableClone = mainTable.cloneNode(true);
    tableClone.id = "editorTable";

    const rawHeaders = Array.from(mainTable.querySelectorAll("thead th")).map(
      (th) => th.getAttribute("data-column") || "",
    );

    // --- SELECT ALL LOGIC ---
    const theadRow = tableClone.querySelector("thead tr");
    const selectTh = document.createElement("th");
    selectTh.innerHTML = `<input type="checkbox" id="selectAllRows" style="cursor: pointer;">`;
    theadRow.insertBefore(selectTh, theadRow.firstChild);

    const selectAllCheckbox = selectTh.querySelector("#selectAllRows");
    selectAllCheckbox.onclick = (e) => {
      const isChecked = e.target.checked;
      tableClone
        .querySelectorAll(".row-select")
        .forEach((cb) => (cb.checked = isChecked));
    };

    // --- ROW EDITING LOGIC ---
    tableClone.querySelectorAll("tbody tr").forEach((row) => {
      const id = row.getAttribute("data-id");
      const selectTd = document.createElement("td");
      selectTd.innerHTML = `<input type="checkbox" name="selectedItems[]" value="${id}" class="row-select">`;
      row.insertBefore(selectTd, row.firstChild);

      row.querySelectorAll("td").forEach((td, i) => {
        const h = rawHeaders[i - 1]; // Offset dahil sa in-insert na checkbox

        const readOnlyCols = [
          "select",
          "id",
          "total_value",
          "beginning_inventory",
          "received_qty",
          "is_acknowledged",
          "item_uuid",
        ];

        if (h && !readOnlyCols.includes(h)) {
          td.setAttribute("contenteditable", "true");
          td.style.backgroundColor = "#fffdf0";
          if (h === "price")
            td.textContent = td.textContent.replace(/[₱,]/g, "");
        } else {
          td.setAttribute("contenteditable", "false");
          td.style.backgroundColor = "#f0f0f0";
        }
      });
    });

    // --- SYNC CHANGES BUTTON ---
    const saveBtn = document.createElement("button");
    saveBtn.textContent = "Sync Changes";
    saveBtn.className = "opnbtn";

    saveBtn.onclick = async (e) => {
      e.preventDefault();
      const updateData = Array.from(
        tableClone.querySelectorAll("tbody tr"),
      ).map((row) => {
        const obj = { id: row.getAttribute("data-id") };
        row.querySelectorAll("td").forEach((td, i) => {
          const h = rawHeaders[i - 1];
          if (
            h &&
            ![
              "select",
              "id",
              "total_value",
              "beginning_inventory",
              "received_qty",
              "is_acknowledged",
              "item_uuid",
            ].includes(h)
          ) {
            let val = td.textContent.trim();
            if (h === "price") val = val.replace(/[₱,]/g, "");
            obj[h] = val;
          }
        });
        return obj;
      });

      try {
        const response = await fetch("inventoryFunction.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ updateData }),
        });
        if (response.ok) {
          const currentUrl = new URL(window.location.href);
          currentUrl.searchParams.set("keepOpen", "true");
          window.location.href = currentUrl.toString();
        } else {
          alert("Error syncing changes.");
        }
      } catch (err) {
        console.error("Sync failed:", err);
      }
    };

    actionContainer.appendChild(saveBtn);
    tableContainer.appendChild(tableClone);

    // --- QR GENERATION VALIDATION (NEW) ---
    const qrBtnContainer = document.querySelector(".qr-btn-container");
    if (qrBtnContainer) {
      const qrLink = qrBtnContainer.closest("a"); 

      if (qrLink) {
        qrLink.onclick = (e) => {
          e.preventDefault();
          const selectedCheckboxes = tableClone.querySelectorAll(
            ".row-select:checked",
          );
          const selectedIds = Array.from(selectedCheckboxes).map(
            (cb) => cb.value,
          );

          if (selectedIds.length === 0) {
            console.log("QR Action: FAILED - No items selected.");
            alert(
              "Please select at least one item (Select All or individual) before generating a QR code.",
            );
          } else {
            console.log(
              `QR Action: SUCCESS - ${selectedIds.length} item(s) selected:`,
              selectedIds,
            );
            const baseUrl = qrLink.getAttribute("href").split("?")[0];
            window.location.href = `${baseUrl}?ids=${selectedIds.join(",")}`;
          }
        };
      }
    }
  };

  if (openBtn) openBtn.onclick = triggerModal;
  if (openBtnEmpty) openBtnEmpty.onclick = triggerModal;
  if (closeBtn) closeBtn.onclick = () => modal.classList.remove("show");

  const params = new URLSearchParams(window.location.search);
  if (params.has("keepOpen")) triggerModal();
});
