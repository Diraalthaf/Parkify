-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 09, 2026 at 02:58 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_parkir_ukk`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int NOT NULL,
  `transaksi_id` int DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci,
  `user_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `transaksi_id`, `action`, `message`, `user_id`, `created_at`) VALUES
(1, 38, 'PAYMENT', 'Pembayaran Rp117000, Fee Rp117000, Kembalian Rp0', 21, '2026-04-02 09:05:45'),
(2, 38, 'GATE_OPEN', 'Gerbang dibuka untuk kartu AABBCCDD', 21, '2026-04-02 09:05:45'),
(3, 43, 'PAYMENT', 'Pembayaran Rp50, Fee Rp50, Kembalian Rp0', 21, '2026-04-02 16:30:17'),
(4, 43, 'GATE_OPEN', 'Gerbang dibuka untuk kartu E2537', 21, '2026-04-02 16:30:17'),
(5, 44, 'PAYMENT', 'Pembayaran Rp50, Fee Rp50, Kembalian Rp0', 21, '2026-04-08 12:45:02'),
(6, 44, 'GATE_OPEN', 'Gerbang dibuka untuk kartu E2537', 21, '2026-04-08 12:45:02'),
(7, 46, 'PAYMENT', 'Pembayaran Rp50, Fee Rp50, Kembalian Rp0', 21, '2026-04-08 12:46:21'),
(8, 46, 'GATE_OPEN', 'Gerbang dibuka untuk kartu 9C896A6', 21, '2026-04-08 12:46:21'),
(9, 50, 'PAYMENT', 'Pembayaran Rp50, Fee Rp50, Kembalian Rp0', 21, '2026-04-08 12:47:53'),
(10, 50, 'GATE_OPEN', 'Gerbang dibuka untuk kartu 45E2F26', 21, '2026-04-08 12:47:53'),
(11, 47, 'PAYMENT', 'Pembayaran Rp150, Fee Rp150, Kembalian Rp0', 21, '2026-04-09 19:48:02'),
(12, 47, 'GATE_OPEN', 'Gerbang dibuka untuk kartu 45E2F26', 21, '2026-04-09 19:48:02');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int NOT NULL,
  `card_id` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nopol` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `checkin_time` datetime DEFAULT NULL,
  `checkout_time` datetime DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `fee` int DEFAULT NULL,
  `status` enum('IN','OUT','DONE') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'IN',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `paid_amount` int DEFAULT NULL,
  `change_amount` int DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `card_id`, `nopol`, `checkin_time`, `checkout_time`, `duration`, `fee`, `status`, `created_at`, `paid_amount`, `change_amount`, `paid_at`) VALUES
(21, 'TAG1024', 'B 6141 QTZ', '2026-03-10 08:58:55', '2026-03-10 09:30:59', 1, 3000, 'DONE', '2026-03-10 08:58:55', 3000, 0, '2026-03-10 09:31:09'),
(22, 'TAG1117', 'B 1388 OSD', '2026-03-10 08:59:55', '2026-03-10 09:33:08', 1, 3000, 'DONE', '2026-03-10 08:59:55', 3000, 0, '2026-03-23 21:46:14'),
(29, '1234', 'B 9827 TSJ', '2026-03-13 16:01:45', '2026-03-13 16:02:23', 1, 3000, 'DONE', '2026-03-13 16:01:45', 3000, 0, '2026-03-13 16:02:51'),
(30, '1234', 'B 8659 FPB', '2026-03-13 20:09:32', '2026-03-13 20:11:19', 1, 3000, 'DONE', '2026-03-13 20:09:32', 3000, 0, '2026-03-23 21:46:02'),
(31, '55667788', 'B 8247 KOI', '2026-03-13 20:12:09', '2026-03-23 21:16:14', 242, 726000, 'OUT', '2026-03-13 20:12:09', NULL, NULL, NULL),
(32, 'AABBCCDD', 'B 2065 JYQ', '2026-03-13 20:12:24', '2026-03-23 20:30:39', 241, 723000, 'OUT', '2026-03-13 20:12:24', NULL, NULL, NULL),
(33, '4112233', 'B 7337 WCT', '2026-03-13 20:14:22', '2026-03-23 21:45:24', 242, 726000, 'DONE', '2026-03-13 20:14:22', 726000, 0, '2026-03-25 12:13:49'),
(34, '11223344', 'B 5437 VBE', '2026-03-13 20:15:06', '2026-03-23 20:30:08', 241, 723000, 'DONE', '2026-03-13 20:15:06', 723000, 0, '2026-03-23 21:45:43'),
(35, '1234', 'B 8362 THY', '2026-03-23 21:50:47', '2026-03-25 17:43:43', 2633, 7899000, 'DONE', '2026-03-23 21:50:47', 7899000, 0, '2026-03-25 17:45:00'),
(36, '11223344', 'B 1450 BZF', '2026-03-23 21:50:59', NULL, NULL, NULL, 'IN', '2026-03-23 21:50:59', NULL, NULL, NULL),
(37, '55667788', 'B 9834 FYP', '2026-03-23 21:51:04', '2026-03-25 17:44:11', 2634, 7902000, 'OUT', '2026-03-23 21:51:04', NULL, NULL, NULL),
(38, 'AABBCCDD', 'B 7475 LNB', '2026-03-23 21:51:09', '2026-03-25 12:09:07', 39, 117000, 'DONE', '2026-03-23 21:51:09', 117000, 0, '2026-04-02 09:05:45'),
(39, '4112233', 'B 4994 ZFR', '2026-03-25 17:42:45', NULL, NULL, NULL, 'IN', '2026-03-25 17:42:45', NULL, NULL, NULL),
(40, 'C0FFEE99', 'B 7041 JBT', '2026-03-25 17:43:29', '2026-03-27 13:19:34', 2617, 7851000, 'DONE', '2026-03-25 17:43:29', 7851000, 0, '2026-03-27 21:40:54'),
(41, 'AABBCCDD', 'B 2535 FKD', '2026-03-27 13:18:47', NULL, NULL, NULL, 'IN', '2026-03-27 13:18:47', NULL, NULL, NULL),
(42, '55667788', 'B 8490 MWP', '2026-03-27 13:19:13', NULL, NULL, NULL, 'IN', '2026-03-27 13:19:13', NULL, NULL, NULL),
(43, 'E2537', 'B 1198 CII', '2026-04-02 16:29:02', '2026-04-02 16:29:22', 1, 50, 'DONE', '2026-04-02 16:29:02', 50, 0, '2026-04-02 16:30:17'),
(44, 'E2537', 'B 9013 UOO', '2026-04-02 16:29:49', '2026-04-02 16:29:57', 1, 50, 'DONE', '2026-04-02 16:29:49', 50, 0, '2026-04-08 12:45:02'),
(45, 'E2537', 'B 6249 YCC', '2026-04-02 16:30:55', NULL, NULL, NULL, 'IN', '2026-04-02 16:30:55', NULL, NULL, NULL),
(46, '9C896A6', 'B 6396 XYT', '2026-04-08 12:43:08', '2026-04-08 12:44:07', 1, 50, 'DONE', '2026-04-08 12:43:08', 50, 0, '2026-04-08 12:46:20'),
(47, '45E2F26', 'B 8166 IOU', '2026-04-08 12:43:34', '2026-04-08 12:46:07', 3, 150, 'DONE', '2026-04-08 12:43:34', 150, 0, '2026-04-09 19:48:02'),
(48, 'E95AF26', 'B 4322 AHU', '2026-04-08 12:43:47', NULL, NULL, NULL, 'IN', '2026-04-08 12:43:47', NULL, NULL, NULL),
(49, 'D95D6A6', 'B 2279 KMW', '2026-04-08 12:43:57', NULL, NULL, NULL, 'IN', '2026-04-08 12:43:57', NULL, NULL, NULL),
(50, '45E2F26', 'B 8714 FIW', '2026-04-08 12:47:12', '2026-04-08 12:47:32', 1, 50, 'DONE', '2026-04-08 12:47:12', 50, 0, '2026-04-08 12:47:53');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `level` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nama`, `username`, `password`, `level`, `created_at`) VALUES
(15, 'Andira', '1', '1', 'admin', '2026-03-31 12:18:38'),
(16, 'Althaf', '2', '2', 'owner', '2026-03-31 12:18:38'),
(21, 'Aryaga', '3', '3', 'pegawai', '2026-04-01 22:50:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `card_id` (`card_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
