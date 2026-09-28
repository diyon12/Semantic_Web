<?php
/* ================================================================
   Form Fakultas (Admin) — tambah baru, atau ubah kalau ada ?id=
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['admin']);
require __DIR__ . '/../includes/layout_admin.php';

include __DIR__ . '/../config/connection.php';

$id   = (int) ($_GET['id'] ?? 0);
$data = ['kode_fakultas' => '', 'nama_fakultas' => ''];

if ($id > 0) {
    $stmt = mysqli_prepare($koneksi, "SELECT kode_fakultas, nama_fakultas FROM fakultas WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $baris = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$baris) {
        $_SESSION['pesan_galat'] = 'Fakultas tidak ditemukan.';
        header('Location: /administrator/fakultas.php');
        exit;
    }
    $data = $baris;
}

// Isian sebelumnya kalau validasi gagal, supaya tidak perlu mengetik ulang
$lama = $_SESSION['form_lama_fakultas'] ?? null;
unset($_SESSION['form_lama_fakultas']);
if (is_array($lama)) {
    $data = array_merge($data, $lama);
}

$mode_ubah = $id > 0;
admin_buka(
    $mode_ubah ? 'Ubah fakultas' : 'Tambah fakultas',
    $mode_ubah ? 'Perbarui kode atau nama fakultas.' : 'Isi data fakultas baru.',
    'fakultas'
);
?>
      <div class="panel panel-tabel form-crud">
        <div class="panel-kepala">
          <div>
            <h3><?= $mode_ubah ? 'Ubah data fakultas' : 'Data fakultas baru' ?></h3>
            <p>Semua kolom wajib diisi.</p>
          </div>
        </div>
        <div class="panel-isi">
          <form action="/administrator/fakultas_simpan.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="kolom">
              <label for="kode_fakultas">Kode fakultas</label>
              <input type="text" id="kode_fakultas" name="kode_fakultas" maxlength="10" required autofocus
                     style="text-transform:uppercase;" value="<?= teks($data['kode_fakultas']) ?>">
              <div class="bantuan">Huruf atau angka tanpa spasi, maksimal 10 karakter. Contoh: FT, FEB</div>
            </div>
            <div class="kolom">
              <label for="nama_fakultas">Nama fakultas</label>
              <input type="text" id="nama_fakultas" name="nama_fakultas" maxlength="100" required
                     value="<?= teks($data['nama_fakultas']) ?>">
              <div class="bantuan">Contoh: Teknik, Ekonomi dan Bisnis</div>
            </div>
            <div class="aksi-form">
              <button type="submit" class="tombol-utama"><?= $mode_ubah ? 'Simpan perubahan' : 'Tambah fakultas' ?></button>
              <a href="/administrator/fakultas.php" class="tombol-garis"><?= admin_ikon('kembali') ?>Batal</a>
            </div>
          </form>
        </div>
      </div>
<?php admin_tutup(); ?>