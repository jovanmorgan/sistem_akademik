  <!--   Core JS Files   -->
  <script src="../../assets/js/core/jquery.min.js?v=<?= time(); ?>"></script>
  <script src="../../assets/js/core/popper.min.js?v=<?= time(); ?>"></script>
  <script src="../../assets/js/core/bootstrap.min.js?v=<?= time(); ?>"></script>
  <script src="../../assets/js/plugins/perfect-scrollbar.jquery.min.js?v=<?= time(); ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('tambahForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Menghentikan aksi default form submit

        var form = this;
        var formData = new FormData(form);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'proses/<?= $page_title_proses ?>/tambah.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = xhr.responseText.trim();
                console.log(response); // Debugging

                if (response === 'success') {
                    form.reset();
                    document.getElementById('closeTambahModal').click();
                    loadTable(); // reload table data

                    Swal.fire({
                        title: "Berhasil!",
                        text: "Data Berhasil Ditambahkan",
                        icon: "success",
                        timer: 12200, // 1,2 detik
                        showConfirmButton: false, // Tidak menampilkan tombol OK
                    });
                } else if (response === 'transaksi_sukses') {
                    form.reset();
                    document.getElementById('closeTambahModal').click();
                    loadTable(); // reload table data

                    Swal.fire({
                        title: "Transaksi Berhasil!",
                        text: "Data Transaksi Berhasil Disimpan!",
                        icon: "success",
                        timer: 3000,
                        showConfirmButton: false, // Tidak menampilkan tombol OK
                    });
                } else if (response === 'data_sudah_ada') {
                    Swal.fire({
                        title: "Error",
                        text: "Data data sudah ada, silakan pilih data lainnya",
                        icon: "info",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                } else if (response === 'saldo_tidak_cukup') {
                    Swal.fire({
                        title: "Saldo Tidak Cukup",
                        text: "Pengeluaran saldo tidak mencukupi, silakan kurangi pengeluaran saldo Anda !",
                        icon: "info",
                        timer: 3000, // 2 detik
                        showConfirmButton: false,
                    });
                } else if (response === 'data_tidak_lengkap') {
                    Swal.fire({
                        title: "Error",
                        text: "Data yang anda masukan belum lengkap",
                        icon: "info",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: "Gagal menambahkan data",
                        icon: "error",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                }
            } else {
                Swal.fire({
                    title: "Error",
                    text: "Terjadi kesalahan saat mengirim data",
                    icon: "error",
                    timer: 2000, // 2 detik
                    showConfirmButton: false,
                });
            }
        };
        xhr.send(formData);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('editForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Menghentikan aksi default form submit

        var form = this;
        var formData = new FormData(form);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'proses/<?= $page_title_proses ?>/edit.php', true);
        xhr.onload = function() {

            if (xhr.status === 200) {
                var response = xhr.responseText.trim();
                console.log(response); // Debugging

                if (response === 'success') {
                    form.reset();
                    document.getElementById('closeEditModal').click();
                    loadTable(); // reload table data

                    Swal.fire({
                        title: "Berhasil!",
                        text: "Data berhasil diperbarui",
                        icon: "success",
                        timer: 1200, // 1,2 detik
                        showConfirmButton: false, // Tidak menampilkan tombol OK
                    });
                } else if (response === 'transaksi_diperbarui') {
                    form.reset();
                    document.getElementById('closeEditModal').click();
                    loadTable(); // reload table data

                    Swal.fire({
                        title: "Transaksi Diedit!",
                        text: "Data Transaksi Berhasil Diedit!",
                        icon: "success",
                        timer: 3000,
                        showConfirmButton: false, // Tidak menampilkan tombol OK
                    });
                } else if (response === 'data_sudah_ada') {
                    Swal.fire({
                        title: "Error",
                        text: "Data data sudah ada, silakan pilih data lainnya",
                        icon: "info",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                } else if (response === 'saldo_tidak_cukup') {
                    Swal.fire({
                        title: "Saldo Tidak Cukup",
                        text: "Pengeluaran saldo tidak mencukupi, silakan kurangi pengeluaran saldo Anda !",
                        icon: "info",
                        timer: 3000, // 2 detik
                        showConfirmButton: false,
                    });
                } else if (response === 'data_tidak_lengkap') {
                    Swal.fire({
                        title: "Error",
                        text: "Data yang anda masukan belum lengkap",
                        icon: "info",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: "Gagal memperbarui data",
                        icon: "error",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                }
            } else {
                Swal.fire({
                    title: "Error",
                    text: "Terjadi kesalahan saat mengirim data",
                    icon: "error",
                    timer: 2000, // 2 detik
                    showConfirmButton: false,
                });
            }
        };
        xhr.send(formData);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('validasiDataEdit').addEventListener('submit', function(event) {
        event.preventDefault(); // Menghentikan aksi default form submit

        var form = this;
        var formData = new FormData(form);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'proses/<?= $page_title_proses ?>/validasi.php', true);
        xhr.onload = function() {

            if (xhr.status === 200) {
                var response = xhr.responseText.trim();
                console.log(response); // Debugging

                if (response === 'success') {
                    form.reset();
                    document.getElementById('closeValidasi').click();
                    loadTable(); // reload table data

                    Swal.fire({
                        title: "Berhasil!",
                        text: "Data berhasil diperbarui",
                        icon: "success",
                        timer: 1200, // 1,2 detik
                        showConfirmButton: false, // Tidak menampilkan tombol OK
                    });
                } else if (response === 'data_sudah_ada') {
                    Swal.fire({
                        title: "Error",
                        text: "Data data sudah dipromosikan, silakan pilih data data lainnya",
                        icon: "info",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                } else if (response === 'error_password_length') {
                    Swal.fire({
                        title: "Error",
                        text: "Password minimal 8 karakter",
                        icon: "info",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                } else if (response === 'error_password_strength') {
                    Swal.fire({
                        title: "Error",
                        text: "Password harus mengandung angka, huruf kecil dan huruf besar",
                        icon: "info",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                } else if (response === 'no_kk_harus_16') {
                    Swal.fire({
                        title: "Error",
                        text: "Nomor Kartu Keluarga tidak boleh lebih kurang atau lebih dari 16",
                        icon: "info",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                } else if (response === 'no_kk_sudah_ada') {
                    Swal.fire({
                        title: "Error",
                        text: "Data Nomor Kartu Keluarga Sudah ada silakan gunakan Nomor Kartu Keluarga Lainnya",
                        icon: "info",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                } else if (response === 'nik_harus_16') {
                    Swal.fire({
                        title: "Error",
                        text: "Nomor Kartu Keluarga tidak boleh lebih kurang atau lebih dari 16",
                        icon: "info",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                } else if (response === 'nik_sudah_ada') {
                    Swal.fire({
                        title: "Error",
                        text: "Data Nomor Kartu Keluarga Sudah ada silakan gunakan Nomor Kartu Keluarga Lainnya",
                        icon: "info",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                } else if (response === 'data_tidak_lengkap') {
                    Swal.fire({
                        title: "Error",
                        text: "Data yang anda masukan belum lengkap",
                        icon: "info",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: "Gagal memperbarui data",
                        icon: "error",
                        timer: 2000, // 2 detik
                        showConfirmButton: false,
                    });
                }
            } else {
                Swal.fire({
                    title: "Error",
                    text: "Terjadi kesalahan saat mengirim data",
                    icon: "error",
                    timer: 2000, // 2 detik
                    showConfirmButton: false,
                });
            }
        };
        xhr.send(formData);
    });
});

function hapus(id) {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Setelah dihapus, Anda tidak akan dapat memulihkan data ini!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
        dangerMode: true,
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika pengguna mengonfirmasi untuk menghapus
            var xhr = new XMLHttpRequest();

            xhr.open('POST', 'proses/<?= $page_title_proses ?>/hapus.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {

                if (xhr.status === 200) {
                    var response = xhr.responseText.trim();
                    if (response === 'success') {
                        loadTable();
                        Swal.fire({
                            title: 'Sukses!',
                            text: 'Data berhasil dihapus.',
                            icon: 'success',
                            timer: 1200, // 1,2 detik
                            showConfirmButton: false // Menghilangkan tombol OK
                        }).then(() => {
                            location.reload()
                        })
                    } else if (response === 'error') {
                        Swal.fire({
                            title: 'Error',
                            text: 'Gagal menghapus Data.',
                            icon: 'error',
                            timer: 2000, // 2 detik
                            showConfirmButton: false // Menghilangkan tombol OK
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Terjadi kesalahan saat mengirim data.',
                            icon: 'error',
                            timer: 2000, // 2 detik
                            showConfirmButton: false // Menghilangkan tombol OK
                        });
                    }
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengirim data.',
                        icon: 'error',
                        timer: 2000, // 2 detik
                        showConfirmButton: false // Menghilangkan tombol OK
                    });
                }
            };
            xhr.send("id=" + id);
        } else {
            // Jika pengguna membatalkan penghapusan
            Swal.fire({
                title: 'Penghapusan dibatalkan',
                icon: 'info',
                timer: 1500, // 1,5 detik
                showConfirmButton: false // Menghilangkan tombol OK
            });
        }
    });
}

function loadTable() {
    // Get current page and search query from URL
    var currentPage = new URLSearchParams(window.location.search).get('page') || 1;
    var searchQuery = new URLSearchParams(window.location.search).get('search') || '';

    var xhrTable = new XMLHttpRequest();
    xhrTable.onreadystatechange = function() {
        if (xhrTable.readyState == 4 && xhrTable.status == 200) {
            document.getElementById('load_data').innerHTML = xhrTable.responseText;
        }
    };

    // Send request with current page and search query
    xhrTable.open('GET', 'proses/<?= $page_title_proses ?>/load_data.php?page=' + currentPage + '&search=' +
        encodeURIComponent(
            searchQuery), true);
    xhrTable.send();
}
  </script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

  <!--   Core JS Files   -->
  <script src="../../assets/js/core/jquery-3.7.1.min.js?v=<?= time(); ?>"></script>
  <script src="../../assets/js/core/popper.min.js?v=<?= time(); ?>"></script>
  <script src="../../assets/js/core/bootstrap.min.js?v=<?= time(); ?>"></script>

  <!-- jQuery Scrollbar -->
  <script src="../../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js?v=<?= time(); ?>"></script>

  <!-- Chart JS -->
  <script src="../../assets/js/plugin/chart.js/chart.min.js?v=<?= time(); ?>"></script>

  <!-- jQuery Sparkline -->
  <script src="../../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js?v=<?= time(); ?>"></script>

  <!-- Chart Circle -->
  <script src="../../assets/js/plugin/chart-circle/circles.min.js?v=<?= time(); ?>"></script>

  <!-- Datatables -->
  <script src="../../assets/js/plugin/datatables/datatables.min.js?v=<?= time(); ?>"></script>

  <!-- Bootstrap Notify -->
  <script src="../../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js?v=<?= time(); ?>"></script>

  <!-- jQuery Vector Maps -->
  <script src="../../assets/js/plugin/jsvectormap/jsvectormap.min.js?v=<?= time(); ?>"></script>
  <script src="../../assets/js/plugin/jsvectormap/world.js?v=<?= time(); ?>"></script>

  <!-- Sweet Alert -->
  <script src="../../assets/js/plugin/sweetalert/sweetalert.min.js?v=<?= time(); ?>"></script>

  <!-- Kaiadmin JS -->
  <script src="../../assets/js/kaiadmin.min.js?v=<?= time(); ?>"></script>

  <!-- Kaiadmin DEMO methods, don't include it in your project! -->
  <script src="../../assets/js/setting-demo.js?v=<?= time(); ?>"></script>
  <script src="../../assets/js/demo.js?v=<?= time(); ?>"></script>
  <script>
$("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
    type: "line",
    height: "70",
    width: "100%",
    lineWidth: "2",
    lineColor: "#177dff",
    fillColor: "rgba(23, 125, 255, 0.14)",
});

$("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
    type: "line",
    height: "70",
    width: "100%",
    lineWidth: "2",
    lineColor: "#f3545d",
    fillColor: "rgba(243, 84, 93, .14)",
});

$("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
    type: "line",
    height: "70",
    width: "100%",
    lineWidth: "2",
    lineColor: "#ffa534",
    fillColor: "rgba(255, 165, 52, .14)",
});
  </script>