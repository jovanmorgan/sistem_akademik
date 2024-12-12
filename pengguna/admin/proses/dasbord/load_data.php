// load_data.php
<?php
include '../../keamanan/koneksi.php';

$tanggal = $_GET['tanggal'];
$query = "SELECT * FROM transaksi WHERE DATE(tanggal_transaksi) = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $tanggal);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $saldo_sebelumnya = floatval($row['saldo_sebelumnya']);
    $jumlah_uang = floatval($row['jumlah_uang']);
    $saldo_sesudah = ($row['type'] === 'pemasukan') ? $saldo_sebelumnya + $jumlah_uang : $saldo_sebelumnya - $jumlah_uang;
    $data[] = [
        'type' => $row['type'],
        'jumlah_uang' => $jumlah_uang,
        'saldo_sesudah' => $saldo_sesudah,
    ];
}

echo json_encode($data);
?>