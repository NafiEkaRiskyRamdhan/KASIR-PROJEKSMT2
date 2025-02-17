<?php
session_start();
include 'koneksi.php';

// Cek apakah ada transaksi yang bisa ditampilkan
if (!isset($_SESSION['last_id_pembelian'])) {
    die("<div class='alert alert-danger text-center'>Tidak ada transaksi yang dapat ditampilkan!</div>");
}

$id_pembelian = $_SESSION['last_id_pembelian'];

// Ambil data transaksi dari database
$query_pembelian = "SELECT * FROM pembelian WHERE id = '$id_pembelian'";
$eksekusi_pembelian = mysqli_query($conn, $query_pembelian);
$pembelian = mysqli_fetch_assoc($eksekusi_pembelian);

// Jika transaksi tidak ditemukan, tampilkan error
if (!$pembelian) {
    die("<div class='alert alert-danger text-center'>Transaksi tidak ditemukan di database!</div>");
}

// Ambil detail produk yang dibeli
$query_detail = "SELECT dp.*, p.nama_produk 
                 FROM detail_pembelian dp 
                 JOIN produk p ON dp.produk_id = p.id 
                 WHERE dp.pembelian_id = '$id_pembelian'";
$eksekusi_detail = mysqli_query($conn, $query_detail);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .struk-container {
            width: 350px;
            margin: auto;
            padding: 20px;
            border: 1px dashed #000;
            background: #fff;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="struk-container">
        <h4 class="text-center">Struk Pembelian</h4>
        <hr>
        <p><strong>Kode Transaksi:</strong> <?php echo $pembelian['kode_pembelian']; ?></p>
        <p><strong>Tanggal:</strong> <?php echo $pembelian['tanggal_pembelian']; ?></p>
        <hr>

        <table class="table table-borderless">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($eksekusi_detail)) { ?>
                    <tr>
                        <td><?php echo $row['nama_produk']; ?></td>
                        <td><?php echo $row['jumlah']; ?></td>
                        <td>Rp. <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <hr>
        <p><strong>Total:</strong> Rp. <?php echo number_format($pembelian['harga_jual'], 0, ',', '.'); ?></p>
        <p><strong>Dibayar:</strong> Rp. <?php echo number_format($pembelian['harga_bayar'], 0, ',', '.'); ?></p>
        <p><strong>Kembalian:</strong> Rp. <?php echo number_format($pembelian['kembalian'], 0, ',', '.'); ?></p>
        <hr>

        <p class="text-center">Terima Kasih!</p>
        <button class="btn btn-primary w-100" onclick="window.print()">Cetak Struk</button>
    </div>

    <div class="text-center mt-4">
    <a href="index.php" class="btn btn-danger">Keluar</a>
</div>

</div>

</body>
</html>
