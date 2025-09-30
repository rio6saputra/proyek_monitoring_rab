<?php
// api/hapus_detail.php (Versi Final dengan Hard Delete untuk Revisi)
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) { 
    http_response_code(403); 
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']); 
    exit; 
}

/**
 * Fungsi rekursif untuk mengumpulkan semua ID dalam satu grup (induk dan semua anak).
 */
function kumpulkan_semua_id_grup($koneksi, $parent_id, &$ids_to_process) {
    $ids_to_process[] = $parent_id;
    $stmt_children = mysqli_prepare($koneksi, "SELECT id_rab_detail FROM RAB_Detail WHERE parent_detail_id = ?");
    mysqli_stmt_bind_param($stmt_children, "i", $parent_id);
    mysqli_stmt_execute($stmt_children);
    $result_children = mysqli_stmt_get_result($stmt_children);
    while ($child = mysqli_fetch_assoc($result_children)) {
        kumpulkan_semua_id_grup($koneksi, $child['id_rab_detail'], $ids_to_process);
    }
    mysqli_stmt_close($stmt_children);
}

// Ambil data ID dari request
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['id_rab_detail'])) { 
    http_response_code(400); 
    echo json_encode(['status' => 'error', 'message' => 'ID detail tidak valid.']); 
    exit; 
}

$id_from_user = (int)$data['id_rab_detail'];

mysqli_begin_transaction($koneksi);

try {
    // Cek status anggaran dan dapatkan parent_id
    $stmt_check = mysqli_prepare($koneksi, "SELECT e.status_anggaran, d.parent_detail_id FROM RAB_Entry e JOIN RAB_Detail d ON e.id_rab_entry = d.rab_entry_id WHERE d.id_rab_detail = ?");
    mysqli_stmt_bind_param($stmt_check, "i", $id_from_user);
    mysqli_stmt_execute($stmt_check);
    $detail_info = mysqli_stmt_get_result($stmt_check)->fetch_assoc();
    mysqli_stmt_close($stmt_check);

    if (!$detail_info) { 
        throw new Exception("Detail atau Akun induk tidak ditemukan."); 
    }
    
    $current_status = $detail_info['status_anggaran'];

    // Pastikan anggaran dalam status yang bisa diedit/dihapus
    if (!in_array($current_status, ['DRAFT', 'DITOLAK', 'REVISI_DRAFT'])) {
        throw new Exception("Gagal. Anggaran harus berstatus DRAFT, DITOLAK, atau REVISI_DRAFT.");
    }
    
    // Tentukan target penghapusan grup
    $id_target_grup = $id_from_user;
    if ($detail_info['parent_detail_id'] !== null) {
        $id_target_grup = (int)$detail_info['parent_detail_id'];
    }

    // Kumpulkan semua ID yang akan dihapus
    $all_ids_to_delete = [];
    kumpulkan_semua_id_grup($koneksi, $id_target_grup, $all_ids_to_delete);

    if (empty($all_ids_to_delete)) {
        throw new Exception("Tidak ada detail yang ditemukan untuk dihapus.");
    }

    // --- PERUBAHAN LOGIKA UTAMA ---
    // Logika soft delete dihapus. Sekarang semua status yang diizinkan akan melakukan hard delete.
    
    $placeholders = implode(',', array_fill(0, count($all_ids_to_delete), '?'));
    $types = str_repeat('i', count($all_ids_to_delete));
    
    $sql_delete = "DELETE FROM RAB_Detail WHERE id_rab_detail IN ($placeholders)";
    $stmt_delete = mysqli_prepare($koneksi, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, $types, ...$all_ids_to_delete);
    mysqli_stmt_execute($stmt_delete);
    
    $message = 'Rincian Telah Di Hapus.';

    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => $message]);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>