-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2025 at 01:25 PM
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
  `gambar_utama` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id`, `judul`, `isi`, `penulis`, `tanggal_post`, `gambar_utama`) VALUES
(16, 'jk', 'buijo', 'buiop', '2025-07-31 17:03:11', '688b85aff3a5c.png'),
(17, 'uuhio', 'joknokp', 'uhijo', '2025-07-31 17:17:20', '688b8900d6135.png'),
(18, 'knk', 'njkl', 'k', '2025-07-31 17:17:34', '688b890e18709.jpg'),
(19, '[po', 'hbjk', 'hjk', '2025-07-31 17:17:44', '688b8918986c5.png'),
(20, 'ojh', 'hbjnkml', 'bjk', '2025-07-31 17:17:59', '688b892783a20.png'),
(21, 'poiu', 'hbjkop', 'hvhbjk', '2025-07-31 17:18:11', '688b8933675c7.png'),
(22, 'jhghjk', 'awawr', 'oiugghjk', '2025-07-31 17:18:24', '688b894047003.png'),
(23, 'awda', 'awa asdawd asda wd sa wd sa wd sa fa f se f sdg s g rh r e rs efsef sefsdf sesegs egseef efegr gfddhd drts eferw rqwrqf dfsdgs rgfh dfhdfg sefs dfefaeaaw dsdawd awdasdawdsa awdasd awdsa wdsaa wdawd asda wd', 'awd', '2025-08-01 13:12:05', 'berita_688ca10523e41.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `tanggal_post` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `galeri`
--

INSERT INTO `galeri` (`id`, `nama`, `deskripsi`, `file_path`, `tanggal_post`) VALUES
(5, 'uijo', 'bi', 'galeri_688b85cc761c2.png', '2025-07-31 17:03:40'),
(6, 'jnkmlmkj', 'sdw', 'galeri_688b895758c61.jpg', '2025-07-31 17:18:47'),
(7, 'nvud', 'dj', 'galeri_688b895fd739e.jpg', '2025-07-31 17:18:55'),
(8, 'acno', 'nac', 'galeri_688b89690811c.jpg', '2025-07-31 17:19:05'),
(9, 'oacn', 'noac', 'galeri_688b89731abf6.jpg', '2025-07-31 17:19:15'),
(10, 'po', 'fie', 'galeri_688b89802cd4c.jpg', '2025-07-31 17:19:28'),
(11, 'lkn', 'uir', 'galeri_688b8989784f0.jpg', '2025-07-31 17:19:37'),
(16, 'awdwd', 'awdad', 'galeri_688ca10f8c054.jpg', '2025-08-01 13:12:15');

-- --------------------------------------------------------

--
-- Table structure for table `gambar_beranda`
--

CREATE TABLE `gambar_beranda` (
  `id` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gambar_beranda`
--

INSERT INTO `gambar_beranda` (`id`, `nama_file`) VALUES
(3, '688ca35470a3e.jpg');

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
(1, 'Siswa Aktif', '35'),
(2, 'Akreditasi', 'A'),
(3, 'Jumlah Kelas', '12'),
(4, 'Guru & Staff', '25'),
(5, 'Alumni', '500'),
(6, 'Mata Pelajaran', '15');

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `urutan` int(11) DEFAULT 0,
  `pengalaman_kerja` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pegawai`
--

INSERT INTO `pegawai` (`id`, `nama`, `jabatan`, `foto`, `urutan`, `pengalaman_kerja`) VALUES
(9, 'ghj', 'hjk', '6888afb6026a1.png', 1, 'hjk'),
(10, 'kmlcs', 'kmcs', '688b7f84133ee.png', 1, 'nkcm'),
(11, 'awd', 'awd', '688ca0c53f1fe.jpg', 1, 'awd');

-- --------------------------------------------------------

--
-- Table structure for table `prestasi`
--

CREATE TABLE `prestasi` (
  `id` int(11) NOT NULL,
  `nama_prestasi` varchar(255) NOT NULL,
  `tingkat` varchar(100) NOT NULL,
  `penyelenggara` varchar(255) DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tanggal_post` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prestasi`
--

INSERT INTO `prestasi` (`id`, `nama_prestasi`, `tingkat`, `penyelenggara`, `tahun`, `deskripsi`, `gambar`, `tanggal_post`) VALUES
(14, 'jn', 'Sekolah', 'bj', '2025', 'ooi', '688b85c22be37.png', '2025-07-31 10:03:30'),
(15, 'awdsda', 'Sekolah', 'qweqwe', '0000', 'qweqwe', '688ca0b13d1d2.jpg', '2025-08-01 06:10:41'),
(16, 'sad', 'Kecamatan', 'qewe', '0000', 'qwe', '688cb1ac710b2.png', '2025-08-01 07:23:08'),
(17, 'sf', 'Sekolah', 'sdf', '0000', 'sdf', '688cb1bdb0d74.png', '2025-08-01 07:23:25'),
(18, 'poipi', 'Kecamatan', 'hkjhkj', '0000', 'nmnb', '688cb1d6bd5d1.png', '2025-08-01 07:23:50'),
(19, 'cvbc', 'Nasional', 'cvb', '0000', 'cvb', '688cb1e833a4e.png', '2025-08-01 07:24:08'),
(20, 'hjgj', 'Kabupaten', 'mnb', '0000', 'lkj', '688cb1fcc70b3.png', '2025-08-01 07:24:28');

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
(1, 'visi', 'Ini visi sekolah'),
(2, 'misi', 'ini misi sekolah'),
(3, 'sejarah', 'ini sejarah sekolah'),
(4, 'sambutan_kepala', 'https://www.youtube.com/watch?v=pp4YQPykBMM&list=RDpp4YQPykBMM&start_radio=1&ab_channel=IlleniumVEVO'),
(5, 'tag_line', 'ini tag line sekolah');

-- --------------------------------------------------------

--
-- Table structure for table `profil_pemimpin`
--

CREATE TABLE `profil_pemimpin` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `slogan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profil_pemimpin`
--

INSERT INTO `profil_pemimpin` (`id`, `nama`, `jabatan`, `foto`, `slogan`) VALUES
(3, 'mencoba', 'coba lagi', '6888af9a637f9.png', 'mantap jiwa');

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
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gambar_beranda`
--
ALTER TABLE `gambar_beranda`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `info_statistik`
--
ALTER TABLE `info_statistik`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prestasi`
--
ALTER TABLE `prestasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profil_pemimpin`
--
ALTER TABLE `profil_pemimpin`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `gambar_beranda`
--
ALTER TABLE `gambar_beranda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `info_statistik`
--
ALTER TABLE `info_statistik`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `prestasi`
--
ALTER TABLE `prestasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `profil`
--
ALTER TABLE `profil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `profil_pemimpin`
--
ALTER TABLE `profil_pemimpin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
