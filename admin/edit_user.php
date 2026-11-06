<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

include "../dbconnect.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT u.*, 
          v.isApproved,
          v.dbname,
          v.gst_proof_path,
          v.dl1_proof_path,
          v.dl2_proof_path
FROM user_table u
LEFT JOIN user_verification v
ON u.user_id = v.user_id
WHERE u.user_id=$id";

$result = mysqli_query($conn,$query);
$row = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html>
<head>

<title>EXPIROCHAIN - Approve User</title>

<link rel="shortcut icon" href="../images/favicon/android-chrome-192x192.png" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/edit_user.css">

</head>

<body>

<!-- HEADER -->

<div class="header">
<h1>EXPIROCHAIN Admin</h1>
<a href="../logout.php" class="logout-btn">Logout</a>
</div>

<!-- DASHBOARD -->

<div class="dashboard">

<div class="top-bar">
<button type="button" class="back-btn" onclick="history.back()">
<i class="fa-solid fa-arrow-left"></i> Back
</button>
</div>
<div class="form-container">


<h2>Approve User</h2>

<form action="update_user.php" method="POST">

<input type="hidden" name="id" value="<?php echo $row['user_id']; ?>">

<!-- Row 1 -->

<div class="row">

<div class="field">
<label>Mobile No</label>
<input type="text" name="phn_no" value="<?php echo $row['phn_no']; ?>">
</div>

<div class="field">
<label>Organization Type</label>

<select name="user_type">

<option value="Medical" <?php if($row['user_type']=="Medical") echo "selected"; ?>>Medical</option>
<option value="NGO" <?php if($row['user_type']=="NGO") echo "selected"; ?>>NGO</option>
<option value="Clinic" <?php if($row['user_type']=="Clinic") echo "selected"; ?>>Clinic</option>

</select>

</div>

</div>

<!-- Row 2 -->

<div class="row">

<div class="field">
<label>Firm Name</label>
<input type="text" name="firm_name" value="<?php echo $row['firm_name']; ?>">
</div>

<div class="field">
<label>Owner Name</label>
<input type="text" name="owner_name" value="<?php echo $row['owner_name']; ?>">
</div>

</div>

<!-- Row 3 -->

<div class="row">

<div class="field">
<label>Email</label>
<input type="text" name="email_id" value="<?php echo $row['email_id']; ?>">
</div>

<div class="field">
<label>Address</label>
<textarea name="address"><?php echo $row['address']; ?></textarea>
</div>

</div>

<!-- Row 4 -->

<div class="row">

<div class="field">
<label>GST No</label>
<input type="text" name="gstno" value="<?php echo $row['gstno']; ?>">
</div>

<div class="field">
<label>DL No 1</label>
<input type="text" name="dl1" value="<?php echo $row['dl1']; ?>">
</div>

<div class="field">
<label>DL No 2</label>
<input type="text" name="dl2" value="<?php echo $row['dl2']; ?>">
</div>

</div>

<h3>Documents Verification</h3>

<div class="doc-grid">

<div class="doc-box">
<p>GST Proof</p>
<img src="../<?php echo $row['gst_proof_path']; ?>" class="doc-img">
</div>

<div class="doc-box">
<p>DL No1 Proof</p>
<img src="../<?php echo $row['dl1_proof_path']; ?>" class="doc-img">
</div>

<div class="doc-box">
<p>DL No2 Proof</p>
<img src="../<?php echo $row['dl2_proof_path']; ?>" class="doc-img">
</div>

</div>

<!-- ACTION BUTTONS -->

<div class="buttons">

<button type="submit" name="action" value="approve" class="approve">
<i class="fa-solid fa-floppy-disk"></i> Approve
</button>

<button type="submit" name="action" value="reject" class="reject">
Reject
</button>

<button type="button" class="cancel" onclick="history.back()">
Cancel
</button>

</div>

</form>

</div>

</div>

<footer>
© 2026 Expirochain
</footer>

<!-- IMAGE MODAL -->

<div id="imgModal" class="img-modal">

<span class="close">&times;</span>

<div class="img-controls">
<button id="rotateLeft"><i class="fa-solid fa-rotate-left"></i></button>
<button id="rotateRight"><i class="fa-solid fa-rotate-right"></i></button>
</div>

<img class="modal-content" id="modalImg">

</div>

<script>

const modal = document.getElementById("imgModal");
const modalImg = document.getElementById("modalImg");
const images = document.querySelectorAll(".doc-img");
const closeBtn = document.querySelector(".close");

let rotation = 0;

/* open image */

images.forEach(img => {
    img.onclick = function(){
        modal.style.display = "block";
        modalImg.src = this.src;
        rotation = 0;
        modalImg.style.transform = "translate(-50%, -50%) rotate(0deg)";
    }
});

/* close button */

closeBtn.onclick = function(){
    modal.style.display = "none";
}

/* click outside closes */

modal.onclick = function(e){
    if(e.target === modal){
        modal.style.display = "none";
    }
}

/* ESC key closes */

document.addEventListener("keydown", function(e){
    if(e.key === "Escape"){
        modal.style.display = "none";
    }
});

/* rotate left */

document.getElementById("rotateLeft").onclick = function(){
    rotation -= 90;
    modalImg.style.transform = `translate(-50%, -50%) rotate(${rotation}deg)`;
};

/* rotate right */

document.getElementById("rotateRight").onclick = function(){
    rotation += 90;
    modalImg.style.transform = `translate(-50%, -50%) rotate(${rotation}deg)`;
};

</script>

</body>
</html>