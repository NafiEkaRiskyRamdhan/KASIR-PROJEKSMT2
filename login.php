<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {   
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // Gunakan md5 sesuai database (bisa diganti dengan password_hash)

    // Cek username & password di database
    $query = "SELECT * FROM user WHERE username='$username' AND password='$password'";
    $eksekusi = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($eksekusi);
    $jumlah_data = mysqli_num_rows($eksekusi);

    if ($jumlah_data == 1) {
        $_SESSION['id'] = $data['id'];
        $_SESSION['username'] = $data['username']; // Simpan username, bukan nama
        $_SESSION['role'] = $data['role'];

        header("Location: index.php");
        exit();
    } else {
        $error = "Login gagal, periksa kembali username & password!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg" style="width: 400px;">
        <div class="card-header bg-primary text-white text-center">
            <h3>Login</h3>
        </div>
        <div class="card-body">
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger text-center">
                    <?= $error; ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
                </div>

                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
