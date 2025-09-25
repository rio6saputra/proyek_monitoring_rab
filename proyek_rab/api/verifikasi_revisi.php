<?php
// api/verifikasi_revisi.php (Final dengan Logika Pergeseran Data & revisi_ke)
include __DIR__ . '/../includes/config/session_manager.php';
include __DIR__ . '/../includes/config/koneksi.php';
header('Content-Type: application/json');

// Keamanan & Validasi Input
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Hanya admin yang dapat melakukan verifikasi.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['id_rab_detail']) || empty($data['action'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap. ID detail dan aksi verifikasi diperlukan.']);
    exit;
}

$id_rab_detail = (int)$data['id_rab_detail'];
$action = $data['action']; // 'setujui' atau 'tolak'

mysqli_begin_transaction($koneksi);
try {
    // Kunci baris untuk mencegah race condition dan ambil semua data yang relevan
    $stmt_check = mysqli_prepare($koneksi, "SELECT * FROM RAB_Detail WHERE id_rab_detail = ? FOR UPDATE");
    mysqli_stmt_bind_param($stmt_check, "i", $id_rab_detail);
    mysqli_stmt_execute($stmt_check);
    $current_detail = mysqli_stmt_get_result($stmt_check)->fetch_assoc();
    mysqli_stmt_close($stmt_check);

    if (!$current_detail) {
        throw new Exception("Rincian detail tidak ditemukan.");
    }
    
    // Verifikasi ini seharusnya hanya untuk item yang status revisinya 'REVISI'
    if ($current_detail['status_revisi'] !== 'REVISI') {
        throw new Exception("Aksi tidak valid. Rincian ini tidak sedang dalam status revisi.");
    }

    $rab_entry_id = $current_detail['rab_entry_id'];

    if ($action === 'setujui') {
        $revisi_ke = (int)$current_detail['revisi_ke'];

        // Untuk revisi pertama (revisi_ke = 1), kita salin revisi ke utama
        // Untuk revisi lanjutan (revisi_ke > 1), kita juga salin revisi ke utama (ini adalah "pergeseran"nya)
        // Jadi, logikanya sama untuk semua level persetujuan revisi.
        $sql = "UPDATE RAB_Detail SET
                    uraian_pekerjaan = ?,
                    harga_satuan = harga_satuan_revisi,
                    total_biaya = total_biaya_revisi,
                    volume_data = volume_data_revisi,
                    status_revisi = 'DISETUJUI',
                    harga_satuan_revisi = NULL,
                    total_biaya_revisi = NULL,
                    volume_data_revisi = NULL
                WHERE id_rab_detail = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "si", 
            $current_detail['uraian_pekerjaan'], // Uraian sudah diubah saat pengajuan revisi
            $id_rab_detail
        );
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal mengeksekusi persetujuan revisi.");
        }
        $message = "Revisi berhasil disetujui.";

    } elseif ($action === 'tolak') {
        // Jika ditolak, kita batalkan usulan revisi dan kembalikan nomor revisinya
        $sql = "UPDATE RAB_Detail SET
                    harga_satuan_revisi = NULL,
                    total_biaya_revisi = NULL,
                    volume_data_revisi = NULL,
                    status_revisi = 'DITOLAK',
                    revisi_ke = revisi_ke - 1,
                    catatan_revisi = CONCAT('Ditolak: ', catatan_revisi)
                WHERE id_rab_detail = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_rab_detail);
        mysqli_stmt_execute($stmt);
        $message = "Revisi berhasil ditolak.";
    } else {
        throw new Exception("Aksi tidak valid.");
    }

    // Periksa apakah ini usulan revisi terakhir untuk Akun ini
    $stmt_count = mysqli_prepare($koneksi, "SELECT COUNT(*) as count FROM RAB_Detail WHERE rab_entry_id = ? AND status_revisi = 'REVISI'");
    mysqli_stmt_bind_param($stmt_count, "i", $rab_entry_id);
    mysqli_stmt_execute($stmt_count);
    $count_data = mysqli_stmt_get_result($stmt_count)->fetch_assoc();

    // Jika sudah tidak ada lagi revisi yang menunggu, kembalikan status utama ke DISETUJUI
    if ($count_data['count'] == 0) {
        $stmt_entry = mysqli_prepare($koneksi, "UPDATE RAB_Entry SET memiliki_revisi = FALSE, status_anggaran = 'DISETUJUI' WHERE id_rab_entry = ?");
        mysqli_stmt_bind_param($stmt_entry, "i", $rab_entry_id);
        mysqli_stmt_execute($stmt_entry);
    }
    
    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => $message]);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>