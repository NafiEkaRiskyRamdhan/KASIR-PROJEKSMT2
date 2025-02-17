<?php
include 'koneksi.php';

$query = "SELECT * FROM produk";
$eksekusi = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Produk</title>
</head>
<body>
    <h2>Data Produk</h2>
    <a href="tambah_produk.php">[Tambah Produk]</a>
    <br><br>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Kode Produk</th>
            <th>Nama Produk</th>
            <th>Stok</th>
            <th>Harga</th>
            <th>Aksi</th>
        </tr>

        <?php 
        $no = 1;
        while ($row = mysqli_fetch_assoc($eksekusi)) { ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $row['kode_produk']; ?></td>
            <td><?php echo $row['nama_produk']; ?></td>
            <td><?php echo $row['stok']; ?></td>
            <td>Rp. <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
            <td>
                <a href="edit_produk.php?id=<?php echo $row['id']; ?>">Edit</a> |
                <a href="hapus_produk.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus produk ini?');">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>