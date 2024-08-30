-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2024 at 05:08 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `adsi_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`) VALUES
(1, 'admin1');

-- --------------------------------------------------------

--
-- Table structure for table `denda_pelanggaran`
--

CREATE TABLE `denda_pelanggaran` (
  `id` int(11) NOT NULL,
  `total_denda` decimal(10,2) NOT NULL,
  `keterangan` text NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `id_penghuni` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `denda_pelanggaran`
--

INSERT INTO `denda_pelanggaran` (`id`, `total_denda`, `keterangan`, `id_admin`, `id_penghuni`) VALUES
(20, '10000.00', 'merusak jendela kamar', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `detail_kamar`
--

CREATE TABLE `detail_kamar` (
  `id` int(11) NOT NULL,
  `id_penghuni` int(11) DEFAULT NULL,
  `nomor_kamar` int(11) DEFAULT NULL,
  `durasi_kamar` int(11) NOT NULL,
  `tanggal_mulai_sewa` date NOT NULL,
  `tanggal_selesai_sewa` date NOT NULL,
  `total_harga` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_kamar`
--

INSERT INTO `detail_kamar` (`id`, `id_penghuni`, `nomor_kamar`, `durasi_kamar`, `tanggal_mulai_sewa`, `tanggal_selesai_sewa`, `total_harga`) VALUES
(5, 4, 1, 2, '2024-06-05', '2024-08-05', '2000000.00'),
(6, 1, 2, 2, '2024-06-14', '2024-08-14', '2000000.00');

-- --------------------------------------------------------

--
-- Table structure for table `kamar`
--

CREATE TABLE `kamar` (
  `nomor_kamar` int(11) NOT NULL,
  `harga_kamar` decimal(10,2) NOT NULL,
  `jenis_kamar` varchar(50) NOT NULL,
  `status` enum('available','filled','under repair') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kamar`
--

INSERT INTO `kamar` (`nomor_kamar`, `harga_kamar`, `jenis_kamar`, `status`) VALUES
(1, '1000000.00', 'mantap', 'available'),
(2, '1000000.00', 'mantap', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `komplain`
--

CREATE TABLE `komplain` (
  `id_penghuni` int(11) NOT NULL,
  `tambahan` int(11) NOT NULL,
  `tanggal_komplain` date NOT NULL,
  `deskripsi` text NOT NULL,
  `bukti` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lahan_parkir`
--

CREATE TABLE `lahan_parkir` (
  `nomor_lahan_parkir` int(11) NOT NULL,
  `harga_lahan_parkir` decimal(10,2) NOT NULL,
  `jenis_lahan_parkir` varchar(50) NOT NULL,
  `status` enum('active','filled','under repair') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lahan_parkir`
--

INSERT INTO `lahan_parkir` (`nomor_lahan_parkir`, `harga_lahan_parkir`, `jenis_lahan_parkir`, `status`) VALUES
(1, '1250.00', 'Motor', 'active'),
(2, '2000.00', 'Mobil', 'active'),
(3, '20000.00', 'Helicopter', 'under repair'),
(4, '2000.00', 'Mobil', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `log_parkir`
--

CREATE TABLE `log_parkir` (
  `id` int(11) NOT NULL,
  `id_penghuni` int(11) DEFAULT NULL,
  `nomor_lahan_parkir` int(11) DEFAULT NULL,
  `tanggal_masuk` datetime NOT NULL,
  `tanggal_keluar` datetime DEFAULT NULL,
  `total_harga` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_parkir`
--

INSERT INTO `log_parkir` (`id`, `id_penghuni`, `nomor_lahan_parkir`, `tanggal_masuk`, `tanggal_keluar`, `total_harga`) VALUES
(1, 4, 1, '2024-06-05 00:00:00', '2024-06-13 00:00:00', '11250.00');

-- --------------------------------------------------------

--
-- Table structure for table `penghuni`
--

CREATE TABLE `penghuni` (
  `id` int(11) NOT NULL,
  `nama_penghuni` varchar(100) NOT NULL,
  `no_ktp` varchar(20) NOT NULL,
  `no_telpon` varchar(20) NOT NULL,
  `kontak_wali` varchar(20) NOT NULL,
  `status` enum('active','idle','non active') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penghuni`
--

INSERT INTO `penghuni` (`id`, `nama_penghuni`, `no_ktp`, `no_telpon`, `kontak_wali`, `status`) VALUES
(1, 'ivan', '111', '81', '82', 'active'),
(4, 'david', '121', '81', '82', 'active'),
(5, 'dani', '131', '81', '82', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `tagihan_denda`
--

CREATE TABLE `tagihan_denda` (
  `id` int(11) NOT NULL,
  `id_denda_pelanggaran` int(11) DEFAULT NULL,
  `bulan` int(11) NOT NULL,
  `tanggal_maksimal_bayar` date NOT NULL,
  `harga_tagihan` decimal(10,2) NOT NULL,
  `denda_keterlambatan` decimal(10,2) DEFAULT 0.00,
  `tanggal_bayar` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tagihan_kamar`
--

CREATE TABLE `tagihan_kamar` (
  `id` int(11) NOT NULL,
  `detail_kamar` int(11) DEFAULT NULL,
  `bulan` int(11) NOT NULL,
  `tanggal_maksimal_bayar` date NOT NULL,
  `harga_tagihan` decimal(10,2) NOT NULL,
  `denda_keterlambatan` decimal(10,2) DEFAULT 0.00,
  `tanggal_bayar` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tagihan_kamar`
--

INSERT INTO `tagihan_kamar` (`id`, `detail_kamar`, `bulan`, `tanggal_maksimal_bayar`, `harga_tagihan`, `denda_keterlambatan`, `tanggal_bayar`) VALUES
(14, 5, 1, '2024-06-12', '1000000.00', '0.00', NULL),
(15, 5, 2, '2024-07-12', '1000000.00', '0.00', NULL),
(16, 6, 1, '2024-06-21', '1000000.00', '0.00', NULL),
(17, 6, 2, '2024-07-21', '1000000.00', '0.00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tagihan_log_parkir`
--

CREATE TABLE `tagihan_log_parkir` (
  `id` int(11) NOT NULL,
  `id_log_parkir` int(11) DEFAULT NULL,
  `bulan` int(11) NOT NULL,
  `tanggal_maksimal_bayar` date NOT NULL,
  `harga_tagihan` decimal(10,2) NOT NULL,
  `denda_keterlambatan` decimal(10,2) DEFAULT 0.00,
  `tanggal_bayar` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `denda_pelanggaran`
--
ALTER TABLE `denda_pelanggaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `fk_adsi_project_penghuni` (`id_penghuni`);

--
-- Indexes for table `detail_kamar`
--
ALTER TABLE `detail_kamar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_penghuni` (`id_penghuni`),
  ADD KEY `nomor_kamar` (`nomor_kamar`);

--
-- Indexes for table `kamar`
--
ALTER TABLE `kamar`
  ADD PRIMARY KEY (`nomor_kamar`);

--
-- Indexes for table `komplain`
--
ALTER TABLE `komplain`
  ADD PRIMARY KEY (`id_penghuni`,`tambahan`);

--
-- Indexes for table `lahan_parkir`
--
ALTER TABLE `lahan_parkir`
  ADD PRIMARY KEY (`nomor_lahan_parkir`);

--
-- Indexes for table `log_parkir`
--
ALTER TABLE `log_parkir`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_penghuni` (`id_penghuni`),
  ADD KEY `nomor_lahan_parkir` (`nomor_lahan_parkir`);

--
-- Indexes for table `penghuni`
--
ALTER TABLE `penghuni`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_ktp` (`no_ktp`);

--
-- Indexes for table `tagihan_denda`
--
ALTER TABLE `tagihan_denda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_denda_pelanggaran` (`id_denda_pelanggaran`);

--
-- Indexes for table `tagihan_kamar`
--
ALTER TABLE `tagihan_kamar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_kamar` (`detail_kamar`);

--
-- Indexes for table `tagihan_log_parkir`
--
ALTER TABLE `tagihan_log_parkir`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_log_parkir` (`id_log_parkir`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `denda_pelanggaran`
--
ALTER TABLE `denda_pelanggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `detail_kamar`
--
ALTER TABLE `detail_kamar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `log_parkir`
--
ALTER TABLE `log_parkir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `penghuni`
--
ALTER TABLE `penghuni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tagihan_denda`
--
ALTER TABLE `tagihan_denda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tagihan_kamar`
--
ALTER TABLE `tagihan_kamar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tagihan_log_parkir`
--
ALTER TABLE `tagihan_log_parkir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `denda_pelanggaran`
--
ALTER TABLE `denda_pelanggaran`
  ADD CONSTRAINT `denda_pelanggaran_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`),
  ADD CONSTRAINT `fk_adsi_project_penghuni` FOREIGN KEY (`id_penghuni`) REFERENCES `penghuni` (`id`);

--
-- Constraints for table `detail_kamar`
--
ALTER TABLE `detail_kamar`
  ADD CONSTRAINT `detail_kamar_ibfk_1` FOREIGN KEY (`id_penghuni`) REFERENCES `penghuni` (`id`),
  ADD CONSTRAINT `detail_kamar_ibfk_2` FOREIGN KEY (`nomor_kamar`) REFERENCES `kamar` (`nomor_kamar`);

--
-- Constraints for table `komplain`
--
ALTER TABLE `komplain`
  ADD CONSTRAINT `komplain_ibfk_1` FOREIGN KEY (`id_penghuni`) REFERENCES `penghuni` (`id`);

--
-- Constraints for table `log_parkir`
--
ALTER TABLE `log_parkir`
  ADD CONSTRAINT `log_parkir_ibfk_1` FOREIGN KEY (`id_penghuni`) REFERENCES `penghuni` (`id`),
  ADD CONSTRAINT `log_parkir_ibfk_2` FOREIGN KEY (`nomor_lahan_parkir`) REFERENCES `lahan_parkir` (`nomor_lahan_parkir`);

--
-- Constraints for table `tagihan_denda`
--
ALTER TABLE `tagihan_denda`
  ADD CONSTRAINT `tagihan_denda_ibfk_1` FOREIGN KEY (`id_denda_pelanggaran`) REFERENCES `denda_pelanggaran` (`id`);

--
-- Constraints for table `tagihan_kamar`
--
ALTER TABLE `tagihan_kamar`
  ADD CONSTRAINT `tagihan_kamar_ibfk_1` FOREIGN KEY (`detail_kamar`) REFERENCES `detail_kamar` (`id`);

--
-- Constraints for table `tagihan_log_parkir`
--
ALTER TABLE `tagihan_log_parkir`
  ADD CONSTRAINT `tagihan_log_parkir_ibfk_1` FOREIGN KEY (`id_log_parkir`) REFERENCES `log_parkir` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
