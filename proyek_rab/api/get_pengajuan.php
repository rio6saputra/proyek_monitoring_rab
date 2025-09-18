<?php
// api/get_pengajuan.php
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

// Keamanan: Hanya Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

try {
    // Query untuk mengambil daftar pengajuan yang unik berdasarkan kombinasi
    // Kegiatan, KRO, dan Bagian yang statusnya MENUNGGU_VERIFIKASI
    $sql = "SELECT DISTINCT
                keg.kegiatan_id,
                keg.nama_kegiatan,
                kro.kro_id,
                kro.nama_kro,
                bag.bagian_id,
                bag.nama_bagian
            FROM RAB_Entry e
            JOIN PAR_RO ro ON e.ro_id = ro.ro_id
            JOIN PAR_RAB_Kegiatan keg ON ro.kegiatan_id = keg.kegiatan_id
            JOIN PAR_RAB_KRO kro ON ro.kro_id = kro.kro_id
            JOIN PAR_RAB_Bagian bag ON ro.bagian_id = bag.bagian_id
            WHERE e.status_anggaran = 'MENUNGGU_VERIFIKASI'
            ORDER BY bag.nama_bagian, keg.nama_kegiatan";

    $result = mysqli_query($koneksi, $sql);

    if (!$result) {
        throw new Exception("Gagal mengambil data pengajuan: " . mysqli_error($koneksi));
    }

    $pengajuan = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $pengajuan[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $pengajuan]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>