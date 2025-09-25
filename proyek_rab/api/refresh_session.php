<?php
// api/refresh_session.php
include __DIR__ . '/../includes/config/session_manager.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada sesi aktif.']);
    exit;
}

// Cukup dengan mengakses sesi, PHP akan otomatis memperbarui timestamp-nya.
// Jika ingin lebih eksplisit, kita bisa tambahkan ini:
$_SESSION['last_activity'] = time();

echo json_encode(['status' => 'success', 'message' => 'Sesi berhasil diperpanjang.']);
?>