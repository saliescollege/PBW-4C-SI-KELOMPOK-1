<?php
include '../koneksi.php';

// Ambil id produk dari URL (misal: get_size.php?id_produk=2)
$id_produk = isset($_GET['id_produk']) ? intval($_GET['id_produk']) : 0;
$sizes = [];

// Jika id produk ada, ambil semua size dari produk tersebut
if ($id_produk) {
    $res = mysqli_query($conn, "SELECT id, size FROM produk_stock WHERE id_produk = $id_produk");
    while ($row = mysqli_fetch_assoc($res)) {
        // Masukkan id dan size ke array
        $sizes[] = [
            'id' => $row['id'],
            'size' => $row['size']
        ];
    }
}

// Kirim data size dalam format JSON ke JavaScript
header('Content-Type: application/json');
echo json_encode($sizes);