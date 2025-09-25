<?php
// api/get_filter_options.php
include __DIR__ . '/../includes/config/session_manager.php';
include __DIR__ . '/../includes/config/koneksi.php';
header('Content-Type: application/json');

// Keamanan: Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu. Akses ditolak.']);
    exit;
}

// Ambil parameter dari request JavaScript
$level = $_GET['level'] ?? '';
$parentId = $_GET['parentId'] ?? null;
// Parameter tambahan untuk level RO
$kegiatanIdForRo = $_GET['kegiatanId'] ?? null;

$data = [];
$sql = '';
$params = [];
$types = '';

if (empty($level) || ($level !== 'biro' && $parentId === null)) {
    echo json_encode($data);
    exit;
}

// Gunakan switch untuk menentukan query berdasarkan level yang diminta
switch ($level) {
    case 'biro':
        // Mengambil semua biro yang memiliki bagian terkait di par_ro
        $sql = "SELECT DISTINCT b.biro_id, b.nama_biro 
                FROM par_rab_biro b
                JOIN par_ro ro ON b.biro_id = ro.biro_id
                ORDER BY b.nama_biro";
        break;

    case 'bagian':
        // Mengambil bagian berdasarkan biro_id yang dipilih
        $sql = "SELECT DISTINCT bg.bagian_id, bg.nama_bagian 
                FROM par_rab_bagian bg
                JOIN par_ro ro ON bg.bagian_id = ro.bagian_id
                WHERE ro.biro_id = ? 
                ORDER BY bg.nama_bagian";
        $params = [$parentId];
        $types = "i";
        break;

    case 'kegiatan':
        // Mengambil kegiatan berdasarkan bagian_id yang dipilih
        $sql = "SELECT DISTINCT k.kegiatan_id, k.nama_kegiatan
                FROM par_rab_kegiatan k
                JOIN par_ro ro ON k.kegiatan_id = ro.kegiatan_id
                WHERE ro.bagian_id = ?
                ORDER BY k.nama_kegiatan";
        $params = [$parentId];
        $types = "i";
        break;

    case 'kro':
        // Mengambil KRO berdasarkan kegiatan_id yang dipilih
        $sql = "SELECT DISTINCT kr.kro_id, kr.nama_kro
                FROM par_rab_kro kr
                JOIN par_ro ro ON kr.kro_id = ro.kro_id
                WHERE ro.kegiatan_id = ?
                ORDER BY kr.nama_kro";
        $params = [$parentId];
        $types = "i";
        break;
    
    case 'ro':
        // Mengambil RO berdasarkan kro_id DAN kegiatan_id yang dipilih
        $sql = "SELECT ro_id, no_ro, nama_ro
                FROM par_ro
                WHERE kro_id = ? AND kegiatan_id = ?
                ORDER BY no_ro";
        $params = [$parentId, $kegiatanIdForRo];
        $types = "si";
        break;
}

if (!empty($sql)) {
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($stmt) {
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
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