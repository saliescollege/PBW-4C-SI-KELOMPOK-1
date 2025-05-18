<?php
session_start();


// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";    
$db   = "db_uniform";


$conn = new mysqli($host, $user, $pass, $db);


// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


// Jika form disubmit
$errMsg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $created_at = date("Y-m-d H:i:s");


    // Cek apakah username sudah ada
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
   
    if ($result->num_rows > 0) {
        $errMsg = "Username sudah digunakan!";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);


        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password_hash, $created_at);
       
        if ($stmt->execute()) {
            // Redirect setelah berhasil registrasi
            header("Location: lengkapiprofil.php");
            exit;
        } else {
            $errMsg = "Gagal menyimpan data.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun</title>
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
                    <h3 class="mb-4 text-center">Registrasi</h3>

                    <?php if (!empty($errMsg)): ?>
                        <div class="alert alert-danger"><?php echo $errMsg; ?></div>
                    <?php endif; ?>

                     <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label for="new-username" class="form-label">Username</label>
                            <input type="text" id="new-username" name="username" class="form-control" placeholder="Masukkan username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email" required>
                        </div>
                        <div class="mb-3">
                            <label for="new-password" class="form-label">Password</label>
                            <input type="password" id="new-password" name="password" class="form-control" placeholder="Buat password" required>
                        </div>
                      <button type="button" class="btn custom-btn w-100" onclick="window.location.href='lengkapiprofil.php'">Daftar</button>

                    </form>
                    <div class="text-center mt-3">
                        <p>Sudah punya akun? <a href="login.html" class="text-decoration-none">Login di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>