<?php
// auth_check.php - Include this at the start of pos.php and any other protected pages

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Optional: Check for user role/permissions
function checkPermission($required_role) {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $required_role) {
        header("Location: unauthorized.php");
        exit();
    }
}
?>