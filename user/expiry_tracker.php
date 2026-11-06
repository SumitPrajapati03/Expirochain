<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

/* CONNECT MAIN DATABASE */
require_once __DIR__ . '/../dbconnect.php';
$main_conn = $conn;

/* GET LISTED ITEMS */

$listed_items = [];

$list_query = "SELECT stock_id FROM marketplace_listings 
WHERE firm_id='$user_id' AND status='Active'";

$list_res = mysqli_query($main_conn, $list_query);

while ($l = mysqli_fetch_assoc($list_res)) {
    $listed_items[] = $l['stock_id'];
}

/* GET USER DATABASE */

$q = "SELECT dbname FROM user_verification WHERE user_id='$user_id'";
$res = mysqli_query($main_conn, $q);

$row = mysqli_fetch_assoc($res);

$dbname = $row['dbname'];

/* CONNECT FIRM DATABASE */
$conn = mysqli_connect($db_host, $db_user, $db_pass, $dbname);

if (!$conn) {
    die("Firm DB connection failed: " . mysqli_connect_error());
}
    
$soon = [];
$expired = [];

/* FETCH STOCK WITH TIME */

$query = "
SELECT
stock_id,
prod_name,
batch_no,
qty,
exp_date,
TIMESTAMPDIFF(SECOND, NOW(), CONCAT(exp_date,' 23:59:59')) AS seconds_left
FROM stock_table
WHERE exp_date IS NOT NULL
AND exp_date != '0000-00-00'
ORDER BY exp_date ASC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die(mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($result)) {

    $seconds_left = intval($row['seconds_left']);

    /* EXPIRED */

    if ($seconds_left <= 0) {

        $expired[] = $row;
    } else {

        $days_left = floor($seconds_left / 86400);

        $hours = floor(($seconds_left % 86400) / 3600);
        $minutes = floor(($seconds_left % 3600) / 60);
        $seconds = $seconds_left % 60;

        $time_left = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

        if ($days_left <= 30) {

            $row['days_left'] = $days_left;
            $row['time_left'] = $time_left;

            $soon[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Expiry Tracker | EXPIROCHAIN</title>

    <link rel="shortcut icon" href="../images/favicon/android-chrome-192x192.png" />
    <link rel="stylesheet" href="../css/home.css" />
    <link rel="stylesheet" href="css/expiry_tracker.css" />

</head>

<body>

    <?php include "layout.php"; ?>

    <br><br><br>

    <!-- EXPIRING SOON -->

    <div class="register-card">

        <h3 class="section-heading">Expiring Within 30 Days</h3>

        <table class="admin-table">

            <?php if (!empty($soon)) { ?>

                <thead>

                    <tr>
                        <th>Name</th>
                        <th>Batch</th>
                        <th>Qty</th>
                        <th>Expiry</th>
                        <th>Days Left</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($soon as $row) {

                        $days = $row['days_left'];

                        $row_class = ($days <= 7) ? "critical-expiry" : "warning-expiry";

                    ?>

                        <tr class="<?php echo $row_class; ?>">

                            <td><?php echo $row['prod_name']; ?></td>

                            <td><?php echo $row['batch_no']; ?></td>

                            <td><?php echo $row['qty']; ?></td>

                            <td><?php echo $row['exp_date']; ?></td>

                            <td>

                                <?php

                                if ($days <= 1) {

                                    echo "<span class='risk-badge critical countdown' data-seconds='" . $row['seconds_left'] . "'>" . $row['time_left'] . "</span>";
                                } elseif ($days <= 7) {

                                    echo "<span class='risk-badge critical'>" . $days . " days</span>";
                                } else {

                                    echo "<span class='risk-badge warning'>" . $days . " days</span>";
                                }

                                ?>

                            </td>

                            <td>

                                <?php if (in_array($row['stock_id'], $listed_items)) { ?>

                                    <form action="revoke_listing.php" method="POST" onsubmit="return confirm('Revoke this listing?');">

                                        <input type="hidden" name="stock_id" value="<?php echo $row['stock_id']; ?>">

                                        <button class="revoke-btn">
                                            Revoke
                                        </button>

                                    </form>

                                <?php } else { ?>

                                    <button
                                        class="sale-btn"
                                        data-stock="<?php echo $row['stock_id']; ?>"
                                        data-name="<?php echo htmlspecialchars($row['prod_name']); ?>"
                                        data-batch="<?php echo $row['batch_no']; ?>"
                                        data-qty="<?php echo $row['qty']; ?>"
                                        data-exp="<?php echo $row['exp_date']; ?>">
                                        List For Sale
                                    </button>

                                <?php } ?>

                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            <?php } else { ?>

                <tbody>

                    <tr>

                        <td colspan="6" style="text-align:center;padding:20px;">
                            No medicines expiring within 30 days
                        </td>

                    </tr>

                </tbody>

            <?php } ?>

        </table>

    </div>

    <!-- EXPIRED -->

    <div class="register-card">

        <h3 class="section-heading">Expired Medicines</h3>

        <table class="admin-table">

            <?php if (!empty($expired)) { ?>

                <thead>

                    <tr>
                        <th>Name</th>
                        <th>Batch</th>
                        <th>Qty</th>
                        <th>Expired On</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($expired as $row) { ?>

                        <tr>

                            <td><?php echo $row['prod_name']; ?></td>
                            <td><?php echo $row['batch_no']; ?></td>
                            <td><?php echo $row['qty']; ?></td>
                            <td><?php echo $row['exp_date']; ?></td>

                            <td>
                                <!-- <button>Dispose</button> -->
                                <button
                                    class="dispose-btn"
                                    data-stock="<?php echo $row['stock_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($row['prod_name']); ?>"
                                    data-qty="<?php echo $row['qty']; ?>">
                                    Dispose
                                </button>
                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            <?php } else { ?>

                <tbody>

                    <tr>

                        <td colspan="5" style="text-align:center;padding:20px;">
                            No expired medicines
                        </td>

                    </tr>

                </tbody>

            <?php } ?>

        </table>

    </div>

    <!-- SALE POPUP -->

    <div id="salePopup" class="popup">

        <div class="popup-content">

            <h3>List Medicine For Sale</h3>

            <form id="saleForm">

                <input type="hidden" id="stock_id" name="stock_id">

                <label>Product</label>
                <input type="text" id="prod_name" name="prod_name" readonly>

                <label>Batch</label>
                <input type="text" id="batch_no" name="batch_no" readonly>

                <label>Quantity to List</label>
                <input type="number" id="qty" name="qty" required>

                <label>Total Rate</label>
                <input type="number" id="total_rate" name="total_rate" required>

                <label>Expiry</label>
                <input type="text" id="exp_date" name="exp_date" readonly>

                <br><br>

                <div class="popup-buttons">
                    <button type="submit">Confirm Listing</button>
                    <button type="button" onclick="closePopup()">Cancel</button>
                </div>

            </form>

        </div>

    </div>

    <!-- DISPOSE POPUP -->

    <div id="disposePopup" class="popup">

        <div class="popup-content">

            <h3>Dispose Medicine</h3>

            <form id="disposeForm">

                <input type="hidden" id="d_stock_id" name="stock_id">
                <input type="hidden" id="d_prod_name" name="prod_name">
                <input type="hidden" id="d_qty" name="qty">

                <label>Performed By</label>
                <input type="text" id="performed_by" name="performed_by" required>

                <label>Password</label>

                <div style="position:relative; width:95%">
                    <input type="password" id="password" name="password" required style="width:100%;padding-right:35px;">
                    <i class="fa fa-eye" id="togglePassword"
                        style="position:absolute; right:-30px; top:50%; transform:translateY(-50%); cursor:pointer;"></i>
                </div>

                <br><br>

                <div class="popup-buttons">
                    <button type="submit">Confirm Dispose</button>
                    <button type="button" onclick="closeDispose()">Cancel</button>
                </div>

            </form>

        </div>

    </div>

    <script>
        /* OPEN DISPOSE POPUP */

        document.querySelectorAll(".dispose-btn").forEach(btn => {

            btn.addEventListener("click", function() {

                document.getElementById("disposePopup").style.display = "flex";

                document.getElementById("d_stock_id").value = this.dataset.stock;
                document.getElementById("d_prod_name").value = this.dataset.name;
                document.getElementById("d_qty").value = this.dataset.qty;

            });

        });

        /* CLOSE */

        function closeDispose() {
            document.getElementById("disposePopup").style.display = "none";
        }

        /* TOGGLE PASSWORD */

        document.getElementById("togglePassword").addEventListener("click", function() {

            let pass = document.getElementById("password");

            if (pass.type === "password") {
                pass.type = "text";
                this.classList.remove("fa-eye");
                this.classList.add("fa-eye-slash");
            } else {
                pass.type = "password";
                this.classList.remove("fa-eye-slash");
                this.classList.add("fa-eye");
            }

        });

        /* SUBMIT DISPOSE */

        document.getElementById("disposeForm").addEventListener("submit", function(e) {

            e.preventDefault();

            let formData = new FormData(this);

            fetch("dispose_stock.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.text())
                .then(data => {

                    console.log(data);

                    if (data.trim() === "success") {
                        alert("Disposed successfully");
                        location.reload();
                    } else {
                        alert("Error disposing stock");
                    }

                });

        });
    </script>

    <script>
        let currentButton = null;

        /* OPEN POPUP */

        document.querySelectorAll(".sale-btn").forEach(btn => {

            btn.addEventListener("click", function() {

                currentButton = this;

                document.getElementById("salePopup").style.display = "flex";

                document.getElementById("stock_id").value = this.dataset.stock;
                document.getElementById("prod_name").value = this.dataset.name;
                document.getElementById("batch_no").value = this.dataset.batch;
                document.getElementById("qty").value = this.dataset.qty;
                document.getElementById("exp_date").value = this.dataset.exp;

            });

        });

        /* CLOSE POPUP */

        function closePopup() {
            document.getElementById("salePopup").style.display = "none";
        }

        /* SUBMIT */

        document.getElementById("saleForm").addEventListener("submit", function(e) {

            e.preventDefault();

            let formData = new FormData(this);

            fetch("list_for_sale.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.text())
                .then(data => {

                    if (data.includes("success")) {

                        closePopup();

                        if (currentButton) {

                            let stockId = currentButton.dataset.stock;

                            /* 🔥 REPLACE BUTTON WITH REVOKE FORM */

                            currentButton.outerHTML = `
<form action="revoke_listing.php" method="POST" onsubmit="return confirm('Revoke this listing?');">
<input type="hidden" name="stock_id" value="${stockId}">
<button class="revoke-btn">Revoke</button>
</form>
`;
                        }

                    } else if (data.trim() === "already_listed") {
                        alert("Already listed in marketplace");
                    } else {
                        alert("Error listing medicine");
                    }

                });

        });
    </script>

    <script>
        function formatTime(seconds) {

            let hrs = Math.floor(seconds / 3600);
            let mins = Math.floor((seconds % 3600) / 60);
            let secs = seconds % 60;

            return String(hrs).padStart(2, '0') + ":" +
                String(mins).padStart(2, '0') + ":" +
                String(secs).padStart(2, '0');
        }

        function startCountdown() {

            const timers = document.querySelectorAll(".countdown");

            timers.forEach(timer => {

                let seconds = parseInt(timer.dataset.seconds);

                setInterval(() => {

                    if (seconds <= 0) {
                        timer.innerText = "Expired";
                        return;
                    }

                    seconds--;

                    timer.innerText = formatTime(seconds);

                }, 1000);

            });

        }

        startCountdown();
    </script>

</body>

</html>