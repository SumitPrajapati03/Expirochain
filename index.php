<?php
/**
 * EXPIROCHAIN - Application Entry Point
 * Redirects visitors to appropriate landing page based on authentication status.
 */
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin/admin.php");
    exit();
}

header("Location: login.php");
exit();
?>

