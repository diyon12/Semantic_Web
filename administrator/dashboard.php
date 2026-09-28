<?php
/* ================================================================
   Dashboard Admin — statistik + grafik
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['admin']);
require __DIR__ . '/../includes/layout_admin.php';

include __DIR__ . '/../config/connection.php';

$total_fakultas  = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS j FROM fakultas"))['j'];
$total_prodi     = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS j FROM program_studi"))['j'];
$total_mahasiswa = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS j FROM mahasiswa"))['j'];
$total_akun      = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS j FROM users"))['j'];

$total_laki      = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS j FROM mahasiswa WHERE jenis_kelamin = 'laki-laki'"))['j'];
$total_perempuan = $total_mahasiswa - $total_laki;
$persen_laki     = $total_mahasiswa > 0 ? round($total_laki / $total_mahasiswa * 100) : 0;

$per_prodi = mysqli_fetch_all(mysqli_query($koneksi, "
    SELECT p.programstudi, COUNT(m.npm) AS jumlah
    FROM program_studi p LEFT JOIN mahasiswa m ON m.id_prodi = p.id
    GROUP BY p.id, p.programstudi
    ORDER BY jumlah DESC
"), MYSQLI_ASSOC);
$maks_per_prodi = 1;
foreach ($per_prodi as $p) {
    $maks_per_prodi = max($maks_per_prodi, (int) $p['jumlah']);
}

admin_buka('Dashboard', 'Ringkasan seluruh data akademik.', 'dashboard');
?>
      <div class="grid-statistik-v2">
        <div class="kartu-stat-v2 aksen">
          <div class="kartu-stat-v2-atas">
            <span class="kartu-stat-v2-label">Total mahasiswa</span>
            <div class="kartu-stat-v2-ikon"><?= admin_ikon('orang') ?></div>
          </div>
          <span class="kartu-stat-v2-angka"><?= $total_mahasiswa ?></span>
          <div class="kartu-stat-v2-catatan">Terdaftar di seluruh prodi</div>
        </div>
        <div class="kartu-stat-v2">
          <div class="kartu-stat-v2-atas">
            <span class="kartu-stat-v2-label">Program studi</span>
            <div class="kartu-stat-v2-ikon"><?= admin_ikon('topi') ?></div>
          </div>
          <span class="kartu-stat-v2-angka"><?= $total_prodi ?></span>
          <div class="kartu-stat-v2-catatan">Program studi aktif</div>
        </div>
        <div class="kartu-stat-v2">
          <div class="kartu-stat-v2-atas">
            <span class="kartu-stat-v2-label">Fakultas</span>
            <div class="kartu-stat-v2-ikon"><?= admin_ikon('gedung') ?></div>
          </div>
          <span class="kartu-stat-v2-angka"><?= $total_fakultas ?></span>
          <div class="kartu-stat-v2-catatan">Fakultas terdaftar</div>
        </div>
        <div class="kartu-stat-v2">
          <div class="kartu-stat-v2-atas">
            <span class="kartu-stat-v2-label">Total akun</span>
            <div class="kartu-stat-v2-ikon"><?= admin_ikon('orang') ?></div>
          </div>
          <span class="kartu-stat-v2-angka"><?= $total_akun ?></span>
          <div class="kartu-stat-v2-catatan">Mahasiswa, prodi &amp; admin</div>
        </div>
      </div>

      <div class="grid-panel">
        <div class="panel">
          <h3>Mahasiswa per program studi</h3>
          <p class="panel-sub">Perbandingan jumlah mahasiswa antar program studi.</p>
          <?php if (empty($per_prodi)): ?>
            <p class="keterangan-field">Belum ada program studi.</p>
          <?php else: ?>
            <div class="bar-list">
              <?php foreach ($per_prodi as $p): ?>
              <div>
                <div class="bar-baris-label"><span><?= teks($p['programstudi']) ?></span><span><?= (int) $p['jumlah'] ?></span></div>
                <div class="bar-trek"><div class="bar-isi" style="width:<?= round((int) $p['jumlah'] / $maks_per_prodi * 100) ?>%;"></div></div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="panel">
          <h3>Rasio jenis kelamin</h3>
          <p class="panel-sub">Seluruh mahasiswa, semua prodi.</p>
          <?php if ($total_mahasiswa > 0): ?>
          <div class="donat-bungkus">
            <div class="donat" style="--donat-persen:<?= $persen_laki ?>;">
              <div class="donat-tengah"><?= $persen_laki ?>%</div>
            </div>
            <div class="legenda-donat">
              <div class="legenda-item"><span class="legenda-titik" style="background:var(--hijau-tua);"></span>Laki-laki (<?= $total_laki ?>)</div>
              <div class="legenda-item"><span class="legenda-titik" style="background:#edeae1;"></span>Perempuan (<?= $total_perempuan ?>)</div>
            </div>
          </div>
          <?php else: ?>
            <p class="keterangan-field">Belum ada mahasiswa untuk dihitung rasionya.</p>
          <?php endif; ?>
        </div>
      </div>
<?php admin_tutup(); ?>