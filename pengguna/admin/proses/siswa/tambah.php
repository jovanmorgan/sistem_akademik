<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$nis = $_POST['nis'];
$nama = $_POST['nama'];
$alamat = $_POST['alamat'];

// Cek apakah nis sudah ada di database
$check_query_siswa = "SELECT * FROM siswa WHERE nis = '$nis'";
$result_siswa = mysqli_query($koneksi, $check_query_siswa);
if (mysqli_num_rows($result_siswa) > 0) {
    echo "data_sudah_ada"; // Kirim respon "data_sudah_ada" jika email sudah terdaftar
    exit();
}

// Buat query SQL untuk menambahkan data RT ke dalam database
$query = "INSERT INTO siswa (nis, nama, alamat) 
          VALUES ('$nis', '$nama','$alamat')";

// Jalankan query
if (mysqli_query($koneksi, $query)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
