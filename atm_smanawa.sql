-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2026 at 01:19 AM
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
-- Database: `atm_smanawa`
--

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `jenis` enum('Setor','Tarik') DEFAULT NULL,
  `jumlah` bigint(20) DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `user_id`, `jenis`, `jumlah`, `tanggal`) VALUES
(1, 1, 'Setor', 200000, '2025-11-11 12:44:26'),
(2, 1, 'Tarik', 150000, '2025-11-11 12:44:36'),
(3, 1, 'Setor', 50000, '2025-11-14 11:29:03'),
(4, 2, 'Setor', 200000, '2025-11-14 11:30:26'),
(5, 1, 'Tarik', 25000, '2025-11-17 11:52:26'),
(6, 1, 'Setor', 15000, '2025-11-17 11:52:50'),
(7, 3, 'Setor', 200, '2025-11-17 11:59:55'),
(8, 3, 'Setor', 200, '2025-11-17 11:59:56'),
(9, 3, 'Setor', 200, '2025-11-17 11:59:59'),
(10, 2, 'Tarik', 33000, '2025-11-17 12:05:19'),
(11, 1, 'Setor', 10, '2026-01-26 11:27:54'),
(12, 1, 'Setor', 10, '2026-01-26 11:30:26'),
(13, 1, 'Setor', 10, '2026-01-26 11:30:49'),
(14, 1, 'Setor', 10, '2026-01-26 11:33:01'),
(15, 1, 'Tarik', 40, '2026-01-26 11:33:30'),
(16, 1, 'Setor', 10, '2026-01-26 11:33:37'),
(17, 1, 'Tarik', 50, '2026-01-26 11:34:28'),
(18, 2, 'Tarik', 30000, '2026-01-26 11:36:00'),
(19, 2, 'Tarik', 30000, '2026-01-26 11:36:37'),
(20, 2, 'Setor', 50000, '2026-01-26 11:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `transfer`
--

CREATE TABLE `transfer` (
  `id` int(11) NOT NULL,
  `pengirim_id` int(11) DEFAULT NULL,
  `penerima_id` int(11) DEFAULT NULL,
  `jumlah` bigint(20) DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transfer`
--

INSERT INTO `transfer` (`id`, `pengirim_id`, `penerima_id`, `jumlah`, `tanggal`) VALUES
(1, 1, 3, 50000, '2025-11-17 10:56:41'),
(2, 1, 3, 10000, '2025-11-17 11:52:41'),
(3, 1, 3, 100000, '2025-11-17 11:59:56'),
(4, 2, 4, 67000, '2025-11-17 12:01:44'),
(5, 1, 2, 30000, '2026-01-26 11:11:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `pin` varchar(10) NOT NULL,
  `saldo` bigint(20) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `pin`, `saldo`) VALUES
(1, 'Arva', '1234', 900000),
(2, 'Budi', '5678', 2150000),
(3, 'Cici', '9101', 1660600),
(4, 'Dina', '1122', 3067000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transfer`
--
ALTER TABLE `transfer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengirim_id` (`pengirim_id`),
  ADD KEY `penerima_id` (`penerima_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `transfer`
--
ALTER TABLE `transfer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transfer`
--
ALTER TABLE `transfer`
  ADD CONSTRAINT `transfer_ibfk_1` FOREIGN KEY (`pengirim_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transfer_ibfk_2` FOREIGN KEY (`penerima_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
