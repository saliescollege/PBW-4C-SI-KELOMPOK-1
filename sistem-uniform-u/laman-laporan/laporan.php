<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laporan Penjualan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <link rel="stylesheet" href="../styles.css">
</head>

<body>
  <div class="d-flex">
  <?php include '../sidebar.php'; ?> <!--Menambahkan sidebar-->

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
      <h1>Laporan Penjualan</h1>
      <hr>

      <!-- Toolbar -->
      <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
          <label for="startDate" class="form-label mb-0 me-2">Rentang Waktu:</label>
      
          <div class="d-flex align-items-center">
            <input type="date" id="startDate" class="form-control form-control-sm me-2" style="max-width: 160px;">
            <span class="me-2">s/d</span>
            <input type="date" id="endDate" class="form-control form-control-sm" style="max-width: 160px;">
          </div>
        </div>
      
        <button class="btn btn-danger btn-sm mt-2 mt-md-0" onclick="downloadPDF()">
          <i class="fas fa-file-pdf me-2"></i>Download PDF
        </button>
      </div>

      <!-- Tabel Laporan -->
      <div class="card shadow-sm mb-4" id="reportSection">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle table-bordered">
              <thead class="table-light">
                <tr>
                  <th>Tanggal</th>
                  <th>ID Invoice</th>
                  <th>Jumlah Pesanan</th>
                  <th>Total Pendapatan</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2025-04-01</td>
                  <td>INV-001</td>
                  <td>5</td>
                  <td>Rp500.000</td>
                </tr>
                <tr>
                  <td>2025-04-01</td>
                  <td>INV-002</td>
                  <td>3</td>
                  <td>Rp300.000</td>
                </tr>
                <tr>
                  <td>2025-04-02</td>
                  <td>INV-003</td>
                  <td>2</td>
                  <td>Rp200.000</td>
                </tr>
                <tr>
                  <td>2025-04-03</td>
                  <td>INV-004</td>
                  <td>4</td>
                  <td>Rp400.000</td>
                </tr>
                <tr>
                  <td>2025-04-04</td>
                  <td>INV-005</td>
                  <td>1</td>
                  <td>Rp100.000</td>
                </tr>
                <tr>
                  <td>2025-04-05</td>
                  <td>INV-006</td>
                  <td>6</td>
                  <td>Rp600.000</td>
                </tr>
                <tr>
                  <td>2025-04-06</td>
                  <td>INV-007</td>
                  <td>7</td>
                  <td>Rp700.000</td>
                </tr>
              </tbody>              
            </table>
          </div>
        </div>
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
</script>