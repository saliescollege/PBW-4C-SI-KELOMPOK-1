<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pesanan Baru</title>

  <!-- CSS -->
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

    .hidden {
      display: none;
    }

    .container {
      max-width: 1000px; /* Untuk Mengatur lebar maksimal card */
      margin: auto; /*  Supaya cardnyaa tetap di tengah */
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
      display: inline-block;
    }

    .breadcrumb-custom li:not(:last-child):not([style*="display: none"])::after {
      content: "\203A"; /* tanda panah kecil (›) */
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

  </style>
</head>

<body>
  <div class="d-flex">
    <!-- Sidebar -->
    <?php include '../sidebar.php'; ?>

    <!-- Main Content -->
    <div class="container my-4">
    <div class="card shadow-lg">
      <div class="container">
      <h2 class="mb-4">Buat Pesanan Baru</h2>

     <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb-custom" id="breadcrumb">
            <li><a href="pesanan.php">List Pesanan</a></li>
            <li><a href="#">Detail Pesanan</a></li>
          </ul>
        </nav>

    <div class="card-body">
      <form>
        <!-- Informasi Umum -->
        <div class="mb-3">
          <label for="idPesanan" class="form-label">ID Pesanan</label>
          <input type="text" class="form-control" id="idPesanan" placeholder="#ORD001">
        </div>

        <div class="mb-3">
          <label for="namaPelanggan" class="form-label">Nama Pelanggan</label>
          <input type="text" class="form-control" id="namaPelanggan">
        </div>

        <div class="mb-3">
          <label for="institusi" class="form-label">Institusi</label>
          <input type="text" class="form-control" id="institusi">
        </div>

        <div class="mb-3">
          <label for="alamatInstitusi" class="form-label">Alamat Institusi</label>
          <textarea class="form-control" id="alamatInstitusi" rows="2"></textarea>
        </div>

        <!-- Metode Pembayaran -->
        <div class="mb-3">
          <label class="form-label">Metode Pembayaran</label><br>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="metodeBayar" id="lunas" value="lunas" checked>
            <label class="form-check-label" for="lunas">Lunas</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="metodeBayar" id="nyicil" value="nyicil">
            <label class="form-check-label" for="nyicil">Nyicil</label>
          </div>
        </div>

        <!-- Detail Cicilan -->
        <div id="detailCicilan" class="border p-3 mb-3 hidden">
          <div class="mb-2">
            <label for="nominalDp" class="form-label">Nominal DP</label>
            <input type="number" class="form-control" id="nominalDp" placeholder="Contoh: 100000">
          </div>
          <div class="mb-2">
            <label for="tanggalDp" class="form-label">Tanggal Pembayaran DP</label>
            <input type="date" class="form-control" id="tanggalDp">
          </div>
          <div class="mb-2">
            <label for="sisaBayar" class="form-label">Nominal Sisa yang Harus Dibayar</label>
            <input type="number" class="form-control" id="sisaBayar">
          </div>
          <div class="mb-2">
            <label for="tanggalJatuhTempo" class="form-label">Tanggal Jatuh Tempo</label>
            <input type="date" class="form-control" id="tanggalJatuhTempo">
          </div>
          <div class="mb-2">
            <label for="metodeBayarCicilan" class="form-label">Metode Pembayaran</label>
            <select class="form-select" id="metodeBayarCicilan">
              <option value="transfer">Transfer Bank</option>
              <option value="tunai">Tunai</option>
              <option value="qris">QRIS</option>
            </select>
          </div>
        </div>

        <!-- Produk yang Dipesan -->
        <div class="mb-3">
          <label class="form-label">Produk yang Dipesan</label>
          <div class="row g-2 mb-2">
            <div class="col-md-6">
              <input type="text" class="form-control" placeholder="Nama Produk">
            </div>
            <div class="col-md-3">
              <input type="number" class="form-control" placeholder="Jumlah">
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control" placeholder="Variasi (opsional)">
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-plus"></i> Tambah Produk
          </button>
        </div>

        <!-- Tombol Submit -->
        <div class="d-grid">
          <button type="submit" class="btn btn-success">Buat Pesanan</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

  <!-- JavaScript -->
  <script>
    const metodeBayarRadios = document.querySelectorAll('input[name="metodeBayar"]');
    const detailCicilanDiv = document.getElementById('detailCicilan');
    const pembayaranBreadcrumb = document.getElementById('pembayaran');

    metodeBayarRadios.forEach(radio => {
      radio.addEventListener('change', () => {
        const nyicilChecked = document.getElementById('nyicil').checked;
        detailCicilanDiv.classList.toggle('hidden', !nyicilChecked);

        // Tampilkan 'Pembayaran' jika metode dipilih
        pembayaranBreadcrumb.style.display = 'inline';
      });
    });


    // Sidebar toggle (jika ada elemen-elemen ini di sidebar.php)
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const logo = document.getElementById('sidebarLogo');

    toggleBtn?.addEventListener('click', () => {
      sidebar?.classList.toggle('collapsed');
    });

    sidebar?.addEventListener('mouseenter', () => {
      if (sidebar.classList.contains('collapsed')) {
        sidebar.classList.remove('collapsed');
      }
    });

    sidebar?.addEventListener('mouseleave', () => {
      if (!sidebar.classList.contains('manual-toggle')) {
        sidebar.classList.add('collapsed');
      }
    });

    // Fitur pencarian tabel (jika ada elemen pencarian)
    const searchInput = document.getElementById("searchInput");
    searchInput?.addEventListener("keyup", function () {
      const searchValue = this.value.toLowerCase();
      const rows = document.querySelectorAll("table tbody tr");

      rows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(searchValue) ? "" : "none";
      });
    });

    // Breadcrumb otomatis berdasarkan URL
    document.addEventListener('DOMContentLoaded', function () {
      const url = window.location.href;
      const pembayaranBreadcrumb = document.getElementById('pembayaran'); // id dari elemen breadcrumb 'Pembayaran'

      if (url.includes('pesanan.php')) {
        pembayaranBreadcrumb.style.display = 'none'; // Tidak tampil di list pesanan
      } else if (url.includes('detail-pesanan.php')) {
        pembayaranBreadcrumb.style.display = 'none'; // Tidak tampil di detail
      } else if (url.includes('pembayaran.php')) {
        pembayaranBreadcrumb.style.display = 'inline'; // Tampil di halaman pembayaran
      }
    });
  </script>
</body>
</html>
