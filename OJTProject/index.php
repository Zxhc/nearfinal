<?php
session_start();
include "./include/config.php"; 

if (isset($_SESSION['user_id'])) {
    header("Location: ./pages/dashBoard/dashBoard.php");
    exit();
}

$error_msg = "";
    
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    if (!empty($user) && !empty($pass)) {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($pass, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                
                header("Location: ./pages/dashBoard/dashBoard.php");
                exit();
            } else {
                $error_msg = "Incorrect Password, Please Try.";
            }
        } else {
            $error_msg = "Username Not Found.";
        }
        $stmt->close();
    } else {
        $error_msg = ".";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEPC JIG IMS | Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
   
    <link rel="stylesheet" href="./style.css">
</head>
<body>

  <div class = "login-container"> 
    <div class="login-wrapper">
        <div class="brand-side hide-on-mobile">
            <img src="./src/hepc.jpg" alt="www" style="width: 70px; height: auto;">
            <p>Inventory Management System</p>
        </div>

        <div class="form-side">
            <div class="form-header">
                <img src="./src/hepc.jpg" alt="www" style="width: 70px; height: auto;" class="mobile-logo">
                <h2>Login</h2>
                <p class="desktop-text">Welcome Back!</p>
                <p class="mobile-text">Welcome to JIG IMS</p>
                
                <?php if (!empty($error_msg)): ?>
                    <div style="background: #ffebee; color: #d32f2f; padding: 10px; border-radius: 8px; margin-top: 15px; font-size: 0.85rem; border-left: 4px solid #d32f2f;">
                        <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">error</span>
                        <?= htmlspecialchars($error_msg) ?>
                    </div>
                <?php endif; ?>
            </div>

            <form action="" method="POST">
                <div class="input-box">
                    <span class="material-symbols-outlined">person</span>
                    <input type="text" name="username" placeholder="Username" required>
                </div>

                <div class="input-box">
                    <span class="material-symbols-outlined">lock</span>
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <button type="submit" class="login-btn">
                    LOGIN
                    <span class="material-symbols-outlined">double_arrow</span>
                </button>
            </form> 

            <p style="text-align:center; margin-top:20px; font-size:0.9rem; color:#666;">
                <a href="./component/umtd/manuals.html" style="color:#1e293b; text-decoration:none; font-weight:bold;">Manual & Documentation</a>
            </p>
            
            <p style="text-align:center; margin-top:20px; font-size:0.9rem; color:#666;">
                Don't have an account? <a href="./pages/signup/signup.php" style="color:#d32f2f; text-decoration:none; font-weight:bold;">Sign Up</a>
            </p>
        </div>
    </div>
  </div>
<footer style="position: absolute; bottom: 10px; left: 0; width: 100%; text-align: center; color: #64748b; font-size: 0.8rem; pointer-events: none;">
        <p style="margin: 0;">&copy; <?= date("Y"); ?> HEPC JIG Inventory Management System.</p>
        <p style="font-size: 0.7rem; margin-top: 2px; opacity: 0.7;">Developed by Elijah Boon & Chaz Honrada</p>
    </footer>
</body>
</html>