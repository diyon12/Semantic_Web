-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql101.infinityfree.com
-- Generation Time: Sep 28, 2026 at 09:28 AM
-- Server version: 11.4.13-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_42928299_sql_websemantik`
--

-- --------------------------------------------------------

--
-- Table structure for table `fakultas`
--

CREATE TABLE `fakultas` (
  `id` int(11) NOT NULL,
  `kode_fakultas` varchar(10) NOT NULL,
  `nama_fakultas` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `fakultas`
--

INSERT INTO `fakultas` (`id`, `kode_fakultas`, `nama_fakultas`, `created_at`, `updated_at`) VALUES
(1, '55201', 'Teknik', '2026-09-26 14:01:55', '2026-09-28 04:39:58'),
(2, '57201', 'Sistem Informasi', '2026-09-28 04:39:33', '2026-09-28 04:39:33');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `npm` varchar(15) NOT NULL,
  `user_id` int(11) NOT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `id_prodi` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`npm`, `user_id`, `jenis_kelamin`, `tempat_lahir`, `tgl_lahir`, `alamat`, `id_prodi`, `created_at`, `updated_at`) VALUES
('2023110001', 1, 'laki-laki', 'Bengkulu', '2004-05-12', 'Jl. Merdeka No. 10, Bengkulu', 1, '2026-09-26 14:01:56', '2026-09-26 14:01:56'),
('2023110002', 2, 'perempuan', 'Curup 1', '2004-11-06', 'Jl. Sudirman No. 5, Bengkulus', 1, '2026-09-26 14:01:56', '2026-09-27 13:41:28');

-- --------------------------------------------------------

--
-- Table structure for table `program_studi`
--

CREATE TABLE `program_studi` (
  `id` int(11) NOT NULL,
  `kode_prodi` char(5) NOT NULL,
  `programstudi` varchar(100) NOT NULL,
  `id_fakultas` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `program_studi`
--

INSERT INTO `program_studi` (`id`, `kode_prodi`, `programstudi`, `id_fakultas`, `created_at`, `updated_at`) VALUES
(1, '55201', 'Teknik Informatika', 1, '2026-09-26 14:01:56', '2026-09-26 14:01:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','prodi','admin') NOT NULL,
  `id_prodi` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `id_prodi`, `created_at`, `updated_at`) VALUES
(1, 'Andi Saputra', 'andi@ui.ac.id', '$2a$12$2wyOh1cH9kALCXtd5en2j.QYgEzSK4w/71WgnGd62KarVm3HPVN/C', 'mahasiswa', NULL, '2026-09-26 14:01:56', '2026-09-28 13:12:48'),
(2, 'Siti Rahma', 'siti@ui.ac.id', '$2a$12$2wyOh1cH9kALCXtd5en2j.QYgEzSK4w/71WgnGd62KarVm3HPVN/C', 'mahasiswa', NULL, '2026-09-26 14:01:56', '2026-09-28 13:13:04'),
(3, 'Admin Prodi TI', 'prodi.ti@ui.ac.id', '$2a$12$2wyOh1cH9kALCXtd5en2j.QYgEzSK4w/71WgnGd62KarVm3HPVN/C', 'prodi', 1, '2026-09-26 14:01:56', '2026-09-28 13:13:00'),
(4, 'Administrator', 'admin@ui.ac.id', '$2a$12$2wyOh1cH9kALCXtd5en2j.QYgEzSK4w/71WgnGd62KarVm3HPVN/C', 'admin', NULL, '2026-09-26 14:01:56', '2026-09-28 13:12:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fakultas`
--
ALTER TABLE `fakultas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_fakultas_unik` (`kode_fakultas`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`npm`),
  ADD UNIQUE KEY `user_id_unik` (`user_id`),
  ADD KEY `fk_mahasiswa_prodi` (`id_prodi`);

--
-- Indexes for table `program_studi`
--
ALTER TABLE `program_studi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_prodi_unik` (`kode_prodi`),
  ADD KEY `fk_prodi_fakultas` (`id_fakultas`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unik` (`email`),
  ADD KEY `fk_users_prodi` (`id_prodi`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fakultas`
--
ALTER TABLE `fakultas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `program_studi`
--
ALTER TABLE `program_studi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
