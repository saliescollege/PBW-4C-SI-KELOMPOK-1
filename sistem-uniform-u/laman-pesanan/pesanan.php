<?php session_start(); ?>

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
      background-color: #ffe6e6 !important;
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
  </style>

</head>

<body>
  <div class="d-flex">
  <?php include '../sidebar.php'; ?> <!--Menambahkan sidebar-->

  <!-- Main Content -->
  <div class="flex-grow-1 p-4">
    <h1>Pesanan</h1>
    <hr>
    <!-- Toolbar -->
     <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-1">List Transaksi</h5>
      <div class="d-flex align-items-center gap-2">
        <a href="pesanan-baru.php" class="btn btn-light border text-black">
          <i class="fas fa-plus me-1"></i> Tambah Pesanan
        </a>
        <div class="input-group" style="max-width: 250px;">
          <span class="input-group-text"><i class="fas fa-search"></i></span>
          <input type="text" id="searchInput" class="form-control" placeholder="Cari transaksi...">
        </div>
      </div>
    </div>


    <!-- Card List Pesanan -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle table-bordered">
              <thead class="table-light">
                <tr>
                  <th>ID Pesanan</th>
                  <th>ID Pelanggan</th>
                  <th>Tanggal Pesanan</th>
                  <th>Total Harga</th>
                  <th>Status Pembayaran</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#ORD001</td>
                  <td>#CUST123</td>
                  <td>01/04/2025</td>
                  <td>IDR 245,000</td>
                  <td>
                    <span class="status-badge text-success border border-success bg-light">
                      <i class="fas fa-check-circle"></i> Lunas
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-full" title="Lihat Detail"><i class="fas fa-receipt"></i></button>
                  </td>
                </tr>
                <tr>
                  <td>#ORD002</td>
                  <td>#CUST124</td>
                  <td>01/04/2025</td>
                  <td>IDR 180,000</td>
                  <td>
                    <span class="status-badge text-danger border border-danger bg-light-pink">
                      <i class="fas fa-times-circle"></i> Belum Lunas
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-full" title="Lihat Detail"><i class="fas fa-receipt"></i></button>
                  </td>
                </tr>
                <tr>
                  <td>#ORD003</td>
                  <td>#CUST125</td>
                  <td>31/03/2025</td>
                  <td>IDR 300,000</td>
                  <td>
                    <span class="status-badge text-success border border-success bg-light">
                      <i class="fas fa-check-circle"></i> Lunas
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-full" title="Lihat Detail"><i class="fas fa-receipt"></i></button>
                  </td>
                </tr>
                <tr>
                  <td>#ORD004</td>
                  <td>#CUST126</td>
                  <td>30/03/2025</td>
                  <td>IDR 220,000</td>
                  <td>
                    <span class="status-badge text-danger border border-danger bg-light-pink">
                      <i class="fas fa-times-circle"></i> Belum Lunas
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-full" title="Lihat Detail"><i class="fas fa-receipt"></i></button>
                  </td>
                </tr>
                <tr>
                  <td>#ORD005</td>
                  <td>#CUST127</td>
                  <td>29/03/2025</td>
                  <td>IDR 400,000</td>
                  <td>
                    <span class="status-badge text-success border border-success bg-light">
                      <i class="fas fa-check-circle"></i> Lunas
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-full" title="Lihat Detail"><i class="fas fa-receipt"></i></button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      

<!-- JavaScript -->
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
    
    document.getElementById("searchInput").addEventListener("keyup", function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll("table tbody tr");

    rows.forEach(row => {
      const rowText = row.textContent.toLowerCase();
      row.style.display = rowText.includes(searchValue) ? "" : "none";
    });
    });
  </script>
</body>
</html>