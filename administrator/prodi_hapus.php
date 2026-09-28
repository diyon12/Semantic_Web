<?php
/* ================================================================
   Hapus Program Studi (Admin)
   Ditolak kalau masih ada mahasiswa. Akun admin prodi yang terkait
   ikut terhapus otomatis (ON DELETE CASCADE di tabel users).
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /administrator/prodi.php');
    exit;
}
csrf_periksa('/administrator/prodi.php');

include __DIR__ . '/../config/connection.php';
// Error database dijadikan exception supaya bisa ditangkap try/catch di bawah
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function kembali($kunci, $pesan) {
    $_SESSION[$kunci] = $pesan;
    header('Location: /administrator/prodi.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    kembali('pesan_galat', 'Data program studi tidak valid.');
}

$hitung_m = mysqli_prepare($koneksi, "SELECT COUNT(*) AS j FROM mahasiswa WHERE id_prodi = ?");
mysqli_stmt_bind_param($hitung_m, 'i', $id);
mysqli_stmt_execute($hitung_m);
$jumlah_mahasiswa = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($hitung_m))['j'];

if ($jumlah_mahasiswa > 0) {
    kembali('pesan_galat', "Program studi masih memiliki $jumlah_mahasiswa mahasiswa. Pindahkan atau hapus mahasiswanya terlebih dahulu.");
}

$hitung_a = mysqli_prepare($koneksi, "SELECT COUNT(*) AS j FROM users WHERE id_prodi = ?");
mysqli_stmt_bind_param($hitung_a, 'i', $id);
mysqli_stmt_execute($hitung_a);
$jumlah_akun = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($hitung_a))['j'];

try {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM program_studi WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $terhapus = mysqli_stmt_affected_rows($stmt);
} catch (mysqli_sql_exception $e) {
    kembali('pesan_galat', 'Program studi tidak bisa dihapus karena masih dipakai data lain.');
}

if ($terhapus < 1) {
    kembali('pesan_galat', 'Program studi tidak ditemukan.');
}

$pesan = 'Program studi berhasil dihapus.';
if ($jumlah_akun > 0) {
    $pesan .= " $jumlah_akun akun admin prodi yang terkait ikut dihapus.";
}
kembali('pesan_sukses', $pesan);