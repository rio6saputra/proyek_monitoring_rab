<?php
// api/edit_akun.php
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

if (empty($data['id_rab_entry']) || empty($data['id_akun']) || empty($data['kode_akun'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

$id_rab_entry = (int)$data['id_rab_entry'];
$id_akun = (int)$data['id_akun'];
$kode_akun = $data['kode_akun'];

mysqli_begin_transaction($koneksi);

try {
    $sql = "UPDATE RAB_Entry SET id_akun = ?, kode_akun = ? WHERE id_rab_entry = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "isi", $id_akun, $kode_akun, $id_rab_entry);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Gagal mengupdate entri Akun dari database.");
    }

    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Entri Akun berhasil diperbarui.']);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>