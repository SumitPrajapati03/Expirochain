<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'config.php';

/* ---------------- BASIC SAFETY CHECK ---------------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid access");
}

/* ---------------- BASIC VALIDATION ---------------- */
if (empty($_POST['email_id']) || empty($_POST['firm_name'])) {
    die("<script>alert('Required fields missing'); window.history.back();</script>");
}

/* ---------------- PASSWORD CHECK ---------------- */
if (
    empty($_POST['user_pass']) ||
    $_POST['user_pass'] !== $_POST['re_password']
) {
    die("<script>alert('Password mismatch'); window.history.back();</script>");
}

/* ---------------- STORE FORM DATA IN SESSION ---------------- */
$_SESSION['reg_data'] = [
    'firm_name'  => trim($_POST['firm_name']),
    'owner_name' => trim($_POST['owner_name']),
    'user_type'  => trim($_POST['user_type']),
    'email_id'   => trim($_POST['email_id']),
    'phn_no'     => trim($_POST['phn_no']),
    'gstno'      => trim($_POST['gstno']),
    'dl1'        => trim($_POST['dl1']),
    'dl2'        => trim($_POST['dl2']),
    'address'    => trim($_POST['address']),
    'user_pass'  => $_POST['user_pass']
];

/* ---------------- TEMP FILE STORAGE WITH VALIDATION ---------------- */
$temp_dir = "temp_uploads/" . session_id() . "/";
if (!is_dir($temp_dir)) {
    mkdir($temp_dir, 0777, true);
}

function moveTempFile($file, $name, $temp_dir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Max file size: 5MB
    if ($file['size'] > 5 * 1024 * 1024) {
        return false;
    }

    // Whitelist allowed extensions
    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return false;
    }

    $new_name = $name . "." . $ext;
    if (move_uploaded_file($file['tmp_name'], $temp_dir . $new_name)) {
        return $new_name;
    }
    return false;
}

$_SESSION['reg_files'] = [
    'gst_file' => moveTempFile($_FILES['gst_file'] ?? null, "gst", $temp_dir),
    'dl1_file' => moveTempFile($_FILES['dl1_file'] ?? null, "dl1", $temp_dir),
    'dl2_file' => moveTempFile($_FILES['dl2_file'] ?? null, "dl2", $temp_dir)
];

/* ---------------- FILE VALIDATION ---------------- */
if (
    empty($_SESSION['reg_files']['gst_file']) ||
    empty($_SESSION['reg_files']['dl1_file']) ||
    empty($_SESSION['reg_files']['dl2_file'])
) {
    array_map('unlink', glob($temp_dir . "*"));
    @rmdir($temp_dir);
    session_destroy();
    die("<script>alert('All documents are mandatory (Allowed: PDF, JPG, PNG under 5MB)'); window.history.back();</script>");
}

/* ---------------- GENERATE OTP ---------------- */
$_SESSION['otp'] = random_int(100000, 999999);
$_SESSION['otp_time'] = time();

/* ---------------- SEND OTP MAIL ---------------- */
$email = $_SESSION['reg_data']['email_id'];

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->Port       = SMTP_PORT;
    $mail->SMTPSecure = SMTP_SECURE;

    $mail->setFrom(SMTP_USER, APP_NAME);
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = APP_NAME . ' - Email Verification OTP';
    $mail->Body = "
        <h2>Email Verification</h2>
        <p>Dear {$email},</p>
        <p>Your OTP for completing registration on <b>" . APP_NAME . "</b> is:</p>
        <h1 style='color: #2b6cb0; letter-spacing: 4px;'>{$_SESSION['otp']}</h1>
        <p>This OTP is valid for <b>5 minutes</b>.</p>
        <p>Please do not share this OTP with anyone.</p>
        <br>
        <p>Regards,<br><b>Team " . APP_NAME . "</b></p>
    ";

    $mail->send();
    header("Location: verify_otp.php");
    exit();

} catch (Exception $e) {
    array_map('unlink', glob($temp_dir . "*"));
    @rmdir($temp_dir);
    session_destroy();
    die("Mail Error: Failed to send OTP. Please check your SMTP configuration in config.php. " . htmlspecialchars($mail->ErrorInfo));
}
?>
