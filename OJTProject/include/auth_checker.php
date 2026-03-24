<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/OJTProject/index.php");
    exit(); 
}


$timeout = 3600; 
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: http://localhost/OJTProject/index.php");
    exit();
}
$_SESSION['last_activity'] = time(); 
?>