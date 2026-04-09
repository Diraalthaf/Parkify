<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/koneksi.php';

use Dompdf\Dompdf;

$query = mysqli_query($koneksi, "
    SELECT card_id, nopol, checkin_time, checkout_time, duration, fee 
    FROM transaksi
    WHERE status='DONE'
    ORDER BY checkout_time DESC
");

$html = '
<h2 style="text-align:center;">Laporan Riwayat Parkir</h2>
<table border="1" cellspacing="0" cellpadding="6" width="100%">
<tr>
<th>No</th>
<th>Card ID</th>
<th>No Polisi</th>
<th>Masuk</th>
<th>Keluar</th>
<th>Durasi</th>
<th>Biaya</th>
</tr>
';

$no = 1;

while ($row = mysqli_fetch_assoc($query)) {

    $jam = floor($row['duration']/60);
    $menit = $row['duration']%60;

    $durasi = $jam.' jam '.$menit.' menit';

    $html .= "
    <tr>
        <td>".$no++."</td>
        <td>".$row['card_id']."</td>
        <td>".$row['nopol']."</td>
        <td>".$row['checkin_time']."</td>
        <td>".$row['checkout_time']."</td>
        <td>".$durasi."</td>
        <td>Rp".number_format($row['fee'],0,',','.')."</td>
    </tr>
    ";
}

$html .= "</table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','portrait');
$dompdf->render();

$dompdf->stream("laporan_parkir.pdf", ["Attachment"=>0]);