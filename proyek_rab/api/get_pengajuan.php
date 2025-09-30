<?php
// api/get_pengajuan.php (Versi Aman dengan Prepared Statements)
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
    // Query SQL yang sudah aman menggunakan prepared statement
    $sql = "SELECT DISTINCT
                keg.kegiatan_id,
                keg.nama_kegiatan,
                kro.kro_id,
                kro.nama_kro,
                bag.bagian_id,
                bag.nama_bagian,
                CASE
                    WHEN e.status_anggaran = ? THEN 'Baru'
                    WHEN e.status_anggaran = ? THEN 'Revisi'
                    ELSE 'Lainnya'
                END AS tipe_pengajuan
            FROM RAB_Entry e
            JOIN PAR_RO ro ON e.ro_id = ro.ro_id
            JOIN PAR_RAB_Kegiatan keg ON ro.kegiatan_id = keg.kegiatan_id
            JOIN PAR_RAB_KRO kro ON ro.kro_id = kro.kro_id
            JOIN PAR_RAB_Bagian bag ON ro.bagian_id = bag.bagian_id
            WHERE e.status_anggaran IN (?, ?)
            ORDER BY bag.nama_bagian, keg.nama_kegiatan";

    // Menyiapkan statement
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($stmt === false) {
        throw new Exception("Gagal menyiapkan statement: " . mysqli_error($koneksi));
    }
    
    // Mendefinisikan parameter status yang akan digunakan
    $status_baru = 'MENUNGGU_VERIFIKASI';
    $status_revisi = 'REVISI_MENUNGGU_VERIFIKASI';

    // Mengikat parameter ke placeholder (?)
    // Tipe data 's' berarti string
    mysqli_stmt_bind_param($stmt, "ssss", 
        $status_baru, 
        $status_revisi, 
        $status_baru, 
        $status_revisi
    );

    // Mengeksekusi query
    mysqli_stmt_execute($stmt);

    // Mendapatkan hasil
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        throw new Exception("Gagal mengambil hasil query: " . mysqli_stmt_error($stmt));
    }

    $pengajuan = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $pengajuan[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $pengajuan]);

    // Menutup statement
    mysqli_stmt_close($stmt);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>