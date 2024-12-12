<?php
// Dapatkan nama halaman dari URL saat ini tanpa ekstensi .php
$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ".php");

// Tentukan judul halaman berdasarkan nama file
switch ($current_page) {
    case 'dashboard':
        $page_title = 'Dashboard';
        break;
    case 'siswa':
        $page_title = 'Sistem ODP';
        break;
    case 'peserta':
        $page_title = 'Peserta';
        break;
    case 'mata_pelajaran':
        $page_title = 'Mata Pelajaran';
        break;
    case 'ujian':
        $page_title = 'Ujian';
        break;
    case 'profile':
        $page_title = 'Profile Saya';
        break;
    case 'log_out':
        $page_title = 'Log Out';
        break;
    default:
        $page_title = 'Admin Akademik ';
        break;
}
