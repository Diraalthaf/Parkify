<?php

require_once __DIR__ . '/../config/koneksi.php';

class checkinModel {

    private $koneksi;

    public function __construct() {
        global $koneksi;
        $this->koneksi = $koneksi;
    }

    // Cek apakah kartu masih aktif (belum DONE)
    public function cekKartuAktif($card_id) {

        $stmt = $this->koneksi->prepare(
            "SELECT id 
             FROM transaksi 
             WHERE card_id = ? 
             AND status IN ('IN','OUT') 
             LIMIT 1"
        );

        $stmt->bind_param('s', $card_id);
        $stmt->execute();

        return $stmt->get_result();
    }

    // Insert transaksi checkin
    public function insertCheckin($card_id, $nopol, $checkin_time) {

    $stmt = $this->koneksi->prepare(
        "INSERT INTO transaksi (card_id, nopol, checkin_time, status) 
         VALUES (?, ?, ?, 'IN')"
    );

    $stmt->bind_param('sss', $card_id, $nopol, $checkin_time);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        return $this->koneksi->insert_id;
    }

    return false;
}

    // Insert log aktivitas
    public function insertLog($transaksi_id, $message, $user_id) {

        $stmt = $this->koneksi->prepare(
            "INSERT INTO logs (transaksi_id, action, message, user_id) 
             VALUES (?, 'CHECKIN', ?, ?)"
        );

        $stmt->bind_param('isi', $transaksi_id, $message, $user_id);

        return $stmt->execute();
    }
}