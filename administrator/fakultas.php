<?php
/* ================================================================
   Fakultas (Admin) — daftar + tambah / ubah / hapus
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['admin']);
require __DIR__ . '/../includes/layout_admin.php';

include __DIR__ . '/../config/connection.php';

$daftar = mysqli_fetch_all(mysqli_query($koneksi, "
    SELECT f.id, f.kode_fakultas, f.nama_fakultas, COUNT(p.id) AS jumlah_prodi
    FROM fakultas f LEFT JOIN program_studi p ON p.id_fakultas = f.id
    GROUP BY f.id, f.kode_fakultas, f.nama_fakultas
    ORDER BY f.nama_fakultas ASC
"), MYSQLI_ASSOC);

admin_buka('Fakultas', 'Kelola data fakultas di lingkungan universitas.', 'fakultas', true);
?>
      <div class="panel panel-tabel">
        <div class="panel-kepala">
          <div>
            <h3>Daftar fakultas</h3>
            <p>Fakultas yang masih memiliki program studi tidak bisa dihapus.</p>
          </div>
          <a href="/administrator/fakultas_form.php" class="tombol-utama"><?= admin_ikon('tambah') ?>Tambah fakultas</a>
        </div>

        <?php if (empty($daftar)): ?>
          <div class="kosong-tabel">
            <b>Belum ada fakultas</b>
            Klik "Tambah fakultas" untuk membuat yang pertama.
          </div>
        <?php else: ?>
          <div class="panel-isi">
            <div class="tabel-gulir">
              <table class="tabel-data" data-cari="Cari kode atau nama fakultas...">
                <thead>
                  <tr><th>Kode</th><th>Nama fakultas</th><th>Program studi</th><th class="kanan">Aksi</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($daftar as $f):
                      $jumlah = (int) $f['jumlah_prodi'];
                      $konfirmasi = 'Hapus fakultas "' . $f['nama_fakultas'] . '"? Tindakan ini tidak bisa dibatalkan.';
                  ?>
                  <tr>
                    <td><span class="kode-chip"><?= teks($f['kode_fakultas']) ?></span></td>
                    <td><span class="nama-utama"><?= teks($f['nama_fakultas']) ?></span></td>
                    <td data-order="<?= sprintf('%06d', $jumlah) ?>"><span class="lencana<?= $jumlah === 0 ? ' nol' : '' ?>"><?= $jumlah ?> prodi</span></td>
                    <td class="kanan">
                      <div class="aksi-baris">
                        <a class="tombol-aksi" href="/administrator/fakultas_form.php?id=<?= (int) $f['id'] ?>"><?= admin_ikon('pensil') ?>Ubah</a>
                        <form action="/administrator/fakultas_hapus.php" method="post"
                              onsubmit="return confirm(<?= htmlspecialchars(json_encode($konfirmasi), ENT_QUOTES, 'UTF-8') ?>)">
                          <?= csrf_field() ?>
                          <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                          <button type="submit" class="tombol-aksi bahaya"
                            <?= $jumlah > 0 ? 'disabled title="Masih memiliki program studi. Hapus atau pindahkan dulu program studinya."' : '' ?>><?= admin_ikon('sampah') ?>Hapus</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php endif; ?>
      </div>
<?php admin_tutup(); ?>