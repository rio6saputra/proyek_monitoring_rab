<?php
// api/get_list_kegiatan.php (Versi Final)
include __DIR__ . '/../includes/config/session_manager.php';
include __DIR__ . '/../includes/config/koneksi.php';
header('Content-Type: application/json');

$user_role = $_SESSION['user_role'] ?? 'Guest';
$bagian_id_user = $_SESSION['bagian_id'] ?? null;

$data = [];

// Admin akan melihat semua kegiatan yang memiliki RO.
// User biasa akan melihat kegiatan yang memiliki RO di bagiannya.
$sql_kegiatan = "SELECT DISTINCT keg.kegiatan_id, keg.nama_kegiatan, keg.Program_id 
                 FROM PAR_RAB_Kegiatan keg
                 JOIN PAR_RO ro ON keg.kegiatan_id = ro.kegiatan_id";

$params = [];
$types = "";

// Filter hanya berlaku untuk user yang bukan admin
if ($user_role !== 'admin' && $bagian_id_user !== null) {
    $sql_kegiatan .= " WHERE ro.bagian_id = ?";
    $params[] = $bagian_id_user;
    $types .= "i";
}

$sql_kegiatan .= " ORDER BY keg.kegiatan_id";

$stmt = mysqli_prepare($koneksi, $sql_kegiatan);
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

echo json_encode($data);
mysqli_close($koneksi);
?>