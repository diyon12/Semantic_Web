<?php
/* ================================================================
   Helper bersama — dipakai di halaman yang butuh login
   ================================================================ */

function wajib_login(array $peran_diizinkan = []) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['role'])) {
        header('Location: /login.php');
        exit;
    }
    if (!empty($peran_diizinkan) && !in_array($_SESSION['role'], $peran_diizinkan, true)) {
        // Login sah, tapi role-nya tidak berhak atas halaman ini
        header('Location: /index.php');
        exit;
    }
}

/* ---------- Perlindungan CSRF untuk form yang mengubah/menghapus data ---------- */

/* Membuat string acak untuk token.
   Sebagian hosting gratis menonaktifkan random_bytes() atau membuatnya
   gagal ("Could not gather sufficient random data"). Kalau tidak
   ditangani, halaman berhenti di tengah jalan tanpa pesan apa pun
   (form jadi kosong). Karena itu disediakan dua cadangan. */
function acak_hex($jumlah_byte = 32) {
    if (function_exists('random_bytes')) {
        try {
            return bin2hex(random_bytes($jumlah_byte));
        } catch (Throwable $e) {
            // lanjut ke cadangan
        }
    }
    if (function_exists('openssl_random_pseudo_bytes')) {
        $byte = @openssl_random_pseudo_bytes($jumlah_byte);
        if ($byte !== false && strlen($byte) === $jumlah_byte) {
            return bin2hex($byte);
        }
    }
    return hash('sha256', uniqid('', true) . mt_rand() . microtime() . session_id());
}

function csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = acak_hex(32);
    }
    return $_SESSION['csrf'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

/* Panggil di awal file pemroses form (POST). Kalau token tidak cocok,
   permintaan ditolak dan pengguna dikembalikan ke $halaman_balik. */
function csrf_periksa($halaman_balik) {
    $sah = isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
    if (!$sah) {
        $_SESSION['pesan_galat'] = 'Formulir sudah kedaluwarsa atau tidak valid. Muat ulang halaman lalu coba lagi.';
        header('Location: ' . $halaman_balik);
        exit;
    }
}

/* Panjang teks dalam karakter (bukan byte), tetap aman kalau mbstring tidak aktif */
function panjang_teks($teks) {
    return function_exists('mb_strlen') ? mb_strlen($teks, 'UTF-8') : strlen($teks);
}