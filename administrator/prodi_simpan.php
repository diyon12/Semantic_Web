<?php
/* ================================================================
   Simpan Program Studi (Admin) — INSERT kalau id kosong, UPDATE kalau ada
   Kode prodi divalidasi 5 digit di sini, karena constraint CHECK di
   MySQL lama bisa saja tidak ditegakkan.
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

$id          = (int) ($_POST['id'] ?? 0);
$kode        = trim($_POST['kode_prodi'] ?? '');
$nama        = trim($_POST['programstudi'] ?? '');
$id_fakultas = (int) ($_POST['id_fakultas'] ?? 0);

$halaman_form = '/administrator/prodi_form.php' . ($id > 0 ? '?id=' . $id : '');

function gagal($pesan, $balik) {
    $_SESSION['pesan_galat'] = $pesan;
    $_SESSION['form_lama_prodi'] = [
        'kode_prodi'   => $_POST['kode_prodi'] ?? '',
        'programstudi' => $_POST['programstudi'] ?? '',
        'id_fakultas'  => $_POST['id_fakultas'] ?? '',
    ];
    header('Location: ' . $balik);
    exit;
}

if (!preg_match('/^[0-9]{5}$/', $kode)) {
    gagal('Kode program studi harus tepat 5 digit angka.', $halaman_form);
}
if ($nama === '' || panjang_teks($nama) > 100) {
    gagal('Nama program studi wajib diisi (maksimal 100 karakter).', $halaman_form);
}
if ($id_fakultas <= 0) {
    gagal('Pilih fakultas untuk program studi ini.', $halaman_form);
}

try {
    $cek_f = mysqli_prepare($koneksi, "SELECT id FROM fakultas WHERE id = ?");
    mysqli_stmt_bind_param($cek_f, 'i', $id_fakultas);
    mysqli_stmt_execute($cek_f);
    if (!mysqli_fetch_assoc(mysqli_stmt_get_result($cek_f))) {
        gagal('Fakultas yang dipilih tidak ditemukan.', $halaman_form);
    }

    if ($id > 0) {
        $cek = mysqli_prepare($koneksi, "SELECT id FROM program_studi WHERE id = ?");
        mysqli_stmt_bind_param($cek, 'i', $id);
        mysqli_stmt_execute($cek);
        if (!mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
            $_SESSION['pesan_galat'] = 'Program studi tidak ditemukan.';
            header('Location: /administrator/prodi.php');
            exit;
        }
        $stmt = mysqli_prepare($koneksi, "UPDATE program_studi SET kode_prodi = ?, programstudi = ?, id_fakultas = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'ssii', $kode, $nama, $id_fakultas, $id);
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO program_studi (kode_prodi, programstudi, id_fakultas) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssi', $kode, $nama, $id_fakultas);
    }
    mysqli_stmt_execute($stmt);
} catch (mysqli_sql_exception $e) {
    if ((int) $e->getCode() === 1062) {
        gagal('Kode program studi ' . $kode . ' sudah dipakai program studi lain.', $halaman_form);
    }
    gagal('Data belum bisa disimpan. Coba lagi beberapa saat.', $halaman_form);
}

$_SESSION['pesan_sukses'] = $id > 0 ? 'Program studi berhasil diperbarui.' : 'Program studi berhasil ditambahkan.';
header('Location: /administrator/prodi.php');
exit;