<?php
include '../../../../keamanan/koneksi.php';

// Terima ID transaksi yang akan dihapus
$id_transaksi = $_POST['id'];

// Validasi input
if (empty($id_transaksi)) {
    echo "data_tidak_lengkap";
    exit();
}

// Ambil detail transaksi untuk mendapatkan jumlah_uang dan tipe transaksi
$query_get_transaksi = "SELECT type, jumlah_uang, id_admin FROM transaksi WHERE id_transaksi = '$id_transaksi'";
$result_transaksi = mysqli_query($koneksi, $query_get_transaksi);

if ($result_transaksi && mysqli_num_rows($result_transaksi) > 0) {
    $transaksi = mysqli_fetch_assoc($result_transaksi);
    $type = $transaksi['type'];
    $jumlah_uang = $transaksi['jumlah_uang'];
    $id_admin = $transaksi['id_admin'];

    // Update saldo berdasarkan tipe transaksi
    if ($type === 'pemasukan') {
        $query_update_saldo = "UPDATE saldo SET saldo = saldo - $jumlah_uang WHERE id_admin = '$id_admin'";
        $query_delete_type = "DELETE FROM pemasukan WHERE id_transaksi = '$id_transaksi' AND id_admin = '$id_admin'";
    } elseif ($type === 'pengeluaran') {
        $query_update_saldo = "UPDATE saldo SET saldo = saldo + $jumlah_uang WHERE id_admin = '$id_admin'";
        $query_delete_type = "DELETE FROM pengeluaran WHERE id_transaksi = '$id_transaksi' AND id_admin = '$id_admin'";
    } else {
        echo "tipe_transaksi_tidak_valid";
        exit();
    }

    // Lakukan update saldo
    if (!mysqli_query($koneksi, $query_update_saldo)) {
        echo "error_update_saldo";
        exit();
    }

    // Hapus dari tabel pemasukan atau pengeluaran sesuai tipe transaksi
    if (!mysqli_query($koneksi, $query_delete_type)) {
        echo "error_delete_type";
        exit();
    }

    // Hapus transaksi dari tabel transaksi
    $query_delete_transaksi = "DELETE FROM transaksi WHERE id_transaksi = '$id_transaksi'";
    if (mysqli_query($koneksi, $query_delete_transaksi)) {
        echo "success";
    } else {
        echo "error_delete_transaksi";
    }
} else {
    echo "transaksi_tidak_ditemukan";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
