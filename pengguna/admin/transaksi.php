<?php include 'fitur/penggunah.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'fitur/head.php'; ?>
<?php include 'fitur/nama_halaman.php'; ?>
<?php include 'fitur/nama_halaman_proses.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'fitur/sidebar.php'; ?>
        <div class="main-panel">
            <?php include 'fitur/navbar.php'; ?>
            <div class="container">
                <div class="page-inner">
                    <?php include 'fitur/papan_halaman.php'; ?>

                    <div id="load_data">
                        <section class="section">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <!-- Search Form -->
                                            <form method="GET" action="">
                                                <div class="input-group mt-3">
                                                    <input type="text" class="form-control" placeholder="Cari Data..."
                                                        name="search"
                                                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                                    <button class="btn btn-outline-secondary"
                                                        type="submit">Cari</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <?php
                        include '../../keamanan/koneksi.php';

                        // Pencarian dan pagination
                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $limit = 10;
                        $offset = ($page - 1) * $limit;

                        // Query untuk mendapatkan data transaksi dengan pencarian, pagination, dan pengurutan berdasarkan tanggal terbaru
                        $query = "
                                SELECT * FROM transaksi
                                WHERE type LIKE ? OR jumlah_uang LIKE ? OR deskripsi LIKE ? OR tanggal_transaksi LIKE ?
                                ORDER BY id_transaksi DESC
                                LIMIT ?, ?";
                        $stmt = $koneksi->prepare($query);
                        $search_param = '%' . $search . '%';
                        $stmt->bind_param("ssssii", $search_param, $search_param, $search_param, $search_param, $offset, $limit);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Hitung total halaman
                        $total_query = "
                                SELECT COUNT(*) as total 
                                FROM transaksi
                                WHERE type LIKE ? OR jumlah_uang LIKE ? OR deskripsi LIKE ? OR tanggal_transaksi LIKE ?";
                        $stmt_total = $koneksi->prepare($total_query);
                        $stmt_total->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
                        $stmt_total->execute();
                        $total_result = $stmt_total->get_result();
                        $total_row = $total_result->fetch_assoc();
                        $total_pages = ceil($total_row['total'] / $limit);
                        ?>


                        <!-- Tabel Data transaksi -->
                        <section class="section">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body" style="overflow-x: hidden;">
                                            <div style="overflow-x: auto;">
                                                <?php if ($result->num_rows > 0): ?>
                                                <table class="table table-hover text-center mt-3"
                                                    style="border-collapse: separate; border-spacing: 0;">
                                                    <thead>
                                                        <tr>
                                                            <th style="white-space: nowrap;">Nomor</th>
                                                            <th style="white-space: nowrap;">Type Transaksi</th>
                                                            <th style="white-space: nowrap;">Saldo Sebelum</th>
                                                            <th style="white-space: nowrap;">Jumlah Uang</th>
                                                            <th style="white-space: nowrap;">Saldo Sesudah</th>
                                                            <th style="white-space: nowrap;">Kategori Transaksi</th>
                                                            <th style="white-space: nowrap;">Deskripsi</th>
                                                            <th style="white-space: nowrap;">Tanggal Transaksi</th>
                                                            <th style="white-space: nowrap;">Aksi</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php
                                                            $nomor = $offset + 1; // Mulai nomor urut dari $offset + 1
                                                            while ($row = $result->fetch_assoc()) :
                                                                // Mengkonversi jumlah_uang dan saldo_sebelumnya ke tipe numerik
                                                                $saldo_sebelumnya = floatval($row['saldo_sebelumnya']);
                                                                $jumlah_uang = floatval($row['jumlah_uang']);

                                                                // Hitung saldo sesudah
                                                                if ($row['type'] === 'pemasukan') {
                                                                    $saldo_sesudah = $saldo_sebelumnya + $jumlah_uang;
                                                                    $icon = '<i class="fas fa-arrow-up text-success me-2"></i>';
                                                                    $warna = 'text-success';
                                                                } else { // pengeluaran
                                                                    $saldo_sesudah = $saldo_sebelumnya - $jumlah_uang;
                                                                    $icon = '<i class="fas fa-arrow-down text-danger me-2"></i>';
                                                                    $warna = 'text-danger';
                                                                }

                                                                // Ambil tanggal dalam format yang disimpan (e.g., Minggu, 03-November-2024 12:00:00 Pagi)
                                                                $tanggalTransaksiOriginal = $row['tanggal_transaksi'];

                                                                // Pisahkan komponen waktu
                                                                preg_match('/(\w+), (\d{2}-\w+-\d{4}) (\d{2}:\d{2}:\d{2}) (Pagi|Siang|Sore|Malam)/', $tanggalTransaksiOriginal, $matches);

                                                                if (!empty($matches)) {
                                                                    $tanggal = $matches[2]; // Bagian tanggal dalam format "03-November-2024"
                                                                    $waktu = $matches[3]; // Bagian waktu dalam format "12:00:00"
                                                                    $period = $matches[4]; // Bagian periode waktu (Pagi, Siang, Sore, Malam)

                                                                    // Ubah periode waktu ke format 24-jam
                                                                    $datetime = DateTime::createFromFormat('d-F-Y h:i:s A', "$tanggal $waktu " . ($period === 'Pagi' ? 'AM' : ($period === 'Malam' ? 'PM' : 'PM')));

                                                                    if ($datetime) {
                                                                        // Format ulang ke datetime-local
                                                                        $tanggalTransaksiFormatted = $datetime->format('Y-m-d\TH:i');
                                                                    } else {
                                                                        $tanggalTransaksiFormatted = ''; // Jika parsing gagal
                                                                    }
                                                                } else {
                                                                    $tanggalTransaksiFormatted = ''; // Jika regex tidak cocok
                                                                }
                                                            ?>
                                                        <tr>
                                                            <td><?php echo $nomor++; ?></td>
                                                            <td>
                                                                <?php if ($row['type'] === 'pemasukan'): ?>
                                                                <span
                                                                    class="text-success"><?php echo ucfirst(htmlspecialchars($row['type'])); ?></span>
                                                                <?php elseif ($row['type'] === 'pengeluaran'): ?>
                                                                <span
                                                                    class="text-danger"><?php echo ucfirst(htmlspecialchars($row['type'])); ?></span>
                                                                <?php endif; ?>
                                                            </td>

                                                            <!-- Saldo Sebelum -->
                                                            <td style="color: 
    <?php echo ($row['saldo_sebelumnya'] >= 0) ? '#daa520' : 'red'; ?>">
                                                                <i class="fas fa-wallet me-2"></i>
                                                                Rp
                                                                <span>
                                                                    <?php echo number_format($row['saldo_sebelumnya'], 0, ',', '.'); ?>
                                                                </span>
                                                            </td>

                                                            <!-- Jumlah Uang -->
                                                            <?php
                                                                    // Tentukan ikon berdasarkan tipe transaksi
                                                                    $icon_type = ($row['type'] === 'pemasukan') ? '<i class="fas fa-plus"></i>' : '<i class="fas fa-minus"></i>';

                                                                    // Tentukan warna teks berdasarkan tipe transaksi
                                                                    $warna_type = ($row['type'] === 'pemasukan') ? 'text-success' : 'text-danger';
                                                                    ?>
                                                            <td class="<?php echo $warna_type; ?>">
                                                                <?php echo $icon_type; ?>
                                                                <span>
                                                                    Rp
                                                                    <?php echo number_format($row['jumlah_uang'], 0, ',', '.'); ?>
                                                                </span>
                                                            </td>

                                                            <!-- Saldo Sesudah -->
                                                            <td>
                                                                <?php echo $icon; ?>
                                                                <span class="<?php echo $warna; ?>">Rp
                                                                    <?php echo number_format($saldo_sesudah, 0, ',', '.'); ?></span>
                                                            </td>

                                                            <!-- Kategori Transaksi dengan Emoji -->
                                                            <td>
                                                                <?php
                                                                        $kategori = ucfirst($row['kategori_transaksi']);
                                                                        switch ($kategori) {
                                                                            case 'Makanan':
                                                                                echo '🥐 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                            case 'Pekerjaan':
                                                                                echo '👩‍💻 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                            case 'Bisnis':
                                                                                echo '📈 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                            case 'Sakit':
                                                                                echo '🤒 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                            case 'Transportasi':
                                                                                echo '🚗 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                            case 'Hiburan':
                                                                                echo '🎉 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                            case 'Pendidikan':
                                                                                echo '📚 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                            case 'Tabungan':
                                                                                echo '💰 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                            case 'Kesehatan':
                                                                                echo '🏥 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                            case 'Pakaian':
                                                                                echo '👚 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                            case 'Hadiah':
                                                                                echo '🎁 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                            case 'Keluarga':
                                                                                echo '👨‍👩‍👧‍👦 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                            case 'Pacar':
                                                                                echo '💞 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                            default:
                                                                                echo '🤔 ' . htmlspecialchars($kategori);
                                                                                break;
                                                                        }
                                                                        ?>
                                                            </td>

                                                            <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['tanggal_transaksi']); ?>
                                                            </td>
                                                            <td
                                                                style="display: flex; justify-content: center; gap: 10px;">
                                                                <button class="btn btn-primary btn-sm" onclick="openEditModal(
                                                                                    '<?php echo addslashes($row['id_transaksi']); ?>',
                                                                                    '<?php echo addslashes($row['type']); ?>', 
                                                                                    '<?php echo addslashes($row['jumlah_uang']); ?>', 
                                                                                    '<?php echo addslashes($row['kategori_transaksi']); ?>', 
                                                                                    '<?php echo addslashes($row['deskripsi']); ?>', 
                                                                                    '<?php echo $tanggalTransaksiFormatted; ?>'
                                                                                )">Edit</button>
                                                                <button class="btn btn-danger btn-sm"
                                                                    onclick="hapus('<?php echo $row['id_transaksi']; ?>')">Hapus</button>
                                                            </td>
                                                        </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>

                                                </table>
                                                <?php else: ?>
                                                <p class=" text-center mt-4">Data tidak ditemukan.</p>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Pagination Section -->
                        <section class="section">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <!-- Pagination with icons -->
                                            <nav aria-label="Pagxample" style="margin-top: 2.2rem;">
                                                <ul class="pagination justify-content-center">
                                                    <li class="page-item <?php if ($page <= 1) {
                                                                                echo 'disabled';
                                                                            } ?>">
                                                        <a class="page-link" href="<?php if ($page > 1) {
                                                                                        echo "?page=" . ($page - 1) . "&search=" . $search;
                                                                                    } ?>" aria-label="Previous">
                                                            <span aria-hidden="true">&laquo;</span>
                                                        </a>
                                                    </li>
                                                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                                    <li class="page-item <?php if ($i == $page) {
                                                                                    echo 'active';
                                                                                } ?>">
                                                        <a class="page-link"
                                                            href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                                                    </li>
                                                    <?php } ?>
                                                    <li class="page-item <?php if ($page >= $total_pages) {
                                                                                echo 'disabled';
                                                                            } ?>">
                                                        <a class="page-link" href="<?php if ($page < $total_pages) {
                                                                                        echo "?page=" . ($page + 1) . "&search=" . $search;
                                                                                    } ?>" aria-label="Next">
                                                            <span aria-hidden="true">&raquo;</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </nav>
                                            <!-- End Pagination with icons -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="tambahDataModalLabel">Tambah <?= $page_title ?></h5>
                            <button type="button" class="btn-close" id="closeTambahModal" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="tambahForm" method="POST" action="proses/<?= $page_title_proses ?>/tambah.php"
                                enctype="multipart/form-data">
                                <input type="hidden" id="id_admin" name="id_admin" value="<?php echo $id_admin; ?>">

                                <!-- Type Transaksi -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Type Transaksi</label>
                                    <br>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="type" value="pemasukan" class="selectgroup-input"
                                                checked onchange="updateColors()" />
                                            <span class="selectgroup-button text-success">
                                                <i class="fas fa-arrow-circle-down"></i> Pemasukan
                                            </span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="type" value="pengeluaran"
                                                class="selectgroup-input" onchange="updateColors()" />
                                            <span class="selectgroup-button text-danger">
                                                <i class="fas fa-arrow-circle-up"></i> Pengeluaran
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Jumlah Uang -->
                                <div class="form-group mb-3">
                                    <label class="form-label" for="jumlah_uang">Jumlah Uang</label>
                                    <div class="input-group">
                                        <span id="icon-jumlah" class="input-group-text text-white"><i
                                                class="fas fa-money-bill-wave"></i></span>
                                        <input type="text" class="form-control" id="jumlah_uang" name="jumlah_uang"
                                            placeholder="100.000 (Seratus Ribu)" required>

                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="kategori_transaksi">Kategori Transaksi</label>
                                    <div class="input-group">
                                        <span id="icon-kategori" class="input-group-text text-white"><i
                                                class="fas fa-list-alt"></i></span>
                                        <select class="form-select" id="kategori_transaksi" name="kategori_transaksi"
                                            required>
                                            <option value="" disabled selected>Pilih kategori</option>
                                            <option value="Pekerjaan">Pekerjaan 👩‍💻</option>
                                            <option value="Bisnis">Bisnis 📈</option>
                                            <option value="Makanan">Makanan 🍲</option>
                                            <option value="Transportasi">Transportasi 🚗</option>
                                            <option value="Hiburan">Hiburan 🎉</option>
                                            <option value="Pendidikan">Pendidikan 📚</option>
                                            <option value="Tabungan">Tabungan 💰</option>
                                            <option value="Kesehatan">Kesehatan 🏥</option>
                                            <option value="Sakit">Sakit 🤒</option>
                                            <option value="Pakaian">Pakaian 👚</option>
                                            <option value="Hadiah">Hadiah 🎁</option>
                                            <option value="Keluarga">Keluarga 👨‍👩‍👧‍👦</option>
                                            <option value="Pacar">Pacar 💞</option>
                                            <option value="lainnya">Lainnya 🤔</option>
                                        </select>
                                    </div>
                                    <div class="form-group mt-3" id="kategori_lainnya_container" style="display: none;">
                                        <label class="form-label" for="kategori_lainnya">Kategori Lainnya</label>
                                        <div class="input-group">
                                            <span id="icon-kategorilainnya" class="input-group-text text-white"><i
                                                    class="fas fa-plus-circle"></i></span>
                                            <input type="text" class="form-control" id="kategori_lainnya"
                                                name="kategori_lainnya" placeholder="Masukkan kategori lainnya">
                                        </div>
                                    </div>
                                </div>

                                <!-- Deskripsi -->
                                <div class="form-group mb-3">
                                    <label class="form-label" for="deskripsi">Deskripsi</label>
                                    <div class="input-group">
                                        <span id="icon-deskripsi" class="input-group-text text-white"><i
                                                class="fas fa-align-left"></i></span>
                                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
                                            placeholder="Masukkan deskripsi transaksi" required></textarea>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            // Format jumlah uang dengan titik setiap tiga angka
            const jumlahUangInput = document.getElementById('jumlah_uang');
            jumlahUangInput.addEventListener('input', function(e) {
                let cleanValue = e.target.value.replace(/\D/g, '');
                e.target.value = cleanValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            });
            jumlahUangInput.form.addEventListener('submit', function() {
                jumlahUangInput.value = jumlahUangInput.value.replace(/\./g, '');
            });

            // Tampilkan input "Kategori Lainnya" jika dipilih
            const kategoriSelect = document.getElementById('kategori_transaksi');
            const kategoriLainnyaContainer = document.getElementById('kategori_lainnya_container');
            const kategoriLainnyaInput = document.getElementById('kategori_lainnya');
            kategoriSelect.addEventListener('change', function() {
                if (this.value === 'lainnya') {
                    kategoriLainnyaContainer.style.display = 'block';
                    kategoriLainnyaInput.setAttribute('required', 'required');
                } else {
                    kategoriLainnyaContainer.style.display = 'none';
                    kategoriLainnyaInput.removeAttribute('required');
                }
            });

            // Update warna ikon berdasarkan tipe transaksi
            function updateColors() {
                const type = document.querySelector('input[name="type"]:checked').value;
                const color = (type === 'pemasukan') ? 'bg-success' : 'bg-danger';
                document.getElementById('icon-jumlah').className = `input-group-text text-white ${color}`;
                document.getElementById('icon-kategori').className = `input-group-text text-white ${color}`;
                document.getElementById('icon-kategorilainnya').className = `input-group-text text-white ${color}`;
                document.getElementById('icon-deskripsi').className = `input-group-text text-white ${color}`;
                document.getElementById('icon-tanggal').className = `input-group-text text-white ${color}`;
            }
            document.addEventListener("DOMContentLoaded", updateColors);
            </script>


            <!-- Modal Edit -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editDataModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editDataModalLabel">Edit <?= $page_title ?></h5>
                            <button type="button" class="btn-close" id="closeEditModal" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editForm" method="POST" action="proses/<?= $page_title_proses ?>/edit.php"
                                enctype="multipart/form-data">
                                <input type="hidden" id="edit_id" name="id_transaksi">
                                <input type="hidden" id="id_admin" name="id_admin" value="<?php echo $id_admin; ?>">

                                <!-- Type Transaksi -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Type Transaksi</label>
                                    <br>
                                    <div class="selectgroup selectgroup-pills">
                                        <label class="selectgroup-item">
                                            <input type="radio" name="type_edit" value="pemasukan"
                                                class="selectgroup-input" onchange="updateColorsEdit()">
                                            <span class="selectgroup-button text-success">
                                                <i class="fas fa-arrow-circle-down"></i> Pemasukan
                                            </span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input type="radio" name="type_edit" value="pengeluaran"
                                                class="selectgroup-input" onchange="updateColorsEdit()">
                                            <span class="selectgroup-button text-danger">
                                                <i class="fas fa-arrow-circle-up"></i> Pengeluaran
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Jumlah Uang -->
                                <div class="form-group mb-3">
                                    <label class="form-label" for="edit_jumlah_uang_data">Jumlah Uang</label>
                                    <div class="input-group">
                                        <span id="icon-jumlah-edit" class="input-group-text text-white"><i
                                                class="fas fa-money-bill-wave"></i></span>
                                        <input type="text" class="form-control" id="edit_jumlah_uang" name="jumlah_uang"
                                            placeholder="100.000 (Seratus Ribu)" required>
                                    </div>
                                </div>

                                <!-- Kategori Transaksi -->
                                <div class="form-group mb-3">
                                    <label class="form-label" for="edit_kategori_transaksi_data">Kategori
                                        Transaksi</label>
                                    <div class="input-group">
                                        <span id="icon-kategori-edit" class="input-group-text text-white"><i
                                                class="fas fa-list-alt"></i></span>
                                        <select class="form-select" id="edit_kategori_transaksi"
                                            name="kategori_transaksi" required>
                                            <option value="" disabled selected>Pilih kategori</option>
                                            <option value="Pekerjaan">Pekerjaan 👩‍💻</option>
                                            <option value="Bisnis">Bisnis 📈</option>
                                            <option value="Makanan">Makanan 🍲</option>
                                            <option value="Transportasi">Transportasi 🚗</option>
                                            <option value="Hiburan">Hiburan 🎉</option>
                                            <option value="Pendidikan">Pendidikan 📚</option>
                                            <option value="Tabungan">Tabungan 💰</option>
                                            <option value="Kesehatan">Kesehatan 🏥</option>
                                            <option value="Sakit">Sakit 🤒</option>
                                            <option value="Pakaian">Pakaian 👚</option>
                                            <option value="Hadiah">Hadiah 🎁</option>
                                            <option value="Keluarga">Keluarga 👨‍👩‍👧‍👦</option>
                                            <option value="Pacar">Pacar 💞</option>
                                            <option value="lainnya">Lainnya 🤔</option>
                                        </select>
                                    </div>
                                    <div class="form-group mt-3" id="kategori_lainnya_container_edit"
                                        style="display: none;">
                                        <label class="form-label" for="kategori_edit_data_lainnya">Kategori
                                            Lainnya</label>
                                        <div class="input-group">
                                            <span id="icon-kategorilainnya_edit" class="input-group-text text-white"><i
                                                    class="fas fa-plus-circle"></i></span>
                                            <input type="text" class="form-control" id="kategori_edit_lainnya"
                                                name="kategori_lainnya" placeholder="Masukkan kategori lainnya">
                                        </div>
                                    </div>
                                </div>

                                <!-- Deskripsi -->
                                <div class="form-group mb-3">
                                    <label class="form-label" for="edit_deskripsi_data">Deskripsi</label>
                                    <div class="input-group">
                                        <span id="icon-deskripsi-edit" class="input-group-text text-white"><i
                                                class="fas fa-align-left"></i></span>
                                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"
                                            placeholder="Masukkan deskripsi transaksi" required></textarea>
                                    </div>
                                </div>

                                <!-- Tanggal Transaksi -->
                                <div class="form-group mb-3">
                                    <label class="form-label" for="edit_tanggal_transaksi_data">Tanggal
                                        Transaksi</label>
                                    <div class="input-group">
                                        <span id="icon-tanggal-edit" class="input-group-text text-white"><i
                                                class="fas fa-calendar-alt"></i></span>
                                        <input type="datetime-local" class="form-control" id="edit_tanggal_transaksi"
                                            name="tanggal_transaksi" required>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script>
            function openEditModal(id, type, jumlah_uang, kategori_transaksi, deskripsi, tanggal_transaksi) {
                let editModal = new bootstrap.Modal(document.getElementById('editModal'));
                document.getElementById('edit_id').value = id;
                document.querySelector(`input[name="type_edit"][value="${type}"]`).checked = true;
                document.getElementById('edit_jumlah_uang').value = jumlah_uang.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                document.getElementById('edit_deskripsi').value = deskripsi;
                document.getElementById('edit_tanggal_transaksi').value = tanggal_transaksi;

                // Cek apakah kategori_transaksi ada dalam opsi yang tersedia
                const kategoriSelect = document.getElementById('edit_kategori_transaksi');
                const kategoriOptions = Array.from(kategoriSelect.options).map(option => option.value);

                if (kategoriOptions.includes(kategori_transaksi)) {
                    kategoriSelect.value = kategori_transaksi;
                    document.getElementById('kategori_lainnya_container_edit').style.display = 'none';
                    document.getElementById('kategori_edit_lainnya').removeAttribute('required');
                } else {
                    kategoriSelect.value = 'lainnya';
                    document.getElementById('kategori_lainnya_container_edit').style.display = 'block';
                    document.getElementById('kategori_edit_lainnya').value =
                        kategori_transaksi; // Set nilai kategori lainnya
                    document.getElementById('kategori_edit_lainnya').setAttribute('required', 'required');
                }

                updateColorsEdit();
                editModal.show();
            }

            // Format jumlah uang dengan titik setiap tiga angka
            const jumlahUangInputedit = document.getElementById('edit_jumlah_uang');
            jumlahUangInputedit.addEventListener('input', function(e) {
                let cleanValue = e.target.value.replace(/\D/g, '');
                e.target.value = cleanValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            });
            jumlahUangInputedit.form.addEventListener('submit', function() {
                jumlahUangInputedit.value = jumlahUangInputedit.value.replace(/\./g, '');
            });

            // Tampilkan input "Kategori Lainnya" jika dipilih
            const kategoriSelectedit = document.getElementById('edit_kategori_transaksi');
            const kategoriLainnyaContaineredit = document.getElementById('kategori_lainnya_container_edit');
            const kategoriLainnyaInputedit = document.getElementById('kategori_edit_lainnya');
            kategoriSelectedit.addEventListener('change', function() {
                if (this.value === 'lainnya') {
                    kategoriLainnyaContaineredit.style.display = 'block';
                    kategoriLainnyaInputedit.setAttribute('required', 'required');
                } else {
                    kategoriLainnyaContaineredit.style.display = 'none';
                    kategoriLainnyaInputedit.removeAttribute('required');
                }
            });

            function updateColorsEdit() {
                const type = document.querySelector('input[name="type_edit"]:checked').value;
                const color = (type === 'pemasukan') ? 'bg-success' : 'bg-danger';

                // Update icon colors based on selected transaction type
                document.getElementById('icon-jumlah-edit').className = `input-group-text text-white ${color}`;
                document.getElementById('icon-kategori-edit').className = `input-group-text text-white ${color}`;
                document.getElementById('icon-kategorilainnya_edit').className = `input-group-text text-white ${color}`;
                document.getElementById('icon-deskripsi-edit').className = `input-group-text text-white ${color}`;
                document.getElementById('icon-tanggal-edit').className = `input-group-text text-white ${color}`;
            }

            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll('input[name="type_edit"]').forEach((input) => {
                    input.addEventListener('change', updateColorsEdit);
                });
            });
            </script>


            <?php include 'fitur/footer.php'; ?>
        </div>
    </div>


    <?php include 'fitur/js.php'; ?>
</body>

</html>