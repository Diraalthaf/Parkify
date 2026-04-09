<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/koneksi.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

$server   = 'broker.hivemq.com';
$port     = 1883;  
$clientId = 'php-listener-' . uniqid();

$mqtt = new MqttClient($server, $port, $clientId);

$connectionSettings = (new ConnectionSettings())
    ->setKeepAliveInterval(60)
    ->setConnectTimeout(10);

try {
    $mqtt->connect($connectionSettings);
    echo "Terhubung ke HiveMQ...\n";
} catch (Exception $e) {
    die("Gagal konek: " . $e->getMessage() . "\n");
}

$topicEntryRfid = 'parking/dira/entry/rfid';
$topicExitRfid  = 'parking/dira/exit/rfid';
$topicEntryServo = 'parking/dira/entry/servo';
$topicExitServo  = 'parking/dira/exit/servo';
$topicLcd        = 'parking/dira/lcd';

// ─── CHECK-IN ───────────────────────────────
$mqtt->subscribe($topicEntryRfid, function ($topic, $message) use ($koneksi, $mqtt, $topicEntryServo, $topicLcd) {

    $data    = json_decode($message, true);
    $card_id = strtoupper(trim($data['rfid'] ?? ''));

    if (empty($card_id)) return;

    echo "[MASUK] Card: $card_id\n";

    // Cek apakah sudah parkir
    $stmt = $koneksi->prepare("SELECT id FROM transaksi WHERE card_id = ? AND status = 'IN' LIMIT 1");
    $stmt->bind_param("s", $card_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "  → Sudah parkir, diabaikan.\n";
        return;
    }

    // Cek card terdaftar (opsional, skip jika tidak ada tabel kartu)
    $checkin_time = date('Y-m-d H:i:s');
    $angka = rand(1000, 9999);
    $huruf = chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90));
    $nopol = "B $angka $huruf";

    $stmt_insert = $koneksi->prepare("INSERT INTO transaksi (card_id, nopol, checkin_time, status) VALUES (?, ?, ?, 'IN')");
    $stmt_insert->bind_param("sss", $card_id, $nopol, $checkin_time);

    if ($stmt_insert->execute()) {
        echo "  → Check-in berhasil. Nopol: $nopol\n";
        // Buka palang masuk
        $mqtt->publish($topicEntryServo, 'OPEN', 0, false);
    } else {
        echo "  → Gagal insert: " . $stmt_insert->error . "\n";
    }

}, 0);

// ─── CHECK-OUT ──────────────────────────────
$mqtt->subscribe($topicExitRfid, function ($topic, $message) use ($koneksi, $mqtt, $topicExitServo, $topicLcd) {

    $data    = json_decode($message, true);
    $card_id = strtoupper(trim($data['rfid'] ?? ''));

    if (empty($card_id)) return;

    echo "[KELUAR] Card: $card_id\n";

    $stmt = $koneksi->prepare("SELECT id, checkin_time FROM transaksi WHERE card_id = ? AND status = 'IN' ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("s", $card_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "  → Tidak ada transaksi aktif.\n";
        return;
    }

    $row             = $result->fetch_assoc();
    $transaksi_id    = $row['id'];
    $checkin_ts      = strtotime($row['checkin_time']);
    $checkout_ts     = time();
    $durasi_menit    = max(1, (int)ceil(($checkout_ts - $checkin_ts) / 60));
    $tarif_per_menit = 50; // Rp50/menit = Rp3000/jam
    $total_bayar     = $durasi_menit * $tarif_per_menit;
    $checkout_format = date('Y-m-d H:i:s', $checkout_ts);

    $jam    = intdiv($durasi_menit, 60);
    $menit  = $durasi_menit % 60;

    $stmt_update = $koneksi->prepare("UPDATE transaksi SET checkout_time = ?, duration = ?, fee = ?, status = 'OUT' WHERE id = ?");
    $stmt_update->bind_param("siii", $checkout_format, $durasi_menit, $total_bayar, $transaksi_id);

    if ($stmt_update->execute()) {
        echo "  → Checkout berhasil. Durasi: {$jam}j {$menit}m, Biaya: Rp$total_bayar\n";

        // Kirim info ke LCD OLED
        $lcd_msg = "Durasi: {$jam} Jam {$menit} m|Total: Rp" . number_format($total_bayar, 0, ',', '.');
        $mqtt->publish($topicLcd, $lcd_msg, 0, false);

        // Palang keluar dibuka setelah pegawai konfirmasi pembayaran
        // (bukan otomatis — dibuka via handle_payment.php di dashboard)
    } else {
        echo "  → Gagal update: " . $stmt_update->error . "\n";
    }

}, 0);

echo "Listener aktif. Menunggu data RFID...\n";

// Loop selamanya
while (true) {
    try {
        if (!$mqtt->isConnected()) {
            echo "Reconnect...\n";
            $mqtt->connect();
        }
        $mqtt->loop(true, true);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        sleep(3);
        try { $mqtt->connect(); } catch (Exception $e2) { echo "Reconnect gagal.\n"; }
    }
}