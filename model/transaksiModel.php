<?php

require_once __DIR__ . '/../config/koneksi.php';

class transaksiModel {

    private $koneksi;

    public function __construct() {
        global $koneksi;
        $this->koneksi = $koneksi;
    }

    // ===============================
    // Status IN (sedang parkir)
    // ===============================
    public function getActive() {

        $stmt = $this->koneksi->prepare("
            SELECT id, card_id, nopol, checkin_time, status
            FROM transaksi
            WHERE status = 'IN'
            ORDER BY checkin_time DESC
        ");

        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    // ===============================
    // Status OUT (menunggu pembayaran)
    // ===============================
    public function getPending() {

        $stmt = $this->koneksi->prepare("
            SELECT id, card_id, nopol, checkin_time, checkout_time, duration, fee, status
            FROM transaksi
            WHERE status = 'OUT'
            ORDER BY checkout_time DESC
        ");

        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row['fee_display'] = 'Rp' . number_format($row['fee'], 0, ',', '.');
            $data[] = $row;
        }

        return $data;
    }

    // ===============================
    // Status DONE (riwayat)
    // ===============================
    public function getHistory() {

        $stmt = $this->koneksi->prepare("
            SELECT id, card_id, nopol, checkin_time, checkout_time, duration, fee, status
            FROM transaksi
            WHERE status = 'DONE'
            ORDER BY checkout_time DESC
            LIMIT 50
        ");

        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {

            $row['duration_display'] = sprintf(
                '%d jam %d menit',
                intval($row['duration'] / 60),
                $row['duration'] % 60
            );

            $row['fee_display'] = 'Rp' . number_format($row['fee'], 0, ',', '.');

            $data[] = $row;
        }

        return $data;
    }

    // ===============================
    // Ambil transaksi OUT berdasarkan ID
    // ===============================
    public function getTransaksiOutById($id) {

        $stmt = $this->koneksi->prepare("
            SELECT id, card_id, nopol, fee
            FROM transaksi
            WHERE id = ? AND status = 'OUT'
            LIMIT 1
        ");

        $stmt->bind_param('i', $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // ===============================
    // Update transaksi jadi DONE
    // ===============================
    public function updateToDone($id, $paid_amount, $change_amount) {

        $stmt = $this->koneksi->prepare("
            UPDATE transaksi
            SET status = 'DONE',
                paid_amount = ?,
                change_amount = ?,
                paid_at = NOW()
            WHERE id = ?
        ");

        $stmt->bind_param('iii', $paid_amount, $change_amount, $id);

        return $stmt->execute();
    }

    // ===============================
    // Insert log aktivitas
    // ===============================
    public function insertLog($transaksi_id, $action, $message, $user_id) {

        $stmt = $this->koneksi->prepare("
            INSERT INTO logs (transaksi_id, action, message, user_id)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param('issi', $transaksi_id, $action, $message, $user_id);

        return $stmt->execute();
    }
}
?>