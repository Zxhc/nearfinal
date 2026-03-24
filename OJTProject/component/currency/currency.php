<?php
include_once __DIR__ . '/../../include/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$d_rate = 0;
$y_rate = 0;
$peso = 0;

// --- FIXED LOGIC: Default to Current Month if Session is not set ---
// Ito ay para mag-match sa inventory_logic.php mo
$selectedMonth = isset($_SESSION['selected_month']) ? (int)$_SESSION['selected_month'] : (int)date('n');
$selectedYear = isset($_SESSION['selected_year']) ? (int)$_SESSION['selected_year'] : (int)date('Y');

// Handle Rate Updates (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_dollar'])) {
        $new_rate = (float)$_POST['new_dollar_rate'];
        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'dollar_rate'");
        $stmt->bind_param("d", $new_rate);
        $stmt->execute();
        $stmt->close();
    }
    if (isset($_POST['update_yen'])) {
        $new_rate = (float)$_POST['new_yen_rate'];
        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'yen_rate'");
        $stmt->bind_param("d", $new_rate);
        $stmt->execute();
        $stmt->close();
    }
    // Refresh page para makita agad ang bagong rate
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch rates STRICTLY from Database
$settingsResult = $conn->query("SELECT * FROM settings");
if ($settingsResult && $settingsResult->num_rows > 0) {
    while($row = $settingsResult->fetch_assoc()) {
        if ($row['setting_key'] === 'dollar_rate') $d_rate = (float)$row['setting_value'];
        if ($row['setting_key'] === 'yen_rate') $y_rate = (float)$row['setting_value'];
    }
}

// Run Inventory Query - Ngayon laging may value ito (Current Month default)
$totalQuery = $conn->query("SELECT SUM(quantity * price) AS grand_total FROM inventory WHERE MONTH(date_created) = $selectedMonth AND YEAR(date_created) = $selectedYear");
if ($totalQuery) {
    $invRow = $totalQuery->fetch_assoc();
    $peso = (float)($invRow['grand_total'] ?? 0);
}

$dollarTotal = ($d_rate > 0) ? ($peso / $d_rate) : 0;
$yenTotal    = ($y_rate > 0) ? ($peso / $y_rate) : 0; 
?>

<div class="box-content" id="box-dollar">
    <div class="inner-box" id="inner-dollar">
        <div class="box-header">
            <span>Dollar Rate ($1 = ₱<?= number_format($d_rate, 2) ?>)</span>
            <form class="set-rate" method="POST">
                <input type="number" step="0.01" name="new_dollar_rate" placeholder="rate" required>
                <button type="submit" name="update_dollar">Set</button>
            </form> 
        </div>
       <h1 class="count-up" data-target="<?= $dollarTotal ?>">$0.00</h1>

    </div>
</div>

<div class="box-content" id="box-yen">
    <div class="inner-box" id="inner-yen">
        <div class="box-header">
            <span>Yen Rate (¥1 = ₱<?= number_format($y_rate, 2) ?>)</span>
            <form class="set-rate" method="POST">
                <input type="number" step="0.01" name="new_yen_rate" placeholder="rate" required>
                <button type="submit" name="update_yen">Set</button>
            </form> 
        </div>
       <h1 class="count-up" data-target="<?= $yenTotal ?>">¥0.00</h1>

    </div>
</div>