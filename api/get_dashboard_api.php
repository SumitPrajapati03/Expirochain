<?php

header("Content-Type: application/json");

include "../dbconnect.php";

/* CHECK USER */

if(!isset($_GET['user_id'])){
    echo json_encode([
        "status" => "error",
        "message" => "User ID required"
    ]);
    exit();
}

$user_id = intval($_GET['user_id']);

/* GET USER DATABASE */

$q = "SELECT dbname FROM user_verification WHERE user_id='$user_id'";
$r = mysqli_query($conn,$q);
$data = mysqli_fetch_assoc($r);

if(!$data){
    echo json_encode([
        "status" => "error",
        "message" => "Database not found"
    ]);
    exit();
}

$dbname = $data['dbname'];

/* SWITCH DATABASE */

mysqli_select_db($conn,$dbname);

/* TOTAL PRODUCTS */

$q_products = "SELECT COUNT(*) AS total_products FROM prod_table";
$r_products = mysqli_query($conn,$q_products);
$total_products = mysqli_fetch_assoc($r_products)['total_products'];

/* TOTAL STOCK */

$q_stock = "SELECT COUNT(*) AS total_stock FROM stock_table";
$r_stock = mysqli_query($conn,$q_stock);
$total_stock = mysqli_fetch_assoc($r_stock)['total_stock'];

/* TOTAL MEDICINES */

$q_medicine = "SELECT COUNT(*) AS total_medicine
FROM prod_table
WHERE category='Medicine'";
$r_medicine = mysqli_query($conn,$q_medicine);
$total_medicine = mysqli_fetch_assoc($r_medicine)['total_medicine'];

/* TOTAL COSMETICS */

$q_cosmetic = "SELECT COUNT(*) AS total_cosmetic
FROM prod_table
WHERE category='Cosmetic'";
$r_cosmetic = mysqli_query($conn,$q_cosmetic);
$total_cosmetic = mysqli_fetch_assoc($r_cosmetic)['total_cosmetic'];

/* TOTAL OTHERS */

$q_other = "SELECT COUNT(*) AS total_other
FROM prod_table
WHERE category='Other'";
$r_other = mysqli_query($conn,$q_other);
$total_other = mysqli_fetch_assoc($r_other)['total_other'];

/* NEAR EXPIRY (30 DAYS) */

$q_near = "
SELECT COUNT(*) AS near_expiry
FROM stock_table
WHERE exp_date IS NOT NULL
AND exp_date != '0000-00-00'
AND DATEDIFF(exp_date,CURDATE()) <= 30
AND exp_date >= CURDATE()
";
$r_near = mysqli_query($conn,$q_near);
$near_expiry = mysqli_fetch_assoc($r_near)['near_expiry'];

/* EXPIRED */

$q_expired = "
SELECT COUNT(*) AS expired
FROM stock_table
WHERE exp_date < CURDATE()
";
$r_expired = mysqli_query($conn,$q_expired);
$expired = mysqli_fetch_assoc($r_expired)['expired'];

/* NEAR EXPIRY SOLD */

$q_sold = "
SELECT COUNT(*) AS nearly_expiry_sold
FROM stock_table
WHERE exp_date IS NOT NULL
AND exp_date != '0000-00-00'
AND DATEDIFF(exp_date,CURDATE()) <= 30
AND qty = 0
";
$r_sold = mysqli_query($conn,$q_sold);
$nearly_expiry_sold = mysqli_fetch_assoc($r_sold)['nearly_expiry_sold'];

/* RESPONSE */

echo json_encode([
    "status" => "success",

    "total_products" => intval($total_products),
    "total_stock" => intval($total_stock),

    "total_medicine" => intval($total_medicine),
    "total_cosmetic" => intval($total_cosmetic),
    "total_other" => intval($total_other),

    "near_expiry" => intval($near_expiry),
    "expired" => intval($expired),

    "nearly_expiry_sold" => intval($nearly_expiry_sold)
]);

?>