<?php
// api/get_kro.php (Versi Final)
include __DIR__ . '/../includes/config/session_manager.php';
include __DIR__ . '/../includes/config/koneksi.php';
header('Content-Type: application/json');

$user_role = $_SESSION['user_role'] ?? 'Guest';
$bagian_id_user = $_SESSION['bagian_id'] ?? null;
$kegiatan_id = isset($_GET['kegiatan_id']) ? (int)$_GET['kegiatan_id'] : 0;

$data = [];

if ($kegiatan_id > 0) {
    $sql = "SELECT DISTINCT kro.kro_id, kro.nama_kro 
            FROM PAR_RO ro
            JOIN PAR_RAB_KRO kro ON ro.kro_id = kro.kro_id
            WHERE ro.kegiatan_id = ?";
            
    $params = [$kegiatan_id];
    $types = "i";

    // Filter KRO hanya untuk user biasa
    if ($user_role !== 'admin' && $bagian_id_user !== null) {
        $sql .= " AND ro.bagian_id = ?";
        $params[] = $bagian_id_user;
        $types .= "i";
    }

    $sql .= " ORDER BY kro.kro_id";
    
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
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