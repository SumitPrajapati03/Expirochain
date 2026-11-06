-- =====================================================================
-- EXPIROCHAIN - Database Schema Definition
-- Platform: Medicine Lifecycle, Expiry Management, Waste Rescue & Compliance
-- Compatible: MySQL 5.7+ / MySQL 8.0+ / MariaDB 10.3+
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ---------------------------------------------------------------------
-- 1. CENTRAL DATABASE: exch_user
-- ---------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `exch_user` 
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `exch_user`;

-- ---------------------------------------------------------------------
-- Table structure for table: `user_table`
-- Description: Stores registered healthcare firms, pharmacies, clinics & NGOs
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `user_table`;
CREATE TABLE `user_table` (
  `user_id` INT(10) NOT NULL AUTO_INCREMENT,
  `firm_name` VARCHAR(50) NOT NULL,
  `owner_name` VARCHAR(50) NOT NULL,
  `user_type` ENUM('Medical', 'Clinic', 'NGO') NOT NULL,
  `user_pass` VARCHAR(255) NOT NULL,
  `gstno` VARCHAR(15) NOT NULL,
  `dl1` VARCHAR(30) NOT NULL,
  `dl2` VARCHAR(30) NOT NULL,
  `phn_no` VARCHAR(10) NOT NULL,
  `email_id` VARCHAR(100) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `registered_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uk_gstno` (`gstno`),
  UNIQUE KEY `uk_dl1` (`dl1`),
  UNIQUE KEY `uk_dl2` (`dl2`),
  INDEX `idx_email` (`email_id`),
  INDEX `idx_phone` (`phn_no`),
  INDEX `idx_firm` (`firm_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table structure for table: `user_verification`
-- Description: Stores KYC verification, uploaded license documents, and tenant DB mapping
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `user_verification`;
CREATE TABLE `user_verification` (
  `vrf_id` INT(10) NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) NOT NULL,
  `firm_name` VARCHAR(50) NOT NULL,
  `isApproved` TINYINT(1) NOT NULL DEFAULT 0,
  `gst_proof_path` VARCHAR(255) DEFAULT NULL,
  `dl1_proof_path` VARCHAR(255) DEFAULT NULL,
  `dl2_proof_path` VARCHAR(255) DEFAULT NULL,
  `dbname` VARCHAR(50) NOT NULL,
  `remarks` VARCHAR(255) DEFAULT NULL,
  `registered_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approved_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`vrf_id`),
  UNIQUE KEY `uk_vrf_dbname` (`dbname`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_isApproved` (`isApproved`),
  INDEX `idx_firm_name` (`firm_name`),
  CONSTRAINT `fk_verification_user` 
    FOREIGN KEY (`user_id`) 
    REFERENCES `user_table` (`user_id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table structure for table: `marketplace_listings`
-- Description: Stores cross-organization medicine listings for near-expiry redistribution
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `marketplace_listings`;
CREATE TABLE `marketplace_listings` (
  `listing_id` INT(11) NOT NULL AUTO_INCREMENT,
  `stock_id` INT(11) DEFAULT NULL,
  `prod_name` VARCHAR(100) DEFAULT NULL,
  `batch_no` VARCHAR(50) DEFAULT NULL,
  `qty` INT(11) DEFAULT NULL,
  `total_rate` FLOAT NOT NULL,
  `exp_date` DATE DEFAULT NULL,
  `firm_id` INT(11) DEFAULT NULL,
  `firm_name` VARCHAR(150) DEFAULT NULL,
  `listed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('Active', 'Sold', 'Removed') DEFAULT 'Active',
  PRIMARY KEY (`listing_id`),
  INDEX `idx_firm_id` (`firm_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_exp_date` (`exp_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table structure for table: `exchange_requests`
-- Description: Stores medicine transfer/rescue requests between registered organizations
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `exchange_requests`;
CREATE TABLE `exchange_requests` (
  `request_id` INT(11) NOT NULL AUTO_INCREMENT,
  `listing_id` INT(11) NOT NULL,
  `prod_name` VARCHAR(100) NOT NULL,
  `batch_no` VARCHAR(50) NOT NULL,
  `qty_requested` INT(11) NOT NULL,
  `requested_rate` FLOAT NOT NULL,
  `from_firm_id` INT(11) NOT NULL,
  `to_firm_id` INT(11) NOT NULL,
  `status` ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
  `request_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`),
  INDEX `idx_listing_id` (`listing_id`),
  INDEX `idx_from_firm` (`from_firm_id`),
  INDEX `idx_to_firm` (`to_firm_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_request_date` (`request_date`),
  CONSTRAINT `fk_exchange_listing`
    FOREIGN KEY (`listing_id`)
    REFERENCES `marketplace_listings` (`listing_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. TENANT DATABASE TEMPLATE
-- Note: A dedicated database is automatically provisioned for each approved
-- firm upon administrator verification. Below is the blueprint schema.
-- Example Tenant Database: `sample_pharmacy_1`
-- ---------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `sample_pharmacy_1`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `sample_pharmacy_1`;

-- Table structure for table: `prod_table`
DROP TABLE IF EXISTS `prod_table`;
CREATE TABLE `prod_table` (
  `prod_id` INT(11) NOT NULL AUTO_INCREMENT,
  `barcode` VARCHAR(50) DEFAULT NULL,
  `prod_name` VARCHAR(100) NOT NULL,
  `category` ENUM('Medicine', 'Cosmetic', 'Other') NOT NULL,
  `manufacturer` VARCHAR(100) DEFAULT NULL,
  `expiry_applicable` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`prod_id`),
  UNIQUE KEY `uk_barcode` (`barcode`),
  INDEX `idx_barcode` (`barcode`),
  INDEX `idx_prod_name` (`prod_name`),
  INDEX `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table: `stock_table`
DROP TABLE IF EXISTS `stock_table`;
CREATE TABLE `stock_table` (
  `stock_id` INT(11) NOT NULL AUTO_INCREMENT,
  `prod_id` INT(11) NOT NULL,
  `prod_name` VARCHAR(100) NOT NULL,
  `batch_no` VARCHAR(50) NOT NULL,
  `exp_date` DATE DEFAULT NULL,
  `qty` INT(11) NOT NULL,
  `added_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stock_id`),
  INDEX `idx_prod_id` (`prod_id`),
  INDEX `idx_batch` (`batch_no`),
  INDEX `idx_exp_date` (`exp_date`),
  CONSTRAINT `fk_stock_prod`
    FOREIGN KEY (`prod_id`)
    REFERENCES `prod_table` (`prod_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table: `action_log`
DROP TABLE IF EXISTS `action_log`;
CREATE TABLE `action_log` (
  `actn_id` INT(11) NOT NULL AUTO_INCREMENT,
  `stock_id` INT(11) NOT NULL,
  `action_type` ENUM('Transfer', 'Donate', 'Dispose') NOT NULL,
  `qty` INT(11) NOT NULL,
  `to_firm_name` VARCHAR(100) DEFAULT NULL,
  `remarks` VARCHAR(255) DEFAULT NULL,
  `prod_name` VARCHAR(255) DEFAULT NULL,
  `days_left_at_action` INT(11) DEFAULT NULL,
  `risk_score_at_action` INT(11) DEFAULT NULL,
  `action_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `performed_by` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`actn_id`),
  INDEX `idx_stock_id` (`stock_id`),
  INDEX `idx_action_type` (`action_type`),
  INDEX `idx_action_date` (`action_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. OPTIONAL SAMPLE DATA (Clean mock data for local testing)
-- ---------------------------------------------------------------------
USE `exch_user`;

-- Sample verified user
INSERT INTO `user_table` (`user_id`, `firm_name`, `owner_name`, `user_type`, `user_pass`, `gstno`, `dl1`, `dl2`, `phn_no`, `email_id`, `address`) VALUES
(1, 'Sample Pharmacy', 'John Doe', 'Medical', 'password123', '24AAACH7409R1ZZ', 'DL-20B-100201', 'DL-21B-100202', '9876543210', 'demo@expirochain.local', '101 Healthcare Park, Medical Hub');

INSERT INTO `user_verification` (`vrf_id`, `user_id`, `firm_name`, `isApproved`, `dbname`, `remarks`) VALUES
(1, 1, 'Sample Pharmacy', 1, 'sample_pharmacy_1', 'Verified sample demo account');

USE `sample_pharmacy_1`;

INSERT INTO `prod_table` (`prod_id`, `barcode`, `prod_name`, `category`, `manufacturer`, `expiry_applicable`) VALUES
(1, '8901030383011', 'Paracetamol 500mg', 'Medicine', 'Cipla Ltd', 1),
(2, '8901030383022', 'Amoxicillin 250mg', 'Medicine', 'Sun Pharma', 1),
(3, '8901030383033', 'Antiseptic Cream', 'Cosmetic', 'Himalaya', 1);

INSERT INTO `stock_table` (`stock_id`, `prod_id`, `prod_name`, `batch_no`, `exp_date`, `qty`) VALUES
(1, 1, 'Paracetamol 500mg', 'BATCH-PCM-01', DATE_ADD(CURDATE(), INTERVAL 15 DAY), 120),
(2, 2, 'Amoxicillin 250mg', 'BATCH-AMX-02', DATE_ADD(CURDATE(), INTERVAL 45 DAY), 80),
(3, 3, 'Antiseptic Cream', 'BATCH-CR-03', DATE_ADD(CURDATE(), INTERVAL 180 DAY), 50);

USE `exch_user`;

INSERT INTO `marketplace_listings` (`listing_id`, `stock_id`, `prod_name`, `batch_no`, `qty`, `total_rate`, `exp_date`, `firm_id`, `firm_name`, `status`) VALUES
(1, 1, 'Paracetamol 500mg', 'BATCH-PCM-01', 50, 450.00, DATE_ADD(CURDATE(), INTERVAL 15 DAY), 1, 'Sample Pharmacy', 'Active');

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

