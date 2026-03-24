<?php
$usd_rate = 1; 
$jpy_rate = 1;
$res_settings = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('dollar_rate', 'yen_rate')");

while($row = $res_settings->fetch_assoc()) {
    if ($row['setting_key'] == 'dollar_rate') $usd_rate = $row['setting_value'];
    if ($row['setting_key'] == 'yen_rate') $jpy_rate = $row['setting_value'];
}

date_default_timezone_set('Asia/Manila'); 

$current_month = date('Y-m');
$res_count = $conn->query("SELECT COUNT(*) as total_this_month FROM pr_reports WHERE date_created LIKE '$current_month%'");
$row_count = $res_count->fetch_assoc();
$next_num = ($row_count['total_this_month'] ?? 0) + 1;
$report_num = str_pad($next_num, 4, '0', STR_PAD_LEFT);
$generated_ref = "JIG-" . date('Ymd') . "-" . $report_num;
?>

<div id="prModal" class="modal-overlay">
    <div class="pr-modal-content">
        
        <h3 id="modal_title" class="pr-modal-title">
            📝 Prepare Purchase Request
        </h3>

        <form id="exportForm" method="POST" action="../../prs_gen.php" target="_blank" onsubmit="return prepareSubmission();">
            <input type="hidden" name="form_mode" id="form_mode" value="create">
            <input type="hidden" name="ref_number" id="final_ref">

            <div class="pr-form-grid">
                
                <div class="span-2">
                    <label class="pr-section-label">Reference Number & Suffix</label>
                    <div class="flex-row">
                        <input type="text" id="gen_ref" class="pr-input-style pr-input-readonly" style="flex:2;" value="<?= $generated_ref ?>" readonly>
                        <input type="text" id="admin_suffix" name="admin_suffix" class="pr-input-style" placeholder="Suffix (e.g. URGENT)" style="flex:1;">
                    </div>
                </div>

                <div>
                    <label class="pr-section-label">PR Date</label>
                    <input type="date" name="pr_date" id="modal_pr_date" class="pr-input-style" value="<?= date('Y-m-d') ?>" >
                </div>

                <div>
                    <label class="pr-section-label">Attention to</label>
                    <input list="company_options" name="company" id="modal_company" class="pr-input-style" >
                </div>

                <div class="span-2 flex-between">
                    <label class="pr-item-details-label">📦 Item Details</label>
                </div>

                <div class="span-2 pr-table-container">
                    <div class="pr-items-grid pr-header-labels">
                        <div class="text-center">No.</div>
                        <div>Item Name</div>
                        <div>Description/Specs</div>
                        <div>Maker</div>
                        <div>UOM</div>
                        <div class="text-center">Qty</div>
                        <div>Unit Price</div>
                        <div class="text-right pr-padding-right">Subtotal</div>
                        <div></div>
                    </div>
                    
                    <div id="items_list_body">
                        </div>
                </div>

                <div>
                    <label class="pr-section-label">Currency</label>
                    <select name="currency" id="currency_type" class="pr-input-style" onchange="calculateGrandTotal()">
                        <option value="PHP" data-rate="1">PHP (₱)</option>
                        <option value="USD" data-rate="<?= $usd_rate ?>">USD ($)</option>
                        <option value="JPY" data-rate="<?= $jpy_rate ?>">JPY (¥)</option>
                    </select>
                </div>

                <div>
                    <label class="pr-section-label">RM / FG</label>
                    <input list="RMFG_options" name="rm_fg" id="modal_rmfg" class="pr-input-style" >
                </div>
                
                <div>
                    <label class="pr-section-label">Type</label>
                    <input list="ToR_options" name="ToR" id="modal_tor" class="pr-input-style" >
                </div>
                
                <div>
                    <label class="pr-section-label">Total Amount (<span id="currency_label">PHP</span>)</label>
                    <input type="number" step="0.01" id="pr_total" name="total_amount" class="pr-input-style pr-total-input" readonly>
                </div>

                <div class="span-2">
                    <label class="pr-section-label">Remarks</label>
                    <textarea name="remarks" id="modal_remarks" class="pr-input-style pr-remarks-area" placeholder="Notes..."></textarea>
                </div>
                
                <div class="span-2 flex-footer">
                    <button type="button" onclick="closePRModal()" class="btn-cancel">Cancel</button>
                    <button type="submit" name="bulk_resolve" class="btn-save">
                        Save & Download
                    </button>
                </div>
            </div>

            <datalist id="company_options"><option value="Ms Carla"><option value="Mr Mark Agno"></datalist>
            <datalist id="uom_options"><option value="PC/s"><option value="Sheet"><option value="Roll"></datalist>
            <datalist id="RMFG_options"><option value="✔"><option value="✖"></datalist>
            <datalist id="ToR_options"><option value="Machinery"><option value="Supplies"></datalist>
            <datalist id="maker_options"><option value="Samsung"><option value="TDK"><option value="Local"></datalist>
        </form>
    </div>
</div>