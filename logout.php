<?php
/* ================================================================
   Logout — menghapus sesi login mahasiswa
   ================================================================ */

session_start();
$_SESSION = [];
session_destroy();

header('Location: /login.php');
exit;