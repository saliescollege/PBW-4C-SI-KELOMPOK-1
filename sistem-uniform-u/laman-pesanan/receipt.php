<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles.css">
</head>

<body>
    <div class="d-flex">
    <?php include '../sidebar.php'; ?> <!--Menambahkan sidebar-->
    
        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <h1>Pesanan</h1>
            <hr>
            <!-- Toolbar -->
            <div class="transaction-toolbar">
              <h6 class="mb-1">
                  List Transaksi <span class="text-muted">&gt; Detail Transaksi</span>
                  <span class="text-muted">&gt; Pembayaran</span>
                </h6>
                <p></p>
        </div>

<!-- Receipt Card -->
<div class="card shadow-sm p-4 mt-4">
    <div class="d-flex justify-content-between">
      <h5 class="fw-bold">Faktur Pembayaran</h5>
      <span class="text-muted">01 April 2025</span>
    </div>
    <hr>
  
    <div class="mb-3">
      <p class="mb-1"><strong>ID Pesanan:</strong> #ORD001</p>
      <p class="mb-1"><strong>Nama Pelanggan:</strong> Amelia Putri</p>
      <p class="mb-1"><strong>Metode Pembayaran:</strong> Tunai</p>
    </div>
  
    <table class="table table-sm">
      <thead class="table-light">
        <tr>
          <th>Produk</th>
          <th>Ukuran</th>
          <th>Qty</th>
          <th>Harga</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Seragam Putih</td>
          <td>M</td>
          <td>2</td>
          <td>IDR 120,000</td>
        </tr>
        <tr>
          <td>Rok Abu-Abu</td>
          <td>L</td>
          <td>1</td>
          <td>IDR 125,000</td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="3" class="text-end">Total</th>
          <th>IDR 365,000</th>
        </tr>
      </tfoot>
    </table>
  
    <div class="mt-4">
      <p><strong>Nominal Uang Tunai:</strong> IDR 400,000</p>
      <p><strong>Kembalian:</strong> IDR 35,000</p>
    </div>
  
    <div class="text-end mt-4">
      <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="fas fa-print me-1"></i> Cetak Faktur
      </button>
    </div>
  </div>  
</body>

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
</html>