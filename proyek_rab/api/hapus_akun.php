<?php
// api/hapus_akun.php (Versi Final dengan Pengecekan Status & Error Handling)
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['id_rab_entry'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID RAB Entry tidak valid.']);
    exit;
}

$id_rab_entry = (int)$data['id_rab_entry'];

mysqli_begin_transaction($koneksi);

try {
    // Langkah 1: Cek status anggaran sebelum menghapus
    $stmt_check = mysqli_prepare($koneksi, "SELECT status_anggaran FROM RAB_Entry WHERE id_rab_entry = ?");
    mysqli_stmt_bind_param($stmt_check, "i", $id_rab_entry);
    mysqli_stmt_execute($stmt_check);
    $entry = mysqli_stmt_get_result($stmt_check)->fetch_assoc();
    mysqli_stmt_close($stmt_check);

    if (!$entry) {
        throw new Exception("Entri Akun tidak ditemukan di database.");
    }

    // Langkah 2: Validasi status
    if ($entry['status_anggaran'] !== 'DRAFT') {
        throw new Exception("Gagal menghapus. Anggaran ini tidak dalam status DRAFT.");
    }

    // Langkah 3: Jika validasi lolos, lanjutkan proses hapus
    $stmt_delete = mysqli_prepare($koneksi, "DELETE FROM RAB_Entry WHERE id_rab_entry = ?");
    mysqli_stmt_bind_param($stmt_delete, "i", $id_rab_entry);
    
    if (!mysqli_stmt_execute($stmt_delete)) {
        throw new Exception("Database error: " . mysqli_stmt_error($stmt_delete));
    }
    mysqli_stmt_close($stmt_delete);

    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Detail berhasil dihapus.']);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>