<?php
/* ================================================================
   Simpan Nama Program Studi
   Prodi hanya boleh mengubah nama prodi MILIKNYA SENDIRI —
   id_prodi selalu diambil dari sesi, bukan dari form.
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['prodi']);

include __DIR__ . '/../config/connection.php'; // ganti ke 'koneksi_lokal.php' saat menguji di komputer sendiri

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /studyprogram/profil.php');
    exit;
}

$programstudi = trim($_POST['programstudi'] ?? '');

if ($programstudi === '') {
    $_SESSION['pesan_galat'] = 'Nama program studi tidak boleh kosong.';
    header('Location: /studyprogram/profil.php');
    exit;
}

$stmt = mysqli_prepare($koneksi, "UPDATE program_studi SET programstudi = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'si', $programstudi, $_SESSION['id_prodi']);
mysqli_stmt_execute($stmt);

$_SESSION['pesan_sukses'] = 'Nama program studi berhasil diperbarui.';
header('Location: /studyprogram/profil.php');
exit;