<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$id_matpel = $_POST['id_matpel'];
$nama_matpel = $_POST['nama_matpel'];

// Validasi apakah nama_matpel yang diinputkan ada di database
$check_query = "SELECT * FROM mata_pelajaran WHERE id_matpel = '$id_matpel'";
$result = mysqli_query($koneksi, $check_query);

if (mysqli_num_rows($result) > 0) {
    // Jika data ditemukan, lakukan update
    $update_query = "UPDATE mata_pelajaran SET 
                        nama_matpel = '$nama_matpel' 
                     WHERE id_matpel = '$id_matpel'";

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
