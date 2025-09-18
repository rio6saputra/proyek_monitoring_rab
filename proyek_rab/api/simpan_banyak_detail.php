<?php
// api/simpan_banyak_detail.php (Versi Final dengan Hirarki)
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
    // Cek status, hanya boleh DRAFT
    $stmt_check = mysqli_prepare($koneksi, "SELECT status_anggaran FROM RAB_Entry WHERE id_rab_entry = ?");
    mysqli_stmt_bind_param($stmt_check, "i", $rab_entry_id);
    mysqli_stmt_execute($stmt_check);
    $entry = mysqli_stmt_get_result($stmt_check)->fetch_assoc();

    if (!$entry || $entry['status_anggaran'] !== 'DRAFT') {
        throw new Exception("Gagal menyimpan. Anggaran tidak dalam status DRAFT.");
    }

    // Hapus semua detail LAMA yang terkait
    $stmt_delete = mysqli_prepare($koneksi, "DELETE FROM RAB_Detail WHERE rab_entry_id = ?");
    mysqli_stmt_bind_param($stmt_delete, "i", $rab_entry_id);
    mysqli_stmt_execute($stmt_delete);

    // Fungsi rekursif untuk menyimpan detail BARU
    function simpan_rincian_baru($koneksi, $rab_entry_id, $details, $parent_id = null) {
        $sql_detail = "INSERT INTO RAB_Detail (rab_entry_id, parent_detail_id, uraian_pekerjaan, harga_satuan, total_biaya) VALUES (?, ?, ?, ?, ?)";
        $stmt_detail = mysqli_prepare($koneksi, $sql_detail);
        
        $sql_volume = "INSERT INTO RAB_Volume (rab_detail_id, volume, satuan) VALUES (?, ?, ?)";
        $stmt_volume = mysqli_prepare($koneksi, $sql_volume);

        foreach ($details as $item) {
            $uraian = $item['uraian'] ?? '';
            $harga_satuan = !empty($item['harga_satuan']) ? (float)$item['harga_satuan'] : null;
            
            $total_volume = 1;
            if (!empty($item['volumes'])) {
                foreach ($item['volumes'] as $vol) { $total_volume *= (float)$vol['volume']; }
            } else { $total_volume = 0; }
            $total_biaya = $total_volume * ($harga_satuan ?? 0);

            mysqli_stmt_bind_param($stmt_detail, "iisdd", $rab_entry_id, $parent_id, $uraian, $harga_satuan, $total_biaya);
            mysqli_stmt_execute($stmt_detail);
            $new_detail_id = mysqli_insert_id($koneksi);

            if ($new_detail_id > 0 && !empty($item['volumes'])) {
                foreach($item['volumes'] as $vol) {
                    if(!empty($vol['volume']) && !empty($vol['satuan'])) {
                        mysqli_stmt_bind_param($stmt_volume, "ids", $new_detail_id, $vol['volume'], $vol['satuan']);
                        mysqli_stmt_execute($stmt_volume);
                    }
                }
            }
            if ($new_detail_id > 0 && !empty($item['children'])) {
                simpan_rincian_baru($koneksi, $rab_entry_id, $item['children'], $new_detail_id);
            }
        }
    }

    if (!empty($details_data)) {
        simpan_rincian_baru($koneksi, $rab_entry_id, $details_data);
    }
    
    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Detail RAB berhasil diperbarui!']);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
mysqli_close($koneksi);
?>