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

mysqli_select_db($conn, $dbname);

/* FETCH PRODUCTS */

$query = "SELECT * FROM prod_table ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>

<head>

    <title>EXPIROCHAIN - Products</title>

    <link rel="shortcut icon" href="../images/favicon/android-chrome-192x192.png" />
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/export.css">
    <link rel="stylesheet" href="../css/home.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

</head>

<body>

    <?php include "layout.php"; ?>

    <div class="container">

        <div class="products-card">

            <div class="products-header">

                <h2>Products</h2>

                <div>

                    <a href="add_product.php" class="add-product-btn">
                        <i class="fa-solid fa-plus"></i> Add Product
                    </a>

                    <button onclick="openExportPopup()" class="export-btn">
                        <i class="fa-solid fa-file-export"></i> Export
                    </button>

                </div>

            </div>

            <table id="productTable">

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Barcode</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Manufacturer</th>
                        <th>Expiry Applicable</th>
                        <th>Created At</th>
                    </tr>

                </thead>

                <tbody>

                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>

                        <tr>

                            <td><?php echo $row['prod_id']; ?></td>

                            <td><?php echo htmlspecialchars($row['barcode']); ?></td>

                            <td><?php echo htmlspecialchars($row['prod_name']); ?></td>

                            <td><?php echo htmlspecialchars($row['category']); ?></td>

                            <td><?php echo htmlspecialchars($row['manufacturer']); ?></td>

                            <td>
                                <?php
                                if ($row['expiry_applicable']) {
                                    echo "<span class='tick'>✔</span>";
                                } else {
                                    echo "<span class='cross'>✖</span>";
                                }
                                ?>
                            </td>

                            <td>
                                <?php echo date("d M Y | h:i A", strtotime($row['created_at'])); ?>
                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

        <footer>
            © <?php echo date('Y'); ?> EXPIROCHAIN
        </footer>

    </div>


    <!-- EXPORT POPUP -->
    <?php include "export.php"; ?>


    <script src="export.js"></script>


    <script>
        window.exportTable = null;

        $(document).ready(function() {

            exportTable = $('#productTable').DataTable({

                autoWidth: false,
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],

                dom: 'lBfrtip',

                buttons: [{
                        extend: 'csv',
                        title: 'EXPIROCHAIN Products'
                    },
                    {
                        extend: 'excel',
                        title: 'EXPIROCHAIN Products'
                    },
                    {
                        extend: 'pdf',
                        title: 'EXPIROCHAIN Products'
                    }
                ]

            });

            /* hide default datatable buttons */

            exportTable.buttons().container().hide();

        });
    </script>

</body>

</html>