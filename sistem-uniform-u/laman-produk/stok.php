<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Update Stok Produk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .size-stock { display: none; }
  </style>
</head>
<body class="p-4">

  <div class="container">
    <h2 class="mb-4">Update Stok Produk</h2>
    
    <form id="updateStockForm">
      <div class="mb-3">
        <label for="product" class="form-label">Pilih Produk</label>
        <select id="product" class="form-select" required>
          <option value="" disabled selected>-- Pilih Produk --</option>
          <option value="seragam" data-has-size="true">Seragam Sekolah</option>
          <option value="atribut" data-has-size="false">Topi</option>
          <option value="atribut2" data-has-size="false">Tas</option>
          <!-- Tambahkan produk lain sesuai data nyata -->
        </select>
      </div>

      <!-- Input stok per size, hanya tampil jika produk punya size -->
      <div id="sizeStockInputs" class="size-stock mb-3">
        <label class="form-label">Stok per Ukuran</label>
        <div class="row g-2">
          <div class="col">
            <input type="number" min="0" name="stok_s" class="form-control" placeholder="Stok S" />
          </div>
          <div class="col">
            <input type="number" min="0" name="stok_m" class="form-control" placeholder="Stok M" />
          </div>
          <div class="col">
            <input type="number" min="0" name="stok_l" class="form-control" placeholder="Stok L" />
          </div>
          <div class="col">
            <input type="number" min="0" name="stok_xl" class="form-control" placeholder="Stok XL" />
          </div>
        </div>
      </div>

      <!-- Input stok total, hanya tampil jika produk tidak punya size -->
      <div id="totalStockInput" class="mb-3" style="display:none;">
        <label for="stok_total" class="form-label">Stok Total</label>
        <input type="number" min="0" id="stok_total" name="stok_total" class="form-control" placeholder="Jumlah stok total" />
      </div>

      <button type="submit" class="btn btn-primary">Update Stok</button>
    </form>
  </div>

  <script>
    const productSelect = document.getElementById('product');
    const sizeStockInputs = document.getElementById('sizeStockInputs');
    const totalStockInput = document.getElementById('totalStockInput');

    productSelect.addEventListener('change', () => {
      const selectedOption = productSelect.options[productSelect.selectedIndex];
      const hasSize = selectedOption.getAttribute('data-has-size') === 'true';

      if (hasSize) {
        sizeStockInputs.style.display = 'block';
        totalStockInput.style.display = 'none';
      } else {
        sizeStockInputs.style.display = 'none';
        totalStockInput.style.display = 'block';
      }
    });

    // Optional: Form submit handler
    document.getElementById('updateStockForm').addEventListener('submit', function(e) {
      e.preventDefault();

      // Ambil data stok sesuai pilihan
      const selectedOption = productSelect.options[productSelect.selectedIndex];
      const hasSize = selectedOption.getAttribute('data-has-size') === 'true';

      let stokData = {};
      if (hasSize) {
        stokData = {
          stok_s: this.stok_s.value || 0,
          stok_m: this.stok_m.value || 0,
          stok_l: this.stok_l.value || 0,
          stok_xl: this.stok_xl.value || 0
        };
      } else {
        stokData = {
          stok_total: this.stok_total.value || 0
        };
      }

      // Contoh: Kirim data via AJAX/fetch ke backend (tidak disertakan di sini)
      console.log('Update stok produk:', productSelect.value, stokData);

      alert('Stok berhasil diupdate (simulasi).');
      this.reset();
      sizeStockInputs.style.display = 'none';
      totalStockInput.style.display = 'none';
      productSelect.value = '';
    });
  </script>

</body>
</html>
