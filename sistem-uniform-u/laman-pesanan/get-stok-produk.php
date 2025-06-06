<?php
include '../koneksi.php';
$id_produk = $_GET['id_produk'] ?? 0;
$size = $_GET['size'] ?? '';
if ($size) {
    $stmt = $conn->prepare("SELECT stok FROM produk_stock WHERE id_produk = ? AND size = ?");
    $stmt->bind_param("is", $id_produk, $size);
} else {
    $stmt = $conn->prepare("SELECT stok FROM produk_stock WHERE id_produk = ? AND (size IS NULL OR size = '')");
    $stmt->bind_param("i", $id_produk);
}
$stmt->execute();
$stmt->bind_result($stok);
$stmt->fetch();
echo json_encode(['stok' => (int)$stok]);
$stmt->close();
$conn->close();
?>