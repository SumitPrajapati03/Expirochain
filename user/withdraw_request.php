<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../dbconnect.php';

$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = intval($_SESSION['user_id']);

if ($request_id > 0) {
    mysqli_query($conn, "
        DELETE FROM exchange_requests
        WHERE request_id='$request_id'
        AND from_firm_id='$user_id'
        AND status='Pending'
    ");
}

header("Location: received_requests.php");
exit();
?>
