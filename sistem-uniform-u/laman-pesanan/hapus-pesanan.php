<?php
include '../koneksi.php';

$id_pesanan = intval($_POST['id'] ?? 0);

if ($id_pesanan) {
    // Ambil detail pesanan
    $sql = "SELECT id_produk, id_stock, jumlah FROM detail_pesanan WHERE id_pesanan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pesanan);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kembalikan stok
    while ($row = $result->fetch_assoc()) {
        $id_stock = $row['id_stock'];
        $jumlah = $row['jumlah'];
        if ($id_stock && $jumlah) {
            $update = $conn->prepare("UPDATE produk_stock SET stok = stok + ? WHERE id = ?");
            $update->bind_param("ii", $jumlah, $id_stock);
            $update->execute();
            $update->close();
        }
    }
    $stmt->close();

    // Hapus detail pesanan
    $conn->query("DELETE FROM detail_pesanan WHERE id_pesanan = $id_pesanan");
    // Hapus pembayaran
    $conn->query("DELETE FROM pembayaran WHERE id_pesanan = $id_pesanan");
    // Hapus pesanan
    $conn->query("DELETE FROM pesanan WHERE id_pesanan = $id_pesanan");

    echo "ok";
} else {
    echo "error";
}
?>