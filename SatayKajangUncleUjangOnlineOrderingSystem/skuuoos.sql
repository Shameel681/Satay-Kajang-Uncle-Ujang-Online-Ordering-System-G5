-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 23, 2025 at 07:52 PM
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
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `email`, `password`, `phone_no`, `address`) VALUES
(1, 'MUHAMMAD SHAMEEL BIN SHAMSUL ADZMI', 'Shameel681@gmail.com', '$2y$10$uz7mOBzrPvktzY31lmnA/eQgO/XXCwyUXRUR2xrWr5/E6T3dDhumu', '011-10084626', 'selangor'),
(2, 'MUHAMMAD FIKRI BIN MAWARDI', 'toonpow43@gmail.com', '$2y$10$or5N59syXjotYSgypPhg/.Shz0Iw1Bm9ERcmS8W/5BBig0xDsTQhi', '011-62226128', NULL),
(3, 'ANAS IZZUDDIN BIN MUAMAR ', 'anas3939@gmail.com', '$2y$10$4Dvud/q9cYjln6Ow0Lk7K.xlCJ9DImmL0kuNhUB3RMQMR/Q4DOd62', '011-101010101', NULL),
(4, 'suhaimi sham', 'suhaimi@gmail.com', '$2y$10$79IvAgCfEHpranqu8d7ib.urSWsjGZaY8uEWdZ08iXpskqxtOzTey', '01119181717', NULL);

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
(4, 'MUHAMMAD SHAMEEL BIN SHAMSUL ADZMI', 'Shameel681@gmail.com', 'sedap jugak ye', '2025-08-17 19:32:31');

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
(29, 'MUHAMMAD ZAKUAN', 'mfitrizakuan@gmail.com', 'MAKANAN RASA BIASA BIASA JE', '2025-08-17 19:32:03');

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `feedback_customer`
--
ALTER TABLE `feedback_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `feedback_guest`
--
ALTER TABLE `feedback_guest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
ALTER TABLE `customer`
ADD `reset_token` VARCHAR(100) NULL,
ADD `reset_expires` DATETIME NULL;

--
-- ... (rest of the SQL remains the same)

COMMIT;

CREATE TABLE staff (
    staff_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone_no VARCHAR(20),
    address TEXT,
    role ENUM('Admin','Staff') DEFAULT 'Staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE staff
ADD reset_token VARCHAR(100) NULL,
ADD reset_expires DATETIME NULL;

ALTER TABLE customer
ADD COLUMN is_verified TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN verify_token VARCHAR(64) DEFAULT NULL;
