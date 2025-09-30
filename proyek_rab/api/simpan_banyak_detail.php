<?php
// api/simpan_banyak_detail.php (Versi Final dengan Logika Update Tunggal yang Aman)
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['rab_entry_id']) || !isset($data['details'][0])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

$rab_entry_id = (int)$data['rab_entry_id'];
$item_from_form = $data['details'][0]; // Kita hanya proses satu item yang diedit

mysqli_begin_transaction($koneksi);

try {
    $current_detail_id = !empty($item_from_form['id_rab_detail']) ? (int)$item_from_form['id_rab_detail'] : null;

    if (!$current_detail_id) {
        throw new Exception("ID Detail tidak ditemukan untuk diupdate.");
    }
    
    // 1. Ambil data lama dari DB untuk mendapatkan parent_id yang benar
    $stmt_get_old = mysqli_prepare($koneksi, "SELECT parent_detail_id FROM RAB_Detail WHERE id_rab_detail = ? AND rab_entry_id = ?");
    mysqli_stmt_bind_param($stmt_get_old, "ii", $current_detail_id, $rab_entry_id);
    mysqli_stmt_execute($stmt_get_old);
    $old_data = mysqli_stmt_get_result($stmt_get_old)->fetch_assoc();

    if (!$old_data) {
        throw new Exception("Data detail asli tidak ditemukan di database.");
    }
    
    // Ini adalah kunci perbaikannya: selalu gunakan parent_id dari database.
    $correct_parent_id = $old_data['parent_detail_id'];

    // 2. Siapkan data baru dari form
    $uraian = $item_from_form['uraian'] ?? '';
    $harga_satuan = !empty($item_from_form['harga_satuan']) ? (float)str_replace('.', '', $item_from_form['harga_satuan']) : null;
    $volumes_array = $item_from_form['volumes'] ?? [];
    $volume_data_json = json_encode($volumes_array);
    
    $total_volume = 1;
    if (!empty($volumes_array)) { foreach ($volumes_array as $vol) { $total_volume *= (float)($vol['volume'] ?? 0); } } else { $total_volume = 0; }
    $total_biaya = $total_volume * ($harga_satuan ?? 0);
    
    // 3. Lakukan UPDATE dengan data yang sudah valid, tanpa mengubah parent_id
    // Perhatikan bahwa kita menggunakan $correct_parent_id yang diambil dari DB
    $stmt_update = mysqli_prepare($koneksi, "UPDATE RAB_Detail SET parent_detail_id = ?, uraian_pekerjaan = ?, harga_satuan = ?, total_biaya = ?, volume_data = ? WHERE id_rab_detail = ?");
    mysqli_stmt_bind_param($stmt_update, "isddsi", $correct_parent_id, $uraian, $harga_satuan, $total_biaya, $volume_data_json, $current_detail_id);
    mysqli_stmt_execute($stmt_update);
    
    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Detail RAB berhasil diperbarui!']);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
mysqli_close($koneksi);
?>