<?php
session_start();
include "../dbconnect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* GET USER DATABASE */

$q = "SELECT dbname FROM user_verification WHERE user_id = $user_id";
$r = mysqli_query($conn, $q);
$data = mysqli_fetch_assoc($r);

if (!$data) {
    die("Database not found");
}

$dbname = $data['dbname'];

/* SWITCH DATABASE */

mysqli_select_db($conn, $dbname);

$today = date("Y-m-d");
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">
    <title>Add Stock | EXPIROCHAIN</title>

    <link rel="shortcut icon" href="../images/favicon/android-chrome-192x192.png">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="css/add_product.css">

</head>

<body>

    <?php include "layout.php"; ?>

    <div class="dashboard">

        <div class="add-product-card">

            <h2>Add Stock</h2>

            <form action="save_stock.php" method="POST">

                <!-- BARCODE -->

                <label>Barcode</label>
                <input type="text" name="barcode" id="barcode" required>

                <!-- PRODUCT NAME -->

                <label>Product Name</label>
                <input type="text" name="prod_name" id="prod_name" readonly>

                <!-- BATCH NUMBER -->

                <label>Batch Number</label>
                <input type="text" name="batch_no" required>

                <!-- EXPIRY DATE -->

                <label>Expiry Date</label>
                <input
                    type="date"
                    name="exp_date"
                    id="exp_date"
                    min="<?php echo $today; ?>">

                <!-- QUANTITY -->

                <label>Quantity</label>
                <input type="number" name="qty" min="1" required>

                <!-- BUTTONS -->

                <div class="form-buttons">

                    <button type="submit" class="btn-add">
                        Add Stock
                    </button>

                    <button type="reset" class="btn-reset">
                        Reset
                    </button>

                </div>

            </form>

        </div>

    </div>

    <footer>
        © <?php echo date('Y'); ?> EXPIROCHAIN and Team
    </footer>

    <script>
        /* SET MIN DATE TO TODAY */

        const today = new Date().toISOString().split("T")[0];
        document.getElementById("exp_date").setAttribute("min", today);


        /* FETCH PRODUCT NAME FROM BARCODE */

        const barcodeField = document.getElementById("barcode");

        barcodeField.addEventListener("change", function() {

            let barcode = this.value.trim();

            if (barcode === "") return;

            fetch("get_product_by_barcode.php?barcode=" + barcode)

                .then(response => response.json())

                .then(data => {

                    if (data.status === "success") {

                        document.getElementById("prod_name").value = data.prod_name;

                        let expiryField = document.querySelector("input[name='exp_date']");

                        if (data.expiry_applicable == 0) {

                            expiryField.disabled = true;
                            expiryField.value = "";

                        } else {

                            expiryField.disabled = false;
                            expiryField.setAttribute("min", today);

                        }

                    } else {

                        alert("Product not found. Please add product first.");
                        document.getElementById("prod_name").value = "";

                    }

                });

        });
    </script>

</body>

</html>