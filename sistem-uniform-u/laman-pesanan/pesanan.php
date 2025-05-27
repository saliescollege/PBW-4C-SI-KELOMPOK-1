<?php
session_start();
include '../koneksi.php';
include '../config.php';
?>

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
    <?php include '../sidebar.php'; ?>

    <div class="flex-grow-1 p-4">
      <h1>Pesanan</h1>
      <hr>
      <!-- Toolbar -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-1">List Pesanan</h6>
        <div class="d-flex align-items-center gap-2">
          <a href="pesanan-baru.php" class="btn btn-light border text-black text-nowrap">
            <i class="fas fa-plus me-1"></i> Tambah Pesanan
          </a>
          <div class="input-group" style="max-width: 250px;">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari transaksi...">
          </div>
        </div>
      </div>

      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle table-bordered">
              <thead class="table-light">
                <tr>
                  <th>ID Pesanan</th>
                  <th>Nama Pelanggan</th>
                  <th>Institusi</th>
                  <th>Tanggal Pesanan</th>
                  <th>Total Harga</th>
                  <th>Status Pembayaran</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT * FROM pesanan ORDER BY tanggal_pesanan DESC";
                $result = mysqli_query($conn, $sql);
                if ($result && mysqli_num_rows($result) > 0):
                  while ($row = mysqli_fetch_assoc($result)):
                ?>
                <tr>
                  <td><?= htmlspecialchars($row['id_pesanan']) ?></td>
                  <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                  <td><?= htmlspecialchars($row['institusi']) ?></td>
                  <td><?= htmlspecialchars($row['tanggal_pesanan']) ?></td>
                  <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                  <td>
                    <span class="status-badge bg-light-pink">
                      <?= htmlspecialchars($row['status_pembayaran']) ?>
                    </span>
                  </td>
                  <td>
                    <a href="detail_pesanan.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-sm btn-primary">Detail</a>
                    <a href="hapus_pesanan.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus pesanan?')">Hapus</a>
                  </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                  <td colspan="7" class="text-center text-muted">Belum ada pesanan.</td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
  // Fitur pencarian
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

<?php
// Alter table query to modify id_pesanan column
$alterSql = "ALTER TABLE pesanan MODIFY id_pesanan INT AUTO_INCREMENT PRIMARY KEY";
mysqli_query($conn, $alterSql);
?>