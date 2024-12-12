<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$id_ujian = $_POST['id_ujian'];
$nama_ujian = $_POST['nama_ujian'];
$id_matpel = $_POST['id_matpel'];
$tanggal = $_POST['tanggal'];
$nis = $_POST['peserta'];
$nilai = $_POST['nilai'];

// Query untuk memperbarui data ujian di tabel ujian
$query_ujian = "UPDATE ujian 
                SET nama_ujian = '$nama_ujian', id_matpel = '$id_matpel', tanggal = '$tanggal'
                WHERE id_ujian = '$id_ujian'";

// Jalankan query untuk memperbarui ujian
if (mysqli_query($koneksi, $query_ujian)) {

    // Query untuk memperbarui data peserta di tabel peserta
    $query_peserta = "UPDATE peserta
                      SET peserta = '$nis', nilai = '$nilai'
                      WHERE id_ujian = '$id_ujian' AND peserta = '$nis'";

    // Jalankan query untuk memperbarui peserta
    if (mysqli_query($koneksi, $query_peserta)) {
        echo "success";
    } else {
        echo "Error updating peserta: " . mysqli_error($koneksi);
    }
} else {
    echo "Error updating ujian: " . mysqli_error($koneksi);
}

// Tutup koneksi ke database
mysqli_close($koneksi);
