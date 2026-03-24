<?php
include_once __DIR__ . '/../../include/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userId = $_SESSION['user_id'] ?? null; 
$currentUser = 'System'; 
if ($userId) {
    $userStmt = $conn->prepare("SELECT full_name FROM users WHERE id = ? LIMIT 1");
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $userRes = $userStmt->get_result();
    if ($userRow = $userRes->fetch_assoc()) {
        $currentUser = $userRow['full_name'];
        $_SESSION['full_name'] = $currentUser; 
    }
}

// --- SESSION FIX (Default to Last Selected) ---
if (isset($_GET['month'])) {
    $_SESSION['selected_month'] = (int)$_GET['month'];
} elseif (!isset($_SESSION['selected_month'])) {
    $_SESSION['selected_month'] = (int)date('n'); 
}

if (isset($_GET['year'])) {
    $_SESSION['selected_year'] = (int)$_GET['year'];
} elseif (!isset($_SESSION['selected_year'])) {
    $_SESSION['selected_year'] = (int)date('Y'); 
}

$selectedMonth = $_SESSION['selected_month'];
$selectedYear = $_SESSION['selected_year'];

// --- DATABASE HELPER ---
$cols = ['category', 'cabinet', 'item', 'description', 'beginning_inventory', 'received_qty', 'quantity', 'min_quantity', 'price', 'is_acknowledged'];

function verifyColumn($conn, $colName){
    $colName = preg_replace('/[^a-zA-Z0-9_]/', '', strtolower($colName));
    $protected = ['id', 'total', 'action', 'select', 'unfold_more'];
    
    if(empty($colName) || in_array($colName, $protected)) return null;

    $check = $conn->query("SHOW COLUMNS FROM inventory LIKE '$colName'");
    if($check->num_rows == 0){
        if($colName === 'is_acknowledged'){
            $conn->query("ALTER TABLE inventory ADD COLUMN `is_acknowledged` TINYINT(1) DEFAULT 0");
        } elseif($colName === 'item_uuid'){
            $conn->query("ALTER TABLE inventory ADD COLUMN `item_uuid` VARCHAR(50) DEFAULT NULL");
            $conn->query("ALTER TABLE inventory ADD INDEX (item_uuid)");
        } else {
            $type = (strpos($colName, 'qty') !== false || strpos($colName, 'inventory') !== false) ? "INT DEFAULT 0" : "VARCHAR(255) DEFAULT NULL";
            $conn->query("ALTER TABLE inventory ADD COLUMN `$colName` $type");
        }
    }
    return $colName;
}

verifyColumn($conn, 'is_acknowledged');
verifyColumn($conn, 'item_uuid');

// --- FORM ACTIONS ---

// ADD ITEM / RESTOCK
if (isset($_POST['addItem'])){
    $category = $_POST['category']; 
    $item = $_POST['item'];
    $description = $_POST['description']; 
    $cabinet = $_POST['cabinet'];         
    $input_qty = (int)$_POST['quantity'];
    $min_quantity = (int)$_POST['min_quantity']; 
    $price = (float)$_POST['price'];
    $targetDate = "$selectedYear-" . str_pad($selectedMonth, 2, "0", STR_PAD_LEFT) . "-01 00:00:00";

    $checkQty = $conn->prepare("SELECT quantity FROM inventory WHERE item = ? AND cabinet = ? LIMIT 1");
    $checkQty->bind_param("ss", $item, $cabinet);
    $checkQty->execute();
    $resQty = $checkQty->get_result();
    $rowQty = $resQty->fetch_assoc();
    $oldQty = ($rowQty) ? (int)$rowQty['quantity'] : 0;
    $finalStock = $oldQty + $input_qty; // <--- DITO NATIN DEFINE SI FINALSTOCK


    // --- UUID LOOKUP: NUUID ---
    $stmtUUID = $conn->prepare("SELECT item_uuid FROM inventory WHERE item = ? AND cabinet = ? AND item_uuid IS NOT NULL AND item_uuid != '' LIMIT 1");
    $stmtUUID->bind_param("ss", $item, $cabinet);
    $stmtUUID->execute();
    $resUUID = $stmtUUID->get_result();
    $existing = $resUUID->fetch_assoc();
    $item_uuid = ($existing) ? $existing['item_uuid'] : uniqid('JIG-');

    $stmt = $conn->prepare("INSERT INTO inventory 
        (category, item, description, cabinet, beginning_inventory, received_qty, quantity, min_quantity, price, date_created, is_acknowledged, item_uuid) 
        VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?, ?, 0, ?) 
        ON DUPLICATE KEY UPDATE 
            received_qty = received_qty + VALUES(beginning_inventory), 
            quantity = quantity + VALUES(beginning_inventory),
            min_quantity = VALUES(min_quantity), 
            description = VALUES(description),
            is_acknowledged = CASE WHEN (quantity + VALUES(beginning_inventory)) <= VALUES(min_quantity) THEN 0 ELSE is_acknowledged END,
            item_uuid = COALESCE(item_uuid, VALUES(item_uuid))"); 
    
    $stmt->bind_param("ssssiiidss", 
        $category, $item, $description, $cabinet, $input_qty, 
        $input_qty, $min_quantity, $price, $targetDate, $item_uuid
    );
    
   if($stmt->execute()){
        
        $stmtHist = $conn->prepare("INSERT INTO history (name, item, description, quantity_in, quantity_out, min_quantity, remaining) VALUES (?, ?, ?, ?, 0, ?, ?)");
        $stmtHist->bind_param("sssiii", $currentUser, $item, $description, $input_qty, $min_quantity, $finalStock);
        $stmtHist->execute();
    }

    header("Location: inventory.php?keepOpen=1");
    exit();
}


// JSON BULK/INLINE EDIT
$input = file_get_contents('php://input');
$json = json_decode($input, true);
if (isset($json['updateData'])){
    foreach($json['updateData'] as $row){
        $id = (int)$row['id'];
        $current = $conn->query("SELECT item, description, quantity, min_quantity FROM inventory WHERE id = $id")->fetch_assoc();
        $oldQty = (int)($current['quantity'] ?? 0);
        $oldMin = (int)($current['min_quantity'] ?? 0);
        $itemName = $current['item'];
        $itemDesc = $current['description'];

        $updateParts = [];
        foreach($row as $key => $val){
            $cleanCol = verifyColumn($conn, $key);
            if ($cleanCol && !in_array($cleanCol, ['is_acknowledged', 'item_uuid', 'id'])){
               $val = str_replace(',', '', $val); 
                $cleanVal = $conn->real_escape_string($val);
                $updateParts[] = "`$cleanCol` = '$cleanVal'";
                
            }
        }

        if(!empty($updateParts)){
            $newQty = isset($row['quantity']) ? (int)$row['quantity'] : $oldQty;
            $newMin = isset($row['min_quantity']) ? (int)$row['min_quantity'] : $oldMin;
            if ($newQty != $oldQty) {
                $qty_in = ($newQty > $oldQty) ? ($newQty - $oldQty) : 0;
                $qty_out = ($newQty < $oldQty) ? ($oldQty - $newQty) : 0;

                $stmtHist = $conn->prepare("INSERT INTO history (name, item, description, quantity_in, quantity_out, min_quantity, remaining) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmtHist->bind_param("sssiiii", $currentUser, $itemName, $itemDesc, $qty_in, $qty_out, $newMin, $newQty);
                $stmtHist->execute();
            }

            if ($newQty <= $newMin && $oldQty > $newMin) {
                $updateParts[] = "`is_acknowledged` = 0";
            }

            $conn->query("UPDATE inventory SET " . implode(', ', $updateParts) . " WHERE id = $id");
            
            if (isset($row['quantity']) && $row['quantity'] > $oldQty) {
                $addedAmount = (int)$row['quantity'] - $oldQty;
                $conn->query("UPDATE inventory SET `received_qty` = `received_qty` + $addedAmount WHERE id = $id");
            }
        }
    }
    echo json_encode(["status" => "success"]);
    exit();
}

// BULK DELETE
if(isset($_POST['bulkDelete'])){
    if(!empty($_POST['selectedItems'])){
        $ids = implode(',', array_map('intval', $_POST['selectedItems']));
        $conn->query("DELETE FROM inventory WHERE id IN ($ids)");
    }
    header("Location: inventory.php");
    exit();
}

// --- DATA RETRIEVAL ---
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $conn->real_escape_string($_GET['sort']) : 'id';
$order = (isset($_GET['order']) && $_GET['order'] == 'ASC') ? 'ASC' : 'DESC';

$sql = "SELECT * FROM inventory WHERE 
        (item LIKE '%$search%' OR category LIKE '%$search%') 
        AND MONTH(date_created) = $selectedMonth 
        AND YEAR(date_created) = $selectedYear
        ORDER BY $sort $order";
$result = $conn->query($sql);

$totalQuery = $conn->query("SELECT SUM(quantity * price) AS grand_total FROM inventory WHERE MONTH(date_created) = $selectedMonth AND YEAR(date_created) = $selectedYear");
$grandTotal = ($totalQuery) ? (float)($totalQuery->fetch_assoc()['grand_total'] ?? 0) : 0;

// --- AUTO-PURGE OLD DATA ---
$YearsAgo = date('Y-m-d', strtotime('-10 years'));
$purgeSql = "DELETE FROM inventory WHERE date_created < '$YearsAgo'";
$conn->query($purgeSql);
?>