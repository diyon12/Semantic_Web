<?php
/* ================================================================
   Form Program Studi (Admin) — tambah baru, atau ubah kalau ada ?id=
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['admin']);
require __DIR__ . '/../includes/layout_admin.php';

include __DIR__ . '/../config/connection.php';

$id   = (int) ($_GET['id'] ?? 0);
$data = ['kode_prodi' => '', 'programstudi' => '', 'id_fakultas' => ''];

if ($id > 0) {
    $stmt = mysqli_prepare($koneksi, "SELECT kode_prodi, programstudi, id_fakultas FROM program_studi WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $baris = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$baris) {
        $_SESSION['pesan_galat'] = 'Program studi tidak ditemukan.';
        header('Location: /administrator/prodi.php');
        exit;
    }
    $data = $baris;
}

$lama = $_SESSION['form_lama_prodi'] ?? null;
unset($_SESSION['form_lama_prodi']);
if (is_array($lama)) {
    $data = array_merge($data, $lama);
}

$daftar_fakultas = mysqli_fetch_all(mysqli_query($koneksi,
    "SELECT id, kode_fakultas, nama_fakultas FROM fakultas ORDER BY nama_fakultas ASC"), MYSQLI_ASSOC);

$mode_ubah = $id > 0;
admin_buka(
    $mode_ubah ? 'Ubah program studi' : 'Tambah program studi',
    $mode_ubah ? 'Perbarui data program studi.' : 'Isi data program studi baru.',
    'prodi'
);
?>
      <?php if (empty($daftar_fakultas)): ?>
        <div class="panel panel-tabel form-crud">
          <div class="kosong-tabel">
            <b>Belum ada fakultas</b>
            Program studi harus berada di bawah sebuah fakultas.
            <div style="margin-top:16px;"><a href="/administrator/fakultas_form.php" class="tombol-utama"><?= admin_ikon('tambah') ?>Tambah fakultas dulu</a></div>
          </div>
        </div>
      <?php else: ?>
      <div class="panel panel-tabel form-crud">
        <div class="panel-kepala">
          <div>
            <h3><?= $mode_ubah ? 'Ubah data program studi' : 'Data program studi baru' ?></h3>
            <p>Semua kolom wajib diisi.</p>
          </div>
        </div>
        <div class="panel-isi">
          <form action="/administrator/prodi_simpan.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="kolom">
              <label for="kode_prodi">Kode program studi</label>
              <input type="text" id="kode_prodi" name="kode_prodi" maxlength="5" inputmode="numeric"
                     pattern="[0-9]{5}" required autofocus value="<?= teks($data['kode_prodi']) ?>">
              <div class="bantuan">Tepat 5 digit angka, mengikuti kode resmi PDDIKTI. Contoh: 55201</div>
            </div>
            <div class="kolom">
              <label for="programstudi">Nama program studi</label>
              <input type="text" id="programstudi" name="programstudi" maxlength="100" required
                     value="<?= teks($data['programstudi']) ?>">
              <div class="bantuan">Contoh: Teknik Informatika, Sistem Informasi</div>
            </div>
            <div class="kolom">
              <label for="id_fakultas">Fakultas</label>
              <select id="id_fakultas" name="id_fakultas" required>
                <option value="">— Pilih fakultas —</option>
                <?php foreach ($daftar_fakultas as $f): ?>
                  <option value="<?= (int) $f['id'] ?>" <?= (int) $data['id_fakultas'] === (int) $f['id'] ? 'selected' : '' ?>>
                    <?= teks($f['kode_fakultas']) ?> — <?= teks($f['nama_fakultas']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="aksi-form">
              <button type="submit" class="tombol-utama"><?= $mode_ubah ? 'Simpan perubahan' : 'Tambah program studi' ?></button>
              <a href="/administrator/prodi.php" class="tombol-garis"><?= admin_ikon('kembali') ?>Batal</a>
            </div>
          </form>
        </div>
      </div>
      <?php endif; ?>
<?php admin_tutup(); ?>