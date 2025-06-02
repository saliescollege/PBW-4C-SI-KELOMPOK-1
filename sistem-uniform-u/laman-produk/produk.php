<?php
include '../koneksi.php';
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
    .product-card img,
    .card-img-top {
      width: 100%;
      height: 220px;
      object-fit: cover;
      aspect-ratio: 1 / 1;
      background: #f8f9fa;
      border-top-left-radius: 16px;
      border-top-right-radius: 16px;
      display: block;
      margin-left: auto;
      margin-right: auto;
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
    .breadcrumb-custom {
      display: flex;
      list-style: none;
      padding: 8px 15px;
      background-color: #f8f9fa;
      border-radius: 8px;
      font-size: 0.9rem;
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
      color:rgb(1, 1, 1);
    }
    .breadcrumb-custom .active {
      color: #6c757d;
      pointer-events: none;
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
  <?php include '../sidebar.php'; ?>
  <div class="main-content">
    <h1>Produk</h1>
    <hr>
    <nav aria-label="breadcrumb" class="breadcrumb-container mb-3">
      <ul class="breadcrumb-custom" id="breadcrumb">
        <li class="active">List Produk</li>
      </ul>
    </nav>
    <div class="product-toolbar">
      <div class="product-categories">
        <button id="btn-semua" class="btn btn-light border text-black">Semua Produk</button>
        <button id="btn-sd" class="btn sd-btn">SD</button>
        <button id="btn-smp" class="btn smp-btn">SMP</button>
        <button id="btn-sma" class="btn sma-btn">SMA</button>
      </div>
      <div class="d-flex align-items-center gap-2">
        <a href="add_produk.php" class="btn btn-light border text-black">
          <i class="fas fa-plus me-1"></i> Tambah Produk
        </a>
        <div class="input-icon">
          <i class="fas fa-search"></i>
          <input type="text" class="form-control" placeholder="Cari produk..." id="searchInput">
        </div>
      </div>
    </div>
    <div class="row mt-4">
      <div class="row" id="listProduk">
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
          <?php 
            $id = $row['id_produk'];
            $nama = $row['nama_produk'];
            $kategori = $row['kategori'];
            $gambar = $row['gambar_produk'];
            $harga = number_format($row['harga'], 0, ',', '.');
            $badgeClass = '';
            $editBtnClass = '';
            $deleteBtnClass = '';
            if (strtolower($kategori) === 'sd') {
                $badgeClass = 'tag-sd';
                $editBtnClass = 'btn-danger';
                $deleteBtnClass = 'btn-danger';
            } elseif (strtolower($kategori) === 'smp') {
                $badgeClass = 'tag-smp';
                $editBtnClass = 'btn-primary';
                $deleteBtnClass = 'btn-primary';
            } elseif (strtolower($kategori) === 'sma') {
                $badgeClass = 'tag-sma';
                $editBtnClass = 'btn-secondary';
                $deleteBtnClass = 'btn-secondary';
            }
            $stokQuery = mysqli_query($conn, "SELECT size, stok FROM produk_stock WHERE id_produk = '$id'");
            $sizes = [];
            while ($data = mysqli_fetch_assoc($stokQuery)) {
                $cleanedSize = trim($data['size']);
                if ($cleanedSize !== '' && $cleanedSize !== '-' && preg_match('/^[A-Za-z0-9]+$/', $cleanedSize)) {
                    $sizes[] = $data;
                }
            }
            $punyaUkuran = count($sizes) > 0;
          ?>
          <div class="col-md-3 mb-4 produk-item">
            <div class="card h-100 product-card shadow-sm border-0" data-product-id="<?= $id ?>" data-kategori="<?= strtolower($kategori) ?>" style="border-radius: 16px;">
              <div class="position-relative">
                <img src="../assets/uniform/<?= htmlspecialchars($gambar) ?>"
                  class="card-img-top rounded-top"
                  alt="<?= htmlspecialchars($nama) ?>"
                  style="width: 100%; height: 100%; aspect-ratio: 1 / 1; object-fit: cover; background: #f8f9fa; display: block; margin-left: auto; margin-right: auto;" />
                <span class="position-absolute top-0 start-0 m-2 badge <?= $badgeClass ?> px-3 py-2 fs-6 shadow-sm" style="opacity:0.95;">
                  <?= htmlspecialchars($kategori) ?>
                </span>
              </div>
              <div class="card-body d-flex flex-column pt-2 pb-1 px-2">
                <div class="d-flex align-items-center justify-content-between mb-1">
                  <h5 class="card-title mb-0" style="font-size:1rem; color:#212529; font-weight:400;">
                    <?= htmlspecialchars($nama) ?>
                  </h5>
                  <div class="d-flex gap-1">
                    <a href="stok.php?id=<?= $id ?>"
                      class="btn btn-sm btn-light btn-icon-only border-0"
                      title="Edit"
                      style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:50%; color:#6c757d;">
                      <i class="fas fa-pen"></i>
                    </a>
                    <a href="hapus-produk.php?id=<?= $id ?>"
                      class="btn btn-sm btn-light btn-icon-only border-0"
                      title="Hapus"
                      onclick="return confirm('Yakin ingin menghapus produk ini?');"
                      style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:50%; color:#dc3545;">
                      <i class="fas fa-trash"></i>
                    </a>
                  </div>
                </div>
                <div class="mb-2" style="font-size:1rem; color:#212529; font-weight:700;">
                  Rp <?= $harga ?>
                </div>
                <?php if (!empty($row['jenis_kelamin'])): ?>
                  <div class="mb-2" style="font-size:0.95rem; color:#6c757d;">
                    <?= htmlspecialchars($row['jenis_kelamin']) ?>
                  </div>
                <?php endif; ?>
                <?php if ($punyaUkuran): ?>
                  <div class="mb-2 d-flex justify-content-between size-buttons">
                    <?php foreach ($sizes as $data): ?>
                      <button class="btn btn-sm btn-outline-primary px-3 me-1 mb-1 shadow-none"
                        onclick="showStock(this, '<?= $data['size'] ?>')"
                        style="transition: background 0.2s; font-size:0.85rem;">
                        <?= htmlspecialchars($data['size']) ?>
                      </button>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <button class="btn btn-sm btn-outline-secondary mb-2 shadow-none" onclick="showStock(this)">Show Stock</button>
                <?php endif; ?>
                <p class="stock-text fw-bold text-success mt-1 mb-2"></p>
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
      function showStock(button, size = null) {
        const productId = button.closest('.product-card').getAttribute('data-product-id');
        const stockDiv = button.closest('.card-body').querySelector('.stock-text');
        if (!size) {
          fetch(`get_stock.php?id_produk=${productId}`)
            .then(response => response.json())
            .then(data => {
              if (data.stock !== undefined) {
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
      function filterKategori(kategori) {
        const produkItems = document.querySelectorAll('.produk-item');
        produkItems.forEach(item => {
          const card = item.querySelector('.product-card');
          const kategoriProduk = card.getAttribute('data-kategori');
          if (kategori === 'all' || kategoriProduk === kategori) {
            item.style.display = 'block';
          } else {
            item.style.display = 'none';
          }
        });
      }
      document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('btn-semua').addEventListener('click', () => filterKategori('all'));
        document.getElementById('btn-sd').addEventListener('click', () => filterKategori('sd'));
        document.getElementById('btn-smp').addEventListener('click', () => filterKategori('smp'));
        document.getElementById('btn-sma').addEventListener('click', () => filterKategori('sma'));
      });
      document.getElementById("searchInput").addEventListener("input", function () {
        const keyword = this.value.toLowerCase();
        const produkItems = document.querySelectorAll(".produk-item");
        produkItems.forEach(function (item) {
          const namaProduk = item.querySelector(".card-title").textContent.toLowerCase();
          if (namaProduk.includes(keyword)) {
            item.style.display = "block";
          } else {
            item.style.display = "none";
          }
        });
      });
    </script>
  </div>
</div>
</body>
</html>