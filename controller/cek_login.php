<?php
session_start();

require_once __DIR__ . '/../model/CekLogin_model.php';

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($username === '' || $password === '') {
	header('Location: index.php?page=login&pesan=gagal');
	exit;
}

$model = new CekLogin_model();
$result = $model->getUserByUsername($username);

if ($result->num_rows === 0) {
	header('Location: index.php?page=login&pesan=user_tidak_ada');
	exit;
}

$data = $result->fetch_assoc();

// Cek password (hashed atau plain)
$dbPass = $data['password'];
$passwordOk = false;

if (password_verify($password, $dbPass)) {
	$passwordOk = true;
} elseif ($password === $dbPass) {
	$passwordOk = true;
}

if (!$passwordOk) {
	header('Location: index.php?page=login&pesan=password_salah');
	exit;
}

// Set session
$_SESSION['user_id'] = $data['id'];
$_SESSION['username'] = $data['username'];

$role = $data['level'];
if ($role === 'owner') $role = 'owner';
$_SESSION['level'] = $role;

// Redirect berdasarkan role
if ($role === 'admin') {
	header('Location: index.php?page=admin');
	exit;
} elseif ($role === 'pegawai') {
	header('Location: index.php?page=petugas');
	exit;
} elseif ($role === 'owner') {
	header('Location: index.php?page=owner');
	exit;
} else {
	header('Location: index.php?page=login&pesan=gagal');
	exit;
}
?>