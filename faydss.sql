-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2026 at 07:01 AM
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
-- Database: `faydss`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(133, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 00:21:52'),
(134, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 00:21:54'),
(135, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 00:21:58'),
(136, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 00:22:03'),
(137, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 00:22:32'),
(138, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 00:22:36'),
(139, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 00:23:34'),
(140, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 00:23:45'),
(141, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 00:24:31'),
(142, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 00:24:46'),
(143, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 00:25:35'),
(144, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:04:07'),
(145, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:05:07'),
(146, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:06:23'),
(147, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:06:24'),
(148, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:08:25'),
(149, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:08:44'),
(150, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:09:02'),
(151, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:09:03'),
(152, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:09:24'),
(153, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:09:50'),
(154, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:17:53'),
(155, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:22:37'),
(156, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:22:54'),
(157, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:23:08'),
(158, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:23:10'),
(159, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:24:00'),
(160, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:24:00'),
(161, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:27:51'),
(162, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:27:52'),
(163, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:33:30'),
(164, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:33:31'),
(165, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:35:14'),
(166, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:37:36'),
(167, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:37:37'),
(168, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:38:32'),
(169, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:38:35'),
(170, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:38:37'),
(171, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:44:04'),
(172, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:44:40'),
(173, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:44:43'),
(174, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:57:17'),
(175, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:57:17'),
(176, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:57:18'),
(177, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 08:57:18'),
(178, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:00:21'),
(179, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:19:34'),
(180, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:19:35'),
(181, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:20:43'),
(182, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:20:54'),
(183, '1', 'ORDER', 'Placed order #21', '2026-03-28 09:21:10'),
(184, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:21:12'),
(185, '0', 'SYSTEM', 'Cheese Burger has been restocked to 10!', '2026-03-28 09:21:39'),
(186, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:21:46'),
(187, '1', 'ORDER', 'Placed order #22', '2026-03-28 09:22:02'),
(188, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:22:04'),
(189, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:22:22'),
(190, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:22:23'),
(191, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:25:16'),
(192, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:25:22'),
(193, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:27:56'),
(194, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:27:56'),
(195, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:27:56'),
(196, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:30:35'),
(197, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:30:36'),
(198, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:30:36'),
(199, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:30:37'),
(200, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:31:00'),
(201, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:31:05'),
(202, '1', 'LOGIN', 'User accessed dashboard', '2026-03-28 09:31:06'),
(203, '0', 'SYSTEM', 'Only 5 left on Cheese Burger. Order now!', '2026-03-28 09:32:03'),
(204, '1', 'ORDER', 'Placed order #23', '2026-03-28 09:38:11'),
(205, '1', 'ORDER', 'Placed order #24', '2026-03-28 09:38:44'),
(206, '1', 'ORDER', 'Placed order #25', '2026-03-28 09:39:29'),
(207, '1', 'ORDER', 'Placed order #26', '2026-03-28 09:42:22'),
(208, '0', 'SYSTEM', 'Cheese Burger has been restocked!', '2026-03-28 09:43:30'),
(209, '0', 'SYSTEM', 'Only 5 left on Cheese Burger. Order now!', '2026-03-28 09:43:54'),
(210, '0', 'SYSTEM', 'Cheese Burger is now SOLD OUT!', '2026-03-28 09:44:09'),
(211, '0', 'SYSTEM', 'Only 1 left on Cheese Burger. Order now!', '2026-03-28 09:44:43'),
(212, '0', 'SYSTEM', 'Cheese Burger is now SOLD OUT!', '2026-03-28 09:45:01'),
(213, '0', 'SYSTEM', 'Only 1 left on Chicken Burger. Order now!', '2026-03-28 09:45:19'),
(214, '0', 'SYSTEM', 'Chicken Burger is now SOLD OUT!', '2026-03-28 09:45:34'),
(215, '0', 'SYSTEM', 'Only 1 left on Cheese Burger. Order now!', '2026-03-28 09:45:46'),
(216, '0', 'SYSTEM', 'Cheese Burger is now SOLD OUT!', '2026-03-28 09:46:01'),
(217, '1', 'ORDER', 'Placed order #27', '2026-03-28 09:47:06'),
(218, '1', 'ORDER', 'Placed order #28', '2026-03-28 09:53:54'),
(219, '0', 'SYSTEM', 'Stock updated for Classic Burger: 44 remaining.', '2026-03-28 09:54:27'),
(220, '0', 'SYSTEM', 'Only 2 left on Iced Coffee. Order now!', '2026-03-28 09:54:42'),
(221, '1', 'ORDER', 'Placed order #29', '2026-03-28 09:55:19'),
(222, '1', 'ORDER', 'Placed order #30', '2026-03-28 09:55:40'),
(223, '0', 'SYSTEM', 'Stock updated for Classic Burger: 40 remaining.', '2026-03-28 09:56:01'),
(224, '0', 'SYSTEM', 'Stock updated for Classic Burger: 12 remaining.', '2026-03-28 09:56:25'),
(225, '0', 'SYSTEM', 'Stock updated for Dark Coffee: 92 remaining.', '2026-03-28 09:56:58'),
(226, '1', 'ORDER', 'Placed order #31', '2026-03-28 09:58:21'),
(227, '0', 'SYSTEM', 'Only 5 left on Cheese Burger. Order now!', '2026-03-28 10:01:01'),
(228, '0', 'SYSTEM', 'Only 5 left on Chicken Burger. Order now!', '2026-03-28 10:01:07'),
(229, '1', 'ORDER', 'Placed order #32', '2026-03-28 10:01:43'),
(230, '0', 'SYSTEM', 'Only 4 left on Cheese Burger. Order now!', '2026-03-28 10:01:43'),
(231, '1', 'ORDER', 'Placed order #33', '2026-03-28 10:02:02'),
(232, '1', 'ORDER', 'Placed order #34', '2026-03-28 10:02:35'),
(233, '1', 'ORDER', 'Placed order #35', '2026-03-28 10:02:48'),
(234, '0', 'SYSTEM', 'Only 3 left on Cheese Burger. Order now!', '2026-03-28 10:02:49'),
(235, '1', 'ORDER', 'Placed order #36', '2026-03-28 10:10:54'),
(236, '1', 'ORDER', 'Placed order #37', '2026-03-28 10:11:16'),
(237, '0', 'SYSTEM', 'Only 2 left on Cheese Burger. Order now!', '2026-03-28 10:11:18'),
(238, '1', 'ORDER', 'Placed order #38', '2026-03-28 10:13:25'),
(239, '1', 'ORDER', 'Placed order #39', '2026-03-28 10:13:47'),
(240, '1', 'ORDER', 'Placed order #40', '2026-03-28 10:14:16'),
(241, '1', 'ORDER', 'Placed order #41', '2026-03-28 10:18:12'),
(242, '1', 'ORDER', 'Placed order #42', '2026-03-28 10:18:47'),
(243, '1', 'ORDER', 'Placed order #43', '2026-03-28 10:19:22'),
(244, '1', 'ORDER', 'Placed order #44', '2026-03-28 10:28:57'),
(245, '1', 'ORDER', 'Placed order #45', '2026-03-28 10:29:28'),
(246, '1', 'ORDER', 'Placed order #46', '2026-03-28 10:29:51'),
(247, '1', 'ORDER', 'Placed order #47', '2026-03-28 11:22:48'),
(248, '1', 'ORDER', 'Placed order #48', '2026-03-28 11:23:25'),
(249, '1', 'ORDER', 'Placed order #49', '2026-03-28 11:30:13'),
(250, '0', 'SYSTEM', 'Stock updated for Iced Coffee: 50 remaining.', '2026-03-28 11:31:22'),
(251, '1', 'ORDER', 'Placed order #50', '2026-03-28 11:35:35'),
(252, '1', 'ORDER', 'Placed order #51', '2026-03-28 11:35:54'),
(253, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 100 remaining.', '2026-03-28 11:36:17'),
(254, '1', 'ORDER', 'Placed order #52', '2026-03-28 11:36:43'),
(255, '1', 'ORDER', 'Placed order #53', '2026-03-28 11:37:13'),
(256, '1', 'ORDER', 'Placed order #54', '2026-03-28 11:39:39'),
(257, '1', 'ORDER', 'Placed order #55', '2026-03-28 11:40:03'),
(258, '1', 'ORDER', 'Placed order #56', '2026-03-28 11:54:04'),
(259, '1', 'ORDER', 'Placed order #57', '2026-03-28 12:06:12'),
(260, '1', 'ORDER', 'Placed order #58', '2026-03-28 12:19:42'),
(261, '1', 'ORDER', 'Placed order #59', '2026-03-28 12:24:11'),
(262, '1', 'ORDER', 'Placed order #60', '2026-03-28 12:33:58'),
(263, '1', 'ORDER', 'Placed order #62', '2026-03-28 12:35:57'),
(264, '1', 'ORDER', 'Placed order #65', '2026-03-28 12:56:57'),
(265, '1', 'ORDER', 'Placed order #66', '2026-03-28 13:00:17'),
(266, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 5 remaining.', '2026-03-28 13:00:42'),
(267, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 3 remaining.', '2026-03-28 13:00:57'),
(268, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 1 remaining.', '2026-03-28 13:01:15'),
(269, '0', 'SYSTEM', 'Cheese Burger is now SOLD OUT!', '2026-03-28 13:01:33'),
(270, '1', 'ORDER', 'Placed order #67', '2026-03-28 13:03:24'),
(271, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 100 remaining.', '2026-03-28 13:14:32'),
(272, '0', 'SYSTEM', 'Stock updated for Chicken Burger: 100 remaining.', '2026-03-28 13:14:44'),
(273, '1', 'ORDER', 'Placed order #68', '2026-03-28 13:27:41'),
(274, '1', 'ORDER', 'Placed order #69', '2026-03-28 13:38:44'),
(275, '1', 'ORDER', 'Placed order #70', '2026-03-28 13:49:26'),
(276, '1', 'ORDER', 'Placed order #71', '2026-03-28 13:53:43'),
(277, '0', 'SYSTEM', 'Cheese Burger is now SOLD OUT!', '2026-03-28 15:36:21'),
(278, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 2 remaining.', '2026-03-28 15:36:42'),
(279, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 5 remaining.', '2026-03-28 15:37:00'),
(280, '0', 'SYSTEM', 'Cheese Burger is now SOLD OUT!', '2026-03-28 15:37:42'),
(281, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 25 remaining.', '2026-03-28 15:41:40'),
(282, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 26 remaining.', '2026-03-28 15:41:55'),
(283, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 22 remaining.', '2026-03-28 15:54:42'),
(284, '1', 'ORDER', 'Placed order #72', '2026-03-28 15:54:58'),
(285, '0', 'SYSTEM', 'Cheese Burger is now SOLD OUT!', '2026-03-28 15:55:24'),
(286, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 12 remaining.', '2026-03-28 15:55:36'),
(287, '0', 'SYSTEM', 'Cheese Burger is now SOLD OUT!', '2026-03-28 15:58:03'),
(288, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 60 remaining.', '2026-03-28 15:58:16'),
(289, '1', 'ORDER', 'Placed order #73', '2026-03-28 15:58:33'),
(290, '0', 'SYSTEM', 'Cheese Burger is now SOLD OUT!', '2026-03-28 16:04:44'),
(291, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 5 remaining.', '2026-03-28 16:05:05'),
(292, '1', 'ORDER', 'Placed order #74', '2026-03-28 16:05:18'),
(293, '1', 'ORDER', 'Placed order #75', '2026-03-28 16:11:55'),
(294, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 6 remaining.', '2026-03-28 16:16:06'),
(295, '1', 'ORDER', 'Placed order #76', '2026-03-28 16:24:05'),
(296, '0', 'SYSTEM', 'Cheese Burger is now SOLD OUT!', '2026-03-28 16:24:23'),
(297, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 5 remaining.', '2026-03-28 16:24:32'),
(298, '1', 'ORDER', 'Placed order #77', '2026-03-28 16:30:06'),
(299, '1', 'ORDER', 'Placed order #78', '2026-03-28 16:34:19'),
(300, '1', 'ORDER', 'Placed order #79', '2026-03-28 16:34:38'),
(301, '1', 'ORDER', 'Placed order #80', '2026-03-28 16:49:57'),
(302, '1', 'ORDER', 'Placed order #81', '2026-03-28 16:57:16'),
(303, '1', 'ORDER', 'Placed order #82', '2026-03-28 17:07:26'),
(304, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 5 remaining.', '2026-03-28 17:07:54'),
(305, '1', 'ORDER', 'Placed order #83', '2026-03-28 17:08:27'),
(306, '1', 'ORDER', 'Placed order #84', '2026-03-28 17:42:35'),
(307, '0', 'SYSTEM', 'Cheese Burger is now SOLD OUT!', '2026-03-28 17:42:54'),
(308, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 5 remaining.', '2026-03-28 17:43:15'),
(309, '1', 'ORDER', 'Placed order #85', '2026-03-28 17:44:01'),
(310, '1', 'ORDER', 'Placed order #86', '2026-03-28 17:50:29'),
(311, '0', 'SYSTEM', 'Your order #86 is now processing.', '2026-03-28 17:51:02'),
(312, '0', 'SYSTEM', 'You successfully claimed your order #86!', '2026-03-28 17:51:29'),
(313, '4', 'ORDER', 'Placed order #87', '2026-03-28 17:56:58'),
(314, '0', 'SYSTEM', 'You successfully claimed your order #87!', '2026-03-28 17:58:00'),
(315, '0', 'SYSTEM', 'Your order #87 is now processing.', '2026-03-28 17:58:21'),
(316, '0', 'SYSTEM', 'You successfully claimed your order #87!', '2026-03-28 17:58:54'),
(317, '4', 'ORDER', 'Placed order #88', '2026-03-28 18:00:58'),
(318, '5', 'ORDER', 'Placed order #89', '2026-03-28 19:46:13'),
(319, '5', 'ORDER', 'Placed order #90', '2026-03-28 19:48:51'),
(320, '5', 'ORDER', 'Placed order #91', '2026-03-28 23:14:58'),
(321, '8', 'ORDER', 'Placed order #92', '2026-05-15 14:02:21'),
(322, '0', 'PRODUCT_CREATE', 'Created product \"Fadsa\"', '2026-05-15 23:15:56'),
(323, '0', 'PRODUCT_DELETE', 'Deleted product \"Fadsa\"', '2026-05-15 23:16:47'),
(324, '0', 'PRODUCT_CREATE', 'Created product \"sadsa\"', '2026-05-15 23:25:18'),
(325, '0', 'PRODUCT_DELETE', 'Deleted product \"sadsa\"', '2026-05-15 23:26:00'),
(326, '0', 'PRODUCT_CREATE', 'Created product \"asdsad\"', '2026-05-15 23:27:42'),
(327, '0', 'PRODUCT_CREATE', 'Created product \"asdsa\"', '2026-05-15 23:28:13'),
(328, '0', 'PRODUCT_DELETE', 'Deleted product \"asdsad\"', '2026-05-15 23:28:19'),
(329, '0', 'PRODUCT_DELETE', 'Deleted product \"asdsa\"', '2026-05-15 23:28:21'),
(330, '0', 'PRODUCT_CREATE', 'Created product \"asdsad\"', '2026-05-15 23:31:47'),
(331, '0', 'PRODUCT_DELETE', 'Deleted product \"asdsad\"', '2026-05-15 23:31:55'),
(332, '0', 'ORDER_UPDATE', 'Order #92 updated', '2026-05-17 14:56:00'),
(333, '0', 'ORDER_UPDATE', 'Updated order \"undefined\" status to \"COMPLETED\"', '2026-05-17 15:27:23'),
(334, '0', 'ORDER_UPDATE', 'Updated order \"undefined\" status to \"COMPLETED\"', '2026-05-17 15:27:30'),
(335, '0', 'ORDER_UPDATE', 'Updated order \"undefined\" status to \"COMPLETED\"', '2026-05-17 15:31:43'),
(336, '0', 'ORDER_UPDATE', 'Updated order \"92\" status to \"COMPLETED\"', '2026-05-17 15:32:57'),
(337, '0', 'SYSTEM', 'You successfully claimed your order #92!', '2026-05-17 15:32:57'),
(338, '0', 'PRODUCT_UPDATE', 'Updated product \"Cheese Burger\"', '2026-05-17 15:40:45'),
(339, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 20 remaining.', '2026-05-17 15:40:47'),
(340, '0', 'PRODUCT_UPDATE', 'Updated product \"Chicken Burger\"', '2026-05-17 15:41:05'),
(341, '0', 'PRODUCT_UPDATE', 'Updated product \"Classic Burger\"', '2026-05-17 15:42:45'),
(342, '0', 'PRODUCT_UPDATE', 'Updated product \"Iced Coffee\"', '2026-05-17 15:43:20'),
(343, '0', 'PRODUCT_UPDATE', 'Updated product \"Hot Coffee\"', '2026-05-17 15:47:03'),
(344, '0', 'PRODUCT_UPDATE', 'Updated product \"Choco Cream Cake\"', '2026-05-17 15:52:40'),
(345, '0', 'PRODUCT_UPDATE', 'Updated product \"Leche Flan\"', '2026-05-17 15:52:51'),
(346, '0', 'PRODUCT_UPDATE', 'Updated product \"Strawberry Cream Cheese\"', '2026-05-17 15:53:35'),
(347, '0', 'PRODUCT_UPDATE', 'Updated product \"Watermelon Limeade\"', '2026-05-17 15:57:16'),
(348, '0', 'PRODUCT_UPDATE', 'Updated product \"Mango Shake\"', '2026-05-17 15:57:20'),
(349, '10', 'ORDER', 'Placed order #93', '2026-05-17 16:07:54'),
(350, '9', 'ORDER', 'Placed order #94', '2026-05-17 16:14:23'),
(351, '9', 'ORDER', 'Placed order #95', '2026-05-17 16:14:50'),
(352, '0', 'ORDER_UPDATE', 'Updated order \"95\" status to \"PENDING\"', '2026-05-17 16:14:59'),
(353, '0', 'ORDER_UPDATE', 'Updated order \"95\" status to \"COMPLETED\"', '2026-05-17 16:15:04'),
(354, '0', 'SYSTEM', 'You successfully claimed your order #95!', '2026-05-17 16:15:06'),
(355, '9', 'ORDER', 'Placed order #96', '2026-05-17 16:16:28'),
(356, '0', 'ORDER_UPDATE', 'Updated order \"96\" status to \"PENDING\"', '2026-05-17 16:16:43'),
(357, '0', 'ORDER_UPDATE', 'Updated order \"96\" status to \"COMPLETED\"', '2026-05-17 16:16:46'),
(358, '0', 'SYSTEM', 'You successfully claimed your order #96!', '2026-05-17 16:16:48'),
(359, '9', 'ORDER', 'Placed order #97', '2026-05-17 16:28:52'),
(360, '9', 'ORDER', 'Placed order #98', '2026-05-17 16:29:17'),
(361, '9', 'ORDER', 'Placed order #99', '2026-05-17 16:50:48'),
(362, '9', 'ORDER', 'Placed order #100', '2026-05-17 16:51:11'),
(363, '9', 'ORDER', 'Placed order #101', '2026-05-17 16:51:25'),
(364, '9', 'ORDER', 'Placed order #102', '2026-05-17 16:51:50'),
(365, '0', 'ORDER_UPDATE', 'Updated order \"102\" status to \"PENDING\"', '2026-05-17 16:51:58'),
(366, '0', 'ORDER_UPDATE', 'Updated order \"102\" status to \"COMPLETED\"', '2026-05-17 16:52:01'),
(367, '0', 'SYSTEM', 'You successfully claimed your order #102!', '2026-05-17 16:52:02'),
(368, '0', 'ORDER_DELETE', 'Order #102 deleted', '2026-05-17 16:55:43'),
(369, '0', 'ORDER_DELETE', 'Order #102 deleted', '2026-05-17 16:55:48'),
(370, '0', 'ORDER_DELETE', 'Order #96 deleted', '2026-05-17 16:56:11'),
(371, '9', 'ORDER', 'Placed order #103', '2026-05-17 17:02:59'),
(372, '10', 'ORDER', 'Placed order #104', '2026-05-21 20:18:23'),
(373, '10', 'ORDER', 'Placed order #105', '2026-05-21 20:18:49'),
(374, '10', 'ORDER', 'Placed order #106', '2026-05-21 20:19:19'),
(375, '0', 'ORDER_UPDATE', 'Order #106 updated', '2026-05-21 20:19:59'),
(376, '0', 'ORDER_DELETE', 'Order #106 deleted', '2026-05-21 20:20:08'),
(377, '0', 'ORDER_UPDATE', 'Updated order \"105\" status to \"PENDING\"', '2026-05-21 20:22:21'),
(378, '0', 'ORDER_UPDATE', 'Updated order \"104\" status to \"PENDING\"', '2026-05-21 20:22:22'),
(379, '0', 'ORDER_UPDATE', 'Updated order \"105\" status to \"COMPLETED\"', '2026-05-21 20:22:28'),
(380, '0', 'ORDER_UPDATE', 'Updated order \"104\" status to \"COMPLETED\"', '2026-05-21 20:22:30'),
(381, '0', 'SYSTEM', 'You successfully claimed your order #104!', '2026-05-21 20:22:30'),
(382, '0', 'SYSTEM', 'You successfully claimed your order #105!', '2026-05-21 20:22:30'),
(383, '0', 'ORDER_UPDATE', 'Updated order \"103\" status to \"PENDING\"', '2026-05-22 14:14:36'),
(384, '0', 'ORDER_UPDATE', 'Updated order \"103\" status to \"COMPLETED\"', '2026-05-22 14:14:43'),
(385, '0', 'SYSTEM', 'You successfully claimed your order #103!', '2026-05-22 14:14:44'),
(386, '10', 'ORDER', 'Placed order #107', '2026-05-22 14:15:25'),
(387, '10', 'ORDER', 'Placed order #108', '2026-05-22 14:15:37'),
(388, '0', 'ORDER_UPDATE', 'Updated order \"100\" status to \"PENDING\"', '2026-05-22 14:24:05'),
(389, '0', 'ORDER_UPDATE', 'Updated order \"100\" status to \"COMPLETED\"', '2026-05-22 14:25:12'),
(390, '0', 'SYSTEM', 'You successfully claimed your order #100!', '2026-05-22 14:25:12'),
(391, '0', 'ORDER_UPDATE', 'Updated order \"85\" status to \"COMPLETED\"', '2026-05-22 14:25:36'),
(392, '0', 'SYSTEM', 'You successfully claimed your order #85!', '2026-05-22 14:25:37'),
(393, '0', 'ORDER_DELETE', 'Order #93 deleted', '2026-05-23 00:54:24'),
(394, '0', 'ORDER_UPDATE', 'Order #104 updated', '2026-05-23 00:54:29'),
(395, '0', 'ORDER_UPDATE', 'Updated order \"108\" status to \"PENDING\"', '2026-05-23 01:38:28'),
(396, '0', 'ORDER_UPDATE', 'Updated order \"107\" status to \"PENDING\"', '2026-05-23 01:53:08'),
(397, '0', 'ORDER_UPDATE', 'Updated order \"108\" status to \"COMPLETED\"', '2026-05-23 01:53:12'),
(398, '0', 'SYSTEM', 'You successfully claimed your order #108!', '2026-05-23 01:53:13'),
(399, '0', 'PRODUCT_DELETE', 'Deleted product \" Asian Beef \"', '2026-05-23 02:04:07'),
(400, '0', 'PRODUCT_UPDATE', 'Updated product \"Cheese Burger\"', '2026-05-23 02:13:58'),
(401, '0', 'SYSTEM', 'Stock updated for Cheese Burger: 21 remaining.', '2026-05-23 02:13:58'),
(402, '0', 'PRODUCT_UPDATE', 'Updated product \"Baked Chicken Breast \"', '2026-05-23 02:14:17'),
(403, '0', 'PRODUCT_UPDATE', 'Updated product \"Baked Chicken Breast \"', '2026-05-23 02:18:46'),
(404, '10', 'ORDER', 'Placed order #109', '2026-05-23 10:04:08'),
(405, '0', 'PRODUCT_DELETE', 'Deleted product \"Chicken Burger\"', '2026-05-23 10:25:00'),
(406, '0', 'PRODUCT_DELETE', 'Deleted product \"Classic Burger\"', '2026-05-23 12:44:43'),
(407, '10', 'ORDER', 'Placed order #110', '2026-05-23 12:46:05'),
(408, '8', 'PRODUCT_UPDATE', 'Updated product \"Iced Coffee\"', '2026-05-23 12:56:08'),
(409, '8', 'LOGOUT', 'User logged out', '2026-05-23 12:58:23'),
(410, '8', 'LOGIN', 'User admin logged in as admin', '2026-05-23 12:58:30');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `categ_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`categ_id`, `name`, `image_path`, `updated_at`) VALUES
(1, 'Burgers', 'images/burgers.jpg', '2026-03-28 10:03:59'),
(2, 'Drink', 'images/coffee.jpg', '2026-03-27 14:31:54'),
(3, 'Dessert', 'images/dessert.jpg', '2026-03-28 10:10:53'),
(4, 'Chicken', 'images/chik.jpg', '2026-03-28 10:24:33'),
(5, 'Bundles', 'images/bund.jpg', '2026-03-28 10:36:47'),
(6, 'Rice Bowl', 'images/riceB.jpg', '2026-03-28 10:50:38');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `price_total` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','processing','cancelled','completed') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `due_at` time NOT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `staff_id`, `price_total`, `status`, `created_at`, `due_at`, `address`) VALUES
(77, 1, NULL, 630.00, 'completed', '2026-03-28 16:30:06', '00:00:00', 'Pintorluna st. BRGY. Central 1'),
(78, 1, NULL, 480.00, '', '2026-03-28 16:34:19', '00:00:00', 'Palompon Center'),
(79, 1, NULL, 95.00, '', '2026-03-28 16:34:38', '00:00:00', 'ASDASD'),
(80, 1, NULL, 313.50, '', '2026-03-28 16:49:57', '00:00:00', 'Palomponhub'),
(81, 1, NULL, 156.75, '', '2026-03-28 16:57:16', '00:00:00', 'Palomponhub'),
(82, 1, NULL, 156.75, '', '2026-03-28 17:07:26', '00:00:00', 'Pintorluna st. BRGY. Central 1'),
(83, 1, NULL, 252.00, '', '2026-03-28 17:08:27', '00:00:00', 'Pintorluna st. BRGY. Central 1'),
(84, 1, NULL, 126.00, 'processing', '2026-03-28 17:42:35', '00:00:00', 'palompon'),
(85, 1, NULL, 252.00, 'completed', '2026-03-28 17:44:01', '00:00:00', 'Palomponhub'),
(86, 1, NULL, 126.00, 'completed', '2026-03-28 17:50:29', '00:00:00', 'Palomponhub'),
(87, 4, NULL, 126.00, 'completed', '2026-03-28 17:56:58', '00:00:00', 'Lopez,Washington'),
(88, 4, NULL, 686.75, 'pending', '2026-03-28 18:00:58', '00:00:00', 'Palompon Center'),
(89, 5, NULL, 176.22, 'pending', '2026-03-28 19:46:13', '00:00:00', 'Palompon, Leyte'),
(90, 5, NULL, 88.11, 'pending', '2026-03-28 19:48:51', '11:11:00', 'Palompon, Leyte'),
(91, 5, NULL, 133.00, 'pending', '2026-03-28 23:14:58', '11:11:00', 'Palompon, Leyte'),
(92, 8, NULL, 126.00, 'completed', '2026-05-15 14:02:21', '11:11:00', 'Palompon, Leyte'),
(94, 9, NULL, 190.00, 'processing', '2026-05-17 16:14:23', '11:11:00', 'FDSADAD'),
(95, 9, NULL, 123.50, 'completed', '2026-05-17 16:14:50', '11:11:00', 'Palompon, Leyte'),
(97, 9, NULL, 114.00, 'processing', '2026-05-17 16:28:52', '11:11:00', 'Palompon, Leyte'),
(98, 9, NULL, 225.00, 'processing', '2026-05-17 16:29:17', '11:11:00', '1'),
(99, 9, NULL, 1140.00, 'processing', '2026-05-17 16:50:48', '11:11:00', '1111111'),
(100, 9, NULL, 675.00, 'completed', '2026-05-17 16:51:11', '11:11:00', 'Palompon, Leyte'),
(101, 9, NULL, 225.00, 'processing', '2026-05-17 16:51:25', '11:11:00', 'sadsadsdsadsa'),
(103, 9, NULL, 133.00, 'completed', '2026-05-17 17:02:59', '11:11:00', 'Palompon, Leyte'),
(104, 10, NULL, 370.50, 'completed', '2026-05-21 20:18:23', '11:11:00', 'TEST2'),
(105, 10, NULL, 370.50, 'completed', '2026-05-21 20:18:49', '11:11:00', 'TEST2 CART CUSTOMER'),
(107, 10, NULL, 342.00, 'pending', '2026-05-22 14:15:25', '11:11:00', '11111'),
(108, 10, NULL, 114.00, 'completed', '2026-05-22 14:15:37', '11:11:00', '22222'),
(109, 10, NULL, 123.50, 'processing', '2026-05-23 10:04:08', '11:11:00', 'Palompon, Leyte'),
(110, 10, NULL, 123.50, 'processing', '2026-05-23 12:46:05', '11:11:00', 'FDSADAD');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `orderitem_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`orderitem_id`, `order_id`, `product`, `quantity`, `price`, `discount`, `subtotal`) VALUES
(1, 14, 2, 1, 165.00, 5.00, 156.75),
(2, 14, 6, 1, 75.00, 0.00, 75.00),
(3, 32, 1, 1, 140.00, 10.00, 126.00),
(4, 35, 1, 1, 140.00, 10.00, 126.00),
(5, 37, 1, 1, 140.00, 10.00, 126.00),
(6, 39, 1, 1, 140.00, 10.00, 126.00),
(7, 41, 1, 1, 140.00, 10.00, 126.00),
(8, 42, 2, 1, 165.00, 5.00, 156.75),
(9, 43, 2, 2, 165.00, 5.00, 313.50),
(10, 44, 2, 1, 165.00, 5.00, 156.75),
(11, 45, 2, 1, 165.00, 5.00, 156.75),
(12, 46, 3, 1, 120.00, 0.00, 120.00),
(13, 47, 3, 1, 120.00, 0.00, 120.00),
(14, 48, 3, 5, 120.00, 0.00, 600.00),
(15, 49, 4, 2, 95.00, 0.00, 190.00),
(16, 50, 4, 1, 95.00, 0.00, 95.00),
(17, 51, 4, 5, 95.00, 0.00, 475.00),
(18, 52, 1, 1, 140.00, 10.00, 126.00),
(19, 53, 1, 2, 140.00, 10.00, 252.00),
(20, 54, 1, 1, 140.00, 10.00, 126.00),
(21, 55, 1, 1, 140.00, 10.00, 126.00),
(22, 56, 1, 5, 140.00, 10.00, 630.00),
(23, 57, 1, 2, 140.00, 10.00, 252.00),
(24, 58, 1, 1, 140.00, 10.00, 126.00),
(30, 63, 1, 1, 140.00, 10.00, 126.00),
(42, 76, 1, 1, 140.00, 10.00, 126.00),
(43, 77, 1, 5, 140.00, 10.00, 630.00),
(44, 78, 3, 4, 120.00, 0.00, 480.00),
(45, 79, 4, 1, 95.00, 0.00, 95.00),
(46, 80, 2, 2, 165.00, 5.00, 313.50),
(47, 81, 2, 1, 165.00, 5.00, 156.75),
(48, 82, 2, 1, 165.00, 5.00, 156.75),
(49, 83, 1, 2, 140.00, 10.00, 252.00),
(50, 84, 1, 1, 140.00, 10.00, 126.00),
(51, 85, 1, 2, 140.00, 10.00, 252.00),
(52, 86, 1, 1, 140.00, 10.00, 126.00),
(53, 87, 1, 1, 140.00, 10.00, 126.00),
(54, 88, 2, 1, 165.00, 5.00, 156.75),
(55, 88, 4, 4, 95.00, 0.00, 380.00),
(56, 88, 6, 2, 75.00, 0.00, 150.00),
(57, 89, 7, 2, 89.00, 1.00, 176.22),
(58, 90, 7, 1, 89.00, 1.00, 88.11),
(59, 91, 18, 1, 140.00, 5.00, 133.00),
(60, 92, 1, 1, 140.00, 10.00, 126.00),
(62, 94, 21, 1, 200.00, 5.00, 190.00),
(63, 95, 16, 1, 130.00, 5.00, 123.50),
(65, 97, 15, 1, 120.00, 5.00, 114.00),
(66, 98, 13, 1, 250.00, 10.00, 225.00),
(67, 99, 15, 10, 120.00, 5.00, 1140.00),
(68, 100, 13, 3, 250.00, 10.00, 675.00),
(69, 101, 13, 1, 250.00, 10.00, 225.00),
(71, 103, 18, 1, 140.00, 5.00, 133.00),
(72, 104, 16, 3, 130.00, 5.00, 370.50),
(73, 105, 16, 3, 130.00, 5.00, 370.50),
(75, 107, 15, 3, 120.00, 5.00, 342.00),
(76, 108, 15, 1, 120.00, 5.00, 114.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `prod_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `discount` decimal(5,2) DEFAULT NULL,
  `image_path` varchar(50) DEFAULT NULL,
  `category` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`prod_id`, `name`, `price`, `stock`, `discount`, `image_path`, `category`) VALUES
(4, 'Iced Coffee', 95.00, 39, 2.00, 'images/coffee.jpg', 2),
(6, 'Hot Coffee', 75.00, 4, 1.00, '2', 2),
(7, 'Choco Cream Cake', 89.00, 97, 1.00, '3', 3),
(8, 'Leche Flan', 75.00, 50, 4.00, '3', 3),
(9, 'Strawberry Cream Cheese', 100.00, 60, 1.00, '3', 3),
(11, 'Watermelon Limeade', 60.00, 100, 1.00, '2', 2),
(12, 'Mango Shake', 90.00, 50, 5.00, '2', 2),
(13, 'Roasted Chicken', 250.00, 91, 10.00, 'images/ChickR.JPG', 4),
(15, 'Chicken Garlic', 120.00, 84, 5.00, 'images/CHICKENg.jpg', 4),
(16, 'Baked Chicken Breast ', 130.00, 90, 5.00, 'images/baked.jpg', 4),
(17, 'Family', 500.00, 100, 5.00, 'images/fam.jpg', 5),
(18, 'Couple ', 140.00, 98, 5.00, 'images/coup.jpg', 5),
(19, 'Friends', 200.00, 100, 5.00, 'images/bff.jpg', 5),
(20, 'Chicken Teriyaki Bowl', 100.00, 100, 5.00, 'images/Chicken ter.jpg', 6);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` enum('admin','staff','customer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `login_at` timestamp NULL DEFAULT NULL,
  `logout_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `usertype`, `created_at`, `login_at`, `logout_at`) VALUES
(1, 'ryan', 'hi@gmail.com', '$2y$10$JPZMDKiPUKCquLCCpwr13u5zqtJcSiu1NQQnjLxz968mYT7OIq822', 'customer', '2026-03-27 06:29:42', NULL, NULL),
(2, 'lara', 'lara@gmail.com', '$2y$10$FIZaYE8ZwFwhIpFVk6RynOX7WdVHxcfcNSnwCAitSFnWlzV4WmZPm', 'customer', '2026-03-27 07:13:12', NULL, NULL),
(3, 'lara', 'laraa@gmail.com', '$2y$10$/SZqGcSCw9Vj.FDCy8xuYOmKDIhtx7MTpLAkKiP9bOzNAbXYa607a', 'customer', '2026-03-27 11:58:19', NULL, NULL),
(4, 'jane', 'Jane1@gmail.com', '$2y$10$gFNhHLkcagpjd2OU9fpEcugVgjApPN64u2V8Gb0fwi4vj6/83kUWW', 'customer', '2026-03-28 09:53:37', NULL, NULL),
(5, 'Adrian', 'adrianaldiano55@gmail.com', '$2y$10$yB1GBBDgMuZBSDNzd2pH7.gKd1IDUtprmiQz2fdqCXhYofc/bVtha', 'customer', '2026-03-28 11:45:27', NULL, NULL),
(8, 'admin', 'adrianaldiano55@gmail.com', '$2y$10$lAPNZg/glkdM01TKrASIEOo3VU58pm.8XGtd5VlEmiD.EnPW.ODpS', 'admin', '2026-05-15 02:49:04', '2026-05-23 04:58:30', '2026-05-23 04:58:23'),
(9, 'deliverytest', 'adrianaldiano55@gmail.com', '$2y$10$7m.AJHc6FF3TaFoBThVltOwLzwHfVfmEdhxJytuFQ/EkUCSLO7Dhm', 'staff', '2026-05-17 04:25:00', NULL, NULL),
(10, 'customertest', 'adrianaldiano55@gmail.com', '$2y$10$rOGAbEN.Erjq/ICMXzIrcuewr6cpolZd4Mng78TqekaKTzTiqPdOq', 'customer', '2026-05-17 04:32:01', '2026-05-23 04:45:55', '2026-05-23 04:46:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`categ_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`orderitem_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product` (`product`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`prod_id`),
  ADD KEY `category` (`category`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=411;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `categ_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121215;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `orderitem_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `prod_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12127;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
