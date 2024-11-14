-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2024 at 12:46 PM
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
-- Database: `project`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `password`) VALUES
(1, 'admin', '9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `product_id` int(100) NOT NULL,
  `quantity` int(10) NOT NULL DEFAULT 1,
  `variant_id` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variants_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `method` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `placed_on` datetime DEFAULT current_timestamp(),
  `payment_status` enum('pending','paid','failed','refunded','completed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `total_products` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `variants_id`, `number`, `method`, `address`, `total_price`, `placed_on`, `payment_status`, `created_at`, `updated_at`, `name`, `email`, `total_products`) VALUES
(1, 11, 12, 21, 2, 'Credit Card', '123 Main St, City, Country', 199.99, '2024-11-14 15:30:00', 'completed', '2024-11-14 09:15:00', '2024-11-14 09:51:18', 'John Doe', 'johndoe@example.com', 3),
(4, 11, 13, 24, 3, 'Credit Card', '321 Pine St, City, Country', 150.00, '2024-11-11 14:45:00', 'pending', '2024-11-11 08:45:00', '2024-11-14 10:16:17', 'Bob Green', 'bobgreen@example.com', 2),
(8, 11, 12, 22, 6, 'Debit Card', '246 Fir St, Countryside, Country', 499.94, '2024-11-07 18:00:00', 'refunded', '2024-11-07 12:15:00', '2024-11-14 09:48:20', 'Fiona Blue', 'fionablue@example.com', 3),
(9, 11, 13, 23, 7, 'Credit Card', '135 Cherry St, Downtown, Country', 799.93, '2024-11-06 11:30:00', 'pending', '2024-11-06 05:15:00', '2024-11-06 05:15:00', 'George Red', 'georgered@example.com', 2),
(14, 11, 12, 22, 2147483647, 'Cash on Delivery', 'jitpur, bhaktapur, bagmati', 42.00, '2024-11-14 12:41:35', 'pending', '2024-11-14 06:56:35', '2024-11-14 06:56:35', 'krishna', 'krishna@gmail.com', 2);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `gender` enum('male','female','child') NOT NULL DEFAULT 'male',
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `weight` decimal(10,2) NOT NULL DEFAULT 0.00,
  `slug` varchar(255) NOT NULL,
  `meta_description` text DEFAULT NULL,
  `seo_keywords` text DEFAULT NULL,
  `tags` text NOT NULL,
  `image` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `popularity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `gender`, `stock_quantity`, `weight`, `slug`, `meta_description`, `seo_keywords`, `tags`, `image`, `created_at`, `updated_at`, `popularity`) VALUES
(12, 'sadsad', 'hat', 'female', 2, 250.00, 'dsadsad', 'asdsad', 'sadsadasd', 'sdsadasd', 'Gorkha-Beer.jpg', '2024-11-14 08:50:16', '2024-11-14 10:16:23', 2),
(13, 'sdasd', 'boots', 'female', 21, 23.00, '213', '213', '213', '', 'Gorkha_LN.jpg', '2024-11-14 08:50:41', '2024-11-14 10:16:23', 2);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `size`, `color`, `price`, `stock_quantity`, `created_at`, `updated_at`) VALUES
(21, 12, 'xs', 'pink', 250.00, 20, '2024-11-14 08:50:16', '2024-11-14 08:50:16'),
(22, 12, 'xs', 'blue', 21.00, 23, '2024-11-14 08:50:16', '2024-11-14 08:50:16'),
(23, 13, 'xs', 'red', 12.00, 21, '2024-11-14 08:50:41', '2024-11-14 08:50:41'),
(24, 13, 'L', 'red', 250.00, 250, '2024-11-14 08:58:47', '2024-11-14 08:58:47'),
(25, 13, 'sad', 'sadsa', 123.00, 13, '2024-11-14 08:59:16', '2024-11-14 08:59:16'),
(26, 13, 'sad', 'sadsa', 123.00, 13, '2024-11-14 09:00:12', '2024-11-14 09:00:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `number` varchar(10) NOT NULL,
  `password` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `number`, `password`, `address`, `created_at`, `updated_at`) VALUES
(11, 'krishna', 'krishna@gmail.com', '9869078280', '8e3ee9d5d3c305f9525b9cb6d284c5236c15c503', 'jitpur, bhaktapur, bagmati', '2024-11-12 15:30:22', '2024-11-12 17:07:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`),
  ADD KEY `cart_user_fk` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_variants_id` (`variants_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_variant_fk` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_variants_id` FOREIGN KEY (`variants_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
