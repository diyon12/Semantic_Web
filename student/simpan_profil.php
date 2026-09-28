<?php
/* ================================================================
   Simpan Profil Mahasiswa
   Field yang DIIZINKAN diubah: jenis_kelamin, tempat_lahir,
   tgl_lahir, alamat.
   nama (di tabel users), npm, dan id_prodi TIDAK PERNAH diambil
   dari form — aturan role ditegakkan di server, bukan cuma
   disembunyikan di tampilan.
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['mahasiswa']);

include __DIR__ . '/../config/connection.php'; // ganti ke 'koneksi_lokal.php' saat menguji di komputer sendiri

function kembali_gagal($pesan) {
    $_SESSION['pesan_galat'] = $pesan;
    header('Location: /student/profil.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /student/profil.php');
    exit;
}

$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
$tempat_lahir  = trim($_POST['tempat_lahir'] ?? '');
$tgl_lahir     = $_POST['tgl_lahir'] ?? '';
$alamat        = trim($_POST['alamat'] ?? '');

if (!in_array($jenis_kelamin, ['laki-laki', 'perempuan'], true)) {
    kembali_gagal('Jenis kelamin tidak valid.');
}

$stmt = mysqli_prepare($koneksi, "
    UPDATE mahasiswa
    SET jenis_kelamin = ?, tempat_lahir = ?, tgl_lahir = ?, alamat = ?
    WHERE npm = ?
");
mysqli_stmt_bind_param(
    $stmt, 'sssss',
    $jenis_kelamin, $tempat_lahir, $tgl_lahir, $alamat,
    $_SESSION['npm'] // selalu pakai NPM dari sesi, bukan dari form
);
mysqli_stmt_execute($stmt);

$_SESSION['pesan_sukses'] = 'Data berhasil diperbarui.';
header('Location: /student/profil.php');
exit;