-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2025 at 11:53 PM
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
-- Database: `skuuoos`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verify_token` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `email`, `password`, `is_verified`, `verify_token`, `last_login`, `created_at`, `updated_at`) VALUES
(3, 'MUHAMMAD SHAMEEL BIN SHAMSUL ADZMI', 'shameel681@gmail.com', '$2y$10$J149gfHywEFwTNTZStdDDeF/8bfLsEYp.uANEeycci5j.nXuqvMRG', 1, NULL, '2025-09-09 05:48:39', '2025-09-09 05:43:46', '2025-09-09 05:48:39');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verify_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `email`, `password`, `phone_no`, `address`, `reset_token`, `reset_expires`, `reset_expiry`, `is_verified`, `verify_token`) VALUES
(19, 'MUHAMMAD SUHAIMI BIN MOHD SHAM', 'm.suhaimipro@gmail.com', '$2y$10$i2Vv1tNp/a1nVGc61hnZbOoIRpasC0Z1mlPFQRTVdRQJ.Bw4QaD4K', '01113277665', NULL, NULL, NULL, NULL, 0, '91b93fa6d1fbbd6507b59041144081f4'),
(20, 'MUHAMMAD ANAS IZZUDIN BIN MUAMAR ', 'anasizzuddin@graduate.utm.my', '$2y$10$0CEJ4RszQYGHE60cw.YT1uQVYRXvHmx3vkN.eB8Cx0E9qlbaHpnzq', '0102045904', NULL, NULL, NULL, NULL, 0, '4a37db85f42c739ce2adaa7d1489fcbd'),
(21, 'ZUHAIKAL AIMAN BIN ZAILAN', 'zuhaikal566@gmail.com', '$2y$10$aQAb6.4Xu7BK2YViunQ.1.gYdRmcqTFptagC7AbtiKuhC.yh4dt6S', '01133114674', NULL, NULL, NULL, NULL, 0, 'ac51b47d0095cbc2aa865c3d847f1a99'),
(22, 'MUHAMMAD FIKRI BIN MAWARDI', 'toonpow3@gmail.com', '$2y$10$rAcVn8qWtVLl/doUGJEArOdBjc2HBQX24saGP9tJ0GEk6ThsZuApa', '011-6222 6128', NULL, NULL, NULL, NULL, 0, '199eb125e30897606e264033f6698275'),
(33, 'MUHAMMAD SHAMEEL BIN SHAMSUL ADZMI', 'shameel681@gmail.com', '$2y$10$AexpywrEw4bh.ogj/TizROlEUHR/J.zKpTCSVdsPyqbX9sVqw1LtO', '01110084626', NULL, NULL, NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback_customer`
--

CREATE TABLE `feedback_customer` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `feedback` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_customer`
--

INSERT INTO `feedback_customer` (`id`, `customer_name`, `customer_email`, `feedback`, `created_at`) VALUES
(1, 'MUHAMMAD SHAMEEL BIN SHAMSUL ADZMI', 'Shameel681@gmail.com', 'sedap', '2025-08-17 19:18:16'),
(2, 'suhaimi sham', 'suhaimi@gmail.com', 'makanan sedap gile', '2025-08-17 19:24:23'),
(3, 'suhaimi sham', 'suhaimi@gmail.com', 'sedapnya makanan', '2025-08-17 19:28:28'),
(4, 'MUHAMMAD SHAMEEL BIN SHAMSUL ADZMI', 'Shameel681@gmail.com', 'sedap jugak ye', '2025-08-17 19:32:31'),
(5, 'MUHAMMAD FIKRI BIN MAWARDI', 'toonpow43@gmail.com', 'sedapnyooooooooooooo', '2025-08-24 05:33:18'),
(6, 'MUHAMMAD SHAMEEL BIN SHAMSUL ADZMI', 'shameel681@gmail.com', 'test 2', '2025-08-28 06:37:45');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_guest`
--

CREATE TABLE `feedback_guest` (
  `id` int(11) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `guest_email` varchar(100) NOT NULL,
  `feedback` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_guest`
--

INSERT INTO `feedback_guest` (`id`, `guest_name`, `guest_email`, `feedback`, `created_at`) VALUES
(22, 'fikri', 'fikri@fikri.com', 'fikri', '2025-08-17 18:03:35'),
(23, 'shameel', 'shameel@gmail.com', 'shameel', '2025-08-17 18:04:54'),
(24, 'anas', 'anas@nas.com', 'saya anas', '2025-08-17 18:15:45'),
(25, 'suhaimi test', 'test@suhaimi.com', 'saya tengah test', '2025-08-17 18:22:04'),
(26, 'fikri mawardi', 'fikri@gmail.com', 'saya tak suka makanan sini', '2025-08-17 18:23:41'),
(27, 'shameel clone', 'shameeldoubleganger@gmail.com', 'makanan ini tidak sedap', '2025-08-17 19:20:54'),
(28, 'shameel', 'shameel@hotmail.com', 'makanan 10/10', '2025-08-17 19:23:15'),
(29, 'MUHAMMAD ZAKUAN', 'mfitrizakuan@gmail.com', 'MAKANAN RASA BIASA BIASA JE', '2025-08-17 19:32:03'),
(30, 'MUHAMMAD FIKRI BIN MAWARDI', 'toonpow43@gmail.com', 'test 1', '2025-08-28 06:30:33'),
(31, 'fitrizakuanazmee', 'm.fitreezakuanazmee@gmail.com', 'syedap', '2025-09-07 09:56:33');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('Admin','Staff') DEFAULT 'Staff',
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `feedback_customer`
--
ALTER TABLE `feedback_customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback_guest`
--
ALTER TABLE `feedback_guest`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `feedback_customer`
--
ALTER TABLE `feedback_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback_guest`
--
ALTER TABLE `feedback_guest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
