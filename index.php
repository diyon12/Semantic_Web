<?php
/* ================================================================
   Landing Page — Informasi Sistem Akademik
   Universitas Muhammadiyah Bengkulu
   Halaman ini berdiri sendiri (CSS ada di dalam file ini),
   jadi tidak bergantung pada style.css.
   ================================================================ */

session_start();
$sudah_login = isset($_SESSION['role']);
$tautan_dashboard = [
    'mahasiswa' => '/student/dashboard.php',
    'prodi'     => '/studyprogram/dashboard.php',
    'admin'     => '/administrator/dashboard.php',
][$_SESSION['role'] ?? ''] ?? '/login.php';

include __DIR__ . '/config/connection.php'; // ganti ke 'koneksi_lokal.php' saat menguji di komputer sendiri

$total_mahasiswa = 0;
$total_prodi     = 0;
$total_fakultas  = 0;

if ($koneksi) {
    if ($r = mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM mahasiswa")) {
        $total_mahasiswa = mysqli_fetch_assoc($r)['jumlah'];
    }
    if ($r = mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM program_studi")) {
        $total_prodi = mysqli_fetch_assoc($r)['jumlah'];
    }
    if ($r = mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM fakultas")) {
        $total_fakultas = mysqli_fetch_assoc($r)['jumlah'];
    }
}

function teks($nilai) {
    return htmlspecialchars($nilai ?? '-', ENT_QUOTES, 'UTF-8');
}

// Kumpulan ikon garis (viewBox 24x24), dipakai di lencana, navbar, dan baris statistik
$ikon = [
    'cap'      => '<path d="M12 3 2 8l10 5 10-5-10-5z"/><path d="M6 10.5V16c0 1.5 2.7 3 6 3s6-1.5 6-3v-5.5"/>',
    'book'     => '<path d="M4 5a2 2 0 012-2h13v16H6a2 2 0 00-2 2V5z"/><path d="M4 19a2 2 0 012-2h13"/>',
    'users'    => '<circle cx="9" cy="8" r="3"/><path d="M2 20c0-3.5 3-6 7-6s7 2.5 7 6"/><circle cx="17" cy="9" r="2.3"/><path d="M16 14.2c2.6.3 4.5 2.3 4.5 5.8"/>',
    'building' => '<rect x="4" y="3" width="16" height="18" rx="1.5"/><line x1="8" y1="8" x2="16" y2="8"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="16" x2="12" y2="16"/>',
    'idcard'   => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8" cy="10.5" r="2"/><line x1="13" y1="9.5" x2="18" y2="9.5"/><line x1="13" y1="13" x2="18" y2="13"/>',
    'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="3" x2="8" y2="7"/><line x1="16" y1="3" x2="16" y2="7"/>',
    'chart'    => '<line x1="6" y1="20" x2="6" y2="12"/><line x1="12" y1="20" x2="12" y2="6"/><line x1="18" y1="20" x2="18" y2="10"/>',
    'lock'     => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/>',
    'medal'    => '<circle cx="12" cy="9" r="5"/><path d="M8.5 13.5 7 21l5-3 5 3-1.5-7.5"/>',
    'pencil'   => '<path d="M4 20h4l11-11-4-4L4 16v4z"/>',
    'masuk'    => '<path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>',
];

function svg_ikon($nama, $ikon) {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $ikon[$nama] . '</svg>';
}

// Lencana ikon yang "mengorbit" pada cincin: [radius cincin, sudut derajat, ikon, warna]
$lencana = [
    [430, 212, 'cap',      '#1F5C46'],
    [540, 187, 'book',     '#B8862B'],
    [430, 168, 'users',    '#1F5C46'],
    [540, 148, 'building', '#B8862B'],
    [330, 155, 'idcard',   '#1F5C46'],
    [430, 322, 'calendar', '#B8862B'],
    [540, 352, 'chart',    '#1F5C46'],
    [430,  15, 'lock',     '#B8862B'],
    [540,  38, 'medal',    '#1F5C46'],
    [330,  25, 'pencil',   '#B8862B'],
];
$pusat_x = 600;
$pusat_y = 330;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sistem Informasi Akademik — Universitas Muhammadiyah Bengkulu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root {
    --hijau-tua: #0D3B2E;
    --hijau: #1F5C46;
    --emas: #B8862B;
    --latar: #FAF9F5;
    --tinta: #15211D;
    --tinta-lembut: #5B6560;
    --garis: #E7E4D9;
  }
  * { box-sizing: border-box; margin: 0; }
  html { scroll-behavior: smooth; }
  body {
    font-family: 'IBM Plex Sans', system-ui, sans-serif;
    background: var(--latar);
    color: var(--tinta);
    line-height: 1.55;
    -webkit-font-smoothing: antialiased;
  }
  a { color: inherit; text-decoration: none; }
  :focus-visible { outline: 2px solid var(--emas); outline-offset: 3px; }

  /* ---------- Hero ---------- */
  .lp-hero { position: relative; overflow: hidden; min-height: 780px; padding: 20px 20px 90px; }
  .lp-hero::after {
    content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 180px; z-index: 1;
    background: linear-gradient(to bottom, rgba(250,249,245,0), var(--latar)); pointer-events: none;
  }
  .lp-bg { position: absolute; inset: 0; width: 100%; height: 100%; z-index: 0; }

  /* ---------- Navbar pill ---------- */
  .lp-nav {
    position: relative; z-index: 5; max-width: 1040px; margin: 0 auto;
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
    background: #fff; border: 1px solid var(--garis); border-radius: 16px;
    padding: 10px 12px 10px 16px;
    box-shadow: 0 1px 2px rgba(20,32,28,.04), 0 10px 28px rgba(20,32,28,.06);
  }
  .lp-logo { display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 15px; letter-spacing: -0.01em; }
  .lp-logo-mark { width: 30px; height: 30px; border-radius: 9px; background: var(--hijau-tua); color: #fff; display: grid; place-items: center; }
  .lp-logo-mark svg { width: 17px; height: 17px; }
  .lp-links { display: flex; gap: 30px; font-size: 14px; color: var(--tinta-lembut); }
  .lp-links a:hover { color: var(--tinta); }
  .lp-nav-aksi { display: flex; align-items: center; gap: 8px; }
  .lp-salam { font-size: 13.5px; color: var(--tinta-lembut); margin-right: 6px; }

  .btn {
    display: inline-flex; align-items: center; gap: 8px; border-radius: 10px; padding: 9px 16px;
    font-family: inherit; font-size: 14px; font-weight: 500; border: 1px solid transparent; cursor: pointer;
    transition: background .15s, border-color .15s;
  }
  .btn svg { width: 16px; height: 16px; }
  .btn-gelap { background: var(--hijau-tua); color: #fff; }
  .btn-gelap:hover { background: var(--hijau); }
  .btn-terang { background: #fff; color: var(--tinta); border-color: var(--garis); }
  .btn-terang:hover { border-color: #cdc8b5; }
  .btn-lg { padding: 13px 20px; font-size: 14.5px; }

  /* ---------- Isi hero ---------- */
  .lp-inner {
    position: relative; z-index: 2; max-width: 680px; margin: 0 auto; padding-top: 72px;
    display: flex; flex-direction: column; align-items: center; text-align: center;
  }
  .lp-info {
    display: inline-flex; align-items: center; gap: 14px; background: #fff; border: 1px solid var(--garis);
    border-radius: 999px; padding: 7px 16px; font-size: 13.5px; color: var(--tinta-lembut);
  }
  .lp-info b { color: var(--tinta); font-weight: 600; }
  .lp-info-item { display: flex; align-items: center; gap: 7px; }
  .lp-info-item svg { width: 15px; height: 15px; color: var(--emas); }
  .lp-info-sep { width: 1px; height: 14px; background: var(--garis); }

  h1 {
    margin-top: 26px; font-size: clamp(36px, 5.4vw, 62px); line-height: 1.06;
    letter-spacing: -0.03em; font-weight: 600; text-wrap: balance;
  }
  .lp-sub { margin-top: 18px; max-width: 500px; font-size: 17px; color: var(--tinta-lembut); text-wrap: balance; }
  .lp-aksi { margin-top: 28px; display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; }

  /* ---------- Kartu bertumpuk ---------- */
  .lp-kartu-wrap { position: relative; margin-top: 70px; width: min(440px, 100%); height: 200px; scroll-margin-top: 40px; }
  .lp-kartu-wrap::before {
    content: ""; position: absolute; inset: -30px -80px -10px; z-index: -1; filter: blur(18px);
    background: radial-gradient(closest-side, rgba(31,92,70,.24), rgba(31,92,70,0));
  }
  .lp-kartu {
    position: absolute; left: 0; right: 0; display: flex; align-items: center; gap: 12px; text-align: left;
    background: #fff; border: 1px solid var(--garis); border-radius: 14px; padding: 12px 14px;
    box-shadow: 0 12px 30px rgba(20,32,28,.08);
  }
  .lp-kartu:nth-child(1) { top: 0; z-index: 3; }
  .lp-kartu:nth-child(2) { top: 64px; left: 5%; right: 5%; z-index: 2; }
  .lp-kartu:nth-child(3) {
    top: 128px; left: 10%; right: 10%; z-index: 1; opacity: .9;
    -webkit-mask-image: linear-gradient(to bottom, #000 35%, transparent 100%);
            mask-image: linear-gradient(to bottom, #000 35%, transparent 100%);
  }
  .lp-av {
    position: relative; width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    display: grid; place-items: center; font-size: 13px; font-weight: 600; color: #fff;
  }
  .lp-av::after {
    content: ""; position: absolute; right: -2px; bottom: -2px; width: 12px; height: 12px;
    border-radius: 50%; background: #2f9e6b; border: 2px solid #fff;
  }
  .lp-kartu-judul { font-size: 13.5px; color: var(--tinta-lembut); }
  .lp-kartu-judul b { color: var(--tinta); font-weight: 600; }
  .lp-kartu-sub { font-size: 12px; color: #8a938e; margin-top: 1px; }

  /* ---------- Baris statistik (pengganti "trusted by") ---------- */
  .lp-trust { position: relative; z-index: 2; max-width: 960px; margin: 0 auto; padding: 0 24px 76px; text-align: center; scroll-margin-top: 20px; }
  .lp-trust p { font-size: 13.5px; color: #8a938e; }
  .lp-trust-baris { margin-top: 22px; display: flex; flex-wrap: wrap; justify-content: center; gap: 16px 44px; }
  .lp-trust-item { display: flex; align-items: center; gap: 10px; color: #79827d; font-size: 16px; font-weight: 500; }
  .lp-trust-item svg { width: 20px; height: 20px; color: #9aa39e; }
  .lp-trust-item b { color: var(--tinta); font-weight: 600; }

  .lp-foot { border-top: 1px solid var(--garis); padding: 24px 20px; text-align: center; font-size: 13px; color: var(--tinta-lembut); }

  /* ---------- Gerak halus (dimatikan jika pengguna memilih reduced motion) ---------- */
  @keyframes lp-melayang { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
  @media (prefers-reduced-motion: no-preference) {
    .lp-float { animation: lp-melayang 6s ease-in-out infinite; }
  }

  @media (max-width: 820px) {
    .lp-links, .lp-salam, .lp-badges { display: none; }
    .lp-inner { padding-top: 56px; }
    .lp-kartu-wrap { margin-top: 52px; }
    .lp-info { flex-wrap: wrap; justify-content: center; }
  }
</style>
</head>
<body>

<div class="lp-hero" id="beranda">

  <!-- Latar: cincin konsentris + lencana ikon (satu SVG, jadi selalu sejajar) -->
  <svg class="lp-bg" viewBox="0 0 1200 760" preserveAspectRatio="xMidYMin slice" aria-hidden="true" focusable="false">
    <defs>
      <filter id="lp-bayang" x="-60%" y="-60%" width="220%" height="220%">
        <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#14201C" flood-opacity="0.10"/>
      </filter>
    </defs>
    <g fill="none" stroke="#E7E4D9" stroke-width="1">
      <?php foreach ([230, 330, 430, 540, 660, 790] as $r): ?>
        <circle cx="<?= $pusat_x ?>" cy="<?= $pusat_y ?>" r="<?= $r ?>"/>
      <?php endforeach; ?>
    </g>
    <g fill="none" stroke-width="2" stroke-linecap="round">
      <circle cx="<?= $pusat_x ?>" cy="<?= $pusat_y ?>" r="330" stroke="#1F5C46" stroke-opacity=".55" stroke-dasharray="150 1924" transform="rotate(195 <?= $pusat_x ?> <?= $pusat_y ?>)"/>
      <circle cx="<?= $pusat_x ?>" cy="<?= $pusat_y ?>" r="430" stroke="#B8862B" stroke-opacity=".6"  stroke-dasharray="200 2502" transform="rotate(120 <?= $pusat_x ?> <?= $pusat_y ?>)"/>
      <circle cx="<?= $pusat_x ?>" cy="<?= $pusat_y ?>" r="540" stroke="#1F5C46" stroke-opacity=".55" stroke-dasharray="260 3133" transform="rotate(-25 <?= $pusat_x ?> <?= $pusat_y ?>)"/>
    </g>
    <g class="lp-badges">
      <?php foreach ($lencana as $i => [$radius, $sudut, $nama, $warna]):
          $x = $pusat_x + $radius * cos(deg2rad($sudut));
          $y = $pusat_y + $radius * sin(deg2rad($sudut));
      ?>
        <g transform="translate(<?= round($x, 1) ?> <?= round($y, 1) ?>)">
          <g class="lp-float" style="animation-delay:-<?= $i * 0.7 ?>s">
            <circle r="21" fill="#fff" stroke="#ECE9DE" filter="url(#lp-bayang)"/>
            <g transform="translate(-11 -11) scale(.92)" fill="none" stroke="<?= $warna ?>" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
              <?= $ikon[$nama] ?>
            </g>
          </g>
        </g>
      <?php endforeach; ?>
    </g>
  </svg>

  <!-- Navbar pill -->
  <header class="lp-nav">
    <a href="/index.php" class="lp-logo">
      <span class="lp-logo-mark"><?= svg_ikon('cap', $ikon) ?></span>
      SIA UM Bengkulu
    </a>
    <nav class="lp-links" aria-label="Navigasi halaman">
      <a href="#beranda">Beranda</a>
    </nav>
    <div class="lp-nav-aksi">
      <?php if ($sudah_login): ?>
        <span class="lp-salam">Halo, <?= teks($_SESSION['nama_tampil']) ?></span>
        <a href="/logout.php" class="btn btn-terang">Keluar</a>
        <a href="<?= htmlspecialchars($tautan_dashboard, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-gelap">Dasbor saya</a>
      <?php else: ?>
        <a href="/login.php" class="btn btn-gelap">Masuk</a>
      <?php endif; ?>
    </div>
  </header>

  <!-- Isi hero -->
  <main class="lp-inner">
    <div class="lp-info">
      <span class="lp-info-item"><?= svg_ikon('users', $ikon) ?><b><?= (int) $total_mahasiswa ?></b> mahasiswa terdaftar</span>
      <span class="lp-info-sep"></span>
      <span class="lp-info-item"><?= svg_ikon('cap', $ikon) ?><b><?= (int) $total_prodi ?></b> program studi</span>
    </div>

    <h1>Sistem Informasi Akademik Terintegrasi</h1>
    <p class="lp-sub">Kelola seluruh kebutuhan akademik Anda dengan mudah, cepat, dan terintegrasi dalam satu platform.</p>

    <div class="lp-aksi">
      <?php if ($sudah_login): ?>
        <a href="<?= htmlspecialchars($tautan_dashboard, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-gelap btn-lg">Buka dasbor</a>
      <?php else: ?>
        <a href="/login.php" class="btn btn-gelap btn-lg"><?= svg_ikon('masuk', $ikon) ?>Masuk ke akun Anda</a>
      <?php endif; ?>
    </div>
  </main>
</div>

</body>
</html>