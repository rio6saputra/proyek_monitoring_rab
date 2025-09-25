<?php
// api/hapus_ro.php
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Kode status 'Forbidden'
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (empty($data['ro_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID RO tidak ditemukan.']);
    exit;
}

$ro_id = $data['ro_id'];

mysqli_begin_transaction($koneksi);

try {
    $sql = "DELETE FROM PAR_RO WHERE ro_id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "s", $ro_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Gagal menghapus entri RO dari database.");
    }

    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Entri RO berhasil dihapus.']);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>