<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header("Content-Type: application/json");

include "../dbconnect.php";

$response = [
    "status" => "error",
    "products" => []
];

/* CHECK USER ID */

if(!isset($_GET['user_id'])){
    $response["message"] = "User ID missing";
    echo json_encode($response);
    exit();
}

$user_id = intval($_GET['user_id']);

/* GET USER DATABASE */

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

/* SWITCH DATABASE */

mysqli_select_db($conn,$dbname);

/* FETCH PRODUCTS */

$query = "SELECT * FROM prod_table ORDER BY created_at DESC";
$result = mysqli_query($conn,$query);

$products = [];

if($result){

    while($row = mysqli_fetch_assoc($result)){

        $products[] = [
            "prod_id" => $row["prod_id"],
            "barcode" => $row["barcode"],
            "prod_name" => $row["prod_name"],
            "category" => $row["category"],
            "manufacturer" => $row["manufacturer"],
            "expiry_applicable" => $row["expiry_applicable"],
            "created_at" => $row["created_at"]
        ];
    }

}

echo json_encode([
    "status" => "success",
    "products" => $products
]);