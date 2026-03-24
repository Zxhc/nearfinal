<?php

$m = isset($_SESSION['selected_month']) ? (int)$_SESSION['selected_month'] : (int)date('n');
$y = isset($_SESSION['selected_year']) ? (int)$_SESSION['selected_year'] : (int)date('Y');


$alertSql = "SELECT id, item, description, cabinet, quantity, min_quantity 
             FROM inventory 
             WHERE quantity <= min_quantity 
             AND is_acknowledged = 0 
             AND MONTH(date_created) = $m 
             AND YEAR(date_created) = $y 
             ORDER BY quantity ASC";


$alertResult = $conn->query($alertSql);
$pendingCount = ($alertResult) ? $alertResult->num_rows : 0;


?>
<form method="POST" action="dashBoard.php">
    <div class="box-content box-3 <?php echo ($pendingCount > 0) ? 'has-pending' : ''; ?>" id="history">
        <div class="content-container" id="history-content">
            <?php include "inventoryAlertsHeader.php"?>
            <p style="font-size: 0.85rem; color: #666; margin-top: 5px;">
                <?= ($pendingCount > 0) ? '<strong>ATTENTION:</strong> Critical stock levels detected!' : 'All items are above minimum levels.' ?>
            </p>
            <hr>
            <?php include "inventoryAlertsTable.php"?>
        </div>
    </div>
</form>
