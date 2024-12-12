<?php
include '../../../../keamanan/koneksi.php';

// Terima id_ujian yang akan dihapus dari formulir HTML
$id_ujian = $_POST['id']; // Ubah menjadi $_GET jika menggunakan metode GET

// Lakukan validasi data
if (empty($id_ujian)) {
    echo "data_tidak_lengkap";
    exit();
}

// Query untuk menghapus data peserta yang memiliki id_ujian yang sama
$query_delete_peserta = "DELETE FROM peserta WHERE id_ujian = '$id_ujian'";

// Jalankan query untuk menghapus data peserta
if (mysqli_query($koneksi, $query_delete_peserta)) {
    // Setelah data peserta dihapus, lanjutkan untuk menghapus data ujian
    $query_delete_ujian = "DELETE FROM ujian WHERE id_ujian = '$id_ujian'";

    // Jalankan query untuk menghapus data ujian
    if (mysqli_query($koneksi, $query_delete_ujian)) {
        echo "success";
    } else {
        echo "error_hapus_ujian: " . mysqli_error($koneksi);
    }
} else {
    echo "error_hapus_peserta: " . mysqli_error($koneksi);
}

// Tutup koneksi ke database
mysqli_close($koneksi);
