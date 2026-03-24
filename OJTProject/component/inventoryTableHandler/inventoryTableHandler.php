<?php if ($result && $result->num_rows > 0): ?>
    <?php include "../../component/inventoryTableHandler/inventoryTableHandlerHeader.php";?>
    <hr>
   <div class="table-wrapper" style="max-height: 500px; overflow-y: auto; border: 1px solid #ccc; border-radius: 8px;">
        <table class="inventory-table" id="inventoryTable">
            <thead>
                <tr>
                    <?php foreach($cols as $index => $col): ?>
                        <th onclick="sortTable(<?= $index ?>)" data-column="<?= $col ?>" style="cursor: pointer;">
                            <div class="th-content">
                                <?php 
                                    $displayHeader = str_replace('_', ' ', $col);
                                    echo ($col === 'is_acknowledged') ? 'Status' : ucfirst($displayHeader); 
                                ?> 
                                <span class="material-symbols-outlined" style="font-size: 16px;">unfold_more</span>
                            </div>
                        </th>
                    <?php endforeach; ?>
                    <th data-column="total_value">Total Value</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $result->data_seek(0); 
                while($row = $result->fetch_assoc()): 
                    $qty = (int)($row['quantity'] ?? 0);
                    $minQty = (int)($row['min_quantity'] ?? 0);
                    $isAck = (int)($row['is_acknowledged'] ?? 0);
                    $rowTotal = (float)$qty * (float)$row['price'];
                    $isCritical = ($qty <= $minQty);
                ?>
                    <tr data-id="<?= $row['id'] ?>" class="<?= ($qty <= $minQty && $qty > 0) ? 'critical-row' : ($qty <= 0 ? 'out-of-stock-row' : '') ?>">
                        <?php foreach($cols as $col): ?>
                            <td><?php include "inventoryCells.php"; ?></td>
                        <?php endforeach; ?>
                        <td class="total-val">₱<?= number_format($rowTotal, 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>
    <div class="no-data-wrapper" style="text-align:center; padding:80px; background:#fff; border-radius:12px; border:1px dashed #ccc; margin-top:20px;">
        <span class="material-symbols-outlined" style="font-size:80px; color:#ddd;">inventory_2</span>
        <h3>No records found for <?= date("F", mktime(0, 0, 0, $selectedMonth, 1)) ?> <?= $selectedYear ?></h3>
        
        <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center; align-items: center;">
            <button id="openBtnEmpty" class="opnbtn" style="margin: 0; display: flex; align-items: center; justify-content: center; height: 40px; padding: 0 20px;">
                Add First Item
            </button>

            <form method="POST" style="display: inline; margin: 0;">
                <input type="hidden" name="month" value="<?= $selectedMonth ?>">
                <input type="hidden" name="year" value="<?= $selectedYear ?>">
                <button type="submit" name="carryOverAction" class="carry-over-btn">
                    Carry Over from Last Month
                </button>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php include "../../component/inventoryModal/inventoryModal.php"; ?>   