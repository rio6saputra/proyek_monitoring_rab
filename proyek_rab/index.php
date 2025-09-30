<?php
session_start();
include 'includes/config/koneksi.php';

// --- FUNGSI UNTUK MENANGANI "REMEMBER ME" ---
function handleRememberMe($koneksi, $user_id) {
    // Hapus token lama jika ada
    $stmt_delete = mysqli_prepare($koneksi, "DELETE FROM auth_tokens WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt_delete, "i", $user_id);
    mysqli_stmt_execute($stmt_delete);

    // Buat token baru
    $selector = bin2hex(random_bytes(16));
    $validator = bin2hex(random_bytes(32));
    $hashed_validator = password_hash($validator, PASSWORD_DEFAULT);
    $expires = new DateTime('now');
    $expires->add(new DateInterval('P30D')); // Cookie berlaku 30 hari
    $expires_db = $expires->format('Y-m-d H:i:s');

    // Simpan token ke database
    $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO auth_tokens (selector, hashed_validator, user_id, expires) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_insert, "ssis", $selector, $hashed_validator, $user_id, $expires_db);
    mysqli_stmt_execute($stmt_insert);

    // Set cookie di browser
    $cookie_value = $selector . ':' . $validator;
    setcookie('remember_me', $cookie_value, $expires->getTimestamp(), '/');
}

// Redirect ke halaman input_rab jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: input_rab.php");
    exit;
}

$error_message = '';
$is_locked = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);

    // --- LOGIKA PEMBATASAN LOGIN ---
    $sql_user = "SELECT user_id, password, user_role, biro_id, bagian_id, failed_login_attempts, last_failed_login FROM users WHERE username = ?";
    $stmt_user = mysqli_prepare($koneksi, $sql_user);
    mysqli_stmt_bind_param($stmt_user, "s", $username);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);

    if ($row = mysqli_fetch_assoc($result_user)) {
        $lockout_time = 15 * 60; // 15 menit
        if ($row['failed_login_attempts'] >= 5 && (strtotime($row['last_failed_login']) > time() - $lockout_time)) {
            $is_locked = true;
            $error_message = "Terlalu banyak percobaan gagal. Silakan coba lagi nanti.";
        } else {
            // Verifikasi password
            if (password_verify($password, $row['password'])) {
                // --- LOGIN BERHASIL ---
                // Reset percobaan gagal
                $stmt_reset = mysqli_prepare($koneksi, "UPDATE users SET failed_login_attempts = 0, last_failed_login = NULL WHERE user_id = ?");
                mysqli_stmt_bind_param($stmt_reset, "i", $row['user_id']);
                mysqli_stmt_execute($stmt_reset);
                
                // Buat sesi login
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user_role'] = $row['user_role'];
                $_SESSION['biro_id'] = $row['biro_id'];
                $_SESSION['bagian_id'] = $row['bagian_id'];

                // Handle "Remember Me" jika dicentang
                if ($remember_me) {
                    handleRememberMe($koneksi, $row['user_id']);
                }
                
                header("Location: input_rab.php");
                exit;
            } else {
                // --- LOGIN GAGAL ---
                $stmt_fail = mysqli_prepare($koneksi, "UPDATE users SET failed_login_attempts = failed_login_attempts + 1, last_failed_login = NOW() WHERE user_id = ?");
                mysqli_stmt_bind_param($stmt_fail, "i", $row['user_id']);
                mysqli_stmt_execute($stmt_fail);
                $error_message = "Username atau password salah.";
            }
        }
    } else {
        $error_message = "Username tidak ditemukan.";
    }
    mysqli_stmt_close($stmt_user);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - e-RAB</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-card">
        <h2>Login e-RAB</h2>
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required>
                    <span class="toggle-password" id="togglePassword">👁️</span>
                </div>
            </div>
            <div class="remember-me">
                <input type="checkbox" id="remember_me" name="remember_me">
                <label for="remember_me">Ingat Saya</label>
            </div>
            <button type="submit" class="btn-primary btn-login" <?php if ($is_locked) echo 'disabled'; ?>>Login</button>
        </form>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>
</body>
</html>
<?php mysqli_close($koneksi); ?>