<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$nama_matpel = $_POST['nama_matpel'];

// Buat query SQL untuk menambahkan data RT ke dalam database
$query = "INSERT INTO mata_pelajaran (nama_matpel) 
          VALUES ('$nama_matpel')";

// Jalankan query
if (mysqli_query($koneksi, $query)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
