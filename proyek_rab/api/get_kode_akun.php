<?php
// Path yang benar: keluar satu folder (../), lalu masuk ke includes/config/
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Kode status 'Forbidden'
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit;
}

$data = [];

// Query untuk mengambil semua data kode akun
$sql = "SELECT id_akun, kode_akun, uraian_akun FROM PAR_Kode_Akun ORDER BY kode_akun";
$result = mysqli_query($koneksi, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

echo json_encode($data);
mysqli_close($koneksi);
?>