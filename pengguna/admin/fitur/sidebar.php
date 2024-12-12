<?php
// Dapatkan nama halaman dari URL saat ini tanpa ekstensi .php
$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ".php");

// Fungsi untuk mendapatkan ikon yang sesuai dengan halaman
function getIconForPage($page)
{
    switch ($page) {
        case 'dashboard':
            return 'fas fa-chart-line'; // Ikon statistik untuk dashboard
        case 'siswa':
            return 'fas fa-user-graduate'; // Ikon siswa
        case 'peserta':
            return 'fas fa-users'; // Ikon kelompok peserta
        case 'mata_pelajaran':
            return 'fas fa-book'; // Ikon buku untuk mata pelajaran
        case 'ujian':
            return 'fas fa-pencil-alt'; // Ikon pensil untuk ujian
        case 'profile':
            return 'fas fa-user-circle'; // Ikon profil pengguna
        case 'log_out':
            return 'fas fa-sign-out-alt'; // Ikon keluar
        default:
            return 'fas fa-folder'; // Ikon default jika halaman tidak dikenal
    }
}
?>


<!-- Sidebar -->
<div class="sidebar" data-background-color="white">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="white">
            <a href="dashboard" class="logo">
                <img src="../../assets/img/akademik/3.png" alt="navbar brand" class="navbar-brand gbr" height="50px" />
                <h5 class="text-black judul">Sistem Odp
                </h5>
            </a>
            <!-- style untuk judul -->
            <style>
            .gbr {
                position: relative;
                right: 10px;
            }

            .judul {
                font-size: 27px;
                position: relative;
                right: 5px;
                margin-top: 10px;
                font-weight: 500;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            /* media untuk hp */

            @media only screen and (max-width: 600px) {
                .gbr {
                    position: relative;
                    right: 5px;
                }

                .judul {
                    font-size: 25px;
                    position: relative;
                    right: 5px;
                }
            }
            </style>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner" data-background-color="white">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <!-- Bagian Utama -->
                <!-- <li class="nav-item <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
                    <a href="dashboard">
                        <i class="<?php echo getIconForPage('dashboard'); ?>"></i>
                        <p>Dashboard</p>
                    </a>
                </li> -->

                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fas fa-book"></i>
                    </span>
                    <h4 class="text-section">Manajemen Akademik</h4>
                </li>

                <li class="nav-item <?php echo ($current_page == 'siswa') ? 'active' : ''; ?>">
                    <a href="siswa">
                        <i class="<?php echo getIconForPage('siswa'); ?>"></i>
                        <p>Siswa</p>
                    </a>
                </li>

                <li class="nav-item <?php echo ($current_page == 'peserta') ? 'active' : ''; ?>">
                    <a href="peserta">
                        <i class="<?php echo getIconForPage('peserta'); ?>"></i>
                        <p>Peserta</p>
                    </a>
                </li>

                <li class="nav-item <?php echo ($current_page == 'mata_pelajaran') ? 'active' : ''; ?>">
                    <a href="mata_pelajaran">
                        <i class="<?php echo getIconForPage('mata_pelajaran'); ?>"></i>
                        <p>Mata Pelajaran</p>
                    </a>
                </li>

                <li class="nav-item <?php echo ($current_page == 'ujian') ? 'active' : ''; ?>">
                    <a href="ujian">
                        <i class="<?php echo getIconForPage('ujian'); ?>"></i>
                        <p>Ujian</p>
                    </a>
                </li>

                <!-- Bagian Akun dan Pengaturan -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <h4 class="text-section">Akun dan Pengaturan</h4>
                </li>

                <li class="nav-item <?php echo ($current_page == 'profile') ? 'active' : ''; ?>">
                    <a href="profile">
                        <i class="<?php echo getIconForPage('profile'); ?>"></i>
                        <p>Profile Saya</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="log_out">
                        <i class="<?php echo getIconForPage('log_out'); ?>"></i>
                        <p>Log Out</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->