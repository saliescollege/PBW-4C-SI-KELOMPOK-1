<?php
include '../config.php'; 
include '../koneksi.php';

$username = $_SESSION['username'] ?? '';
$email = $_SESSION['email'] ?? '';
?>

<div class="d-flex">
  <!-- Sidebar -->
  <div id="sidebar" class="sidebar bg-white shadow-sm">
    <div class="sidebar-header d-flex justify-content-between px-3 py-3 border-bottom">
      <img src="<?= $base_url ?>assets/Logo Uniform-U.png" alt="Logo" class="sidebar-logo" id="sidebarLogo">
      <i class="fas fa-bars toggle-btn" id="toggleSidebar"></i>
    </div>

    <ul class="nav flex-column mt-3">
      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>laman-dashboard/dashboard.php">
          <i class="fas fa-home me-2"></i><span class="sidebar-text">Beranda</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>laman-produk/produk.php">
          <i class="fas fa-tshirt me-2"></i><span class="sidebar-text">Produk</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>laman-pesanan/pesanan.php">
          <i class="fas fa-clipboard-list me-2"></i><span class="sidebar-text">Pesanan</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>laman-laporan/laporan.php">
          <i class="fas fa-chart-line me-2"></i><span class="sidebar-text">Laporan Penjualan</span>
        </a>
      </li>
    </ul>

    <!-- Profile Pengguna -->
<a href="<?= $base_url ?>laman-user/user-profile.php" class="sidebar-footer d-flex align-items-center border-top mt-auto py-2 px-3 text-decoration-none text-dark">
<img src="<?= $base_url ?>assets/PFP.png" class="rounded-circle me-2" width="32" height="32" alt="User">
  <div class="user-info">
    <div class="fw-semibold sidebar-text">
      <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Tamu' ?>
    </div>
    <small class="text-muted sidebar-text">
      <?= isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '@gmail.com' ?>
    </small>
  </div>
</a>

  </div>
</div>