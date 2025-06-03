<?php
session_start();
include '../koneksi.php';
include '../config.php';

$user_id = $_SESSION['user_id'] ?? null;
$profile = null;

if ($user_id) {
    $sql = "SELECT * FROM user_profile WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $profile = mysqli_fetch_assoc($result);
}
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


  <div class="flex-grow-1 p-4">
    <h2>Profil Anda</h2>

    <!-- Card Form -->
    <div class="card shadow-lg">
      <div class="card-body">
    <?php if ($profile): ?>
      <form id="profileForm" action="<?= $base_url ?>laman-masuk/lengkapiprofil.php" method="POST">
        <div class="mb-3">
          <label for="full_name" class="form-label">Nama Lengkap</label>
          <input type="text" id="full_name" name="full_name" class="form-control" required
            value="<?= htmlspecialchars($profile['full_name']) ?>" disabled/>
        </div>
        <div class="mb-3">
          <label for="phone_number" class="form-label">Nomor HP</label>
          <input type="text" id="phone_number" name="phone_number" class="form-control" required
            value="<?= htmlspecialchars($profile['phone_number']) ?>"  disabled/>
        </div>
        <div class="mb-3">
          <label for="address" class="form-label">Alamat</label>
          <textarea id="address" name="address" class="form-control" rows="3" required  disabled><?= htmlspecialchars($profile['address']) ?></textarea>
        </div>
        <div class="mb-3">
          <label for="gender" class="form-label">Jenis Kelamin</label>
          <select id="gender" name="gender" class="form-select" required  disabled>
            <option value="">-- Pilih --</option>
            <option value="Laki-laki" <?= $profile['gender'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="Perempuan" <?= $profile['gender'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="birth_date" class="form-label">Tanggal Lahir</label>
          <input type="date" id="birth_date" name="birth_date" class="form-control" required
            value="<?= htmlspecialchars($profile['birth_date']) ?>"  disabled/>
        </div>
        <div class="d-flex gap-2">
          <button type="button" id="editBtn" class="btn btn-primary">Edit Profil</button>
          <button type="submit" id="saveBtn" class="btn btn-success d-none">Simpan Perubahan</button>
          <a href="<?= $base_url ?>laman-user/logout.php" class="btn btn-danger ms-auto">Logout</a>
        </div>
      </form>
    <?php else: ?>
      <p>Anda belum memiliki akun, silahkan buat akun terlebih dahulu <a href="<?= $base_url ?>laman-masuk/register.php">di sini</a>.</p>
    <?php endif; ?>
  </div>
</div>
</div>

<script>
  const editBtn = document.getElementById('editBtn');
  const saveBtn = document.getElementById('saveBtn');
  const formInputs = document.querySelectorAll('#profileForm input, #profileForm textarea, #profileForm select');

  editBtn.addEventListener('click', () => {
    formInputs.forEach(input => input.disabled = false);
    saveBtn.classList.remove('d-none');
    editBtn.classList.add('d-none');
  });
</script>

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