<?php
include '../../../../keamanan/koneksi.php';

// Terima ID ketua_kub yang akan dihapus dari formulir HTML
$id_ketua_kub = $_POST['id']; // Ubah menjadi $_GET jika menggunakan metode GET

// Lakukan validasi data
if (empty($id_ketua_kub)) {
    echo "data_tidak_lengkap";
    exit();
}

// Buat query SQL untuk menghapus data ketua_kub berdasarkan ID
$query_delete_ketua_kub = "DELETE FROM ketua_kub WHERE id_ketua_kub = '$id_ketua_kub'";

// Jalankan query untuk menghapus data
if (mysqli_query($koneksi, $query_delete_ketua_kub)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
