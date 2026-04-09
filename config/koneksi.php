<?php 
// Set timezone ke Asia/Jakarta (UTC+7)
date_default_timezone_set('Asia/Jakarta');

$koneksi = mysqli_connect("localhost","root","","db_parkir_ukk");
 
// Check connection
if (mysqli_connect_errno()){
	echo "Koneksi database gagal : " . mysqli_connect_error();
}
 
?>