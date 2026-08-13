-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Agu 2026 pada 08.50
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

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
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama`, `created_at`) VALUES
(1, 'admin cantik', '$2y$12$Kb8GJTbY2iZVLE3vbvYWE.VdoQ0SwlEHE.rukBaSMi37ekcCOO6IO', 'tyaa cantik', '2026-08-11 05:54:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `konten_sosmed`
--

CREATE TABLE `konten_sosmed` (
  `id` int(11) NOT NULL,
  `platform` enum('INSTAGRAM','FACEBOOK','TIKTOK','YOUTUBE') NOT NULL,
  `tahun` year(4) NOT NULL,
  `bulan` tinyint(4) NOT NULL,
  `jumlah_konten` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `konten_sosmed`
--

INSERT INTO `konten_sosmed` (`id`, `platform`, `tahun`, `bulan`, `jumlah_konten`, `created_at`, `updated_at`) VALUES
(193, 'INSTAGRAM', '2026', 1, 94, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(194, 'YOUTUBE', '2026', 1, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(195, 'FACEBOOK', '2026', 1, 74, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(196, 'TIKTOK', '2026', 1, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(197, 'INSTAGRAM', '2026', 2, 94, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(198, 'YOUTUBE', '2026', 2, 2, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(199, 'FACEBOOK', '2026', 2, 52, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(200, 'TIKTOK', '2026', 2, 12, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(201, 'INSTAGRAM', '2026', 3, 313, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(202, 'YOUTUBE', '2026', 3, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(203, 'FACEBOOK', '2026', 3, 77, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(204, 'TIKTOK', '2026', 3, 3, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(205, 'INSTAGRAM', '2026', 4, 113, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(206, 'YOUTUBE', '2026', 4, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(207, 'FACEBOOK', '2026', 4, 50, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(208, 'TIKTOK', '2026', 4, 3, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(209, 'INSTAGRAM', '2026', 5, 71, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(210, 'YOUTUBE', '2026', 5, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(211, 'FACEBOOK', '2026', 5, 31, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(212, 'TIKTOK', '2026', 5, 12, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(213, 'INSTAGRAM', '2026', 6, 107, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(214, 'YOUTUBE', '2026', 6, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(215, 'FACEBOOK', '2026', 6, 5, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(216, 'TIKTOK', '2026', 6, 4, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(217, 'INSTAGRAM', '2026', 7, 117, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(218, 'YOUTUBE', '2026', 7, 1, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(219, 'FACEBOOK', '2026', 7, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(220, 'TIKTOK', '2026', 7, 2, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(221, 'INSTAGRAM', '2026', 8, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(222, 'YOUTUBE', '2026', 8, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(223, 'FACEBOOK', '2026', 8, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(224, 'TIKTOK', '2026', 8, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(225, 'INSTAGRAM', '2026', 9, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(226, 'YOUTUBE', '2026', 9, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(227, 'FACEBOOK', '2026', 9, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(228, 'TIKTOK', '2026', 9, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(229, 'INSTAGRAM', '2026', 10, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(230, 'YOUTUBE', '2026', 10, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(231, 'FACEBOOK', '2026', 10, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(232, 'TIKTOK', '2026', 10, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(233, 'INSTAGRAM', '2026', 11, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(234, 'YOUTUBE', '2026', 11, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(235, 'FACEBOOK', '2026', 11, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(236, 'TIKTOK', '2026', 11, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(237, 'INSTAGRAM', '2026', 12, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(238, 'YOUTUBE', '2026', 12, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(239, 'FACEBOOK', '2026', 12, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36'),
(240, 'TIKTOK', '2026', 12, 0, '2026-08-12 06:40:36', '2026-08-12 06:40:36');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `konten_sosmed`
--
ALTER TABLE `konten_sosmed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_platform_tahun_bulan` (`platform`,`tahun`,`bulan`),
  ADD KEY `idx_platform_tahun` (`platform`,`tahun`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `konten_sosmed`
--
ALTER TABLE `konten_sosmed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
