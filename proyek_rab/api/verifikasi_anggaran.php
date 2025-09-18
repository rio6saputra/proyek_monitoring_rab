<?php
// api/verifikasi_anggaran.php (Versi Perbaikan)
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

// Keamanan: Hanya Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Hanya admin yang dapat melakukan verifikasi.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$kegiatan_id = $data['kegiatan_id'] ?? 0;
$kro_id = $data['kro_id'] ?? '';
$bagian_id = $data['bagian_id'] ?? 0;
$action = $data['action'] ?? ''; // 'setujui' atau 'tolak'

if (empty($kegiatan_id) || empty($kro_id) || empty($bagian_id) || !in_array($action, ['setujui', 'tolak'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Informasi tidak lengkap atau aksi tidak valid.']);
    exit;
}

// Tentukan status baru dan status lama berdasarkan aksi
$new_status = ($action === 'setujui') ? 'DISETUJUI' : 'DITOLAK';
$old_status = 'MENUNGGU_VERIFIKASI';

mysqli_begin_transaction($koneksi);

try {
    // Ambil semua RO ID yang relevan untuk kombinasi filter
    $sql_ro = "SELECT ro_id FROM par_ro WHERE kegiatan_id = ? AND kro_id = ? AND bagian_id = ?";
    $stmt_ro = mysqli_prepare($koneksi, $sql_ro);
    mysqli_stmt_bind_param($stmt_ro, "isi", $kegiatan_id, $kro_id, $bagian_id);
    mysqli_stmt_execute($stmt_ro);
    $result_ro = mysqli_stmt_get_result($stmt_ro);
    
    $ro_ids = [];
    while ($row = mysqli_fetch_assoc($result_ro)) {
        $ro_ids[] = $row['ro_id'];
    }

    if (empty($ro_ids)) {
        throw new Exception("Tidak ada data anggaran yang ditemukan untuk diverifikasi.");
    }

    // Ubah status semua RAB_Entry yang relevan
    $placeholders = implode(',', array_fill(0, count($ro_ids), '?'));
    
    // [PERBAIKAN] Gabungkan semua parameter ke dalam satu array
    $params = array_merge([$new_status], $ro_ids, [$old_status]);
    $types = 's' . str_repeat('s', count($ro_ids)) . 's';

    $sql_update = "UPDATE RAB_Entry SET status_anggaran = ? WHERE ro_id IN ($placeholders) AND status_anggaran = ?";
    $stmt_update = mysqli_prepare($koneksi, $sql_update);
    
    // Gunakan call_user_func_array untuk binding dinamis yang aman
    mysqli_stmt_bind_param($stmt_update, $types, ...$params);
    
    if (!mysqli_stmt_execute($stmt_update)) {
        throw new Exception("Gagal memperbarui status anggaran: " . mysqli_stmt_error($stmt_update));
    }

    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Anggaran telah berhasil di-' . $action . '.']);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>