<div class="modal" id="modal">
    <div class="modal-content">
        <button id="closeBtn" style="float:right; background:red; color:white; border:none; padding:5px 10px; cursor:pointer;">X</button>
        <h2>Manage Inventory</h2>
        
        <form id="addItemForm" method="POST">
            <div class="form-group">
                <input type="hidden" name="selected_month" value="<?= $selectedMonth ?>">
                <input type="text" name="category" placeholder="Category..." class="pop-input" required />
                <input type="text" name="item" placeholder="Item Name..." class="pop-input" required />
                <input type="text" name="description" placeholder="Description..." class="pop-input" required />
                <input type="number" name="cabinet" placeholder="Cabinet..." class="pop-input" required />
                <input type="number" name="quantity" placeholder="QTY..." class="pop-input" required />
                <input type="number" name="min_quantity" placeholder="Min Quantity..." class="pop-input" required />
                <input type="number" step="0.01" name="price" placeholder="Price..." class="pop-input" required />
                <button type="submit" name="addItem" class="add-btn">Add Item</button>
            </div>
        </form>

        <hr style="margin: 20px 0;">
        <div class="modal-func">
         <div class="search-box">
                <span class="material-symbols-outlined">search</span>
                <input type="text" id="modalInventorySearch" 
                    placeholder="Search in modal..." 
                    onkeyup="modalLiveSearch()"> 
            </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 10px;">
            <a href="../../component/qrGeneration/qr_gen.php" style="text-decoration: none;">
                <button type="button" class="qr-btn-container" style="background: #333; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 5px; height:40px;">
                    <span class="material-symbols-outlined">qr_code_2</span>
                    <span>Generate QR</span>
                </button>
            </a>
            
            <button type="submit" form="bulkDeleteForm" name="bulkDelete" onclick="return confirm('Delete selected?')" style="background:#ed0505; color:white; height:40px; border:none; padding:8px 15px; cursor:pointer; border-radius:5px; font-weight:bold;">Delete</button>
            
            <div id="modal-actions"></div>
        </div>
        </div>
        <form id="bulkDeleteForm" method="POST">
            <input type="hidden" name="selected_month" value="<?= $selectedMonth ?>">
            <div id="modal-table-container" style="max-height: 500px; overflow-y: auto; border: 1px solid #ddd; margin-top: 10px; position: relative;"></div>
        </form>
        
    </div>   
</div>