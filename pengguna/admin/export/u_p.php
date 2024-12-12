<?php
// Koneksi ke database
include '../../../keamanan/koneksi.php';

// Ambil konten dari laporan_u_p.php
ob_start(); // Mulai output buffering
include 'laporan_u_p.php'; // Panggil halaman laporan
$laporanHtml = ob_get_clean(); // Dapatkan konten HTML dan bersihkan output buffer

// Buat file HTML sementara untuk laporan
$tmpHtmlFile = tempnam(sys_get_temp_dir(), 'html') . '.html';
file_put_contents($tmpHtmlFile, $laporanHtml);

// Nama file output PDF
$outputFile = sys_get_temp_dir() . '/laporan_usulan_permohonan.pdf';

// Jalankan perintah wkhtmltopdf untuk mengonversi HTML menjadi PDF
$command = "C:/xampp/htdocs/gereja_katolik/wkhtmltopdf/bin/wkhtmltopdf $tmpHtmlFile $outputFile";
exec($command, $output, $return_var);

// Hapus file HTML sementara
unlink($tmpHtmlFile);

// Cek apakah file PDF berhasil dibuat
if (file_exists($outputFile)) {
    // Tampilkan PDF ke browser
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="laporan_usulan_permohonan.pdf"');
    header('Content-Length: ' . filesize($outputFile));
    readfile($outputFile);
} else {
    echo "File PDF tidak ditemukan.";
}
