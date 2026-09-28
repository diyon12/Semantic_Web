<?php
/* ================================================================
   Program Studi (Admin) — daftar + tambah / ubah / hapus
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['admin']);
require __DIR__ . '/../includes/layout_admin.php';

include __DIR__ . '/../config/connection.php';

$daftar = mysqli_fetch_all(mysqli_query($koneksi, "
    SELECT p.id, p.kode_prodi, p.programstudi, f.nama_fakultas,
           (SELECT COUNT(*) FROM mahasiswa m WHERE m.id_prodi = p.id) AS jumlah_mahasiswa,
           (SELECT COUNT(*) FROM users u WHERE u.id_prodi = p.id)     AS jumlah_akun
    FROM program_studi p
    JOIN fakultas f ON p.id_fakultas = f.id
    ORDER BY p.programstudi ASC
"), MYSQLI_ASSOC);

admin_buka('Program studi', 'Kelola data program studi di setiap fakultas.', 'prodi', true);
?>
      <div class="panel panel-tabel">
        <div class="panel-kepala">
          <div>
            <h3>Daftar program studi</h3>
            <p>Program studi yang masih memiliki mahasiswa tidak bisa dihapus.</p>
          </div>
          <a href="/administrator/prodi_form.php" class="tombol-utama"><?= admin_ikon('tambah') ?>Tambah program studi</a>
        </div>

        <?php if (empty($daftar)): ?>
          <div class="kosong-tabel">
            <b>Belum ada program studi</b>
            Klik "Tambah program studi" untuk membuat yang pertama.
          </div>
        <?php else: ?>
          <div class="panel-isi">
            <div class="tabel-gulir">
              <table class="tabel-data" data-cari="Cari kode, nama, atau fakultas...">
                <thead>
                  <tr><th>Kode</th><th>Nama program studi</th><th>Fakultas</th><th>Mahasiswa</th><th class="kanan">Aksi</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($daftar as $p):
                      $jml_mhs  = (int) $p['jumlah_mahasiswa'];
                      $jml_akun = (int) $p['jumlah_akun'];
                      $konfirmasi = 'Hapus program studi "' . $p['programstudi'] . '"?';
                      if ($jml_akun > 0) {
                          $konfirmasi .= " $jml_akun akun admin prodi yang terkait juga akan ikut terhapus.";
                      }
                      $konfirmasi .= ' Tindakan ini tidak bisa dibatalkan.';
                  ?>
                  <tr>
                    <td><span class="kode-chip"><?= teks($p['kode_prodi']) ?></span></td>
                    <td><span class="nama-utama"><?= teks($p['programstudi']) ?></span></td>
                    <td><?= teks($p['nama_fakultas']) ?></td>
                    <td data-order="<?= sprintf('%06d', $jml_mhs) ?>"><span class="lencana<?= $jml_mhs === 0 ? ' nol' : '' ?>"><?= $jml_mhs ?> mahasiswa</span></td>
                    <td class="kanan">
                      <div class="aksi-baris">
                        <a class="tombol-aksi" href="/administrator/prodi_form.php?id=<?= (int) $p['id'] ?>"><?= admin_ikon('pensil') ?>Ubah</a>
                        <form action="/administrator/prodi_hapus.php" method="post"
                              onsubmit="return confirm(<?= htmlspecialchars(json_encode($konfirmasi), ENT_QUOTES, 'UTF-8') ?>)">
                          <?= csrf_field() ?>
                          <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                          <button type="submit" class="tombol-aksi bahaya"
                            <?= $jml_mhs > 0 ? 'disabled title="Masih memiliki mahasiswa. Pindahkan atau hapus mahasiswanya dulu."' : '' ?>><?= admin_ikon('sampah') ?>Hapus</button>
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