<?php
session_start();
include "../dbconnect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

/* GET USER DATABASE */
$q = "SELECT dbname FROM user_verification WHERE user_id = $user_id";
$r = mysqli_query($conn, $q);
$data = mysqli_fetch_assoc($r);

if (!$data) {
    die("Database not found");
}

$dbname = $data['dbname'];

/* SWITCH DATABASE */
mysqli_select_db($conn, $dbname);

/* GET FORM DATA */
$barcode   = mysqli_real_escape_string($conn, trim($_POST['barcode'] ?? ''));
$batch_no  = mysqli_real_escape_string($conn, trim($_POST['batch_no'] ?? ''));
$exp_date  = mysqli_real_escape_string($conn, trim($_POST['exp_date'] ?? ''));
$qty       = intval($_POST['qty'] ?? 0);

/* VALIDATION */
if (empty($barcode) || empty($batch_no) || empty($qty)) {
    die("<script>alert('Required fields missing');history.back();</script>");
}

/* EXPIRY DATE VALIDATION */
if (!empty($exp_date)) {
    $today = date("Y-m-d");
    if ($exp_date < $today) {
        die("<script>
        alert('Expiry date cannot be in the past');
        history.back();
        </script>");
    }
}

/* GET PRODUCT FROM BARCODE */
$product = mysqli_query($conn, "
SELECT prod_id, prod_name
FROM prod_table
WHERE barcode = '$barcode'
");

if (mysqli_num_rows($product) == 0) {
    die("<script>alert('Product not found');history.back();</script>");
}

$row = mysqli_fetch_assoc($product);

$prod_id   = intval($row['prod_id']);
$prod_name = mysqli_real_escape_string($conn, $row['prod_name']);

/* INSERT STOCK */
$sql = "
INSERT INTO stock_table
(prod_id, prod_name, batch_no, exp_date, qty)
VALUES
('$prod_id','$prod_name','$batch_no','$exp_date','$qty')
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Stock insert failed: " . mysqli_error($conn));
}

/* SUCCESS */
echo "<script>
alert('Stock added successfully');
window.location.href='add_stock.php';
</script>";
exit();
?>
