<?php
/*
|--------------------------------------------------------------------------
| Database Connection & Initializer
|--------------------------------------------------------------------------
*/

// Load configuration
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
} elseif (file_exists(__DIR__ . '/config.example.php')) {
    require_once __DIR__ . '/config.example.php';
}

$db_host = defined('DB_HOST') ? DB_HOST : 'localhost';
$db_user = defined('DB_USER') ? DB_USER : 'root';
$db_pass = defined('DB_PASS') ? DB_PASS : '';
$database = defined('DB_NAME') ? DB_NAME : 'exch_user';

$user_table = "user_table";
$verification_table = "user_verification";

/* CONNECT */
$conn = mysqli_connect($db_host, $db_user, $db_pass);

if (!$conn) {
    die("Database Connection Error: " . mysqli_connect_error());
}

/* CREATE DATABASE IF NOT EXISTS */
$sql = "CREATE DATABASE IF NOT EXISTS `$database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database creation failed: " . mysqli_error($conn));
}

/* SELECT DATABASE */
mysqli_select_db($conn, $database);

/* CREATE USER TABLE */
$sql = "CREATE TABLE IF NOT EXISTS `$user_table`(
    `user_id` INT(10) NOT NULL AUTO_INCREMENT,
    `firm_name` VARCHAR(50) NOT NULL,
    `owner_name` VARCHAR(50) NOT NULL,
    `user_type` VARCHAR(20) NOT NULL,
    `user_pass` VARCHAR(255) NOT NULL,
    `gstno` VARCHAR(15) NOT NULL UNIQUE,
    `dl1` VARCHAR(30) NOT NULL UNIQUE,
    `dl2` VARCHAR(30) NOT NULL UNIQUE,
    `phn_no` VARCHAR(10) NOT NULL,
    `email_id` VARCHAR(100) NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `registered_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`user_id`),
    INDEX `idx_email` (`email_id`),
    INDEX `idx_phone` (`phn_no`),
    INDEX `idx_firm` (`firm_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$result = mysqli_query($conn, $sql);
if (!$result) {
    error_log("Creation of user table failed: " . mysqli_error($conn));
}

/* CREATE VERIFICATION TABLE */
$sql = "CREATE TABLE IF NOT EXISTS `$verification_table`(
    `vrf_id` INT(10) NOT NULL AUTO_INCREMENT,
    `user_id` INT(10) NOT NULL,
    `firm_name` VARCHAR(50) NOT NULL,
    `isApproved` TINYINT(1) NOT NULL DEFAULT 0,
    `gst_proof_path` VARCHAR(255) UNIQUE,
    `dl1_proof_path` VARCHAR(255) UNIQUE,
    `dl2_proof_path` VARCHAR(255) UNIQUE,
    `dbname` VARCHAR(50) NOT NULL UNIQUE,
    `remarks` VARCHAR(255),
    `registered_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `approved_at` DATETIME DEFAULT NULL,
    PRIMARY KEY(`vrf_id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_isApproved` (`isApproved`),
    INDEX `idx_firm_name` (`firm_name`),
    CONSTRAINT `fk_verification_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `$user_table` (`user_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$result = mysqli_query($conn, $sql);
if (!$result) {
    error_log("Creation of verification table failed: " . mysqli_error($conn));
}

/* CREATE MARKETPLACE TABLE */
$sql = "CREATE TABLE IF NOT EXISTS marketplace_listings (
    listing_id INT(11) NOT NULL AUTO_INCREMENT,
    stock_id INT(11) DEFAULT NULL,
    prod_name VARCHAR(100) DEFAULT NULL,
    batch_no VARCHAR(50) DEFAULT NULL,
    qty INT(11) DEFAULT NULL,
    total_rate FLOAT NOT NULL,
    exp_date DATE DEFAULT NULL,
    firm_id INT(11) DEFAULT NULL,
    firm_name VARCHAR(150) DEFAULT NULL,
    listed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Active','Sold','Removed') DEFAULT 'Active',
    PRIMARY KEY (listing_id),
    KEY firm_id (firm_id),
    INDEX idx_status (status),
    INDEX idx_exp_date (exp_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$result = mysqli_query($conn, $sql);
if (!$result) {
    error_log("Creation of marketplace listing table failed: " . mysqli_error($conn));
}

/* CREATE EXCHANGE REQUESTS TABLE */
$sql = "CREATE TABLE IF NOT EXISTS exchange_requests (
    request_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    listing_id INT(11) NOT NULL,
    prod_name VARCHAR(100) NOT NULL,
    batch_no VARCHAR(50) NOT NULL,
    qty_requested INT(11) NOT NULL,
    requested_rate FLOAT NOT NULL,
    from_firm_id INT(11) NOT NULL,
    to_firm_id INT(11) NOT NULL,
    status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    request_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_listing_id (listing_id),
    INDEX idx_from_firm (from_firm_id),
    INDEX idx_to_firm (to_firm_id),
    INDEX idx_status (status),
    INDEX idx_request_date (request_date),
    CONSTRAINT fk_exchange_listing
        FOREIGN KEY (listing_id)
        REFERENCES marketplace_listings (listing_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

$result = mysqli_query($conn, $sql);
if (!$result) {
    error_log("Creation of exchange requests failed: " . mysqli_error($conn));
}
?>
