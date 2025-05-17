<?php
// Sesuaikan path koneksi database
include '../koneksi.php'; // misal koneksi.php ada di 1 folder atas

if (!isset($_GET['id'])) {
    header('Location: produk.php');
    exit;
}

$id = intval($_GET['id']);

// Hapus stok produk terkait dulu
mysqli_query($conn, "DELETE FROM produk_stock WHERE id_produk = '$id'");

// Hapus produk
$hapus = mysqli_query($conn, "DELETE FROM produk WHERE id_produk = '$id'");

if ($hapus) {
    header('Location: produk.php?msg=Produk berhasil dihapus');
} else {
    header('Location: produk.php?error=Gagal menghapus produk');
}
exit;
?>
