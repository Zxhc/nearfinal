<?php
include "../../include/config.php"; 


$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'users';

$table_map = [
    'users' => 'technicians',
    'equipment' => 'jig_equipment',
    'customers' => 'customers'
];

$label_map = [
    'users' => 'User Full Name',
    'equipment' => 'Equipment Name',
    'customers' => 'Customer Name'
];


$target_table = isset($table_map[$current_tab]) ? $table_map[$current_tab] : 'technicians';
$current_label = isset($label_map[$current_tab]) ? $label_map[$current_tab] : 'Name';


if (isset($_POST['add_entry'])) {
    $name = $_POST['itemName'];
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO $target_table (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        
        header("Location: " . $_SERVER['PHP_SELF'] . "?tab=" . $current_tab);
        exit;
    }
}


if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM $target_table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    header("Location: " . $_SERVER['PHP_SELF'] . "?tab=" . $current_tab);
    exit;
}


$result = $conn->query("SELECT * FROM $target_table ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management System | Admin</title>
    <link rel="stylesheet" href="addUser.css">
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="../../component/settings/settings.css">
    <link rel="stylesheet" href="../../component/navBar/nav-bar.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />  
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
</head>
<body>
     <?php include "../../component/navbar/nav-bar.php";?>

<div class="body-container">
         
    <div class="container">
        <div class="tabs-header">
            <button class="tab-btn <?php echo ($current_tab == 'users') ? 'active' : ''; ?>" onclick="switchTab('users')">
                <span class="material-symbols-outlined">group</span> Users
            </button>
            <button class="tab-btn <?php echo ($current_tab == 'equipment') ? 'active' : ''; ?>" onclick="switchTab('equipment')">
                <span class="material-symbols-outlined">construction</span> Equipment
            </button>
            <button class="tab-btn <?php echo ($current_tab == 'customers') ? 'active' : ''; ?>" onclick="switchTab('customers')">
                <span class="material-symbols-outlined">person_pin</span> Customers
            </button>
        </div>

        <div class="main-content">
            <div class="section-header">
                <h2 class="section-title">Manage <?php echo ucfirst($current_tab); ?></h2>
            </div>

            <div class="form-section">
                <form method="POST" action="">
                    <div class="input-group">
                        <label id="input-label"><?php echo $current_label; ?></label>
                        <div class="input-wrapper">
                            <input type="text" name="itemName" placeholder="Enter name..." required autofocus>
                            <button type="submit" name="add_entry" class="submit-btn">
                                Add <span class="material-symbols-outlined">add_circle</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Entry Name</th>
                            <th class="action-cell">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['Name']); ?></strong></td>
                                    <td class="action-cell">
                                        <button class="delete-btn" onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo $current_tab; ?>')">
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="2" style="text-align:center; color:#94a3b8; padding:30px;">No entries found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include "../../component/settings/settings.php"; ?>
<script src="../../component/settings/settings.js"></script>
<script src="addUser.js"></script>
</body>
</html>