<?php
session_start();

require_once __DIR__ . '/../model/checkinModel.php';

header('Content-Type: application/json');

// Format nopol otomatis (B 1234 ABC)
function formatNopol($nopol){

    // hilangkan spasi
    $nopol = strtoupper(str_replace(' ', '', $nopol));

    // pisahkan huruf depan
    preg_match('/^([A-Z]+)([0-9]+)([A-Z]*)$/', $nopol, $matches);

    if(count($matches) >= 3){

        $depan = $matches[1];   // B
        $angka = $matches[2];   // 1234
        $belakang = $matches[3] ?? ''; // ABC

        if($belakang != ''){
            return $depan . ' ' . $angka . ' ' . $belakang;
        }else{
            return $depan . ' ' . $angka;
        }

    }

    return $nopol;
}
// Pastikan user login & pegawai
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'pegawai') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$card_id = isset($_POST['card_id']) ? trim($_POST['card_id']) : '';
$nopol = isset($_POST['nopol']) ? trim($_POST['nopol']) : '';
$nopol = formatNopol($nopol);

if (empty($card_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Card ID tidak boleh kosong']);
    exit;
}

$model = new checkinModel();

// Cek kartu aktif
$cek = $model->cekKartuAktif($card_id);

if ($cek->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Kendaraan dengan tag ini masih parkir!']);
    exit;
}

// Insert checkin
$checkin_time = date('Y-m-d H:i:s');
$transaksi_id = $model->insertCheckin($card_id, $nopol, $checkin_time);

if ($transaksi_id) {

    // Insert log
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    $log_message = "Check-in untuk kartu $card_id";
    $model->insertLog($transaksi_id, $log_message, $user_id);

    echo json_encode([
        'status' => 'success',
        'message' => 'Selamat Datang! Silakan Masuk',
        'transaksi_id' => $transaksi_id,
        'card_id' => $card_id,
        'checkin_time' => $checkin_time
    ]);

} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menyimpan data'
    ]);
}