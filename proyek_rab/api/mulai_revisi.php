<?php
// api/mulai_revisi.php (Versi Final dengan Penghapusan Catatan Revisi Lama)
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
$user_role = $_SESSION['user_role'] ?? 'Guest';
$bagian_id_user = $_SESSION['bagian_id'] ?? null;

if (empty($kegiatan_id) || empty($kro_id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Informasi Kegiatan atau KRO tidak lengkap.']);
    exit;
}

mysqli_begin_transaction($koneksi);
try {
    // Dapatkan semua RO ID yang relevan berdasarkan hak akses
    $sql_ro = "SELECT ro_id FROM par_ro WHERE kegiatan_id = ? AND kro_id = ?";
    $params = [$kegiatan_id, $kro_id];
    $types = "is";

    if ($user_role !== 'admin') {
        if ($bagian_id_user === null) { throw new Exception("Pengguna tidak memiliki bagian yang terasosiasi."); }
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
    if (empty($ro_ids)) { throw new Exception("Tidak ada data anggaran yang ditemukan untuk kombinasi ini."); }
    $ro_placeholders = implode(',', array_fill(0, count($ro_ids), '?'));

    // Dapatkan semua ID entry yang akan diproses
    $sql_entry_ids = "SELECT id_rab_entry FROM RAB_Entry WHERE ro_id IN ($ro_placeholders)";
    $stmt_entry_ids = mysqli_prepare($koneksi, $sql_entry_ids);
    mysqli_stmt_bind_param($stmt_entry_ids, str_repeat('s', count($ro_ids)), ...$ro_ids);
    mysqli_stmt_execute($stmt_entry_ids);
    $result_entry_ids = mysqli_stmt_get_result($stmt_entry_ids);
    $entry_ids = [];
    while ($row = mysqli_fetch_assoc($result_entry_ids)) { $entry_ids[] = $row['id_rab_entry']; }

    if (!empty($entry_ids)) {
        $entry_placeholders = implode(',', array_fill(0, count($entry_ids), '?'));

        // --- LOGIKA BARU: HAPUS CATATAN REVISI LAMA ---
        // 1. Cek nilai revisi_ke maksimum saat ini
        $stmt_check_revisi = mysqli_prepare($koneksi, "SELECT MAX(revisi_ke) as max_revisi FROM RAB_Detail WHERE rab_entry_id IN ($entry_placeholders)");
        mysqli_stmt_bind_param($stmt_check_revisi, str_repeat('i', count($entry_ids)), ...$entry_ids);
        mysqli_stmt_execute($stmt_check_revisi);
        $revisi_data = mysqli_stmt_get_result($stmt_check_revisi)->fetch_assoc();
        $current_max_revisi = $revisi_data ? (int)$revisi_data['max_revisi'] : 0;
        
        // 2. Jika revisi_ke saat ini >= 1 (artinya ini akan menjadi revisi ke-2 atau lebih), bersihkan catatan
        if ($current_max_revisi >= 1) {
            $stmt_clear_notes = mysqli_prepare($koneksi, "UPDATE RAB_Detail SET catatan_revisi = NULL WHERE rab_entry_id IN ($entry_placeholders)");
            mysqli_stmt_bind_param($stmt_clear_notes, str_repeat('i', count($entry_ids)), ...$entry_ids);
            mysqli_stmt_execute($stmt_clear_notes);
        }
        // --- AKHIR LOGIKA BARU ---
    }

    // Lanjutkan dengan proses revisi progresif seperti sebelumnya
    // LANGKAH A: Lakukan "Pergeseran Data"
    $sql_shift = "UPDATE RAB_Detail SET harga_satuan = IF(harga_satuan_revisi IS NOT NULL, harga_satuan_revisi, harga_satuan), total_biaya = IF(total_biaya_revisi IS NOT NULL, total_biaya_revisi, total_biaya), volume_data = IF(volume_data_revisi IS NOT NULL, volume_data_revisi, volume_data) WHERE rab_entry_id IN (SELECT id_rab_entry FROM RAB_Entry WHERE ro_id IN ($ro_placeholders))";
    $stmt_shift = mysqli_prepare($koneksi, $sql_shift);
    mysqli_stmt_bind_param($stmt_shift, str_repeat('s', count($ro_ids)), ...$ro_ids);
    mysqli_stmt_execute($stmt_shift);

    // LANGKAH B: Lakukan "Auto-Copy"
    $sql_copy = "UPDATE RAB_Detail SET harga_satuan_revisi = harga_satuan, total_biaya_revisi = total_biaya, volume_data_revisi = volume_data, status_revisi = 'ORIGINAL' WHERE rab_entry_id IN (SELECT id_rab_entry FROM RAB_Entry WHERE ro_id IN ($ro_placeholders))";
    $stmt_copy = mysqli_prepare($koneksi, $sql_copy);
    mysqli_stmt_bind_param($stmt_copy, str_repeat('s', count($ro_ids)), ...$ro_ids);
    mysqli_stmt_execute($stmt_copy);

    // LANGKAH C: Naikkan nomor revisi dan ubah status utama
    $sql_increment_revisi = "UPDATE RAB_Detail SET revisi_ke = revisi_ke + 1 WHERE rab_entry_id IN (SELECT id_rab_entry FROM RAB_Entry WHERE ro_id IN ($ro_placeholders))";
    $stmt_increment_revisi = mysqli_prepare($koneksi, $sql_increment_revisi);
    mysqli_stmt_bind_param($stmt_increment_revisi, str_repeat('s', count($ro_ids)), ...$ro_ids);
    mysqli_stmt_execute($stmt_increment_revisi);

    $sql_update_entry = "UPDATE RAB_Entry SET status_anggaran = 'REVISI_DRAFT' WHERE ro_id IN ($ro_placeholders) AND status_anggaran = 'DISETUJUI'";
    $stmt_update_entry = mysqli_prepare($koneksi, $sql_update_entry);
    mysqli_stmt_bind_param($stmt_update_entry, str_repeat('s', count($ro_ids)), ...$ro_ids);
    mysqli_stmt_execute($stmt_update_entry);
    if (mysqli_stmt_affected_rows($stmt_update_entry) === 0) {
        throw new Exception("Tidak ada anggaran berstatus DISETUJUI yang dapat direvisi.");
    }
    
    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Mode revisi telah diaktifkan. Catatan lama dibersihkan.']);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
mysqli_close($koneksi);
?>