<?php
// api/ajukan_revisi.php (Versi Final dengan Logika Update Tunggal yang Stabil)
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
$item_from_form = $data['details'][0]; // Kita hanya proses satu item
$catatan_revisi_utama = $data['catatan_revisi'] ?? '';

mysqli_begin_transaction($koneksi);

try {
    $current_detail_id = !empty($item_from_form['id_rab_detail']) ? (int)$item_from_form['id_rab_detail'] : null;

    if (!$current_detail_id) {
        throw new Exception("ID Detail tidak ditemukan untuk diupdate.");
    }
    
    // 1. Ambil data lama dari DB untuk mendapatkan parent_id yang benar dan untuk perbandingan
    $stmt_get_old = mysqli_prepare($koneksi, "SELECT * FROM RAB_Detail WHERE id_rab_detail = ? AND rab_entry_id = ?");
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
    $harga_satuan_rev_form = !empty($item_from_form['harga_satuan']) ? (float)str_replace('.', '', $item_from_form['harga_satuan']) : null;
    $volumes_rev_array = $item_from_form['volumes'] ?? [];
    $volume_data_rev_json = json_encode($volumes_rev_array);
    
    $total_volume_rev = 1;
    if (!empty($volumes_rev_array)) { foreach ($volumes_rev_array as $vol) { $total_volume_rev *= (float)($vol['volume'] ?? 0); } } else { $total_volume_rev = 0; }
    $total_biaya_rev = $total_volume_rev * ($harga_satuan_rev_form ?? 0);
    
    // 3. Bandingkan data untuk melihat apakah ada perubahan
    $old_volumes_array = json_decode($old_data['volume_data_revisi'], true) ?? [];
    $is_changed = (
        $old_data['uraian_pekerjaan'] != $uraian ||
        (float)$old_data['harga_satuan_revisi'] != $harga_satuan_rev_form ||
        $old_volumes_array != $volumes_rev_array
    );
    
    $new_status_revisi = $is_changed ? 'REVISI' : $old_data['status_revisi'];
    $new_catatan_revisi = $is_changed ? $catatan_revisi_utama : $old_data['catatan_revisi'];

    // 4. Lakukan UPDATE dengan data yang sudah valid
    $stmt_update = mysqli_prepare($koneksi, "UPDATE RAB_Detail SET parent_detail_id = ?, uraian_pekerjaan = ?, harga_satuan_revisi = ?, total_biaya_revisi = ?, volume_data_revisi = ?, status_revisi = ?, catatan_revisi = ? WHERE id_rab_detail = ?");
    mysqli_stmt_bind_param($stmt_update, "isdssssi", $correct_parent_id, $uraian, $harga_satuan_rev_form, $total_biaya_rev, $volume_data_rev_json, $new_status_revisi, $new_catatan_revisi, $current_detail_id);
    mysqli_stmt_execute($stmt_update);
    
    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Usulan revisi berhasil disimpan!']);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
mysqli_close($koneksi);
?>