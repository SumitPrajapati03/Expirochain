<?php
include "../dbconnect.php";

session_start();

if(!isset($_SESSION['admin_logged_in'])){
    header("Location: ../login.php");
    exit();
}

/* TOTAL USERS */
$q1 = "SELECT COUNT(*) as total_users FROM user_table";
$r1 = mysqli_query($conn,$q1);
$d1 = mysqli_fetch_assoc($r1);
$total_users = $d1['total_users'];

/* TOTAL MEDICALS */
$q2 = "SELECT COUNT(*) as total_medicals FROM user_table WHERE user_type='Medical'";
$r2 = mysqli_query($conn,$q2);
$d2 = mysqli_fetch_assoc($r2);
$total_medicals = $d2['total_medicals'];

/* TOTAL CLINICS */
$q3 = "SELECT COUNT(*) as total_clinics FROM user_table WHERE user_type='Clinic'";
$r3 = mysqli_query($conn,$q3);
$d3 = mysqli_fetch_assoc($r3);
$total_clinics = $d3['total_clinics'];

/* TOTAL NGOs */
$q4 = "SELECT COUNT(*) as total_ngos FROM user_table WHERE user_type='NGO'";
$r4 = mysqli_query($conn,$q4);
$d4 = mysqli_fetch_assoc($r4);
$total_ngos = $d4['total_ngos'];

/* TOTAL VERIFIED USERS */
$q5 = "SELECT COUNT(*) as total_verified FROM user_verification WHERE isApproved=1";
$r5 = mysqli_query($conn,$q5);
$d5 = mysqli_fetch_assoc($r5);
$total_verified = $d5['total_verified'];

/* UNVERIFIED USERS (FOR PIE CHART) */
$total_unverified = $total_users - $total_verified;
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8" />

<title>Dashboard | EXPIROCHAIN</title>

<link rel="shortcut icon" href="../images/favicon/android-chrome-192x192.png" />
<link rel="stylesheet" href="css/admin.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<?php include "layout.php"; ?>

<div class="dashboard">

<div class="welcome-card">
<h2>
Welcome, <span>Admin</span>
</h2>
</div>

<div class="stats">

<div class="stat-box">
<h3>Total Users</h3>
<p><?php echo $total_users; ?></p>
</div>

<div class="stat-box">
<h3>Total Medicals</h3>
<p><?php echo $total_medicals; ?></p>
</div>

<div class="stat-box">
<h3>Total Clinics</h3>
<p><?php echo $total_clinics; ?></p>
</div>

<div class="stat-box">
<h3>Total NGOs</h3>
<p><?php echo $total_ngos; ?></p>
</div>

<div class="stat-box">
<h3>Total Verified Users</h3>
<p><?php echo $total_verified; ?></p>
</div>

</div>

<div class="add-product-container">

<a href="../register.php" class="add-product-btn">
<i class="fa-solid fa-user-plus"></i> Add User
</a>

</div>


<!-- ================= CHART SECTION ================= -->

<div class="charts-container" style="display:flex; gap:40px; margin-top:40px; flex-wrap:wrap; justify-content:center;">

<div class="charts">
<h3 style="font-size: 25px;">User Distribution</h3>
<canvas id="userChart"></canvas>
</div>

<div class="charts">
<h3 style="font-size: 25px;">User Verification Status</h3>
<canvas id="verifyChart"></canvas>
</div>

</div>

</div>

</div>

<footer>
© <?php echo date('Y'); ?> EXPIROCHAIN
</footer>


<script>

/* PASS PHP VALUES TO JAVASCRIPT */

const medicals = <?php echo $total_medicals; ?>;
const clinics = <?php echo $total_clinics; ?>;
const ngos = <?php echo $total_ngos; ?>;

const verified = <?php echo $total_verified; ?>;
const unverified = <?php echo $total_unverified; ?>;


/* BAR CHART */

new Chart(document.getElementById("userChart"), {
type: "bar",
data: {
labels: ["Medicals", "Clinics", "NGOs"],
datasets: [{
label: "Registered Users",
data: [medicals, clinics, ngos],
backgroundColor: [
"#2563eb",
"#10b981",
"#f59e0b"
],
borderRadius: 6
}]
},
options: {
responsive: true,
plugins: {
legend: {
display: false
}
},
scales: {
y: {
beginAtZero: true
}
}
}
});


/* PIE CHART */

new Chart(document.getElementById("verifyChart"), {
type: "pie",
data: {
labels: ["Verified Users", "Unverified Users"],
datasets: [{
data: [verified, unverified],
backgroundColor: [
"#22c55e",
"#ef4444"
]
}]
},
options: {
responsive: true
}
});

</script>

</body>
</html>