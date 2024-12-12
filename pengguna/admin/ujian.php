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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <?php
                        include '../../keamanan/koneksi.php';

                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $limit = 10;
                        $offset = ($page - 1) * $limit;

                        // Query untuk mendapatkan data ujian dengan join tabel mata pelajaran dan peserta
                        $query = "
    SELECT ujian.id_ujian, ujian.nama_ujian, ujian.id_matpel, ujian.tanggal, 
           mata_pelajaran.nama_matpel, peserta.peserta, siswa.nis, siswa.nama, peserta.nilai
    FROM ujian
    INNER JOIN mata_pelajaran ON ujian.id_matpel = mata_pelajaran.id_matpel
    LEFT JOIN peserta ON ujian.id_ujian = peserta.id_ujian
    LEFT JOIN siswa ON peserta.peserta = siswa.nis
    WHERE ujian.id_ujian LIKE ? OR ujian.nama_ujian LIKE ? OR mata_pelajaran.nama_matpel LIKE ? OR ujian.tanggal LIKE ?
    LIMIT ?, ?";
                        $stmt = $koneksi->prepare($query);
                        $search_param = '%' . $search . '%';
                        $stmt->bind_param("ssssii", $search_param, $search_param, $search_param, $search_param, $offset, $limit);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Hitung total halaman
                        $total_query = "
    SELECT COUNT(*) as total
    FROM ujian
    INNER JOIN mata_pelajaran ON ujian.id_matpel = mata_pelajaran.id_matpel
    LEFT JOIN peserta ON ujian.id_ujian = peserta.id_ujian
    WHERE ujian.id_ujian LIKE ? OR ujian.nama_ujian LIKE ? OR mata_pelajaran.nama_matpel LIKE ? OR ujian.tanggal LIKE ?";
                        $stmt_total = $koneksi->prepare($total_query);
                        $stmt_total->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
                        $stmt_total->execute();
                        $total_result = $stmt_total->get_result();
                        $total_row = $total_result->fetch_assoc();
                        $total_pages = ceil($total_row['total'] / $limit);
                        ?>

                        <!-- Tabel Data Ujian dan Peserta -->
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
                                                                <th>Nomor</th>
                                                                <th>ID Ujian</th>
                                                                <th>Nama Ujian</th>
                                                                <th>Nama Mata Pelajaran</th>
                                                                <th>Tanggal</th>
                                                                <th>Nama Peserta</th>
                                                                <th>NIS</th>
                                                                <th>Nilai</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $nomor = $offset + 1;
                                                            while ($row = $result->fetch_assoc()):
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $nomor++; ?></td>
                                                                    <td><?php echo htmlspecialchars($row['id_ujian']); ?></td>
                                                                    <td><?php echo htmlspecialchars($row['nama_ujian']); ?></td>
                                                                    <td><?php echo htmlspecialchars($row['nama_matpel']); ?>
                                                                    </td>
                                                                    <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                                                    <td><?php echo htmlspecialchars($row['nama']); ?>
                                                                    </td>
                                                                    <td><?php echo htmlspecialchars($row['nis']); ?></td>
                                                                    <td><?php echo htmlspecialchars($row['nilai']); ?></td>
                                                                    <td
                                                                        style="display: flex; justify-content: center; gap: 10px;">
                                                                        <button class="btn btn-primary btn-sm"
                                                                            onclick="openEditModal('<?php echo $row['id_ujian']; ?>', '<?php echo $row['nama_ujian']; ?>', '<?php echo $row['id_matpel']; ?>', '<?php echo $row['tanggal']; ?>', '<?php echo $row['nis']; ?>', '<?php echo $row['nilai']; ?>')">Edit</button>
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
                                                                    href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
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


            <!-- Modal -->
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
                                <div class="mb-3">
                                    <label for="nama_ujian" class="form-label">Nama Ujian</label>
                                    <input type="text" id="nama_ujian" name="nama_ujian" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="peserta" class="form-label">Peserta Ujian</label>
                                    <select id="peserta" name="peserta" class="form-select" required>
                                        <option value="" disabled selected>Pilih Peserta Ujian</option>
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
                                <div class="mb-3">
                                    <label for="id_matpel" class="form-label">Mata Pelajaran</label>
                                    <select id="id_matpel" name="id_matpel" class="form-control" required>
                                        <option value="" disabled selected>Pilih Pelajaran</option>
                                        <?php
                                        $matpel_query = "SELECT id_matpel, nama_matpel FROM mata_pelajaran";
                                        $matpel_result = $koneksi->query($matpel_query);
                                        while ($matpel = $matpel_result->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo $matpel['id_matpel']; ?>">
                                                <?php echo $matpel['nama_matpel']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal</label>
                                    <input type="datetime-local" id="tanggal" name="tanggal" class="form-control"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="nilai" class="form-label">Nilai Ujian</label>
                                    <input type="number" id="nilai" name="nilai" class="form-control" required>
                                </div>

                                <!-- Wrapper for the submit button to align it to the right -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

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
                                <input type="hidden" id="edit_id_ujian" name="id_ujian">

                                <!-- Nama Ujian -->
                                <div class="mb-3">
                                    <label for="edit_nama_ujian" class="form-label">Nama Ujian</label>
                                    <input type="text" id="edit_nama_ujian" name="nama_ujian" class="form-control"
                                        required>
                                </div>

                                <!-- Peserta Ujian -->
                                <div class="mb-3">
                                    <label for="edit_peserta" class="form-label">Peserta Ujian</label>
                                    <select id="edit_peserta" name="peserta" class="form-select" required>
                                        <option value="" disabled selected>Pilih Peserta Ujian</option>
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

                                <!-- Mata Pelajaran -->
                                <div class="mb-3">
                                    <label for="edit_id_matpel" class="form-label">Mata Pelajaran</label>
                                    <select id="edit_id_matpel" name="id_matpel" class="form-control" required>
                                        <option value="" disabled selected>Pilih Pelajaran</option>
                                        <?php
                                        $matpel_query = "SELECT id_matpel, nama_matpel FROM mata_pelajaran";
                                        $matpel_result = $koneksi->query($matpel_query);
                                        while ($matpel = $matpel_result->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo $matpel['id_matpel']; ?>">
                                                <?php echo $matpel['nama_matpel']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Tanggal -->
                                <div class="mb-3">
                                    <label for="edit_tanggal" class="form-label">Tanggal</label>
                                    <input type="datetime-local" id="edit_tanggal" name="tanggal" class="form-control"
                                        required>
                                </div>

                                <!-- Nilai Ujian -->
                                <div class="mb-3">
                                    <label for="edit_nilai" class="form-label">Nilai Ujian</label>
                                    <input type="number" id="edit_nilai" name="nilai" class="form-control" required>
                                </div>

                                <!-- Wrapper for the submit button to align it to the right -->
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
        function openEditModal(id, nama_ujian, id_matpel, tanggal, id_peserta, nilai) {
            let editModal = new bootstrap.Modal(document.getElementById('editModal'));

            // Set value dari data yang dikirim untuk diisi ke dalam form edit
            document.getElementById('edit_id_ujian').value = id;
            document.getElementById('edit_nama_ujian').value = nama_ujian;
            document.getElementById('edit_id_matpel').value = id_matpel;
            document.getElementById('edit_tanggal').value = tanggal;
            document.getElementById('edit_peserta').value = id_peserta;
            document.getElementById('edit_nilai').value = nilai;

            // Menampilkan modal edit
            editModal.show();
        }
    </script>


    <?php include 'fitur/js.php'; ?>
</body>

</html>