 <div id="load_data">
     <section class="section">
         <div class="row">
             <div class="col-lg-12">
                 <div class="card">
                     <div class="card-body text-center">
                         <!-- Search Form -->
                         <form method="GET" action="">
                             <div class="input-group mt-3">
                                 <input type="text" class="form-control" placeholder="Cari Data..." name="search"
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
                                                                echo '� ' . htmlspecialchars($kategori);
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
                                                 <td style="display: flex; justify-content: center; gap: 10px;">
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