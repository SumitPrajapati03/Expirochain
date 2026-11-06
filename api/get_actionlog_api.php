<?php

error_reporting(0);
header("Content-Type: application/json");

include "../dbconnect.php";

$response = [
    "status" => "error",
    "actions" => []
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

/* FETCH ACTION LOG */

$query = "SELECT * FROM action_log ORDER BY action_date DESC";
$result = mysqli_query($conn,$query);

$actions = [];

if($result){

    while($row = mysqli_fetch_assoc($result)){

        $actions[] = [
            "action_id" => $row["action_id"],
            "stock_id" => $row["stock_id"],
            "prod_name" => $row["prod_name"],
            "action_type" => $row["action_type"],
            "qty" => $row["qty"],
            "days_left" => $row["days_left_at_action"],
            "risk_score" => $row["risk_score_at_action"],
            "action_date" => $row["action_date"],
            "performed_by" => $row["performed_by"]
        ];
    }

}

echo json_encode([
    "status" => "success",
    "actions" => $actions
]);