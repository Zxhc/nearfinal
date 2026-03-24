<?php
require_once '../../include/auth_checker.php';
include "../../include/config.php"; 

$check_max = "SELECT MAX(pr_id) as last_id FROM pr_reports";
$res_max = $conn->query($check_max);
$row_max = $res_max->fetch_assoc();
$next_number = ($row_max['last_id'] ?? 0) + 1;
$report_num = str_pad($next_number, 4, '0', STR_PAD_LEFT);
$datePart = date('Ymd');
$generated_ref = "JIG-{$datePart}-{$report_num}";
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JIG Invetory || Dashboard</title>
    <link rel="stylesheet" href="../../style.css" /> 
    <link rel="stylesheet" href="../../component/navBar/nav-bar.css" />
    <link rel="stylesheet" href="dashBoard.css" />
    <link rel="stylesheet" href="../../component/inventoryAlertBox/inventoryAlertModal.css">
    <link rel="stylesheet" href="../../component/currency/currency.css">
    <link rel="stylesheet" href="../../component/graphBox/graph.css">
    <link rel="stylesheet" href="../../component/settings/settings.css">
    <link rel="stylesheet" href="../../component/inventoryAlertBox/inventoryAlerts.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />  
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
</head>
<body>
    <?php include "../../component/navbar/nav-bar.php"; ?>

    <section class="main">
        <div class="box">
            <div class="box-1">
                <?php include __DIR__ . "/../../component/currency/currency.php"; ?>
            </div>
             
            <?php include __DIR__ . "/../../component/inventoryAlertBox/inventoryAlerts.php"; ?>
            <?php include "../../component/inventoryAlertBox/inventoryAlertsModal.php"; ?> 
            
            <?php include __DIR__ . "/../../component/graphBox/graph.php"; ?> 
        </div>

        <?php include __DIR__ . "/../../component/settings/settings.php"; ?>
    </section>

    <script src="../../component/inventoryAlertBox/inventoryAlerts.js"></script>
    <script src="../../component/inventoryAlertBox/inventoryAlertsModal.js"></script>
    <script src="../../component/currency/countUp.js"></script>
    <script src="../../component/settings/settings.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        var serverChartData = <?php echo json_encode($chartData ?? []); ?>;
        var serverView = "<?php echo $view ?? 'weekly'; ?>";
    </script>

    <script src="../../component/graphBox/chart.js"></script>
</body>
</html>