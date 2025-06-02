<?php session_start(); ?>
<?php include '../koneksi.php'; ?>

<?php
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

$where = '';
if (!empty($startDate) && !empty($endDate)) {
  $where = "WHERE DATE(tanggal_pesanan) BETWEEN '$startDate' AND '$endDate'";
}

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
</head>
<body>
  <div class="d-flex">
    <?php include '../sidebar.php'; ?> <!-- sidebar tetap -->

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
      <h1>Laporan Penjualan</h1>
      <hr>

      <!-- Toolbar dengan form GET -->
      <form method="GET" class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
          <label for="startDate" class="form-label mb-0 me-2">Rentang Waktu:</label>
          <div class="d-flex align-items-center">
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
          <button type="submit" class="btn btn-primary btn-sm ms-3">Filter</button>
        </div>

        <button type="button" class="btn btn-danger btn-sm mt-2 mt-md-0" onclick="downloadPDF()">
          <i class="fas fa-file-pdf me-2"></i>Download PDF
        </button>
      </form>

      <!-- Tabel Laporan -->
      <div class="table-responsive">
        <table class="table table-striped table-bordered" id="reportTable">
          <thead>
            <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>Jumlah Pesanan</th>
              <th>Total Pendapatan (Rp)</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if ($result && mysqli_num_rows($result) > 0) {
              $no = 1;
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>";
                echo "<td>" . number_format($row['jumlah_pesanan']) . "</td>";
                echo "<td>" . number_format($row['total_pendapatan'], 0, ',', '.') . "</td>";
                echo "</tr>";
              }
            } else {
              echo '<tr><td colspan="4" class="text-center">Data tidak ditemukan</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <script>
    function downloadPDF() {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();

      doc.setFontSize(18);
      doc.text("Laporan Penjualan", 14, 22);

      const startDate = document.getElementById('startDate').value;
      const endDate = document.getElementById('endDate').value;
      doc.setFontSize(12);
      if (startDate && endDate) {
        doc.text(`Rentang: ${startDate} s/d ${endDate}`, 14, 30);
      }

      const headers = [["No", "Tanggal", "Jumlah Pesanan", "Total Pendapatan (Rp)"]];
      const data = [];
      document.querySelectorAll("#reportTable tbody tr").forEach(row => {
        const cols = row.querySelectorAll("td");
        if(cols.length === 4) {
          data.push([
            cols[0].innerText,
            cols[1].innerText,
            cols[2].innerText,
            cols[3].innerText
          ]);
        }
      });

      if(data.length === 0) {
        alert("Tidak ada data untuk diunduh.");
        return;
      }

      doc.autoTable({
        startY: 40,
        head: headers,
        body: data,
        styles: { fontSize: 10 },
      });

      doc.save("laporan_penjualan.pdf");
    }
  </script>
</body>
</html>
