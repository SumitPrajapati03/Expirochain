<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    exit("unauthorized");
}

require_once __DIR__ . '/../dbconnect.php';

/* GET POST DATA */
$request_id   = intval($_POST['request_id']);
$stock_id     = intval($_POST['stock_id']);

$performed_by = mysqli_real_escape_string($conn, $_POST['performed_by']);
$remarks      = mysqli_real_escape_string($conn, $_POST['remarks']);
$to_firm_name = mysqli_real_escape_string($conn, $_POST['to_firm_name']);
$prod_name    = mysqli_real_escape_string($conn, $_POST['prod_name']);

$user_id = $_SESSION['user_id'];

/* START TRANSACTION */
mysqli_begin_transaction($conn);

try {

    /* STEP 1: GET REQUEST */
    $q = "SELECT * FROM exchange_requests WHERE request_id='$request_id' FOR UPDATE";
    $r = mysqli_query($conn, $q);

    if (!$r || mysqli_num_rows($r) == 0) {
        throw new Exception("Request not found");
    }

    $request = mysqli_fetch_assoc($r);

    $listing_id    = (int)$request['listing_id'];
    $qty_requested = (int)$request['qty_requested'];

    /* STEP 2: GET LISTING */
    $q2 = "SELECT * FROM marketplace_listings WHERE listing_id='$listing_id' FOR UPDATE";
    $r2 = mysqli_query($conn, $q2);

    if (!$r2 || mysqli_num_rows($r2) == 0) {
        throw new Exception("Listing not found");
    }

    $listing = mysqli_fetch_assoc($r2);

    $current_qty = (int)$listing['qty'];

    if ($listing['status'] != 'Active') {
        throw new Exception("Listing already sold/removed");
    }

    if ($qty_requested > $current_qty) {
        throw new Exception("Requested qty exceeds available stock");
    }

    /* STEP 3: UPDATE LISTING */
    if ($qty_requested == $current_qty) {

        $updateListing = "
        UPDATE marketplace_listings
        SET status='Sold', qty=0
        WHERE listing_id='$listing_id'
        ";
    } else {

        $new_qty = $current_qty - $qty_requested;

        $updateListing = "
        UPDATE marketplace_listings
        SET qty='$new_qty'
        WHERE listing_id='$listing_id'
        ";
    }

    if (!mysqli_query($conn, $updateListing)) {
        throw new Exception("Failed to update listing");
    }

    /* STEP 4: APPROVE REQUEST */
    if (!mysqli_query($conn, "
        UPDATE exchange_requests
        SET status='Approved'
        WHERE request_id='$request_id'
    ")) {
        throw new Exception("Failed to approve request");
    }

    /* STEP 5: REJECT OTHERS */
    if (!mysqli_query($conn, "
        UPDATE exchange_requests
        SET status='Rejected'
        WHERE listing_id='$listing_id'
        AND status='Pending'
        AND request_id != '$request_id'
    ")) {
        throw new Exception("Failed to reject other requests");
    }

    /* STEP 6: GET USER DB */
    $q3 = "SELECT dbname FROM user_verification WHERE user_id='$user_id'";
    $res3 = mysqli_query($conn, $q3);
    $row3 = mysqli_fetch_assoc($res3);
    $dbname = $row3['dbname'];

    $conn2 = mysqli_connect($db_host, $db_user, $db_pass, $dbname);

    if (!$conn2) {
        throw new Exception("Firm DB connection failed");
    }

    /* STEP 6.5: CALCULATE DAYS LEFT */
    $stock_q = mysqli_query($conn2, "
        SELECT DATEDIFF(exp_date, CURDATE()) AS days_left 
        FROM stock_table 
        WHERE stock_id=$stock_id
    ");

    if (!$stock_q || mysqli_num_rows($stock_q) == 0) {
        throw new Exception("Stock not found for expiry");
    }

    $stock_data = mysqli_fetch_assoc($stock_q);
    $days_left = (int)$stock_data['days_left'];

    if ($days_left < 0) {
        $days_left = 0;
    }

    /* OPTIONAL: Dynamic Risk Score */
    if ($days_left <= 5) {
        $risk_score = 90;
    } elseif ($days_left <= 15) {
        $risk_score = 60;
    } else {
        $risk_score = 20;
    }

    /* STEP 7: INSERT ACTION LOG (FIXED HERE ✅) */
    $insert = "
    INSERT INTO action_log
    (stock_id, action_type, qty, to_firm_name, remarks, prod_name, days_left_at_action, risk_score_at_action, action_date, performed_by)
    VALUES
    ($stock_id, 'Transfer', $qty_requested, '$to_firm_name', '$remarks', '$prod_name', $days_left, $risk_score, NOW(), '$performed_by')
    ";

    if (!mysqli_query($conn2, $insert)) {
        throw new Exception("Failed to insert action log");
    }

    /* STEP 8: UPDATE OR DELETE STOCK */
    $remaining_qty = $current_qty - $qty_requested;

    if ($remaining_qty > 0) {
        mysqli_query($conn2, "
            UPDATE stock_table 
            SET qty = $remaining_qty 
            WHERE stock_id = $stock_id
        ");
    } else {
        mysqli_query($conn2, "
            DELETE FROM stock_table 
            WHERE stock_id = $stock_id
        ");
    }

    /* COMMIT */
    mysqli_commit($conn);

    echo "success";
} catch (Exception $e) {

    mysqli_rollback($conn);

    echo "error: " . $e->getMessage();
}
