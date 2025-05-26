<?php
session_start();
// koneksi database (sesuaikan)
$host = 'localhost';
$dbname = 'db_uniform';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Query produk dengan total stok kurang dari 5
$sql = "
    SELECT p.id_produk, p.nama_produk, p.kategori, p.warna, p.harga, p.gambar_produk,
           SUM(IFNULL(ps.stok, 0)) AS total_stok
    FROM produk p
    LEFT JOIN produk_stock ps ON p.id_produk = ps.id_produk
    GROUP BY p.id_produk
    HAVING total_stok < 5
    ORDER BY total_stok ASC
    LIMIT 5
";

$stmt = $conn->query($sql);
$produk_reminder = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql_jumlah_produk = "SELECT COUNT(*) as total_produk FROM produk";
$stmt_jumlah_produk = $conn->query($sql_jumlah_produk);
$row_jumlah_produk = $stmt_jumlah_produk->fetch(PDO::FETCH_ASSOC);
$total_produk = $row_jumlah_produk['total_produk'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="d-flex">
<?php include '../sidebar.php'; ?> <!--Menambahkan sidebar-->

<!-- Main Content -->
<div class="flex-grow-1 p-4">
 <h2>Selamat Datang, <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Tamu' ?></h2>
  <hr>

  <!-- Row Cards -->
  <div class="row g-4 mb-4">
    <!-- Total Penjualan -->
    <div class="col-md-6 col-xl-3">
      <div class="card shadow-sm border-0">
        <div class="card-body d-flex align-items-center">
          <div class="me-3">
            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
          </div>
          <div>
            <h6 class="mb-0">Total Penjualan</h6>
            <h4 class="fw-bold">28</h4>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Pendapatan -->
    <div class="col-md-6 col-xl-3">
      <div class="card shadow-sm border-0">
        <div class="card-body d-flex align-items-center">
          <div class="me-3">
            <i class="fas fa-money-bill-wave fa-2x text-success"></i>
          </div>
          <div>
            <h6 class="mb-0">Pendapatan</h6>
            <h4 class="fw-bold">Rp 2.800.000</h4>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Pelanggan -->
    <div class="col-md-6 col-xl-3">
      <div class="card shadow-sm border-0">
        <div class="card-body d-flex align-items-center">
          <div class="me-3">
            <i class="fas fa-users fa-2x text-warning"></i>
          </div>
          <div>
            <h6 class="mb-0">Pelanggan</h6>
            <h4 class="fw-bold">124</h4>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Produk -->
    <div class="col-md-6 col-xl-3">
      <div class="card shadow-sm border-0">
        <div class="card-body d-flex align-items-center">
          <div class="me-3">
            <i class="fas fa-boxes fa-2x text-danger"></i>
          </div>
          <div>
            <h6 class="mb-0">Jumlah Produk</h6>
            <h4 class="fw-bold"><?=$total_produk ?></h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Grafik Penjualan & Reminder Produk -->
<div class="row g-4 mb-4">
  <!-- Grafik Penjualan -->
  <div class="col-lg-8">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body">
        <h5 class="card-title">Grafik Penjualan</h5>
        <canvas id="salesChart" height="150"></canvas>
      </div>
    </div>
  </div>

<!-- Produk Hampir Habis -->
<div class="col-lg-4">
  <div class="card shadow-sm border-0 h-100">
    <div class="card-body">
      <h5 class="card-title mb-3">Stok Hampir Habis!</h5>

      <?php if (count($produk_reminder) > 0): ?>
        <?php foreach ($produk_reminder as $produk): ?>
          <div class="card product-card mb-3">
            <!-- Ganti image sesuai produk, kalau gak ada pake placeholder -->
            <?php
              $gambar = !empty($produk['gambar_produk']) ? "../assets/uniform/" . htmlspecialchars($produk['gambar_produk']) : "../assets/uniform/default.png";
            ?>
            <img src="<?= $gambar ?>" class="card-img-top img-fluid" style="max-height: 200px; object-fit: contain;" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
            <div class="card-body">
              <span class="product-tag tag-sd"><?= htmlspecialchars($produk['kategori']) ?></span>
              <div class="d-flex justify-content-between align-items-center">
                <strong><?= htmlspecialchars($produk['nama_produk']) ?></strong>
              </div>
              <small class="text-muted"><?= htmlspecialchars($produk['warna']) ?></small>
              <hr>
              <div class="stock-text <?= ($produk['total_stok'] <= 2) ? 'text-danger' : '' ?>">
                Stok tersisa: <?= $produk['total_stok'] ?>
              </div>
              <hr>
              <h6 class="fw-bold">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></h6>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Semua produk stoknya aman.</p>
      <?php endif; ?>

    </div>
  </div>
</div>

<!-- Efek Collapse di Sidebar -->
<script>
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('toggleSidebar');
  const logo = document.getElementById('sidebarLogo');

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
</script>

<!-- Visualisasi Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
      datasets: [{
        label: 'Penjualan',
        data: [5, 12, 9, 14, 8, 15],
        backgroundColor: 'rgba(13, 110, 253, 0.2)',
        borderColor: 'rgba(13, 110, 253, 1)',
        borderWidth: 2,
        tension: 0.3,
        fill: true,
        pointBackgroundColor: 'rgba(13, 110, 253, 1)'
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

    function showStock(btn, qty) {
    const stockText = btn.closest('.card-body').querySelector('.stock-text');
    stockText.textContent = `Stok ukuran ${btn.innerText}: ${qty}`;
    if (qty <= 2) {
      stockText.classList.add('text-danger');
    } else {
      stockText.classList.remove('text-danger');
    }
  }
</script>

</body>
</html>