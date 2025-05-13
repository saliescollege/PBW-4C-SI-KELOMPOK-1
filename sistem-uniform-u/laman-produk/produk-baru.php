<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Produk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../styles.css">
  
  <style>
    body {
      padding: 20px;
    }

    .form-label {
      font-weight: 500;
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

    .main-wrapper {
    display: flex;
  }

  .sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
  }

  .main-content {
    margin-left: 250px;
    padding: 20px;
    width: calc(100% - 250px);
  }

  .collapsed {
    width: 70px !important;
  }

  .collapsed + .main-content {
    margin-left: 80px !important;
    width: calc(100% - 80px);
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
        <h6 class="mb-1">
          List Produk <span class="text-muted">&gt; Tambah Produk</span>
        </h6>
      </div>
      <p></p>
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <form>
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="productName" class="form-label">Nama Produk</label>
                <input type="text" class="form-control" id="productName" placeholder="Masukkan nama produk">
              </div>
              <div class="col-md-6">
                <label for="productCategory" class="form-label">Kategori</label>
                <select class="form-select" id="productCategory">
                  <option selected disabled>Pilih kategori</option>
                  <option value="SD">SD</option>
                  <option value="SMP">SMP</option>
                  <option value="SMA">SMA</option>
                </select>
              </div>
            </div>
      
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="productGender" class="form-label">Jenis Kelamin</label>
                <select class="form-select" id="productGender">
                  <option selected disabled>Pilih jenis kelamin</option>
                  <option value="Pria">Pria</option>
                  <option value="Wanita">Wanita</option>
                  <option value="Unisex">Unisex</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="productPrice" class="form-label">Harga</label>
                <input type="number" class="form-control" id="productPrice" placeholder="Contoh: 124850">
              </div>
            </div>
      
            <div class="mb-3">
              <label for="productSizes" class="form-label">Stok per Ukuran</label>
              <div class="row g-2">
                <div class="col">
                  <input type="number" class="form-control" placeholder="XS">
                </div>
                <div class="col">
                  <input type="number" class="form-control" placeholder="S">
                </div>
                <div class="col">
                  <input type="number" class="form-control" placeholder="M">
                </div>
                <div class="col">
                  <input type="number" class="form-control" placeholder="L">
                </div>
                <div class="col">
                  <input type="number" class="form-control" placeholder="XL">
                </div>
              </div>
            </div>
      
            <div class="mb-3">
              <label for="productImage" class="form-label">Upload Gambar Produk</label>
              <input class="form-control" type="file" id="productImage">
            </div>
      
            <button type="submit" class="btn custom-btn">Simpan</button>

            <button type="button" class="btn btn-danger">Batal</button>
          </form>
        </div>
      </div>      
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

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