<?php
session_start();
include '../koneksi.php';

// Ambil daftar produk dari database untuk pilihan produk
$produkList = [];
$sql = "SELECT p.id_produk, p.nama_produk, p.kategori, p.harga FROM produk p";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $produkList[] = $row;
}

// Proses form jika ada POST (saat user submit pesanan baru)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pelanggan = $_POST['namaPelanggan'] ?? '';
    $no_telepon = $_POST['nomorTelepon'] ?? '';
    $sekolah = $_POST['sekolah'] ?? '';
    $alamat_sekolah = $_POST['alamatSekolah'] ?? '';
    $metode_bayar = $_POST['metodeBayar'] ?? '';
    $tanggal_pesanan = date('Y-m-d H:i:s');

    // Status pesanan tergantung metode bayar
    $status = ($metode_bayar === 'lunas') ? 'Sudah Lunas' : 'Dicicil';

    // Cek apakah pelanggan sudah ada, jika belum tambahkan
    $stmt = $conn->prepare("SELECT id_pelanggan FROM pelanggan WHERE nama_pelanggan=? AND no_telepon=? AND sekolah=? AND alamat_sekolah=?");
    if (!$stmt) die("Prepare failed (pelanggan SELECT): " . $conn->error);
    $stmt->bind_param("ssss", $nama_pelanggan, $no_telepon, $sekolah, $alamat_sekolah);
    $stmt->execute();
    $stmt->bind_result($id_pelanggan);
    if ($stmt->fetch()) {
        $stmt->close();
    } else {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO pelanggan (nama_pelanggan, no_telepon, sekolah, alamat_sekolah) VALUES (?, ?, ?, ?)");
        if (!$stmt) die("Prepare failed (pelanggan INSERT): " . $conn->error);
        $stmt->bind_param("ssss", $nama_pelanggan, $no_telepon, $sekolah, $alamat_sekolah);
        $stmt->execute();
        $id_pelanggan = $stmt->insert_id;
        $stmt->close();
    }

    // Ambil list produk yang dipesan dari input tersembunyi (JSON)
    $produkListPesanan = json_decode($_POST['produkListPesanan'] ?? '[]', true);

    // Hitung total harga pesanan
    $total_harga = 0;
    foreach ($produkListPesanan as $item) {
        $total_harga += ($item['harga'] ?? 0) * ($item['jumlah'] ?? 0);
    }

    // Simpan pesanan ke tabel pesanan
    $stmt = $conn->prepare("INSERT INTO pesanan (id_pelanggan, tanggal_pesanan, total_harga, status) VALUES (?, ?, ?, ?)");
    if (!$stmt) die("Prepare failed (pesanan INSERT): " . $conn->error);
    $stmt->bind_param("isds", $id_pelanggan, $tanggal_pesanan, $total_harga, $status);
    $stmt->execute();
    $id_pesanan = $stmt->insert_id;
    $stmt->close();

    // Tentukan metode pembayaran dan nominal bayar
    $metode_pembayaran = $_POST['metodeBayarCicilan'] ?? $metode_bayar;
    if ($status === 'Sudah Lunas') {
        $jumlah_bayar = $total_harga;
        $tanggal_bayar = $tanggal_pesanan;
    } else {
        $jumlah_bayar = floatval($_POST['nominalDp'] ?? 0);
        $tanggal_bayar = $_POST['tanggalDp'] ?? $tanggal_pesanan;
        $metode_pembayaran = $_POST['metodeBayarCicilan'] ?? $metode_bayar;
    }

    // Simpan data pembayaran ke tabel pembayaran
    $stmt = $conn->prepare("INSERT INTO pembayaran (id_pesanan, metode_pembayaran, jumlah_bayar, tanggal_bayar) VALUES (?, ?, ?, ?)");
    if (!$stmt) die("Prepare failed (pembayaran INSERT): " . $conn->error);
    $stmt->bind_param("isds", $id_pesanan, $metode_pembayaran, $jumlah_bayar, $tanggal_bayar);
    $stmt->execute();
    $stmt->close();

    // Update stok produk sesuai pesanan
    foreach ($produkListPesanan as $item) {
        $produk_id = $item['id'];
        $jumlah = $item['jumlah'];
        $size = $item['size'] ?? '';
        if ($jumlah > 0 && $produk_id) {
            if ($size) {
                $stmt = $conn->prepare("UPDATE produk_stock SET stok = stok - ? WHERE id_produk = ? AND size = ?");
                $stmt->bind_param("iis", $jumlah, $produk_id, $size);
            } else {
                $stmt = $conn->prepare("UPDATE produk_stock SET stok = stok - ? WHERE id_produk = ? AND (size IS NULL OR size = '')");
                $stmt->bind_param("ii", $jumlah, $produk_id);
            }
            $stmt->execute();
            $stmt->close();
        }
    }

    // Setelah selesai, redirect ke halaman daftar pesanan
    header("Location: pesanan.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pesanan Baru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../styles.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    /* Styling badge status, tombol, dan select2 agar tampilan rapi */
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
    .select2-container .select2-selection--single {
      height: 38px !important;
      padding: 6px 12px !important;
      font-size: 1rem !important;
      border-radius: 8px !important;
      border: 1px solid #ced4da !important;
      display: flex;
      align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 24px !important;
      font-size: 1rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 38px !important;
      right: 10px;
    }
    .breadcrumb-custom {
      display: flex;
      list-style: none;
      padding: 8px 15px;
      background-color: #f8f9fa;
      border-radius: 8px;
      font-size: 0.95rem;
      margin-bottom: 1rem;
    }
    .breadcrumb-custom li {
      margin-right: 8px;
    }
    .breadcrumb-custom li:not(:last-child)::after {
      content: "\203A";
      margin-left: 8px;
      color: #6c757d;
    }
    .breadcrumb-custom li:last-child::after {
      content: "";
      margin: 0;
    }
    .breadcrumb-custom a {
      text-decoration: none;
      color: #212529;
    }
    .breadcrumb-custom .active {
      color: #6c757d;
      pointer-events: none;
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <?php include '../sidebar.php'; ?>
    <div class="flex-grow-1 p-4">
      <h1>Pesanan</h1>
      <hr>
      <!-- Breadcrumb: List Pesanan > Pesanan Baru -->
      <div class="transaction-toolbar mb-3">
        <nav aria-label="breadcrumb">
          <ul class="breadcrumb-custom">
            <li><a href="pesanan.php">List Pesanan</a></li>
            <li class="active">Pesanan Baru</li>
          </ul>
        </nav>
      </div>
      <div class="card shadow-sm mb-4 w-100">
        <div class="card-body">
          <!-- Form input pesanan baru -->
          <form method="POST" action="pembayaran.php">
            <div class="mb-3">
              <label for="namaPelanggan" class="form-label">Nama Pelanggan</label>
              <input type="text" class="form-control" id="namaPelanggan" name="namaPelanggan" required>
            </div>
            <div class="mb-3">
              <label for="nomorTelepon" class="form-label">Nomor Telepon</label>
              <input type="text" class="form-control" id="nomorTelepon" name="nomorTelepon" required>
            </div>
            <div class="mb-3">
              <label for="sekolah" class="form-label">Sekolah</label>
              <input type="text" class="form-control" id="sekolah" name="sekolah" required>
            </div>
            <div class="mb-3">
              <label for="alamatSekolah" class="form-label">Alamat Sekolah</label>
              <textarea class="form-control" id="alamatSekolah" name="alamatSekolah" rows="2" required></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Produk yang Dipesan</label>
              <div id="produkInputs"></div>
              <button type="button" class="btn btn-light border text-black text-nowrap rounded w-100 mt-2" id="tambahProdukBtn">
                <i class="fas fa-plus"></i> Tambah Produk
              </button>
            </div>
            <div class="d-grid">
              <input type="hidden" name="produkListPesanan" id="produkListPesananInput">
              <button type="submit" class="btn btn-success">Lanjut</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Script untuk dynamic form produk dan perhitungan total -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    // Event untuk menampilkan/menyembunyikan detail cicilan
    const metodeBayarRadios = document.querySelectorAll('input[name="metodeBayar"]');
    const detailCicilanDiv = document.getElementById('detailCicilan');
    metodeBayarRadios.forEach(radio => {
      radio && radio.addEventListener('change', () => {
        detailCicilanDiv && detailCicilanDiv.classList.toggle('hidden', !document.getElementById('nyicil').checked);
      });
    });

    // Inisialisasi select2 dan event produk
    $(document).ready(function() {
      $('#produkSelect').select2({
        placeholder: "Cari produk...",
        allowClear: true
      });

      $('#produkSelect').on('change', function() {
        var produkId = $(this).val();
        if (!produkId) {
          $('#colVariasi').hide();
          $('#colJumlah').removeClass('col-md-3').addClass('col-md');
          $('#variasiSelect').html('<option value="">Pilih Variasi</option>');
          return;
        }
        $.get('get-variasi.php', { id_produk: produkId }, function(data) {
          if (data && data.length > 0) {
            var html = '<option value="">Pilih Variasi</option>';
            data.forEach(function(variasi) {
              html += `<option value="${variasi}">${variasi}</option>`;
            });
            $('#variasiSelect').html(html);
            $('#colVariasi').show();
            $('#colJumlah').removeClass('col-md').addClass('col-md-3');
          } else {
            $('#colVariasi').hide();
            $('#colJumlah').removeClass('col-md-3').addClass('col-md');
            $('#variasiSelect').html('<option value="">Pilih Variasi</option>');
          }
        }, 'json');
      });
    });

    // Variabel untuk menyimpan produk yang dipilih
    let produkListPesanan = [];
    let totalQty = 0;
    let totalHarga = 0;
    let produkIndex = 0;

    // Fungsi untuk membuat select produk
    function getProdukSelectHtml(idx) {
      let options = `<option value="">Pilih Produk...</option>`;
      <?php foreach ($produkList as $produk): ?>
        options += `<option value="<?= $produk['id_produk'] ?>" data-harga="<?= $produk['harga'] ?? 0 ?>">
          <?= htmlspecialchars($produk['nama_produk']) ?> (<?= $produk['kategori'] ?>)
        </option>`;
      <?php endforeach; ?>
      return `<select class="form-select produkSelect" name="produk_id[${idx}]" required>${options}</select>`;
    }

    // Fungsi untuk membuat select size
    function getSizeSelectHtml(idx) {
      return `<select class="form-select sizeSelect" name="size[${idx}]" style="display:none;">
        <option value="">Pilih Size</option>
      </select>`;
    }

    // Fungsi untuk membuat input jumlah
    function getJumlahInputHtml(idx) {
      return `<input type="number" class="form-control jumlahProduk" name="jumlah[${idx}]" min="1" placeholder="Jumlah" required>`;
    }

    // Fungsi untuk membuat satu baris input produk
    function getProdukInputRow(idx) {
      return `<div class="row g-2 mb-2 align-items-end produk-row" data-idx="${idx}">
        <div class="col-md-6">${getProdukSelectHtml(idx)}</div>
        <div class="col-md-3 size-col" style="display:none;">${getSizeSelectHtml(idx)}</div>
        <div class="col-md jumlah-col">${getJumlahInputHtml(idx)}</div>
      </div>`;
    }

    // Event handler untuk tambah produk
    $(document).ready(function() {
      tambahProdukInput();

      $('#tambahProdukBtn').on('click', function() {
        tambahProdukInput();
      });

      $('#produkInputs').on('click', '.btn-hapus-produk', function() {
        $(this).closest('.produk-row').remove();
        updateTotal();
      });

      // Event saat produk dipilih, tampilkan size jika ada
      $('#produkInputs').on('change', '.produkSelect', function() {
        let $row = $(this).closest('.produk-row');
        let produkId = $(this).val();
        let $sizeCol = $row.find('.size-col');
        let $sizeSelect = $row.find('.sizeSelect');
        let $jumlahInput = $row.find('.jumlahProduk');
        if (!produkId) {
          $sizeCol.hide();
          $sizeSelect.hide().html('<option value="">Pilih Size</option>');
          $jumlahInput.attr('max', '');
          $row.find('.jumlah-col').removeClass('col-md-3').addClass('col-md');
          updateTotal();
          return;
        }
        $.get('get-variasi.php', { id_produk: produkId }, function(data) {
          if (data && data.length > 0) {
            let html = '<option value="">Pilih Size</option>';
            data.forEach(function(size) {
              html += `<option value="${size}">${size}</option>`;
            });
            $sizeSelect.html(html).show();
            $sizeCol.show();
            $jumlahInput.attr('max', ''); // reset max, tunggu size dipilih
            $row.find('.jumlah-col').removeClass('col-md').addClass('col-md-3');
          } else {
            $sizeCol.hide();
            $sizeSelect.hide().html('<option value="">Pilih Size</option>');
            $row.find('.jumlah-col').removeClass('col-md-3').addClass('col-md');
            // Ambil stok produk tanpa size
            $.get('get-stok-produk.php', { id_produk: produkId }, function(res) {
              $jumlahInput.attr('max', res.stok || 0);
            }, 'json');
          }
          updateTotal();
        }, 'json');
      });

      // Saat size dipilih, ambil stok size tersebut
      $('#produkInputs').on('change', '.sizeSelect', function() {
        let $row = $(this).closest('.produk-row');
        let produkId = $row.find('.produkSelect').val();
        let size = $(this).val();
        let $jumlahInput = $row.find('.jumlahProduk');
        if (produkId && size) {
          $.get('get-stok-produk.php', { id_produk: produkId, size: size }, function(res) {
            $jumlahInput.attr('max', res.stok || 0);
          }, 'json');
        } else {
          $jumlahInput.attr('max', '');
        }
      });

      // Event saat jumlah diubah
      $('#produkInputs').on('input', '.jumlahProduk', function() {
        let max = parseInt($(this).attr('max')) || 0;
        let val = parseInt($(this).val()) || 0;
        if (max && val > max) {
          $(this).val(max);
        }
        updateTotal();
      });

      // Event saat size diubah
      $('#produkInputs').on('change', '.sizeSelect', function() {
        updateTotal();
      });

      // Saat form disubmit, simpan list produk ke input hidden
      $('form').on('submit', function() {
        $('#produkListPesananInput').val(JSON.stringify(getProdukListPesanan()));
      });
    });

    // Fungsi untuk menambah baris input produk baru
    function tambahProdukInput() {
      $('#produkInputs').append(getProdukInputRow(produkIndex));
      $('#produkInputs .produkSelect').last().select2({
        placeholder: "Cari produk...",
        allowClear: true
      });
      produkIndex++;
      updateTotal();
    }

    // Ambil list produk yang diinput user
    function getProdukListPesanan() {
      let list = [];
      $('#produkInputs .produk-row').each(function() {
        let produkId = $(this).find('.produkSelect').val();
        let produkNama = $(this).find('.produkSelect option:selected').text();
        let size = $(this).find('.sizeSelect').is(':visible') ? $(this).find('.sizeSelect').val() : '';
        let jumlah = parseInt($(this).find('.jumlahProduk').val()) || 0;
        let harga = parseInt($(this).find('.produkSelect option:selected').data('harga')) || 0;
        if (produkId && jumlah > 0) {
          list.push({id: produkId, nama: produkNama, size: size, jumlah: jumlah, harga: harga});
        }
      });
      return list;
    }

    // Hitung total qty dan harga semua produk
    function updateTotal() {
      let totalQty = 0;
      let totalHarga = 0;
      $('#produkInputs .produk-row').each(function() {
        let jumlah = parseInt($(this).find('.jumlahProduk').val()) || 0;
        let harga = parseInt($(this).find('.produkSelect option:selected').data('harga')) || 0;
        totalQty += jumlah;
        totalHarga += harga * jumlah;
      });
      $('#totalQty').text(totalQty);
      $('#totalHarga').text(totalHarga.toLocaleString());

      // Jika metode nyicil, hitung DP dan sisa bayar
      if ($('#nyicil').is(':checked')) {
        const dp = Math.round(totalHarga * 0.5);
        $('#nominalDp').val(dp);
        $('#sisaBayar').val(totalHarga - dp);
      }
    }

    // Update total jika metode bayar berubah
    $('input[name="metodeBayar"]').on('change', function() {
      updateTotal();
    });

    // Efek collapse di sidebar
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const logo = document.getElementById('sidebarLogo');

    if (sidebar && toggleBtn) {
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
    }
  </script>
</body>
</html>