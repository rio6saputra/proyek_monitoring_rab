<?php
// api/mulai_revisi.php (Versi dengan Pengecekan `revisi_ke`)
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
    // Cari semua RO ID yang relevan untuk anggaran ini
    $sql_ro = "SELECT ro_id FROM par_ro WHERE kegiatan_id = ? AND kro_id = ?";
    $params = [$kegiatan_id, $kro_id];
    $types = "is";

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
    while ($row = mysqli_fetch_assoc($result_ro)) { $ro_ids[] = $row['ro_id']; }

    if (empty($ro_ids)) {
        throw new Exception("Tidak ada data anggaran yang ditemukan untuk direvisi.");
    }

    $placeholders = implode(',', array_fill(0, count($ro_ids), '?'));

    // --- LOGIKA BARU DIMULAI DI SINI ---
    
    // 1. Cek nomor revisi saat ini dari salah satu detail (kita asumsikan semua sama)
    $stmt_check_revisi = mysqli_prepare($koneksi, "SELECT revisi_ke FROM RAB_Detail WHERE rab_entry_id IN (SELECT id_rab_entry FROM RAB_Entry WHERE ro_id IN ($placeholders)) LIMIT 1");
    mysqli_stmt_bind_param($stmt_check_revisi, str_repeat('s', count($ro_ids)), ...$ro_ids);
    mysqli_stmt_execute($stmt_check_revisi);
    $result_revisi = mysqli_stmt_get_result($stmt_check_revisi);
    $revisi_data = mysqli_fetch_assoc($result_revisi);
    $current_revisi_ke = $revisi_data ? (int)$revisi_data['revisi_ke'] : 0;
    
    // 2. Lakukan "auto-copy" HANYA jika ini revisi pertama (revisi_ke = 0)
    if ($current_revisi_ke == 0) {
        $sql_copy = "UPDATE RAB_Detail SET
                        harga_satuan_revisi = harga_satuan,
                        total_biaya_revisi = total_biaya,
                        volume_data_revisi = volume_data
                     WHERE rab_entry_id IN (SELECT id_rab_entry FROM RAB_Entry WHERE ro_id IN ($placeholders))";
        $stmt_copy = mysqli_prepare($koneksi, $sql_copy);
        mysqli_stmt_bind_param($stmt_copy, str_repeat('s', count($ro_ids)), ...$ro_ids);
        mysqli_stmt_execute($stmt_copy);
    }
    
    // 3. Naikkan nomor revisi untuk semua detail terkait
    $sql_increment_revisi = "UPDATE RAB_Detail SET revisi_ke = revisi_ke + 1 WHERE rab_entry_id IN (SELECT id_rab_entry FROM RAB_Entry WHERE ro_id IN ($placeholders))";
    $stmt_increment_revisi = mysqli_prepare($koneksi, $sql_increment_revisi);
    mysqli_stmt_bind_param($stmt_increment_revisi, str_repeat('s', count($ro_ids)), ...$ro_ids);
    mysqli_stmt_execute($stmt_increment_revisi);

    // 4. UPDATE status dari DISETUJUI menjadi REVISI_DRAFT
    $sql_update = "UPDATE RAB_Entry SET status_anggaran = 'REVISI_DRAFT' 
                   WHERE ro_id IN ($placeholders) AND status_anggaran = 'DISETUJUI'";
    $stmt_update = mysqli_prepare($koneksi, $sql_update);
    mysqli_stmt_bind_param($stmt_update, str_repeat('s', count($ro_ids)), ...$ro_ids);
    mysqli_stmt_execute($stmt_update);
    if (mysqli_stmt_affected_rows($stmt_update) === 0) {
        throw new Exception("Tidak ada anggaran berstatus DISETUJUI yang dapat direvisi.");
    }
    // --- LOGIKA BARU SELESAI ---

    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Mode revisi telah diaktifkan.']);
} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
mysqli_close($koneksi);
?>