<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$nama_matpel = $_POST['id_ujian'];
$peserta = $_POST['peserta'];

// Buat query SQL untuk menambahkan data RT ke dalam database
$query = "INSERT INTO peserta (id_ujian, peserta) 
          VALUES ('$id_ujian', '$peserta')";

// Jalankan query
if (mysqli_query($koneksi, $query)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
