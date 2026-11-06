<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../dbconnect.php';

$user_id = (int)$_SESSION['user_id'];

/* GET INPUT */

$listing_id = isset($_POST['listing_id']) ? (int)$_POST['listing_id'] : 0;
$stock_id   = isset($_POST['stock_id']) ? (int)$_POST['stock_id'] : 0;

/* FIND LISTING */

if($listing_id > 0){

    $q = "SELECT * FROM marketplace_listings 
          WHERE listing_id='$listing_id' AND firm_id='$user_id'";

}else{

    $q = "SELECT * FROM marketplace_listings 
          WHERE stock_id='$stock_id' AND firm_id='$user_id' AND status='Active'";
}

$res = mysqli_query($conn,$q);

if(mysqli_num_rows($res) == 0){
    die("Unauthorized action");
}

$data = mysqli_fetch_assoc($res);
$listing_id = $data['listing_id'];

/* TRANSACTION */
mysqli_begin_transaction($conn);

try{

    /* DELETE LISTING */
    mysqli_query($conn,"
    DELETE FROM marketplace_listings 
    WHERE listing_id='$listing_id'
    ");

    /* REJECT REQUESTS */
    mysqli_query($conn,"
    UPDATE exchange_requests
    SET status='Rejected'
    WHERE listing_id='$listing_id'
    AND status='Pending'
    ");

    mysqli_commit($conn);

    echo "<script>
    alert('Listing revoked successfully');
    window.location.href=document.referrer;
    </script>";

}catch(Exception $e){

    mysqli_rollback($conn);

    echo "<script>
    alert('Error occurred');
    window.history.back();
    </script>";
}
?>