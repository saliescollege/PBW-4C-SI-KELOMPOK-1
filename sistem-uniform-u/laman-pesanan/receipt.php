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



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_lunasi']) && $id_pesanan) {
    $conn->query("UPDATE pesanan SET status='Sudah Lunas' WHERE id_pesanan=$id_pesanan");
    $conn->query("UPDATE pembayaran SET jumlah_bayar=total_harga WHERE id_pesanan=$id_pesanan");
    header("Location: pesanan.php");
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
                <ul class="breadcrumb-custom">
                  <li><a href="pesanan.php">List Pesanan</a></li>
                  <li class="active">Detail Pesanan</li>
                </ul>
              </nav>
            </div>

            <div class="d-flex justify-content-end align-items-center gap-2 mt-4 mb-2">
              <button id="btn-unduh-pdf" class="btn btn-light border text-black text-nowrap" style="background-color:#ffe6e6; color:#d63384; border:1px solid #d63384;">
                <i class="fas fa-file-pdf me-1"></i> Unduh PDF
              </button>
              <button id="btn-hapus-pesanan" class="btn btn-light border text-black text-nowrap" data-id="<?= htmlspecialchars($data['id_pesanan']) ?>">
                <i class="fas fa-trash me-1"></i> Hapus Pesanan
              </button>
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

                <?php if (strtolower(trim($data['status'])) === 'dicicil'): ?>
                  <?php
                    $dp = round($data['total_harga'] * 0.5);
                    $sisa = $data['total_harga'] - $dp;
                  ?>
                  <table class="table table-bordered table-sm w-auto mb-3 mx-auto" style="max-width:350px;">
                    <tbody>
                      <tr>
                        <th class="bg-light">Nominal DP (50%)</th>
                        <td>Rp <?= number_format($dp, 0, ',', '.') ?></td>
                      </tr>
                      <tr>
                        <th class="bg-light">Sisa Cicilan</th>
                        <td>Rp <?= number_format($sisa, 0, ',', '.') ?></td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="text-center mb-3">
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="aksi_lunasi" value="1">
                      <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Tandai Sudah Lunas
                      </button>
                    </form>
                  </div>
                <?php endif; ?>
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
      .breadcrumb-custom {
        display: flex;
        list-style: none;
        padding: 8px 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
        font-size: 0.95rem;
        margin-bottom: 1rem;
      }
      .breadcrumb-custom li {
        margin-right: 8px;
      }
      .breadcrumb-custom li:not(:last-child)::after {
        content: "\203A";
        margin-left: 8px;
        color: #6c757d;
      }
      .breadcrumb-custom li:last-child::after {
        content: "";
        margin: 0;
      }
      .breadcrumb-custom a {
        text-decoration: none;
        color: #212529;
      }
      .breadcrumb-custom .active {
        color: #6c757d;
        pointer-events: none;
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
    $('#btn-hapus-pesanan').on('click', function() {
      if (confirm('Yakin ingin menghapus pesanan ini?')) {
        var id = $(this).data('id');
        $.post('hapus-pesanan.php', {id: id}, function(res) {
          if (res.trim() === 'ok') {
            alert('Pesanan berhasil dihapus!');
            window.location.href = 'pesanan.php';
          } else {
            alert('Gagal menghapus pesanan!');
          }
        });
      }
    });

    $('#btn-unduh-pdf').on('click', function() {
      var element = document.querySelector('.card.shadow-sm');
      html2pdf().from(element).set({
        margin: 10,
        filename: 'Pesanan_<?= htmlspecialchars($data['id_pesanan']) ?>.pdf',
        html2canvas: { scale: 2 },
        jsPDF: {orientation: 'portrait', unit: 'mm', format: 'a4'}
      }).save();
    });
    </script>
</body>
</html>