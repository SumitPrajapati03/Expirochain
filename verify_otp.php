<?php
session_start();

/* ---------------- SESSION SAFETY CHECK ---------------- */
if (!isset($_SESSION['otp'], $_SESSION['otp_time'], $_SESSION['reg_data'])) {
    die("Session expired. Please register again.");
}

/* ---------------- HANDLE OTP SUBMISSION ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_otp = $_POST['otp'];

    if (time() - $_SESSION['otp_time'] > 300) {
        session_destroy();
        die("<script>alert('OTP expired. Please register again.');</script>");
    }

    if ($user_otp == $_SESSION['otp']) {
        unset($_SESSION['otp'], $_SESSION['otp_time']);
        header("Location: save_user.php");
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Verify OTP | EXPIROCHAIN</title>

<link rel="shortcut icon" href="images/favicon/android-chrome-192x192.png" />
<link rel="stylesheet" href="css/register.css">
<link rel="stylesheet" href="css/login.css">
<link rel="stylesheet" href="css/verify_otp.css">

</head>
<body>

<header class="app-header">
  <div class="header-left">
    <img src="images/Logo.png" alt="ExpiroChain Logo">
  </div>
</header>

<div class="page-title">
  <h2>Verify OTP</h2>
  <p>Enter the 6-digit code sent to your email</p>
</div>

<div class="page-wrapper">
  <div class="register-card otp-card">

    <h2 class="otp-title">Check Your Inbox</h2>

    <p class="otp-subtext">
      Enter the 6-digit security code
    </p>

    <form method="post" id="otpForm">

      <div class="otp-group" id="otpGroup">
        <input maxlength="1" inputmode="numeric">
        <input maxlength="1" inputmode="numeric">
        <input maxlength="1" inputmode="numeric">
        <input maxlength="1" inputmode="numeric">
        <input maxlength="1" inputmode="numeric">
        <input maxlength="1" inputmode="numeric">
      </div>

      <!-- Hidden field required by backend -->
      <input type="hidden" name="otp" id="otp">

      <button class="register-btn" id="verifyBtn" type="submit" disabled>
        Verify Code
      </button>

    </form>

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <div class="footer-text">
      OTP is valid for 5 minutes
    </div>

  </div>
</div>

<script>
const inputs = document.querySelectorAll("#otpGroup input");
const verifyBtn = document.getElementById("verifyBtn");
const otpHidden = document.getElementById("otp");
const form = document.getElementById("otpForm");

/* ---------------- OTP INPUT UX ---------------- */

inputs.forEach((input, idx) => {

  input.addEventListener("input", () => {
    input.value = input.value.replace(/\D/g, "");

    if (input.value && idx < inputs.length - 1) {
      inputs[idx + 1].focus();
    }

    toggleVerify();
  });

  input.addEventListener("keydown", (e) => {
    if (e.key === "Backspace" && !input.value && idx > 0) {
      inputs[idx - 1].focus();
    }
  });

});

/* ---------------- PASTE SUPPORT ---------------- */

inputs[0].addEventListener("paste", (e) => {
  e.preventDefault();

  const data = e.clipboardData.getData("text")
               .replace(/\D/g, "")
               .slice(0, 6);

  data.split("").forEach((digit, i) => {
    if (inputs[i]) inputs[i].value = digit;
  });

  toggleVerify();
});

/* ---------------- ENABLE BUTTON ---------------- */

function toggleVerify() {
  verifyBtn.disabled = ![...inputs].every(i => i.value !== "");
}

/* ---------------- COMBINE OTP BEFORE SUBMIT ---------------- */

form.addEventListener("submit", function() {

  let combined = "";
  inputs.forEach(input => combined += input.value);

  otpHidden.value = combined;
});
</script>

</body>
</html>
