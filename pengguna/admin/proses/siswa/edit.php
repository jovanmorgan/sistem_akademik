<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$nis = $_POST['nis'];
$nama = $_POST['nama'];
$alamat = $_POST['alamat'];

// Validasi apakah NIS yang diinputkan ada di database
$check_query = "SELECT * FROM siswa WHERE nis = '$nis'";
$result = mysqli_query($koneksi, $check_query);

if (mysqli_num_rows($result) > 0) {
    // Jika data ditemukan, lakukan update
    $update_query = "UPDATE siswa SET 
                        nama = '$nama', 
                        alamat = '$alamat' 
                     WHERE nis = '$nis'";

    if (mysqli_query($koneksi, $update_query)) {
        echo "success"; // Berhasil mengupdate data
    } else {
        echo "error"; // Gagal mengupdate data
    }
} else {
    echo "data_tidak_ditemukan"; // Kirim respon jika data tidak ditemukan
}

// Tutup koneksi ke database
mysqli_close($koneksi);
