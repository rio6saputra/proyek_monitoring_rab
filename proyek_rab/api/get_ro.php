<?php
// Panggil koneksi database
include __DIR__ . '/../includes/config/koneksi.php';
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Kode status 'Forbidden'
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit;
}

// Ambil kro_id yang dikirim oleh JavaScript
$kro_id = isset($_GET['kro_id']) ? $_GET['kro_id'] : '';

$data = []; // Siapkan array kosong untuk menampung hasil

if (!empty($kro_id)) {
    // Buat query yang aman untuk menghindari SQL Injection
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM PAR_RO WHERE kro_id = ? ORDER BY no_ro");
    mysqli_stmt_bind_param($stmt, "s", $kro_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row; // Masukkan setiap baris data RO ke dalam array
    }
    mysqli_stmt_close($stmt);
}

// Ubah array PHP menjadi format JSON dan kirim sebagai output
echo json_encode($data);

mysqli_close($koneksi);
?>