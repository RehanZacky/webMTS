-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2025 at 03:00 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_mts`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` int(11) NOT NULL,
  `judul` varchar(150) DEFAULT NULL,
  `isi` text DEFAULT NULL,
  `penulis` varchar(100) DEFAULT NULL,
  `tanggal_post` datetime DEFAULT current_timestamp(),
  `gambar_utama` varchar(255) DEFAULT NULL,
  `video_youtube` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id`, `judul`, `isi`, `penulis`, `tanggal_post`, `gambar_utama`, `video_youtube`) VALUES
(1, 'test', 'test', 'test', '2025-07-08 00:00:00', 'berita_1751981795.jpg', 'https://www.youtube.com/watch?v=XpmeVNxZ-Ks&list=RDpp4YQPykBMM&index=11&ab_channel=IlleniumVEVO'),
(2, 'tes', 'jadi gini', 'saya', '2025-07-17 00:00:00', 'berita_1752753200.jpeg', '');

-- --------------------------------------------------------

--
-- Table structure for table `gambar`
--

CREATE TABLE `gambar` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `kategori` enum('logo','banner','lainnya') DEFAULT 'lainnya',
  `tanggal_upload` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `info_statistik`
--

CREATE TABLE `info_statistik` (
  `id` int(11) NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `nilai` varchar(100) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `info_statistik`
--

INSERT INTO `info_statistik` (`id`, `label`, `nilai`) VALUES
(1, 'Siswa Aktif', '350'),
(2, 'Akreditasi', 'A'),
(3, 'Jumlah Kelas', '12'),
(4, 'Guru & Staff', '25'),
(5, 'Alumni', '500'),
(6, 'Mata Pelajaran', '15');

-- --------------------------------------------------------

--
-- Table structure for table `profil`
--

CREATE TABLE `profil` (
  `id` int(11) NOT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `isi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profil`
--

INSERT INTO `profil` (`id`, `jenis`, `isi`) VALUES
(4, 'sambutan_kepala', 'https://www.youtube.com/watch?v=pp4YQPykBMM&list=RDpp4YQPykBMM&start_radio=1&ab_channel=IlleniumVEVO');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','guru') NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `nama_lengkap`) VALUES
(1, 'secure_access_1', '$2y$10$YcwJJr0o10wD.eFXKqTCyOxbtgi/vjxxf0mJr8MVDdn4zCqSj.58K', 'admin', 'SuperAdmin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gambar`
--
ALTER TABLE `gambar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `info_statistik`
--
ALTER TABLE `info_statistik`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gambar`
--
ALTER TABLE `gambar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `info_statistik`
--
ALTER TABLE `info_statistik`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `profil`
--
ALTER TABLE `profil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
