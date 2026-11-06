<?php
/**
 * ============================================================================
 * EXPIROCHAIN - Configuration Template
 * ============================================================================
 * Instructions:
 * 1. Copy this file and rename it to 'config.php' in the same directory:
 *    cp config.example.php config.php
 * 2. Update the database credentials, SMTP email settings, and admin password.
 * 3. IMPORTANT: Never commit or push 'config.php' to a public repository!
 * ============================================================================
 */

// ============================================================================
// 1. DATABASE CONFIGURATION
// ============================================================================
// Default XAMPP credentials: host='localhost', user='root', pass='', db='exch_user'
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "exch_user");

// ============================================================================
// 2. SMTP / EMAIL SERVICE (PHPMailer with Gmail SMTP)
// ============================================================================
// For Gmail SMTP:
// 1. Enable 2-Step Verification in your Google Account: https://myaccount.google.com/security
// 2. Generate an App Password under Security > 2-Step Verification > App Passwords
// 3. Paste the 16-character generated password into SMTP_PASS below
define("SMTP_HOST", "smtp.gmail.com");
define("SMTP_PORT", 587);
define("SMTP_SECURE", "tls"); // Options: 'tls' or 'ssl'

define("SMTP_USER", "your-organization@gmail.com");      // Sender email address
define("SMTP_PASS", "your-16-char-app-password");        // Google App Password (NOT personal password)

// ============================================================================
// 3. ADMINISTRATOR CREDENTIALS
// ============================================================================
// Credentials used to access the admin panel at /admin/admin.php
define("ADMIN_USERNAME", "admin");
define("ADMIN_PASSWORD", "ChangeThisSecurePassword!2026");

// ============================================================================
// 4. APPLICATION & ROUTING SETTINGS
// ============================================================================
define("APP_NAME", "EXPIROCHAIN");

// Optional: Explicitly define BASE_URL (e.g. '/Expirochain' or '' if hosted on root domain)
// If left empty or undefined, the platform automatically detects its base URL.
if (!defined('BASE_URL')) {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $parentDir = basename($scriptDir);
    if (in_array($parentDir, ['user', 'admin', 'api'])) {
        $scriptDir = dirname($scriptDir);
    }
    $scriptDir = str_replace('\\', '/', $scriptDir);
    define('BASE_URL', ($scriptDir === '/' || $scriptDir === '.' || $scriptDir === '') ? '' : rtrim($scriptDir, '/'));
}
?>

