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

<h2>User Verification Dashboard</h2>

<button onclick="openExportPopup()" class="export-btn">
<i class="fa-solid fa-file-export"></i> Export
</button>

</div>

<table id="userTable">

<thead>

<tr>

<th>Approved</th>
<th>ID</th>
<th style="text-align:center;">Firm Name</th>
<th style="text-align:center;">User Type</th>
<th style="text-align:center;">GST</th>
<th style="text-align:center;">DL1</th>
<th style="text-align:center;">DL2</th>
<th>Phone</th>
<th>Email</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php
while($row=mysqli_fetch_assoc($result)){
?>

<tr>

<td style="text-align:center;font-size:18px;">
<?php
if($row['isApproved']==1){
echo "<span style='color:#16a34a;font-weight:bold;'>✔</span>";
}else{
echo "<span style='color:#dc2626;font-weight:bold;'>✖</span>";
}
?>
</td>

<td style="text-align:center;">
<?php echo $row['user_id']; ?>
</td>

<td style="text-align:center;">
<?php echo htmlspecialchars($row['firm_name']); ?>
</td>

<td style="text-align:center;">
<?php echo htmlspecialchars($row['user_type']); ?>
</td>

<td style="text-align:center;">
<?php echo htmlspecialchars($row['gstno']); ?>
</td>

<td style="word-break:break-all;">
<?php echo htmlspecialchars($row['dl1']); ?>
</td>

<td style="word-break:break-all;">
<?php echo htmlspecialchars($row['dl2']); ?>
</td>

<td style="text-align:center;">
<?php echo htmlspecialchars($row['phn_no']); ?>
</td>

<td style="word-break:break-all;">
<?php echo htmlspecialchars($row['email_id']); ?>
</td>

<td style="text-align:center;">
<a href="edit_user.php?id=<?php echo $row['user_id']; ?>" class="edit-btn">
Edit
</a>
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
order:[[0,'desc']],

dom:'lBfrtip',

buttons:[
{
extend:'csv',
title:'User Verification EXPIROCHAIN'
},
{
extend:'excel',
title:'User Verification EXPIROCHAIN'
},
{
extend:'pdf',
title:'User Verification EXPIROCHAIN'
}
]

});

/* hide default datatable buttons */

exportTable.buttons().container().hide();

});

</script>

</body>
</html>