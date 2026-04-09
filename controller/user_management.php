<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start();
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../model/user_managementModel.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$model = new UserModel($koneksi);
$action = $_GET['action'] ?? '';

/* ================= LIST ================= */
if ($action === 'list') {

    $users = $model->getAllUsers();
    echo json_encode(['status' => 'success', 'data' => $users]);
    exit;
}

/* ================= GET ================= */
if ($action === 'get') {

    $id = intval($_GET['id'] ?? 0);
    $user = $model->getUserById($id);

    if ($user) {
        echo json_encode(['status' => 'success', 'data' => $user]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User tidak ditemukan']);
    }
    exit;
}

/* ================= CREATE ================= */
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $level = trim($_POST['level']);

    if ($model->usernameExists($username)) {
        echo json_encode(['status' => 'error', 'message' => 'Username sudah ada']);
        exit;
    }

    if ($level === 'owner') $level = 'pengurus';

    $create = $model->createUser($nama, $username, $password, $level);

    echo json_encode([
        'status' => $create ? 'success' : 'error',
        'message' => $create ? 'User berhasil dibuat' : 'Gagal membuat user'
    ]);
    exit;
}

/* ================= UPDATE ================= */
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = intval($_POST['id']);
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'] ?? '';
    $level = trim($_POST['level']);

    if ($model->usernameExists($username, $id)) {
        echo json_encode(['status' => 'error', 'message' => 'Username sudah dipakai']);
        exit;
    }

    if ($level === 'owner') $level = 'pengurus';

    $update = $model->updateUser($id, $nama, $username, $password, $level);

    echo json_encode([
        'status' => $update ? 'success' : 'error',
        'message' => $update ? 'User berhasil diupdate' : 'Gagal update'
    ]);
    exit;
}

/* ================= DELETE ================= */
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = intval($_POST['id']);
    $delete = $model->deleteUser($id);

    echo json_encode([
        'status' => $delete ? 'success' : 'error',
        'message' => $delete ? 'User berhasil dihapus' : 'Gagal delete'
    ]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Action tidak valid']);