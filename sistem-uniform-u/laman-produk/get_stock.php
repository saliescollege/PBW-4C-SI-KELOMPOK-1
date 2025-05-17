<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_uniform";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Koneksi database gagal']);
    exit;
}

$id_produk = $_GET['id_produk'] ?? null;
$size = $_GET['size'] ?? null;

if (!$id_produk) {
    echo json_encode(['error' => 'Parameter id_produk wajib']);
    exit;
}

if ($size) {
    // Query stok dengan ukuran
    $stmt = $conn->prepare("SELECT stok FROM produk_stock WHERE id_produk = ? AND size = ?");
    $stmt->bind_param("is", $id_produk, $size);
} else {
    // Query stok tanpa ukuran, misal ambil stok dari tabel produk atau stok size default
    $stmt = $conn->prepare("SELECT stok FROM produk_stock WHERE id_produk = ? LIMIT 1");
    $stmt->bind_param("i", $id_produk);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    // Kirim stok sebagai integer, contoh: 5, 0, dll
    echo json_encode(['stock' => (int)$row['stok']]);
} else {
    // Jika tidak ada data stok ditemukan
    echo json_encode(['stock' => 0]);
}

$stmt->close();
$conn->close();
?>
