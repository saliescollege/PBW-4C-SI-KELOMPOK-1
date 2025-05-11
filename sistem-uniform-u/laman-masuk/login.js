document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Mencegah reload halaman

    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;
    let errorMsg = document.getElementById("error-msg");
    let loading = document.getElementById("loading");

    // Simulasi loading sebelum validasi
    loading.classList.remove("d-none");

    setTimeout(() => {
        loading.classList.add("d-none");

        if (username === "admin" && password === "admin123") {
            window.location.href = "dashboard.html"; // Redirect ke dashboard jika sukses
        } else {
            errorMsg.style.display = "block"; // Tampilkan pesan error jika login gagal
        }
    }, 1500); // Simulasi loading 1.5 detik
});
