<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    exit("unauthorized");
}

if (!isset($_POST['stock_id'], $_POST['qty'], $_POST['prod_name'], $_POST['performed_by'])) {
    exit("invalid request");
}

require_once __DIR__ . '/../dbconnect.php';

$user_id = intval($_SESSION['user_id']);
$stock_id = intval($_POST['stock_id']);
$qty = intval($_POST['qty']);

$main_conn = $conn;

$q = "SELECT dbname FROM user_verification WHERE user_id='$user_id' LIMIT 1";
$res = mysqli_query($main_conn, $q);

if (!$res || mysqli_num_rows($res) === 0) {
    die("Query error");
}

$row = mysqli_fetch_assoc($res);
$dbname = $row['dbname'];

/* CONNECT FIRM DB */
$conn = mysqli_connect($db_host, $db_user, $db_pass, $dbname);

if (!$conn) {
    die("DB error");
}

/* ESCAPE */
$prod_name    = mysqli_real_escape_string($conn, $_POST['prod_name']);
$performed_by = mysqli_real_escape_string($conn, $_POST['performed_by']);

/* CHECK STOCK */
$check = mysqli_query($conn, "SELECT * FROM stock_table WHERE stock_id=$stock_id");

if (mysqli_num_rows($check) == 0) {
    exit("stock not found");
}

/* INSERT INTO ACTION LOG */
$insert = "
INSERT INTO action_log
(stock_id, action_type, qty, to_firm_name, remarks, prod_name, days_left_at_action, risk_score_at_action, action_date, performed_by)
VALUES
($stock_id, 'Dispose', $qty, '', '', '$prod_name', 0, 100, NOW(), '$performed_by')
";

if (mysqli_query($conn, $insert)) {
    if (mysqli_query($conn, "DELETE FROM stock_table WHERE stock_id=$stock_id")) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($conn);
    }
} else {
    echo "error: " . mysqli_error($conn);
}
?>
