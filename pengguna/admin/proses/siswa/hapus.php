<?php
include '../../../../keamanan/koneksi.php';

// Terima nis siswa yang akan dihapus dari formulir HTML
$nis_siswa = $_POST['id']; // Ubah menjadi $_GET jika menggunakan metode GET

// Lakukan valnisasi data
if (empty($nis_siswa)) {
    echo "data_tnisak_lengkap";
    exit();
}

// Buat query SQL untuk menghapus data siswa berdasarkan nis
$query_delete_siswa = "DELETE FROM siswa WHERE nis = '$nis_siswa'";

// Jalankan query untuk menghapus data
if (mysqli_query($koneksi, $query_delete_siswa)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
