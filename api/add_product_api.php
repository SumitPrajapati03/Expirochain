<?php

error_reporting(E_ALL);
ini_set('display_errors', 0);

header("Content-Type: application/json");

include "../dbconnect.php";

$response = [
    "status" => "error",
    "message" => ""
];

/* ---------------- CHECK INPUT ---------------- */

if(
    !isset($_POST['user_id']) ||
    !isset($_POST['barcode']) ||
    !isset($_POST['prod_name']) ||
    !isset($_POST['category'])
){
    $response["message"] = "Required fields missing";
    echo json_encode($response);
    exit();
}

$user_id = intval($_POST['user_id']);
$barcode = mysqli_real_escape_string($conn,$_POST['barcode']);
$prod_name = mysqli_real_escape_string($conn,$_POST['prod_name']);
$category = mysqli_real_escape_string($conn,$_POST['category']);
$manufacturer = isset($_POST['manufacturer']) ? mysqli_real_escape_string($conn,$_POST['manufacturer']) : "";

$expiry_applicable = isset($_POST['expiry_applicable']) ? 1 : 0;


/* ---------------- GET USER DATABASE ---------------- */

$q = "SELECT dbname FROM user_verification WHERE user_id = $user_id";
$r = mysqli_query($conn,$q);

if(!$r){
    $response["message"] = "User verification query failed";
    echo json_encode($response);
    exit();
}

$data = mysqli_fetch_assoc($r);

if(!$data){
    $response["message"] = "Database not found";
    echo json_encode($response);
    exit();
}

$dbname = $data['dbname'];


/* ---------------- SWITCH DATABASE ---------------- */

mysqli_select_db($conn,$dbname);


/* ---------------- DUPLICATE BARCODE CHECK ---------------- */

$check = mysqli_query($conn,"
SELECT prod_id
FROM prod_table
WHERE barcode = '$barcode'
");

if(mysqli_num_rows($check) > 0){

    $response["message"] = "Product with this barcode already exists";
    echo json_encode($response);
    exit();

}


/* ---------------- INSERT PRODUCT ---------------- */

$sql = "
INSERT INTO prod_table
(barcode, prod_name, category, manufacturer, expiry_applicable)
VALUES
('$barcode','$prod_name','$category','$manufacturer','$expiry_applicable')
";

$result = mysqli_query($conn,$sql);

if(!$result){

    $response["message"] = "Product insert failed";
    echo json_encode($response);
    exit();

}


/* ---------------- SUCCESS ---------------- */

$response["status"] = "success";
$response["message"] = "Product added successfully";

echo json_encode($response);

?>