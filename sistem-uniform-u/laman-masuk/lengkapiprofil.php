<?php
session_start();


// KONFIGURASI KONEKSI DATABASE
$servername = "localhost";
$db_username = "root";      
$db_password = "";          
$dbname = "db_uniform";        


$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $birth_date = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $updated_at = date('Y-m-d H:i:s');


    $cek = mysqli_query($conn, "SELECT * FROM user_profile WHERE user_id = '$user_id'");
    if (mysqli_num_rows($cek) > 0) {
        // update
        $query = "UPDATE user_profile SET
                    full_name = '$full_name',
                    phone_number = '$phone_number',
                    address = '$address',
                    gender = '$gender',
                    birth_date = '$birth_date',
                    updated_at = '$updated_at'
                  WHERE user_id = '$user_id'";
    } else {
        // insert
        $query = "INSERT INTO user_profile
                    (user_id, full_name, phone_number, address, gender, birth_date, updated_at)
                  VALUES
                    ('$user_id', '$full_name', '$phone_number', '$address', '$gender', '$birth_date', '$updated_at')";
    }


    if (mysqli_query($conn, $query)) {
        header("Location: ../laman-user/user-profile.php");
        exit();
    } else {
        $error_message = "Gagal menyimpan data, silakan coba lagi.";
    }
}
?>

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

            <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
          <?php endif; ?>

          <form method="POST" id="profileForm">
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

<!-- <script>
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

    
  }); -->
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
