<?php
/* ================================================================
   Simpan Fakultas (Admin) — INSERT kalau id kosong, UPDATE kalau ada
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

$id   = (int) ($_POST['id'] ?? 0);
$kode = strtoupper(trim($_POST['kode_fakultas'] ?? ''));
$nama = trim($_POST['nama_fakultas'] ?? '');

$halaman_form = '/administrator/fakultas_form.php' . ($id > 0 ? '?id=' . $id : '');

function gagal($pesan, $balik) {
    $_SESSION['pesan_galat'] = $pesan;
    $_SESSION['form_lama_fakultas'] = [
        'kode_fakultas' => $_POST['kode_fakultas'] ?? '',
        'nama_fakultas' => $_POST['nama_fakultas'] ?? '',
    ];
    header('Location: ' . $balik);
    exit;
}

if (!preg_match('/^[A-Z0-9]{1,10}$/', $kode)) {
    gagal('Kode fakultas harus 1–10 karakter berupa huruf atau angka, tanpa spasi.', $halaman_form);
}
if ($nama === '' || panjang_teks($nama) > 100) {
    gagal('Nama fakultas wajib diisi (maksimal 100 karakter).', $halaman_form);
}

try {
    if ($id > 0) {
        $cek = mysqli_prepare($koneksi, "SELECT id FROM fakultas WHERE id = ?");
        mysqli_stmt_bind_param($cek, 'i', $id);
        mysqli_stmt_execute($cek);
        if (!mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
            $_SESSION['pesan_galat'] = 'Fakultas tidak ditemukan.';
            header('Location: /administrator/fakultas.php');
            exit;
        }
        $stmt = mysqli_prepare($koneksi, "UPDATE fakultas SET kode_fakultas = ?, nama_fakultas = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'ssi', $kode, $nama, $id);
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO fakultas (kode_fakultas, nama_fakultas) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'ss', $kode, $nama);
    }
    mysqli_stmt_execute($stmt);
} catch (mysqli_sql_exception $e) {
    if ((int) $e->getCode() === 1062) {
        gagal('Kode fakultas "' . $kode . '" sudah dipakai fakultas lain.', $halaman_form);
    }
    gagal('Data belum bisa disimpan. Coba lagi beberapa saat.', $halaman_form);
}

$_SESSION['pesan_sukses'] = $id > 0 ? 'Fakultas berhasil diperbarui.' : 'Fakultas berhasil ditambahkan.';
header('Location: /administrator/fakultas.php');
exit;