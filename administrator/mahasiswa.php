<?php
/* ================================================================
   Mahasiswa (Admin) — daftar semua mahasiswa, admin bisa ubah nama
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['admin']);
require __DIR__ . '/../includes/layout_admin.php';

include __DIR__ . '/../config/connection.php';

$daftar_mahasiswa = mysqli_fetch_all(mysqli_query($koneksi, "
    SELECT m.npm, u.id AS user_id, u.name AS nama_mahasiswa, p.programstudi
    FROM mahasiswa m
    JOIN users u ON m.user_id = u.id
    JOIN program_studi p ON m.id_prodi = p.id
    ORDER BY u.name ASC
"), MYSQLI_ASSOC);

admin_buka('Mahasiswa', count($daftar_mahasiswa) . ' mahasiswa dari seluruh program studi.', 'mahasiswa');
?>
      <?php if (empty($daftar_mahasiswa)): ?>
        <div class="kosong"><p>Belum ada mahasiswa terdaftar.</p></div>
      <?php else: ?>
        <div class="panel" style="padding:0;overflow:hidden;">
          <div class="tabel-bungkus" style="border:none;border-radius:0;">
            <table>
              <thead><tr><th>NPM</th><th>Nama mahasiswa</th><th>Program studi</th></tr></thead>
              <tbody>
                <?php foreach ($daftar_mahasiswa as $m): ?>
                <tr>
                  <td><?= teks($m['npm']) ?></td>
                  <td>
                    <form class="form-inline" action="simpan_nama_mahasiswa.php" method="post">
                      <input type="hidden" name="user_id" value="<?= teks($m['user_id']) ?>">
                      <input type="text" name="name" value="<?= teks($m['nama_mahasiswa']) ?>" required>
                      <button type="submit" class="tombol-kecil">Simpan</button>
                    </form>
                  </td>
                  <td><?= teks($m['programstudi']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <p class="keterangan-field" style="margin-top:14px;">Admin hanya berwenang mengubah nama mahasiswa. Data lain diubah oleh mahasiswa sendiri.</p>
      <?php endif; ?>
<?php admin_tutup(); ?>