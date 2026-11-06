<?php
session_start();
include "../dbconnect.php";

header("Content-Type: application/json");

if(!isset($_GET['barcode'])){
echo json_encode(["status"=>"error"]);
exit();
}

$barcode = $_GET['barcode'];
$user_id = $_SESSION['user_id'];

/* GET USER DATABASE */

$q = "SELECT dbname FROM user_verification WHERE user_id = $user_id";
$r = mysqli_query($conn,$q);
$data = mysqli_fetch_assoc($r);

if(!$data){
echo json_encode(["status"=>"error"]);
exit();
}

$dbname = $data['dbname'];

mysqli_select_db($conn,$dbname);

/* FETCH PRODUCT */

$query = "
SELECT prod_id, prod_name, expiry_applicable
FROM prod_table
WHERE barcode = '$barcode'
";

$result = mysqli_query($conn,$query);

if(mysqli_num_rows($result) > 0){

$row = mysqli_fetch_assoc($result);

echo json_encode([
"status"=>"success",
"prod_id"=>$row['prod_id'],
"prod_name"=>$row['prod_name'],
"expiry_applicable"=>$row['expiry_applicable']
]);

}else{

echo json_encode([
"status"=>"error"
]);

}

?>