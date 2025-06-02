<?php
include '../koneksi.php';

// Ambil id pesanan dari URL
$id_pesanan = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query data pesanan, pelanggan, dan pembayaran
$sql = "SELECT p.id_pesanan, p.tanggal_pesanan, p.total_harga, p.status, 
               pel.nama_pelanggan, pel.sekolah, pel.no_telepon, pel.alamat_sekolah,
               bayar.metode_pembayaran, bayar.jumlah_bayar, bayar.tanggal_bayar
        FROM pesanan p
        JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
        LEFT JOIN pembayaran bayar ON bayar.id_pesanan = p.id_pesanan
        WHERE p.id_pesanan = $id_pesanan
        LIMIT 1";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

// Query detail produk pesanan (dengan join ke produk dan produk_stock untuk ambil nama & ukuran)
$detail = [];
$sql_detail = "SELECT dp.jumlah, dp.subtotal, pr.nama_produk, ps.size, pr.harga
               FROM detail_pesanan dp
               LEFT JOIN produk pr ON dp.id_produk = pr.id_produk
               LEFT JOIN produk_stock ps ON dp.id_stock = ps.id
               WHERE dp.id_pesanan = $id_pesanan";
$res_detail = mysqli_query($conn, $sql_detail);
while ($row = mysqli_fetch_assoc($res_detail)) {
    $detail[] = $row;
}

// Proses simpan data pesanan baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_produk = isset($_POST['id_produk']) ? intval($_POST['id_produk']) : 0;
    $id_stock = isset($_POST['id_stock']) ? intval($_POST['id_stock']) : 0;
    $jumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 0;
    $subtotal = isset($_POST['subtotal']) ? floatval($_POST['subtotal']) : 0;

    // Siapkan dan eksekusi query
    $stmt = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, id_stock, jumlah, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiid", $id_pesanan, $id_produk, $id_stock, $jumlah, $subtotal);
    $stmt->execute();

    // Redirect atau tampilkan pesan sukses
    header("Location: faktur.php?id=" . $id_pesanan);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan #<?= htmlspecialchars($data['id_pesanan'] ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="d-flex">
    <?php include '../sidebar.php'; ?>
        <div class="flex-grow-1 p-4">
            <h1>Pesanan</h1>
            <hr>
            <div class="transaction-toolbar mb-3">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                  <li class="breadcrumb-item"><a href="pesanan.php">List Pesanan</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Detail Pesanan</li>
                </ol>
              </nav>
            </div>

            <!-- Receipt Card -->
            <div class="card shadow-sm p-4 mt-4">
                <div class="d-flex justify-content-between">
                  <h5 class="fw-bold">Pesanan #<?= htmlspecialchars($data['id_pesanan'] ?? '') ?></h5>
                  <span class="text-muted"><?= date('d M Y', strtotime($data['tanggal_pesanan'] ?? '')) ?></span>
                </div>
                <hr>
                <div class="mb-3">
                  <p class="mb-1"><strong>Nama Pelanggan:</strong> <?= htmlspecialchars($data['nama_pelanggan'] ?? '') ?></p>
                  <p class="mb-1"><strong>No. Telepon:</strong> <?= htmlspecialchars($data['no_telepon'] ?? '') ?></p>
                  <p class="mb-1"><strong>Sekolah:</strong> <?= htmlspecialchars($data['sekolah'] ?? '') ?></p>
                  <p class="mb-1"><strong>Alamat:</strong> <?= htmlspecialchars($data['alamat_sekolah'] ?? '') ?></p>
                  <p class="mb-1"><strong>Status:</strong>
                    <?php if (strtolower(trim($data['status'])) === 'sudah lunas'): ?>
                      <span class="status-badge bg-bright-green text-white"><?= htmlspecialchars($data['status']) ?></span>
                    <?php elseif (strtolower(trim($data['status'])) === 'dicicil'): ?>
                      <span class="status-badge bg-light-pink text-dark"><?= htmlspecialchars($data['status']) ?></span>
                    <?php else: ?>
                      <span class="status-badge bg-secondary text-white"><?= htmlspecialchars($data['status']) ?></span>
                    <?php endif; ?>
                  </p>
                  <p class="mb-1"><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($data['metode_pembayaran'] ?? '-') ?></p>
                </div>
                <table class="table table-sm">
                  <thead class="table-light">
                    <tr>
                      <th>Produk</th>
                      <th>Ukuran</th>
                      <th>Qty</th>
                      <th>Harga</th>
                      <th>Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($detail as $item): ?>
                    <tr>
                      <td><?= htmlspecialchars($item['nama_produk'] ?? '-') ?></td>
                      <td><?= htmlspecialchars($item['size'] ?? '-') ?></td>
                      <td><?= $item['jumlah'] ?></td>
                      <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                      <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="4" class="text-end">Total</th>
                      <th>Rp <?= number_format($data['total_harga'] ?? 0, 0, ',', '.') ?></th>
                    </tr>
                  </tfoot>
                </table>
                <!-- Hapus bagian jumlah bayar dan kembalian -->
                <div class="text-end mt-4">
                  <button class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Cetak Faktur
                  </button>
                </div>
            </div>
            <pre>
</pre>
        </div>
    </div>

    <style>
      .status-badge {
        font-size: 0.95rem;
        font-weight: normal;
        padding: 6px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
      }
      .bg-light-pink {
        background-color: #ffe6e6 !important;
        color: #d63384 !important;
      }
      .bg-bright-green {
        background-color: #b6fcb6 !important;
        color: #198754 !important;
      }

      @media print {
        body * {
          visibility: hidden !important;
        }
        .card, .card * {
          visibility: visible !important;
        }
        .card {
          position: absolute !important;
          left: 0; top: 0;
          width: 100% !important;
          margin: 0 !important;
          box-shadow: none !important;
          border: none !important;
          background: #fff !important;
        }
      }
    </style>
</body>
</html>