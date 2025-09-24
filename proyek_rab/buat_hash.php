<?php

// ===================================================================
// SCRIPT UNTUK MEMBUAT PASSWORD HASH YANG AMAN
// ===================================================================

// 1. Ganti 'admin123' dengan password baru yang Anda inginkan.
//    Pastikan Anda mengingat password ini untuk login nanti.
// Ganti password di sini
$password_untuk_login = 'password123';


// 2. Proses pembuatan hash
//    Kode ini menggunakan algoritma BCRYPT yang direkomendasikan PHP.
//    Tidak perlu mengubah bagian ini.
$hash_database = password_hash($password_untuk_login, PASSWORD_BCRYPT);


// 3. Menampilkan hasil
//    Hasilnya akan muncul di browser saat Anda menjalankan file ini.
echo "<h1>Pembuat Password Hash</h1>";
echo "<p>Gunakan script ini untuk mereset password admin Anda.</p>";
echo "<hr>";
echo "<p><strong>Password yang akan dipakai login:</strong> " . htmlspecialchars($password_untuk_login) . "</p>";
echo "<p><strong>Hash untuk database (salin semua teks di bawah ini):</strong></p>";
echo '<textarea rows="3" style="width: 100%; font-family: monospace; font-size: 1.2em;" readonly>' . $hash_database . '</textarea>';

?>