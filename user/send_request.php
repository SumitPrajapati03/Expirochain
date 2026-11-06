<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "login_required";
    exit();
}

require_once __DIR__ . '/../dbconnect.php';

$from_firm_id = intval($_SESSION['user_id']);

/* GET DATA FROM POPUP */
$listing_id     = intval($_POST['listing_id'] ?? 0);
$prod_name      = mysqli_real_escape_string($conn, trim($_POST['prod_name'] ?? ''));
$batch_no       = mysqli_real_escape_string($conn, trim($_POST['batch_no'] ?? ''));
$to_firm_id     = intval($_POST['to_firm_id'] ?? 0);
$qty_requested  = intval($_POST['qty'] ?? 0);
$requested_rate = floatval($_POST['total_rate'] ?? 0);

/* VALIDATION */
if ($from_firm_id === $to_firm_id) {
    echo "self_request_not_allowed";
    exit();
}

if ($listing_id <= 0 || $qty_requested <= 0) {
    echo "invalid_request";
    exit();
}

/* CHECK DUPLICATE */
$check = mysqli_query($conn, "
SELECT listing_id
FROM exchange_requests
WHERE listing_id='$listing_id'
AND from_firm_id='$from_firm_id'
AND status='Pending'
");

if (mysqli_num_rows($check) > 0) {
    echo "already_requested";
    exit();
}

/* CHECK STOCK LIMIT */
$stockCheck = mysqli_query($conn, "
SELECT qty FROM marketplace_listings
WHERE listing_id='$listing_id'
");

if (!$stockCheck || mysqli_num_rows($stockCheck) == 0) {
    echo "listing_not_found";
    exit();
}

$row = mysqli_fetch_assoc($stockCheck);
$available_qty = intval($row['qty']);

if ($qty_requested > $available_qty) {
    echo "exceeds_stock";
    exit();
}

/* INSERT REQUEST */
$query = "
INSERT INTO exchange_requests
(listing_id, prod_name, batch_no, qty_requested, requested_rate, from_firm_id, to_firm_id, status)
VALUES
('$listing_id','$prod_name','$batch_no','$qty_requested','$requested_rate','$from_firm_id','$to_firm_id','Pending')
";

if (mysqli_query($conn, $query)) {
    echo "success";
} else {
    echo "error";
}

exit();
?>
