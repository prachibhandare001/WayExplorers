-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 18, 2025 at 08:01 PM
-- Server version: 8.0.43-0ubuntu0.22.04.1
-- PHP Version: 8.1.2-1ubuntu2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tours_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `blog_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int NOT NULL,
  `package_id` int NOT NULL,
  `user_id` int NOT NULL,
  `travel_date` date NOT NULL,
  `booking_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `package_id`, `user_id`, `travel_date`, `booking_date`) VALUES
(1, 1, 1, '2025-09-20', '2025-09-13 18:23:06'),
(2, 1, 1, '2025-09-25', '2025-09-13 18:45:34'),
(3, 1, 2, '2025-09-17', '2025-09-13 20:54:46');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `user_id` int NOT NULL,
  `method` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour_package`
--

CREATE TABLE `tour_package` (
  `package_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `location` varchar(150) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` varchar(100) NOT NULL,
  `description` text,
  `images` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tour_package`
--

INSERT INTO `tour_package` (`package_id`, `title`, `location`, `image`, `price`, `duration`, `description`, `images`) VALUES
(1, 'demo 1', 'kashmir', NULL, '15000.00', '4 days, 3 nights', 'umm umm umm umm', '[\"https://www.maharanacab.com/wp-content/uploads/2018/09/goa-tour-package-cheap.jpg\"]'),
(2, 'mumbai darshan', 'mumbai', NULL, '10000.00', '2 days,1 night', '', '[\"https://tse4.mm.bing.net/th/id/OIP.BDwCR6coT6XuWPkBVzF3xQHaFE?r=0&rs=1&pid=ImgDetMain&o=7&rm=3\", \"https://mumbaidarshan.pro/wp-content/uploads/2024/12/mumbai-darshan-bus-tour.png\"]'),
(3, 'kashmir trip', 'kashmir', NULL, '30000.00', '5 days, 4 nights', 'fnoiefn nwioefoW DNQWD0W', '[\"https://tse2.mm.bing.net/th/id/OIP.b0zmtF9wdSODujCIYCH6yQHaD4?r=0&rs=1&pid=ImgDetMain&o=7&rm=3\"]');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `profile_pic` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `phone`, `password`, `profile_photo`, `created_at`, `profile_pic`, `is_admin`) VALUES
(1, 'Prachi Bhandare', 'prachibhandare001@gmail.com', '09321763411', '$2y$10$/ZmKSZDzBm1MC.zwJFoo0OjmJGOCqZICGYxEV13.jhwGZwl3UeRDG', NULL, '2025-09-13 14:42:35', NULL, 1),
(2, 'ohyeahom', 'omdalvi4205@gmail.com', '8356979194', '$2y$10$ZcP8XFpmPB7IqcH09L0AretrRsYOGPeeoac1QMqakmxuXBgaicjvO', NULL, '2025-09-13 19:24:31', NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`blog_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tour_package`
--
ALTER TABLE `tour_package`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `blog_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tour_package`
--
ALTER TABLE `tour_package`
  MODIFY `package_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog`
--
ALTER TABLE `blog`
  ADD CONSTRAINT `blog_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `tour_package` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
