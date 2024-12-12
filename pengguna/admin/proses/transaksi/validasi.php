<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$id_u_p = $_POST['id_u_p'];
$status = $_POST['status'];

// Buat query SQL untuk mengupdate data
$query_update = "UPDATE u_p SET status = '$status' WHERE id_u_p = '$id_u_p'";

// Jalankan query
if (mysqli_query($koneksi, $query_update)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
