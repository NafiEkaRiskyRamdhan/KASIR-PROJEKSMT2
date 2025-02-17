<?php
session_start();
include 'koneksi.php'; 
$kode = date('YmdHis');

$query = "INSERT INTO pembelian (kode, user_id, tanggal, bayar, total, kembali) 
          VALUES ('$kode', '{$_SESSION['id']}', NOW(), 0, 0, 0)";

$eksekusi = mysqli_query($conn, $query);

if ($eksekusi) {

    $query = "SELECT id FROM pembelian ORDER BY id DESC LIMIT 1";
    $eksekusi = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($eksekusi);
    
    $_SESSION['pembelian_id'] = $row['id']; 
    
    header("Location: pembelian_form.php"); 
    exit();
} else {
    echo "Gagal membuat transaksi: " . mysqli_error($conn);
}
?>