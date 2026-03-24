<?php
require_once "../../include/config.php"; 
require_once "../../include/auth_checker.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status_manual'])) {
    $ref = $_POST['ref_number'];
    $new_status = $_POST['new_status'];

    $update_stmt = $conn->prepare("UPDATE pr_reports SET status = ? WHERE ref_number = ?");
    $update_stmt->bind_param("ss", $new_status, $ref);
    
    if ($update_stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}


if (isset($_GET['filter_month'])) {
    $_SESSION['selected_month'] = date('n', strtotime($_GET['filter_month']));
    $_SESSION['selected_year'] = date('Y', strtotime($_GET['filter_month']));
}

$m = $_SESSION['selected_month'] ?? (int)date('n');
$y = $_SESSION['selected_year'] ?? (int)date('Y');
$status_filter = $_GET['status_filter'] ?? 'all';


function getStatusClass($status) {
    $s = strtolower(trim($status ?? ''));
    switch ($s) {
        case 'cancelled': return 'status-cancelled';
        case 'follow up': return 'status-follow-up';
        case 'hold': return 'status-hold';
        case 'on process': return 'status-on-process';
        case 'ready for reporting': return 'status-done';
        case 'production office': return 'status-production';
        case 'received': return 'status-received';
        default: return 'status-default';
    }
}


$count_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'On Process' THEN 1 ELSE 0 END) as on_process,
    SUM(CASE WHEN status = 'Hold' THEN 1 ELSE 0 END) as hold,
    SUM(CASE WHEN status = 'Follow Up' THEN 1 ELSE 0 END) as follow_up,
    SUM(CASE WHEN status = 'Production Office' THEN 1 ELSE 0 END) as production,
    SUM(CASE WHEN status = 'Ready for Reporting' THEN 1 ELSE 0 END) as ready_reporting,
    SUM(CASE WHEN status = 'Received' THEN 1 ELSE 0 END) as received,
    SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM pr_reports 
    WHERE MONTH(pr_date) = $m AND YEAR(pr_date) = $y";
$counts_res = $conn->query($count_sql);
$counts = $counts_res ? $counts_res->fetch_assoc() : [];


$where_clause = "WHERE MONTH(r.pr_date) = $m AND YEAR(r.pr_date) = $y";
if ($status_filter !== 'all') {
    $where_clause .= " AND r.status = '" . $conn->real_escape_string($status_filter) . "'";
}

$where_clause = "WHERE MONTH(r.pr_date) = $m AND YEAR(r.pr_date) = $y";

if ($status_filter !== 'all') {
    $where_clause .= " AND r.status = '" . $conn->real_escape_string($status_filter) . "'";
}
$sql = "SELECT r.*, 
        GROUP_CONCAT(i.material_name SEPARATOR ', ') as all_materials,
        GROUP_CONCAT(i.description SEPARATOR ', ') as all_descriptions
        FROM pr_reports r
        LEFT JOIN pr_items i ON r.ref_number = i.pr_ref_number
        $where_clause
        GROUP BY r.pr_id
        ORDER BY r.pr_id DESC";

$result = $conn->query($sql); 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEPC JIG IMS | Purchase Request Status</title>
    
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="prsStatus.css">
    <link rel="stylesheet" href="../../component/prsModal/prsModal.css">
    <link rel="stylesheet" href="../../pages/inventory/inventory.css"> 
    <link rel="stylesheet" href="../../component/navbar/nav-bar.css"> 
    <link rel="stylesheet" href="../../component/inventoryTableHandler/inventoryTableHandler.css"> 
    <link rel="stylesheet" href="../../component/settings/settings.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" /> 
</head>
<body>
    <?php include "../../component/navbar/nav-bar.php"; ?>

    <div class="inventory-container list-mode" id="mainContainer">
        <div class="inv-header">
            <div>
                <h2 style="font-weight: 800; color: #072d7a;">PRS STATUS</h2>
                <p style="color: #64748b; font-size: 0.85rem;">
                    Monitoring records for <strong><?= date('F Y', mktime(0, 0, 0, $m, 1, $y)) ?></strong>
                </p>
            </div>
            
            <div class="btn-container">
                <div class="search-box">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" id="inventorySearch" placeholder="Search Reference..." onkeyup="filterTable()">
                </div>
                
                <div class="view-toggle">
                    <a href="#" id="listView" class="toggle-btn active"><span class="material-symbols-outlined">format_list_bulleted</span>List</a>
                    <a href="#" id="gridView" class="toggle-btn"><span class="material-symbols-outlined">grid_view</span>Grid</a>
                </div>
            </div>
        </div>

        <hr>

        <div class="summary-wrapper">
            <div class="stat-card card-total"><h4>Total</h4><div class="count"><?= $counts['total'] ?? 0 ?></div></div>
            <div class="stat-card card-on-process"><h4>On Process</h4><div class="count"><?= $counts['on_process'] ?? 0 ?></div></div>
            <div class="stat-card card-hold"><h4>Hold</h4><div class="count"><?= $counts['hold'] ?? 0 ?></div></div>
            <div class="stat-card card-follow-up"><h4>Follow Up</h4><div class="count"><?= $counts['follow_up'] ?? 0 ?></div></div>
            <div class="stat-card card-production"><h4>Production</h4><div class="count"><?= $counts['production'] ?? 0 ?></div></div>
            <div class="stat-card card-ready"><h4>Ready</h4><div class="count"><?= $counts['ready_reporting'] ?? 0 ?></div></div>
            <div class="stat-card card-received"><h4>Received</h4><div class="count"><?= $counts['received'] ?? 0 ?></div></div>
            <div class="stat-card card-cancelled"><h4>Cancelled</h4><div class="count"><?= $counts['cancelled'] ?? 0 ?></div></div>
        </div>

        <div class="control-bar">
            <form method="GET" class="filter-section">
                <div class="filter-item">
                    <label>Period</label>
                    <input type="month" name="filter_month" value="<?= $y ?>-<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>">
                </div>
                <div class="filter-item">
                    <label>Status</label>
                    <select name="status_filter">
                        <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>All Records</option>
                        <option value="On Process" <?= $status_filter == 'On Process' ? 'selected' : '' ?>>On Process</option>
                        <option value="Hold" <?= $status_filter == 'Hold' ? 'selected' : '' ?>>Hold</option>
                        <option value="Follow Up" <?= $status_filter == 'Follow Up' ? 'selected' : '' ?>>Follow Up</option>
                        <option value="Production Office" <?= $status_filter == 'Production Office' ? 'selected' : '' ?>>Production Office</option>
                        <option value="Ready for Reporting" <?= $status_filter == 'Ready for Reporting' ? 'selected' : '' ?>>Ready for Reporting</option>
                        <option value="Received" <?= $status_filter == 'Received' ? 'selected' : '' ?>>Received</option>
                        <option value="Cancelled" <?= $status_filter == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn-apply">Apply Filter</button>
                <a href="PRSStatus.php" class="btn-reset">Reset</a>
            </form>

            <div class="action-section">
            <button class="btn-action" id="reuseBtn" onclick="toggleReuseMode()" style="background-color: #28a745;">
                <span class="material-symbols-outlined" style="font-size: 18px;">history</span> 
                <span id="reuseText">Reuse</span>
            </button>
            <button type="button" onclick="openNewPRModal()" style="background:#072d7a; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:8px;">
                <span class="material-symbols-outlined">add_circle</span>
                Create New PR
            </button>
                </div>
            </div> <div class="status-content">
                <?php include __DIR__ . "/prsTableView.php"; ?>
            </div>

            <?php include __DIR__ . "/../../component/settings/settings.php"; ?>
            <?php include "../../component/prsModal/prsModal.php"; ?>  

        </div> 
        <script src="../../component/prsModal/prsModal.js"></script>
        <script src="../../component/settings/settings.js"></script>
        <script src="../../component/search.js"></script>
        <script src="./prsStatus.js"></script>

    <script>
        function filterTable() {
            let input = document.getElementById("inventorySearch").value.toLowerCase();
            let rows = document.querySelectorAll("#inventoryTable tbody tr");
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? "" : "none";
            });
        }
    </script>
</body>
</html>