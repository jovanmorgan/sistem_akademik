<?php
include '../../../../keamanan/koneksi.php';

date_default_timezone_set('Asia/Makassar');

// Ambil data dari form edit
$id_transaksi = $_POST['id_transaksi'];
$id_admin = $_POST['id_admin'];
$type_edit = $_POST['type_edit'];
$jumlah_uang = $_POST['jumlah_uang'];
$kategori_transaksi = $_POST['kategori_transaksi'];
$kategori_lainnya = $_POST['kategori_lainnya'];
$deskripsi = $_POST['deskripsi'];
$tanggal_transaksi_input = $_POST['tanggal_transaksi'];

// Validasi input
if (empty($id_transaksi) || empty($id_admin) || empty($type_edit) || empty($jumlah_uang) || empty($deskripsi)) {
    echo "data_tidak_lengkap";
    exit();
}

// Tentukan kategori transaksi jika memilih 'lainnya'
if ($kategori_transaksi === 'lainnya') {
    $kategori_transaksi = $kategori_lainnya;
}

// Format tanggal transaksi
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
$waktu = ($jam_24 < 12) ? 'Pagi' : (($jam_24 < 16) ? 'Siang' : (($jam_24 < 19) ? 'Sore' : 'Malam'));

$tanggal_transaksi = $nama_hari . ', ' . $tanggal_obj->format('d-F-Y') . " $jam $waktu";

// Ambil saldo sebelumnya
$result_saldo = mysqli_query($koneksi, "SELECT saldo FROM saldo WHERE id_admin = '$id_admin'");
$saldo_sebelumnya = ($result_saldo && mysqli_num_rows($result_saldo) > 0) ? mysqli_fetch_assoc($result_saldo)['saldo'] : 0;

// Ambil data transaksi sebelumnya
$result_transaksi = mysqli_query($koneksi, "SELECT type, jumlah_uang FROM transaksi WHERE id_transaksi = '$id_transaksi' AND id_admin = '$id_admin'");
$transaksi_lama = ($result_transaksi && mysqli_num_rows($result_transaksi) > 0) ? mysqli_fetch_assoc($result_transaksi) : null;

if (!$transaksi_lama) {
    echo "transaksi_tidak_ditemukan";
    exit();
}

$type_sebelumnya = $transaksi_lama['type'];
$jumlah_uang_sebelumnya = $transaksi_lama['jumlah_uang'];

// Pindahkan entri di tabel pemasukan/pengeluaran jika tipe berubah
if ($type_edit !== $type_sebelumnya) {
    if ($type_sebelumnya === 'pemasukan') {
        // Hapus dari tabel pemasukan dan tambahkan ke pengeluaran
        mysqli_query($koneksi, "DELETE FROM pemasukan WHERE id_transaksi = '$id_transaksi' AND id_admin = '$id_admin'");
        mysqli_query($koneksi, "INSERT INTO pengeluaran (id_admin, id_transaksi, status) VALUES ('$id_admin', '$id_transaksi', 'aktif')");
    } else {
        // Hapus dari tabel pengeluaran dan tambahkan ke pemasukan
        mysqli_query($koneksi, "DELETE FROM pengeluaran WHERE id_transaksi = '$id_transaksi' AND id_admin = '$id_admin'");
        mysqli_query($koneksi, "INSERT INTO pemasukan (id_admin, id_transaksi, status) VALUES ('$id_admin', '$id_transaksi', 'aktif')");
    }
}

// Cek saldo untuk pengeluaran
if ($type_edit === 'pengeluaran' && $saldo_sebelumnya < $jumlah_uang) {
    echo "saldo_tidak_cukup";
    exit();
}

// Update saldo berdasarkan perubahan jumlah uang
if ($type_edit === 'pemasukan') {
    $selisih = $jumlah_uang - $jumlah_uang_sebelumnya;
    $query_saldo = ($selisih >= 0) ? "UPDATE saldo SET saldo = saldo + $selisih WHERE id_admin = '$id_admin'" : "UPDATE saldo SET saldo = saldo + ($selisih) WHERE id_admin = '$id_admin'";
} else {
    $selisih = $jumlah_uang_sebelumnya - $jumlah_uang;
    $query_saldo = ($selisih >= 0) ? "UPDATE saldo SET saldo = saldo + $selisih WHERE id_admin = '$id_admin'" : "UPDATE saldo SET saldo = saldo - ($selisih) WHERE id_admin = '$id_admin'";
}

// Eksekusi update saldo
if (isset($query_saldo) && mysqli_query($koneksi, $query_saldo)) {
    // Update data transaksi
    $query_update = "UPDATE transaksi SET 
        type = '$type_edit',
        jumlah_uang = '$jumlah_uang',
        kategori_transaksi = '$kategori_transaksi',
        deskripsi = '$deskripsi',
        tanggal_transaksi = '$tanggal_transaksi' 
    WHERE id_transaksi = '$id_transaksi' AND id_admin = '$id_admin'";

    if (mysqli_query($koneksi, $query_update)) {
        echo "transaksi_diperbarui";
    } else {
        echo "gagal_memperbarui_data_transaksi";
    }
} else {
    echo "gagal_memperbarui_data_saldo";
}

// Menutup koneksi
mysqli_close($koneksi);
