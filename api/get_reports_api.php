<?php
error_reporting(0);
header("Content-Type: application/json");

include "../dbconnect.php";

$response = [
    "status" => "error",
    "data" => []
];

if (!isset($_GET['user_id'])) {
    $response["message"] = "User ID missing";
    echo json_encode($response);
    exit();
}

$user_id = intval($_GET['user_id']);
$report_type = isset($_GET['type']) ? trim($_GET['type']) : 'actions';

/* GET USER DATABASE */
$q = "SELECT dbname FROM user_verification WHERE user_id = $user_id LIMIT 1";
$r = mysqli_query($conn, $q);

if (!$r || mysqli_num_rows($r) === 0) {
    $response["message"] = "User verification database not found";
    echo json_encode($response);
    exit();
}

$data = mysqli_fetch_assoc($r);
$dbname = $data['dbname'];

/* SWITCH DATABASE */
mysqli_select_db($conn, $dbname);

if ($report_type === 'stock') {
    $query = "SELECT s.*, p.category, p.manufacturer 
              FROM stock_table s 
              JOIN prod_table p ON s.prod_id = p.prod_id 
              ORDER BY s.exp_date ASC";
} else {
    $query = "SELECT * FROM action_log ORDER BY action_date DESC";
}

$result = mysqli_query($conn, $query);

$rows = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
}

$response["status"] = "success";
$response["type"] = $report_type;
$response["total"] = count($rows);
$response["data"] = $rows;

echo json_encode($response);
exit();
?>

