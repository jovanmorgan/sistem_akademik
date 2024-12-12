<?php
include '../../../../keamanan/koneksi.php';

date_default_timezone_set('Asia/Makassar');

$id_admin = $_POST['id_admin'];
$type = $_POST['type'];
$jumlah_uang = $_POST['jumlah_uang'];
$kategori_transaksi = $_POST['kategori_transaksi'];
$kategori_lainnya = $_POST['kategori_lainnya'];
$deskripsi = $_POST['deskripsi'];

$tanggal_transaksi_input = date('Y-m-d H:i:s');

if (empty($id_admin) || empty($type) || empty($jumlah_uang) || empty($deskripsi)) {
    echo "data_tidak_lengkap";
    exit();
}

if ($kategori_transaksi === 'lainnya') {
    $kategori_transaksi = $kategori_lainnya;
}

$tanggal_obj = new DateTime($tanggal_transaksi_input);
$hari = $tanggal_obj->format('l');
$hari_indonesia = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];
$nama_hari = $hari_indonesia[$hari];

$jam = $tanggal_obj->format('h:i:s');
$jam_24 = $tanggal_obj->format('H');

if ($jam_24 >= 0 && $jam_24 < 12) {
    $waktu = 'Pagi';
} elseif ($jam_24 >= 12 && $jam_24 < 16) {
    $waktu = 'Siang';
} elseif ($jam_24 >= 16 && $jam_24 < 19) {
    $waktu = 'Sore';
} else {
    $waktu = 'Malam';
}

$tanggal_transaksi = $nama_hari . ', ' . $tanggal_obj->format('d-F-Y') . " $jam $waktu";

// Ambil saldo sebelumnya dari tabel saldo
$result_saldo = mysqli_query($koneksi, "SELECT saldo FROM saldo WHERE id_admin = '$id_admin'");
$saldo_sebelumnya = 0;

if ($result_saldo && mysqli_num_rows($result_saldo) > 0) {
    $row_saldo = mysqli_fetch_assoc($result_saldo);
    $saldo_sebelumnya = $row_saldo['saldo'];
}

// Cek jika type adalah pengeluaran dan saldo tidak cukup
if ($type === 'pengeluaran' && $saldo_sebelumnya < $jumlah_uang) {
    echo "saldo_tidak_cukup";
    exit();
}

// Buat query SQL untuk menambahkan data ke dalam tabel transaksi, termasuk saldo_sebelumnya
$query_transaksi = "INSERT INTO transaksi (id_admin, type, jumlah_uang, kategori_transaksi, deskripsi, tanggal_transaksi, saldo_sebelumnya) 
                    VALUES ('$id_admin', '$type', '$jumlah_uang', '$kategori_transaksi', '$deskripsi', '$tanggal_transaksi', '$saldo_sebelumnya')";

if (mysqli_query($koneksi, $query_transaksi)) {
    $id_transaksi = mysqli_insert_id($koneksi);

    if ($type === 'pengeluaran') {
        $query_pengeluaran = "INSERT INTO pengeluaran (id_transaksi, id_admin, status) 
                              VALUES ('$id_transaksi', '$id_admin', 'belum dilihat')";
        if (!mysqli_query($koneksi, $query_pengeluaran)) {
            echo "error_pengeluaran";
            exit();
        }

        $query_saldo = "UPDATE saldo SET saldo = saldo - $jumlah_uang WHERE id_admin = '$id_admin'";
    } elseif ($type === 'pemasukan') {
        $query_pemasukan = "INSERT INTO pemasukan (id_transaksi, id_admin, status) 
                            VALUES ('$id_transaksi', '$id_admin', 'belum dilihat')";
        if (!mysqli_query($koneksi, $query_pemasukan)) {
            echo "error_pemasukan";
            exit();
        }

        $query_saldo = "UPDATE saldo SET saldo = saldo + $jumlah_uang WHERE id_admin = '$id_admin'";
    }

    if (mysqli_num_rows($result_saldo) === 0) {
        $query_insert_saldo = "INSERT INTO saldo (id_admin, saldo) VALUES ('$id_admin', 0)";
        if (!mysqli_query($koneksi, $query_insert_saldo)) {
            echo "error_insert_saldo";
            exit();
        }
        mysqli_query($koneksi, $query_saldo);
    } else {
        mysqli_query($koneksi, $query_saldo);
    }

    echo "transaksi_sukses";
} else {
    echo "error_transaksi";
}

mysqli_close($koneksi);
