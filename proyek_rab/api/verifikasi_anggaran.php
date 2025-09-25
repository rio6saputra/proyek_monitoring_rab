<?php
// api/verifikasi_anggaran.php (Versi Final dengan Logika Persetujuan Revisi)
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

// Keamanan: Hanya Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
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

mysqli_begin_transaction($koneksi);

try {
    // 1. Dapatkan semua ID RAB_Entry yang relevan
    $sql_ro = "SELECT ro_id FROM par_ro WHERE kegiatan_id = ? AND kro_id = ? AND bagian_id = ?";
    $stmt_ro = mysqli_prepare($koneksi, $sql_ro);
    mysqli_stmt_bind_param($stmt_ro, "isi", $kegiatan_id, $kro_id, $bagian_id);
    mysqli_stmt_execute($stmt_ro);
    $result_ro = mysqli_stmt_get_result($stmt_ro);
    $ro_ids = [];
    while ($row = mysqli_fetch_assoc($result_ro)) { $ro_ids[] = $row['ro_id']; }

    if (empty($ro_ids)) {
        throw new Exception("Tidak ada data anggaran yang ditemukan untuk diverifikasi.");
    }
    $ro_placeholders = implode(',', array_fill(0, count($ro_ids), '?'));
    
    // Dapatkan semua ID RAB_Entry untuk operasi di tabel detail
    $sql_entry_ids = "SELECT id_rab_entry FROM RAB_Entry WHERE ro_id IN ($ro_placeholders)";
    $stmt_entry_ids = mysqli_prepare($koneksi, $sql_entry_ids);
    mysqli_stmt_bind_param($stmt_entry_ids, str_repeat('s', count($ro_ids)), ...$ro_ids);
    mysqli_stmt_execute($stmt_entry_ids);
    $result_entry_ids = mysqli_stmt_get_result($stmt_entry_ids);
    $entry_ids = [];
    while ($row = mysqli_fetch_assoc($result_entry_ids)) { $entry_ids[] = $row['id_rab_entry']; }
    $entry_placeholders = implode(',', array_fill(0, count($entry_ids), '?'));


    // 2. Cek status saat ini untuk menentukan alur
    $stmt_check = mysqli_prepare($koneksi, "SELECT status_anggaran FROM RAB_Entry WHERE id_rab_entry IN ($entry_placeholders) LIMIT 1");
    mysqli_stmt_bind_param($stmt_check, str_repeat('i', count($entry_ids)), ...$entry_ids);
    mysqli_stmt_execute($stmt_check);
    $entry_data = mysqli_stmt_get_result($stmt_check)->fetch_assoc();
    $current_status = $entry_data ? $entry_data['status_anggaran'] : null;

    if ($action === 'setujui') {
        // --- LOGIKA PERSETUJUAN ---
        if ($current_status === 'REVISI_MENUNGGU_VERIFIKASI') {
            // JIKA INI PERSETUJUAN REVISI, LAKUKAN "PERGESERAN DATA"
            // Salin data dari kolom _revisi ke kolom utama untuk semua detail yang statusnya REVISI
            $sql_shift = "UPDATE RAB_Detail SET
                            uraian_pekerjaan = CASE WHEN status_revisi = 'REVISI' THEN uraian_pekerjaan ELSE uraian_pekerjaan END,
                            harga_satuan = CASE WHEN status_revisi = 'REVISI' THEN harga_satuan_revisi ELSE harga_satuan END,
                            total_biaya = CASE WHEN status_revisi = 'REVISI' THEN total_biaya_revisi ELSE total_biaya END,
                            volume_data = CASE WHEN status_revisi = 'REVISI' THEN volume_data_revisi ELSE volume_data END,
                            status_revisi = 'DISETUJUI',
                            harga_satuan_revisi = NULL,
                            total_biaya_revisi = NULL,
                            volume_data_revisi = NULL
                          WHERE rab_entry_id IN ($entry_placeholders)";
            $stmt_shift = mysqli_prepare($koneksi, $sql_shift);
            mysqli_stmt_bind_param($stmt_shift, str_repeat('i', count($entry_ids)), ...$entry_ids);
            mysqli_stmt_execute($stmt_shift);
        }
        
        // Tetapkan status akhir menjadi DISETUJUI untuk semua kasus (baik baru maupun revisi)
        $new_status = 'DISETUJUI';
        $sql_update_entry = "UPDATE RAB_Entry SET status_anggaran = ?, memiliki_revisi = FALSE WHERE id_rab_entry IN ($entry_placeholders)";
        $stmt_update_entry = mysqli_prepare($koneksi, $sql_update_entry);
        mysqli_stmt_bind_param($stmt_update_entry, "s" . str_repeat('i', count($entry_ids)), $new_status, ...$entry_ids);
        mysqli_stmt_execute($stmt_update_entry);

    } else { // action === 'tolak'
        // --- LOGIKA PENOLAKAN ---
        // Penolakan revisi akan mengembalikan status ke REVISI_DRAFT agar bisa diedit lagi
        $new_status = ($current_status === 'REVISI_MENUNGGU_VERIFIKASI') ? 'REVISI_DRAFT' : 'DITOLAK';
        $sql_update_entry = "UPDATE RAB_Entry SET status_anggaran = ? WHERE id_rab_entry IN ($entry_placeholders)";
        $stmt_update_entry = mysqli_prepare($koneksi, $sql_update_entry);
        mysqli_stmt_bind_param($stmt_update_entry, "s" . str_repeat('i', count($entry_ids)), $new_status, ...$entry_ids);
        mysqli_stmt_execute($stmt_update_entry);
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