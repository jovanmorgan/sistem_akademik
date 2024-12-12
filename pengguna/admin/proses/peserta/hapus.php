<?php
include '../../../../keamanan/koneksi.php';

// Terima nis mata_pelajaran yang akan dihapus dari formulir HTML
$id_matpel = $_POST['id']; // Ubah menjadi $_GET jika menggunakan metode GET

// Lakukan valnisasi data
if (empty($id_matpel)) {
    echo "data_tnisak_lengkap";
    exit();
}

// Buat query SQL untuk menghapus data mata_pelajaran berdasarkan nis
$query_delete_mata_pelajaran = "DELETE FROM mata_pelajaran WHERE id_matpel = '$id_matpel'";

// Jalankan query untuk menghapus data
if (mysqli_query($koneksi, $query_delete_mata_pelajaran)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
