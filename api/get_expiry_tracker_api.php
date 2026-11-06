<?php

header("Content-Type: application/json");
include "../dbconnect.php";

/* CHECK USER */

if(!isset($_GET['user_id'])){
    echo json_encode([
        "status"=>"error",
        "message"=>"user_id required"
    ]);
    exit();
}

$user_id = intval($_GET['user_id']);

$main_conn = $conn;

/* GET USER DATABASE */

$q = "SELECT dbname FROM user_verification WHERE user_id='$user_id'";
$res = mysqli_query($main_conn,$q);

if(mysqli_num_rows($res) == 0){
    echo json_encode([
        "status"=>"error",
        "message"=>"User database not found"
    ]);
    exit();
}

$row = mysqli_fetch_assoc($res);
$dbname = $row['dbname'];

/* CONNECT FIRM DATABASE */

$conn = mysqli_connect($db_host, $db_user, $db_pass, $dbname);

if(!$conn){
    echo json_encode([
        "status"=>"error",
        "message"=>"Firm DB connection failed"
    ]);
    exit();
}

/* GET LISTED ITEMS */

$listed_items = [];

$list_query = "
SELECT stock_id
FROM marketplace_listings
WHERE firm_id='$user_id'
AND status='Active'
";

$list_res = mysqli_query($main_conn,$list_query);

while($l = mysqli_fetch_assoc($list_res)){
    $listed_items[] = $l['stock_id'];
}

/* FETCH STOCK WITH TIME LEFT */

$query = "
SELECT 
stock_id,
prod_name,
batch_no,
qty,
exp_date,
TIMESTAMPDIFF(SECOND, NOW(), CONCAT(exp_date,' 23:59:59')) AS seconds_left
FROM stock_table
WHERE exp_date IS NOT NULL
AND exp_date != '0000-00-00'
ORDER BY exp_date ASC
";

$result = mysqli_query($conn,$query);

$soon = [];
$expired = [];

while($row = mysqli_fetch_assoc($result)){

    $seconds_left = intval($row['seconds_left']);

    /* CHECK LISTED */
    $listed = in_array($row['stock_id'],$listed_items);

    /* EXPIRED */
    if($seconds_left <= 0){

        $expired[] = [
            "stock_id"=>$row['stock_id'],
            "prod_name"=>$row['prod_name'],
            "batch_no"=>$row['batch_no'],
            "qty"=>$row['qty'],
            "exp_date"=>$row['exp_date']
        ];

    }

    /* EXPIRING WITHIN 30 DAYS */
    else{

        $days_left = floor($seconds_left / 86400);

        $hours = floor(($seconds_left % 86400) / 3600);
        $minutes = floor(($seconds_left % 3600) / 60);
        $seconds = $seconds_left % 60;

        $time_left = sprintf("%02d:%02d:%02d",$hours,$minutes,$seconds);

        if($days_left <= 30){

            $soon[] = [
                "stock_id"=>$row['stock_id'],
                "prod_name"=>$row['prod_name'],
                "batch_no"=>$row['batch_no'],
                "qty"=>$row['qty'],
                "exp_date"=>$row['exp_date'],
                "days_left"=>$days_left,
                "time_left"=>$time_left,
                "listed"=>$listed
            ];

        }
    }
}

/* RETURN JSON */

echo json_encode([
    "status"=>"success",
    "expiring_soon"=>$soon,
    "expired"=>$expired
]);

?>