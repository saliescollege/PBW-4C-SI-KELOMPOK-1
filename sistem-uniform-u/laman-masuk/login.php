<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Akun</title>
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
                    <h3 class="mb-4 text-center">Login</h3>
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" class="form-control" placeholder="Masukkan username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" class="form-control" placeholder="Masukkan password" required>
                        </div>
                        <div class="mb-3 text-danger" id="error-msg" style="display: none;">
                            Username atau password salah!
                        </div>
                        <button type="submit" class="btn custom-btn w-100">
                            <span id="loading" class="spinner-border spinner-border-sm d-none"></span>
                            Login
                        </button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="laman-masuk\reset-password.html" class="text-decoration-none">Lupa Password?</a>
                    </div>
                    <div class="text-center mt-2">
                        <p>Belum punya akun? <a href="register.php" class="text-decoration-none">Daftar di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../laman-masuk/login.js"></script>

</body>
</html>