<?php
// api/get_list_bagian.php (Versi Final Dinamis)
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Kode status 'Forbidden'
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit;
}

// Ambil parameter dari JavaScript
$kegiatan_id = isset($_GET['kegiatan_id']) ? (int)$_GET['kegiatan_id'] : 0;
$kro_id = isset($_GET['kro_id']) ? $_GET['kro_id'] : '';

$data = [];

// Hanya jalankan query jika parameter lengkap
if ($kegiatan_id > 0 && !empty($kro_id)) {
    // Query ini hanya mengambil Bagian yang relevan dengan
    // Kegiatan dan KRO yang sedang dipilih, mengacu pada par_ro.
    $sql = "SELECT DISTINCT b.bagian_id, b.nama_bagian 
            FROM par_rab_bagian b
            INNER JOIN par_ro ro ON b.bagian_id = ro.bagian_id
            WHERE ro.kegiatan_id = ? AND ro.kro_id = ?
            ORDER BY b.nama_bagian";

    $stmt = mysqli_prepare($koneksi, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "is", $kegiatan_id, $kro_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}

echo json_encode($data);
mysqli_close($koneksi);
?>