<?php
$usd_rate = 1; $jpy_rate = 1;
$res_settings = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('dollar_rate', 'yen_rate')");
if($res_settings) {
    while($row = $res_settings->fetch_assoc()) {
        if ($row['setting_key'] == 'dollar_rate') $usd_rate = $row['setting_value'];
        if ($row['setting_key'] == 'yen_rate') $jpy_rate = $row['setting_value'];
    }
}


$check_max = "SELECT MAX(pr_id) as last_id FROM pr_reports";
$res_max = $conn->query($check_max);
$row_max = $res_max->fetch_assoc();
$next_number = ($row_max['last_id'] ?? 0) + 1;
$report_num = str_pad($next_number, 4, '0', STR_PAD_LEFT);
$datePart = date('Ymd');
$generated_ref = "JIG-{$datePart}-{$report_num}";
?>
<div id="prModal" class="modal-overlay">
    <div class="modal-content">
        <h3 id="modal_title">📝 Prepare Purchase Request</h3>
        
        <form id="exportForm" method="POST" action="../../prs_gen.php" target="_blank">
            <input type="hidden" name="form_mode" id="form_mode" value="create">
            <input type="hidden" name="ref_number" id="final_ref">

            <div class="modal-form-grid">
                
                <div class="full-width">
                    <label class="modal-label">Reference Number & Suffix</label>
                    <div class="ref-group">
                        <input type="text" id="gen_ref" class="pr-input-style ref-readonly" 
                               value="<?= htmlspecialchars($generated_ref) ?>" readonly>
                        <input type="text" name="admin_suffix" id="admin_suffix" 
                               class="pr-input-style suffix-input" placeholder="Suffix (e.g. URGENT, REUSE)">
                    </div>
                </div>

                <div>
                    <label class="modal-label">PR Date</label>
                    <input type="date" name="pr_date" id="modal_pr_date" class="pr-input-style" value="<?= date('Y-m-d') ?>" >
                </div>

                <div>
                    <label class="modal-label">Attention to</label>
                    <input list="company_options" name="company" id="modal_company" class="pr-input-style" >
                </div>

                <div class="full-width item-details-header">
                    <label>📦 Item Details</label>
                    <button type="button" class="btn-add-item" onclick="addNewRow()">
                        <span>+</span> Add New Item
                    </button>
                </div>

                <div class="full-width items-container">
                    <div class="pr-items-grid pr-header-labels">
                        <div class="text-center">No.</div>
                        <div>Item Name</div>
                        <div>Description/Specs</div>
                        <div>Maker</div>
                        <div>UOM</div>
                        <div class="text-center">Qty</div>
                        <div>Unit Price</div>
                        <div class="text-right">Subtotal</div>
                        <div></div> 
                    </div>
                    
                    <div id="items_list_body">
                        </div>
                </div>

                <div>
                    <label class="modal-label">Currency</label>
                    <select name="currency" id="currency_type" class="pr-input-style" onchange="calculateGrandTotal()">
                        <option value="PHP" data-rate="1">PHP (₱)</option>
                        <option value="USD" data-rate="<?= $usd_rate ?>">USD ($)</option>
                        <option value="JPY" data-rate="<?= $jpy_rate ?>">JPY (¥)</option>
                    </select>
                </div>

                <div>
                    <label class="modal-label">RM / FG</label>
                    <input list="RMFG_options" name="rm_fg" id="modal_rmfg" class="pr-input-style" > 
                </div>

                <div>
                    <label class="modal-label">Types of Requisition</label>
                    <input list="ToR_options" name="ToR" id="modal_tor" class="pr-input-style" >
                </div>

                <div >
                    <label class="modal-label">Grand Total</label>
                    <input type="number" step="0.01" id="pr_total" name="total_amount" class="pr-input-style total-input" readonly>
                </div>

                <div class="full-width">
                    <label class="modal-label">Remarks</label>
                    <textarea name="remarks" id="modal_remarks" class="pr-input-style" style="height:50px; resize:none;"></textarea>
                </div>
                
                <div class="full-width modal-footer">
                    <button type="button" class="btn-cancel" onclick="closePRModal()">Cancel</button>
                    <button type="button" name="bulk_resolve" class="btn-save" onclick="return prepareSubmission();">
                        Save & Download
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<datalist id="RMFG_options"><option value="✔"><option value="✖"></datalist>
<datalist id="ToR_options">
    <option value="Machinery">
    <option value="Maintenance Parts & Supplies">
</datalist>
<datalist id="maker_options">
    <option value="Samsung"><option value="TDK Philippines"><option value="Murata"><option value="Kyocera">
</datalist>

