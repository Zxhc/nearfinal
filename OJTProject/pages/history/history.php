<?php
require_once "../../include/config.php";
require_once "../../include/auth_checker.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$m = $_SESSION['selected_month'] ?? (int)date('n');
$y = $_SESSION['selected_year'] ?? (int)date('Y');

$columnResult = $conn->query("SHOW COLUMNS FROM history");
$cols = [];
if ($columnResult) {
    while($c = $columnResult->fetch_assoc()){
        $cols[] = $c['Field'];
    }
}

$groupedItems = [];
$sql = "SELECT * FROM history 
        WHERE MONTH(date) = $m 
        AND YEAR(date) = $y 
        ORDER BY item ASC, description ASC, date DESC, id DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $itemName = $row['item'] ?? 'Unknown Item';
        $desc = $row['description'] ?? 'No Description';
        $groupKey = $itemName . " | " . $desc;
        $groupedItems[$groupKey][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="../inventory/inventory.css"> 
    <link rel="stylesheet" href="history.css">
    <link rel="stylesheet" href="../../component/settings/settings.css">
    <link rel="stylesheet" href="../../component/navbar/nav-bar.css"> 
    <link rel="stylesheet" href="../../component/inventoryTableHandler/inventoryTableHandler.css"> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />  
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />

    <title>HEPC JIG IMS | History</title>
    
    
</head>
<body>
    <?php include "../../component/navbar/nav-bar.php"; ?>

   <div class="inventory-container">
        <div class="inv-header">
            <div>
                <h2 class="header-title">Transaction History</h2>
                <p class="header-subtitle">
                    Records for: <span class="selected-date-span"><?= date('F', mktime(0, 0, 0, $m, 1)) ?> <?= $y ?></span>
                </p>
            </div>
            
            <div class="btnContainer">
                <div class="search-box">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" id="historySearch" placeholder="Search history..." onkeyup="historyLiveSearch()">
                </div>

                <button type="button" class="excel-btn hide-on-mobile" onclick="exportSelectedHistory()">
                    <span class="material-symbols-outlined">download</span> Export to Excel
                </button>
            </div>
        </div>

        <div class="table-wrapper">
            <div id="historyTableContainer">
                <?php if (!empty($groupedItems)): ?>
                    <?php foreach ($groupedItems as $groupKey => $transactions): ?>
                        <div class="item-group-wrapper">
                            <h3 class="item-title"><?= htmlspecialchars($groupKey) ?></h3>
                            <table class="inventory-table">
                                <thead>
                                    <tr>
                                        <?php foreach($cols as $col): ?>
                                            <?php if (in_array(strtolower($col), ['id', 'description', 'item'])) continue; ?> 
                                            <th><?= ucfirst(str_replace('_', ' ', $col)) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $row): ?>
                                        <tr>
                                            <?php foreach ($cols as $col): ?>
                                                <?php if (in_array(strtolower($col), ['id', 'description', 'item'])) continue; ?> 
                                                <td class="<?= ($col == 'date') ? 'td-date' : '' ?>">
                                                    <?php 
                                                        $val = $row[$col];
                                                        if ($val === null || trim((string)$val) === '') {
                                                            $numericCols = ['quantity_in', 'quantity_out', 'min_quantity', 'remaining'];
                                                            echo in_array($col, $numericCols) ? '0' : '<span class="na-text">N/A</span>';
                                                        } else {
                                                            $cleanVal = htmlspecialchars($val);
                                                            if ($col == 'action') {
                                                                echo "<span class='status-badge'>$cleanVal</span>";
                                                            } else {
                                                                echo $cleanVal;
                                                            }
                                                        }
                                                    ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="item-group-wrapper no-records-wrapper">
                        <span class="material-symbols-outlined no-records-icon">history_toggle_off</span>
                        <h3 class="no-records-text">No history records found</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php include "../../component/settings/settings.php"; ?>
         <script src="../../component/settings/settings.js"></script></script>

    <script src="../../component/search.js"></script>
    <script>
      

        function exportSelectedHistory() {
            const selected = document.querySelectorAll('.history-checkbox:checked');
            const ids = Array.from(selected).map(cb => cb.value);
            const m = "<?= $m ?>";
            const y = "<?= $y ?>";

            let exportUrl = `./historyExport.php?month=${m}&year=${y}`;
            if (ids.length > 0) {
                exportUrl += `&ids=${ids.join(',')}`;
            }
            window.location.href = exportUrl;
        }
    </script>
</body>
</html>