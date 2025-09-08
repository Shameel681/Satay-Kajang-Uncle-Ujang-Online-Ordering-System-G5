-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2025 at 02:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
(1, 'MUHAMMAD SHAMEEL BIN SHAMSUL ADZMI', 'Shameel681@gmail.com', '$2y$10$uz7mOBzrPvktzY31lmnA/eQgO/XXCwyUXRUR2xrWr5/E6T3dDhumu', '011-10084626', 'selangor', NULL, NULL, NULL, 0, NULL),
(2, 'MUHAMMAD FIKRI BIN MAWARDI', 'toonpow43@gmail.com', '', '011-62226128', 'NO 1 JALAN 1 TAMAN SERI TANJUNG', 'c809f97644999a9b8e3f6f2b30711c8c7b2b26c15b12f7c635c1dfa8bbf6e40e', '2025-08-24 08:24:50', '2025-08-24 21:35:45', 0, NULL),
(3, 'ANAS IZZUDDIN BIN MUAMAR ', 'anas3939@gmail.com', '$2y$10$4Dvud/q9cYjln6Ow0Lk7K.xlCJ9DImmL0kuNhUB3RMQMR/Q4DOd62', '011-101010101', NULL, NULL, NULL, NULL, 0, NULL),
(4, 'suhaimi sham', 'suhaimi@gmail.com', '$2y$10$79IvAgCfEHpranqu8d7ib.urSWsjGZaY8uEWdZ08iXpskqxtOzTey', '01119181717', NULL, NULL, NULL, NULL, 0, NULL),
(10, 'alexparley', 'alexparley123@gmail.com', '$2y$10$uUea55sKFzt8WUvFOz5UxO2sYSEN4fk.nLmvBLtMSTm5mO63T2OUq', '01163336128', NULL, NULL, NULL, NULL, 0, NULL),
(24, 'FIKRI', 'toonpow3@gmail.com', '$2y$10$RBJAZvmMN46WtDGjg53UUudRBZrrUOastHMdHylHJ0qNlA24pWcJW', '01163336128', 'NO 1 JALA', NULL, NULL, NULL, 1, NULL);

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
(5, 'MUHAMMAD FIKRI BIN MAWARDI', 'toonpow43@gmail.com', 'sedapnyooooooooooooo', '2025-08-24 05:33:18');

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
(30, 'MUHAMMAD SYABIL AMSYAR', 'syabil123@gmail.com', 'sedap', '2025-09-04 14:24:06'),
(31, 'MUHAMMAD SYABIL AMSYAR', 'toonpow00@gmail.com', 'tak sedap', '2025-09-04 14:26:47');

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
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `name`, `email`, `password`, `phone_no`, `address`, `role`, `reset_token`, `reset_expires`) VALUES
(1, 'Ali', 'ali@ujang.com', '$2y$10$Bph4UG1okIqaKHj0s4AAcuQjB73ExrTdXbNj1RZpXkCFQnnwWkW72', '0123456789', NULL, 'Staff', NULL, NULL),
(103, 'Siti Binti Ahmad', 'siti@ujang.com', '$2y$10$g6K5m9bxV3Fs6L2Yk...', '0198765432', NULL, 'Staff', NULL, NULL),
(104, 'Kumar A/L Rajan', 'kumar@ujang.com', '$2y$10$u4L2X9LjqPh6FhL3...', '0172233445', NULL, 'Staff', NULL, NULL);

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `feedback_customer`
--
ALTER TABLE `feedback_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback_guest`
--
ALTER TABLE `feedback_guest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
