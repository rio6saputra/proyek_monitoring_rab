<?php
// api/simpan_banyak_detail.php (Versi Revisi)
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (empty($data['rab_entry_id']) || !isset($data['details']) || empty($data['catatan_revisi'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap (entry_id, details, atau catatan revisi hilang).']);
    exit;
}

$rab_entry_id = (int)$data['rab_entry_id'];
$details_data = $data['details'];
$catatan_revisi = trim($data['catatan_revisi']);

mysqli_begin_transaction($koneksi);

try {
    // 1. Fungsi rekursif untuk menyimpan/update usulan revisi
    function simpan_revisi_rekursif($koneksi, $rab_entry_id, $details, $catatan, $parent_id = null) {
        foreach ($details as $item) {
            $uraian = $item['uraian'] ?? '';
            $harga_satuan_revisi = !empty($item['harga_satuan']) ? (float)$item['harga_satuan'] : 0;
            
            $total_volume_revisi = 1;
            if (!empty($item['volumes'])) {
                foreach ($item['volumes'] as $vol) { $total_volume_revisi *= (float)$vol['volume']; }
            } else {
                $total_volume_revisi = 0;
            }
            $total_biaya_revisi = $total_volume_revisi * $harga_satuan_revisi;
            $volume_data_revisi_json = json_encode($item['volumes'] ?? []);

            // Cek apakah item ini sudah ada (punya ID) atau item baru
            $id_rab_detail = $item['id_rab_detail'] ?? null;

            if ($id_rab_detail) {
                // UPDATE DATA REVISI
                $sql = "UPDATE RAB_Detail SET 
                            uraian_pekerjaan = ?, harga_satuan_revisi = ?, total_biaya_revisi = ?, 
                            volume_data_revisi = ?, status_revisi = 'REVISI', catatan_revisi = ?
                        WHERE id_rab_detail = ?";
                $stmt = mysqli_prepare($koneksi, $sql);
                mysqli_stmt_bind_param($stmt, "sdsdsi", $uraian, $harga_satuan_revisi, $total_biaya_revisi, $volume_data_revisi_json, $catatan, $id_rab_detail);
            } else {
                // INSERT DETAIL BARU SEBAGAI REVISI
                $sql = "INSERT INTO RAB_Detail 
                            (rab_entry_id, parent_detail_id, uraian_pekerjaan, harga_satuan_revisi, total_biaya_revisi, volume_data_revisi, status_revisi, catatan_revisi) 
                        VALUES (?, ?, ?, ?, ?, ?, 'REVISI', ?)";
                $stmt = mysqli_prepare($koneksi, $sql);
                mysqli_stmt_bind_param($stmt, "iisdsds", $rab_entry_id, $parent_id, $uraian, $harga_satuan_revisi, $total_biaya_revisi, $volume_data_revisi_json, $catatan);
            }
            mysqli_stmt_execute($stmt);
            $new_detail_id = $id_rab_detail ?? mysqli_insert_id($koneksi);

            // Panggil rekursif untuk anak-anaknya
            if (!empty($item['children'])) {
                simpan_revisi_rekursif($koneksi, $rab_entry_id, $item['children'], $catatan, $new_detail_id);
            }
        }
    }

    // 2. Tandai entri induk bahwa ada revisi
    mysqli_query($koneksi, "UPDATE RAB_Entry SET memiliki_revisi = TRUE WHERE id_rab_entry = $rab_entry_id");

    // 3. Panggil fungsi untuk memproses semua detail
    simpan_revisi_rekursif($koneksi, $rab_entry_id, $details_data, $catatan_revisi);
    
    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Usulan revisi berhasil diajukan!']);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
mysqli_close($koneksi);
?>