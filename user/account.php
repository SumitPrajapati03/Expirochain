<?php
session_start();
include "../dbconnect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* FETCH USER DATA */

$query = "
SELECT 
    firm_name,
    owner_name,
    user_type,
    gstno,
    dl1,
    dl2,
    phn_no,
    email_id,
    address,
    registered_at
FROM user_table
WHERE user_id = $user_id
";

$result = mysqli_query($conn,$query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Account | EXPIROCHAIN</title>

    <link rel="shortcut icon" href="../images/favicon/android-chrome-192x192.png" />
    <link rel="stylesheet" href="../css/home.css" />
    <link rel="stylesheet" href="css/account.css" />
    
  </head>

  <body>
    <!-- HEADER + SIDEBAR -->
     <?php include "layout.php"; ?>

    <!-- PAGE CONTENT -->

    <div class="dashboard">
      <div class="account-card">
        <h2>Account Details</h2>

        <div class="account-grid">
          <div class="field">
            <label>Firm Name</label>
            <p><?php echo htmlspecialchars($user['firm_name']); ?></p>
          </div>

          <div class="field">
            <label>Owner Name</label>
            <p><?php echo htmlspecialchars($user['owner_name']); ?></p>
          </div>

          <div class="field">
            <label>Organization Type</label>
            <p><?php echo htmlspecialchars($user['user_type']); ?></p>
          </div>

          <div class="field">
            <label>GST Number</label>
            <p><?php echo htmlspecialchars($user['gstno']); ?></p>
          </div>

          <div class="field">
            <label>DL1 Number</label>
            <p><?php echo htmlspecialchars($user['dl1']); ?></p>
          </div>

          <div class="field">
            <label>DL2 Number</label>
            <p><?php echo htmlspecialchars($user['dl2']); ?></p>
          </div>

          <div class="field">
            <label>Phone Number</label>
            <p><?php echo htmlspecialchars($user['phn_no']); ?></p>
          </div>

          <div class="field">
            <label>Email Address</label>
            <p><?php echo htmlspecialchars($user['email_id']); ?></p>
          </div>

          <div class="field full">
            <label>Address</label>
            <p><?php echo htmlspecialchars($user['address']); ?></p>
          </div>

          <div class="field">
            <label>Created At</label>
            <p>
              <?php echo date("d M Y • h:i A", strtotime($user['registered_at'])); ?>
            </p>
          </div>
        </div>
      </div>
    </div>

    <footer>© <?php echo date('Y'); ?> EXPIROCHAIN and Team</footer>

    <script>
      const sidebar = document.getElementById("sidebar");
      const overlay = document.getElementById("overlay");
      const hamburger = document.getElementById("hamburger");
      const closeSidebar = document.getElementById("closeSidebar");

      hamburger.onclick = () => {
        sidebar.classList.add("active");
        overlay.classList.add("active");
      };

      closeSidebar.onclick = () => {
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
      };

      overlay.onclick = () => {
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
      };

      document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
          sidebar.classList.remove("active");
          overlay.classList.remove("active");
        }
      });
    </script>
  </body>
</html>