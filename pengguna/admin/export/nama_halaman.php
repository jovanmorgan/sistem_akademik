<?php
// Dapatkan nama halaman dari URL saat ini tanpa ekstensi .php
$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ".php");

// Tentukan judul halaman berdasarkan nama file
switch ($current_page) {
    case 'dashboard':
        $page_title = 'Dashboard';
        break;
    case 'kub':
        $page_title = 'KUB';
        break;
    case 'ketua_kub':
        $page_title = 'Ketua KUB';
        break;
    case 'u_p':
        $page_title = 'Usulan Pelayanan';
        break;
    case 'pelayanan':
        $page_title = 'Pelayanan';
        break;
    case 'pastor':
        $page_title = 'Pastor';
        break;
    case 'umat':
        $page_title = 'Umat';
        break;
    case 'profile':
        $page_title = 'Profile Saya';
        break;
    case 'log_out':
        $page_title = 'Log Out';
        break;
    default:
        $page_title = 'Admin Gereja ';
        break;
}
