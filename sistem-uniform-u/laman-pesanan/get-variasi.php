<?php
include '../koneksi.php';

// Ambil id produk dari URL (misal: get-variasi.php?id_produk=2)
$id_produk = $_GET['id_produk'] ?? '';
$out = [];

// Jika id produk ada, ambil semua size yang tersedia dari produk tersebut
if ($id_produk) {
    $q = $conn->prepare("SELECT size FROM produk_stock WHERE id_produk=? AND size IS NOT NULL AND size != ''");
    $q->bind_param("i", $id_produk);
    $q->execute();
    $res = $q->get_result();
    while ($row = $res->fetch_assoc()) {
        // Masukkan size ke array
        $out[] = $row['size'];
    }
}

// Kirim data size dalam format JSON ke JavaScript
header('Content-Type: application/json');
echo json_encode($out);