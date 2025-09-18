<?php
// Sertakan pengelola sesi terlebih dahulu
include __DIR__.'/../config/session_manager.php';

// Redirect ke halaman login jika pengguna belum terautentikasi
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Sertakan file koneksi database
include __DIR__.'/../config/koneksi.php';

// Ambil data pengguna dari sesi
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'Guest'; // Set default jika tidak ada
$biro_id = $_SESSION['biro_id'] ?? null;
$bagian_id = $_SESSION['bagian_id'] ?? null;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-RAB | Setjen DPD RI</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/action-bar.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="app-container">
    <div class="sidebar">
        <div class="sidebar-brand">e-RAB</div>
        <ul class="sidebar-menu">
            <li><a href="index.php">📊 Dashboard</a></li>
            <li><a href="input_rab.php">✍️ Input RAB</a></li>
            <?php if ($user_role === 'admin'): ?>
            <li><a href="verifikasi.php">✅ Verifikasi Anggaran</a></li>
            <?php endif; ?>
            <li><a href="laporan.php">📋 Laporan</a></li>
            <li class="logout-btn"><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </div>

    <nav class="navbar">
        <div class="navbar-right">
            <div class="user-info">
                <span class="user-name">Pengguna (ID: <?php echo htmlspecialchars($user_id); ?>)</span>
                <span class="user-role"><?php echo htmlspecialchars($user_role); ?></span>
            </div>
        </div>
    </nav>
    
    <main class="main-content">