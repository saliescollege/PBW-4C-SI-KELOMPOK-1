<?php
session_start();
include '../koneksi.php';
include '../config.php';

// Proses simpan pesanan baru jika ada POST dari form pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['namaPelanggan'])) {
    // Ambil data pelanggan dan pesanan dari form
    $nama_pelanggan = $_POST['namaPelanggan'];
    $no_telepon = $_POST['nomorTelepon'] ?? '';
    $sekolah = $_POST['sekolah'] ?? '';
    $alamat_sekolah = $_POST['alamatSekolah'] ?? '';
    $produkListPesanan = json_decode($_POST['produkListPesanan'], true);
    $total_harga = $_POST['total_harga'] ?? 0;
    $jenis_pembayaran = $_POST['jenisPembayaran'] ?? 'lunas';
    $nominal_dp = $_POST['nominalDp'] ?? 0;
    $sisa_bayar = $_POST['sisaBayar'] ?? 0;
    $metode_bayar = $_POST['metodeBayar'] ?? '';
    $tanggal_pesanan = date('Y-m-d H:i:s');
    $status = ($jenis_pembayaran === 'lunas') ? 'Sudah Lunas' : 'Dicicil';

    // Cek apakah pelanggan sudah ada, jika belum tambahkan
    $stmt = $conn->prepare("SELECT id_pelanggan FROM pelanggan WHERE nama_pelanggan=? AND no_telepon=? AND sekolah=? AND alamat_sekolah=?");
    if (!$stmt) die("Prepare failed (SELECT pelanggan): " . $conn->error);
    $stmt->bind_param("ssss", $nama_pelanggan, $no_telepon, $sekolah, $alamat_sekolah);
    $stmt->execute();
    $stmt->bind_result($id_pelanggan);
    if ($stmt->fetch()) {
        $stmt->close();
    } else {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO pelanggan (nama_pelanggan, no_telepon, sekolah, alamat_sekolah) VALUES (?, ?, ?, ?)");
        if (!$stmt) die("Prepare failed (INSERT pelanggan): " . $conn->error);
        $stmt->bind_param("ssss", $nama_pelanggan, $no_telepon, $sekolah, $alamat_sekolah);
        $stmt->execute();
        $id_pelanggan = $stmt->insert_id;
        $stmt->close();
    }

    // Simpan pesanan ke tabel pesanan
    $stmt = $conn->prepare("INSERT INTO pesanan (id_pelanggan, tanggal_pesanan, total_harga, status) VALUES (?, ?, ?, ?)");
    if (!$stmt) die("Prepare failed (INSERT pesanan): " . $conn->error);
    $stmt->bind_param("isds", $id_pelanggan, $tanggal_pesanan, $total_harga, $status);
    $stmt->execute();
    $id_pesanan = $stmt->insert_id;
    $stmt->close();

    // Simpan pembayaran (jika nyicil, hanya DP yang masuk)
    $stmt = $conn->prepare("INSERT INTO pembayaran (id_pesanan, metode_pembayaran, jumlah_bayar, tanggal_bayar) VALUES (?, ?, ?, ?)");
    if (!$stmt) die("Prepare failed (INSERT pembayaran): " . $conn->error);
    $jumlah_bayar = ($jenis_pembayaran === 'nyicil') ? $nominal_dp : $total_harga;
    $tanggal_bayar = date('Y-m-d H:i:s');
    $stmt->bind_param("isds", $id_pesanan, $metode_bayar, $jumlah_bayar, $tanggal_bayar);
    $stmt->execute();
    $stmt->close();

    // Simpan detail produk pesanan ke tabel detail_pesanan
    if (is_array($produkListPesanan)) {
        foreach ($produkListPesanan as $item) {
            $id_produk = $item['id'] ?? 0;
            $size = $item['size'] ?? '';
            $jumlah = $item['jumlah'] ?? 0;
            $harga = $item['harga'] ?? 0;
            $subtotal = $harga * $jumlah;

            $id_stock = 0;
            if ($size !== '') {
                // Cari id_stock berdasarkan produk dan size
                $stmt = $conn->prepare("SELECT id FROM produk_stock WHERE id_produk = ? AND size = ?");
                $stmt->bind_param("is", $id_produk, $size);
                $stmt->execute();
                $stmt->bind_result($id_stock);
                if (!$stmt->fetch()) {
                    $id_stock = null;
                }
                $stmt->close();
            }

            // Simpan detail pesanan
            $stmt = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_produk, id_stock, jumlah, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiid", $id_pesanan, $id_produk, $id_stock, $jumlah, $subtotal);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Update stok produk setelah pesanan masuk
    if (is_array($produkListPesanan)) {
        foreach ($produkListPesanan as $item) {
            $id_produk = $item['id'] ?? 0;
            $size = $item['size'] ?? '';
            $jumlah = $item['jumlah'] ?? 0;

            $id_stock = 0;
            if ($size !== '') {
                $stmt = $conn->prepare("SELECT id FROM produk_stock WHERE id_produk = ? AND size = ?");
                $stmt->bind_param("is", $id_produk, $size);
                $stmt->execute();
                $stmt->bind_result($id_stock);
                if (!$stmt->fetch()) {
                    $id_stock = null;
                }
                $stmt->close();
            } else {
                // Jika produk tanpa ukuran, cari id_stock dengan size 'NO_SIZE'
                $stmt = $conn->prepare("SELECT id FROM produk_stock WHERE id_produk = ? AND size = 'NO_SIZE'");
                $stmt->bind_param("i", $id_produk);
                $stmt->execute();
                $stmt->bind_result($id_stock);
                if (!$stmt->fetch()) {
                    $id_stock = null;
                }
                $stmt->close();
            }
            if ($id_stock) {
                $stmt = $conn->prepare("UPDATE produk_stock SET stok = stok - ? WHERE id = ?");
                $stmt->bind_param("ii", $jumlah, $id_stock);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // Setelah selesai, kembali ke halaman list pesanan
    header("Location: pesanan.php");
    exit;
}


// Inisialisasi variabel produkListPesanan agar tidak error
if (!isset($produkListPesanan)) {
    $produkListPesanan = [];
}

// Ambil detail produk pesanan jika ada id_pesanan di URL
$id_pesanan = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_pesanan > 0) {
    $sql = "SELECT * FROM detail_pesanan WHERE id_pesanan = $id_pesanan";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo "Query error: " . mysqli_error($conn);
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $produkListPesanan[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pesanan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../styles.css">
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
      background-color: #ffe6e6 !important; /* Pink muda untuk cicil */
      color: #d63384 !important;
    }
  
    .btn-full {
      background-color: #6c757d;
      color: white;
      border: none;
    }
  
    .btn-full:hover {
      background-color: #5a6268;
      color: white;
    }

    .status-badge.bg-success {
      background-color: #198754 !important;
      color: #fff !important;
    }

    .status-badge.bg-bright-green {
      background-color: #b6fcb6 !important; /* Hijau muda untuk lunas */
      color: #198754 !important;
    }

    /* Efek hover pada baris pesanan */
    .row-pesanan {
      cursor: pointer;
      transition: background 0.2s;
    }
    .row-pesanan:hover {
      background:rgba(241, 243, 244, 0.63) !important;
    }

    .row-pesanan:hover > td,
    .row-pesanan:hover > th {
      background: #f1f3f4 !important;
      transition: background 0.2s;
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
      <nav aria-label="breadcrumb">
        <ul class="breadcrumb-custom">
          <li><a href="pesanan.php">List Pesanan</a></li>
        </ul>
      </nav>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div>
        <div class="d-flex align-items-center gap-2">
          <a href="pesanan-baru.php" class="btn btn-light border text-black text-nowrap">
            <i class="fas fa-plus me-1"></i> Tambah Pesanan
          </a>
          <div class="input-group" style="max-width: 250px;">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari transaksi...">
          </div>
        </div>
      </div>
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle table-bordered">
              <thead class="table-light">
                <tr>
                  <th>ID Pesanan</th>
                  <th>Nama Pelanggan</th>
                  <th>Institusi</th>
                  <th>Tanggal Pesanan</th>
                  <th>Total Harga</th>
                  <th>Status Pembayaran</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Ambil semua pesanan dari database dan tampilkan di tabel
                $sql = "SELECT 
                            p.id_pesanan, 
                            pel.nama_pelanggan, 
                            pel.sekolah, 
                            p.tanggal_pesanan, 
                            p.total_harga, 
                            p.status
                        FROM pesanan p
                        JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
                        ORDER BY p.tanggal_pesanan DESC";
                $result = mysqli_query($conn, $sql);
                if ($result && mysqli_num_rows($result) > 0):
                  while ($row = mysqli_fetch_assoc($result)):
                ?>
                <tr class="row-pesanan" data-id="<?= $row['id_pesanan'] ?>">
                  <td><?= htmlspecialchars($row['id_pesanan']) ?></td>
                  <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                  <td><?= htmlspecialchars($row['sekolah']) ?></td>
                  <td><?= htmlspecialchars($row['tanggal_pesanan']) ?></td>
                  <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                  <td>
                    <?php if (strtolower(trim($row['status'])) === 'sudah lunas'): ?>
                      <span class="status-badge bg-bright-green text-white">
                        <?= htmlspecialchars($row['status']) ?>
                      </span>
                    <?php elseif (strtolower(trim($row['status'])) === 'dicicil'): ?>
                      <span class="status-badge bg-light-pink text-dark">
                        <?= htmlspecialchars($row['status']) ?>
                      </span>
                    <?php else: ?>
                      <span class="status-badge bg-secondary text-white">
                        <?= htmlspecialchars($row['status']) ?>
                      </span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                  <td colspan="6" class="text-center text-muted">Belum ada pesanan.</td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  // Fitur pencarian pada tabel pesanan
  document.getElementById("searchInput").addEventListener("keyup", function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll("table tbody tr");

    rows.forEach(row => {
      const rowText = row.textContent.toLowerCase();
      row.style.display = rowText.includes(searchValue) ? "" : "none";
    });
  });

  // Event hapus pesanan (AJAX)
  $(document).on('click', '.btn-hapus-pesanan', function() {
    if (confirm('Yakin ingin menghapus pesanan ini?')) {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        $.post('hapus-pesanan.php', {id: id}, function(res) {
            if (res.trim() === 'ok') {
                row.fadeOut(300, function() { $(this).remove(); });
            } else {
                alert('Gagal menghapus pesanan!');
            }
        });
    }
  });

  // Klik baris pesanan untuk melihat detail (redirect ke receipt)
  $(document).on('click', '.row-pesanan', function(e) {
    if ($(e.target).closest('a,button').length === 0) {
        var id = $(this).data('id');
        window.location.href = 'receipt.php?id=' + id;
    }
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