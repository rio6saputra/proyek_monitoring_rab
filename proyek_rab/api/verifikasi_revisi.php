<?php
// api/verifikasi_revisi.php
include __DIR__ . '/../includes/config/session_manager.php';
include __DIR__ . '/../includes/config/koneksi.php';
header('Content-Type: application/json');

// 1. Keamanan & Validasi Input
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Hanya admin yang dapat melakukan verifikasi.']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (empty($data['id_rab_detail']) || empty($data['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap. ID detail dan aksi verifikasi diperlukan.']);
    exit;
}

$id_rab_detail = (int)$data['id_rab_detail'];
$action = $data['action']; // 'setujui' or 'tolak'

// Mulai transaksi database
mysqli_begin_transaction($koneksi);

try {
    // Ambil data saat ini untuk mendapatkan rab_entry_id
    $stmt_check = mysqli_prepare($koneksi, "SELECT rab_entry_id, status_revisi FROM RAB_Detail WHERE id_rab_detail = ? FOR UPDATE");
    mysqli_stmt_bind_param($stmt_check, "i", $id_rab_detail);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $current_detail = mysqli_fetch_assoc($result_check);

    if (!$current_detail) {
        throw new Exception("Rincian detail tidak ditemukan.");
    }
    if ($current_detail['status_revisi'] !== 'REVISI') {
        throw new Exception("Aksi tidak valid. Rincian ini tidak sedang dalam status revisi.");
    }

    $rab_entry_id = $current_detail['rab_entry_id'];

    // 2. Logika berdasarkan Aksi (Setujui / Tolak)
    if ($action === 'setujui') {
        // Salin data dari kolom revisi ke kolom original, lalu kosongkan kolom revisi
        $sql = "UPDATE RAB_Detail SET
                    harga_satuan = harga_satuan_revisi,
                    total_biaya = total_biaya_revisi,
                    harga_satuan_revisi = NULL,
                    total_biaya_revisi = NULL,
                    volume_data_revisi = NULL,
                    status_revisi = 'DISETUJUI',
                    catatan_revisi = CONCAT('Disetujui: ', catatan_revisi)
                WHERE id_rab_detail = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_rab_detail);
        $message = "Revisi berhasil disetujui.";

    } elseif ($action === 'tolak') {
        // Cukup kosongkan kolom revisi dan ubah statusnya
        $sql = "UPDATE RAB_Detail SET
                    harga_satuan_revisi = NULL,
                    total_biaya_revisi = NULL,
                    volume_data_revisi = NULL,
                    status_revisi = 'DITOLAK',
                    catatan_revisi = CONCAT('Ditolak: ', catatan_revisi)
                WHERE id_rab_detail = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_rab_detail);
        $message = "Revisi berhasil ditolak.";
    } else {
        throw new Exception("Aksi tidak valid.");
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Gagal mengeksekusi aksi verifikasi.");
    }

    // 3. Periksa kembali apakah masih ada revisi lain di bawah Entry yang sama
    $stmt_count = mysqli_prepare($koneksi, "SELECT COUNT(*) as count FROM RAB_Detail WHERE rab_entry_id = ? AND status_revisi = 'REVISI'");
    mysqli_stmt_bind_param($stmt_count, "i", $rab_entry_id);
    mysqli_stmt_execute($stmt_count);
    $result_count = mysqli_stmt_get_result($stmt_count);
    $count_data = mysqli_fetch_assoc($result_count);

    // Jika sudah tidak ada lagi revisi yang menunggu, update penanda di tabel induk
    if ($count_data['count'] == 0) {
        $stmt_entry = mysqli_prepare($koneksi, "UPDATE RAB_Entry SET memiliki_revisi = FALSE WHERE id_rab_entry = ?");
        mysqli_stmt_bind_param($stmt_entry, "i", $rab_entry_id);
        if (!mysqli_stmt_execute($stmt_entry)) {
            throw new Exception("Gagal memperbarui status entri induk.");
        }
    }

    // Jika semua berhasil, commit transaksi
    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => $message]);

} catch (Exception $e) {
    // Jika ada error, batalkan semua perubahan
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>