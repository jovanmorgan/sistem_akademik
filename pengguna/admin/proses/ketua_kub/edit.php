<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$id_ketua_kub = $_POST['id_ketua_kub']; // Pastikan 'id_rt' sudah dikirim melalui form
$id_kub = $_POST['id_kub'];
$nama_lengkap = $_POST['nama_lengkap'];
$username = $_POST['username'];
$password = $_POST['password'];

// Lakukan validasi data
if (empty($id_ketua_kub) || empty($id_kub) || empty($nama_lengkap) || empty($username) || empty($password)) {
    echo "data_tidak_lengkap";
    exit();
}

// Cek apakah username sudah ada di database
$check_query = "SELECT * FROM admin WHERE username = '$username'";
$result = mysqli_query($koneksi, $check_query);
if (mysqli_num_rows($result) > 0) {
    echo "data_sudah_ada"; // Kirim respon "data_sudah_ada" jika email sudah terdaftar
    exit();
}
// Cek apakah username sudah ada di database
$check_query_pastor = "SELECT * FROM pastor WHERE username = '$username'";
$result_pastor = mysqli_query($koneksi, $check_query_pastor);
if (mysqli_num_rows($result_pastor) > 0) {
    echo "data_sudah_ada"; // Kirim respon "data_sudah_ada" jika email sudah terdaftar
    exit();
}

// Cek apakah username sudah ada di database
$check_query_umat = "SELECT * FROM umat WHERE username = '$username'";
$result_umat = mysqli_query($koneksi, $check_query_umat);
if (mysqli_num_rows($result_umat) > 0) {
    echo "data_sudah_ada"; // Kirim respon "data_sudah_ada" jika email sudah terdaftar
    exit();
}
// Cek apakah username sudah ada di database
$check_query_ketua_kub = "SELECT * FROM ketua_kub WHERE username = '$username' AND id_ketua_kub != '$id_ketua_kub'";
$result_ketua_kub = mysqli_query($koneksi, $check_query_ketua_kub);
if (mysqli_num_rows($result_ketua_kub) > 0) {
    echo "data_sudah_ada"; // Kirim respon "data_sudah_ada" jika email sudah terdaftar
    exit();
}


if (strlen($password) < 8) {
    echo "error_password_length"; // Kirim respon jika panjang password kurang dari 8 karakter
    exit();
}

// Tambahkan logika untuk memeriksa password
if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/", $password)) {
    echo "error_password_strength"; // Kirim respon jika password tidak memenuhi syarat
    exit();
}

// Buat query SQL untuk mengedit data rt yang sudah ada berdasarkan id_rt
$query = "UPDATE ketua_kub 
            SET id_kub = '$id_kub',
                nama_lengkap = '$nama_lengkap',
                username = '$username',
                password = '$password'
          WHERE id_ketua_kub = '$id_ketua_kub'";

// Jalankan query
if (mysqli_query($koneksi, $query)) {
    echo "success";
} else {
    echo "error";
}

// Tutup koneksi ke database
mysqli_close($koneksi);
