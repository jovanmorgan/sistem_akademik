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
                                                    <input type="text" class="form-control"
                                                        placeholder="Cari Data Siswa..." name="search"
                                                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                                    <button class="btn btn-outline-secondary"
                                                        type="submit">Cari</button>
                                                </div>
                                            </form>
                                            <hr>
                                            <p>Pilih tombol untuk melihat siswa yang lulus dan tidak lulus</p>
                                            <button class="btn btn-outline-danger" type="submit"
                                                onclick="filterStatus('tidak_lulus')">Tidak Lulus</button>
                                            <button class="btn btn-outline-success" type="submit"
                                                onclick="filterStatus('lulus')">Lulus</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Tabel Data Peserta -->
                        <section class="section">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body" style="overflow-x: hidden;">
                                            <div style="overflow-x: auto;">
                                                <?php
                                                include '../../keamanan/koneksi.php';

                                                $search = isset($_GET['search']) ? $_GET['search'] : '';
                                                $status = isset($_GET['status']) ? $_GET['status'] : '';
                                                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                                $limit = 10;
                                                $offset = ($page - 1) * $limit;

                                                // Filter berdasarkan status (lulus atau tidak lulus)
                                                $nilai_filter = $status === 'lulus' ? 'AND nilai >= 70' : ($status === 'tidak_lulus' ? 'AND nilai < 70' : '');

                                                // Query untuk mendapatkan data peserta dengan pencarian dan pagination
                                                $query = "
                                SELECT peserta.id_ujian, peserta.peserta, siswa.nama, peserta.nilai 
                                FROM peserta
                                JOIN siswa ON peserta.peserta = siswa.nis
                                WHERE (peserta.id_ujian LIKE ? OR peserta.peserta LIKE ? OR siswa.nama LIKE ?)
                                $nilai_filter
                                LIMIT ?, ?";
                                                $stmt = $koneksi->prepare($query);
                                                $search_param = '%' . $search . '%';
                                                $stmt->bind_param("sssii", $search_param, $search_param, $search_param, $offset, $limit);
                                                $stmt->execute();
                                                $result = $stmt->get_result();

                                                // Hitung total halaman
                                                $total_query = "
                                SELECT COUNT(*) as total 
                                FROM peserta
                                JOIN siswa ON peserta.peserta = siswa.nis
                                WHERE (peserta.id_ujian LIKE ? OR peserta.peserta LIKE ? OR siswa.nama LIKE ?)
                                $nilai_filter";
                                                $stmt_total = $koneksi->prepare($total_query);
                                                $stmt_total->bind_param("sss", $search_param, $search_param, $search_param);
                                                $stmt_total->execute();
                                                $total_result = $stmt_total->get_result();
                                                $total_row = $total_result->fetch_assoc();
                                                $total_pages = ceil($total_row['total'] / $limit);
                                                ?>

                                                <?php if ($result->num_rows > 0): ?>
                                                    <table class="table table-hover text-center mt-3"
                                                        style="border-collapse: separate; border-spacing: 0;">
                                                        <thead>
                                                            <tr>
                                                                <th>Nomor</th>
                                                                <th>ID Ujian</th>
                                                                <th>Nis</th>
                                                                <th>Nama Peserta</th>
                                                                <th>Nilai</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $nomor = $offset + 1;
                                                            while ($row = $result->fetch_assoc()) :
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $nomor++; ?></td>
                                                                    <td><?php echo htmlspecialchars($row['id_ujian']); ?></td>
                                                                    <td><?php echo htmlspecialchars($row['peserta']); ?></td>
                                                                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                                                    <td><?php echo htmlspecialchars($row['nilai']); ?></td>
                                                                    <td
                                                                        style="display: flex; justify-content: center; gap: 10px;">
                                                                        <button class="btn btn-primary btn-sm"
                                                                            onclick="openEditModal('<?php echo $row['id_ujian']; ?>', '<?php echo $row['peserta']; ?>')">Edit</button>
                                                                        <button class="btn btn-danger btn-sm"
                                                                            onclick="hapus('<?php echo $row['id_ujian']; ?>')">Hapus</button>
                                                                    </td>
                                                                </tr>
                                                            <?php endwhile; ?>
                                                        </tbody>
                                                    </table>
                                                <?php else: ?>
                                                    <p class="text-center mt-4">Data tidak ditemukan.</p>
                                                <?php endif; ?>

                                                <!-- Pagination -->
                                                <nav>
                                                    <ul class="pagination justify-content-center">
                                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                            <li
                                                                class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                                                <a class="page-link"
                                                                    href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&status=<?php echo $status; ?>"><?php echo $i; ?></a>
                                                            </li>
                                                        <?php endfor; ?>
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>


                </div>
            </div>

            <!-- bagian pop up edit dan tambah -->

            <!-- Modal Tambah -->
            <div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="tambahDataModalLabel">Tambah Peserta</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="tambahForm" method="POST" action="proses/tambah_peserta.php">
                                <div class="mb-3">
                                    <label for="id_ujian" class="form-label">ujian</label>
                                    <select id="id_ujian" name="id_ujian" class="form-select" required>
                                        <option value="" disabled selected>Pilih ujian</option>
                                        <?php
                                        // Ambil data ujian dari database
                                        $query_ujian = "SELECT id_ujian, nama_ujian FROM ujian"; // Ganti dengan query yang sesuai
                                        $result_ujian = mysqli_query($koneksi, $query_ujian);
                                        while ($row_ujian = mysqli_fetch_assoc($result_ujian)) {
                                            echo '<option value="' . $row_ujian['id_ujian'] . '">' . $row_ujian['nama_ujian'] . ' (id ujian : ' . $row_ujian['id_ujian'] . ')</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="peserta" class="form-label">Siswa</label>
                                    <select id="peserta" name="peserta" class="form-select" required>
                                        <option value="" disabled selected>Pilih siswa</option>
                                        <?php
                                        // Ambil data siswa dari database
                                        $query_siswa = "SELECT nis, nama FROM siswa"; // Ganti dengan query yang sesuai
                                        $result_siswa = mysqli_query($koneksi, $query_siswa);
                                        while ($row_siswa = mysqli_fetch_assoc($result_siswa)) {
                                            echo '<option value="' . $row_siswa['nis'] . '">' . $row_siswa['nama'] . ' (Nis : ' . $row_siswa['nis'] . ')</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Edit -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Peserta</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editForm" method="POST" action="proses/edit_peserta.php">
                                <input type="hidden" id="edit_id_ujian" name="id_ujian">
                                <div class="mb-3">
                                    <label for="nis" class="form-label">nis</label>
                                    <input type="text" id="edit_nis" name="nis" class="form-control" required>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <?php include 'fitur/footer.php'; ?>
        </div>
    </div>
    <script>
        function filterStatus(status) {
            window.location.href = "?status=" + status; // Arahkan ke halaman dengan status yang dipilih
        }


        function openEditModal(nis, nama, alamat, ) {
            let editModal = new bootstrap.Modal(document.getElementById('editModal'));
            document.getElementById('edit_nis').value = nis;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_alamat').value = alamat;
            editModal.show();
        }
    </script>

    <?php include 'fitur/js.php'; ?>
</body>

</html>