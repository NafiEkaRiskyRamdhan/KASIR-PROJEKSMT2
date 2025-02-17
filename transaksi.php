<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Cek apakah user sudah memiliki transaksi berjalan
if (!isset($_SESSION['id_pembelian'])) {
    $kode = date('YmdHis');
    $query = "INSERT INTO pembelian (kode_pembelian, user_id, tanggal_pembelian, harga_jual, harga_bayar, kembalian) 
              VALUES ('$kode', '$user_id', NOW(), 0, 0, 0)";
    $eksekusi = mysqli_query($conn, $query);

    if ($eksekusi) {
        $_SESSION['id_pembelian'] = mysqli_insert_id($conn);
    } else {
        die("<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>");
    }
}

$id_pembelian = $_SESSION['id_pembelian'];

// Jika tombol bayar ditekan
if (isset($_POST['bayar'])) {
    $bayar = intval($_POST['uang_bayar']);
    $total_harga = 0;

    foreach ($_POST['produk'] as $index => $produk_id) {
        $produk_id = intval($produk_id);
        $jumlah = intval($_POST['jumlah'][$index]);

        // Ambil harga produk
        $query_harga = "SELECT harga, stok FROM produk WHERE id = '$produk_id'";
        $result_harga = mysqli_query($conn, $query_harga);
        $produk = mysqli_fetch_assoc($result_harga);

        if ($produk && $produk['stok'] >= $jumlah) {
            $harga_produk = $produk['harga'];
            $sub_total = $harga_produk * $jumlah;
            $total_harga += $sub_total;

            // Tambahkan produk ke tabel detail_pembelian
            $query_detail = "INSERT INTO detail_pembelian (pembelian_id, produk_id, jumlah, harga) 
                             VALUES ('$id_pembelian', '$produk_id', '$jumlah', '$sub_total')";
            mysqli_query($conn, $query_detail);

            // Kurangi stok produk
            $query_update_stok = "UPDATE produk SET stok = stok - '$jumlah' WHERE id = '$produk_id'";
            mysqli_query($conn, $query_update_stok);
        } else {
            echo "<div class='alert alert-danger text-center'>Stok tidak mencukupi untuk salah satu produk!</div>";
            exit();
        }
    }

    if ($bayar >= $total_harga) {
        $kembalian = $bayar - $total_harga;

        // Update total harga di tabel pembelian
        $query_update_pembelian = "UPDATE pembelian 
                                   SET harga_jual = '$total_harga', 
                                       harga_bayar = '$bayar', 
                                       kembalian = '$kembalian' 
                                   WHERE id = '$id_pembelian'";
        mysqli_query($conn, $query_update_pembelian);

        // Simpan ID transaksi terakhir untuk struk
        $_SESSION['last_id_pembelian'] = $id_pembelian;

        // Reset transaksi setelah pembayaran sukses
        unset($_SESSION['id_pembelian']);

        echo "<div class='alert alert-success text-center'>Pembayaran berhasil! Kembalian: Rp. " . number_format($kembalian, 0, ',', '.') . "</div>";
        echo "<meta http-equiv='refresh' content='2;url=struk.php'>"; // Redirect ke struk.php
        exit();
    } else {
        echo "<div class='alert alert-danger text-center'>Uang tidak cukup!</div>";
    }
} 
?>

<!DOCTYPE html>
<html lang="id">  
<head>
    <meta charset="UTF-8">
    <title>Transaksi Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function updateTotal() {
            let totalHarga = 0;
            document.querySelectorAll('.produk-row').forEach(row => {
                let harga = parseInt(row.querySelector('.produk-option').selectedOptions[0].dataset.harga || 0);
                let jumlah = parseInt(row.querySelector('.jumlah-input').value || 0);
                totalHarga += harga * jumlah;
            });
            document.getElementById('total-harga').innerText = "Total: Rp. " + totalHarga.toLocaleString('id-ID');
        }

        function tambahProduk() {
            let container = document.getElementById('produk-container');
            let produkHTML = `
                <div class="row g-3 mb-2 produk-row">
                    <div class="col-md-6">
                        <select name="produk[]" class="form-select produk-option" onchange="updateTotal()" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php
                            $query_produk = "SELECT * FROM produk";
                            $result_produk = mysqli_query($conn, $query_produk);
                            while ($row_produk = mysqli_fetch_assoc($result_produk)) {
                                echo "<option value='{$row_produk['id']}' data-harga='{$row_produk['harga']}'>{$row_produk['nama_produk']} - Rp. " . number_format($row_produk['harga'], 0, ',', '.') . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" value="1" oninput="updateTotal()" required>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-danger" onclick="this.parentElement.parentElement.remove(); updateTotal();">Hapus</button>
                    </div>
                </div>`;
            container.innerHTML += produkHTML;
        }
    </script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center text-primary mb-4">Transaksi Pembelian</h2>
    <div class="card shadow p-4">
        <div class="card-body">
            <h5 class="card-title">Pilih Produk</h5>
            <form method="POST">
                <div id="produk-container">
                    <div class="row g-3 mb-2 produk-row">
                        <div class="col-md-6">
                            <select name="produk[]" class="form-select produk-option" onchange="updateTotal()" required>
                                <option value="">-- Pilih Produk --</option>
                                <?php
                                $query_produk = "SELECT * FROM produk";
                                $result_produk = mysqli_query($conn, $query_produk);
                                while ($row_produk = mysqli_fetch_assoc($result_produk)) {
                                    echo "<option value='{$row_produk['id']}' data-harga='{$row_produk['harga']}'>{$row_produk['nama_produk']} - Rp. " . number_format($row_produk['harga'], 0, ',', '.') . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" value="1" oninput="updateTotal()" required>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-3">
                    <button type="button" class="btn btn-primary" onclick="tambahProduk()">Tambah Produk</button>
                </div>

                <h4 class="text-center" id="total-harga">Total: Rp. 0</h4>

                <div class="col-md-12">
                    <label class="form-label">Uang Dibayar</label>
                    <input type="number" name="uang_bayar" class="form-control" required placeholder="Masukkan jumlah pembayaran">
                </div>

                <div class="col-md-12 text-center mt-3">
                    <button type="submit" name="bayar" class="btn btn-success">Bayar</button>
                </div>
            </form>
        </div>
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>
</div>
</body>
</html>
