<?php include 'fitur/penggunah.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'fitur/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'fitur/sidebar.php'; ?>
        <div class="main-panel">
            <?php include 'fitur/navbar.php'; ?>
            <div class="container">
                <div class="page-inner">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-3">Dashboard</h3>
                            <h6 class="op-7 mb-2">Halaman Dasboard</h6>
                        </div>
                    </div>

                    <?php
                    include '../../keamanan/koneksi.php';

                    $tables = [
                        'transaksi' => [
                            'label' => 'Transaksi',
                            'icon' => 'fas fa-exchange-alt',
                            'color' => '#FFC107'
                        ],
                        'pemasukan' => [
                            'label' => 'Pemasukan',
                            'icon' => 'fas fa-arrow-up',
                            'color' => '#198754'
                        ],
                        'pengeluaran' => [
                            'label' => 'Pengeluaran',
                            'icon' => 'fas fa-arrow-down',
                            'color' => '#DC3545'
                        ],
                        'target' => [
                            'label' => 'Target',
                            'icon' => 'fas fa-bullseye',
                            'color' => '#198754'
                        ],
                        'prediksi' => [
                            'label' => 'Prediksi Keuangan',
                            'icon' => 'fas fa-chart-line',
                            'color' => '#0D6EFD'
                        ],
                        'peringatan' => [
                            'label' => 'Peringatan Keuangan',
                            'icon' => 'fas fa-exclamation-triangle',
                            'color' => '#DC3545'
                        ]
                    ];

                    $counts = [];

                    foreach ($tables as $table => $details) {
                        $query = "SELECT COUNT(*) as count FROM $table";
                        $result = mysqli_query($koneksi, $query);
                        $row = mysqli_fetch_assoc($result);
                        $counts[$table] = $row['count'];
                        mysqli_free_result($result);
                    }

                    mysqli_close($koneksi);

                    // Bagian nama halaman
                    include 'fitur/nama_halaman.php';

                    ?>

                    <section class="section">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title" style="font-size: 30px;">Selamat Datang</h5>
                                        <p>
                                            Silakan lihat informsi yang kami sajikan Manajemen Keuangan Anda, Berikut
                                            adalah
                                            informasi data Grafik, Diagram, dan Tabel pada Halaman
                                            <?= $page_title ?> ini.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="section">

                        <?php
                        // Menghubungkan ke file koneksi.php
                        include '../../keamanan/koneksi.php';

                        // Ambil data transaksi
                        $sql = "SELECT tanggal_transaksi, jumlah_uang, saldo_sebelumnya, type FROM transaksi ORDER BY id_transaksi DESC";
                        $result = $koneksi->query($sql);

                        // Inisialisasi array untuk grafik
                        $labels = [];         // Label untuk tanggal transaksi
                        $labelOriginal = [];  // Label original untuk format lengkap
                        $saldos = [];         // Data untuk saldo_sebelumnya
                        $results = [];        // Data untuk saldo hasil akhir
                        $jumlahUang = [];     // Data untuk Jumlah Uang

                        // Memproses data
                        while ($row = $result->fetch_assoc()) {
                            // Ambil tanggal dalam format yang disimpan (misalnya "Kamis, 03-November-2024 12:00:00 Pagi")
                            $tanggalTransaksiOriginal = $row['tanggal_transaksi'];

                            // Menghapus nama hari (misalnya "Kamis,") dan waktu (misalnya "Pagi/Siang")
                            $tanggalTransaksiStripped = preg_replace("/^[A-Za-z]+, /", "", $tanggalTransaksiOriginal); // Menghapus nama hari
                            $tanggalTransaksiStripped = preg_replace("/\s*(Pagi|Siang|Sore|Malam)$/", "", $tanggalTransaksiStripped); // Menghapus waktu

                            // Membuat objek DateTime dari string tanggal yang sudah dibersihkan
                            $datetime = new DateTime($tanggalTransaksiStripped);

                            // Memformat tanggal dengan hari dalam bahasa Indonesia
                            setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'indo'); // Set locale Indonesia
                            $tanggalTransaksiFormatted = strftime("%A, %d %B %Y", $datetime->getTimestamp());  // Format: 'Minggu, 03 November 2024'
                            $tanggalTransaksiSimple = $datetime->format('Y-m-d'); // Format standar Y-m-d (2024-11-03)

                            // Simpan tanggal yang telah diformat ke dalam array labels dan labelOriginal
                            $labels[] = $tanggalTransaksiSimple;
                            $labelOriginal[] = $tanggalTransaksiFormatted; // Tanggal dengan format lengkap
                            $saldos[] = $row['saldo_sebelumnya'];

                            if ($row['type'] == 'pengeluaran') {
                                $transactionTypes[] = [
                                    'pointBackgroundColor' => 'red' // Warna titik merah untuk pengeluaran
                                ];
                                $results[] = $row['saldo_sebelumnya'] - $row['jumlah_uang'];
                            } else {
                                $transactionTypes[] = [
                                    'pointBackgroundColor' => 'green' // Warna titik hijau untuk pemasukan
                                ];
                                $results[] = $row['saldo_sebelumnya'] + $row['jumlah_uang'];
                            }

                            // Menyimpan jumlah uang ke dalam array
                            $jumlahUang[] = $row['jumlah_uang'];
                        }

                        // Tutup koneksi
                        $koneksi->close();

                        // Membalik urutan array agar data terbaru muncul di sebelah kanan
                        $labelOriginal = array_reverse($labelOriginal);
                        $labels = array_reverse($labels);
                        $saldos = array_reverse($saldos);
                        $results = array_reverse($results);
                        $jumlahUang = array_reverse($jumlahUang);
                        ?>

                        <!-- Grafik -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-head-row card-tools-still-right">
                                            <div class="card-title">Grafik Keuangan Perhari</div>
                                            <div class="card-tools">
                                                <!-- Input Group dengan Icon Kalender -->
                                                <div class="input-group" style="width: 250px;">
                                                    <span class="input-group-text" id="calendarIcon">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </span>
                                                    <input type="text" id="flatpickrInput" class="form-control"
                                                        placeholder="Pilih tanggal" readonly style="cursor: pointer;" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="multipleLineChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">Bar Chart</div>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="barChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">Pie Chart</div>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="pieChart" style="width: 50%; height: 50%"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <?php foreach ($tables as $table => $details): ?>
                                <!-- Link menuju halaman spesifik berdasarkan nama tabel -->
                                <a href="<?= $table ?>" class="col-sm-6 col-md-3"
                                    style="text-decoration: none; color: inherit;">
                                    <div class="card card-stats card-round">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-icon">
                                                    <div class="icon-big text-center icon-secondary bubble-shadow-small"
                                                        style="background-color: <?= $details['color']; ?>;">
                                                        <i class="<?= $details['icon']; ?>"></i>
                                                    </div>
                                                </div>
                                                <div class="col col-stats ms-3 ms-sm-0">
                                                    <div class="numbers">
                                                        <p class="card-category"><?= $details['label']; ?></p>
                                                        <h4 class="card-title"><?= $counts[$table]; ?></h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                </div>
            </div>

            <?php include 'fitur/footer.php'; ?>
        </div>

    </div>


    <?php include 'fitur/js.php'; ?>
    <!-- Scripts -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/setting-demo2.js"></script>

    <script>
        // Variabel PHP ke dalam JavaScript
        var labelOriginal = <?php echo json_encode($labelOriginal); ?>;
        var labels = <?php echo json_encode($labels); ?>;
        var saldos = <?php echo json_encode($saldos); ?>;
        var transactionTypes = <?php echo json_encode($transactionTypes); ?>;
        var results = <?php echo json_encode($results); ?>;
        var jumlahUang = <?php echo json_encode($jumlahUang); ?>; // Tambahkan data jumlah_uang

        var multipleLineChart = document.getElementById("multipleLineChart").getContext("2d");

        var myMultipleLineChart = new Chart(multipleLineChart, {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                        label: "Saldo Sebelumnya",
                        borderColor: "#FFD700",
                        pointBorderColor: "#FFF",
                        pointBackgroundColor: "#FFD700",
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBorderWidth: 1,
                        pointRadius: 6,
                        backgroundColor: "transparent",
                        fill: true,
                        borderWidth: 2,
                        data: saldos,
                    },
                    {
                        label: "Hasil",
                        borderColor: "#4CAF50",
                        pointBorderColor: "#FFF",
                        pointBackgroundColor: transactionTypes.map(
                            (t) => t.pointBackgroundColor
                        ),
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBorderWidth: 1,
                        pointRadius: 6,
                        backgroundColor: "transparent",
                        fill: true,
                        borderWidth: 2,
                        data: results,
                    },
                    {
                        label: "Jumlah Uang", // Garis ketiga untuk jumlah uang
                        borderColor: "#000", // Warna garis ketiga
                        pointBorderColor: "#FFF",
                        pointBackgroundColor: transactionTypes.map(
                            (t) => t.pointBackgroundColor
                        ),
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBorderWidth: 1,
                        pointRadius: 6,
                        backgroundColor: "transparent",
                        fill: true,
                        borderWidth: 2,
                        data: jumlahUang, // Data jumlah uang untuk garis ketiga
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: "top",
                },
                tooltips: {
                    bodySpacing: 4,
                    mode: "nearest",
                    intersect: 0,
                    position: "nearest",
                    xPadding: 10,
                    yPadding: 10,
                    caretPadding: 10,
                    callbacks: {
                        // Menambahkan format dengan pemisah ribuan pada tooltip
                        label: function(tooltipItem, data) {
                            var label = tooltipItem.yLabel;
                            // Format label dengan pemisah ribuan untuk nilai lainnya
                            if (label === 0 || label === 10 || label === 100) {
                                return label; // Tidak mengubah angka-angka kecil
                            }
                            // Format angka dengan pemisah ribuan
                            return label.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        },
                        // Menampilkan detail lengkap pada tooltip
                        title: function(tooltipItem, data) {
                            var index = tooltipItem[0].index;
                            var tanggalLengkap = labelOriginal[
                                index]; // Menampilkan tanggal dengan format lengkap dalam bahasa Indonesia
                            return tanggalLengkap; // Format tanggal lengkap dalam bahasa Indonesia
                        }

                    },
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true, // Pastikan sumbu Y dimulai dari 0
                            callback: function(value) {
                                return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g,
                                    "."); // Pemisah ribuan
                            },
                        },
                    }],
                },
                layout: {
                    padding: {
                        left: 15,
                        right: 15,
                        top: 15,
                        bottom: 15,
                    },
                },
            },
        });
    </script>



    <script>
        // Bar Chart
        var barChart = document.getElementById("barChart").getContext("2d"),
            pieChart = document.getElementById("pieChart").getContext("2d");

        var myBarChart = new Chart(barChart, {
            type: "bar",
            data: {
                labels: [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                    "Aug",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dec",
                ],
                datasets: [{
                    label: "Sales",
                    backgroundColor: "rgb(23, 125, 255)",
                    borderColor: "rgb(23, 125, 255)",
                    data: [3, 2, 9, 5, 4, 6, 4, 6, 7, 8, 7, 4],
                }, ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                        },
                    }, ],
                },
            },
        });

        var myPieChart = new Chart(pieChart, {
            type: "pie",
            data: {
                datasets: [{
                    data: [50, 35, 15],
                    backgroundColor: ["#1d7af3", "#f3545d", "#fdaf4b"],
                    borderWidth: 0,
                }, ],
                labels: ["New Visitors", "Subscribers", "Active Users"],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: "bottom",
                    labels: {
                        fontColor: "rgb(154, 154, 154)",
                        fontSize: 11,
                        usePointStyle: true,
                        padding: 20,
                    },
                },
                pieceLabel: {
                    render: "percentage",
                    fontColor: "white",
                    fontSize: 14,
                },
                tooltips: false,
                layout: {
                    padding: {
                        left: 20,
                        right: 20,
                        top: 20,
                        bottom: 20,
                    },
                },
            },
        });
    </script>
</body>

</html>