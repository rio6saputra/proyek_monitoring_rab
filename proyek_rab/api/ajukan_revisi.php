<?php
// api/ajukan_revisi.php (Versi Final dengan Logika Bedah Mikro)
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) { /* ... kode keamanan ... */ exit; }
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['rab_entry_id']) || !isset($data['details'])) { /* ... validasi ... */ exit; }

$rab_entry_id = (int)$data['rab_entry_id'];
$details_from_form = $data['details'];
$catatan_revisi = $data['catatan_revisi'] ?? 'Revisi';

mysqli_begin_transaction($koneksi);

try {
    // Pernyataan SQL yang akan kita gunakan berulang kali
    $sql_update = "UPDATE RAB_Detail SET uraian_pekerjaan = ?, harga_satuan_revisi = ?, total_biaya_revisi = ?, volume_data_revisi = ?, status_revisi = 'REVISI', catatan_revisi = ? WHERE id_rab_detail = ?";
    $stmt_update = mysqli_prepare($koneksi, $sql_update);

    $sql_insert = "INSERT INTO RAB_Detail (rab_entry_id, parent_detail_id, uraian_pekerjaan, harga_satuan_revisi, total_biaya_revisi, volume_data_revisi, status_revisi, catatan_revisi) VALUES (?, ?, ?, ?, ?, ?, 'REVISI', ?)";
    $stmt_insert = mysqli_prepare($koneksi, $sql_insert);

    // Ambil item level atas dari form (seharusnya hanya ada satu)
    $top_level_item = $details_from_form[0];
    $header_id = !empty($top_level_item['id_rab_detail']) ? (int)$top_level_item['id_rab_detail'] : null;

    // Proses item header/induk
    $uraian = $top_level_item['uraian'] ?? '';
    $harga_satuan_rev = !empty($top_level_item['harga_satuan']) ? (float)$top_level_item['harga_satuan'] : null;
    $volumes_rev = $top_level_item['volumes'] ?? [];
    $volume_data_rev_json = json_encode($volumes_rev);
    $total_volume_rev = 1;
    if (!empty($volumes_rev)) { foreach ($volumes_rev as $vol) { $total_volume_rev *= (float)($vol['volume'] ?? 0); } } else { $total_volume_rev = 0; }
    $total_biaya_rev = $total_volume_rev * ($harga_satuan_rev ?? 0);

    if ($header_id) {
        mysqli_stmt_bind_param($stmt_update, "sddssi", $uraian, $harga_satuan_rev, $total_biaya_rev, $volume_data_rev_json, $catatan_revisi, $header_id);
        mysqli_stmt_execute($stmt_update);
    } else {
        mysqli_stmt_bind_param($stmt_insert, "iisddss", $rab_entry_id, null, $uraian, $harga_satuan_rev, $total_biaya_rev, $volume_data_rev_json, $catatan_revisi);
        mysqli_stmt_execute($stmt_insert);
        $header_id = mysqli_insert_id($koneksi);
    }

    // Proses item anak
    $children_from_form = $top_level_item['children'] ?? [];
    $form_child_ids = [];
    foreach($children_from_form as $child_item) {
        // ... (logika perhitungan biaya untuk anak, sama seperti di atas) ...
        $child_id = !empty($child_item['id_rab_detail']) ? (int)$child_item['id_rab_detail'] : null;
        if ($child_id) {
            // UPDATE anak yang sudah ada
            mysqli_stmt_bind_param($stmt_update, "sddssi", $child_uraian, $child_hs_rev, $child_tb_rev, $child_vol_json, $catatan_revisi, $child_id);
            mysqli_stmt_execute($stmt_update);
            $form_child_ids[] = $child_id;
        } else {
            // INSERT anak yang baru
            mysqli_stmt_bind_param($stmt_insert, "iisddss", $rab_entry_id, $header_id, $child_uraian, $child_hs_rev, $child_tb_rev, $child_vol_json, $catatan_revisi);
            mysqli_stmt_execute($stmt_insert);
        }
    }

    // Hapus anak yang dihilangkan dari form
    $original_child_ids = $data['original_child_ids'] ?? [];
    $ids_to_delete = array_diff($original_child_ids, $form_child_ids);
    if (!empty($ids_to_delete)) {
        $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));
        $stmt_delete = mysqli_prepare($koneksi, "DELETE FROM RAB_Detail WHERE id_rab_detail IN ($placeholders)");
        mysqli_stmt_bind_param($stmt_delete, str_repeat('i', count($ids_to_delete)), ...$ids_to_delete);
        mysqli_stmt_execute($stmt_delete);
    }

    mysqli_query($koneksi, "UPDATE RAB_Entry SET memiliki_revisi = TRUE WHERE id_rab_entry = $rab_entry_id");
    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Usulan revisi berhasil disimpan!']);

} catch (Exception $e) { /* ... blok catch ... */ }
mysqli_close($koneksi);
?>