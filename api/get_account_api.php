<?php

header("Content-Type: application/json");
include "../dbconnect.php";

if(!isset($_GET['user_id'])){
    echo json_encode([
        "status"=>"error",
        "message"=>"USER ID missing"
    ]);
    exit();
}

$user_id = intval($_GET['user_id']);

$query = "
SELECT
firm_name,
owner_name,
user_type AS organization_type,
gstno,
dl1 AS dl1no,
dl2 AS dl2no,
phn_no AS phone,
email_id AS email,
address,
registered_at
FROM user_table
WHERE user_id = $user_id
";

$result = mysqli_query($conn,$query);

if(!$result || mysqli_num_rows($result)==0){
    echo json_encode([
        "status"=>"error",
        "message"=>"Account not found"
    ]);
    exit();
}

$data = mysqli_fetch_assoc($result);

echo json_encode([
    "status"=>"success",
    "data"=>$data
]);

?>