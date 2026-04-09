<?php

require_once __DIR__ . '/../config/koneksi.php';

class getrecapModel {

    private $koneksi;

    public function __construct() {
        global $koneksi;
        $this->koneksi = $koneksi;
    }

    public function getRecapByDate($start_datetime, $end_datetime) {

        $stmt = $this->koneksi->prepare("
            SELECT 
                id,
                card_id,
                checkin_time,
                checkout_time,
                duration,
                fee,
                status
            FROM transaksi
            WHERE status = 'DONE'
            AND checkout_time BETWEEN ? AND ?
            ORDER BY checkout_time DESC
        ");

        $stmt->bind_param('ss', $start_datetime, $end_datetime);
        $stmt->execute();
        $result = $stmt->get_result();

        $transactions = [];
        $total_revenue = 0;
        $total_duration = 0;

        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
            $total_revenue += $row['fee'];
            $total_duration += $row['duration'];
        }

        $total_transactions = count($transactions);
        $avg_duration = $total_transactions > 0 
            ? $total_duration / $total_transactions 
            : 0;

        return [
            'transactions' => $transactions,
            'stats' => [
                'total_transactions' => $total_transactions,
                'total_revenue' => $total_revenue,
                'avg_duration' => $avg_duration
            ]
        ];
    }
}