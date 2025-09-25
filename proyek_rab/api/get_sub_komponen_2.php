<?php
// Panggil koneksi database
// Path yang benar: keluar satu folder (../), lalu masuk ke includes/config/
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Kode status 'Forbidden'
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit;
}
header('Content-Type: application/json');

// Ambil sub_komponen_id dari request
$sub_komponen_id = isset($_GET['sub_komponen_id']) ? $_GET['sub_komponen_id'] : '';

$data = [];

if (!empty($sub_komponen_id)) {
    // Query untuk mengambil data sub_komponen_2 berdasarkan sub_komponen_id
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM PAR_Sub_Komponen_2 WHERE sub_komponen_id = ? ORDER BY kode_sub_komponen_2");
    mysqli_stmt_bind_param($stmt, "s", $sub_komponen_id);
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