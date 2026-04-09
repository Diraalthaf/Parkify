<?php
session_start();

$page = $_GET['page'] ?? 'login';

// handle logout langsung
if ($page === 'logout') {
  session_destroy();
  header('Location: index.php?page=login');
  exit;
}

switch ($page) {
  case 'login':
    require 'view/login.php';
    break;

  case 'cek_login':
    require 'controller/cek_login.php';
    break;

  case 'admin':
    require 'view/pages/halaman_admin.php';
    break;

  case 'petugas':
    require 'view/pages/halaman_petugas.php';
    break;

  case 'owner':
    require 'view/pages/halaman_owner.php';
    break;

  case 'logs':
    require 'view/pages/logs_riwayat.php';
    break;

  default:
    echo "Halaman tidak ditemukan";
}
