<?php
include "../../include/config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['full_name']); 
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if (!empty($fullname) && !empty($user) && !empty($pass) && !empty($confirm_pass)) {
        if ($pass !== $confirm_pass) {
            $message = "<div class='error-msg'>Passwords do not match!</div>";
        } else {
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, username, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullname, $user, $hashed_password);

            if ($stmt->execute()) {
                $message = "<div class='success-msg'>Success! You can now login <a href='index.php'>here</a>.</div>";
            } else {
                if ($conn->errno == 1062) {
                    $message = "<div class='error-msg'>Error: Username already taken.</div>";
                } else {
                    $message = "<div class='error-msg'>Error: Something went wrong.</div>";
                }
            }
            $stmt->close();
        }
    } else {
        $message = "<div class='error-msg'>Please fill in all fields.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEPC JIG IMS | Sign Up</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="../../style.css">
    <style>
        .error-msg { background: #ffebee; color: #d32f2f; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.85rem; border-left: 4px solid #d32f2f; }
        .success-msg { background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.85rem; border-left: 4px solid #2e7d32; }
    </style>
</head>
<body>

 <div class = "login-container"> 
    <div class="login-wrapper">
        <div class="brand-side hide-on-mobile">
            <img src="../../src/hepc.jpg" alt="www" style="width: 70px; height: auto;">
            <p>Inventory Management System</p>
        </div>

        <div class="form-side">
            <div class="form-header">
                <h2>Sign Up</h2>
                <p style="color: #999;">Create your account</p>
            </div>

            <?= $message ?>

            <form action="" method="POST">
                <div class="input-box">
                    <span class="material-symbols-outlined">badge</span>
                    <input type="text" name="full_name" placeholder="Full Name (e.g. Juan Dela Cruz)" required>
                </div>

                <div class="input-box">
                    <span class="material-symbols-outlined">person</span>
                    <input type="text" name="username" placeholder="Username" required>
                </div>

                <div class="input-box">
                    <span class="material-symbols-outlined">lock</span>
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <div class="input-box">
                    <span class="material-symbols-outlined">lock_reset</span>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                </div>

                <button type="submit" class="login-btn">
                    SIGN UP
                    <span class="material-symbols-outlined">double_arrow</span>
                </button>
            </form> 
            
            <p style="text-align:center; margin-top:20px; font-size:0.9rem; color:#666;">
                Already have an account? <a href="../../index.php" style="color:#d32f2f; text-decoration:none; font-weight:bold;">Login</a>
            </p>
        </div>
    </div>
<footer style="position: absolute; bottom: 10px; left: 0; width: 100%; text-align: center; color: #64748b; font-size: 0.8rem; pointer-events: none;">
        <p style="margin: 0;">&copy; <?= date("Y"); ?> HEPC JIG Inventory Management System.</p>
        <p style="font-size: 0.7rem; margin-top: 2px; opacity: 0.7;">Developed by Elijah Boon & Chaz Honrada</p>
    </footer>

</body>
</html>