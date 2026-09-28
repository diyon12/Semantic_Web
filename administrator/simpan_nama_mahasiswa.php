<?php
/* ================================================================
   Simpan Nama Mahasiswa (khusus Admin)
   Nama sekarang tersimpan di tabel users, bukan mahasiswa.
   Query dibatasi "AND role = 'mahasiswa'" supaya endpoint ini
   tidak bisa disalahgunakan untuk mengubah nama admin/prodi lain.
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['admin']);

include __DIR__ . '/../config/connection.php'; // ganti ke 'koneksi_lokal.php' saat menguji di komputer sendiri

function kembali_gagal($pesan) {
    $_SESSION['pesan_galat'] = $pesan;
    header('Location: /administrator/mahasiswa.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /administrator/mahasiswa.php');
    exit;
}

$user_id = trim($_POST['user_id'] ?? '');
$name    = trim($_POST['name'] ?? '');

if ($user_id === '' || $name === '') {
    kembali_gagal('Nama tidak boleh kosong.');
}

$stmt = mysqli_prepare($koneksi, "UPDATE users SET name = ? WHERE id = ? AND role = 'mahasiswa'");
mysqli_stmt_bind_param($stmt, 'si', $name, $user_id);
mysqli_stmt_execute($stmt);

$_SESSION['pesan_sukses'] = 'Nama mahasiswa berhasil diperbarui.';
header('Location: /administrator/mahasiswa.php');
exit;