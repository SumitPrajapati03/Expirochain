<?php

require('../fpdf/fpdf.php');
include "../dbconnect.php";

$user_id = $_GET['user_id'];
$type = $_GET['type'];
$table = $_GET['table'];

/* STEP 1: GET USER DATABASE */

$q = "SELECT dbname FROM user_verification WHERE user_id='$user_id'";
$r = mysqli_query($conn,$q);

$data = mysqli_fetch_assoc($r);

if(!$data){
    die("User database not found");
}

$dbname = $data['dbname'];

/* STEP 2: CONNECT TO USER DATABASE */

mysqli_select_db($conn,$dbname);

/* STEP 3: SELECT TABLE */

if($table == "products"){
    $query = "SELECT * FROM prod_table ORDER BY prod_id DESC";
}
else{
    $query = "SELECT * FROM stock_table ORDER BY stock_id DESC";
}

$result = mysqli_query($conn,$query);

if(!$result){
    die(mysqli_error($conn));
}


/* ================= PDF EXPORT ================= */

if($type == "pdf"){

$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Products Report',0,1,'C');

$pdf->SetFont('Arial','B',10);

/* TABLE HEADER */

$header = mysqli_fetch_fields($result);

foreach($header as $col){
    $pdf->Cell(30,8,$col->name,1);
}

$pdf->Ln();

/* TABLE DATA */

$pdf->SetFont('Arial','',9);

while($row = mysqli_fetch_assoc($result)){

    foreach($row as $col){
        $pdf->Cell(30,8,$col,1);
    }

    $pdf->Ln();
}

$pdf->Output('D', $table.'_report.pdf');

exit();

}

/* ================= CSV ================= */

if($type == "csv" || $type == "excel"){

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="'.$table.'_report.csv"');

$output = fopen("php://output","w");

while($row = mysqli_fetch_assoc($result)){
    fputcsv($output,$row);
}

fclose($output);
exit();

}

/* ================= JSON ================= */

if($type == "json"){

header('Content-Type: application/json');

$data = [];

while($row = mysqli_fetch_assoc($result)){
    $data[] = $row;
}

echo json_encode($data);

exit();

}

/* ================= TXT ================= */

if($type == "txt"){

header('Content-Type: text/plain');

while($row = mysqli_fetch_assoc($result)){
    echo implode(" | ",$row)."\n";
}

exit();

}

?>