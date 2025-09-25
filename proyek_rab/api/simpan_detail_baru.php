<?php
// api/simpan_detail_baru.php (Versi Final dengan Volume JSON)
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
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap (entry_id atau details hilang).']);
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

    if (!in_array($entry['status_anggaran'], ['DRAFT', 'DITOLAK', 'REVISI_DRAFT'])) {
        throw new Exception("Gagal. Anggaran harus berstatus DRAFT, DITOLAK, atau REVISI_DRAFT.");
    }

    function simpan_rincian_baru($koneksi, $rab_entry_id, $details, $parent_id = null) {
        // PERUBAHAN DI SINI: Menambahkan 'volume_data' ke query
        $sql_detail = "INSERT INTO RAB_Detail (rab_entry_id, parent_detail_id, uraian_pekerjaan, harga_satuan, total_biaya, volume_data) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_detail = mysqli_prepare($koneksi, $sql_detail);
        
        // Query untuk menyimpan ke RAB_Volume sudah tidak diperlukan lagi

        foreach ($details as $item) {
            $uraian = $item['uraian'] ?? '';
            $harga_satuan = !empty($item['harga_satuan']) ? (float)$item['harga_satuan'] : null;
            $volumes = $item['volumes'] ?? [];
            $volume_data_json = json_encode($volumes); // Ubah array volume menjadi JSON
            
            $total_volume = 1;
            if (!empty($volumes)) {
                foreach ($volumes as $vol) { $total_volume *= (float)$vol['volume']; }
            } else { $total_volume = 0; }
            $total_biaya = $total_volume * ($harga_satuan ?? 0);

            // PERUBAHAN DI SINI: Bind parameter 'volume_data_json'
            mysqli_stmt_bind_param($stmt_detail, "iisdds", $rab_entry_id, $parent_id, $uraian, $harga_satuan, $total_biaya, $volume_data_json);
            mysqli_stmt_execute($stmt_detail);
            $new_detail_id = mysqli_insert_id($koneksi);

            // Logika untuk menyimpan ke RAB_Volume sudah dihapus

            if ($new_detail_id > 0 && !empty($item['children'])) {
                simpan_rincian_baru($koneksi, $rab_entry_id, $item['children'], $new_detail_id);
            }
        }
    }

    simpan_rincian_baru($koneksi, $rab_entry_id, $details_data);
    
    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Detail RAB berhasil disimpan!']);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
mysqli_close($koneksi);
?>