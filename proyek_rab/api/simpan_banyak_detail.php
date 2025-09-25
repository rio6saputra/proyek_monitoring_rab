<?php
// api/simpan_banyak_detail.php (Versi Final dengan Targeted Replace)
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Sekarang kita membutuhkan id_detail_asli untuk tahu grup mana yang harus diganti
if (empty($data['rab_entry_id']) || !isset($data['details']) || empty($data['id_detail_asli'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap (ID Asli hilang).']);
    exit;
}

$rab_entry_id = (int)$data['rab_entry_id'];
$details_data = $data['details'];
$id_detail_asli = (int)$data['id_detail_asli']; // ID dari header/item tunggal yang diedit

mysqli_begin_transaction($koneksi);

try {
    // Fungsi untuk mengumpulkan ID dari sebuah grup (header dan semua anaknya)
    function kumpulkan_id_grup_rekursif($koneksi, $parent_id, &$ids) {
        $ids[] = $parent_id;
        $stmt = mysqli_prepare($koneksi, "SELECT id_rab_detail FROM RAB_Detail WHERE parent_detail_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $parent_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            kumpulkan_id_grup_rekursif($koneksi, $row['id_rab_detail'], $ids);
        }
    }

    // 1. HAPUS GRUP YANG LAMA SECARA TERTARGET
    $ids_to_delete = [];
    kumpulkan_id_grup_rekursif($koneksi, $id_detail_asli, $ids_to_delete);
    
    if (!empty($ids_to_delete)) {
        $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));
        $stmt_delete = mysqli_prepare($koneksi, "DELETE FROM RAB_Detail WHERE id_rab_detail IN ($placeholders)");
        mysqli_stmt_bind_param($stmt_delete, str_repeat('i', count($ids_to_delete)), ...$ids_to_delete);
        mysqli_stmt_execute($stmt_delete);
    }

    // 2. INSERT KEMBALI VERSI BARU DARI GRUP TERSEBUT
    function simpan_rincian_baru_rekursif($koneksi, $rab_entry_id, $details, $parent_id = null) {
        $sql_detail = "INSERT INTO RAB_Detail (rab_entry_id, parent_detail_id, uraian_pekerjaan, harga_satuan, total_biaya, volume_data) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_detail = mysqli_prepare($koneksi, $sql_detail);
        
        foreach ($details as $item) {
            $uraian = $item['uraian'] ?? '';
            $harga_satuan = !empty($item['harga_satuan']) ? (float)$item['harga_satuan'] : null;
            $volumes = $item['volumes'] ?? [];
            $volume_data_json = json_encode($volumes);
            
            $total_volume = 1;
            if (!empty($volumes)) { foreach ($volumes as $vol) { $total_volume *= (float)($vol['volume'] ?? 0); } } else { $total_volume = 0; }
            $total_biaya = $total_volume * ($harga_satuan ?? 0);

            mysqli_stmt_bind_param($stmt_detail, "iisdds", $rab_entry_id, $parent_id, $uraian, $harga_satuan, $total_biaya, $volume_data_json);
            mysqli_stmt_execute($stmt_detail);
            $new_detail_id = mysqli_insert_id($koneksi);

            if ($new_detail_id > 0 && !empty($item['children'])) {
                simpan_rincian_baru_rekursif($koneksi, $rab_entry_id, $item['children'], $new_detail_id);
            }
        }
    }

    if (!empty($details_data)) {
        // Mulai simpan dari level atas (parent_id = NULL karena kita menangani satu grup utuh)
        simpan_rincian_baru_rekursif($koneksi, $rab_entry_id, $details_data);
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