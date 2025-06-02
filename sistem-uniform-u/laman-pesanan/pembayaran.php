<?php
session_start();

// Jika form pembayaran dikirim (POST) dan ada data produk pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produkListPesanan'])) {
    // Ambil data dari form
    $nama_pelanggan = $_POST['namaPelanggan'] ?? '';
    $no_telepon = $_POST['nomorTelepon'] ?? '';
    $sekolah = $_POST['sekolah'] ?? '';
    $alamat_sekolah = $_POST['alamatSekolah'] ?? '';
    $produkListPesanan = json_decode($_POST['produkListPesanan'], true);

    // Hitung total harga semua produk
    $total_harga = 0;
    foreach ($produkListPesanan as $item) {
        $total_harga += ($item['harga'] ?? 0) * ($item['jumlah'] ?? 0);
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pembayaran Pesanan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../styles.css">
  <style>
    .main-card { margin: 32px auto; }
    .table-sm th, .table-sm td { vertical-align: middle; }
    @media (max-width: 600px) {
      .main-card { max-width: 100%; }
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
  </style>
</head>
<body>
  <div class="d-flex">
    <?php include '../sidebar.php'; ?>
    <div class="flex-grow-1 p-4">
      <h1>Pesanan</h1>
      <hr>
      <!-- Breadcrumb navigasi -->
      <div class="transaction-toolbar mb-3">
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb-custom">
            <li><a href="pesanan.php">List Pesanan</a></li>
            <li><a href="pesanan-baru.php">Pesanan Baru</a></li>
            <li class="active">Pembayaran</li>
          </ul>
        </nav>
      </div>

      <div class="container mt-4">
        <div class="card shadow-sm main-card w-100">
          <div class="card-body">
            <div class="mb-4">
              <!-- Tampilkan data pelanggan -->
              <div><b>Nama:</b> <?= htmlspecialchars($nama_pelanggan) ?></div>
              <div><b>No. Telepon:</b> <?= htmlspecialchars($no_telepon) ?></div>
              <div><b>Sekolah:</b> <?= htmlspecialchars($sekolah) ?></div>
              <div><b>Alamat:</b> <?= htmlspecialchars($alamat_sekolah) ?></div>
              <hr>
              <!-- Tabel ringkasan produk yang dipesan -->
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>Produk</th>
                    <th>Size</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($produkListPesanan as $item): ?>
                  <tr>
                    <td><?= htmlspecialchars($item['nama']) ?></td>
                    <td><?= htmlspecialchars($item['size']) ?></td>
                    <td><?= $item['jumlah'] ?></td>
                    <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th colspan="4" class="text-end">Total</th>
                    <th>Rp <?= number_format($total_harga, 0, ',', '.') ?></th>
                  </tr>
                </tfoot>
              </table>
            </div>

            <!-- Form pilih metode pembayaran dan jenis pembayaran -->
            <form method="POST" action="pesanan.php">
              <input type="hidden" name="namaPelanggan" value="<?= htmlspecialchars($nama_pelanggan) ?>">
              <input type="hidden" name="nomorTelepon" value="<?= htmlspecialchars($no_telepon) ?>">
              <input type="hidden" name="sekolah" value="<?= htmlspecialchars($sekolah) ?>">
              <input type="hidden" name="alamatSekolah" value="<?= htmlspecialchars($alamat_sekolah) ?>">
              <input type="hidden" name="produkListPesanan" value='<?= htmlspecialchars(json_encode($produkListPesanan), ENT_QUOTES, "UTF-8") ?>'>
              <input type="hidden" name="total_harga" value="<?= $total_harga ?>">

              <div class="mb-3">
                <label class="form-label"><b>Pembayaran</b></label><br>
                <input type="radio" name="jenisPembayaran" value="lunas" id="lunasRadio" checked>
                <label for="lunasRadio" class="me-3">Lunas</label>
                <input type="radio" name="jenisPembayaran" value="nyicil" id="nyicilRadio">
                <label for="nyicilRadio">Nyicil</label>
              </div>
              <!-- Detail cicilan hanya muncul jika pilih "Nyicil" -->
              <div id="cicilanDetail" style="display:none;">
                <table class="table table-bordered table-sm w-auto mb-3">
                  <tbody>
                    <tr>
                      <th class="bg-light">Nominal DP (50%)</th>
                      <td>
                        Rp <?= number_format(round($total_harga * 0.5), 0, ',', '.') ?>
                        <input type="hidden" name="nominalDp" value="<?= round($total_harga * 0.5) ?>">
                      </td>
                    </tr>
                    <tr>
                      <th class="bg-light">Sisa Bayar</th>
                      <td>
                        Rp <?= number_format($total_harga - round($total_harga * 0.5), 0, ',', '.') ?>
                        <input type="hidden" name="sisaBayar" value="<?= $total_harga - round($total_harga * 0.5) ?>">
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="mb-3">
                <label for="paymentMethod" class="form-label">Metode Pembayaran</label>
                <select class="form-select" id="paymentMethod" name="metodeBayar" required>
                  <option value="">Pilih Metode</option>
                  <option value="transfer">Transfer Bank</option>
                  <option value="qris">QRIS</option>
                  <option value="tunai">Tunai</option>
                </select>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-success px-5">
                  <i class="fas fa-paper-plane me-1"></i> Kirim
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    // Tampilkan detail cicilan jika pilih "Nyicil"
    document.querySelectorAll('input[name="jenisPembayaran"]').forEach(function(radio) {
      radio.addEventListener('change', function() {
        document.getElementById('cicilanDetail').style.display = (this.value === 'nyicil') ? '' : 'none';
      });
    });

    // Efek collapse di sidebar
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const logo = document.getElementById('sidebarLogo');

    if (sidebar && toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
      });

      sidebar.addEventListener('mouseenter', () => {
        if (sidebar.classList.contains('collapsed')) {
          sidebar.classList.remove('collapsed');
        }
      });

      sidebar.addEventListener('mouseleave', () => {
        if (!sidebar.classList.contains('manual-toggle')) {
          sidebar.classList.add('collapsed');
        }
      });
    }
  </script>
</body>
</html>
<?php
} else {
// Jika halaman diakses tanpa data, tampilkan form pembayaran sederhana
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
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
          <div class="transaction-toolbar">
            <h6 class="mb-1">
                List Transaksi <span class="text-muted">&gt; Detail Transaksi</span>
                <span class="text-muted">&gt; Pembayaran</span>
            </h6>
            <p></p>
          </div>
          <!-- Form pembayaran sederhana jika tidak ada data pesanan -->
          <div class="card shadow-sm p-4 mt-4" style="max-width: 600px;">
            <h5 class="mb-3">Form Pembayaran</h5>
            <form method="POST" action="faktur.php">
              <div class="mb-3">
                <label for="paymentMethod" class="form-label">Metode Pembayaran</label>
                <select class="form-select" id="paymentMethod" name="metodeBayar" required>
                  <option value="">-- Pilih Metode --</option>
                  <option value="transfer">Transfer Bank</option>
                  <option value="qris">QRIS</option>
                  <option value="tunai">Tunai</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="amount" class="form-label">Nominal Pembayaran</label>
                <input type="number" class="form-control" id="amount" name="total_harga" placeholder="Masukkan jumlah bayar" required>
              </div>
              <div class="mb-3">
                <label for="note" class="form-label">Catatan (Opsional)</label>
                <textarea class="form-control" id="note" name="catatan" rows="2" placeholder="Misal: dibayar oleh wali murid..."></textarea>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-success px-5">
                  <i class="fas fa-paper-plane me-1"></i> Bayar Sekarang
                </button>
              </div>
            </form>
          </div>
        </div>
    </div>
</body>
</html>
<?php } ?>