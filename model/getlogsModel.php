<?php

require_once __DIR__ . '/../config/koneksi.php';

class getlogsModel {

    private $koneksi;

    public function __construct() {
        global $koneksi;
        $this->koneksi = $koneksi;
    }

    public function getLogsByDate($start_datetime, $end_datetime) {

        $stmt = $this->koneksi->prepare("
            SELECT 
                l.id as log_id,
                l.action,
                l.message,
                l.created_at,
                l.user_id,
                u.username as petugas_username,
                t.card_id,
                t.nopol,
                t.checkin_time,
                t.checkout_time,
                t.duration,
                t.fee,
                t.status as transaksi_status
            FROM logs l
            LEFT JOIN user u ON l.user_id = u.id
            LEFT JOIN transaksi t ON l.transaksi_id = t.id
            WHERE l.created_at BETWEEN ? AND ?
            ORDER BY l.created_at DESC
        ");

        $stmt->bind_param('ss', $start_datetime, $end_datetime);
        $stmt->execute();
        $result = $stmt->get_result();

        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }

        return $logs;
    }
}