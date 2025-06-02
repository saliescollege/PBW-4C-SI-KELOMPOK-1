<?php
session_start();
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'db_uniform';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $conn->real_escape_string($_POST['productName'] ?? '');
    $kategori = $conn->real_escape_string($_POST['productCategory'] ?? '');
    $gender = $conn->real_escape_string($_POST['productGender'] ?? '');
    $harga = (int) ($_POST['productPrice'] ?? 0);
    $hasSize = isset($_POST['hasSize']) ? (int)$_POST['hasSize'] : 1;

    $sizes = [];
    if ($hasSize === 1) {
        // stok per size
        $sizes = [
            'XS' => (int)($_POST['stok_xs'] ?? 0),
            'S'  => (int)($_POST['stok_s'] ?? 0),
            'M'  => (int)($_POST['stok_m'] ?? 0),
            'L'  => (int)($_POST['stok_l'] ?? 0),
            'XL' => (int)($_POST['stok_xl'] ?? 0),
        ];
    } else {
        // stok tanpa size
        $sizes = [
            'NO_SIZE' => (int)($_POST['stok_nosize'] ?? 0),
        ];
    }

    $gambar = null;
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['productImage']['tmp_name'];
        $name = basename($_FILES['productImage']['name']);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (in_array($ext, $allowed)) {
            $newName = uniqid('produk_') . '.' . $ext;
            $uploadDir = '../assets/uniform/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $uploadPath = $uploadDir . $newName;
        if (move_uploaded_file($tmp_name, $uploadPath)) {
              $gambar = $newName; // hanya nama file
            } else {
                echo "<script>alert('Gagal upload gambar');</script>";
            }
        } else {
            echo "<script>alert('Format gambar tidak diizinkan');</script>";
        }
    }

    $sql = "INSERT INTO produk (nama_produk, kategori, jenis_kelamin, harga, gambar_produk) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssds", $nama, $kategori, $gender, $harga, $gambar);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $id_produk = $stmt->insert_id;
        $stmt_stock = $conn->prepare("INSERT INTO produk_stock (id_produk, size, stok) VALUES (?, ?, ?)");
        foreach ($sizes as $size => $stok) {
            if ($stok > 0) {
                $stmt_stock->bind_param("isi", $id_produk, $size, $stok);
                $stmt_stock->execute();
            }
        }
        $stmt_stock->close();
        $stmt->close();
        $conn->close();

        header("Location: produk.php?msg=success");
        exit;
    } else {
        echo "<script>alert('Gagal tambah produk');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tambah Produk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .breadcrumb-container {
      margin-bottom: 1rem;
    }

    .bg-light-pink {
      background-color: #ffe6e6 !important;
    }

    .breadcrumb-custom {
      display: flex;
      list-style: none;
      padding: 8px 15px;
      background-color: #f8f9fa;
      border-radius: 8px;
      font-size: 0.9rem;
    }

    .breadcrumb-custom li {
      margin-right: 8px;
    }

    .breadcrumb-custom li:not(:last-child)::after {
      content: "\203A"; /* tanda panah kecil (›) */
      margin-left: 8px;
      color: #6c757d;
    }

    .breadcrumb-custom li:last-child::after {
      content: "";
      margin: 0;
    }

    .breadcrumb-custom a {
      text-decoration: none;
      color:rgb(1, 1, 1);
    }

    .breadcrumb-custom .active {
      color: #6c757d;
      pointer-events: none;
    }

  </style>
</head>
<body>
  <div class="d-flex">
    <?php include '../sidebar.php'; ?> <!-- Sidebar -->

    <!-- Ganti .container menjadi .flex-grow-1 p-4 agar tidak ada margin kiri-kanan -->
    <div class="flex-grow-1 p-4">
      <h1>Produk</h1>
      <!-- Breadcrumbs -->
      <nav aria-label="breadcrumb">
        <ul class="breadcrumb-custom" id="breadcrumb">
          <li><a href="produk.php">List Produk</a></li>
          <li id="add_produk"><a href="#">Tambah Produk</a></li>
        </ul>
      </nav>
      <!-- Card Form -->
      <div class="card shadow-lg">
        <div class="card-body">
          <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="productName" class="form-label">Nama Produk</label>
              <input type="text" class="form-control" id="productName" name="productName" required />
            </div>

            <div class="mb-3">
              <label for="productCategory" class="form-label">Kategori</label>
              <select class="form-select" id="productCategory" name="productCategory" required>
                <option selected disabled> </option>
                <option value="SD">SD</option>
                <option value="SMP">SMP</option>
                <option value="SMA">SMA</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="productGender" class="form-label">Jenis Kelamin</label>
              <select class="form-select" id="productGender" name="productGender" required>
                <option selected disabled> </option>
                <option value="Pria">Pria</option>
                <option value="Wanita">Wanita</option>
                <option value="Unisex">Unisex</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="productPrice" class="form-label">Harga</label>
              <input type="number" class="form-control" id="productPrice" name="productPrice" required />
            </div>

            <div class="mb-3">
              <label class="form-label">Apakah produk memiliki ukuran?</label>
              <div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="hasSize" id="hasSizeYes" value="1" checked />
                  <label class="form-check-label" for="hasSizeYes">Ya</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="hasSize" id="hasSizeNo" value="0" />
                  <label class="form-check-label" for="hasSizeNo">Tidak</label>
                </div>
              </div>
            </div>

            <div id="stokWithSize" class="mb-3">
              <label class="form-label">Stok per Ukuran</label>
              <div class="row g-2">
                <div class="col"><input type="number" min="0" class="form-control" name="stok_xs" placeholder="XS" /></div>
                <div class="col"><input type="number" min="0" class="form-control" name="stok_s" placeholder="S" /></div>
                <div class="col"><input type="number" min="0" class="form-control" name="stok_m" placeholder="M" /></div>
                <div class="col"><input type="number" min="0" class="form-control" name="stok_l" placeholder="L" /></div>
                <div class="col"><input type="number" min="0" class="form-control" name="stok_xl" placeholder="XL" /></div>
              </div>
            </div>

            <div id="stokNoSize" class="mb-3" style="display:none;">
              <label class="form-label">Stok</label>
              <input type="number" min="0" class="form-control" name="stok_nosize"/>
            </div>

            <div class="mb-3">
              <label for="productImage" class="form-label">Upload Gambar Produk</label>
              <input class="form-control" type="file" id="productImage" name="productImage" accept=".jpg,.jpeg,.png,.gif" />
            </div>

            <button type="submit" class="btn btn-light border text-black text-nowrap">
              <i class="fas fa-save me-1"></i> Simpan
            </button>
            <a href="produk.php" class="btn btn-secondary ms-2">Batal</a>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Efek Collapse di Sidebar -->
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

  <script>
    const hasSizeYes = document.getElementById('hasSizeYes');
    const hasSizeNo = document.getElementById('hasSizeNo');
    const stokWithSize = document.getElementById('stokWithSize');
    const stokNoSize = document.getElementById('stokNoSize');

    function toggleStokInput() {
      if (hasSizeYes.checked) {
        stokWithSize.style.display = 'block';
        stokNoSize.style.display = 'none';
      } else {
        stokWithSize.style.display = 'none';
        stokNoSize.style.display = 'block';
      }
    }

    hasSizeYes.addEventListener('change', toggleStokInput);
    hasSizeNo.addEventListener('change', toggleStokInput);
    toggleStokInput();

    // Breadcrumb otomatis berdasarkan URL
    document.addEventListener('DOMContentLoaded', function () {
      const url = window.location.href;
      const addProdukBreadcrumb = document.getElementById('add_produk');
      if (!addProdukBreadcrumb) return; // Cegah error jika id tidak ditemukan

      if (url.includes('add_produk.php')) {
        addProdukBreadcrumb.style.display = 'inline';
      } else {
        addProdukBreadcrumb.style.display = 'none';
      }
    });
  </script>
</body>
</html>