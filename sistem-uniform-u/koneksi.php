<?php
// Data koneksi ke database
$servername = "localhost";  // server database, biasanya localhost
$username = "root";         // username MySQL
$password = "";             // password MySQL, kosong jika belum diisi
$dbname = "db_uniform";    // nama database yang akan digunakan

// Membuat koneksi baru menggunakan mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek apakah koneksi gagal
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

?>
