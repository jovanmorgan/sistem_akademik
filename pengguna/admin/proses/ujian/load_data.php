 <div id="load_data">
     <section class="section">
         <div class="row">
             <div class="col-lg-12">
                 <div class="card">
                     <div class="card-body text-center">
                         <!-- Search Form -->
                         <form method="GET" action="">
                             <div class="input-group mt-3">
                                 <input type="text" class="form-control" placeholder="Cari Data Siswa..." name="search"
                                     value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                 <button class="btn btn-outline-secondary" type="submit">Cari</button>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
     </section>

     <?php
        include '../../../../keamanan/koneksi.php';

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
                                                 <td style="display: flex; justify-content: center; gap: 10px;">
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
                                         <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
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