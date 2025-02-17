<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || !isset($_GET['id'])) {
    echo "Akses ditolak!";
    exit();
}

$role = $_SESSION['role']; // Ambil role user
$id = $_GET['id'];

// Ambil data produk berdasarkan ID
$query = "SELECT * FROM produk WHERE id = $id";
$eksekusi = mysqli_query($conn, $query);

if (!$eksekusi) {
    die("Query error: " . mysqli_error($conn));
}

$produk = mysqli_fetch_assoc($eksekusi);

if (!$produk) {
    die("Produk dengan ID $id tidak ditemukan!");
}

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];

    if ($role == 'admin') {
        // Admin hanya bisa mengubah nama dan harga, tidak stok
        $update_query = "UPDATE produk SET nama_produk = '$nama_produk', harga = $harga WHERE id = $id";
    } else {
        // Petugas bisa mengubah semua (nama, stok, harga)
        $stok = $_POST['stok'];
        $update_query = "UPDATE produk SET nama_produk = '$nama_produk', stok = $stok, harga = $harga WHERE id = $id";
    }

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Produk berhasil diperbarui!'); window.location='index.php';</script>";
    } else {
        echo "Gagal memperbarui produk: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>Update Produk</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Produk:</label>
                    <input type="text" name="nama_produk" value="<?php echo htmlspecialchars($produk['nama_produk']); ?>" class="form-control" required>
                </div>
                <?php if ($role == 'petugas') : ?>
                <div class="mb-3">
                    <label class="form-label">Stok:</label>
                    <input type="number" name="stok" value="<?php echo htmlspecialchars($produk['stok']); ?>" class="form-control" required>
                </div>
                <?php endif; ?>
                <div class="mb-3">
                    <label class="form-label">Harga:</label>
                    <input type="number" name="harga" value="<?php echo htmlspecialchars($produk['harga']); ?>" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
