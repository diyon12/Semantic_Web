<?php
/* ================================================================
   Dashboard Mahasiswa — layout sidebar
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['mahasiswa']);

include __DIR__ . '/../config/connection.php'; // ganti ke 'koneksi_lokal.php' saat menguji di komputer sendiri

$stmt = mysqli_prepare($koneksi, "
    SELECT m.npm, u.name AS nama_mahasiswa, u.email, m.jenis_kelamin, m.tempat_lahir, m.tgl_lahir, m.alamat,
           p.kode_prodi, p.programstudi, f.nama_fakultas
    FROM mahasiswa m
    JOIN users u ON m.user_id = u.id
    JOIN program_studi p ON m.id_prodi = p.id
    JOIN fakultas f ON p.id_fakultas = f.id
    WHERE m.npm = ?
");
mysqli_stmt_bind_param($stmt, 's', $_SESSION['npm']);
mysqli_stmt_execute($stmt);
$profil = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Kelengkapan profil: dari 3 field yang boleh diisi sendiri
$terisi = 0;
if ($profil) {
    foreach (['tempat_lahir', 'tgl_lahir', 'alamat'] as $f) {
        if (!empty($profil[$f])) $terisi++;
    }
}
$persen_lengkap = $profil ? round($terisi / 3 * 100) : 0;

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
<title>Dashboard Mahasiswa — Sistem Informasi Akademik</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,900&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="app-shell">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="lambang">TI</div>
      <div class="sidebar-brand-teks">UM Bengkulu<br>Portal Mahasiswa</div>
    </div>
    <div class="sidebar-menu-label">Menu</div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="sidebar-link aktif">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
        Dashboard
      </a>
      <a href="profil.php" class="sidebar-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-7 8-7s8 3 8 7"/></svg>
        Profil saya
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
    <?php if (!$profil): ?>
      <div class="app-content"><div class="kosong"><p>Data mahasiswa tidak ditemukan untuk akun ini.</p></div></div>
    <?php else: ?>
    <header class="app-topbar">
      <div>
        <h1>Dashboard</h1>
        <p>Selamat datang kembali, <?= teks($profil['nama_mahasiswa']) ?>.</p>
      </div>
      <div class="app-topbar-profil">
        <div class="avatar-kecil"><?= teks(inisial($profil['nama_mahasiswa'])) ?></div>
        <div>
          <div class="topbar-nama"><?= teks($profil['nama_mahasiswa']) ?></div>
          <div class="topbar-sub"><?= teks($profil['email']) ?></div>
        </div>
      </div>
    </header>

    <main class="app-content">
      <div class="grid-statistik-v2">
        <div class="kartu-stat-v2 aksen">
          <div class="kartu-stat-v2-atas">
            <span class="kartu-stat-v2-label">NPM</span>
            <div class="kartu-stat-v2-ikon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8" cy="10.5" r="2"/><line x1="13" y1="9.5" x2="18" y2="9.5"/></svg></div>
          </div>
          <span class="kartu-stat-v2-angka" style="font-size:22px;"><?= teks($profil['npm']) ?></span>
          <div class="kartu-stat-v2-catatan">Nomor pokok mahasiswa</div>
        </div>
        <div class="kartu-stat-v2">
          <div class="kartu-stat-v2-atas">
            <span class="kartu-stat-v2-label">Program studi</span>
            <div class="kartu-stat-v2-ikon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 2 8l10 5 10-5-10-5z"/><path d="M6 10.5V16c0 1.5 2.7 3 6 3s6-1.5 6-3v-5.5"/></svg></div>
          </div>
          <span class="kartu-stat-v2-angka" style="font-size:19px;"><?= teks($profil['programstudi']) ?></span>
          <div class="kartu-stat-v2-catatan">Kode <?= teks($profil['kode_prodi']) ?></div>
        </div>
        <div class="kartu-stat-v2">
          <div class="kartu-stat-v2-atas">
            <span class="kartu-stat-v2-label">Fakultas</span>
            <div class="kartu-stat-v2-ikon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="1.5"/><line x1="8" y1="8" x2="16" y2="8"/><line x1="8" y1="12" x2="16" y2="12"/></svg></div>
          </div>
          <span class="kartu-stat-v2-angka" style="font-size:19px;"><?= teks($profil['nama_fakultas']) ?></span>
          <div class="kartu-stat-v2-catatan">Naungan program studi</div>
        </div>
      </div>

      <div class="grid-panel">
        <div class="panel">
          <h3>Info akademik</h3>
          <p class="panel-sub">Data pokok yang terhubung ke akun kamu.</p>
          <div class="grid-info" style="margin-bottom:0;">
            <div class="kartu-info">
              <div class="ikon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-7 8-7s8 3 8 7"/></svg></div>
              <div><div class="teks-label">Jenis kelamin</div><div class="teks-nilai"><?= $profil['jenis_kelamin'] === 'laki-laki' ? 'Laki-laki' : 'Perempuan' ?></div></div>
            </div>
            <div class="kartu-info">
              <div class="ikon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s7-6.5 7-12a7 7 0 1 0-14 0c0 5.5 7 12 7 12z"/><circle cx="12" cy="10" r="2.3"/></svg></div>
              <div><div class="teks-label">Alamat</div><div class="teks-nilai"><?= teks($profil['alamat']) ?></div></div>
            </div>
          </div>
        </div>

        <div class="panel">
          <h3>Kelengkapan profil</h3>
          <p class="panel-sub">Tempat lahir, tanggal lahir, dan alamat.</p>
          <div class="donat-bungkus">
            <div class="donat" style="--donat-persen:<?= $persen_lengkap ?>;">
              <div class="donat-tengah"><?= $persen_lengkap ?>%</div>
            </div>
            <a href="profil.php" class="tombol-tautan" style="margin-top:18px;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4l11-11-4-4L4 16v4z"/></svg>
              Lengkapi profil
            </a>
          </div>
        </div>
      </div>
    </main>
    <?php endif; ?>
  </div>
</div>
</body>
</html>