<?php
session_start();
include '../koneksi.php';

// Ambil tanggal awal dan akhir dari filter form (GET)
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

// Siapkan filter SQL jika user memilih rentang tanggal
$where = '';
if (!empty($startDate) && !empty($endDate)) {
  $where = "WHERE DATE(tanggal_pesanan) BETWEEN '$startDate' AND '$endDate'";
}

// Query untuk mengambil data laporan penjualan per hari
$query = "
  SELECT 
    DATE(tanggal_pesanan) AS tanggal,
    COUNT(*) AS jumlah_pesanan,
    SUM(total_harga) AS total_pendapatan
  FROM pesanan
  $where
  GROUP BY DATE(tanggal_pesanan)
  ORDER BY tanggal ASC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Laporan Penjualan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
  <link rel="stylesheet" href="../styles.css" />
  <style>
    /* Samakan tinggi input date dengan button agar sejajar */
    .form-control[type="date"].form-control-sm,
    .btn.text-nowrap {
      height: 38px !important;
      min-height: 38px !important;
      font-size: 1rem;
      padding-top: 6px;
      padding-bottom: 6px;
    }
    .btn-pdf {
      background-color: #ffe6e6;
      color: #d63384;
      border: 1px solid #d63384;
    }
    .btn-pdf span {
      color: #d63384;
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <?php include '../sidebar.php'; ?>

    <div class="flex-grow-1 p-4">
      <h1>Laporan Penjualan</h1>
      <hr>

      <!-- Form filter tanggal dan tombol PDF -->
      <form method="GET" class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2">
        <div class="d-flex align-items-center flex-wrap gap-2">
          <label for="startDate" class="form-label mb-0 me-2">Rentang Waktu:</label>
          <div class="d-flex align-items-center">
            <!-- Input tanggal awal -->
            <input 
              type="date" 
              id="startDate" 
              name="startDate" 
              class="form-control form-control-sm me-2"
              style="max-width: 160px;"
              value="<?= htmlspecialchars($startDate) ?>"
              required
            />
            <span class="me-2">s/d</span>
            <!-- Input tanggal akhir -->
            <input 
              type="date" 
              id="endDate" 
              name="endDate" 
              class="form-control form-control-sm"
              style="max-width: 160px;"
              value="<?= htmlspecialchars($endDate) ?>"
              required
            />
          </div>
          <!-- Tombol filter data -->
          <button type="submit" class="btn btn-light border text-black text-nowrap">
            <i class="fas fa-filter me-1"></i> Filter
          </button>
        </div>
        <!-- Tombol unduh PDF -->
        <button type="button" id="btn-unduh-pdf" class="btn btn-light border text-black text-nowrap btn-pdf"
          onclick="downloadPDF()">
          <i class="fas fa-file-pdf me-1"></i>
          <span>Unduh PDF</span>
        </button>
      </form>

      <!-- Tabel laporan penjualan dalam card Bootstrap -->
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle table-bordered" id="reportTable">
              <thead class="table-light">
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>Jumlah Pesanan</th>
                  <th>Total Pendapatan (Rp)</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                // Tampilkan data laporan, jika tidak ada tampilkan pesan
                if ($result && mysqli_num_rows($result) > 0) {
                  $no = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    // Setiap baris menampilkan nomor, tanggal, jumlah pesanan, dan total pendapatan
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
                    echo "<td>" . number_format($row['jumlah_pesanan']) . "</td>";
                    echo "<td>Rp " . number_format($row['total_pendapatan'], 0, ',', '.') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  // Jika tidak ada data, tampilkan pesan
                  echo '<tr><td colspan="4" class="text-center text-muted">Data tidak ditemukan</td></tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Fungsi untuk mengunduh tabel laporan sebagai PDF menggunakan jsPDF dan autotable
    function downloadPDF() {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();

      // Judul laporan
      doc.setFontSize(18);
      doc.text("Laporan Penjualan", 14, 22);

      // Ambil tanggal filter untuk ditampilkan di PDF
      const startDate = document.getElementById('startDate').value;
      const endDate = document.getElementById('endDate').value;
      doc.setFontSize(12);
      if (startDate && endDate) {
        doc.text(`Rentang: ${startDate} s/d ${endDate}`, 14, 30);
      }

      // Ambil data tabel untuk PDF
      const headers = [["No", "Tanggal", "Jumlah Pesanan", "Total Pendapatan (Rp)"]];
      const data = [];
      document.querySelectorAll("#reportTable tbody tr").forEach(row => {
        const cols = row.querySelectorAll("td");
        // Ambil data dari setiap kolom pada baris tabel
        if(cols.length === 4) {
          data.push([
            cols[0].innerText,
            cols[1].innerText,
            cols[2].innerText,
            cols[3].innerText
          ]);
        }
      });

      // Jika tidak ada data, tampilkan alert
      if(data.length === 0) {
        alert("Tidak ada data untuk diunduh.");
        return;
      }

      // Buat tabel di PDF menggunakan autotable
      doc.autoTable({
        startY: 40,
        head: headers,
        body: data,
        styles: { fontSize: 10 },
      });

      // Simpan file PDF
      doc.save("laporan_penjualan.pdf");
    }

    // Efek collapse di Sidebar
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const logo = document.getElementById('sidebarLogo');

    if (sidebar && toggleBtn) {
      // Toggle sidebar saat tombol diklik
      toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
      });

      // Buka sidebar saat mouse hover
      sidebar.addEventListener('mouseenter', () => {
        if (sidebar.classList.contains('collapsed')) {
          sidebar.classList.remove('collapsed');
        }
      });

      // Tutup sidebar saat mouse keluar
      sidebar.addEventListener('mouseleave', () => {
        if (!sidebar.classList.contains('manual-toggle')) {
          sidebar.classList.add('collapsed');
        }
      });
    }
  </script>
</body>
</html>
