<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | EXPIROCHAIN</title>
    <link rel="shortcut icon" href="images/favicon/android-chrome-192x192.png" />
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
<header class="app-header">
    <div class="header-left">
        <img src="images/Logo.png" alt="EXPIROCHAIN Logo">
    </div>
    <div class="header-right">
        <a href="register.php" class="login-btn">Register</a>
    </div>
</header>

<div class="page-title">
    <h2>Forgot Password</h2>
    <p>Enter your registered email or mobile number to receive an OTP</p>
</div>

<div class="page-wrapper">
    <div class="register-card">
        <form action="send_reset_otp.php" method="post">
            <div class="field">
                <label>Email or Mobile Number</label>
                <input type="text" name="user_identity" placeholder="Enter email or mobile number" required>
            </div>

            <button type="submit" class="register-btn" id="sendOtpBtn">Send OTP</button>

            <div class="login-text">
                <p>
                    Remembered your password?
                    <a href="login.php">Login here</a>
                </p>
            </div>
        </form>

    </div>
</div>

<script src="js/forgot_password.js"></script>

</body>
</html>
