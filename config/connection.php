<?php
// ====================================================================
// File Koneksi Database
// Host    : sql101.infinityfree.com
// Database: if0_42928299_sql_websemantik
// ====================================================================

$host     = "sql101.infinityfree.com";
$username = "if0_42928299";
$password = "FknRTaKmkni";
$database = "if0_42928299_sql_websemantik";

// Membuat koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Set charset agar karakter khusus (misalnya nama dengan aksen) tampil benar
mysqli_set_charset($koneksi, "utf8mb4");
?>