-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2025 at 01:16 PM
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
  `phone_no` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verify_token` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `email`, `phone_no`, `address`, `password`, `is_verified`, `verify_token`, `last_login`, `created_at`, `updated_at`, `reset_token`, `reset_expires`) VALUES
(3, 'MUHAMMAD SHAMEEL', 'shameel681@gmail.com', '01110084626', 'no1. jalan semarak', '$2y$10$J149gfHywEFwTNTZStdDDeF/8bfLsEYp.uANEeycci5j.nXuqvMRG', 1, NULL, '2025-09-21 19:12:50', '2025-09-09 05:43:46', '2025-09-21 19:12:50', NULL, NULL),
(5, 'Muhammad Fikri Bin Mawardi', 'toonpow3@gmail.com', '0119898256', 'AG-2, JALAN DESA KENANGA 2, TAMAN DESA KENANGA', '$2y$10$ByarGQwCoC2eLLtXq17pQuMLBrPpvXeLssb9QZVnmhEZ0QSFWSUdy', 1, NULL, '2025-09-18 14:04:00', '2025-09-09 06:32:48', '2025-09-18 14:04:00', NULL, NULL);

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
  `verify_token` varchar(64) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_logged_in` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `email`, `password`, `phone_no`, `address`, `reset_token`, `reset_expires`, `reset_expiry`, `is_verified`, `verify_token`, `profile_image`, `created_at`, `last_logged_in`, `updated_at`) VALUES
(19, 'MUHAMMAD SUHAIMI BIN MOHD SHAM', 'm.suhaimipro@gmail.com', '$2y$10$i2Vv1tNp/a1nVGc61hnZbOoIRpasC0Z1mlPFQRTVdRQJ.Bw4QaD4K', '01113277665', '', NULL, NULL, NULL, 0, '91b93fa6d1fbbd6507b59041144081f4', NULL, '2025-09-19 13:32:35', NULL, '2025-09-21 11:11:07'),
(20, 'MUHAMMAD ANAS IZZUDIN BIN MUAMAR ', 'anasizzuddin@graduate.utm.my', '$2y$10$0CEJ4RszQYGHE60cw.YT1uQVYRXvHmx3vkN.eB8Cx0E9qlbaHpnzq', '0102045904', NULL, NULL, NULL, NULL, 0, '4a37db85f42c739ce2adaa7d1489fcbd', NULL, '2025-09-19 13:32:35', NULL, '2025-09-21 08:16:25'),
(21, 'ZUHAIKAL AIMAN BIN ZAILAN', 'zuhaikal566@gmail.com', '$2y$10$aQAb6.4Xu7BK2YViunQ.1.gYdRmcqTFptagC7AbtiKuhC.yh4dt6S', '01133114674', NULL, NULL, NULL, NULL, 0, 'ac51b47d0095cbc2aa865c3d847f1a99', NULL, '2025-09-19 13:32:35', NULL, '2025-09-21 08:16:25'),
(33, 'MUHAMMAD SHAMEEL BIN SHAMSUL ADZMI', 'shameel681@gmail.com', '$2y$10$AexpywrEw4bh.ogj/TizROlEUHR/J.zKpTCSVdsPyqbX9sVqw1LtO', '01110084626', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-09-19 13:32:35', '2025-09-19 13:36:57', '2025-09-21 08:16:25'),
(35, 'FIKRI MAWARDI', 'toonpow3@gmail.com', '$2y$10$YmwIMqggLTllmpoC7KxwfeoDe7fzzIhU421e.L3yhcWA4ENjwXx9S', '01162226128', NULL, NULL, NULL, NULL, 0, '70068c343d2eb9622fbf6d7ec37e91a6', NULL, '2025-09-19 13:32:35', NULL, '2025-09-21 08:16:25');

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
(25, 'suhaimi test', 'test@suhaimi.com', 'saya tengah test', '2025-08-17 18:22:04'),
(26, 'fikri mawardi', 'fikri@gmail.com', 'saya tak suka makanan sini', '2025-08-17 18:23:41'),
(27, 'shameel clone', 'shameeldoubleganger@gmail.com', 'makanan ini tidak sedap', '2025-08-17 19:20:54'),
(28, 'shameel', 'shameel@hotmail.com', 'makanan 10/10', '2025-08-17 19:23:15'),
(29, 'MUHAMMAD ZAKUAN', 'mfitrizakuan@gmail.com', 'MAKANAN RASA BIASA BIASA JE', '2025-08-17 19:32:03'),
(31, 'fitrizakuanazmee', 'm.fitreezakuanazmee@gmail.com', 'syedap', '2025-09-07 09:56:33'),
(32, 'MUHAMMAD SYABIL AMSYAR', 'toonpow43@gmail.com', 'gasjgdjgdggh', '2025-09-09 13:31:44');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `food_id` varchar(10) NOT NULL,
  `food_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `category` enum('Main Dish','Side Dish') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`food_id`, `food_name`, `price`, `description`, `image_path`, `category`, `created_at`, `updated_at`) VALUES
('F01', 'Satay Ayam', 1.30, 'Ayam diperap rempah rahsia, memanggang harum semerbak', '../image/1758449921_satay ayam.png', 'Main Dish', '2025-09-21 09:36:59', '2025-09-21 10:18:41'),
('F02', 'Satay Daging', 1.20, 'Daging dihiris halus, lembut dan penuh rasa', '../image/satay daging.jpg', 'Main Dish', '2025-09-21 09:36:59', '2025-09-21 09:36:59'),
('F03', 'Satay Perut', 1.20, 'Perut direndam rempah, kenyal dan berperisa unik', '../image/satay perut.jpg', 'Main Dish', '2025-09-21 09:36:59', '2025-09-21 09:36:59'),
('F04', 'Satay Kambing', 2.00, 'Kambing dipanggang tepat, wangi dan tiada bau', '../image/Satay kambing.jpg', 'Main Dish', '2025-09-21 09:36:59', '2025-09-21 09:36:59'),
('S01', 'Kuah Kacang', 2.00, 'Kuah kacang yang dimasak sempurna, memberikan rasa lemak-manis yang memikat', '../image/Kuah kacang.jpg', 'Side Dish', '2025-09-21 09:36:59', '2025-09-21 09:36:59'),
('S02', 'Nasi Impit', 1.50, 'Nasi impit padat tapi lembut, dikukus segar setiap pagi untuk tekstur sempurna ketika dicicah dengan kuah.', '../image/Nasi Impit lagi.jpg', 'Side Dish', '2025-09-21 09:36:59', '2025-09-21 09:36:59');

--
-- Triggers `menu`
--
DELIMITER $$
CREATE TRIGGER `before_insert_menu` BEFORE INSERT ON `menu` FOR EACH ROW BEGIN
    DECLARE next_id INT;

    IF NEW.category = 'Main Dish' THEN
        -- Get last main dish ID starting with 'F'
        SELECT COALESCE(MAX(CAST(SUBSTRING(food_id, 2) AS UNSIGNED)), 0) + 1
        INTO next_id
        FROM menu
        WHERE food_id LIKE 'F%';

        SET NEW.food_id = CONCAT('F', LPAD(next_id, 2, '0'));

    ELSEIF NEW.category = 'Side Dish' THEN
        -- Get last side dish ID starting with 'S'
        SELECT COALESCE(MAX(CAST(SUBSTRING(food_id, 2) AS UNSIGNED)), 0) + 1
        INTO next_id
        FROM menu
        WHERE food_id LIKE 'S%';

        SET NEW.food_id = CONCAT('S', LPAD(next_id, 2, '0'));
    END IF;
END
$$
DELIMITER ;

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
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_logged_in` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `name`, `email`, `password`, `phone_no`, `address`, `reset_token`, `reset_expires`, `created_at`, `last_logged_in`) VALUES
(101, 'Toon Pow 1', 'toonpow3@gmail.com', '$2y$10$Zbq2n3YkF7gNQdFhnx9rCe3w7I0JfD3B5bzQY8rZK5xM7vRjY9YxG', NULL, NULL, NULL, NULL, '2025-09-19 15:23:13', NULL),
(102, 'shameel', 'toonpow43@gmail.com', '$2y$10$k6gaszO/75eAGJTH19Oj5eyFCXv3JcQpBuPebgmmW5WzStDJ66bMi', NULL, NULL, NULL, NULL, '2025-09-19 15:23:13', NULL),
(103, 'MUHAMMAD SHAMEELs', 'shameel681@gmail.com', '$2y$10$kLqkFw6NcZvq2Ow3KRLiQ.fZJxGGMRNzAXLPL1sgekVVceJmnckmq', '01110084626', 'no1. jalan semarak', NULL, NULL, '2025-09-21 07:34:09', NULL);

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
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`food_id`);

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
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `feedback_customer`
--
ALTER TABLE `feedback_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback_guest`
--
ALTER TABLE `feedback_guest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
