<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "login_required";
    exit();
}

require_once __DIR__ . '/../dbconnect.php';

$user_id = intval($_SESSION['user_id']);

/* RECEIVE POPUP DATA */
$stock_id   = intval($_POST['stock_id'] ?? 0);
$prod_name  = mysqli_real_escape_string($conn, trim($_POST['prod_name'] ?? ''));
$batch_no   = mysqli_real_escape_string($conn, trim($_POST['batch_no'] ?? ''));
$qty        = intval($_POST['qty'] ?? 0);
$exp_date   = mysqli_real_escape_string($conn, trim($_POST['exp_date'] ?? ''));
$total_rate = floatval($_POST['total_rate'] ?? 0);

if ($stock_id <= 0 || $qty <= 0) {
    echo "invalid_data";
    exit();
}

/* CHECK IF ALREADY LISTED */
$check = "
SELECT listing_id
FROM marketplace_listings
WHERE stock_id='$stock_id'
AND firm_id='$user_id'
AND status='Active'
";

$res = mysqli_query($conn, $check);

if (mysqli_num_rows($res) > 0) {
    echo "already_listed";
    exit();
}

/* GET FIRM NAME */
$q = "SELECT firm_name FROM user_table WHERE user_id='$user_id' LIMIT 1";
$r = mysqli_query($conn, $q);
$f = mysqli_fetch_assoc($r);

$firm_name = mysqli_real_escape_string($conn, $f['firm_name'] ?? 'Unknown');

/* INSERT LISTING */
$query = "
INSERT INTO marketplace_listings
(stock_id, prod_name, batch_no, qty, exp_date, total_rate, firm_id, firm_name, status)
VALUES
('$stock_id','$prod_name','$batch_no','$qty','$exp_date','$total_rate','$user_id','$firm_name','Active')
";

if (mysqli_query($conn, $query)) {
    echo "success";
} else {
    echo "error";
}

exit();
?>
