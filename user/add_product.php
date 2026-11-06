<?php
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['firm_name'])) {
  header("Location: ../login.php");
  exit();
}

$firm_name = $_SESSION['firm_name'];
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <title>Add Product | EXPIROCHAIN</title>

  <link rel="shortcut icon" href="../images/favicon/android-chrome-192x192.png" />
  <link rel="stylesheet" href="../css/home.css" />
  <link rel="stylesheet" href="css/add_product.css" />

</head>

<body>
  <?php include "layout.php"; ?>

  <div class="dashboard">
    <div class="add-product-card">
      <h2>Add Product</h2>

      <form action="save_product.php" method="POST">
        <!-- BARCODE -->

        <label>Barcode</label>
        <input type="text" name="barcode" required />

        <!-- PRODUCT NAME -->

        <label>Product Name</label>
        <input type="text" name="prod_name" required />

        <!-- PRODUCT TYPE -->

        <label>Product Type</label>

        <select name="category" required>
          <option value="">Select Type</option>
          <option value="Medicine">Medicine</option>
          <option value="Cosmetic">Cosmetic</option>
          <option value="Other">Other</option>
        </select>

        <!-- MANUFACTURER -->

        <label>Manufacturer Name</label>
        <input type="text" name="manufacturer" />

        <!-- EXPIRY CHECKBOX -->

        <div class="checkbox-row">
          <input type="checkbox" name="expiry_applicable" value="1" />

          <span>Expiry Applicable</span>
        </div>

        <!-- BUTTONS -->

        <div class="form-buttons">
          <button type="submit" class="btn-add">Add</button>

          <button type="reset" class="btn-reset">Reset</button>
        </div>
      </form>
    </div>
  </div>

  <footer>© <?php echo date('Y'); ?> EXPIROCHAIN and Team</footer>
</body>

</html>