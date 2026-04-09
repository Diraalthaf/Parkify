<?php
require __DIR__ . '/../config/koneksi.php';

$id = $_GET['id'];

$query = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

$menit = (int)$data['duration'];
$jam = floor($menit / 60);
$sisa_menit = $menit % 60;

$durasi_format = '';

if ($jam > 0) {
    $durasi_format .= $jam . ' jam ';
}

$durasi_format .= $sisa_menit . ' menit';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Struk Parkir</title>
    <style>
        body { font-family: monospace; width: 300px; }
        h2 { text-align: center; }
    </style>
</head>
<body onload="window.print()">
<h2>Parkify</h2>
<hr>
Card ID : <?= $data['card_id'] ?><br>
No Pol  : <?= $data['nopol'] ?? '-' ?><br>
<hr>
Masuk   : <?= date('d-m-Y H:i', strtotime($data['checkin_time'])) ?><br>
Keluar  : <?= date('d-m-Y H:i', strtotime($data['checkout_time'])) ?><br>
Durasi  : <?= $durasi_format ?><br>
<hr>
Biaya   : Rp<?= number_format($data['fee'],0,',','.') ?><br>
<hr>
THANK YOU FOR PARKING HERE HELL YEAH!!
</body>
</html>