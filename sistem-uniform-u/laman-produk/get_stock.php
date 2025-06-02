<?php
header('Content-Type: application/json');
include '../koneksi.php';

$id_produk = $_GET['id_produk'] ?? null;
$size = $_GET['size'] ?? null;

if (!$id_produk) {
    echo json_encode(['error' => 'Parameter id_produk wajib']);
    exit;
}

if ($size) {
    $stmt = $conn->prepare("SELECT stok FROM produk_stock WHERE id_produk = ? AND size = ?");
    $stmt->bind_param("is", $id_produk, $size);
} else {
    $stmt = $conn->prepare("SELECT stok FROM produk_stock WHERE id_produk = ? LIMIT 1");
    $stmt->bind_param("i", $id_produk);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    echo json_encode(['stock' => (int)$row['stok']]);
} else {
    echo json_encode(['stock' => 0]);
}

$stmt->close();
$conn->close();
?>
