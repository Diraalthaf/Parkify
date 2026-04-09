<?php
session_start();

require_once __DIR__ . '/../model/transaksiModel.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$type = $_GET['type'] ?? 'all';

$model = new transaksiModel();

if ($type === 'active') {

    $data = $model->getActive();
    echo json_encode(['status' => 'success', 'data' => $data]);

} elseif ($type === 'pending') {

    $data = $model->getPending();
    echo json_encode(['status' => 'success', 'data' => $data]);

} elseif ($type === 'history') {

    $data = $model->getHistory();
    echo json_encode(['status' => 'success', 'data' => $data]);

} else {

    echo json_encode(['status' => 'error', 'message' => 'Invalid type']);
}
?>