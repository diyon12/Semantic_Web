<?php
/* ================================================================
   Hapus Fakultas (Admin)
   Ditolak kalau fakultas masih punya program studi.
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /administrator/fakultas.php');
    exit;
}
csrf_periksa('/administrator/fakultas.php');

include __DIR__ . '/../config/connection.php';
// Error database dijadikan exception supaya bisa ditangkap try/catch di bawah
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function kembali($kunci, $pesan) {
    $_SESSION[$kunci] = $pesan;
    header('Location: /administrator/fakultas.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    kembali('pesan_galat', 'Data fakultas tidak valid.');
}

$hitung = mysqli_prepare($koneksi, "SELECT COUNT(*) AS j FROM program_studi WHERE id_fakultas = ?");
mysqli_stmt_bind_param($hitung, 'i', $id);
mysqli_stmt_execute($hitung);
$jumlah_prodi = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($hitung))['j'];

if ($jumlah_prodi > 0) {
    kembali('pesan_galat', "Fakultas masih memiliki $jumlah_prodi program studi. Hapus atau pindahkan program studinya terlebih dahulu.");
}

try {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM fakultas WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $terhapus = mysqli_stmt_affected_rows($stmt);
} catch (mysqli_sql_exception $e) {
    kembali('pesan_galat', 'Fakultas tidak bisa dihapus karena masih dipakai data lain.');
}

if ($terhapus < 1) {
    kembali('pesan_galat', 'Fakultas tidak ditemukan.');
}
kembali('pesan_sukses', 'Fakultas berhasil dihapus.');