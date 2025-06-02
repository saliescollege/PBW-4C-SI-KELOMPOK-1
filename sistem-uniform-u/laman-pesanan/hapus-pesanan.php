<?php
include '../koneksi.php';

// Ambil id pesanan dari request POST
$id_pesanan = intval($_POST['id'] ?? 0);

if ($id_pesanan) {
    // Ambil semua detail pesanan untuk mengembalikan stok produk
    $sql = "SELECT id_produk, id_stock, jumlah FROM detail_pesanan WHERE id_pesanan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pesanan);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $id_stock = $row['id_stock'];
        $jumlah = $row['jumlah'];
        // Kembalikan stok produk jika ada id_stock dan jumlah
        if ($id_stock && $jumlah) {
            $update = $conn->prepare("UPDATE produk_stock SET stok = stok + ? WHERE id = ?");
            $update->bind_param("ii", $jumlah, $id_stock);
            $update->execute();
            $update->close();
        }
    }
    $stmt->close();

    // Hapus detail pesanan, pembayaran, dan pesanan utama dari database
    $conn->query("DELETE FROM detail_pesanan WHERE id_pesanan = $id_pesanan");
    $conn->query("DELETE FROM pembayaran WHERE id_pesanan = $id_pesanan");
    $conn->query("DELETE FROM pesanan WHERE id_pesanan = $id_pesanan");

    // Beri respon sukses ke AJAX
    echo "ok";
} else {
    // Jika id tidak valid, kirim error
    echo "error";
}
?>