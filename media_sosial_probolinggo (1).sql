-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2026 at 04:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `media_sosial_probolinggo`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama`, `created_at`) VALUES
(1, 'Admin Cantik', '$2y$12$Kb8GJTbY2iZVLE3vbvYWE.VdoQ0SwlEHE.rukBaSMi37ekcCOO6IO', 'Admin', '2026-08-11 05:54:14');

-- --------------------------------------------------------

--
-- Table structure for table `akun_sosmed`
--

CREATE TABLE `akun_sosmed` (
  `id` int(11) NOT NULL,
  `platform` varchar(20) NOT NULL,
  `nama_akun` varchar(100) NOT NULL,
  `jumlah_follower` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `akun_sosmed`
--

INSERT INTO `akun_sosmed` (`id`, `platform`, `nama_akun`, `jumlah_follower`, `created_at`, `updated_at`) VALUES
(1, 'INSTAGRAM', '@endlessprobolinggo', 18, '2026-08-14 02:58:12', '2026-08-14 02:58:12'),
(2, 'INSTAGRAM', '@pemkab_probolinggo', 7, '2026-08-14 02:58:12', '2026-08-14 02:58:12'),
(3, 'INSTAGRAM', '@diskominfokabprobolinggo', 1, '2026-08-14 02:58:12', '2026-08-14 02:58:12'),
(4, 'INSTAGRAM', '@radiobromo.fm', 671, '2026-08-14 02:58:12', '2026-08-14 02:58:12'),
(5, 'INSTAGRAM', '@halo.sae', 268, '2026-08-14 02:58:12', '2026-08-14 02:58:12'),
(6, 'FACEBOOK', 'Infokabprobolinggo', 28, '2026-08-14 02:58:12', '2026-08-14 02:58:12'),
(7, 'TIKTOK', '@pemkabprobolinggo', 1, '2026-08-14 02:58:12', '2026-08-14 02:58:12'),
(8, 'TIKTOK', '@radiobromofm923', 1, '2026-08-14 02:58:12', '2026-08-14 02:58:12'),
(9, 'TIKTOK', '@halo.sae', 2, '2026-08-14 02:58:12', '2026-08-14 02:58:12'),
(10, 'YOUTUBE', 'Info Kabupaten Probolinggo', 9, '2026-08-14 02:58:12', '2026-08-14 02:58:12');

-- --------------------------------------------------------

--
-- Table structure for table `follower_sosmed`
--

CREATE TABLE `follower_sosmed` (
  `id` int(11) NOT NULL,
  `id_akun` int(11) NOT NULL,
  `tahun` year(4) NOT NULL,
  `bulan` tinyint(4) NOT NULL,
  `jumlah_follower` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `follower_sosmed`
--

INSERT INTO `follower_sosmed` (`id`, `id_akun`, `tahun`, `bulan`, `jumlah_follower`, `created_at`, `updated_at`) VALUES
(59, 1, '2026', 3, 18200, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(60, 2, '2026', 3, 6143, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(61, 3, '2026', 3, 1196, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(62, 4, '2026', 3, 650, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(63, 5, '2026', 3, 147, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(64, 10, '2026', 3, 8320, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(65, 6, '2026', 3, 28000, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(66, 7, '2026', 3, 1146, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(67, 8, '2026', 3, 675, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(68, 9, '2026', 3, 288, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(69, 1, '2026', 7, 18200, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(70, 2, '2026', 7, 6347, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(71, 3, '2026', 7, 1258, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(72, 4, '2026', 7, 660, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(73, 5, '2026', 7, 264, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(74, 10, '2026', 7, 8500, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(75, 6, '2026', 7, 28000, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(76, 7, '2026', 7, 1158, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(77, 8, '2026', 7, 1126, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(78, 9, '2026', 7, 1385, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(79, 1, '2026', 8, 18000, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(80, 2, '2026', 8, 6887, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(81, 3, '2026', 8, 1324, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(82, 4, '2026', 8, 671, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(83, 5, '2026', 8, 268, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(84, 10, '2026', 8, 8500, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(86, 7, '2026', 8, 1169, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(87, 8, '2026', 8, 1138, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(88, 9, '2026', 8, 1645, '2026-08-18 01:36:55', '2026-08-18 01:36:55'),
(89, 6, '2026', 8, 28000, '2026-08-23 12:40:09', '2026-08-23 12:40:09');

-- --------------------------------------------------------

--
-- Table structure for table `konten_sosmed`
--

CREATE TABLE `konten_sosmed` (
  `id` int(11) NOT NULL,
  `akun_id` int(11) DEFAULT NULL,
  `platform` enum('INSTAGRAM','FACEBOOK','TIKTOK','YOUTUBE') NOT NULL,
  `tahun` year(4) NOT NULL,
  `bulan` tinyint(4) NOT NULL,
  `jumlah_konten` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `konten_sosmed`
--

INSERT INTO `konten_sosmed` (`id`, `akun_id`, `platform`, `tahun`, `bulan`, `jumlah_konten`, `created_at`, `updated_at`) VALUES
(471, 1, 'INSTAGRAM', '2026', 1, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(472, 2, 'INSTAGRAM', '2026', 1, 74, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(473, 3, 'INSTAGRAM', '2026', 1, 15, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(474, 4, 'INSTAGRAM', '2026', 1, 5, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(475, 10, 'YOUTUBE', '2026', 1, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(476, 6, 'FACEBOOK', '2026', 1, 74, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(477, 7, 'TIKTOK', '2026', 1, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(478, 1, 'INSTAGRAM', '2026', 2, 1, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(479, 2, 'INSTAGRAM', '2026', 2, 52, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(480, 3, 'INSTAGRAM', '2026', 2, 9, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(481, 4, 'INSTAGRAM', '2026', 2, 32, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(482, 10, 'YOUTUBE', '2026', 2, 2, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(483, 6, 'FACEBOOK', '2026', 2, 52, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(484, 7, 'TIKTOK', '2026', 2, 2, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(485, 8, 'TIKTOK', '2026', 2, 10, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(486, 1, 'INSTAGRAM', '2026', 3, 14, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(487, 2, 'INSTAGRAM', '2026', 3, 77, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(488, 3, 'INSTAGRAM', '2026', 3, 31, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(489, 4, 'INSTAGRAM', '2026', 3, 44, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(490, 5, 'INSTAGRAM', '2026', 3, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(491, 10, 'YOUTUBE', '2026', 3, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(492, 6, 'FACEBOOK', '2026', 3, 77, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(493, 7, 'TIKTOK', '2026', 3, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(494, 8, 'TIKTOK', '2026', 3, 3, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(495, 9, 'TIKTOK', '2026', 3, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(496, 1, 'INSTAGRAM', '2026', 4, 41, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(497, 2, 'INSTAGRAM', '2026', 4, 126, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(498, 3, 'INSTAGRAM', '2026', 4, 45, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(499, 4, 'INSTAGRAM', '2026', 4, 3, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(500, 5, 'INSTAGRAM', '2026', 4, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(501, 10, 'YOUTUBE', '2026', 4, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(502, 6, 'FACEBOOK', '2026', 4, 50, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(503, 7, 'TIKTOK', '2026', 4, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(504, 8, 'TIKTOK', '2026', 4, 3, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(505, 9, 'TIKTOK', '2026', 4, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(506, 1, 'INSTAGRAM', '2026', 5, 30, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(507, 2, 'INSTAGRAM', '2026', 5, 56, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(508, 3, 'INSTAGRAM', '2026', 5, 16, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(509, 4, 'INSTAGRAM', '2026', 5, 6, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(510, 5, 'INSTAGRAM', '2026', 5, 11, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(511, 10, 'YOUTUBE', '2026', 5, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(512, 6, 'FACEBOOK', '2026', 5, 31, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(513, 7, 'TIKTOK', '2026', 5, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(514, 8, 'TIKTOK', '2026', 5, 5, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(515, 9, 'TIKTOK', '2026', 5, 7, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(516, 1, 'INSTAGRAM', '2026', 6, 22, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(517, 2, 'INSTAGRAM', '2026', 6, 97, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(518, 3, 'INSTAGRAM', '2026', 6, 10, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(519, 4, 'INSTAGRAM', '2026', 6, 4, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(520, 5, 'INSTAGRAM', '2026', 6, 4, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(521, 10, 'YOUTUBE', '2026', 6, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(522, 6, 'FACEBOOK', '2026', 6, 5, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(523, 7, 'TIKTOK', '2026', 6, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(524, 8, 'TIKTOK', '2026', 6, 2, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(525, 9, 'TIKTOK', '2026', 6, 2, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(526, 1, 'INSTAGRAM', '2026', 7, 27, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(527, 2, 'INSTAGRAM', '2026', 7, 114, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(528, 3, 'INSTAGRAM', '2026', 7, 14, '2026-08-18 02:02:21', '2026-08-23 10:11:16'),
(529, 4, 'INSTAGRAM', '2026', 7, 8, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(530, 5, 'INSTAGRAM', '2026', 7, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(531, 10, 'YOUTUBE', '2026', 7, 1, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(532, 6, 'FACEBOOK', '2026', 7, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(533, 7, 'TIKTOK', '2026', 7, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(534, 8, 'TIKTOK', '2026', 7, 2, '2026-08-18 02:02:21', '2026-08-18 02:02:21'),
(535, 9, 'TIKTOK', '2026', 7, 0, '2026-08-18 02:02:21', '2026-08-18 02:02:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `akun_sosmed`
--
ALTER TABLE `akun_sosmed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `follower_sosmed`
--
ALTER TABLE `follower_sosmed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unik_akun_bulan_tahun` (`id_akun`,`tahun`,`bulan`);

--
-- Indexes for table `konten_sosmed`
--
ALTER TABLE `konten_sosmed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_akun_platform_tahun_bulan` (`akun_id`,`platform`,`tahun`,`bulan`),
  ADD KEY `idx_platform_tahun` (`platform`,`tahun`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `akun_sosmed`
--
ALTER TABLE `akun_sosmed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `follower_sosmed`
--
ALTER TABLE `follower_sosmed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `konten_sosmed`
--
ALTER TABLE `konten_sosmed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=550;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `follower_sosmed`
--
ALTER TABLE `follower_sosmed`
  ADD CONSTRAINT `fk_follower_sosmed` FOREIGN KEY (`id_akun`) REFERENCES `akun_sosmed` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `konten_sosmed`
--
ALTER TABLE `konten_sosmed`
  ADD CONSTRAINT `fk_konten_akun` FOREIGN KEY (`akun_id`) REFERENCES `akun_sosmed` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
