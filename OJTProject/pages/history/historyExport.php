<?php
ob_start();
include_once __DIR__ . '/../../include/config.php';

if (ob_get_length()) ob_clean();

$selectedIds = isset($_GET['ids']) ? $_GET['ids'] : '';

if (!empty($selectedIds)) {
    $idArray = explode(',', $selectedIds);
    $cleanIds = implode(',', array_map('intval', $idArray));
    $query = "SELECT * FROM history WHERE id IN ($cleanIds) ORDER BY description ASC, date ASC";
} else {
    $m = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
    $y = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
    $query = "SELECT * FROM history WHERE MONTH(date) = $m AND YEAR(date) = $y ORDER BY description ASC, date ASC";
}

$result = $conn->query($query);

$filename = "Wide_Inventory_Report_" . date('Ymd_His') . ".xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");

?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
<style>
   
    .table-style {
        border-collapse: collapse;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        width: 100%; 
    }
    
    .table-style td, .table-style th {
        border: 1px solid #cbd5e0;
        padding: 10px 15px;
        vertical-align: middle;
        white-space: nowrap; 
    }

    .main-header {
        background-color: #1a202c;
        color: #ffffff;
        font-weight: bold;
        text-align: center;
        height: 40px;
    }

    .group-row {
        background-color: #ebf8ff;
        color: #2b6cb0;
        font-weight: bold;
        font-size: 15px;
        height: 35px;
    }

    .even-row { background-color: #f7fafc; }
    .text-center { text-align: center; }
    .text-bold { font-weight: bold; }
    
    .report-title {
        font-size: 24px;
        font-weight: bold;
        color: #1a202c;
        padding-bottom: 10px;
    }
</style>
</head>
<body>

<table>
    <tr>
        <td colspan="9" class="report-title">HEPC JIG IMS - TRANSACTION HISTORY REPORT (WIDE VIEW)</td>
    </tr>
    <tr>
        <td colspan="9" style="color: #718096; font-size: 12px;">Generated on: <?= date('F d, Y h:i A') ?></td>
    </tr>
    <tr><td colspan="9"></td></tr>
</table>

<table class="table-style">
    <thead>
        <tr class="main-header">
            <th style="width: 300px;">DATE</th>
            <th style="width: 300px;">ITEM NAME</th>
            <th style="width: 300px;">USER</th>
            <th style="width: 80px;">IN</th>
            <th style="width: 80px;">OUT</th>
            <th style="width: 300px;">CUSTOMER</th>
            <th style="width: 300px;">EQUIPMENT</th>
            <th style="width: 80px;">REMAINING</th>
            <th style="width: 300px;">REMARKS</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $lastDesc = null;
    $rowCount = 0;

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($lastDesc !== $row['description']) {
                $lastDesc = $row['description'];
                echo '<tr class="group-row">';
                echo '<td colspan="9" style="padding-left: 10px;">ITEM DESCRIPTION: ' . htmlspecialchars(strtoupper($lastDesc)) . '</td>';
                echo '</tr>';
                $rowCount = 0;
            }

            $rowClass = ($rowCount % 2 == 0) ? '' : 'even-row';
            
            echo '<tr class="' . $rowClass . '">';
            echo '<td class="text-center">' . htmlspecialchars($row['date']) . '</td>';
            echo '<td>' . htmlspecialchars($row['item']) . '</td>';
            echo '<td class="text-center">' . htmlspecialchars($row['name']) . '</td>';
            echo '<td class="text-center" style="color: #38a169; font-weight: bold;">' . (int)$row['quantity_in'] . '</td>';
            echo '<td class="text-center" style="color: #e53e3e; font-weight: bold;">' . (int)$row['quantity_out'] . '</td>';
            echo '<td>' . htmlspecialchars($row['customer'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($row['equipment'] ?? 'N/A') . '</td>';
            echo '<td class="text-center text-bold">' . (int)$row['remaining'] . '</td>';
            echo '<td>' . htmlspecialchars($row['remarks']) . '</td>';
            echo '</tr>';
            
            $rowCount++;
        }
    } else {
        echo '<tr><td colspan="9" class="text-center">Walang napiling data.</td></tr>';
    }
    ?>
    </tbody>
</table>

</body>
</html>
<?php
ob_end_flush();
exit();
?>