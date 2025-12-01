-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2025 at 08:47 AM
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
-- Database: `cycle_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `line1` varchar(255) NOT NULL,
  `city` varchar(120) NOT NULL,
  `state` varchar(120) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `line1`, `city`, `state`, `postal_code`, `is_default`, `created_at`) VALUES
(1, 2, 'lokhandwala complex Andheri', 'Mumbai', 'MH', '400001', 1, '2025-09-07 13:13:32'),
(2, 4, '221B Baker Street', 'valsad', 'gujarat', '396001', 1, '2025-09-07 18:07:42');

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `source` enum('user','supplier') NOT NULL DEFAULT 'user',
  `supplier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`id`, `order_id`, `amount`, `created_at`, `source`, `supplier_id`) VALUES
(1, 0, 2000.00, '2025-09-14 19:16:47', 'supplier', 1),
(2, 0, 3000.00, '2025-09-14 19:16:47', 'user', 2),
(3, 0, 4000.00, '2025-09-14 19:16:47', '', 3),
(4, 19, 8598.00, '2025-09-14 19:37:13', 'user', NULL),
(5, 20, 20297.00, '2025-09-14 19:39:57', 'user', NULL),
(6, 6, 5000.00, '2025-09-14 16:35:18', 'supplier', 3),
(7, 13, 28000.00, '2025-09-14 21:04:03', 'supplier', 3),
(8, 23, 17298.00, '2025-09-15 00:39:43', 'user', NULL),
(9, 19, 20000.00, '2025-09-14 21:14:33', 'supplier', 3),
(10, 19, 20000.00, '2025-09-14 21:19:19', 'supplier', 3),
(11, 24, 10299.00, '2025-09-15 09:25:16', 'user', NULL),
(12, 26, 27000.00, '2025-09-15 06:01:24', 'supplier', 3),
(13, 29, 27000.00, '2025-09-15 06:14:32', 'supplier', 3),
(14, 101, 14000.00, '2025-09-15 08:48:36', 'supplier', 3),
(15, 25, 20000.00, '2025-09-15 13:25:56', 'user', NULL),
(16, 26, 10299.00, '2025-09-17 19:10:19', 'user', NULL),
(17, 25, 18000.00, '2025-09-20 07:34:54', 'supplier', 3),
(18, 26, 17500.00, '2025-09-20 09:28:51', 'supplier', 3);

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_at`) VALUES
(1, 2, '2025-09-07 13:13:32'),
(2, 4, '2025-09-07 18:07:19');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(3, 'Hybrid'),
(4, 'Kids'),
(1, 'Mountain'),
(2, 'Road');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `product_id` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`product_id`, `stock`) VALUES
(1, 11),
(2, 8),
(3, 20),
(4, 4),
(45, 5),
(46, 5),
(47, 5),
(48, 5),
(49, 5),
(50, 5),
(51, 5),
(52, 5),
(53, 5),
(54, 5),
(55, 7),
(56, 5),
(57, 5),
(58, 5),
(59, 5),
(60, 5),
(61, 5),
(62, 3),
(63, 4),
(64, 5),
(65, 5),
(66, 4),
(67, 5),
(68, 5),
(69, 5),
(70, 15),
(71, 4),
(72, 4),
(73, 5),
(74, 5),
(75, 5),
(76, 5),
(77, 5),
(78, 5),
(79, 4),
(80, 2),
(81, 3),
(82, 4);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('unpaid','paid','cod','refund') NOT NULL DEFAULT 'unpaid',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_date`, `user_id`, `address_id`, `status`, `subtotal`, `shipping`, `total`, `payment_status`, `created_at`) VALUES
(1, '2025-09-13 15:50:29', 2, 1, 'cancelled', 18999.00, 299.00, 19298.00, 'unpaid', '2025-09-07 15:08:16'),
(2, '2025-09-13 15:50:29', 2, 1, 'cancelled', 6999.00, 299.00, 7298.00, 'unpaid', '2025-09-07 15:18:49'),
(3, '2025-09-13 15:50:29', 2, 1, 'delivered', 6999.00, 299.00, 7298.00, 'unpaid', '2025-09-07 15:20:33'),
(4, '2025-09-13 15:50:29', 4, 2, 'delivered', 1000.00, 299.00, 1299.00, 'paid', '2025-09-07 18:07:42'),
(5, '2025-09-13 15:50:29', 4, 2, 'cancelled', 2000.00, 299.00, 2299.00, 'unpaid', '2025-09-08 11:22:55'),
(6, '2025-09-13 15:50:29', 2, 1, 'delivered', 6999.00, 299.00, 7298.00, 'cod', '2025-09-09 15:58:12'),
(7, '2025-09-13 15:50:29', 4, 2, 'shipped', 10000.00, 299.00, 10299.00, 'paid', '2025-09-11 18:36:46'),
(8, '2025-09-13 15:50:29', 4, 2, 'shipped', 1000.00, 299.00, 1299.00, 'paid', '2025-09-11 19:18:36'),
(9, '2025-09-13 15:50:29', 2, 1, 'confirmed', 10000.00, 299.00, 10299.00, 'cod', '2025-09-13 14:34:53'),
(10, '2025-09-13 15:50:29', 2, 1, 'confirmed', 10000.00, 299.00, 10299.00, 'cod', '2025-09-13 14:56:27'),
(11, '2025-09-14 16:33:22', 2, 1, 'confirmed', 10000.00, 299.00, 10299.00, 'cod', '2025-09-14 16:33:22'),
(12, '2025-09-14 16:41:53', 2, 1, 'confirmed', 10000.00, 299.00, 10299.00, 'cod', '2025-09-14 16:41:53'),
(13, '2025-09-14 16:51:51', 2, 1, 'confirmed', 8299.00, 299.00, 8598.00, 'cod', '2025-09-14 16:51:51'),
(14, '2025-09-14 16:54:32', 2, 1, 'confirmed', 6799.00, 299.00, 7098.00, 'cod', '2025-09-14 16:54:32'),
(15, '2025-09-14 16:58:52', 2, 1, 'confirmed', 62999.00, 0.00, 62999.00, 'cod', '2025-09-14 16:58:52'),
(16, '2025-09-14 17:02:17', 2, 1, 'confirmed', 22999.00, 0.00, 22999.00, 'cod', '2025-09-14 17:02:17'),
(17, '2025-09-14 17:04:13', 2, 1, 'confirmed', 8299.00, 299.00, 8598.00, 'cod', '2025-09-14 17:04:13'),
(18, '2025-09-14 17:05:39', 2, 1, 'confirmed', 62999.00, 0.00, 62999.00, 'paid', '2025-09-14 17:05:39'),
(19, '2025-09-14 19:37:07', 2, 1, 'confirmed', 8299.00, 299.00, 8598.00, 'paid', '2025-09-14 19:37:07'),
(20, '2025-09-14 19:39:39', 2, 1, 'shipped', 19998.00, 299.00, 20297.00, 'paid', '2025-09-14 19:39:39'),
(21, '2025-09-15 00:32:16', 2, 1, 'confirmed', 32999.00, 0.00, 32999.00, 'cod', '2025-09-15 00:32:16'),
(22, '2025-09-15 00:36:50', 2, 1, 'confirmed', 6499.00, 299.00, 6798.00, 'cod', '2025-09-15 00:36:50'),
(23, '2025-09-15 00:39:31', 2, 1, 'confirmed', 16999.00, 299.00, 17298.00, 'paid', '2025-09-15 00:39:31'),
(24, '2025-09-15 09:25:07', 2, 1, 'delivered', 10000.00, 299.00, 10299.00, 'paid', '2025-09-15 09:25:07'),
(25, '2025-09-15 13:25:35', 2, 1, 'confirmed', 20000.00, 0.00, 20000.00, 'paid', '2025-09-15 13:25:35'),
(26, '2025-09-17 19:10:15', 2, 1, 'confirmed', 10000.00, 299.00, 10299.00, 'paid', '2025-09-17 19:10:15'),
(27, '2025-09-20 12:01:44', 2, 1, 'delivered', 10000.00, 299.00, 10299.00, 'cod', '2025-09-20 12:01:44'),
(28, '2025-09-20 12:07:43', 2, 1, 'delivered', 10000.00, 299.00, 10299.00, 'cod', '2025-09-20 12:07:43');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `price`, `quantity`) VALUES
(1, 1, 1, 18999.00, 1),
(2, 2, 3, 6999.00, 1),
(3, 3, 3, 6999.00, 1),
(4, 4, 4, 1000.00, 1),
(5, 5, 4, 1000.00, 2),
(6, 6, 3, 6999.00, 1),
(7, 7, 82, 10000.00, 1),
(8, 8, 4, 1000.00, 1),
(9, 9, 82, 10000.00, 1),
(10, 10, 82, 10000.00, 1),
(11, 11, 82, 10000.00, 1),
(12, 12, 82, 10000.00, 1),
(13, 13, 80, 8299.00, 1),
(14, 14, 79, 6799.00, 1),
(15, 15, 62, 62999.00, 1),
(16, 16, 66, 22999.00, 1),
(17, 17, 80, 8299.00, 1),
(18, 18, 62, 62999.00, 1),
(19, 19, 80, 8299.00, 1),
(20, 20, 78, 9999.00, 2),
(21, 21, 71, 32999.00, 1),
(22, 22, 72, 6499.00, 1),
(23, 23, 63, 16999.00, 1),
(24, 24, 82, 10000.00, 1),
(25, 25, 81, 10000.00, 2),
(26, 26, 81, 10000.00, 1),
(27, 27, 82, 10000.00, 1),
(28, 28, 82, 10000.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_payments`
--

CREATE TABLE `order_payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(32) NOT NULL,
  `reference` varchar(191) DEFAULT NULL,
  `paid_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_payments`
--

INSERT INTO `order_payments` (`id`, `order_id`, `amount`, `method`, `reference`, `paid_at`, `created_at`) VALUES
(1, 7, 10299.00, 'Cash', '', '2025-09-11 15:46:00', '2025-09-11 19:17:07'),
(2, 8, 1299.00, 'Card', '', '2025-09-11 15:54:00', '2025-09-11 19:24:31');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `provider` varchar(40) NOT NULL,
  `provider_ref` varchar(120) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('success','pending','failed') NOT NULL DEFAULT 'success',
  `paid_at` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `provider`, `provider_ref`, `amount`, `status`, `paid_at`, `created_at`) VALUES
(1, 1, 'COD', '', 19298.00, 'pending', '2025-09-07 15:08:20', '2025-09-07 15:08:20'),
(2, 1, 'COD', '', 19298.00, 'pending', '2025-09-07 15:08:31', '2025-09-07 15:08:31'),
(3, 2, 'COD', '', 7298.00, 'pending', '2025-09-07 15:19:01', '2025-09-07 15:19:01'),
(4, 4, 'COD', '', 1299.00, 'pending', '2025-09-07 18:07:56', '2025-09-07 18:07:56'),
(5, 4, 'Card', '', 1299.00, 'success', '2025-09-07 18:08:21', '2025-09-07 18:08:21'),
(6, 5, 'UPI', '', 2299.00, 'success', '2025-09-08 11:23:00', '2025-09-08 11:23:00'),
(7, 6, 'COD', '', 7298.00, 'pending', '2025-09-09 15:58:24', '2025-09-09 15:58:24'),
(8, 5, 'COD', '', 2299.00, 'pending', '2025-09-09 18:20:49', '2025-09-09 18:20:49'),
(9, 7, 'COD', '', 10299.00, 'pending', '2025-09-11 18:36:51', '2025-09-11 18:36:51'),
(10, 8, 'UPI', '', 1299.00, 'success', '2025-09-11 19:18:41', '2025-09-11 19:18:41'),
(11, 9, 'COD', '', 10299.00, 'pending', '2025-09-13 14:34:58', '2025-09-13 14:34:58'),
(12, 10, 'COD', '', 10299.00, 'pending', '2025-09-13 14:56:31', '2025-09-13 14:56:31'),
(13, 11, 'COD', '', 10299.00, 'pending', '2025-09-14 16:33:27', '2025-09-14 16:33:27'),
(14, 12, 'COD', '', 10299.00, 'pending', '2025-09-14 16:41:58', '2025-09-14 16:41:58'),
(15, 13, 'COD', '', 8598.00, 'pending', '2025-09-14 16:51:55', '2025-09-14 16:51:55'),
(16, 14, 'COD', '', 7098.00, 'pending', '2025-09-14 16:54:37', '2025-09-14 16:54:37'),
(17, 15, 'COD', '', 62999.00, 'pending', '2025-09-14 16:58:57', '2025-09-14 16:58:57'),
(18, 16, 'COD', '', 22999.00, 'pending', '2025-09-14 17:02:22', '2025-09-14 17:02:22'),
(19, 17, 'COD', '', 8598.00, 'pending', '2025-09-14 17:04:19', '2025-09-14 17:04:19'),
(20, 18, 'UPI', '5650', 62999.00, 'success', '2025-09-14 17:05:48', '2025-09-14 17:05:48'),
(21, 13, 'COD', '', 8598.00, 'pending', '2025-09-14 19:36:50', '2025-09-14 19:36:50'),
(22, 19, 'UPI', '', 8598.00, 'success', '2025-09-14 19:37:13', '2025-09-14 19:37:13'),
(23, 20, 'UPI', '5820', 20297.00, 'success', '2025-09-14 19:39:57', '2025-09-14 19:39:57'),
(24, 21, 'COD', '', 32999.00, 'pending', '2025-09-15 00:32:22', '2025-09-15 00:32:22'),
(25, 22, 'COD', '', 6798.00, 'pending', '2025-09-15 00:36:56', '2025-09-15 00:36:56'),
(26, 23, 'Card', '', 17298.00, 'success', '2025-09-15 00:39:43', '2025-09-15 00:39:43'),
(27, 24, 'UPI', '', 10299.00, 'success', '2025-09-15 09:25:16', '2025-09-15 09:25:16'),
(28, 25, 'UPI', '25456465', 20000.00, 'success', '2025-09-15 13:25:56', '2025-09-15 13:25:56'),
(29, 26, 'UPI', '', 10299.00, 'success', '2025-09-17 19:10:19', '2025-09-17 19:10:19'),
(30, 27, 'COD', '', 10299.00, 'pending', '2025-09-20 12:01:52', '2025-09-20 12:01:52'),
(31, 28, 'COD', '', 10299.00, 'pending', '2025-09-20 12:08:20', '2025-09-20 12:08:20');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(160) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `brand` varchar(120) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `supplier_id`, `category_id`, `name`, `slug`, `description`, `brand`, `price`, `thumbnail`, `is_active`, `created_at`) VALUES
(1, 1, 1, 'Trailblazer 500', 'trailblazer-500', 'Alloy frame MTB with 21-speed.', 'TrailX', 18999.00, '/cyclestore/uploads/products/mountain/img1.jpg', 1, '2025-09-07 13:13:32'),
(2, 1, 2, 'Speedster Pro', 'speedster-pro', 'Lightweight road cycle with aero frame.', 'Swift', 25999.00, '/cyclestore/uploads/products/road/img1.jpg', 1, '2025-09-07 13:13:32'),
(3, 1, 4, 'MiniRider 16', 'minirider-16', '16-inch kids cycle with training wheels.', 'FunRide', 6999.00, '/cyclestore/uploads/products/kids/img1.jpg', 1, '2025-09-07 13:13:32'),
(4, 1, 3, 'Clayewoss', '-layewoss-86356', 'wa', 'hyper', 1000.00, '/cyclestore/uploads/products/hybrid/img1.jpg', 1, '2025-09-07 15:51:20'),
(45, 1, 1, 'Trailblazer 200', 'trailblazer-200', 'Alloy frame MTB with mechanical discs.', 'TrailX', 17999.00, '/cyclestore/uploads/products/mountain/img2.jpg', 1, '2025-09-08 14:49:33'),
(46, 1, 1, 'Summit Pro 300', 'summit-pro-300', 'Front suspension, 24-speed gearing.', 'PeakRider', 19999.00, '/cyclestore/uploads/products/mountain/img3.jpg', 1, '2025-09-08 14:49:33'),
(47, 1, 1, 'Summit Pro 400', 'summit-pro-400', 'Hydraulic disc brakes, alloy cockpit.', 'PeakRider', 21999.00, '/cyclestore/uploads/products/mountain/img4.jpg', 1, '2025-09-08 14:49:33'),
(48, 1, 1, 'RockTrail 500', 'rocktrail-500', '29er wheels, trail-ready geometry.', 'RockForge', 24999.00, '/cyclestore/uploads/products/mountain/img5.jpg', 1, '2025-09-08 14:49:33'),
(49, 1, 1, 'RockTrail 600', 'rocktrail-600', 'Air fork, wide-range cassette.', 'RockForge', 27999.00, '/cyclestore/uploads/products/mountain/img6.jpg', 1, '2025-09-08 14:49:33'),
(50, 1, 1, 'Mountain Swift 700', 'mountain-swift-700', 'Lightweight alloy, tubeless-ready rims.', 'Swift', 30999.00, '/cyclestore/uploads/products/mountain/img7.jpg', 1, '2025-09-08 14:49:33'),
(51, 1, 1, 'Mountain Swift 800', 'mountain-swift-800', '1x drivetrain, boost spacing.', 'Swift', 33999.00, '/cyclestore/uploads/products/mountain/img8.jpg', 1, '2025-09-08 14:49:33'),
(52, 1, 1, 'Alpine X9', 'alpine-x9', 'Aggressive trail bike with 120mm travel.', 'AlpineWorks', 36999.00, '/cyclestore/uploads/products/mountain/img9.jpg', 1, '2025-09-08 14:49:33'),
(53, 1, 1, 'Alpine X10', 'alpine-x10', 'Long reach, slack head angle, 1x12.', 'AlpineWorks', 39999.00, '/cyclestore/uploads/products/mountain/img10.jpg', 1, '2025-09-08 14:49:33'),
(54, 1, 2, 'Speedster 200', 'speedster-200', 'Endurance geometry, 2x9 drivetrain.', 'Velocity', 27999.00, '/cyclestore/uploads/products/road/img2.jpg', 1, '2025-09-08 14:49:33'),
(55, 1, 2, 'AeroLite 300', 'aerolite-300', 'Aero tubing, internal routing.', 'AeroTech', 31999.00, '/cyclestore/uploads/products/road/img3.jpg', 1, '2025-09-08 14:49:33'),
(56, 1, 2, 'AeroLite 400', 'aerolite-400', 'Hydraulic discs, 2x10 groupset.', 'AeroTech', 35999.00, '/cyclestore/uploads/products/road/img4.jpg', 1, '2025-09-08 14:49:33'),
(57, 1, 2, 'Road Pro 500', 'road-pro-500', 'Race-ready, compact crankset.', 'RoadMax', 39999.00, '/cyclestore/uploads/products/road/img5.jpg', 1, '2025-09-08 14:49:33'),
(58, 1, 2, 'Road Pro 600', 'road-pro-600', 'Carbon seatpost, tubeless tires.', 'RoadMax', 44999.00, '/cyclestore/uploads/products/road/img6.jpg', 1, '2025-09-08 14:49:33'),
(59, 1, 2, 'Strada 700', 'strada-700', 'Light alloy, 2x11 drivetrain.', 'Strada', 48999.00, '/cyclestore/uploads/products/road/img7.jpg', 1, '2025-09-08 14:49:33'),
(60, 1, 2, 'Strada 800', 'strada-800', 'Aero cockpit, performance wheels.', 'Strada', 52999.00, '/cyclestore/uploads/products/road/img8.jpg', 1, '2025-09-08 14:49:33'),
(61, 1, 2, 'CarbonEdge 900', 'carbonedge-900', 'Carbon frameset, endurance fit.', 'EdgeWorks', 57999.00, '/cyclestore/uploads/products/road/img9.jpg', 1, '2025-09-08 14:49:33'),
(62, 1, 2, 'CarbonEdge 1000', 'carbonedge-1000', 'Aero carbon, 2x12 drivetrain.', 'EdgeWorks', 62999.00, '/cyclestore/uploads/products/road/img10.jpg', 1, '2025-09-08 14:49:33'),
(63, 1, 3, 'CityRide 2.0', 'cityride-2-0', 'Alloy frame, 3x7 gearing.', 'UrbanGo', 16999.00, '/cyclestore/uploads/products/hybrid/img2.jpg', 1, '2025-09-08 14:49:33'),
(64, 1, 3, 'MetroFlex 300', 'metroflex-300', 'Suspension fork, city tires.', 'Metro', 18999.00, '/cyclestore/uploads/products/hybrid/img3.jpg', 1, '2025-09-08 14:49:33'),
(65, 1, 3, 'MetroFlex 400', 'metroflex-400', 'Hydraulic discs, ergonomic grips.', 'Metro', 20999.00, '/cyclestore/uploads/products/hybrid/img4.jpg', 1, '2025-09-08 14:49:33'),
(66, 1, 3, 'Urban Sprint 500', 'urban-sprint-500', 'Fast-rolling 700c wheels.', 'SprintX', 22999.00, '/cyclestore/uploads/products/hybrid/img5.jpg', 1, '2025-09-08 14:49:33'),
(67, 1, 3, 'Urban Sprint 600', 'urban-sprint-600', '1x drivetrain, internal cables.', 'SprintX', 24999.00, '/cyclestore/uploads/products/hybrid/img6.jpg', 1, '2025-09-08 14:49:33'),
(68, 1, 3, 'ComfoRide 700', 'comforide-700', 'Comfort saddle, upright posture.', 'Comforta', 26999.00, '/cyclestore/uploads/products/hybrid/img7.jpg', 1, '2025-09-08 14:49:33'),
(69, 1, 3, 'ComfoRide 800', 'comforide-800', 'Lightweight alloy, rack-ready.', 'Comforta', 28999.00, '/cyclestore/uploads/products/hybrid/img8.jpg', 1, '2025-09-08 14:49:33'),
(70, 1, 3, 'Hybrid Pro 900', 'hybrid-pro-900', 'Hydraulic discs, 2x9 drivetrain.', 'Fusion', 30999.00, '/cyclestore/uploads/products/hybrid/img9.jpg', 1, '2025-09-08 14:49:33'),
(71, 1, 3, 'Hybrid Pro 1000', 'hybrid-pro-1000', 'Gates belt-ready frame design.', 'Fusion', 32999.00, '/cyclestore/uploads/products/hybrid/img10.jpg', 1, '2025-09-08 14:49:33'),
(72, 1, 4, 'MiniRider 14', 'minirider-14', '14-inch lightweight kids bike.', 'FunRide', 6499.00, '/cyclestore/uploads/products/kids/img2.jpg', 1, '2025-09-08 14:49:33'),
(73, 1, 4, 'MiniRider 16', 'minirider-17', '16-inch with chainguard and bell.', 'FunRide', 6999.00, '/cyclestore/uploads/products/kids/img3.jpg', 1, '2025-09-08 14:49:33'),
(74, 1, 4, 'MiniRider 18', 'minirider-18', '18-inch, low standover frame.', 'FunRide', 7499.00, '/cyclestore/uploads/products/kids/img4.jpg', 1, '2025-09-08 14:49:33'),
(75, 1, 4, 'JuniorX 20', 'juniorx-20', '20-inch gears, kid-friendly brakes.', 'KidX', 8499.00, '/cyclestore/uploads/products/kids/img5.jpg', 1, '2025-09-08 14:49:33'),
(76, 1, 4, 'JuniorX 22', 'juniorx-22', '22-inch step-through frame.', 'KidX', 8999.00, '/cyclestore/uploads/products/kids/img6.jpg', 1, '2025-09-08 14:49:33'),
(77, 1, 4, 'StarKid 24', 'starkid-24', '24-inch multi-gear, lightweight.', 'Starlet', 9499.00, '/cyclestore/uploads/products/kids/img7.jpg', 1, '2025-09-08 14:49:33'),
(78, 1, 4, 'StarKid 26', 'starkid-26', '26-inch junior hybrid style.', 'Starlet', 9999.00, '/cyclestore/uploads/products/kids/img8.jpg', 1, '2025-09-08 14:49:33'),
(79, 1, 4, 'Spark 16', 'spark-16', '16-inch vivid colors, safety reflectors.', 'Spark', 6799.00, '/cyclestore/uploads/products/kids/img9.jpg', 1, '2025-09-08 14:49:33'),
(80, 1, 4, 'Spark 20', 'spark-20', '20-inch MTB style for kids.', 'Spark', 8299.00, '/cyclestore/uploads/products/kids/img10.jpg', 1, '2025-09-08 14:49:33'),
(81, 1, 2, 'FireFox', '-ire-ox-c60c8', 'heavy cycle', 'MainAt', 10000.00, '/cyclestore/uploads/products/Road/img1.jpg', 1, '2025-09-09 16:10:34'),
(82, 1, 2, 'FireFox', '-ire-ox-cb0eb', 'heavy cycle', 'MainAt', 10000.00, '/cyclestore/uploads/products/Road/img1.jpg', 1, '2025-09-09 16:10:59');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `status` enum('ordered','part_received','received','cancelled') NOT NULL DEFAULT 'ordered',
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `received_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `supplier_id`, `status`, `total`, `created_at`, `received_at`) VALUES
(1, 1, 'received', 4000.00, '2025-09-07 18:00:40', '2025-09-07 18:38:29'),
(2, 1, 'received', 4000.00, '2025-09-07 18:00:48', '2025-09-07 18:05:05'),
(3, 1, 'received', 4000.00, '2025-09-07 18:01:49', '2025-09-07 18:03:51'),
(4, 1, 'received', 20000.00, '2025-09-09 16:02:29', '2025-09-12 06:51:34'),
(5, 1, 'received', 10000.00, '2025-09-09 16:11:49', '2025-09-09 16:15:12'),
(6, 1, 'received', 2500000.00, '2025-09-12 06:53:27', '2025-09-12 06:53:56'),
(7, 1, 'received', 1000.00, '2025-09-13 16:19:03', '2025-09-13 16:35:48'),
(8, 1, 'received', 10000.00, '2025-09-15 12:38:27', '2025-09-15 12:40:06'),
(9, 1, 'part_received', 20000.00, '2025-09-20 12:20:09', '2025-09-20 12:20:13');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `received_qty` int(11) NOT NULL DEFAULT 0,
  `unit_cost` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `po_id`, `product_id`, `qty`, `received_qty`, `unit_cost`) VALUES
(1, 1, 3, 1, 1, 4000.00),
(2, 2, 3, 1, 1, 4000.00),
(3, 3, 3, 1, 1, 4000.00),
(4, 4, 55, 2, 2, 10000.00),
(5, 5, 82, 1, 1, 10000.00),
(6, 6, 56, 5, 0, 500000.00),
(7, 7, 82, 1, 1, 1000.00),
(8, 8, 78, 1, 1, 10000.00),
(9, 9, 81, 2, 1, 10000.00);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(3, 'supplier'),
(2, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(160) DEFAULT NULL,
  `gstin` varchar(40) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `user_id`, `company_name`, `gstin`, `created_at`) VALUES
(1, 3, 'Demo Cycles Pvt Ltd', 'GSTINDEMO1234', '2025-09-07 13:13:32');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `method` varchar(40) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `reference` varchar(120) DEFAULT NULL,
  `paid_at` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_payments`
--

INSERT INTO `supplier_payments` (`id`, `supplier_id`, `po_id`, `method`, `amount`, `reference`, `paid_at`, `created_at`) VALUES
(1, 1, 3, 'UPI', 40000.00, '', '2025-09-07 18:02:43', '2025-09-07 18:02:43'),
(2, 1, 5, 'UPI', 10000.00, '', '2025-09-09 16:13:00', '2025-09-09 16:13:22'),
(3, 1, 6, 'UPI', 200.00, '', '2025-09-12 06:54:18', '2025-09-12 06:54:18'),
(4, 1, 7, 'Cash', 50000.00, '', '2025-09-14 19:22:29', '2025-09-14 19:22:29'),
(5, 1, 7, 'Cash', 15000.00, '5202520', '2025-09-03 12:13:00', '2025-09-15 12:13:35'),
(6, 1, 8, 'Bank', 20000.00, '852200', '2025-09-14 12:39:00', '2025-09-15 12:39:20'),
(7, 1, 9, 'Bank', 10000.00, '', '2025-09-20 12:47:41', '2025-09-20 12:47:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `password_plain` varchar(120) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `password_plain`, `created_at`) VALUES
(1, 1, 'Admin', 'admin@example.com', 'admin123', '2025-09-07 13:13:32'),
(2, 2, 'Test User', 'user@example.com', 'user123', '2025-09-07 13:13:32'),
(3, 3, 'Demo Supplier', 'supplier@example.com', 'supp123', '2025-09-07 13:13:32'),
(4, 2, 'saad', 'saadsema41@gmail.com', 'saad23', '2025-09-07 15:28:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_cart_item` (`cart_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `address_id` (`address_id`),
  ADD KEY `idx_orders_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_payments`
--
ALTER TABLE `order_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payments_order` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_products_cat` (`category_id`),
  ADD KEY `idx_products_supplier` (`supplier_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_po_supplier` (`supplier_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_poi_po` (`po_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `po_id` (`po_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `order_payments`
--
ALTER TABLE `order_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inventory_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `order_payments`
--
ALTER TABLE `order_payments`
  ADD CONSTRAINT `fk_order_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `supplier_payments_ibfk_2` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
