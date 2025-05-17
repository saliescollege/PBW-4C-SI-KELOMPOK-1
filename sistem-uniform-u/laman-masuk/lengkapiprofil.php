<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lengkapi Profil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../styles.css">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-lg">
                <div class="card-body">
                    <img src="../assets/Logo Uniform-U.png" class="logo mx-auto d-block mb-3" alt="Logo">
          <h3 class="mb-4 text-center">Lengkapi Profil Anda</h3>
          <form id="profileForm">
            <div class="mb-3">
              <label for="full_name" class="form-label">Nama Lengkap</label>
              <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="mb-3">
              <label for="phone_number" class="form-label">Nomor HP</label>
              <input type="text" id="phone_number" name="phone_number" class="form-control" placeholder="Contoh: 08123456789" required>
            </div>
            <div class="mb-3">
              <label for="address" class="form-label">Alamat</label>
              <textarea id="address" name="address" class="form-control" rows="3" placeholder="Masukkan alamat lengkap" required></textarea>
            </div>
            <div class="mb-3">
              <label for="gender" class="form-label">Jenis Kelamin</label>
              <select id="gender" name="gender" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="birth_date" class="form-label">Tanggal Lahir</label>
              <input type="date" id="birth_date" name="birth_date" class="form-control" required>
            </div>
            <div class="text-danger mb-3" id="error-msg" style="display: none;">
              Gagal menyimpan data, silakan coba lagi.
            </div>
            <div class="text-success mb-3" id="success-msg" style="display: none;">
              Profil berhasil disimpan!
            </div>
            <button type="submit" class="btn custom-btn w-100">
              <span id="loading" class="spinner-border spinner-border-sm d-none"></span>
              Simpan Profil
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const loading = document.getElementById('loading');
    const successMsg = document.getElementById('success-msg');
    const errorMsg = document.getElementById('error-msg');

    loading.classList.remove('d-none');
    successMsg.style.display = 'none';
    errorMsg.style.display = 'none';

    
    const profileData = {
      full_name: document.getElementById('full_name').value,
      phone_number: document.getElementById('phone_number').value,
      address: document.getElementById('address').value,
      gender: document.getElementById('gender').value,
      birth_date: document.getElementById('birth_date').value
    };

    
    setTimeout(() => {
      loading.classList.add('d-none');
      successMsg.style.display = 'block';
      document.getElementById('profileForm').reset();
    }, 1000);

    
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
