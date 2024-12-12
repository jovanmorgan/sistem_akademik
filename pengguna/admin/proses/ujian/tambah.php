<?php
include '../../../../keamanan/koneksi.php';

// Terima data dari formulir HTML
$nama_ujian = $_POST['nama_ujian'];
$id_matpel = $_POST['id_matpel'];
$tanggal = $_POST['tanggal'];
$nis = $_POST['peserta'];
$nilai = $_POST['nilai'];

// Query untuk menambahkan data ujian ke dalam tabel ujian
$query_ujian = "INSERT INTO ujian (nama_ujian, id_matpel, tanggal) 
                VALUES ('$nama_ujian', '$id_matpel', '$tanggal')";

// Jalankan query untuk menambahkan ujian
if (mysqli_query($koneksi, $query_ujian)) {
    // Mendapatkan ID ujian yang baru saja ditambahkan
    $id_ujian = mysqli_insert_id($koneksi);

    // Query untuk menambahkan peserta ke dalam tabel peserta
    $query_peserta = "INSERT INTO peserta (peserta, id_ujian, nilai) 
                      VALUES ('$nis', '$id_ujian', '$nilai' )";

    // Jalankan query untuk menambahkan peserta
    if (mysqli_query($koneksi, $query_peserta)) {
        echo "success";
    } else {
        echo "Error adding peserta: " . mysqli_error($koneksi);
    }
} else {
    echo "Error adding ujian: " . mysqli_error($koneksi);
}

// Tutup koneksi ke database
mysqli_close($koneksi);
