<?php
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
$stmt = $conn->query("
    SELECT p.id_produk, p.nama_produk, p.kategori, p.warna, p.harga, ps.size, ps.stok
    FROM produk p
    LEFT JOIN produk_stock ps ON p.id_produk = ps.id_produk
    ORDER BY p.id_produk, FIELD(ps.size, 'XS', 'S', 'M', 'L', 'XL')
");
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
    <title>Update Stok Semua Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .size-inputs { display: flex; gap: 10px; flex-wrap: wrap; }
        .size-inputs > div { min-width: 70px; }
    </style>
</head>
<body class="p-4">
<div class="container">
    <h2 class="mb-4">Update Stok</h2>

    <?php if (isset($message)): ?>
        <div class="alert <?= strpos($message, 'Gagal') === false ? 'alert-success' : 'alert-danger' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="" id="stokForm">
        <?php foreach ($produk_list as $produk): ?>
            <div class="mb-4 border p-3 rounded" data-produk-id="<?= $produk['id_produk'] ?>">
                <h4><?= htmlspecialchars($produk['nama_produk']) ?> (ID: <?= $produk['id_produk'] ?>)</h4>
                <p>
                    <strong>Kategori:</strong> <?= htmlspecialchars($produk['kategori']) ?> |
                    <strong>Warna:</strong> <?= htmlspecialchars($produk['warna']) ?> |
                    <strong>Harga:</strong> Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
                </p>

                <!-- Pilihan punya ukuran atau tidak -->
                <div class="mb-3">
                    <label>
                        <input type="radio" name="punya_ukuran_<?= $produk['id_produk'] ?>" value="1" <?= $produk['punya_ukuran'] ? 'checked' : '' ?>>
                        Dengan Ukuran
                    </label>
                    <label class="ms-3">
                        <input type="radio" name="punya_ukuran_<?= $produk['id_produk'] ?>" value="0" <?= !$produk['punya_ukuran'] ? 'checked' : '' ?>>
                        Tanpa Ukuran
                    </label>
                </div>

                <!-- Input stok dengan ukuran -->
                <div class="size-inputs stok-ukuran" style="<?= $produk['punya_ukuran'] ? 'display:flex;' : 'display:none;' ?>">
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

                <!-- Input stok tanpa ukuran -->
                <div class="stok-no-ukuran" style="<?= !$produk['punya_ukuran'] ? 'display:block;' : 'display:none;' ?>">
                    <label for="stok_NO_SIZE_<?= $produk['id_produk'] ?>" class="form-label">Stok</label>
                    <input type="number" min="0" class="form-control"
                           id="stok_NO_SIZE_<?= $produk['id_produk'] ?>"
                           name="stok[<?= $produk['id_produk'] ?>][NO_SIZE]"
                           value="<?= $produk['stok']['NO_SIZE'] ?? 0 ?>" />
                </div>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary mt-3">Update Semua Stok</button>
        <a href="produk.php" class="btn btn-secondary mt-3 ms-2">Kembali ke Produk</a>
    </form>
</div>

<script>
    document.querySelectorAll('div[data-produk-id]').forEach(div => {
        const id = div.getAttribute('data-produk-id');
        const radioUkuran = div.querySelectorAll(`input[name="punya_ukuran_${id}"]`);
        const stokUkuran = div.querySelector('.stok-ukuran');
        const stokNoUkuran = div.querySelector('.stok-no-ukuran');

        function updateView() {
            const pilih = div.querySelector(`input[name="punya_ukuran_${id}"]:checked`).value;
            if (pilih === '1') {
                stokUkuran.style.display = 'flex';
                stokNoUkuran.style.display = 'none';
            } else {
                stokUkuran.style.display = 'none';
                stokNoUkuran.style.display = 'block';
            }
        }

        radioUkuran.forEach(r => r.addEventListener('change', updateView));
        updateView(); // inisialisasi tampilan
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
