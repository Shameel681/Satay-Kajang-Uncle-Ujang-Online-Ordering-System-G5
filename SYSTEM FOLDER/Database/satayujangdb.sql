-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 30, 2025 at 08:51 PM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u134600246_satayujangdb`
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
  `reset_expires` datetime DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `email`, `phone_no`, `address`, `password`, `is_verified`, `verify_token`, `last_login`, `created_at`, `updated_at`, `reset_token`, `reset_expires`, `profile_image`) VALUES
(3, 'MUHAMMAD SHAMEEL', 'shameel681@gmail.com', '01110084626', 'no1. jalan semarak', '$2y$10$J149gfHywEFwTNTZStdDDeF/8bfLsEYp.uANEeycci5j.nXuqvMRG', 1, NULL, '2025-10-27 05:27:09', '2025-09-09 05:43:46', '2025-10-27 05:27:09', NULL, NULL, '3_1760279075.jpg'),
(5, 'Muhammad Fikri Bin Mawardi', 'toonpow3@gmail.com', '0119898256', 'AG-2, JALAN DESA KENANGA 2, TAMAN DESA KENANGA', '$2y$10$ByarGQwCoC2eLLtXq17pQuMLBrPpvXeLssb9QZVnmhEZ0QSFWSUdy', 1, NULL, '2025-10-26 19:05:55', '2025-09-09 06:32:48', '2025-10-26 19:05:55', NULL, NULL, 'capybara.jpg'),
(6, 'ZUHAIKAL AIMAN BIN ZAILAN', 'zuhaikal566@gmail.com', '01133114674', 'BATU PAHAT', '$2y$10$NW6EcmriO43RVBbT.dnqHuX68RVptJ7YYaceDScX7iMuuXldtNiF6', 0, 'd3924ed235507b3af60c87e163e574c2', NULL, '2025-10-27 05:06:31', '2025-10-27 05:06:31', NULL, NULL, NULL),
(7, 'MUHAMMAD SUHAIMI BIN MOHD SHAM', 'm.suhaimipro@gmail.com', '01113277665', 'DESA SAUJANA LANGAT', '$2y$10$RCfurhWGQJgR4StP7/yPq.4HL7KjsnNeym/h1k6Nr56njw2KtVFhq', 0, 'abe72bce686320038e471b75724c5878', '2025-10-29 16:31:10', '2025-10-27 05:07:11', '2025-10-29 16:59:08', NULL, NULL, '7_1761757148.png'),
(8, 'ANAS IZZUDIN BIN MUAMAR', 'anasizzuddin@graduate.utm.my', '0102045904', 'Wangsa Maju', '$2y$10$4Z5/BSVKi3pbslETD24g5ezdCE1HYQKc68ebTVEiMM30lQGYjWw2i', 0, 'febde958f157c71ca06e781cb936affc', NULL, '2025-10-27 05:08:01', '2025-10-27 05:08:01', NULL, NULL, NULL);

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
  `customer_image` varchar(255) DEFAULT NULL,
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

INSERT INTO `customer` (`customer_id`, `name`, `email`, `password`, `phone_no`, `address`, `customer_image`, `reset_token`, `reset_expires`, `reset_expiry`, `is_verified`, `verify_token`, `profile_image`, `created_at`, `last_logged_in`, `updated_at`) VALUES
(20, 'MUHAMMAD ANAS IZZUDIN BIN MUAMAR', 'anasizzuddin@graduate.utm.my', '$2y$10$0CEJ4RszQYGHE60cw.YT1uQVYRXvHmx3vkN.eB8Cx0E9qlbaHpnzq', '0102045904', 'wangsa', NULL, NULL, NULL, NULL, 0, '4a37db85f42c739ce2adaa7d1489fcbd', NULL, '2025-09-19 13:32:35', NULL, '2025-09-25 07:02:29'),
(33, 'MUHAMMAD SHAMEEL BIN SHAMSUL ADZMI', 'shameel681@gmail.com', '$2y$10$AexpywrEw4bh.ogj/TizROlEUHR/J.zKpTCSVdsPyqbX9sVqw1LtO', '01110084626', 'buloh', 'cust_33.jpg', NULL, NULL, NULL, 1, NULL, NULL, '2025-09-19 13:32:35', '2025-10-27 05:09:38', '2025-10-27 05:09:38'),
(37, 'FIKRI MAWARDI', 'toonpow43@gmail.com', '$2y$10$zOnBDUa/3aT8G4pEzCdbYOi6CVKpeQ2SUjEgUg7VrFMkO/a25zBiq', '0116222612', 'AG-2, JALAN DESA KENANGA 2, TAMAN DESA KENANGA', 'cust_37.jpg', NULL, NULL, NULL, 1, NULL, 'cust_37.jpg', '2025-09-22 22:59:57', '2025-10-27 05:24:03', '2025-10-27 05:24:03'),
(39, 'Zuhaikal Aiman', 'zuhaikal566@gmail.com', '$2y$10$5wrwEIkO2MgL2weI4AQRkeEQhawl6.UQxsNrYIO1V9Dy2i9HmBF/6', '01133114674', '', NULL, NULL, NULL, NULL, 0, '20d271c14f2f8dd0a065722310ab8ce2', NULL, '2025-10-26 16:21:42', '2025-10-26 17:40:10', '2025-10-26 17:40:58'),
(40, 'MUHAMMAD SUHAIMI BIN MOHD SHAM', 'samshark411@gmail.com', '$2y$10$LJaLn/1kGp7n2w7y60JZwexdBRUFbCis8ecKSbtUUmsGDjZv4aHMS', '01113277665', NULL, 'cust_40.png', NULL, NULL, NULL, 0, '33fa543317bc3bf3a4a5e97bbf1391d9', NULL, '2025-10-26 16:22:44', '2025-10-29 17:12:47', '2025-10-29 17:13:25'),
(42, 'FIKRI SAPUTRA', 'justpiki123@gmail.com', '$2y$10$5Ly3dNmHIdBGMVrM82zBrePdgG5qJHvydm/NWRyuodWYmlil0vh1.', '01176667126', 'NO 1 JALAN 1 TAMAN SRI TANJUNG', 'cust_42.jpg', NULL, NULL, NULL, 1, NULL, NULL, '2025-10-26 17:32:30', '2025-10-26 17:42:07', '2025-10-26 17:42:07'),
(43, 'MUHAMMAD SUHAIMI BIN MOHD SHAM', 'm.suhaimipro@gmail.com', '$2y$10$M5B2RXtJ/E9rHWFwM7QN9ewJM2TmjwpnYHeLv8IX6kGQVwiu7vEmS', '01113277665', NULL, 'cust_43.png', NULL, NULL, NULL, 1, NULL, NULL, '2025-10-27 05:06:00', '2025-10-27 05:06:38', '2025-10-27 05:09:05'),
(44, 'WAN AQIL DANISH BIN MOHD NIZAM', 'wanaqildanish@graduate.utm.my', '$2y$10$QB4ZTHgvBpMMZY0B.INoKeLJUuLpfcM8tsZGCfiK2OH9W9CTGrCmy', '01133368836', NULL, 'cust_44.jpg', NULL, NULL, NULL, 1, NULL, NULL, '2025-10-27 14:51:52', '2025-10-27 14:52:53', '2025-10-27 14:56:55'),
(45, 'asiah', 'nuraisyahfasiha@gmail.com', '$2y$10$x/GnS6WefYSEW.NeVeq4bel8PSLVQQEMJMr5RR/Eiu/2f2gyHjBTO', '0188704189', 'shah alam', 'cust_45.jpg', NULL, NULL, NULL, 1, NULL, NULL, '2025-10-27 14:53:26', '2025-10-27 14:53:51', '2025-10-27 14:57:09'),
(46, 'MOHAMAD FITRI ZAKUAN BIN AZMEE', 'mfitrizakuanazmee@gmail.com', '$2y$10$noLEKo7hYLyBcW159eYlkuK3f7UWArzDcwMRgMU5s4kg20sECLKZm', '0192005679', NULL, 'cust_46.png', NULL, NULL, NULL, 1, NULL, NULL, '2025-10-28 14:00:37', '2025-10-28 14:02:11', '2025-10-28 14:11:16');

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
(4, 'MUHAMMAD SHAMEEL BIN SHAMSUL ADZMI', 'Shameel681@gmail.com', 'sedap jugak ye', '2025-08-17 19:32:31'),
(5, 'MUHAMMAD FIKRI BIN MAWARDI', 'toonpow43@gmail.com', 'sedapnyooooooooooooo', '2025-08-24 05:33:18'),
(7, 'FIKRI SAPUTRA', 'justpiki123@gmail.com', 'Sedapla, saya suka makan satay dekat kedai ni. Murah dan balance rasanya. Terima kasih.', '2025-10-26 17:37:12'),
(8, 'asiah', 'nuraisyahfasiha@gmail.com', 'Sedapnya sate ayam', '2025-10-27 14:58:21'),
(9, 'MUHAMMAD SUHAIMI BIN MOHD SHAM', 'samshark411@gmail.com', 'Saya recommend tempat ni', '2025-10-29 17:28:37');

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
(26, 'fikri mawardi', 'fikri@gmail.com', 'saya tak suka makanan sini', '2025-08-17 18:23:41'),
(28, 'shameel', 'shameel@hotmail.com', 'makanan 10/10', '2025-08-17 19:23:15'),
(29, 'MUHAMMAD ZAKUAN', 'mfitrizakuan@gmail.com', 'MAKANAN RASA BIASA BIASA JE', '2025-08-17 19:32:03'),
(31, 'fitrizakuanazmee', 'm.fitreezakuanazmee@gmail.com', 'syedap', '2025-09-07 09:56:33'),
(42, 'Fikri Supriaman', 'muhammadfikrimawardi123@gmail.com', 'Saya dengar dari kawan saya kedai ni sedap, saya akan datang kalau ada kesempatan.', '2025-10-26 17:39:40'),
(43, 'Deepak Parcha', 'parchad78@gmail.com', 'Hi,\r\n\r\n\r\nHope you’re doing well!\r\n \r\nI’d love to help you with a fresh, modern, and high-performing website — whether you want to redesign your existing one or build a new site from scratch.\r\nWe work across all major platforms like Shopify, WordPress, Wix, Squarespace, and more.Could you please share your current website link (if any) and a reference website you like?\r\n\r\nThat’ll help me share layout ideas, design suggestions, and an estimated timeline.\r\n\r\nLooking forward to your reply!\r\n\r\nBest\r\nDeepak', '2025-10-27 07:22:47'),
(44, 'Andrewmic', 'no.reply.LucMichel@gmail.com', 'Salutations! sataykajanguncleujang.com \r\n \r\nDid you know that it is possible to send appeal in a compliant manner? \r\nWhen such proposals are sent, no personal data is used, and messages are sent to specially designed forms to receive messages and appeals. Not thought of as spam, messages sent through Feedback Forms are considered important. \r\nWe provide you with the opportunity to test our service free of charge. \r\nOur service will take care of sending up to 50,000 messages for you. \r\n \r\nThe cost of sending one million messages is $59. \r\n \r\nThis offer is automatically generated. \r\n \r\nContact us. \r\nTelegram - https://t.me/FeedbackFormEU \r\nWhatsApp - +375259112693 \r\nWhatsApp  https://wa.me/+375259112693 \r\nWe only use chat for communication.', '2025-10-30 01:07:08');

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
('F01', 'Satay Ayam', 1.30, 'Ayam diperap rempah rahsia, memanggang harum semerbak', '../image/1758781835_satay ayam.png', 'Main Dish', '2025-09-21 09:36:59', '2025-09-25 06:30:35'),
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
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('Pending','Paid','Cancelled') DEFAULT 'Pending',
  `bill_code` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `order_status` enum('Processing','Completed','Cancelled') DEFAULT 'Processing',
  `updated_at` datetime DEFAULT NULL,
  `receipt_sent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `customer_name`, `order_date`, `total_amount`, `payment_status`, `bill_code`, `transaction_id`, `order_status`, `updated_at`, `receipt_sent`) VALUES
(76, 37, NULL, '2025-10-26 22:07:32', 10.00, 'Paid', 'xbe7bqtf', 'TP2510272465440272', 'Processing', NULL, 0),
(77, 37, NULL, '2025-10-26 22:11:18', 19.50, 'Pending', NULL, NULL, 'Completed', '2025-10-27 05:08:42', 0),
(79, 37, NULL, '2025-10-26 22:28:31', 24.00, 'Paid', 'xj33bo6o', 'TP2510274312085780', 'Completed', '2025-10-27 05:08:57', 0),
(80, 37, NULL, '2025-10-26 22:41:43', 49.50, 'Paid', '94lzwmtt', 'TP2510274286720673', 'Completed', '2025-10-27 03:34:35', 0),
(81, 33, NULL, '2025-10-27 03:16:24', 6.00, 'Paid', '9uucvffc', 'TP2510271242942560', 'Completed', '2025-10-27 03:34:31', 0),
(82, 33, NULL, '2025-10-27 05:09:58', 12.00, 'Paid', 'bek7mccn', 'TP2510272063697694', 'Completed', '2025-10-27 05:11:19', 0),
(83, 37, NULL, '2025-10-27 05:24:57', 36.00, 'Paid', 'ccrxjeh9', 'TP2510272323498895', 'Completed', '2025-10-27 05:28:29', 0),
(84, 45, NULL, '2025-10-27 14:54:53', 10.00, 'Paid', 'zgbzgo75', 'TP2510272802924134', 'Processing', NULL, 0),
(85, 44, NULL, '2025-10-27 14:54:54', 41.50, 'Paid', 'vo4w8nco', 'TP2510271266108968', 'Processing', NULL, 0),
(86, 44, NULL, '2025-10-27 14:57:24', 6.50, 'Pending', '1et0bkj2', NULL, '', NULL, 0),
(87, 46, NULL, '2025-10-28 14:03:37', 37.50, 'Paid', 'gh4yqqaz', 'TP2510280768443024', 'Processing', NULL, 0),
(88, 40, NULL, '2025-10-29 17:20:50', 14.50, 'Paid', 'apvoccmw', 'TP2510302540257084', 'Processing', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `food_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_each` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `food_id`, `quantity`, `price_each`) VALUES
(110, 76, 'F04', 5, 2.00),
(111, 77, 'F01', 15, 1.30),
(113, 79, 'F03', 20, 1.20),
(114, 80, 'F04', 15, 2.00),
(115, 80, 'F03', 10, 1.20),
(116, 80, 'S02', 5, 1.50),
(117, 81, 'F03', 5, 1.20),
(118, 82, 'F04', 6, 2.00),
(119, 83, 'F04', 7, 2.00),
(120, 83, 'F03', 5, 1.20),
(121, 83, 'F02', 5, 1.20),
(122, 83, 'F01', 5, 1.30),
(123, 83, 'S02', 1, 1.50),
(124, 83, 'S01', 1, 2.00),
(125, 84, 'F01', 5, 1.30),
(126, 84, 'S01', 1, 2.00),
(127, 84, 'S02', 1, 1.50),
(128, 85, 'F02', 10, 1.20),
(129, 85, 'F04', 10, 2.00),
(130, 85, 'S02', 5, 1.50),
(131, 85, 'S01', 1, 2.00),
(132, 86, 'F01', 5, 1.30),
(133, 87, 'F04', 15, 2.00),
(134, 87, 'S02', 5, 1.50),
(135, 88, 'F01', 5, 1.30),
(136, 88, 'F02', 5, 1.20),
(137, 88, 'S01', 1, 2.00);

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
  `last_logged_in` timestamp NULL DEFAULT NULL,
  `staff_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `name`, `email`, `password`, `phone_no`, `address`, `reset_token`, `reset_expires`, `created_at`, `last_logged_in`, `staff_image`) VALUES
(104, 'FIKRI MAWARDI', 'justpiki123@gmail.com', '$2y$10$D/jUdtrnz5t8D0.ZOcu.r.Hmo3nleiPfx5lLbpyBVgWVTfLk9VOfC', '01162226128', 'AG-2, JALAN DESA KENANGA 2, TAMAN DESA KENANGA', NULL, NULL, '2025-09-21 13:15:06', NULL, '104_1761459354.jpg'),
(105, 'Ammar Zafri', 'toonpow3@gmail.com', '$2y$10$7LoMlaEaApRxH8TY3lHzM.ZF2lKxlT2oBGu6pbhEcKmRsfgXfl5jK', '01162226128', 'AG-76, JALAN DESA KENANGA 2, TAMAN DESA KENANGA', NULL, NULL, '2025-09-25 06:25:56', NULL, NULL),
(106, 'shameel', 'shameel681@gmail.com', '$2y$10$GEJ9UCGVgq.kDlnNKeZr7.Miy85UX/WY6fiX3SM4fUp4RmTKf.Pe6', '01110084626', 'buloh', NULL, NULL, '2025-09-25 07:24:54', NULL, NULL),
(107, 'MUHAMMAD SUHAIMI BIN MOHD SHAM', 'm.suhaimipro@gmail.com', '$2y$10$/.FLX.Mfqd5s7JeMPzjgvOAdhkFVGXREWd4MMMJWogaOryVdkF2TS', '01113277665', 'DESA SAUJANA LANGAT', NULL, NULL, '2025-10-27 05:02:43', NULL, '107_1761757613.png'),
(108, 'MUHAMMAD ANAS IZZUDIN BIN MUAMAR', 'anasizzuddin@graduate.utm.my', '$2y$10$AthRTRHg5kmZKS/hWkYBRey5TP0j43hFAtpqrT4ZPyp8e5URiBg0y', '0102045904', 'Wangsa Maju', NULL, NULL, '2025-10-27 05:04:01', NULL, NULL),
(109, 'ZUHAIKAL AIMAN BIN ZAILAN', 'zuhaikal566@gmail.com', '$2y$10$IeEF1U663XtOYqprGEmVIOb2Rj.cYQIiLipRZhpm0DUu1MMkuCkG6', '01133114674', 'BATU PAHAT', NULL, NULL, '2025-10-27 05:05:03', NULL, NULL);

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
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_orders_customer` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `food_id` (`food_id`);

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
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `feedback_customer`
--
ALTER TABLE `feedback_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `feedback_guest`
--
ALTER TABLE `feedback_guest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`food_id`) REFERENCES `menu` (`food_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
