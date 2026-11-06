<?php
session_start();
include "dbconnect.php";

if (!isset($_SESSION['user_id'], $_SESSION['firm_name'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$firm_name = $_SESSION['firm_name'];

/* GET USER DATABASE */

$q = "SELECT dbname FROM user_verification WHERE user_id = '$user_id'";
$r = mysqli_query($conn, $q);
$data = mysqli_fetch_assoc($r);

if (!$data) {
    die("Database not found");
}

$dbname = $data['dbname'];

/* SWITCH DATABASE */

mysqli_select_db($conn, $dbname);

/* COUNTS */

$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM prod_table"))['total'];
$total_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM stock_table"))['total'];

$total_medicine = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) AS total FROM prod_table WHERE category='Medicine'
"))['total'];

$total_cosmetic = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) AS total FROM prod_table WHERE category='Cosmetic'
"))['total'];

$total_other = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) AS total FROM prod_table WHERE category='Other'
"))['total'];

$near_expiry = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) AS total
FROM stock_table
WHERE exp_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
"))['total'];

$expired = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT COUNT(*) AS total
FROM stock_table
WHERE exp_date < CURDATE()
"))['total'];
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8" />
    <title>Dashboard | EXPIROCHAIN</title>
    <script src="https://kit.fontawesome.com/e05d24f6c7.js" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="images/favicon/android-chrome-192x192.png" />
    <link rel="stylesheet" href="css/home.css" />
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.2.0/css/line.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

    <?php include "user/layout.php"; ?>

    <div class="dashboard">

        <div class="welcome-card">
            <h2>Welcome, <span><?php echo htmlspecialchars($firm_name); ?></span></h2>
            <p>Manage medicine expiry, reduce waste, and stay compliant — all in one place.</p>
        </div>

        <br>

        <div class="stats">

            <div class="stat-box">
                <h3>Total Products</h3>
                <p><?php echo $total_products; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Stock</h3>
                <p><?php echo $total_stock; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Medicines</h3>
                <p><?php echo $total_medicine; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Cosmetics</h3>
                <p><?php echo $total_cosmetic; ?></p>
            </div>

        </div>

        <br>

        <div class="stats">

            <div class="stat-box">
                <h3>Total Others</h3>
                <p><?php echo $total_other; ?></p>
            </div>
            <div class="stat-box">
                <h3>Near Expiry</h3>
                <p><?php echo $near_expiry; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Near Expiry Sold</h3>
                <p>0</p>
            </div>
            <div class="stat-box">
                <h3>Expired</h3>
                <p><?php echo $expired; ?></p>
            </div>

        </div>

        <div class="add-product-container">
            <a href="user/add_product.php" class="add-product-btn">
                <i class="fa-solid fa-plus"></i> Add Product
            </a>
        </div>

        <!-- CHARTS -->

        <div class="charts-container">

            <div class="chart-box">
                <h3>Product Categories</h3>
                <canvas id="categoryChart"></canvas>
            </div>

            <div class="chart-box">
                <h3>Inventory Overview</h3>
                <canvas id="inventoryChart"></canvas>
            </div>

        </div>

    </div>

    <footer>
        © <?php echo date('Y'); ?> EXPIROCHAIN and Team
    </footer>

    <!-- CHART SCRIPT -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const medicines = <?php echo $total_medicine; ?>;
            const cosmetics = <?php echo $total_cosmetic; ?>;
            const others = <?php echo $total_other; ?>;

            const products = <?php echo $total_products; ?>;
            const stock = <?php echo $total_stock; ?>;

            /* PIE */

            new Chart(document.getElementById("categoryChart"), {
                type: "pie",
                data: {
                    labels: ["Medicines", "Cosmetics", "Others"],
                    datasets: [{
                        data: [medicines, cosmetics, others],
                        backgroundColor: ["#2563eb", "#16a34a", "#f59e0b"]
                    }]
                }
            });

            /* BAR */

            new Chart(document.getElementById("inventoryChart"), {
                type: "bar",
                data: {
                    labels: ["Products", "Stock"],
                    datasets: [{
                        data: [products, stock],
                        backgroundColor: ["#2563eb", "#10b981"]
                    }]
                }
            });

        });
    </script>


</body>

</html>