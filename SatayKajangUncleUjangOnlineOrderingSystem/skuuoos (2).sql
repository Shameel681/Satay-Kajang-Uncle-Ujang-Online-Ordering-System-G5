-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2025 at 12:35 PM
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
  `reset_expires` datetime DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `email`, `phone_no`, `address`, `password`, `is_verified`, `verify_token`, `last_login`, `created_at`, `updated_at`, `reset_token`, `reset_expires`, `profile_image`) VALUES
(3, 'MUHAMMAD SHAMEEL', 'shameel681@gmail.com', '01110084626', 'no1. jalan semarak', '$2y$10$J149gfHywEFwTNTZStdDDeF/8bfLsEYp.uANEeycci5j.nXuqvMRG', 1, NULL, '2025-10-12 22:23:40', '2025-09-09 05:43:46', '2025-10-12 22:24:35', NULL, NULL, '3_1760279075.jpg'),
(5, 'Muhammad Fikri Bin Mawardi', 'toonpow3@gmail.com', '0119898256', 'AG-2, JALAN DESA KENANGA 2, TAMAN DESA KENANGA', '$2y$10$ByarGQwCoC2eLLtXq17pQuMLBrPpvXeLssb9QZVnmhEZ0QSFWSUdy', 1, NULL, '2025-10-07 01:02:53', '2025-09-09 06:32:48', '2025-10-07 01:02:53', NULL, NULL, 'capybara.jpg');

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
(19, 'MUHAMMAD SUHAIMI BIN MOHD SHAM', 'm.suhaimipro@gmail.com', '$2y$10$i2Vv1tNp/a1nVGc61hnZbOoIRpasC0Z1mlPFQRTVdRQJ.Bw4QaD4K', '01162226128', 'AG-2, JALAN DESA KENANGA 2, TAMAN DESA KENANGA', NULL, NULL, NULL, NULL, 0, '91b93fa6d1fbbd6507b59041144081f4', NULL, '2025-09-19 13:32:35', NULL, '2025-09-25 07:01:01'),
(20, 'MUHAMMAD ANAS IZZUDIN BIN MUAMAR', 'anasizzuddin@graduate.utm.my', '$2y$10$0CEJ4RszQYGHE60cw.YT1uQVYRXvHmx3vkN.eB8Cx0E9qlbaHpnzq', '0102045904', 'wangsa', NULL, NULL, NULL, NULL, 0, '4a37db85f42c739ce2adaa7d1489fcbd', NULL, '2025-09-19 13:32:35', NULL, '2025-09-25 07:02:29'),
(33, 'MUHAMMAD SHAMEEL BIN SHAMSUL ADZMI', 'shameel681@gmail.com', '$2y$10$AexpywrEw4bh.ogj/TizROlEUHR/J.zKpTCSVdsPyqbX9sVqw1LtO', '01110084626', 'buloh', 'cust_33.jpg', NULL, NULL, NULL, 1, NULL, NULL, '2025-09-19 13:32:35', '2025-10-12 14:20:51', '2025-10-12 14:20:51'),
(37, 'FIKRI MAWARDI', 'toonpow43@gmail.com', '$2y$10$zOnBDUa/3aT8G4pEzCdbYOi6CVKpeQ2SUjEgUg7VrFMkO/a25zBiq', '0116222612', 'AG-2, JALAN DESA KENANGA 2, TAMAN DESA KENANGA', 'cust_37.jpg', NULL, NULL, NULL, 1, NULL, 'cust_37.jpg', '2025-09-22 22:59:57', '2025-10-01 07:21:58', '2025-10-01 07:21:58');

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
(26, 'fikri mawardi', 'fikri@gmail.com', 'saya tak suka makanan sini', '2025-08-17 18:23:41'),
(27, 'shameel clone', 'shameeldoubleganger@gmail.com', 'makanan ini tidak sedap', '2025-08-17 19:20:54'),
(28, 'shameel', 'shameel@hotmail.com', 'makanan 10/10', '2025-08-17 19:23:15'),
(29, 'MUHAMMAD ZAKUAN', 'mfitrizakuan@gmail.com', 'MAKANAN RASA BIASA BIASA JE', '2025-08-17 19:32:03'),
(31, 'fitrizakuanazmee', 'm.fitreezakuanazmee@gmail.com', 'syedap', '2025-09-07 09:56:33'),
(33, 'Muhammad Fikri Bin Mawardi-', 'toonpow43@gmail.com', 'Sedap siot', '2025-09-30 18:15:29');

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
  `order_status` enum('Processing','Completed','Cancelled') DEFAULT 'Processing',
  `receipt_sent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(104, 'FIKRI MAWARDI', 'justpiki123@gmail.com', '$2y$10$D/jUdtrnz5t8D0.ZOcu.r.Hmo3nleiPfx5lLbpyBVgWVTfLk9VOfC', '01162226128', 'AG-2, JALAN DESA KENANGA 2, TAMAN DESA KENANGA', NULL, NULL, '2025-09-21 13:15:06', NULL, 'staff_104.JPG'),
(105, 'Ammar Zafri', 'toonpow3@gmail.com', '$2y$10$7LoMlaEaApRxH8TY3lHzM.ZF2lKxlT2oBGu6pbhEcKmRsfgXfl5jK', '01162226128', 'AG-76, JALAN DESA KENANGA 2, TAMAN DESA KENANGA', NULL, NULL, '2025-09-25 06:25:56', NULL, NULL),
(106, 'shameel', 'shameel681@gmail.com', '$2y$10$GEJ9UCGVgq.kDlnNKeZr7.Miy85UX/WY6fiX3SM4fUp4RmTKf.Pe6', '01110084626', 'buloh', NULL, NULL, '2025-09-25 07:24:54', NULL, NULL);

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
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `feedback_customer`
--
ALTER TABLE `feedback_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback_guest`
--
ALTER TABLE `feedback_guest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

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
