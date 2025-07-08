<?php
$host     = "localhost";    // Ganti jika hosting beda
$username = "root";         // Default untuk XAMPP
$password = "";             // Default XAMPP tanpa password
$database = "db_mts";   // Ganti sesuai nama database kamu

// Buat koneksi
$koneksi = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}
?>
