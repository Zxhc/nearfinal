<?php 
include "inventoryFunction.php"; 
require_once "../../include/auth_checker.php";
include_once "../../component/selectMonth/carryOver.php";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="inventory.css" />
    <link rel="stylesheet" href="../../style.css" /> 
    <link rel="stylesheet" href="../../component/navBar/nav-bar.css" />
    <link rel="stylesheet" href="../../component/settings/settings.css">
    <link rel="stylesheet" href="../../component/selectMonth/selectMonth.css" />
    <link rel="stylesheet" href="../../component/inventoryTableHandler/inventoryTableHandler.css" />
    <link rel="stylesheet" href="../../component/inventoryModal/inventoryModal.css"> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />   
    <title>HEPC JIG Inventory | <?= $selectedYear ?></title>
    <style>
        hr {
            border: 0;
            height: 1px;
            background: #e2e8f0;
            margin: 25px 0;
        }
    </style>
</head>
<body>
    <?php include "../../component/navbar/nav-bar.php"?>
    
    <div class="inventory-container">
        <?php include "../../component/selectMonth/selectMonth.php"?>
        <hr>

        <div class="title-section" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-weight: 800; color: #072d7a;">INVENTORY LIST</h2>
                <span class="badge-month"><?= date("F", mktime(0, 0, 0, $selectedMonth, 1)) ?> <?= $selectedYear ?></span>
                
            </div>
            
            <form method="GET" id="yearForm">
                <input type="hidden" name="month" value="<?= $selectedMonth ?>">
                <select name="year" onchange="this.form.submit()" style="padding: 5px 10px; border-radius: 5px; cursor: pointer;">
                    <?php 
                    $yNow = (int)date('Y');
                    for($i = $yNow - 2; $i <= $yNow + 2; $i++): ?>
                        <option value="<?= $i ?>" <?= ($i == $selectedYear) ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </form>
            <?php include __DIR__ . "/../../component/settings/settings.php"; ?>
        </div>
          <?php include "../../component/inventoryTableHandler/inventoryTableHandler.php"?>
                    
      
    </div>
    <script src="../../component/currency/countUp.js"></script>
    <script src="../../component/settings/settings.js"></script>
    <script src="inventory.js" defer></script>
    <script src="../../component/search.js" defer></script>
    <script src="../../component/sort.js" defer></script>
</body>
</html>
