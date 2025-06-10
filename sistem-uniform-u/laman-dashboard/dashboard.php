<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Query produk dengan total stok per ukuran
$sql = "
    SELECT p.id_produk, p.nama_produk, p.kategori, p.warna, p.harga, p.gambar_produk,
           ps.size, ps.stok AS current_stok, ps.id as id_stock
    FROM produk p
    JOIN produk_stock ps ON p.id_produk = ps.id_produk
    WHERE ps.stok < 100
    ORDER BY ps.stok ASC
    LIMIT 100
";

$stmt = $conn->query($sql);
$produk_reminder = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql_jumlah_produk = "SELECT COUNT(*) as total_produk FROM produk";
$stmt_jumlah_produk = $conn->query($sql_jumlah_produk);
$row_jumlah_produk = $stmt_jumlah_produk->fetch(PDO::FETCH_ASSOC);
$total_produk = $row_jumlah_produk['total_produk'];

$sql_total_penjualan = "SELECT COUNT(*) AS total_penjualan FROM pesanan";
$total_penjualan = $conn->query($sql_total_penjualan)->fetch(PDO::FETCH_ASSOC)['total_penjualan'];

$sql_total_pendapatan = "SELECT SUM(total_harga) AS total_pendapatan FROM pesanan";
$total_pendapatan = $conn->query($sql_total_pendapatan)->fetch(PDO::FETCH_ASSOC)['total_pendapatan'];

$sql_total_pelanggan = "SELECT COUNT(DISTINCT id_pelanggan) AS total_pelanggan FROM pesanan";
$total_pelanggan = $conn->query($sql_total_pelanggan)->fetch(PDO::FETCH_ASSOC)['total_pelanggan'];

// Ambil data penjualan harian untuk grafik
$sql_penjualan = "
    SELECT DATE(tanggal_pesanan) as tanggal, COUNT(*) as jumlah
    FROM pesanan
    GROUP BY DATE(tanggal_pesanan)
    ORDER BY tanggal ASC
";
$stmt_penjualan = $conn->query($sql_penjualan);
$penjualan = $stmt_penjualan->fetchAll(PDO::FETCH_ASSOC);

$tanggal_array = [];
$jumlah_array = [];
foreach ($penjualan as $row) {
    $tanggal_array[] = $row['tanggal'];
    $jumlah_array[] = $row['jumlah'];
}

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
<?php include '../sidebar.php'; ?> <div class="flex-grow-1 p-4">
 <h2>Selamat Datang, <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Tamu' ?></h2>
  <hr>

  <div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
      <div class="card shadow-sm border-0">
        <div class="card-body d-flex align-items-center">
          <div class="me-3">
            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
          </div>
          <div>
            <h6 class="mb-0">Total Penjualan</h6>
            <h4 class="fw-bold"><?= $total_penjualan ?></h4>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="card shadow-sm border-0">
        <div class="card-body d-flex align-items-center">
          <div class="me-3">
            <i class="fas fa-money-bill-wave fa-2x text-success"></i>
          </div>
          <div>
            <h6 class="mb-0">Pendapatan</h6>
            <h4 class="fw-bold">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h4>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="card shadow-sm border-0">
        <div class="card-body d-flex align-items-center">
          <div class="me-3">
            <i class="fas fa-users fa-2x text-warning"></i>
          </div>
          <div>
            <h6 class="mb-0">Pelanggan</h6>
            <h4 class="fw-bold"><?= $total_pelanggan ?></h4>
          </div>
        </div>
      </div>
    </div>

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

  <div class="row g-4 mb-4">
  <div class="col-lg-8">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body">
        <h5 class="card-title">Grafik Penjualan</h5>
        <select id="filterType" class="form-select w-auto">
        <option value="daily">Harian</option>
        <option value="monthly">Bulanan</option></select>
        <canvas id="salesChart" height="150"></canvas>
      </div>
    </div>
  </div>

<div class="col-lg-4">
  <div class="card shadow-sm border-0 h-100">
    <div class="card-body">
      <h5 class="card-title mb-3">Stok Hampir Habis!</h5>

      <?php if (count($produk_reminder) > 0): ?>
        <?php foreach ($produk_reminder as $produk): ?>
          <div class="card product-card mb-3">
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
              <div class="stock-text <?= ($produk['current_stok'] <= 2) ? 'text-danger' : '' ?>">
                Stok tersisa
                <?php
                    if ($produk['size'] !== 'NO_SIZE' && !empty($produk['size'])) {
                        echo htmlspecialchars($produk['size']) . ': ' . $produk['current_stok'];
                    } else {
                        echo $produk['current_stok'];
                    }
                ?>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const rawData = <?= json_encode($penjualan) ?>;

  // Proses data untuk tampilan harian
  const dailyLabels = rawData.map(item => item.tanggal);
  const dailyData = rawData.map(item => parseInt(item.jumlah));

  // Proses data untuk tampilan bulanan
  const monthlyMap = {};
  rawData.forEach(item => {
    const [year, month] = item.tanggal.split('-');
    const key = `<span class="math-inline">\{year\}\-</span>{month}`;
    monthlyMap[key] = (monthlyMap[key] || 0) + parseInt(item.jumlah);
  });
  const monthlyLabels = Object.keys(monthlyMap);
  const monthlyData = Object.values(monthlyMap);

  const ctx = document.getElementById('salesChart').getContext('2d');
  let salesChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: dailyLabels,
      datasets: [{
        label: 'Jumlah Penjualan per Tanggal',
        data: dailyData,
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

  // Event untuk ganti tampilan
  document.getElementById('filterType').addEventListener('change', function () {
    const type = this.value;

    if (type === 'daily') {
      salesChart.data.labels = dailyLabels;
      salesChart.data.datasets[0].data = dailyData;
      salesChart.data.datasets[0].label = 'Jumlah Penjualan per Tanggal';
    } else {
      salesChart.data.labels = monthlyLabels;
      salesChart.data.datasets[0].data = monthlyData;
      salesChart.data.datasets[0].label = 'Jumlah Penjualan per Bulan';
    }

    salesChart.update();
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