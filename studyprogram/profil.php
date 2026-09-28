<?php
/* ================================================================
   Profil Prodi — ubah nama program studi milik sendiri
   ================================================================ */

require __DIR__ . '/../includes/auth.php';
wajib_login(['prodi']);

include __DIR__ . '/../config/connection.php'; // ganti ke 'koneksi_lokal.php' saat menguji di komputer sendiri

$id_prodi = $_SESSION['id_prodi'];

$stmt = mysqli_prepare($koneksi, "SELECT kode_prodi, programstudi FROM program_studi WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id_prodi);
mysqli_stmt_execute($stmt);
$prodi = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$pesan_sukses = $_SESSION['pesan_sukses'] ?? '';
$pesan_galat  = $_SESSION['pesan_galat'] ?? '';
unset($_SESSION['pesan_sukses'], $_SESSION['pesan_galat']);

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
<title>Profil Prodi — Sistem Informasi Akademik</title>
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
      <a href="dashboard.php" class="sidebar-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
        Dashboard
      </a>
      <a href="mahasiswa.php" class="sidebar-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"/><circle cx="17" cy="9" r="2.3"/><path d="M16 14.2c2.6.3 4.5 2.3 4.5 5.8"/></svg>
        Mahasiswa
      </a>
      <a href="profil.php" class="sidebar-link aktif">
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
        <h1>Profil prodi</h1>
        <p>Ubah nama program studi yang kamu kelola.</p>
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
      <?php if ($pesan_sukses): ?><div class="pesan-sukses"><?= teks($pesan_sukses) ?></div><?php endif; ?>
      <?php if ($pesan_galat): ?><div class="pesan-galat"><?= teks($pesan_galat) ?></div><?php endif; ?>

      <div class="panel" style="width: 100%; max-width: none;">
        <h3>Nama program studi</h3>
        <p class="panel-sub">Kode program studi terkunci dan tidak bisa diubah dari sini.</p>
        <form action="simpan_nama.php" method="post">
          <div class="kolom">
            <label for="programstudi">Nama program studi</label>
            <input type="text" id="programstudi" name="programstudi" value="<?= teks($prodi['programstudi']) ?>" required>
          </div>
          <div class="kolom">
            <label>Kode program studi</label>
            <div class="field-terkunci"><?= teks($prodi['kode_prodi']) ?></div>
          </div>
          <button type="submit" class="tombol tombol-auto">Simpan nama prodi</button>
        </form>
      </div>
    </main>
  </div>
</div>
</body>
</html>