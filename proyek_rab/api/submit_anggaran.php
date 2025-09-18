<?php
// api/submit_anggaran.php (Versi Final)
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$kegiatan_id = $data['kegiatan_id'] ?? 0;
$kro_id = $data['kro_id'] ?? '';
$bagian_id_user = $_SESSION['bagian_id'] ?? 0;

if (empty($kegiatan_id) || empty($kro_id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Informasi Kegiatan atau KRO tidak lengkap.']);
    exit;
}

mysqli_begin_transaction($koneksi);
try {
    $sql_ro = "SELECT ro_id FROM par_ro WHERE kegiatan_id = ? AND kro_id = ?";
    $params = [$kegiatan_id, $kro_id];
    $types = "is";

    // Filter berdasarkan bagian PENGGUNA yang sedang login, jika bukan admin
    if ($_SESSION['user_role'] !== 'admin' && !empty($bagian_id_user)) {
        $sql_ro .= " AND bagian_id = ?";
        $params[] = $bagian_id_user;
        $types .= "i";
    }
    
    $stmt_ro = mysqli_prepare($koneksi, $sql_ro);
    mysqli_stmt_bind_param($stmt_ro, $types, ...$params);
    mysqli_stmt_execute($stmt_ro);
    $result_ro = mysqli_stmt_get_result($stmt_ro);
    
    $ro_ids = [];
    while ($row = mysqli_fetch_assoc($result_ro)) {
        $ro_ids[] = $row['ro_id'];
    }

    if (empty($ro_ids)) {
        throw new Exception("Tidak ada data anggaran yang bisa diajukan untuk kombinasi ini.");
    }

    $placeholders = implode(',', array_fill(0, count($ro_ids), '?'));
    $types_update = str_repeat('s', count($ro_ids));

    $sql_update = "UPDATE RAB_Entry SET status_anggaran = 'MENUNGGU_VERIFIKASI' WHERE ro_id IN ($placeholders) AND status_anggaran = 'DRAFT'";
    $stmt_update = mysqli_prepare($koneksi, $sql_update);
    mysqli_stmt_bind_param($stmt_update, $types_update, ...$ro_ids);
    
    if (!mysqli_stmt_execute($stmt_update)) {
        throw new Exception("Gagal memperbarui status anggaran.");
    }
    if (mysqli_stmt_affected_rows($stmt_update) === 0) {
        throw new Exception("Tidak ada anggaran berstatus DRAFT yang dapat diajukan.");
    }

    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Anggaran berhasil diajukan.']);
} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
mysqli_close($koneksi);
?>