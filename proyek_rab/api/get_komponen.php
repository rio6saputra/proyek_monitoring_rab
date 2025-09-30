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

$ro_id = $_GET['ro_id'] ?? null;
$data = [];

if ($ro_id) {
    $sql = "SELECT * FROM PAR_Komponen WHERE ro_id = ? ORDER BY kode_komponen";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "s", $ro_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

echo json_encode($data);
mysqli_close($koneksi);
?>