<?php

error_reporting(0);
header("Content-Type: application/json");

include "../dbconnect.php";

$response = [
    "status" => "error",
    "stocks" => []
];

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

/* FETCH STOCK */

$query = "SELECT * FROM stock_table ORDER BY added_at DESC";
$result = mysqli_query($conn,$query);

$stocks = [];

if($result){

    while($row = mysqli_fetch_assoc($result)){

        $stocks[] = [
            "stock_id" => $row["stock_id"],
            "prod_id" => $row["prod_id"],
            "prod_name" => $row["prod_name"],
            "batch_no" => $row["batch_no"],
            "exp_date" => $row["exp_date"],
            "qty" => $row["qty"],
            "added_at" => $row["added_at"]
        ];
    }

}

echo json_encode([
    "status" => "success",
    "stocks" => $stocks
]);