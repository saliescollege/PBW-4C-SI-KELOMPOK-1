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
    <?php include '../sidebar.php'; ?> <!--Menambahkan sidebar-->

    <!-- Main Content -->
    <div class="main-content">
      <h1>Produk</h1>
      <hr>

      <!-- Toolbar -->
      <div class="product-toolbar">
        <h6 class="mb-1">List Produk</h6>
        
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-light border text-black">
            <i class="fas fa-plus me-1"></i> Tambah Produk
          </button>
      
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

      <!-- Product Cards -->
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
        <!-- Contoh product card (duplikat sesuai kebutuhan) -->
        <!-- Produk 1 -->
        <div class="col">
          <div class="card product-card">
            <img src="Kemeja.png" class="card-img-top" alt="Produk 1">
            <div class="card-body">
              <span class="product-tag tag-smp">SMP</span>
              <div class="d-flex justify-content-between align-items-center">
                <strong>Kemeja Putih</strong>
                <i class="fas fa-edit text-muted"></i>
              </div>
              <small class="text-muted">Pria</small>
              <hr>
              <div class="size-buttons">
                <button class="btn btn-outline-secondary btn-sm" onclick="showStock(this, 10)">XS</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="showStock(this, 15)">S</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="showStock(this, 8)">M</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="showStock(this, 5)">L</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="showStock(this, 2)">XL</button>
              </div>
              <div class="stock-text"></div>
              <hr>
              <h6 class="fw-bold">IDR 124,850</h6>
            </div>
          </div>
        </div>

        <!-- Produk 2 -->
        <div class="col">
          <div class="card product-card">
            <img src="Rok.png" class="card-img-top" alt="Produk 1">
            <div class="card-body">
              <span class="product-tag tag-sd">SD</span>
              <div class="d-flex justify-content-between align-items-center">
                <strong>Rok Merah</strong>
                <i class="fas fa-edit text-muted"></i>
              </div>
              <small class="text-muted">Wanita</small>
              <hr>
              <div class="size-buttons">
                <button class="btn btn-outline-secondary btn-sm" onclick="showStock(this, 10)">XS</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="showStock(this, 15)">S</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="showStock(this, 8)">M</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="showStock(this, 5)">L</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="showStock(this, 2)">XL</button>
              </div>
              <div class="stock-text"></div>
              <hr>
              <h6 class="fw-bold">IDR 94,760</h6>
            </div>
          </div>
        </div>

        <!-- Produk 3 -->
        <div class="col">
          <div class="card product-card">
            <img src="Topi.png" class="card-img-top" alt="Produk 1">
            <div class="card-body">
              <span class="product-tag tag-sma">SMA</span>
              <div class="d-flex justify-content-between align-items-center">
                <strong>Topi Abu-Abu</strong>
                <i class="fas fa-edit text-muted"></i>
              </div>
              <small class="text-muted">Unisex</small>
              <hr>
              <strong> </strong>
              <h6 class="fw-bold">IDR 46,560</h6>
            </div>
          </div>
        </div>
        
        <!-- Produk 4 -->
        <div class="col">
          <div class="card product-card">
            <img src="Sabuk.png" class="card-img-top" alt="Produk 4">
            <div class="card-body">
              <span class="product-tag tag-smp">SMP</span>
              <div class="d-flex justify-content-between align-items-center">
                <strong>Sabuk OSIS Kuning</strong>
                <i class="fas fa-edit text-muted"></i>
              </div>
              <small class="text-muted">Unisex</small>
              <hr>
              <strong> </strong>
              <h6 class="fw-bold">IDR 29,980</h6>
            </div>
          </div>
        </div>       

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

    function showStock(button, stock) {
      const allButtons = button.parentElement.querySelectorAll('button');
      allButtons.forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');

      const stockDiv = button.closest('.card-body').querySelector('.stock-text');
      stockDiv.textContent = `Stok: ${stock}`;
    }
  </script>
</body>
</html>