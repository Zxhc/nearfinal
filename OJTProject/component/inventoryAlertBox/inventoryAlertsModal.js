function createItemRow(index, name = "", desc = "", maker = "", uom = "", qty = "", price = "", invId = "") {
    const currencySelect = document.getElementById("currency_type");
    const currencySign = currencySelect ? currencySelect.value : "PHP";
    
    const subtotal = (parseFloat(qty || 0) * parseFloat(price || 0)).toLocaleString(undefined, {
        minimumFractionDigits: 2,
    });

    return `
        <div class="item-row pr-items-grid" style="margin-bottom:8px; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">
            <input type="hidden" name="acknowledge_ids[]" value="${invId}">
            <div class="row-number">${index + 1}</div>
            <input type="text" name="item_names[]" value="${name}" class="pr-input-style">
            <input type="text" name="item_descs[]" value="${desc}" class="pr-input-style">
            <input type="text" name="item_makers[]" value="${maker}" class="pr-input-style" placeholder="Maker/Brand">
            <input type="text" name="item_uoms[]" value="${uom}" class="pr-input-style" style="text-align:center;" placeholder="pcs/unit" > 
            <input type="number" name="item_qtys[]" value="${qty}" class="pr-input-style item-qty" placeholder="0" style="text-align:center;" oninput="calculateGrandTotal()">
            <input type="number" step="0.01" name="item_prices[]" value="${price}" class="pr-input-style item-price" placeholder="0.00" style="text-align:right;" oninput="calculateGrandTotal()">
            <div class="row-total-display" style="background:#f9fafb; padding:8px; border:1px solid #e2e8f0; border-radius:6px; font-size:12px; font-weight:700; color:#1e293b; text-align:right;">
                ${currencySign} ${subtotal}
            </div>
            <button type="button" onclick="removePRItem(this)" style="background:#fee2e2; border:none; border-radius:4px; height:30px; cursor:pointer;">
                <span class="material-symbols-outlined" style="font-size:18px; color:red;">delete</span>
            </button>
        </div>`;
}

function removePRItem(btn) {
  const row = btn.closest(".item-row");
  if (confirm("Are you sure you want to remove this?")) {
    row.remove();

    const rows = document.querySelectorAll(".item-row");
    rows.forEach((r, idx) => {
      const numDiv = r.querySelector(".row-number");
      if (numDiv) numDiv.innerText = idx + 1;
    });

    calculateGrandTotal();
  }
}

function calculateGrandTotal() {
  let grandTotal = 0;
  const currencySelect = document.getElementById("currency_type");
  const selectedOption = currencySelect.options[currencySelect.selectedIndex];
  const rate = parseFloat(selectedOption.getAttribute("data-rate")) || 1;
  const currencySign = currencySelect.value;

  const rows = document.querySelectorAll(".item-row");
  rows.forEach((row) => {
    const qty = parseFloat(row.querySelector(".item-qty").value) || 0;
    const price = parseFloat(row.querySelector(".item-price").value) || 0;
    const subtotal = qty * price;

    const displaySubtotal = row.querySelector(".row-total-display");
    if (displaySubtotal) {
      displaySubtotal.innerText = `${currencySign} ${subtotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
    grandTotal += subtotal * rate;
  });

  const totalInput = document.getElementById("pr_total");
  if (totalInput) totalInput.value = grandTotal.toFixed(2);
}

function closePRModal() {
  document.getElementById("prModal").style.display = "none";
}
function openPRModal() {
  const selected = document.querySelectorAll(
    'input[name="acknowledge_ids[]"]:checked',
  );
  const itemsListBody = document.getElementById("items_list_body");

  if (selected.length === 0) {
    alert("Select an item first!");
    return;
  }

  itemsListBody.innerHTML = "";
  let hasUrgent = false;

  selected.forEach((cb, index) => {
    const row = cb.closest("tr");
    const invId = cb.value; 
    
    const itemName = row.cells[1].innerText.replace("OUT OF STOCK", "").trim();
    const itemDesc = row.querySelector(".desc-cell")
      ? row.querySelector(".desc-cell").innerText.trim()
      : "";

    if (row.innerText.includes("OUT OF STOCK")) hasUrgent = true;
    itemsListBody.insertAdjacentHTML(
      "beforeend",
      createItemRow(index, itemName, itemDesc, "", "", "", "", invId)
    );
  });

  document.getElementById("admin_suffix").value = hasUrgent ? "URGENT" : "";
  document.getElementById("form_mode").value = "create";
  document.getElementById("modal_title").innerText = "📝 Prepare Purchase Request";
  document.getElementById("prModal").style.display = "flex";

  calculateGrandTotal();
}

function prepareSubmission() {
    const form = document.getElementById("exportForm");
    const items = document.querySelectorAll(".item-row");
    const submitBtn = document.querySelector('button[name="bulk_resolve"]');
    if (items.length === 0) {
        alert(" Error: There are no items on the list!");
        return false;
    }


    const inputsToValidate = form.querySelectorAll('input:not([type="hidden"]), select, textarea');
    let isFormValid = true;
    let firstError = null;

    inputsToValidate.forEach(input => {
        const val = input.value.trim();
        const isInvalid = val === "" || (input.type === "number" && parseFloat(val) <= 0);

        if (isInvalid) {
            isFormValid = false;
            input.style.border = "2px solid #ef4444"; 
            input.style.backgroundColor = "#fff1f2";
            if (!firstError) firstError = input;
        } else {
            input.style.border = "1px solid #cbd5e1";
            input.style.backgroundColor = "white";
        }
    });

    if (!isFormValid) {
        alert(" Incomplete Form: Please fill in all the fields.");
        if (firstError) firstError.focus();
        return false;
    }


    const genRef = document.getElementById('gen_ref').value;
    const suffix = document.getElementById('admin_suffix').value.trim();
    document.getElementById('final_ref').value = suffix ? `${genRef}-${suffix}` : genRef;

    // CONFIRM & SYNC
    if (confirm("Confirm Purchase Request: This action will reconcile the inventory and generate the Excel report.")) {
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerText = "Processing...";
        }
        setTimeout(() => { 
            window.location.reload(); 
        }, 2500);

        return true; 
    }

    return false;
}


document.addEventListener('input', function(e) {
    if (e.target.classList.contains('pr-input-style')) {
        if (e.target.value.trim() !== "") {
            e.target.style.border = "1px solid #cbd5e1";
            e.target.style.backgroundColor = "white";
        }
    }
});
function closePRModal() {
  document.getElementById("prModal").style.display = "none";
  document.getElementById("exportForm").reset();
}
