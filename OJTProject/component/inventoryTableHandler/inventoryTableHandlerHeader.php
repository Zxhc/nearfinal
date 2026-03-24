<div class="inv-header">
    <div class="header-left">
        <p class="grand-total-card">
            <span class="label">Monthly Grand Total</span>
            <span class="amount count-up" data-target="<?= $grandTotal ?>">₱<?= number_format($grandTotal, 2) ?></span>
        </p>
    </div>
    
    <div class="header-right-actions">
        <div class="btn-container">
            
            <div class="search-box">
                <span class="material-symbols-outlined">search</span>
                <input type="text" id="inventorySearch" 
                       placeholder="Search..." 
                       onkeyup="liveSearch()" 
                       onkeypress="if(event.key === 'Enter') window.location.href='?search=' + this.value">
            </div>

            <a href="/OJTProject/component/inventoryTableHandler/inventoryExport.php" style="text-decoration: none;">
                <button type="button" class="excel-btn hide-on-mobile">
                    <span class="material-symbols-outlined">download</span> Export
                </button>
            </a>

            <button id="openBtn" class="opnbtn hide-on-mobile">
                <span class="material-symbols-outlined">add</span> Add / Edit
            </button>
        </div>
    </div>
</div>