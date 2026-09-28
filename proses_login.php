<?php
/* ================================================================
   Logika Proses Login — tabel users, 3 role
   ================================================================ */

session_start();
include __DIR__ . '/config/connection.php'; // ganti ke 'koneksi_lokal.php' saat menguji di komputer sendiri

function tolak($pesan) {
    $_SESSION['login_error'] = $pesan;
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    tolak('Email dan kata sandi wajib diisi.');
}

$stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user || !password_verify($password, $user['password'])) {
    tolak('Email atau kata sandi belum cocok. Periksa kembali dan coba lagi.');
}

// Login berhasil — regenerasi session ID mencegah session fixation
session_regenerate_id(true);
$_SESSION['user_id']     = $user['id'];
$_SESSION['nama_tampil'] = $user['name'];
$_SESSION['role']        = $user['role'];

switch ($user['role']) {
    case 'mahasiswa':
        $s = mysqli_prepare($koneksi, "SELECT npm FROM mahasiswa WHERE user_id = ?");
        mysqli_stmt_bind_param($s, 'i', $user['id']);
        mysqli_stmt_execute($s);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        if (!$row) {
            tolak('Akun ini belum punya data mahasiswa terkait. Hubungi admin.');
        }
        $_SESSION['npm'] = $row['npm'];
        header('Location: /student/dashboard.php');
        break;

    case 'prodi':
        if (empty($user['id_prodi'])) {
            tolak('Akun ini belum dikaitkan ke program studi manapun. Hubungi admin.');
        }
        $_SESSION['id_prodi'] = $user['id_prodi'];
        header('Location: /studyprogram/dashboard.php');
        break;

    case 'admin':
        header('Location: /administrator/dashboard.php');
        break;
}
exit;