<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../dbconnect.php';

$user_id = $_SESSION['user_id'];

/* BASE QUERY */
$query = "SELECT * FROM marketplace_listings
          WHERE status='Active'
          ORDER BY exp_date ASC";

$result = mysqli_query($conn, $query);

/* GET REQUESTED LISTINGS */
$requested = [];

$q = "SELECT listing_id FROM exchange_requests WHERE from_firm_id='$user_id'";
$res = mysqli_query($conn, $q);

while ($r = mysqli_fetch_assoc($res)) {
    $requested[] = $r['listing_id'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Marketplace | EXPIROCHAIN</title>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <link rel="shortcut icon" href="../images/favicon/android-chrome-192x192.png" />
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="css/marketplace.css">
    <link rel="stylesheet" href="css/expiry_tracker.css">

</head>

<body>

    <?php include "layout.php"; ?>

    <div class="marketplace-container">

        <h2 class="market-title">Medicine Marketplace</h2>

        <!-- SEARCH -->

        <div class="search-container">

            <div class="product-search">
                <input type="text" id="medicineSearch" class="main-search" placeholder="Search medicine name...">
                <button class="search-btn" onclick="clearMedicine()">Clear</button>
            </div>

            <div class="firm-search">
                <input type="text" id="firmSearch" class="secondary-search" placeholder="Search city or firm...">
                <button class="search-btn small-btn" onclick="clearFirm()">Clear</button>
            </div>

        </div>

        <!-- TABLE -->

        <table id="marketTable" class="data-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Medicine</th>
                    <th>Batch</th>
                    <th>Qty</th>
                    <th>Expiry</th>
                    <th>Total Rate</th>
                    <th>Firm</th>
                    <th>Listed At</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <?php if (mysqli_num_rows($result) > 0) {

                    $sr = 1;

                    while ($row = mysqli_fetch_assoc($result)) {
                ?>

                        <tr>

                            <td><?php echo $sr++; ?></td>
                            <td><?php echo htmlspecialchars($row['prod_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['batch_no']); ?></td>
                            <td><?php echo (int)$row['qty']; ?></td>

                            <td>
                                <?php echo ($row['exp_date'] == "0000-00-00") ? "-" : $row['exp_date']; ?>
                            </td>

                            <td>₹<?php echo number_format((float)$row['total_rate'], 2); ?></td>

                            <td><?php echo htmlspecialchars($row['firm_name']); ?></td>

                            <td><?php echo date("d M Y, h:i A", strtotime($row['listed_at'])); ?></td>

                            <td>

                                <?php if ($row['firm_id'] == $user_id) { ?>

                                    <!-- ✅ REVOKE BUTTON -->
                                    <form action="revoke_listing.php" method="POST" onsubmit="return confirm('Revoke this listing?');">

                                        <input type="hidden" name="listing_id" value="<?php echo $row['listing_id']; ?>">

                                        <button class="revoke-btn">
                                            Revoke
                                        </button>

                                    </form>

                                <?php } elseif (in_array($row['listing_id'], $requested)) { ?>

                                    <button class="requested-btn" disabled>
                                        Requested
                                    </button>

                                <?php } else { ?>

                                    <button
                                        class="request-btn open-request-popup"
                                        data-id="<?php echo $row['listing_id']; ?>"
                                        data-firm-id="<?php echo $row['firm_id']; ?>"
                                        data-firm-name="<?php echo htmlspecialchars($row['firm_name']); ?>"
                                        data-name="<?php echo htmlspecialchars($row['prod_name']); ?>"
                                        data-batch="<?php echo $row['batch_no']; ?>"
                                        data-exp="<?php echo $row['exp_date']; ?>"
                                        data-maxqty="<?php echo $row['qty']; ?>"
                                        data-rate="<?php echo $row['total_rate']; ?>"
                                        data-unit-price="<?php echo $row['total_rate'] / $row['qty']; ?>">
                                        Request
                                    </button>

                                <?php } ?>

                            </td>

                        </tr>

                    <?php }
                } else { ?>

                    <tr>
                        <td colspan="9" class="no-data">
                            No medicines available
                        </td>
                    </tr>

                <?php } ?>

            </tbody>
        </table>

    </div>

    <!-- POPUP -->

    <div id="requestPopup" class="popup">
        <div class="popup-content">

            <h3>Request Medicine</h3>

            <form id="requestForm">

                <input type="hidden" id="listing_id" name="listing_id">

                <label>Firm Name</label>
                <input type="text" id="firm_name" readonly>

                <label>Product Name</label>
                <input type="text" id="req_prod_name" readonly>

                <label>Batch</label>
                <input type="text" id="req_batch_no" readonly>

                <label>Expiry</label>
                <input type="text" id="req_exp_date" readonly>

                <label>Quantity</label>
                <input type="number" id="req_qty" name="qty" required>

                <label>Total Rate</label>
                <input type="number" id="req_total_rate" name="total_rate" required>

                <br><br>

                <div class="popup-buttons">
                    <button type="submit">Request</button>
                    <button type="button" onclick="closeRequestPopup()">Cancel</button>
                </div>

            </form>

        </div>
    </div>

    <script>
        /* DATATABLE */

        $(document).ready(function() {

            var table = $('#marketTable').DataTable({
                pageLength: 10,
                ordering: true,
                dom: 'lrtip'
            });

            $('#medicineSearch').on('keyup', function() {
                table.column(1).search(this.value).draw();
            });

            $('#firmSearch').on('keyup', function() {
                table.column(6).search(this.value).draw();
            });

        });

        /* CLEAR */

        function clearMedicine() {
            $('#medicineSearch').val('');
            $('#marketTable').DataTable().column(1).search("").draw();
        }

        function clearFirm() {
            $('#firmSearch').val('');
            $('#marketTable').DataTable().column(6).search("").draw();
        }

        /* POPUP */

        let currentRequestBtn = null;
        let selectedFirmId = 0;
        let unitPrice = 0;
        let isManualEdit = false;

        document.addEventListener("click", function(e) {

            if (e.target.classList.contains("open-request-popup")) {

                currentRequestBtn = e.target;
                document.getElementById("requestPopup").style.display = "flex";

                let maxQty = parseInt(e.target.dataset.maxqty);

                selectedFirmId = e.target.dataset.firmId;
                unitPrice = parseFloat(e.target.dataset.unitPrice);
                isManualEdit = false;

                document.getElementById("listing_id").value = e.target.dataset.id;
                document.getElementById("firm_name").value = e.target.dataset.firmName;
                document.getElementById("req_prod_name").value = e.target.dataset.name;
                document.getElementById("req_batch_no").value = e.target.dataset.batch;
                document.getElementById("req_exp_date").value = e.target.dataset.exp;

                let qtyInput = document.getElementById("req_qty");
                let totalInput = document.getElementById("req_total_rate");

                qtyInput.value = maxQty;
                qtyInput.max = maxQty;
                qtyInput.dataset.maxqty = maxQty;

                totalInput.value = Math.ceil(maxQty * unitPrice);
            }
        });

        document.getElementById("req_qty").addEventListener("input", function() {

            let max = parseInt(this.dataset.maxqty);
            let qty = parseInt(this.value);

            if (qty > max) {
                alert("Only " + max + " available");
                this.value = max;
            }

            if (!isManualEdit) {
                document.getElementById("req_total_rate").value = Math.ceil(qty * unitPrice);
            }
        });

        document.getElementById("req_total_rate").addEventListener("input", function() {
            isManualEdit = true;
        });

        function closeRequestPopup() {
            document.getElementById("requestPopup").style.display = "none";
        }

        /* SUBMIT */

        document.getElementById("requestForm").addEventListener("submit", function(e) {

            e.preventDefault();

            let formData = new FormData(this);

            formData.append("to_firm_id", selectedFirmId);
            formData.append("prod_name", document.getElementById("req_prod_name").value);
            formData.append("batch_no", document.getElementById("req_batch_no").value);

            fetch("send_request.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.text())
                .then(data => {

                    if (data.includes("success")) {

                        closeRequestPopup();

                        currentRequestBtn.innerText = "Requested";
                        currentRequestBtn.disabled = true;
                        currentRequestBtn.classList.add("requested-btn");

                    } else {
                        alert("Request failed");
                    }

                });

        });
    </script>

</body>

</html>