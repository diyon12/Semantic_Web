<?php
/* ================================================================
   Dashboard Prodi — layout sidebar, sambutan + statistik
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['prodi']);

include __DIR__ . '/../config/connection.php'; // ganti ke 'koneksi_lokal.php' saat menguji di komputer sendiri

$id_prodi = $_SESSION['id_prodi'];

$stmt = mysqli_prepare($koneksi, "SELECT kode_prodi, programstudi FROM program_studi WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id_prodi);
mysqli_stmt_execute($stmt);
$prodi = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$stmt2 = mysqli_prepare($koneksi, "SELECT COUNT(*) AS jumlah FROM mahasiswa WHERE id_prodi = ?");
mysqli_stmt_bind_param($stmt2, 'i', $id_prodi);
mysqli_stmt_execute($stmt2);
$total_mahasiswa = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2))['jumlah'];

$stmt3 = mysqli_prepare($koneksi, "SELECT COUNT(*) AS jumlah FROM mahasiswa WHERE id_prodi = ? AND jenis_kelamin = 'laki-laki'");
mysqli_stmt_bind_param($stmt3, 'i', $id_prodi);
mysqli_stmt_execute($stmt3);
$total_laki = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt3))['jumlah'];

$total_perempuan = $total_mahasiswa - $total_laki;
$persen_laki = $total_mahasiswa > 0 ? round($total_laki / $total_mahasiswa * 100) : 0;

function teks($nilai) {
    return htmlspecialchars($nilai ?? '-', ENT_QUOTES, 'UTF-8');
}
function inisial($nama) {
    $kata = preg_split('/\s+/', trim($nama ?? ''));
    return strtoupper(($kata[0][0] ?? '') . ($kata[1][0] ?? '')) ?: '-';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Prodi — Sistem Informasi Akademik</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,900&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="app-shell">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="lambang"><?= teks($prodi['kode_prodi']) ?></div>
      <div class="sidebar-brand-teks">UM Bengkulu<br>Portal Prodi</div>
    </div>
    <div class="sidebar-menu-label">Menu</div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="sidebar-link aktif">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
        Dashboard
      </a>
      <a href="mahasiswa.php" class="sidebar-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"/><circle cx="17" cy="9" r="2.3"/><path d="M16 14.2c2.6.3 4.5 2.3 4.5 5.8"/></svg>
        Mahasiswa
      </a>
      <a href="profil.php" class="sidebar-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 2 8l10 5 10-5-10-5z"/><path d="M6 10.5V16c0 1.5 2.7 3 6 3s6-1.5 6-3v-5.5"/></svg>
        Profil prodi
      </a>
    </nav>
    <div class="sidebar-keluar">
      <a href="/index.php" class="sidebar-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
        Beranda situs
      </a>
      <a href="/logout.php" class="sidebar-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Keluar
      </a>
    </div>
  </aside>

  <div class="app-main">
    <header class="app-topbar">
      <div>
        <h1>Dashboard</h1>
        <p>Mengelola <?= teks($prodi['programstudi']) ?> (<?= teks($prodi['kode_prodi']) ?>)</p>
      </div>
      <div class="app-topbar-profil">
        <div class="avatar-kecil"><?= teks(inisial($_SESSION['nama_tampil'])) ?></div>
        <div>
          <div class="topbar-nama"><?= teks($_SESSION['nama_tampil']) ?></div>
          <div class="topbar-sub">Admin program studi</div>
        </div>
      </div>
    </header>

    <main class="app-content">
      <div class="grid-statistik-v2">
        <div class="kartu-stat-v2 aksen">
          <div class="kartu-stat-v2-atas">
            <span class="kartu-stat-v2-label">Total mahasiswa</span>
            <div class="kartu-stat-v2-ikon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"/><circle cx="17" cy="9" r="2.3"/><path d="M16 14.2c2.6.3 4.5 2.3 4.5 5.8"/></svg></div>
          </div>
          <span class="kartu-stat-v2-angka"><?= $total_mahasiswa ?></span>
          <div class="kartu-stat-v2-catatan">Terdaftar di program studi ini</div>
        </div>
        <div class="kartu-stat-v2">
          <div class="kartu-stat-v2-atas">
            <span class="kartu-stat-v2-label">Laki-laki</span>
            <div class="kartu-stat-v2-ikon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-7 8-7s8 3 8 7"/></svg></div>
          </div>
          <span class="kartu-stat-v2-angka"><?= $total_laki ?></span>
          <div class="kartu-stat-v2-catatan">Mahasiswa laki-laki</div>
        </div>
        <div class="kartu-stat-v2">
          <div class="kartu-stat-v2-atas">
            <span class="kartu-stat-v2-label">Perempuan</span>
            <div class="kartu-stat-v2-ikon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-7 8-7s8 3 8 7"/></svg></div>
          </div>
          <span class="kartu-stat-v2-angka"><?= $total_perempuan ?></span>
          <div class="kartu-stat-v2-catatan">Mahasiswa perempuan</div>
        </div>
      </div>

      <div class="grid-panel">
        <div class="panel">
          <h3>Ringkasan program studi</h3>
          <p class="panel-sub">Data pokok program studi yang kamu kelola.</p>
          <div class="grid-info" style="margin-bottom:0;">
            <div class="kartu-info">
              <div class="ikon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8" cy="10.5" r="2"/><line x1="13" y1="9.5" x2="18" y2="9.5"/></svg></div>
              <div><div class="teks-label">Kode program studi</div><div class="teks-nilai"><?= teks($prodi['kode_prodi']) ?></div></div>
            </div>
            <div class="kartu-info">
              <div class="ikon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 2 8l10 5 10-5-10-5z"/><path d="M6 10.5V16c0 1.5 2.7 3 6 3s6-1.5 6-3v-5.5"/></svg></div>
              <div><div class="teks-label">Nama program studi</div><div class="teks-nilai"><?= teks($prodi['programstudi']) ?></div></div>
            </div>
          </div>
          <a href="mahasiswa.php" class="tombol-tautan" style="margin-top:20px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"/></svg>
            Lihat daftar mahasiswa
          </a>
        </div>

        <div class="panel">
          <h3>Rasio jenis kelamin</h3>
          <p class="panel-sub">Dari <?= $total_mahasiswa ?> mahasiswa terdaftar.</p>
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
    </main>
  </div>
</div>
</body>
</html>