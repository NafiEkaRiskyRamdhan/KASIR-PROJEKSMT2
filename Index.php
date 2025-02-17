<?php
session_start();
if (!isset($_SESSION['role']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';
$role = $_SESSION['role'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= $role; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .judul-toko {
            font-size: 2.5rem;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            background: linear-gradient(45deg, rgb(42, 39, 41), rgb(32, 39, 42));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 5px rgba(24, 24, 22, 0.77);
        }
        .nama-user {
            font-size: 1.2rem;
            font-weight: bold;
            color: rgb(57, 57, 51);
            text-shadow: 1px 1px 3px rgb(255, 255, 255);
        }
    </style>
</head>
<body style="background-color: rgb(104, 157, 159);">

    <div class="container mt-4">
        <h2 class="judul-toko">TOKO RAMA696</h2>
        <p class="text-center text-dark">
            Selamat datang, <span class="nama-user"><?= htmlspecialchars($username); ?></span> (<?= $role; ?>)
        </p>

        <div class="d-flex justify-content-between mb-3">
            <?php if ($role == 'petugas') : ?>
                <a href="tambah_produk.php" class="btn btn-primary">Tambah Produk</a>
                <a href="transaksi.php" class="btn btn-success">Tambah Transaksi</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <!-- Data Stok Barang -->
        <div class="card bg-light shadow p-3 mb-4">
            <div class="card-body">
                <h5 class="card-title text-dark">Stok Barang</h5>
                <table class="table table-bordered table-hover text-dark">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Kode Produk</th>
                            <th>Nama Produk</th>
                            <th>Stok</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM produk";
                        $result = mysqli_query($conn, $query);
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['kode_produk']); ?></td>
                            <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                            <td><?= htmlspecialchars($row['stok']); ?></td>
                            <td>Rp. <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($role == 'admin') : ?>
                                    <a href="update_produk.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <?php elseif ($role == 'petugas') : ?>
                                    <a href="update_produk.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="hapus_produk.php?id=<?= $row['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                       Hapus
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Data Transaksi -->
        <div class="card bg-light shadow p-3">
            <div class="card-body">
                <h5 class="card-title text-dark"><?= ($role == 'admin') ? 'Laporan Transaksi Petugas' : 'Transaksi Anda'; ?></h5>
                <table class="table table-bordered table-hover text-dark">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Kode Pembelian</th>
                            <th>Tanggal</th>
                            <th>Harga Jual</th>
                            <th>Harga Bayar</th>
                            <th>Kembalian</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_transaksi = "SELECT pembelian.*, user.username AS petugas 
                                            FROM pembelian 
                                            JOIN user ON pembelian.user_id = user.id";

                        if ($role == 'admin') {
                            $query_transaksi .= " WHERE user.role = 'petugas'";
                        } else {
                            $query_transaksi .= " WHERE user.username = '$username'";
                        }

                        $result_transaksi = mysqli_query($conn, $query_transaksi);
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result_transaksi)) { ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['kode_pembelian']); ?></td>
                            <td><?= htmlspecialchars($row['tanggal_pembelian']); ?></td>
                            <td>Rp. <?= number_format($row['harga_jual'], 0, ',', '.'); ?></td>
                            <td>Rp. <?= number_format($row['harga_bayar'], 0, ',', '.'); ?></td>
                            <td>Rp. <?= number_format($row['kembalian'], 0, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($row['petugas']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rekapitulasi Transaksi Bulanan (Hanya untuk Admin) -->
        <?php if ($role == 'admin') { ?>
        <div class="card bg-light shadow p-3 mt-4">
            <div class="card-body">
                <h5 class="card-title text-dark text-center">Rekapitulasi Transaksi Bulanan</h5>
                <table class="table table-bordered text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>Bulan</th>
                            <th>Total Penjualan</th>
                            <th>Total Pembayaran</th>
                            <th>Total Kembalian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT DATE_FORMAT(tanggal_pembelian, '%Y-%m') AS bulan, 
                                        SUM(harga_jual) AS total_penjualan, 
                                        SUM(harga_bayar) AS total_pembayaran, 
                                        SUM(kembalian) AS total_kembalian
                                 FROM pembelian
                                 GROUP BY bulan
                                 ORDER BY bulan DESC";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= date('F Y', strtotime($row['bulan'])) ?></td>
                            <td>Rp. <?= number_format($row['total_penjualan'], 0, ',', '.') ?></td>
                            <td>Rp. <?= number_format($row['total_pembayaran'], 0, ',', '.') ?></td>
                            <td>Rp. <?= number_format($row['total_kembalian'], 0, ',', '.') ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php } ?>

    </div>
</body>
</html>
