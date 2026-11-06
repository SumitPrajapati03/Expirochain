<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

include "../dbconnect.php";

$query = "SELECT u.*, v.isApproved
FROM user_table u
LEFT JOIN user_verification v
ON u.user_id = v.user_id";

$result = mysqli_query($conn,$query);
?>

<!DOCTYPE html>
<html>

<head>

<title>User Verification | EXPIROCHAIN</title>

<link rel="stylesheet" href="css/admin_vrf.css">
<link rel="shortcut icon" href="../images/favicon/android-chrome-192x192.png" />
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

<div class="header-bar">

<h2>User Table Dashboard</h2>

<button onclick="openExportPopup()" class="export-btn">
<i class="fa-solid fa-file-export"></i> Export
</button>

</div>

<table id="userTable">

<thead>

<tr>

<th>Approved</th>
<th style="text-align:center;">ID</th>
<th>Firm Name</th>
<th>Owner Name</th>
<th>User Type</th>
<th style="text-align:center;">GST</th>
<th style="text-align:center;">DL1</th>
<th style="text-align:center;">DL2</th>
<th>Phone</th>
<th>Email</th>
<th style="text-align:center;">Address</th>
<th style="text-align:center;">Created At</th>

</tr>

</thead>

<tbody>

<?php
while($row=mysqli_fetch_assoc($result)){
?>

<tr>

<!-- APPROVAL -->

<td style="text-align:center; font-size:18px;">

<?php
if($row['isApproved']==1){
echo "<span style='color:#16a34a;font-weight:bold;'>✔</span>";
}else{
echo "<span style='color:#dc2626;font-weight:bold;'>✖</span>";
}
?>

</td>

<!-- USER ID -->

<td style="text-align:center;">
<?php echo $row['user_id']; ?>
</td>

<!-- FIRM NAME -->

<td>
<?php echo nl2br(htmlspecialchars($row['firm_name'])); ?>
</td>

<!-- OWNER NAME -->

<td>
<?php echo nl2br(htmlspecialchars($row['owner_name'])); ?>
</td>

<!-- USER TYPE -->

<td>
<?php echo htmlspecialchars($row['user_type']); ?>
</td>

<!-- GST -->

<td style="text-align:center;">
<?php echo htmlspecialchars($row['gstno']); ?>
</td>

<!-- DL1 -->

<td style="word-break:break-all; max-width:120px;">
<?php echo htmlspecialchars($row['dl1']); ?>
</td>

<!-- DL2 -->

<td style="word-break:break-all; max-width:120px;text-align:center;">
<?php echo htmlspecialchars($row['dl2']); ?>
</td>

<!-- PHONE -->

<td style="text-align:center;">
<?php echo htmlspecialchars($row['phn_no']); ?>
</td>

<!-- EMAIL -->

<td style="word-break:break-all;">
<?php echo htmlspecialchars($row['email_id']); ?>
</td>

<!-- ADDRESS -->

<td style="max-width:220px;">
<?php echo nl2br(htmlspecialchars($row['address'])); ?>
</td>

<!-- CREATED AT -->

<td style="text-align:center;">

<?php
if(!empty($row['registered_at'])){
echo date("d M Y | h:i A", strtotime($row['registered_at']));
}else{
echo "-";
}
?>

</td>

</tr>

<?php
}
?>

</tbody>

</table>

</div>

<?php include "../user/export.php"; ?>

<script src="../user/export.js"></script>

<script>

window.exportTable = null;

$(document).ready(function(){

exportTable = $('#userTable').DataTable({

autoWidth:false,
pageLength:10,
order:[[1,'desc']],

dom:'lBfrtip',

buttons:[
{
extend:'csv',
title:'User Table EXPIROCHAIN'
},
{
extend:'excel',
title:'User Table EXPIROCHAIN'
},
{
extend:'pdf',
title:'User Table EXPIROCHAIN'
}
]

});

/* hide default datatable buttons */

exportTable.buttons().container().hide();

});

</script>

</body>

</html>