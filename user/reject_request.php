<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../dbconnect.php';

$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($request_id > 0) {
    mysqli_query($conn, "
        UPDATE exchange_requests
        SET status='Rejected'
        WHERE request_id='$request_id'
    ");
}

header("Location: received_requests.php");
exit();
?>
