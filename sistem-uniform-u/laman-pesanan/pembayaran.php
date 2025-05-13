<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
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

          <!-- Card Form Pembayaran -->
    <div class="card shadow-sm p-4 mt-4" style="max-width: 600px;">
    <h5 class="mb-3">Form Pembayaran</h5>
    <form>
      <div class="mb-3">
        <label for="paymentMethod" class="form-label">Metode Pembayaran</label>
        <select class="form-select" id="paymentMethod" required>
          <option value="">-- Pilih Metode --</option>
          <option value="transfer">Transfer Bank</option>
          <option value="qris">QRIS</option>
          <option value="tunai">Tunai</option>
        </select>
      </div>
  
      <div class="mb-3">
        <label for="amount" class="form-label">Nominal Pembayaran</label>
        <input type="number" class="form-control" id="amount" placeholder="Masukkan jumlah bayar" required>
      </div>
  
      <div class="mb-3">
        <label for="note" class="form-label">Catatan (Opsional)</label>
        <textarea class="form-control" id="note" rows="2" placeholder="Misal: dibayar oleh wali murid..."></textarea>
      </div>
  
      <div class="text-center">
        <button type="submit" class="btn btn-success px-5">
          <i class="fas fa-paper-plane me-1"></i> Bayar Sekarang
        </button>
      </div>
    </form>
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

    document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    form.addEventListener("submit", function (e) {
      e.preventDefault(); // cegah reload
      alert("Pembayaran berhasil!"); // bisa diganti dengan AJAX, dsb.
    });
  });
  </script>
</html>