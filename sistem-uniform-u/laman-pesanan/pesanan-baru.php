?php
session_start();
include '../koneksi.php';

// Ambil semua produk
$produkList = [];
$sql = "SELECT p.id_produk, p.nama_produk, p.kategori, p.harga FROM produk p"; // tambahkan p.harga
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $produkList[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pelanggan = $_POST['namaPelanggan'] ?? '';
    $nomor_telepon = $_POST['nomorTelepon'] ?? '';
    $sekolah = $_POST['sekolah'] ?? '';
    $alamat_sekolah = $_POST['alamatSekolah'] ?? '';
    $metode_bayar = $_POST['metodeBayar'] ?? '';
    $tanggal_pesanan = date('Y-m-d');
    $total_harga = 0; // Hitung sesuai kebutuhan
    $status_pembayaran = ($metode_bayar === 'lunas') ? 'Lunas' : 'Belum Lunas';

    $stmt = $conn->prepare("INSERT INTO pesanan (nama_pelanggan, nomor_telepon, sekolah, alamat_sekolah, metode_bayar, tanggal_pesanan, total_harga, status_pembayaran) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssis", $nama_pelanggan, $nomor_telepon, $sekolah, $alamat_sekolah, $metode_bayar, $tanggal_pesanan, $total_harga, $status_pembayaran);
    $stmt->execute();

    // Setelah insert pesanan, update stok produk
    $jumlah = $_POST['jumlah'] ?? 0;
    $produk_id = $_POST['produk_id'] ?? 0;
    $variasi = $_POST['variasi'] ?? '';

    if ($jumlah > 0 && $produk_id) {
        if ($variasi) {
            $stmt = $conn->prepare("UPDATE product_stock SET stok = stok - ? WHERE id_produk = ? AND size = ?");
            $stmt->bind_param("iis", $jumlah, $produk_id, $variasi);
        } else {
            $stmt = $conn->prepare("UPDATE product_stock SET stok = stok - ? WHERE id_produk = ? AND (size IS NULL OR size = '')");
            $stmt->bind_param("ii", $jumlah, $produk_id);
        }
        $stmt->execute();
    }

    // Redirect ke pesanan.php setelah insert
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
    /* Select2 agar sama tinggi dengan input Bootstrap */
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
  </style>
</head>
<body>
  <div class="d-flex">
    <?php include '../sidebar.php'; ?>
    <div class="flex-grow-1 p-4">
      <h1>Pesanan</h1>
      <hr>
      <div class="transaction-toolbar">
        <h6 class="mb-1">
          List Pesanan <span class="text-muted">&gt; Pesanan Baru</span>
        </h6>
      </div>
      <div class="card shadow-sm mb-4 w-100">
        <div class="card-body">
          <form method="POST" action="">
            <!-- Informasi Umum -->
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
            <!-- Produk yang Dipesan -->
            <div class="mb-3">
              <label class="form-label">Produk yang Dipesan</label>
              <div id="produkInputs"></div>
              <button type="button" class="btn btn-outline-primary rounded w-100 mt-2" id="tambahProdukBtn">
                <i class="fas fa-plus"></i> Tambah Produk
              </button>
            </div>
            <!-- Total -->
            <div class="mt-2">
              <hr class="my-2">
              <div class="d-flex justify-content-between">
                <span><strong>Total QTY</strong></span>
                <span id="totalQty">0</span>
              </div>
              <div class="d-flex justify-content-between">
                <span><strong>Total Harga</strong></span>
                <span>Rp <span id="totalHarga">0</span></span>
              </div>
              <hr class="my-2">
            </div>
            <!-- Metode Pembayaran -->
            <div class="mb-3">
              <label class="form-label">Pembayaran</label><br>
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
                <label for="nominalDp" class="form-label">Nominal DP (50%)</label>
                <input type="number" class="form-control" id="nominalDp" name="nominalDp" readonly>
              </div>
              <div class="mb-2">
                <label for="sisaBayar" class="form-label">Nominal Sisa yang Harus Dibayar (50%)</label>
                <input type="number" class="form-control" id="sisaBayar" name="sisaBayar" readonly>
              </div>
              <div class="mb-2">
                <label for="tanggalDp" class="form-label">Tanggal Pembayaran DP</label>
                <input type="date" class="form-control" id="tanggalDp" name="tanggalDp">
              </div>
              <div class="mb-2">
                <label for="tanggalJatuhTempo" class="form-label">Tanggal Jatuh Tempo</label>
                <input type="date" class="form-control" id="tanggalJatuhTempo" name="tanggalJatuhTempo">
              </div>
              <div class="mb-2">
                <label for="metodeBayarCicilan" class="form-label">Metode Pembayaran</label>
                <select class="form-select" id="metodeBayarCicilan" name="metodeBayarCicilan">
                  <option value="transfer">Transfer Bank</option>
                  <option value="tunai">Tunai</option>
                  <option value="qris">QRIS</option>
                </select>
              </div>
            </div>
            <!-- Tombol Submit -->
            <div class="d-grid">
              <button type="submit" class="btn btn-success">Buat Pesanan</button>
            </div>
            <input type="hidden" name="produkListPesanan" id="produkListPesananInput">
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- JavaScript -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    // Toggle detail cicilan
    const metodeBayarRadios = document.querySelectorAll('input[name="metodeBayar"]');
    const detailCicilanDiv = document.getElementById('detailCicilan');
    metodeBayarRadios.forEach(radio => {
      radio.addEventListener('change', () => {
        detailCicilanDiv.classList.toggle('hidden', !document.getElementById('nyicil').checked);
      });
    });

    // Select2 produk
    $(document).ready(function() {
      $('#produkSelect').select2({
        placeholder: "Cari produk...",
        allowClear: true
      });

      // AJAX ambil variasi
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

    // Data produk yang ditambahkan
    let produkListPesanan = [];
    let totalQty = 0;
    let totalHarga = 0;
    let produkIndex = 0;

    function getProdukSelectHtml(idx) {
      let options = `<option value="">Pilih Produk...</option>`;
      <?php foreach ($produkList as $produk): ?>
        options += `<option value="<?= $produk['id_produk'] ?>" data-harga="<?= $produk['harga'] ?? 0 ?>">
          <?= htmlspecialchars($produk['nama_produk']) ?> (<?= $produk['kategori'] ?>)
        </option>`;
      <?php endforeach; ?>
      return `<select class="form-select produkSelect" name="produk_id[${idx}]" required>${options}</select>`;
    }

    function getVariasiSelectHtml(idx) {
      return `<select class="form-select variasiSelect" name="variasi[${idx}]" style="display:none;">
        <option value="">Pilih Variasi</option>
      </select>`;
    }

    function getJumlahInputHtml(idx) {
      return `<input type="number" class="form-control jumlahProduk" name="jumlah[${idx}]" min="1" placeholder="Jumlah" required>`;
    }

    function getProdukInputRow(idx) {
      return `<div class="row g-2 mb-2 align-items-end produk-row" data-idx="${idx}">
        <div class="col-md-6">${getProdukSelectHtml(idx)}</div>
        <div class="col-md-3 variasi-col" style="display:none;">${getVariasiSelectHtml(idx)}</div>
        <div class="col-md jumlah-col">${getJumlahInputHtml(idx)}</div>
      </div>`;
    }

    // Tambah produk input pertama saat halaman load
    $(document).ready(function() {
      tambahProdukInput();

      // Tambah produk input baru
      $('#tambahProdukBtn').on('click', function() {
        tambahProdukInput();
      });

      // Hapus baris produk
      $('#produkInputs').on('click', '.btn-hapus-produk', function() {
        $(this).closest('.produk-row').remove();
        updateTotal();
      });

      // Event produk select change
      $('#produkInputs').on('change', '.produkSelect', function() {
        let $row = $(this).closest('.produk-row');
        let produkId = $(this).val();
        let $variasiCol = $row.find('.variasi-col');
        let $variasiSelect = $row.find('.variasiSelect');
        if (!produkId) {
          $variasiCol.hide();
          $variasiSelect.hide().html('<option value="">Pilih Variasi</option>');
          $row.find('.jumlah-col').removeClass('col-md-3').addClass('col-md');
          updateTotal();
          return;
        }
        $.get('get-variasi.php', { id_produk: produkId }, function(data) {
          if (data && data.length > 0) {
            let html = '<option value="">Pilih Variasi</option>';
            data.forEach(function(variasi) {
              html += `<option value="${variasi}">${variasi}</option>`;
            });
            $variasiSelect.html(html).show();
            $variasiCol.show();
            $row.find('.jumlah-col').removeClass('col-md').addClass('col-md-3');
          } else {
            $variasiCol.hide();
            $variasiSelect.hide().html('<option value="">Pilih Variasi</option>');
            $row.find('.jumlah-col').removeClass('col-md-3').addClass('col-md');
          }
          updateTotal();
        }, 'json');
      });

      // Event jumlah input change
      $('#produkInputs').on('input', '.jumlahProduk', function() {
        updateTotal();
      });

      // Event variasi select change
      $('#produkInputs').on('change', '.variasiSelect', function() {
        updateTotal();
      });

      // Saat submit, serialisasi produk ke hidden input
      $('form').on('submit', function() {
        $('#produkListPesananInput').val(JSON.stringify(getProdukListPesanan()));
      });
    });

    // Fungsi tambah input produk
    function tambahProdukInput() {
      $('#produkInputs').append(getProdukInputRow(produkIndex));
      // Inisialisasi select2 pada produk select yang baru
      $('#produkInputs .produkSelect').last().select2({
        placeholder: "Cari produk...",
        allowClear: true
      });
      produkIndex++;
      updateTotal();
    }

    // Ambil data produk dari input
    function getProdukListPesanan() {
      let list = [];
      $('#produkInputs .produk-row').each(function() {
        let produkId = $(this).find('.produkSelect').val();
        let produkNama = $(this).find('.produkSelect option:selected').text();
        let variasi = $(this).find('.variasiSelect').is(':visible') ? $(this).find('.variasiSelect').val() : '';
        let jumlah = parseInt($(this).find('.jumlahProduk').val()) || 0;
        let harga = parseInt($(this).find('.produkSelect option:selected').data('harga')) || 0;
        if (produkId && jumlah > 0) {
          list.push({id: produkId, nama: produkNama, variasi: variasi, jumlah: jumlah, harga: harga});
        }
      });
      return list;
    }

    // Update total QTY dan harga
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

      // Update DP & Sisa jika nyicil
      if ($('#nyicil').is(':checked')) {
        const dp = Math.round(totalHarga * 0.5);
        $('#nominalDp').val(dp);
        $('#sisaBayar').val(totalHarga - dp);
      }
    }

    $('input[name="metodeBayar"]').on('change', function() {
      updateTotal();
    });
  </script>
</body>
</html>