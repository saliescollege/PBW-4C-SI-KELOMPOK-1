<?php
session_start();
// koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$database = "db_uniform";

$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$query = "SELECT * FROM produk";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Produk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../styles.css">

  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow-x: hidden;
    }

    .main-wrapper {
      display: flex;
      height: 100vh;
    }

    .sidebar {
      width: 250px;
      flex-shrink: 0;
    }

    .main-content {
      flex-grow: 1;
      padding: 20px;
      overflow-y: auto;
    }

    .product-toolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }

    .product-categories {
      margin-bottom: 20px;
    }

    .product-categories .btn {
      margin-right: 10px;
      min-width: 80px;
      font-weight: 500;
    }

    .sd-btn {
      background-color: #ffe6eb;
      color: #d40000;
      border: 1px solid #f5c2cb;
    }

    .smp-btn {
      background-color: #e6f9ff;
      color: #003366;
      border: 1px solid #8bdbed;
    }

    .sma-btn {
      background-color: #e6edf4;
      color: #393737;
      border: 1px solid #d0d0e1;
    }

    .product-tag {
      display: inline-block;
      font-size: 0.75rem;
      font-weight: 600;
      padding: 2px 6px;
      border-radius: 4px;
      border: 1px solid;
      margin-bottom: 5px;
    }

    .tag-sd { border-color: #f5c2cb; background-color: pink; color: red; }
    .tag-smp { border-color: #8bdbed; background-color: #e6f9ff; color: navy; }
    .tag-sma { border-color: #d0d0e1; background-color: #e6edf4; color: #393737; }

    .product-card {
    height: 100%;
    display: flex;
    flex-direction: column;
    }

    .product-card img {
      width: 100%;
      aspect-ratio: 1/1;
      object-fit: cover;
    }

    .product-card .size-buttons {
      display: flex;
      justify-content: space-between;
      gap: 4px;
      margin-bottom: 0.5rem;
    }

    .product-card .size-buttons .btn {
      flex: 1;
      font-size: 0.75rem;
      padding: 5px 0;
    }

    .stock-text {
      font-size: 0.75rem;
      color: #777;
      text-align: center;
    }

    .input-icon {
      position: relative;
      width: 250px;
    }

    .input-icon i {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
    }

    .input-icon input {
      padding-left: 30px;
    }

    @media (max-width: 991.98px) {
      .col-md-4 {
        flex: 0 0 50%;
        max-width: 50%;
      }
    }

    @media (max-width: 575.98px) {
      .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
      }
    }
  </style>
</head>
<body>
<div class="main-wrapper">
  <?php include '../sidebar.php'; ?> <!-- Sidebar -->

  <div class="main-content">
    <h1>Produk</h1>
    <hr>

    <!-- Toolbar -->
    <div class="product-toolbar">
      <h6 class="mb-1">List Produk</h6>
      <div class="d-flex align-items-center gap-2">
        <a href="add_produk.php" class="btn btn-light border text-black">
          <i class="fas fa-plus me-1"></i> Tambah Produk
        </a>
        <div class="input-icon">
          <i class="fas fa-search"></i>
          <input type="text" class="form-control" placeholder="Cari produk...">
        </div>
      </div>
    </div>

    <!-- Kategori -->
    <div class="product-categories">
      <button class="btn btn-light border text-black">Semua Produk</button>
      <button class="btn sd-btn">SD</button>
      <button class="btn smp-btn">SMP</button>
      <button class="btn sma-btn">SMA</button>
    </div>

<div class="container mt-4">
    <h3>List Produk</h3>
    <div class="row" id="listProduk">
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <?php 
                $id = $row['id_produk'];
                $nama = $row['nama_produk'];
                $kategori = $row['kategori'];
                $gambar = $row['gambar_produk'];
                $harga = number_format($row['harga'], 0, ',', '.');

                // Ambil semua entri stok produk ini
                $stokQuery = mysqli_query($conn, "SELECT size, stok FROM produk_stock WHERE id_produk = '$id'");
                $sizes = [];

                while ($data = mysqli_fetch_assoc($stokQuery)) {
                    $cleanedSize = trim($data['size']);
                    // Validasi ukuran (hanya nilai yang benar seperti S, M, L, XL, dst)
                    if ($cleanedSize !== '' && $cleanedSize !== '-' && preg_match('/^[A-Za-z0-9]+$/', $cleanedSize)) {
                        $sizes[] = $data;
                    }
                }

                // Jika tidak ada ukuran valid, maka produk dianggap tidak memiliki size
                $punyaUkuran = count($sizes) > 0;
            ?>
            <div class="col-md-3 mb-4 produk-item">
                  <div class="card h-100 product-card" data-product-id="<?= $id ?>">
                
                      <img src="../assets/uniform/<?= htmlspecialchars($gambar) ?>" 
                          class="card-img-top" 
                          alt="<?= htmlspecialchars($nama) ?>" 
                          style="height: 200px; object-fit: contain;" />

                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-info text-dark mb-2"><?= htmlspecialchars($kategori) ?></span>
                        <h5 class="card-title"><?= htmlspecialchars($nama) ?></h5>
                        <p class="card-text mb-1">Rp <?= $harga ?></p>

                        <?php if ($punyaUkuran): ?>
                            <div class="mb-2">
                                <?php foreach ($sizes as $data): ?>
                                    <button class="btn btn-sm btn-outline-primary me-1 mb-1" onclick="showStock(this, '<?= $data['size'] ?>')">
                                        <?= htmlspecialchars($data['size']) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <button class="btn btn-sm btn-outline-secondary mb-2" onclick="showStock(this)">Show Stock</button>
                        <?php endif; ?>

                        <p class="stock-text fw-bold text-success mt-2"></p>

                        <!-- Tombol Edit dan Hapus -->
                        <div class="mt-auto d-flex justify-content-between">
                            <a href="update-produk.php?id=<?= $id ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="hapus-produk.php?id=<?= $id ?>" class="btn btn-sm btn-warning">Hapus</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
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

  // Fungsi showStock untuk produk dengan atau tanpa ukuran
function showStock(button, size = null) {
    const productId = button.closest('.product-card').getAttribute('data-product-id');
    const stockDiv = button.closest('.card-body').querySelector('.stock-text');

    if (!size) {
        fetch(`get_stock.php?id_produk=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.stock !== undefined) {
                    // Tampilkan stok meskipun 0
                    stockDiv.textContent = `Stok: ${data.stock}`;
                } else if (data.error) {
                    stockDiv.textContent = `Error: ${data.error}`;
                } else {
                    stockDiv.textContent = 'Stok: Tidak tersedia';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil stok.');
            });
    } else {
        // Produk dengan ukuran (gunakan parameter size)
        fetch(`get_stock.php?id_produk=${productId}&size=${size}`)
            .then(response => response.json())
            .then(data => {
                if (data.stock !== undefined) {
                    stockDiv.textContent = `Stok: ${data.stock}`;
                } else if (data.error) {
                    stockDiv.textContent = `Error: ${data.error}`;
                    alert(`Error: ${data.error}`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil stok.');
            });
    }
}
</script>



</body>
</html>