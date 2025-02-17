<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    echo "ID produk tidak ditemukan!";
    exit();
}

$id = $_GET['id'];
$query = "DELETE FROM produk WHERE id = $id";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('Produk berhasil dihapus!'); window.location='index.php';</script>";
} else {
    echo "Gagal menghapus produk: " . mysqli_error($conn);
}
?>
