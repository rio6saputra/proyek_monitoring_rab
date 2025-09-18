<?php
// api/update_detail.php
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Kode status 'Forbidden'
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Validasi data input
if (empty($data['id_rab_detail']) || empty($data['uraian']) || !isset($data['harga_satuan']) || !isset($data['catatan'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit;
}

$id_rab_detail = (int)$data['id_rab_detail'];
$uraian = $data['uraian'];
$harga_satuan = !empty($data['harga_satuan']) ? (float)$data['harga_satuan'] : null;
$catatan = $data['catatan'];
$volumes = $data['volumes'] ?? [];

// Mulai transaksi database
mysqli_begin_transaction($koneksi);

try {
    // 1. Hitung ulang total biaya berdasarkan volume dan harga baru
    $total_volume = 1;
    if (!empty($volumes)) {
        foreach ($volumes as $vol_data) {
            if (!empty($vol_data['volume'])) {
                $total_volume *= (float)$vol_data['volume'];
            }
        }
    }
    $total_biaya = $total_volume * ($harga_satuan ?? 0);

    // 2. Update RAB_Detail
    $sql_detail = "UPDATE RAB_Detail SET uraian_pekerjaan = ?, harga_satuan = ?, total_biaya = ?, catatan = ? WHERE id_rab_detail = ?";
    $stmt_detail = mysqli_prepare($koneksi, $sql_detail);
    mysqli_stmt_bind_param($stmt_detail, "sddsi", $uraian, $harga_satuan, $total_biaya, $catatan, $id_rab_detail);
    
    if (!mysqli_stmt_execute($stmt_detail)) {
        throw new Exception("Gagal mengupdate RAB_Detail.");
    }
    
    // 3. Hapus volume lama
    $sql_delete_volumes = "DELETE FROM RAB_Volume WHERE rab_detail_id = ?";
    $stmt_delete_volumes = mysqli_prepare($koneksi, $sql_delete_volumes);
    mysqli_stmt_bind_param($stmt_delete_volumes, "i", $id_rab_detail);
    mysqli_stmt_execute($stmt_delete_volumes);

    // 4. Masukkan volume baru jika ada
    if (!empty($volumes)) {
        $sql_volume = "INSERT INTO RAB_Volume (rab_detail_id, volume, satuan) VALUES (?, ?, ?)";
        $stmt_volume = mysqli_prepare($koneksi, $sql_volume);
        foreach($volumes as $vol_data) {
            if(!empty($vol_data['volume']) && !empty($vol_data['satuan'])) {
                $volume_float = (float)$vol_data['volume'];
                mysqli_stmt_bind_param($stmt_volume, "ids", $id_rab_detail, $volume_float, $vol_data['satuan']);
                mysqli_stmt_execute($stmt_volume);
            }
        }
    }

    // Jika semua berhasil, commit transaksi
    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Detail RAB berhasil diperbarui!']);

} catch (Exception $e) {
    // Jika ada error, batalkan semua perubahan
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>