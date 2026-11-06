<?php
session_start();
include 'dbconnect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'config.php';

/* ---------------- SESSION SAFETY CHECK ---------------- */
if (!isset($_SESSION['reg_data'], $_SESSION['reg_files'])) {
    die("Unauthorized access");
}

$data  = $_SESSION['reg_data'];
$files = $_SESSION['reg_files'];

/* ---------------- ESCAPE INPUTS & HASH PASSWORD ---------------- */
$firm_name  = mysqli_real_escape_string($conn, $data['firm_name']);
$owner_name = mysqli_real_escape_string($conn, $data['owner_name']);
$user_type  = mysqli_real_escape_string($conn, $data['user_type']);
$email_id   = mysqli_real_escape_string($conn, $data['email_id']);
$phn_no     = mysqli_real_escape_string($conn, $data['phn_no']);
$gstno      = mysqli_real_escape_string($conn, $data['gstno']);
$dl1        = mysqli_real_escape_string($conn, $data['dl1']);
$dl2        = mysqli_real_escape_string($conn, $data['dl2']);
$address    = mysqli_real_escape_string($conn, $data['address']);
$hashed_pass = password_hash($data['user_pass'], PASSWORD_DEFAULT);

/* ---------------- INSERT USER ---------------- */
$insert_user = "
INSERT INTO $user_table
(firm_name, owner_name, user_type, email_id, phn_no, gstno, dl1, dl2, address, user_pass, registered_at)
VALUES
(
    '$firm_name',
    '$owner_name',
    '$user_type',
    '$email_id',
    '$phn_no',
    '$gstno',
    '$dl1',
    '$dl2',
    '$address',
    '$hashed_pass',
    CURRENT_TIMESTAMP()
)";

if (!mysqli_query($conn, $insert_user)) {
    die("User registration failed: " . mysqli_error($conn));
}

$user_id = mysqli_insert_id($conn);

/* ---------------- CREATE UPLOADS FOLDER ---------------- */
$firm_slug = strtolower(trim($data['firm_name']));
$firm_slug = preg_replace('/[^a-z0-9_]+/', '_', $firm_slug);
$firm_slug = trim($firm_slug, '_');
$firm_slug .= "_" . $user_id;

$final_dir = "uploads/" . $firm_slug . "/";

if (!is_dir($final_dir)) {
    mkdir($final_dir, 0777, true);
}

/* ---------------- MOVE FILES ---------------- */
$temp_dir = "temp_uploads/" . session_id() . "/";
if (file_exists($temp_dir . $files['gst_file'])) {
    rename($temp_dir . $files['gst_file'], $final_dir . $files['gst_file']);
}
if (file_exists($temp_dir . $files['dl1_file'])) {
    rename($temp_dir . $files['dl1_file'], $final_dir . $files['dl1_file']);
}
if (file_exists($temp_dir . $files['dl2_file'])) {
    rename($temp_dir . $files['dl2_file'], $final_dir . $files['dl2_file']);
}
@rmdir($temp_dir);

/* ---------------- VERIFICATION TABLE ---------------- */
$gst_path = mysqli_real_escape_string($conn, $final_dir . $files['gst_file']);
$dl1_path = mysqli_real_escape_string($conn, $final_dir . $files['dl1_file']);
$dl2_path = mysqli_real_escape_string($conn, $final_dir . $files['dl2_file']);
$dbname_safe = mysqli_real_escape_string($conn, $firm_slug);

mysqli_query($conn, "
INSERT INTO $verification_table
(user_id, firm_name, gst_proof_path, dl1_proof_path, dl2_proof_path, dbname, registered_at)
VALUES
('$user_id', '$firm_name', '$gst_path', '$dl1_path', '$dl2_path', '$dbname_safe', CURRENT_TIMESTAMP())
");

/* ---------------- SEND REGISTRATION SUCCESS EMAIL ---------------- */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port       = SMTP_PORT;

    $mail->setFrom(SMTP_USER, APP_NAME);
    $mail->addAddress($data['email_id']);

    $mail->isHTML(true);
    $mail->Subject = "Welcome to " . APP_NAME;

    $mail->Body = "
        <h2>Registration Successful 🎉</h2>
        <p>Dear <b>" . htmlspecialchars($data['owner_name']) . "</b>,</p>
        <p>Your organization <b>" . htmlspecialchars($data['firm_name']) . "</b> has been successfully registered on the <b>" . APP_NAME . "</b> platform.</p>
        <p><b>Note:</b> You will be able to log in once your drug license and regulatory documents have been reviewed and approved by the administrator.</p>
        <br>
        <p>Regards,<br><b>Team " . APP_NAME . "</b></p>
    ";

    $mail->send();

} catch (Exception $e) {
    // Ignore mail failure on local environments without SMTP configured
}

/* ---------------- PRESERVE DASHBOARD SESSION ---------------- */
$_SESSION['firm_name'] = $data['firm_name'];
$_SESSION['user_id']   = $user_id;

unset($_SESSION['reg_data'], $_SESSION['reg_files']);

/* ---------------- REDIRECT TO LOGIN PAGE ---------------- */
echo "<script>
    alert('Registration successful! Welcome to " . APP_NAME . ". Please log in once your account is approved.');
    window.location.href = 'login.php';
</script>";
exit();
?>
