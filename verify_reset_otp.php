<?php
session_start();

/* ---------------- SESSION SAFETY CHECK ---------------- */
if (
    !isset($_SESSION['reset_otp'], $_SESSION['reset_identity'], $_SESSION['reset_otp_time'])
) {
    die("Unauthorized access");
}

/* ---------------- OTP EXPIRY CHECK (5 MINUTES) ---------------- */
if (time() - $_SESSION['reset_otp_time'] > 300) {
    session_unset();
    session_destroy();
    die("<script>
        alert('OTP expired. Please request a new one.');
        window.location.href = 'forgot_password.php';
    </script>");
}

/* ---------------- OTP VERIFICATION ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['otp'])) {
        die("<script>alert('Please enter OTP'); window.history.back();</script>");
    }

    if ($_POST['otp'] != $_SESSION['reset_otp']) {
        die("<script>alert('Invalid OTP'); window.history.back();</script>");
    }

    // OTP verified
    $_SESSION['reset_verified'] = true;

    header("Location: reset_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP | EXPIROCHAIN</title>

    <link rel="shortcut icon" href="images/favicon/android-chrome-192x192.png" />
    <link rel="stylesheet" href="css/register.css">
    
</head>
<body>

<!-- HEADER (MIRRORED) -->
<header class="app-header">
    <img src="images/Logo.png" alt="EXPIROCHAIN Logo">
</header>

<div class="page-wrapper">
    <div class="form-container">

        <!-- SAME STRUCTURE AS verify_otp.php -->
        <div class="page-title">
            <h2>Verify OTP</h2>
            <p>Enter the OTP sent to your registered email or mobile number</p>
        </div>

        <form method="POST">

            <!-- OTP INPUT (SAME CLASS & STYLE) -->
            <div class="field">
                <label>OTP</label>
                <input
                    type="text"
                    name="otp"
                    maxlength="6"
                    inputmode="numeric"
                    placeholder="Enter 6-digit OTP"
                    required
                >
            </div>

            <!-- VERIFY BUTTON (SAME STYLE) -->
            <button type="submit">Verify OTP</button>

            <div class="login-text">
                Didn't receive OTP?
                <a href="forgot_password.php">Resend OTP</a>
            </div>

        </form>
    </div>
</div>

</body>
</html>
