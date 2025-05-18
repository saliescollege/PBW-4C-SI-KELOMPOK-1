<?php
session_start();
include '../koneksi.php';
// Mendapatkan protocol (http/https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";

// Mendapatkan host (localhost atau domain)
$host = $_SERVER['HTTP_HOST'];

// Ubah sesuai nama folder project di htdocs
$projectFolder = '/sistem-uniform-u';

// Gabungkan jadi base URL
$base_url = $protocol . '://' . $host . $projectFolder . '/';

$user_id = $_SESSION['user_id'];


// Ambil data user_profile dari database
$sql = "SELECT * FROM user_profile WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$profile = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="d-flex" style="min-height: 100vh;">
  <!-- Sidebar -->
  <?php include '../sidebar.php'; ?>


  <!-- Konten utama -->
  <div class="flex-grow-1 p-4">
    <h2>Profil Anda</h2>
    <?php if ($profile): ?>
      <form action="register.php" method="post">
        <div class="mb-3">
          <label for="full_name" class="form-label">Nama Lengkap</label>
          <input type="text" id="full_name" name="full_name" class="form-control" required
            value="<?= htmlspecialchars($profile['full_name']) ?>" />
        </div>


        <div class="mb-3">
          <label for="phone_number" class="form-label">Nomor HP</label>
          <input type="text" id="phone_number" name="phone_number" class="form-control" required
            value="<?= htmlspecialchars($profile['phone_number']) ?>" />
        </div>


        <div class="mb-3">
          <label for="address" class="form-label">Alamat</label>
          <textarea id="address" name="address" class="form-control" rows="3" required><?= htmlspecialchars($profile['address']) ?></textarea>
        </div>


        <div class="mb-3">
          <label for="gender" class="form-label">Jenis Kelamin</label>
          <select id="gender" name="gender" class="form-select" required>
            <option value="">-- Pilih --</option>
            <option value="Laki-laki" <?= $profile['gender'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="Perempuan" <?= $profile['gender'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
          </select>
        </div>


        <div class="mb-3">
          <label for="birth_date" class="form-label">Tanggal Lahir</label>
          <input type="date" id="birth_date" name="birth_date" class="form-control" required
            value="<?= htmlspecialchars($profile['birth_date']) ?>" />
        </div>


        <a href="<?= $base_url ?>laman-masuk/login.php" class="btn btn-danger">Logout</a>
      </form>
    <?php else: ?>
      <p>Profil Anda belum lengkap. Silakan lengkapi terlebih dahulu <a href="lengkapiprofile.php">di sini</a>.</p>
    <?php endif; ?>
  </div>
</div>


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
</script>

</body>
</html>
