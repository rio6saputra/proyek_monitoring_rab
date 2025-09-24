<?php
// api/get_laporan_filters.php
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Kode status 'Forbidden'
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit;
}

$response = [
    'biro' => [],
    'bagian' => [],
    'kegiatan' => [],
    'kro' => []
];

// 1. Get Biro
$result_biro = mysqli_query($koneksi, "SELECT biro_id, nama_biro FROM par_rab_biro ORDER BY nama_biro");
while ($row = mysqli_fetch_assoc($result_biro)) {
    $response['biro'][] = $row;
}

// 2. Get Bagian
$result_bagian = mysqli_query($koneksi, "SELECT bagian_id, nama_bagian, biro_id FROM par_rab_bagian ORDER BY nama_bagian");
while ($row = mysqli_fetch_assoc($result_bagian)) {
    $response['bagian'][] = $row;
}

// 3. Get Kegiatan (yang terpakai di par_ro)
$result_kegiatan = mysqli_query($koneksi, "SELECT DISTINCT keg.kegiatan_id, keg.nama_kegiatan FROM par_rab_kegiatan keg JOIN par_ro ro ON keg.kegiatan_id = ro.kegiatan_id ORDER BY keg.nama_kegiatan");
while ($row = mysqli_fetch_assoc($result_kegiatan)) {
    $response['kegiatan'][] = $row;
}

// 4. Get KRO (yang terpakai di par_ro)
$result_kro = mysqli_query($koneksi, "SELECT DISTINCT kro.kro_id, kro.nama_kro FROM par_rab_kro kro JOIN par_ro ro ON kro.kro_id = ro.kro_id ORDER BY kro.nama_kro");
while ($row = mysqli_fetch_assoc($result_kro)) {
    $response['kro'][] = $row;
}

echo json_encode($response);
mysqli_close($koneksi);
?>