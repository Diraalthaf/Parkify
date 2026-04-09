<?php
session_start();

require_once __DIR__ . '/../model/transaksiModel.php';
require_once __DIR__ . '/../mqtt_publish.php';
require __DIR__ . '/../vendor/autoload.php';
use PhpMqtt\Client\MqttClient;

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['level'] != 'pegawai') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$transaksi_id = intval($_POST['transaksi_id'] ?? 0);
$amount_paid  = intval($_POST['amount_paid'] ?? 0);

if ($transaksi_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Transaksi ID tidak valid']);
    exit;
}

$model = new transaksiModel();

// Ambil data dari model
$transaksi = $model->getTransaksiOutById($transaksi_id);

if (!$transaksi) {
    echo json_encode(['status' => 'error', 'message' => 'Transaksi tidak ditemukan']);
    exit;
}

$required_fee = intval($transaksi['fee']);
$card_id = $transaksi['card_id'];

if ($amount_paid < $required_fee) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Pembayaran kurang',
        'balance' => $required_fee - $amount_paid
    ]);
    exit;
}

$kembalian = $amount_paid - $required_fee;

// Update via model
$update = $model->updateToDone($transaksi_id, $amount_paid, $kembalian);

if ($update) {

    $user_id = $_SESSION['user_id'] ?? NULL;

    $model->insertLog(
        $transaksi_id,
        'PAYMENT',
        "Pembayaran Rp$amount_paid, Fee Rp$required_fee, Kembalian Rp$kembalian",
        $user_id
    );

    $model->insertLog(
        $transaksi_id,
        'GATE_OPEN',
        "Gerbang dibuka untuk kartu $card_id",
        $user_id
    );

    $server = 'broker.hivemq.com';
    $port = 1883;
    $clientId = 'php-publisher-' . rand(1, 1000);

    $mqtt = new MqttClient($server, $port, $clientId);

    try {
        $mqtt->connect();

        $mqtt->publish('parking/dira/exit/servo', 'OPEN', 0);

        $mqtt->disconnect();
    } catch (Exception $e) {
        error_log("MQTT Error: " . $e->getMessage());
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Pembayaran berhasil',
        'kembalian' => $kembalian
    ]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal update']);
}