<?php
// includes/layout/header.php (Versi Final dengan Auto-Login)

// --- BLOK KODE BARU UNTUK AUTO-LOGIN DARI COOKIE ---
// Mulai sesi di paling atas untuk memastikan ketersediaannya
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek cookie HANYA jika sesi belum ada
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    // Sertakan koneksi hanya jika diperlukan
    include_once __DIR__.'/../config/koneksi.php'; 

    list($selector, $validator) = explode(':', $_COOKIE['remember_me'], 2);

    if ($selector && $validator) {
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM auth_tokens WHERE selector = ? AND expires >= NOW()");
        mysqli_stmt_bind_param($stmt, "s", $selector);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($token_data = mysqli_fetch_assoc($result)) {
            if (password_verify($validator, $token_data['hashed_validator'])) {
                // Token valid, buat ulang sesi dari data user
                $user_id = $token_data['user_id'];
                $stmt_user = mysqli_prepare($koneksi, "SELECT user_id, user_role, biro_id, bagian_id FROM users WHERE user_id = ?");
                mysqli_stmt_bind_param($stmt_user, "i", $user_id);
                mysqli_stmt_execute($stmt_user);
                $user_result = mysqli_stmt_get_result($stmt_user);
                
                if ($user_data = mysqli_fetch_assoc($user_result)) {
                    $_SESSION['user_id'] = $user_data['user_id'];
                    $_SESSION['user_role'] = $user_data['user_role'];
                    $_SESSION['biro_id'] = $user_data['biro_id'];
                    $_SESSION['bagian_id'] = $user_data['bagian_id'];
                }
            }
        }
    }
}
// --- BLOK KODE BARU SELESAI ---


// Redirect ke halaman login jika pengguna belum terautentikasi (setelah pengecekan cookie)
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Sertakan file koneksi database jika belum ada (untuk sisa halaman)
if (!isset($koneksi)) {
    include __DIR__.'/../config/koneksi.php';
}

// Ambil data pengguna dari sesi yang sudah valid
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'Guest';
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