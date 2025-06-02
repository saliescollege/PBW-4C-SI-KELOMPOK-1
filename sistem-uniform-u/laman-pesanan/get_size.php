<?php
include '../koneksi.php';

$id_produk = isset($_GET['id_produk']) ? intval($_GET['id_produk']) : 0;
$sizes = [];

if ($id_produk) {
    $res = mysqli_query($conn, "SELECT id, size FROM produk_stock WHERE id_produk = $id_produk");
    while ($row = mysqli_fetch_assoc($res)) {
        $sizes[] = [
            'id' => $row['id'],
            'size' => $row['size']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($sizes);