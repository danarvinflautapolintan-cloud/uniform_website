-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2026 at 02:33 PM
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
-- Database: `uniform_exchange`
--

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `size` varchar(5) NOT NULL,
  `item_condition` varchar(20) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `photo` varchar(100) NOT NULL,
  `status` enum('pending','active','reserved','sold','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`id`, `seller_id`, `name`, `description`, `size`, `item_condition`, `price`, `photo`, `status`, `created_at`) VALUES
(1, 3, 'Type A uniform', '', 'M', 'Good', 150.00, '1792654446_2c2fda96779265234703.jpg', 'sold', '2026-10-22 07:34:06');

-- --------------------------------------------------------

--
-- Table structure for table `listing_requests`
--

CREATE TABLE `listing_requests` (
  `id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listing_requests`
--

INSERT INTO `listing_requests` (`id`, `listing_id`, `buyer_id`, `status`, `created_at`) VALUES
(1, 1, 2, 'approved', '2026-10-22 07:37:57');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','approved','rejected','claimed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `user_id`, `size_id`, `price`, `status`, `created_at`) VALUES
(1, 1, 13, 0.00, 'pending', '2026-10-19 07:30:56'),
(2, 1, 9, 0.00, 'rejected', '2026-10-19 07:31:08'),
(3, 1, 21, 250.00, 'rejected', '2026-10-19 07:32:46'),
(4, 1, 1, 0.00, 'claimed', '2026-10-19 08:48:08'),
(5, 2, 13, 0.00, 'claimed', '2026-10-19 08:49:12'),
(7, 2, 14, 0.00, 'approved', '2026-10-19 08:57:52'),
(8, 2, 20, 250.00, 'approved', '2026-10-19 08:58:48'),
(9, 2, 24, 250.00, 'claimed', '2026-10-19 09:01:12'),
(10, 2, 9, 0.00, 'approved', '2026-10-19 09:03:49'),
(11, 3, 20, 250.00, 'approved', '2026-10-19 09:18:44'),
(12, 3, 8, 0.00, 'claimed', '2026-10-19 09:36:47'),
(13, 2, 21, 250.00, 'claimed', '2026-10-19 09:38:50');

-- --------------------------------------------------------

--
-- Table structure for table `uniforms`
--

CREATE TABLE `uniforms` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `date_acquired` date DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `photo` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uniforms`
--

INSERT INTO `uniforms` (`id`, `name`, `description`, `date_acquired`, `price`, `photo`, `created_at`) VALUES
(1, 'CET Jersey 2026', '“CET Jersey 2026 (T-shirt, NBA Cut & Shorts)', '2026-10-19', 0.00, '1792394770_3701aa2028dd169b883d.jpg', '2026-10-19 07:26:10'),
(2, 'Green pants', 'TAU Uniform Pants (Green)', '2026-10-19', 0.00, '1792394834_fe387e61e6938399953a.jpg', '2026-10-19 07:27:14'),
(3, 'TAU Green Polo', 'TAU Polo Shirt', '2026-10-19', 0.00, '1792394894_ad799dcfc424c9aa3114.jpg', '2026-10-19 07:28:14'),
(4, 'Cas', 'white', '2026-10-19', 250.00, '1792395139_c4251cd3d216d476309f.jpg', '2026-10-19 07:32:19');

-- --------------------------------------------------------

--
-- Table structure for table `uniform_sizes`
--

CREATE TABLE `uniform_sizes` (
  `id` int(11) NOT NULL,
  `uniform_id` int(11) NOT NULL,
  `size` varchar(5) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uniform_sizes`
--

INSERT INTO `uniform_sizes` (`id`, `uniform_id`, `size`, `stock`, `price`) VALUES
(1, 1, 'XS', 0, 0.00),
(2, 1, 'S', 1, 0.00),
(3, 1, 'M', 1, 0.00),
(4, 1, 'L', 1, 0.00),
(5, 1, 'XL', 1, 0.00),
(6, 1, 'XXL', 1, 0.00),
(7, 2, 'XS', 0, 0.00),
(8, 2, 'S', 1, 0.00),
(9, 2, 'M', 2, 0.00),
(10, 2, 'L', 0, 0.00),
(11, 2, 'XL', 1, 0.00),
(12, 2, 'XXL', 1, 0.00),
(13, 3, 'XS', 0, 0.00),
(14, 3, 'S', 0, 0.00),
(15, 3, 'M', 0, 0.00),
(16, 3, 'L', 0, 0.00),
(17, 3, 'XL', 0, 0.00),
(18, 3, 'XXL', 0, 0.00),
(19, 4, 'XS', 0, 250.00),
(20, 4, 'S', 0, 250.00),
(21, 4, 'M', 0, 250.00),
(22, 4, 'L', 0, 250.00),
(23, 4, 'XL', 0, 250.00),
(24, 4, 'XXL', 1, 250.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `student_no` varchar(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('student','admin') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `student_no`, `name`, `password_hash`, `role`, `created_at`) VALUES
(1, 'admin', 'Administrator', '$2y$10$sTHTWQVmOYqCIPCFCmnpi.rQ8KOkkoZ10G0WF/2VouZkS/Hi0qaTW', 'admin', '2026-10-19 07:02:10'),
(2, '2022100120', 'Accel', '$2y$10$3f3bh2vhcYloym5yJXIXju.izfNMQmyOO6PkiXQ8GzqMeg02N65P6', 'student', '2026-10-19 07:04:54'),
(3, '2022100121', 'Diosdado', '$2y$10$v3Y2CJDKooCiokWleZ/DjO6RnjkjrbEcdbefE.YTDMk6IfarTUl3S', 'student', '2026-10-19 09:18:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `listing_requests`
--
ALTER TABLE `listing_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `listing_id` (`listing_id`,`buyer_id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `uniforms`
--
ALTER TABLE `uniforms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uniform_sizes`
--
ALTER TABLE `uniform_sizes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniform_id` (`uniform_id`,`size`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_no` (`student_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `listing_requests`
--
ALTER TABLE `listing_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `uniforms`
--
ALTER TABLE `uniforms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `uniform_sizes`
--
ALTER TABLE `uniform_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `listing_requests`
--
ALTER TABLE `listing_requests`
  ADD CONSTRAINT `listing_requests_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `listing_requests_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `uniform_sizes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `uniform_sizes`
--
ALTER TABLE `uniform_sizes`
  ADD CONSTRAINT `uniform_sizes_ibfk_1` FOREIGN KEY (`uniform_id`) REFERENCES `uniforms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
