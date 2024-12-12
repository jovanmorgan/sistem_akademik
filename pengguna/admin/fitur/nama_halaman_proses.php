<?php
// Dapatkan nama halaman dari URL saat ini tanpa ekstensi .php
$current_page_proses = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ".php");

// Tentukan judul halaman berdasarkan nama file
switch ($current_page_proses) {
    case 'dashboard':
        $page_title_proses = 'dashboard';
        break;
    case 'siswa':
        $page_title_proses = 'siswa';
        break;
    case 'peserta':
        $page_title_proses = 'peserta';
        break;
    case 'mata_pelajaran':
        $page_title_proses = 'mata_pelajaran';
        break;
    case 'ujian':
        $page_title_proses = 'ujian';
        break;
    case 'profile':
        $page_title_proses = 'Profile Saya';
        break;
    case 'log_out':
        $page_title_proses = 'Log Out';
        break;
    default:
        $page_title_proses = 'Admin Gereja ';
        break;
}
