<?php
include '../koneksi.php';
$id_produk = $_GET['id_produk'] ?? '';
$out = [];
if ($id_produk) {
    $q = $conn->prepare("SELECT size FROM produk_stock WHERE id_produk=? AND size IS NOT NULL AND size != ''");
    $q->bind_param("i", $id_produk);
    $q->execute();
    $res = $q->get_result();
    while ($row = $res->fetch_assoc()) {
        $out[] = $row['size'];
    }
}
header('Content-Type: application/json');
echo json_encode($out);