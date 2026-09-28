<?php
/* ================================================================
   Halaman Login — 3 role: mahasiswa, prodi, admin
   Login memakai email (mengikuti tabel users yang baru)
   ================================================================ */

session_start();

if (isset($_SESSION['role'])) {
    $tujuan = ['mahasiswa' => '/student/dashboard.php', 'prodi' => '/studyprogram/dashboard.php', 'admin' => '/administrator/dashboard.php'];
    header('Location: ' . $tujuan[$_SESSION['role']]);
    exit;
}

$pesan_galat = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk — Sistem Informasi Data Mahasiswa</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,900&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>


<section class="login-area">
  <div class="wrap-sempit">
    <div class="kartu-masuk">
      <span class="sudut-kanan-atas"></span>
      <span class="sudut-kanan-bawah"></span>

      <h1 style="text-align: center;">Login Siakad</h1>
      <p class="sub">Silakan masuk menggunakan akun Anda untuk mengakses berbagai layanan dan informasi akademik yang tersedia di Sistem Informasi Akademik (SIAKAD).</p>

      <?php if ($pesan_galat): ?>
        <div class="pesan-galat"><?= htmlspecialchars($pesan_galat, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <form action="proses_login.php" method="post" novalidate>
        <div class="kolom">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" autofocus required>
        </div>
        <div class="kolom">
          <label for="password">Kata sandi</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="tombol">Masuk</button>
      </form>
      <a href="index.php" class="tombol-sekunder tombol-sekunder-lebar">Kembali ke beranda</a>
    </div>
  </div>
</section>


</body>
</html>