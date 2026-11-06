<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    exit();
}

require_once __DIR__ . '/../dbconnect.php';

$user_id = intval($_SESSION['user_id']);
$listing_id = isset($_POST['listing_id']) ? intval($_POST['listing_id']) : 0;

if ($listing_id > 0) {
    /* get listing info */
    $q = "SELECT firm_id, qty, prod_name, batch_no, total_rate FROM marketplace_listings WHERE listing_id='$listing_id' LIMIT 1";
    $r = mysqli_query($conn, $q);

    if ($r && mysqli_num_rows($r) > 0) {
        $data = mysqli_fetch_assoc($r);
        $to_firm = intval($data['firm_id']);
        $prod_name = mysqli_real_escape_string($conn, $data['prod_name'] ?? '');
        $batch_no = mysqli_real_escape_string($conn, $data['batch_no'] ?? '');
        $requested_rate = floatval($data['total_rate'] ?? 0);

        /* insert request */
        $query = "INSERT INTO exchange_requests
        (listing_id, prod_name, batch_no, qty_requested, requested_rate, from_firm_id, to_firm_id, status)
        VALUES
        ('$listing_id', '$prod_name', '$batch_no', 1, '$requested_rate', '$user_id', '$to_firm', 'Pending')";

        mysqli_query($conn, $query);
    }
}

/* redirect back */
header("Location: marketplace.php");
exit();
?>
