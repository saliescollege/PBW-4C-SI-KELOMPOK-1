<?php
include '../koneksi.php';
include '../config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    // Cek apakah user ada
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
        $update->bind_param("ss", $hashedPassword, $username);
        $update->execute();

        $success = "Password berhasil diubah. <a href='login.php'>Login sekarang</a>";
    } else {
        $error = "Username tidak ditemukan.";
    }
}
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
                    <h3 class="mb-4 text-center">Reset Password</h3>
                   <form method="POST" action="">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required>
    </div>
    <div class="mb-3">
        <label for="new_password" class="form-label">Password Baru</label>
        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Masukkan password baru" required>
    </div>
    <div class="mb-3">
        <label for="confirm_password" class="form-label">Ulangi Password Baru</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Ulangi password baru" required>
    </div>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <button type="submit" class="btn custom-btn w-100">Reset Password</button>
</form>

                    <div class="text-center mt-3">
                        <a href="login.php" class="text-decoration-none">Kembali ke Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>