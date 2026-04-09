<?php
session_start();

require_once __DIR__ . '/../model/getlogsModel.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$start_date = $_GET['start_date'] ?? '';
$end_date   = $_GET['end_date'] ?? '';

if (empty($start_date) || empty($end_date)) {
    echo json_encode(['status' => 'error', 'message' => 'Tanggal harus diisi']);
    exit;
}

$start_datetime = $start_date . ' 00:00:00';
$end_datetime   = $end_date . ' 23:59:59';

$model = new getlogsModel();
$logs = $model->getLogsByDate($start_datetime, $end_datetime);

echo json_encode([
    'status' => 'success',
    'data'   => $logs,
    'total'  => count($logs)
]);
?>