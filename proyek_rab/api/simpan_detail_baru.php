<?php
// api/simpan_detail_baru.php (Versi Final dengan Nilai Default 0 untuk Revisi)
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['rab_entry_id']) || !isset($data['details'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

$rab_entry_id = (int)$data['rab_entry_id'];
$details_data = $data['details'];

mysqli_begin_transaction($koneksi);

try {
    $stmt_check = mysqli_prepare($koneksi, "SELECT status_anggaran FROM RAB_Entry WHERE id_rab_entry = ?");
    mysqli_stmt_bind_param($stmt_check, "i", $rab_entry_id);
    mysqli_stmt_execute($stmt_check);
    $entry = mysqli_stmt_get_result($stmt_check)->fetch_assoc();
    $status_anggaran = $entry['status_anggaran'] ?? 'DRAFT';

    if (!in_array($status_anggaran, ['DRAFT', 'DITOLAK', 'REVISI_DRAFT', 'REVISI_DITOLAK'])) {
        throw new Exception("Gagal. Anggaran tidak dalam status yang bisa diedit.");
    }
    
    $is_revision = in_array($status_anggaran, ['REVISI_DRAFT', 'REVISI_DITOLAK']);

    function simpan_rincian_baru_rekursif($koneksi, $rab_entry_id, $details, $parent_id, $is_revision) {
        if ($is_revision) {
            // --- PERBAIKAN DI SINI: Query ini sekarang juga mengisi kolom utama dengan nilai 0 ---
            $sql = "INSERT INTO RAB_Detail (
                        rab_entry_id, parent_detail_id, uraian_pekerjaan, 
                        harga_satuan, total_biaya, volume_data, 
                        harga_satuan_revisi, total_biaya_revisi, volume_data_revisi, 
                        status_revisi
                    ) VALUES (?, ?, ?, 0.00, 0.00, '[]', ?, ?, ?, 'REVISI')";
            $stmt = mysqli_prepare($koneksi, $sql);
        } else {
            $sql = "INSERT INTO RAB_Detail (rab_entry_id, parent_detail_id, uraian_pekerjaan, harga_satuan, total_biaya, volume_data) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $sql);
        }
        
        foreach ($details as $item) {
            $uraian = $item['uraian'] ?? '';
            $harga_satuan = !empty($item['harga_satuan']) ? (float)str_replace('.', '', $item['harga_satuan']) : null;
            $volumes = $item['volumes'] ?? [];
            $volume_data_json = json_encode($volumes);
            
            $total_volume = 1;
            if (!empty($volumes)) { foreach ($volumes as $vol) { $total_volume *= (float)($vol['volume'] ?? 0); } } else { $total_volume = 0; }
            $total_biaya = $total_volume * ($harga_satuan ?? 0);

            if ($is_revision) {
                // Bind parameter hanya untuk kolom revisi, karena kolom utama sudah di-hardcode '0'
                mysqli_stmt_bind_param($stmt, "iisdds", $rab_entry_id, $parent_id, $uraian, $harga_satuan, $total_biaya, $volume_data_json);
            } else {
                mysqli_stmt_bind_param($stmt, "iisdds", $rab_entry_id, $parent_id, $uraian, $harga_satuan, $total_biaya, $volume_data_json);
            }
            mysqli_stmt_execute($stmt);
            $new_detail_id = mysqli_insert_id($koneksi);

            if ($new_detail_id > 0 && !empty($item['children'])) {
                simpan_rincian_baru_rekursif($koneksi, $rab_entry_id, $item['children'], $new_detail_id, $is_revision);
            }
        }
    }

    simpan_rincian_baru_rekursif($koneksi, $rab_entry_id, $details_data, null, $is_revision);
    
    if ($is_revision) {
        mysqli_query($koneksi, "UPDATE RAB_Entry SET memiliki_revisi = TRUE WHERE id_rab_entry = $rab_entry_id");
    }

    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Detail baru berhasil disimpan!']);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
mysqli_close($koneksi);
?>