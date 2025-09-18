<?php
// api/ajukan_revisi.php
include __DIR__ . '/../includes/config/session_manager.php';
include __DIR__ . '/../includes/config/koneksi.php';
header('Content-Type: application/json');

// 1. Keamanan & Validasi Input Dasar
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Silakan login.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (empty($data['id_rab_detail']) || !isset($data['uraian']) || !isset($data['harga_satuan_revisi']) || empty($data['catatan_revisi'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap. Uraian, harga satuan, dan catatan revisi wajib diisi.']);
    exit;
}

$id_rab_detail = (int)$data['id_rab_detail'];
$uraian = $data['uraian'];
$harga_satuan_revisi = !empty($data['harga_satuan_revisi']) ? (float)$data['harga_satuan_revisi'] : 0;
$catatan_revisi = trim($data['catatan_revisi']);
$volumes_revisi = $data['volumes_revisi'] ?? [];

// Mulai transaksi database
mysqli_begin_transaction($koneksi);

try {
    // 2. Kunci Revisi: Periksa status saat ini sebelum melanjutkan
    $stmt_check = mysqli_prepare($koneksi, "SELECT status_revisi, rab_entry_id FROM RAB_Detail WHERE id_rab_detail = ? FOR UPDATE");
    mysqli_stmt_bind_param($stmt_check, "i", $id_rab_detail);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $current_detail = mysqli_fetch_assoc($result_check);

    if (!$current_detail) {
        throw new Exception("Rincian detail tidak ditemukan.");
    }
    if ($current_detail['status_revisi'] === 'REVISI') {
        throw new Exception("Gagal mengajukan revisi. Revisi sebelumnya masih dalam proses verifikasi.");
    }

    $rab_entry_id = $current_detail['rab_entry_id'];

    // 3. Kalkulasi dan Persiapan Data Revisi
    $total_volume_revisi = 1;
    if (!empty($volumes_revisi)) {
        foreach ($volumes_revisi as $vol_data) {
            if (!empty($vol_data['volume'])) {
                $total_volume_revisi *= (float)$vol_data['volume'];
            }
        }
    } else {
        $total_volume_revisi = 0;
    }

    $total_biaya_revisi = $total_volume_revisi * $harga_satuan_revisi;
    $volume_data_revisi_json = json_encode($volumes_revisi);


    // 4. Update tabel RAB_Detail dengan data revisi
    $sql_detail = "UPDATE RAB_Detail SET 
                    uraian_pekerjaan = ?, 
                    harga_satuan_revisi = ?, 
                    total_biaya_revisi = ?, 
                    volume_data_revisi = ?,
                    status_revisi = 'REVISI',
                    catatan_revisi = ?
                   WHERE id_rab_detail = ?";
    $stmt_detail = mysqli_prepare($koneksi, $sql_detail);
    mysqli_stmt_bind_param($stmt_detail, "sddssi", $uraian, $harga_satuan_revisi, $total_biaya_revisi, $volume_data_revisi_json, $catatan_revisi, $id_rab_detail);
    
    if (!mysqli_stmt_execute($stmt_detail)) {
        throw new Exception("Gagal memperbarui data revisi detail.");
    }

    // 5. Update tabel RAB_Volume (data volume di kolom original langsung diupdate)
    $sql_delete_volumes = "DELETE FROM RAB_Volume WHERE rab_detail_id = ?";
    $stmt_delete_volumes = mysqli_prepare($koneksi, $sql_delete_volumes);
    mysqli_stmt_bind_param($stmt_delete_volumes, "i", $id_rab_detail);
    mysqli_stmt_execute($stmt_delete_volumes);

    if (!empty($volumes_revisi)) {
        $sql_volume = "INSERT INTO RAB_Volume (rab_detail_id, volume, satuan) VALUES (?, ?, ?)";
        $stmt_volume = mysqli_prepare($koneksi, $sql_volume);
        foreach($volumes_revisi as $vol_data) {
            if(!empty($vol_data['volume']) && !empty($vol_data['satuan'])) {
                $volume_float = (float)$vol_data['volume'];
                mysqli_stmt_bind_param($stmt_volume, "ids", $id_rab_detail, $volume_float, $vol_data['satuan']);
                mysqli_stmt_execute($stmt_volume);
            }
        }
    }

    // 6. Update penanda 'memiliki_revisi' di tabel RAB_Entry induknya
    $sql_entry = "UPDATE RAB_Entry SET memiliki_revisi = TRUE WHERE id_rab_entry = ?";
    $stmt_entry = mysqli_prepare($koneksi, $sql_entry);
    mysqli_stmt_bind_param($stmt_entry, "i", $rab_entry_id);
    if (!mysqli_stmt_execute($stmt_entry)) {
        throw new Exception("Gagal menandai entri induk.");
    }
    
    // Jika semua berhasil, commit transaksi
    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Usulan revisi berhasil diajukan.']);

} catch (Exception $e) {
    // Jika ada error, batalkan semua perubahan
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>