<?php
// Pengaturan untuk koneksi ke database
$db_host = 'localhost'; // Nama server database, biasanya 'localhost'
$db_user = 'root';      // Username database, default XAMPP adalah 'root'
$db_pass = '';          // Password database, default XAMPP kosong
$db_name = 'erab_setjen_dpd'; // Nama database yang sudah kita buat

// Membuat koneksi ke database menggunakan mysqli
$koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Memeriksa apakah koneksi berhasil atau gagal
if (!$koneksi) {
    // Jika gagal, hentikan script dan tampilkan pesan error
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Mengatur set karakter menjadi utf8mb4 untuk mendukung berbagai karakter
mysqli_set_charset($koneksi, "utf8mb4");

?>