<?php
include __DIR__ . '/../includes/config/session_manager.php';
include __DIR__ . '/../includes/config/koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$kegiatan_id = $data['kegiatan_id'] ?? null;
$kro_id = $data['kro_id'] ?? null;
$bagian_id = $data['bagian_id'] ?? null;
$action = $data['action'] ?? null; // 'setujui' atau 'tolak'

if (!$kegiatan_id || !$kro_id || !$bagian_id || !$action) {
    http_response_code(400);
    // Mengubah pesan error agar lebih sesuai dengan konteks
    echo json_encode(['status' => 'error', 'message' => 'Data pengajuan tidak lengkap untuk verifikasi.']);
    exit;
}

mysqli_begin_transaction($koneksi);

try {
    // 1. Dapatkan semua entri yang cocok dengan kriteria
    $sql_select = "SELECT e.id_rab_entry, e.status_anggaran FROM rab_entry e 
                   JOIN par_ro ro ON e.ro_id = ro.ro_id 
                   WHERE ro.kegiatan_id = ? AND ro.kro_id = ? AND ro.bagian_id = ?";
    
    $stmt_select = mysqli_prepare($koneksi, $sql_select);
    mysqli_stmt_bind_param($stmt_select, "isi", $kegiatan_id, $kro_id, $bagian_id);
    mysqli_stmt_execute($stmt_select);
    $result = mysqli_stmt_get_result($stmt_select);
    $entries_to_update = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if (empty($entries_to_update)) {
        throw new Exception("Tidak ada entri anggaran yang cocok untuk diverifikasi.");
    }

    $success_count = 0;
    foreach ($entries_to_update as $entry) {
        $current_status = $entry['status_anggaran'];
        $rab_entry_id = $entry['id_rab_entry'];
        $new_status = '';

        if ($action === 'setujui') {
            if ($current_status === 'MENUNGGU_VERIFIKASI') {
                $new_status = 'DISETUJUI';
            } elseif ($current_status === 'REVISI_MENUNGGU_VERIFIKASI') {
                $new_status = 'REVISI_DISETUJUI';
            }
        } elseif ($action === 'tolak') {
            // Logika untuk menolak bisa ditambahkan di sini jika perlu
            // Misalnya: $new_status = 'DITOLAK';
            // Untuk saat ini, kita fokus pada 'setujui'
        }

        // Hanya update jika ada perubahan status yang valid
        if ($new_status !== '') {
            $stmt_update = mysqli_prepare($koneksi, "UPDATE rab_entry SET status_anggaran = ? WHERE id_rab_entry = ?");
            mysqli_stmt_bind_param($stmt_update, "si", $new_status, $rab_entry_id);
            if (mysqli_stmt_execute($stmt_update)) {
                $success_count++;
            }
        }
    }

    if ($success_count === 0) {
        throw new Exception("Tidak ada status anggaran yang valid untuk diubah.");
    }
    
    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => "Verifikasi berhasil. {$success_count} data anggaran telah diperbarui."]);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>