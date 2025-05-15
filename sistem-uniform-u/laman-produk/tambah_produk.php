<?php
include '../koneksi.php'; // sesuaikan path koneksi.php

// Tangkap data
$nama_produk   = $_POST['productName'];
$kategori      = $_POST['productCategory'];
$jenis_kelamin = $_POST['productGender'];
$harga         = $_POST['productPrice'];

// Tangani gambar
$gambar        = $_FILES['productImage']['name'];
$tmp           = $_FILES['productImage']['tmp_name'];
$upload_dir    = "../uploads/";

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$path_gambar = $upload_dir . basename($gambar);
if (!move_uploaded_file($tmp, $path_gambar)) {
    die("Upload gambar gagal.");
}

// Simpan ke tabel produk
$query_produk = "INSERT INTO produk (nama_produk, kategori, jenis_kelamin, harga, gambar_produk)
                 VALUES ('$nama_produk', '$kategori', '$jenis_kelamin', '$harga', '$gambar')";

if (!mysqli_query($conn, $query_produk)) {
    die("Gagal insert produk: " . mysqli_error($conn));
}

$id_produk = mysqli_insert_id($conn); // ID dari produk yang baru disimpan

// Simpan ke tabel stok ukuran
$sizes = ['XS', 'S', 'M', 'L', 'XL'];
foreach ($sizes as $size) {
    $key  = 'stok_' . strtolower($size);
    $stok = isset($_POST[$key]) ? (int)$_POST[$key] : 0;

    $query_stok = "INSERT INTO produk_stock (id_produk, size, stok)
                   VALUES ('$id_produk', '$size', '$stok')";
    mysqli_query($conn, $query_stok) or die("Gagal insert stok: " . mysqli_error($conn));
}

// Redirect ke daftar produk
header("Location: list_produk.php?status=sukses");
exit;
?>
