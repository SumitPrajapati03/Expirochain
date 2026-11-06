<?php

header("Content-Type: application/json");

include "../dbconnect.php";

/* ---------------- CHECK REQUIRED PARAMETERS ---------------- */

if(
    !isset($_POST['user_id']) ||
    !isset($_POST['barcode']) ||
    !isset($_POST['batch_no']) ||
    !isset($_POST['qty'])
){
    echo json_encode([
        "status" => "error",
        "message" => "Missing required parameters"
    ]);
    exit();
}

/* ---------------- GET INPUT ---------------- */

$user_id  = intval($_POST['user_id']);
$barcode  = mysqli_real_escape_string($conn,$_POST['barcode']);
$batch_no = mysqli_real_escape_string($conn,$_POST['batch_no']);
$exp_date = $_POST['exp_date'] ?? null;
$qty      = intval($_POST['qty']);

/* ---------------- GET USER DATABASE ---------------- */

$q = "SELECT dbname FROM user_verification WHERE user_id = $user_id";
$r = mysqli_query($conn,$q);

if(!$r){
    echo json_encode([
        "status"=>"error",
        "message"=>"User verification query failed"
    ]);
    exit();
}

$data = mysqli_fetch_assoc($r);

if(!$data){
    echo json_encode([
        "status"=>"error",
        "message"=>"User database not found"
    ]);
    exit();
}

$dbname = $data['dbname'];

/* ---------------- SWITCH DATABASE ---------------- */

mysqli_select_db($conn,$dbname);

/* ---------------- GET PRODUCT BY BARCODE ---------------- */

$product_query = "
SELECT prod_id, prod_name, expiry_applicable
FROM prod_table
WHERE barcode = '$barcode'
LIMIT 1
";

$product_result = mysqli_query($conn,$product_query);

if(!$product_result || mysqli_num_rows($product_result) == 0){
    echo json_encode([
        "status"=>"error",
        "message"=>"Product not found. Add product first."
    ]);
    exit();
}

$product = mysqli_fetch_assoc($product_result);

$prod_id = $product['prod_id'];
$prod_name = $product['prod_name'];
$expiry_applicable = $product['expiry_applicable'];

/* ---------------- EXPIRY VALIDATION ---------------- */

if($expiry_applicable == 0){
    $exp_date = NULL;
}

/* ---------------- INSERT STOCK ---------------- */

$sql = "
INSERT INTO stock_table
(prod_id, prod_name, batch_no, exp_date, qty, added_at)
VALUES
('$prod_id','$prod_name','$batch_no','$exp_date','$qty',NOW())
";

$result = mysqli_query($conn,$sql);

if(!$result){
    echo json_encode([
        "status"=>"error",
        "message"=>"Stock insert failed"
    ]);
    exit();
}

/* ---------------- SUCCESS ---------------- */

echo json_encode([
    "status"=>"success",
    "message"=>"Stock added successfully",
    "product"=>$prod_name
]);

?>