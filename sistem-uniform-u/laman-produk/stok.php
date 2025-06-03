<?php
session_start();
// koneksi database
$host = 'localhost';
$dbname = 'db_uniform';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Ambil ID produk dari query string
$id_produk = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id_produk) {
    // Tampilkan pesan error atau redirect
    exit('Produk tidak ditemukan.');
}

// Proses update stok jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stok'])) {
    try {
        $conn->beginTransaction();

        // Ambil info punya ukuran / tidak dari form
        $punya_ukuran_form = [];
        foreach ($_POST as $key => $val) {
            if (strpos($key, 'punya_ukuran_') === 0) {
                $id_produk = str_replace('punya_ukuran_', '', $key);
                $punya_ukuran_form[$id_produk] = $val;
            }
        }

        foreach ($_POST['stok'] as $id_produk => $sizes) {
            $punya_ukuran = $punya_ukuran_form[$id_produk] ?? '1';

            if ($punya_ukuran === '0') {
                // Jika tanpa ukuran: hapus semua stok ukuran dulu
                $stmtDel = $conn->prepare("DELETE FROM produk_stock WHERE id_produk = ?");
                $stmtDel->execute([$id_produk]);

                // Simpan stok NO_SIZE saja (harus ada 1 input stok[produk][NO_SIZE])
                $stok_no_size = intval($sizes['NO_SIZE'] ?? 0);

                $stmtIns = $conn->prepare("INSERT INTO produk_stock (id_produk, size, stok) VALUES (?, NULL, ?)");
                $stmtIns->execute([$id_produk, $stok_no_size]);
            } else {
                // Jika dengan ukuran: update/insert stok per size
                foreach ($sizes as $size => $stok) {
                    $stok = intval($stok);
                    $size = $size === 'NO_SIZE' ? NULL : strtoupper(trim($size));

                    // cek ada stok untuk produk & ukuran tersebut
                    $stmt = $conn->prepare("SELECT id FROM produk_stock WHERE id_produk = ? AND size <=> ?");
                    $stmt->execute([$id_produk, $size]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        // update stok existing
                        $stmtUpdate = $conn->prepare("UPDATE produk_stock SET stok = ? WHERE id = ?");
                        $stmtUpdate->execute([$stok, $row['id']]);
                    } else {
                        // insert stok baru
                        $stmtInsert = $conn->prepare("INSERT INTO produk_stock (id_produk, size, stok) VALUES (?, ?, ?)");
                        $stmtInsert->execute([$id_produk, $size, $stok]);
                    }
                }

                // Pastikan hapus stok NO_SIZE jika ada, karena sekarang pakai ukuran
                $stmtDelNoSize = $conn->prepare("DELETE FROM produk_stock WHERE id_produk = ? AND size IS NULL");
                $stmtDelNoSize->execute([$id_produk]);
            }
        }

        $conn->commit();
        $message = "Semua stok berhasil diperbarui.";
    } catch (Exception $e) {
        $conn->rollBack();
        $message = "Gagal update stok: " . $e->getMessage();
    }
}

// Ambil data produk dan stok
$stmt = $conn->prepare("
    SELECT p.id_produk, p.nama_produk, p.kategori, p.warna, p.harga, ps.size, ps.stok
    FROM produk p
    LEFT JOIN produk_stock ps ON p.id_produk = ps.id_produk
    WHERE p.id_produk = ?
    ORDER BY FIELD(ps.size, 'XS', 'S', 'M', 'L', 'XL')
");
$stmt->execute([$id_produk]);
$produk_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kelompokkan data stok per produk dan cek punya ukuran
$produk_list = [];
foreach ($produk_data as $row) {
    $id = $row['id_produk'];
    if (!isset($produk_list[$id])) {
        $produk_list[$id] = [
            'id_produk' => $id,
            'nama_produk' => $row['nama_produk'],
            'kategori' => $row['kategori'],
            'warna' => $row['warna'],
            'harga' => $row['harga'],
            'stok' => [],
            'punya_ukuran' => false,
        ];
    }
    $size = $row['size'] ?? 'NO_SIZE';
    $produk_list[$id]['stok'][$size] = $row['stok'] ?? 0;

    if ($size !== 'NO_SIZE') {
        $produk_list[$id]['punya_ukuran'] = true;
    }
}

// Ukuran standar
$sizes = ['XS', 'S', 'M', 'L', 'XL'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Update Stok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .size-inputs { display: flex; gap: 10px; flex-wrap: wrap; }
        .size-inputs > div { min-width: 70px; }
        .breadcrumb-custom {
            display: flex;
            list-style: none;
            padding: 8px 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .breadcrumb-custom li { margin-right: 8px; }
        .breadcrumb-custom li:not(:last-child)::after {
            content: "\203A";
            margin-left: 8px;
            color: #6c757d;
        }
        .breadcrumb-custom li:last-child::after { content: ""; margin: 0; }
        .breadcrumb-custom a { text-decoration: none; color:rgb(1, 1, 1); }
        .breadcrumb-custom .active { color: #6c757d; pointer-events: none; }

        /* Badge kategori sama seperti produk.php */
        .badge.tag-sd {
            border-color: #f5c2cb;
            background-color: pink;
            color: red;
        }
        .badge.tag-smp {
            border-color: #8bdbed;
            background-color: #e6f9ff;
            color: navy;
        }
        .badge.tag-sma {
            border-color: #d0d0e1;
            background-color: #e6edf4;
            color: #393737;
        }
    </style>
</head>

<div class="d-flex">
<?php include '../sidebar.php'; ?>

<div class="flex-grow-1 p-4">
    <h1>Produk</h1>
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb-custom" id="breadcrumb">
            <li><a href="produk.php">List Produk</a></li>
            <li class="active">Update Stok</li>
        </ul>
    </nav>

    <div class="card shadow-lg">
      <div class="card-body">
        <?php if (isset($message)): ?>
            <div class="alert <?= strpos($message, 'Gagal') === false ? 'alert-success' : 'alert-danger' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="" id="stokForm">
            <?php foreach ($produk_list as $produk): ?>
                <div class="mb-4 p-3 rounded" data-produk-id="<?= $produk['id_produk'] ?>">
                    <h4 class="mb-1"><?= htmlspecialchars($produk['nama_produk']) ?></h4>
                    <div class="mb-2 ps-1">
                        <span class="d-block mb-1">#<?= $produk['id_produk'] ?></span>
                        <span class="d-block mb-1">
                            Kategori:
                            <?php
                                $kategori = strtolower($produk['kategori']);
                                $badgeClass = '';
                                if ($kategori === 'sd') $badgeClass = 'tag-sd';
                                elseif ($kategori === 'smp') $badgeClass = 'tag-smp';
                                elseif ($kategori === 'sma') $badgeClass = 'tag-sma';
                            ?>
                            <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-2" style="border:1px solid; font-size:0.95rem;">
                                <?= htmlspecialchars($produk['kategori']) ?>
                            </span>
                        </span>
                        <span class="d-block">
                            Harga: Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
                        </span>
                    </div>

                    <?php if ($produk['punya_ukuran']): ?>
                        <!-- Input stok dengan ukuran -->
                        <div class="size-inputs stok-ukuran">
                            <?php foreach ($sizes as $size): ?>
                                <div>
                                    <label for="stok_<?= $size ?>_<?= $produk['id_produk'] ?>" class="form-label"><?= $size ?></label>
                                    <input type="number" min="0" class="form-control"
                                           id="stok_<?= $size ?>_<?= $produk['id_produk'] ?>"
                                           name="stok[<?= $produk['id_produk'] ?>][<?= $size ?>]"
                                           value="<?= $produk['stok'][$size] ?? 0 ?>" />
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Input stok tanpa ukuran -->
                        <div class="stok-no-ukuran">
                            <label for="stok_NO_SIZE_<?= $produk['id_produk'] ?>" class="form-label">Stok</label>
                            <input type="number" min="0" class="form-control"
                                   id="stok_NO_SIZE_<?= $produk['id_produk'] ?>"
                                   name="stok[<?= $produk['id_produk'] ?>][NO_SIZE]"
                                   value="<?= $produk['stok']['NO_SIZE'] ?? 0 ?>" />
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-light border text-black text-nowrap mt-3">
                <i class="fas fa-save me-1"></i> Kirim
            </button>
        </form>
      </div>
    </div>
</div>
</div>

<script>
    // Efek Collapse di Sidebar
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
