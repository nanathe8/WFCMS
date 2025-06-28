-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2025 at 09:34 AM
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
-- Database: `fcms`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHotSellingProducts` (IN `start_date` DATE, IN `end_date` DATE)   BEGIN
            SELECT P.ProductID, P.ProductName, P.ProductImage, SUM(OP.Quantity) AS TotalQuantity, P.ProductPrice
            FROM PRODUCT P
            JOIN ORDER_PRODUCT OP ON P.ProductID = OP.ProductID
            JOIN ORDERS O ON OP.OrdersID = O.OrdersID
            WHERE O.OrderDate BETWEEN start_date AND end_date
            GROUP BY P.ProductID, P.ProductName, P.ProductImage, P.ProductPrice
            ORDER BY TotalQuantity DESC
            LIMIT 3;
        END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `audittrail`
--

CREATE TABLE `audittrail` (
  `AuditID` int(11) NOT NULL,
  `Timestamp` datetime NOT NULL,
  `EventType` varchar(50) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `OrdersID` varchar(5) DEFAULT NULL,
  `Details` text NOT NULL,
  `Status` varchar(20) NOT NULL,
  `StaffID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audittrail`
--

INSERT INTO `audittrail` (`AuditID`, `Timestamp`, `EventType`, `customer_id`, `OrdersID`, `Details`, `Status`, `StaffID`) VALUES
(1, '2024-11-10 00:00:00', 'Order Created', 1, 'O001', '{\"product_id\":\"FC001\",\"quantity\":3,\"subtotal\":24.00}', 'Pending', NULL),
(3, '2024-11-12 00:00:00', 'Order Created', 3, 'O003', '{\"product_id\":\"FC003\",\"quantity\":5,\"subtotal\":65.00}', 'In Process', NULL),
(5, '2024-11-14 00:00:00', 'Order Created', 5, 'O005', '{\"product_id\":\"FC005\",\"quantity\":4,\"subtotal\":48.00}', 'In Process', NULL),
(7, '2024-11-16 00:00:00', 'Order Created', 7, 'O007', '{\"product_id\":\"FC007\",\"quantity\":2,\"subtotal\":40.00}', 'Completed', NULL),
(16, '2024-11-16 00:00:00', 'Payment Completed', 7, 'O007', '{\"payment_method\":\"Credit Card\",\"total_amount\":40.00}', 'Completed', NULL),
(19, '2025-01-08 15:29:19', 'Order Status Update', 1, 'O001', 'Order ID O001 status updated to Completed.', '', NULL),
(20, '2025-01-08 15:29:41', 'Order Status Update', 1, 'O001', 'Order ID O001 status updated to Pending.', '', NULL),
(30, '2025-01-08 20:37:09', 'Order Status Update', 3, 'O003', 'Order ID O003 status updated to Completed.', '', NULL),
(31, '2025-01-08 20:50:54', 'Product Update', NULL, NULL, 'Product ID FC009 updated.', '', NULL),
(33, '2025-01-09 17:38:15', 'Product Update', NULL, NULL, 'Product ID FC001 updated (Price: 8.00, Stock: Available, Image: ori.jpg).', 'Updated', 1),
(34, '2025-01-09 17:41:02', 'Product Update', NULL, NULL, 'Product ID FC006 updated (Price: 20.00, Stock: Available, Image: ori.jpg).', 'Updated', 1),
(35, '2025-01-09 18:52:54', 'Order Status Update', 5, 'O005', 'Order ID O005 status changed from Completed to Completed.', 'Updated', 1),
(36, '2025-01-09 21:48:54', 'Product Delete', NULL, NULL, 'Product  (ID: FC011) deleted.', 'Deleted', 1),
(37, '2025-01-09 22:00:50', 'Product Add', NULL, NULL, 'New product Original (ID: FC001) added.', 'Added', 1),
(38, '2025-01-09 22:02:09', 'Product Add', NULL, NULL, 'New product Spicy (ID: FC002) added.', 'Added', 1),
(39, '2025-01-09 22:02:20', 'Product Update', NULL, NULL, 'Product ID FC001 updated (Price: 12.00, Stock: Available, Image: ori.jpg).', 'Updated status', 1),
(40, '2025-01-09 22:03:54', 'Product Add', NULL, NULL, 'New product Honey Glazed (ID: FC003) added.', 'Added', 1),
(41, '2025-01-09 22:04:27', 'Product Add', NULL, NULL, 'New product Garlic Permesan (ID: FC004) added.', 'Added', 1),
(42, '2025-01-09 22:04:51', 'Product Add', NULL, NULL, 'New product Korean Spicy (ID: FC005) added.', 'Added', 1),
(43, '2025-01-09 22:05:07', 'Product Update', NULL, NULL, 'Product ID FC005 updated (Price: 14.00, Stock: Available, Image: koreanSpicy.jpg).', 'Updated status', 1),
(44, '2025-01-09 22:05:23', 'Product Add', NULL, NULL, 'New product ayam (ID: FC006) added.', 'Added', 1),
(45, '2025-01-09 22:07:02', 'Product Delete', NULL, NULL, 'Product  (ID: FC006) deleted.', 'Deleted', 1),
(46, '2025-01-09 22:17:43', 'Product Add', NULL, NULL, 'New product Original (ID: FC001) added.', 'Added', 1),
(47, '2025-01-09 22:18:03', 'Product Add', NULL, NULL, 'New product Spicy (ID: FC002) added.', 'Added', 1),
(48, '2025-01-09 22:18:08', 'Product Delete', NULL, NULL, 'Product Original (ID: FC001) deleted.', 'Deleted', 1),
(49, '2025-01-09 22:19:02', 'Product Add', NULL, NULL, 'New product Original (ID: FC001) added.', 'Added', 1),
(50, '2025-01-09 22:20:02', 'Product Add', NULL, NULL, 'New product Korean Spicy (ID: FC003) added.', 'Added', 1),
(51, '2025-01-09 22:21:41', 'Product Update', NULL, NULL, 'Product ID FC001 updated (Price: 8.00, Stock: Available, Image: ori.jpg).', 'Updated status', 1),
(52, '2025-01-09 22:21:52', 'Product Update', NULL, NULL, 'Product ID FC002 updated (Price: 8.00, Stock: Available, Image: spicy.jpg).', 'Updated status', 1),
(53, '2025-01-09 22:22:33', 'Product Update', NULL, NULL, 'Product ID FC003 updated (Price: 13.00, Stock: Available, Image: koreanSpicy.jpg).', 'Updated status', 1),
(54, '2025-01-09 22:23:48', 'Product Add', NULL, NULL, 'New product Honey Glazed (ID: FC004) added.', 'Added', 1),
(55, '2025-01-09 22:24:17', 'Product Add', NULL, NULL, 'New product Garlic Permesan (ID: FC005) added.', 'Added', 1),
(56, '2025-01-09 22:26:09', 'Product Add', NULL, NULL, 'New product Original Happy Box (ID: FC006) added.', 'Added', 1),
(57, '2025-01-09 22:26:36', 'Product Add', NULL, NULL, 'New product Spicy Happy Box (ID: FC007) added.', 'Added', 1),
(58, '2025-01-09 22:27:32', 'Product Update', NULL, NULL, 'Product ID FC007 updated (Price: 20.00, Stock: Available, Image: spicy.jpg).', 'Updated status', 1),
(59, '2025-01-09 22:27:42', 'Product Update', NULL, NULL, 'Product ID FC006 updated (Price: 20.00, Stock: Available, Image: ori.jpg).', 'Updated status', 1),
(60, '2025-01-09 22:29:31', 'Product Add', NULL, NULL, 'New product Korean Spicy Happy Box (ID: FC008) added.', 'Added', 1),
(61, '2025-01-09 22:30:17', 'Product Add', NULL, NULL, 'New product Honey Glazed Happy Box (ID: FC009) added.', 'Added', 1),
(62, '2025-01-09 22:30:28', 'Product Update', NULL, NULL, 'Product ID FC009 updated (Price: 27.00, Stock: Available, Image: honey.jpg).', 'Updated status', 1),
(63, '2025-01-09 22:31:19', 'Product Add', NULL, NULL, 'New product Garlic Permesan Happy Box (ID: FC010) added.', 'Added', 1),
(64, '2025-01-09 22:31:57', 'Product Add', NULL, NULL, 'New product Peanurttt (ID: FC011) added.', 'Added', 1),
(65, '2025-01-09 22:32:11', 'Product Delete', NULL, NULL, 'Product Peanurttt (ID: FC011) deleted.', 'Deleted', 1),
(66, '2025-01-10 00:56:39', 'Product Add', NULL, NULL, 'New product Peanurttt (ID: FC011) added.', 'Added', 1),
(67, '2025-01-10 00:56:52', 'Product Delete', NULL, NULL, 'Product Peanurttt (ID: FC011) deleted.', 'Deleted', 1),
(68, '2025-01-10 01:00:58', 'Product Add', NULL, NULL, 'New product Peanurttt (ID: FC011) added.', 'Added', 1),
(69, '2025-01-10 01:01:26', 'Product Update', NULL, NULL, 'Product ID FC011 updated. Price: 100.00 changed to 500.00, Stock: Available changed to Not Available', 'Updated', 1),
(76, '2025-01-11 11:00:02', 'Order Status Update', 7, 'O007', 'Order ID O007 status changed from Completed to Completed.', 'Updated', 1),
(77, '2025-01-11 11:54:50', 'Order Status Update', 7, 'O007', 'Order ID O007 status changed from Completed to In Process.', 'Updated', 1),
(78, '2025-01-11 12:04:25', 'Order Status Update', 7, 'O007', 'Order ID O007 status changed from In Process to Completed.', 'Updated', 1),
(79, '2025-01-11 12:07:34', 'Order Status Update', 7, 'O007', 'Order ID O007 status changed from Completed to Pending.', 'Updated', 1),
(80, '2025-01-11 12:07:43', 'Order Status Update', 7, 'O007', 'Order ID O007 status changed from Pending to Canceled.', 'Updated', 1),
(81, '2025-01-11 12:09:16', 'Order Status Update', 7, 'O007', 'Order ID O007 status changed from Canceled to Completed.', 'Updated', 1),
(82, '2025-01-11 12:41:11', 'Order Status Update', 7, 'O007', 'Order ID O007 status changed from Completed to Pending.', 'Pending', 1),
(100, '2025-01-11 14:56:52', 'Order Status Update', 7, 'O007', 'Order ID O007 status changed from Pending to Completed.', 'Updated', 1),
(101, '2025-01-11 14:59:44', 'Order Status Update', 7, 'O007', 'Order ID O007 status changed from Completed to Pending.', 'Updated', 1),
(102, '2025-01-11 14:59:50', 'Order Status Update', 7, 'O007', 'Order ID O007 status changed from Pending to Canceled.', 'Updated', 1),
(103, '2025-01-11 15:00:08', 'Order Status Update', 7, 'O007', 'Order ID O007 status changed from Canceled to Pending.', 'Updated', 1),
(107, '2025-01-11 15:03:52', 'Order Status Update', 1, 'O001', 'Order ID O001 status changed from Completed to In Process.', 'Updated', 1),
(108, '2025-01-11 15:10:35', 'Product Update', NULL, NULL, 'ProductID: FC002 updated. Price: 800.00 changed to 8.00', 'Updated', 1),
(109, '2025-01-11 15:11:15', 'Product Delete', NULL, NULL, 'Product Peanurttt (ID: FC011) deleted.', 'Deleted', 1),
(110, '2025-01-11 15:13:51', 'Product Add', NULL, NULL, 'New product Peanurttt (ID: FC011) added.', 'Added', 1),
(111, '2025-01-11 15:20:41', 'Order Status Update', 1, 'O001', 'Order ID O001 status changed from In Process to Pending.', 'Updated', 1),
(112, '2025-01-11 15:21:02', 'Order Status Update', 1, 'O001', 'Order ID O001 status changed from Pending to In Process.', 'Updated', 1),
(113, '2025-01-11 15:21:28', 'Order Status Update', 1, 'O001', 'Order ID O001 status changed from In Process to Completed.', 'Updated', 1),
(114, '2025-01-12 14:41:56', 'Product Add', NULL, NULL, 'New product AZIM HENSEM (ID: AZIM) added.', 'Added', NULL),
(115, '2025-01-12 14:42:28', 'Product Delete', NULL, NULL, 'Product AZIM HENSEM (ID: AZIM) deleted.', 'Deleted', NULL),
(116, '2025-01-17 11:29:07', 'Product Add', NULL, NULL, 'New product DALGONA CAKE (ID: SD004) added.', 'Added', NULL),
(118, '2025-01-18 18:17:35', 'Order Created', 1, 'O047', 'New product added to order with ID: O047.', 'pending', NULL),
(129, '2025-01-19 12:16:22', 'Order Created', 1, 'O064', 'ProductID: FC003, Quantity: 1, SubTotal: RM 13.00', 'pending', NULL),
(130, '2025-01-19 12:53:54', 'Order Created', 1, 'O065', 'ProductID: FC008, Quantity: 1, SubTotal: RM 32.00', 'pending', NULL),
(131, '2025-01-19 13:40:43', 'Order Created', 1, 'O066', 'ProductID: FC004, Quantity: 1, SubTotal: RM 11.00', 'pending', NULL),
(132, '2025-01-19 13:42:18', 'Order Created', 1, 'O067', 'ProductID: FC004, Quantity: 1, SubTotal: RM 11.00', 'pending', NULL),
(135, '2025-01-19 13:52:07', 'Order Created', 1, 'O070', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(136, '2025-01-19 15:37:57', 'Order Created', 1, 'O071', 'ProductID: FC005, Quantity: 1, SubTotal: RM 14.00', 'pending', NULL),
(138, '2025-01-19 15:53:00', 'Order Created', 1, 'O073', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(139, '2025-01-19 15:53:45', 'Order Created', 1, 'O074', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(140, '2025-01-19 15:53:57', 'Order Created', 1, 'O075', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(141, '2025-01-19 15:54:05', 'Order Created', 1, 'O076', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(156, '2025-01-20 00:17:46', 'Order Status Update', 1, 'O073', 'Order ID O073 status changed from Completed to Completed.', 'Updated', 1),
(157, '2025-01-20 00:21:56', 'Order Status Update', 1, 'O071', 'Order ID O071 status changed from Pending to In Process.', 'Updated', 1),
(158, '2025-01-20 00:35:59', 'Order Status Update', 1, 'O070', 'Order ID O070 status changed from Pending to Completed.', 'Updated', 1),
(159, '2025-01-20 00:40:52', 'Order Status Update', 1, 'O076', 'Order ID O076 status changed from In Process to Completed.', 'Updated', 1),
(162, '2025-01-20 01:03:03', 'Product Update', NULL, NULL, 'ProductID: SD004 updated. Stock: Not Available changed to Available', 'Updated', 1),
(163, '2025-01-20 01:06:45', 'Product Update', NULL, NULL, 'ProductID: SD004 updated. Stock: Available changed to Not Available', 'Updated', 1),
(166, '2025-01-20 12:02:40', 'Order Status Update', 1, 'O067', 'Order ID O067 status changed from Pending to Completed.', 'Updated', 1),
(167, '2025-01-20 12:05:00', 'Order Status Update', 1, 'O066', 'Order ID O066 status changed from Pending to Completed.', 'Updated', 1),
(168, '2025-01-20 14:51:04', 'Order Status Update', 1, 'O065', 'Order ID O065 status changed from Pending to Canceled.', 'Updated', 1),
(169, '2025-01-20 14:51:13', 'Order Status Update', 1, 'O064', 'Order ID O064 status changed from Pending to Canceled.', 'Updated', 1),
(170, '2025-01-20 14:51:55', 'Order Status Update', 1, 'O063', 'Order ID O063 status changed from Pending to Completed.', 'Updated', 1),
(171, '2025-01-20 14:52:04', 'Order Status Update', 1, 'O062', 'Order ID O062 status changed from Pending to Completed.', 'Updated', 1),
(172, '2025-01-20 14:52:14', 'Order Status Update', 1, 'O061', 'Order ID O061 status changed from Pending to Completed.', 'Updated', 1),
(173, '2025-01-20 14:52:22', 'Order Status Update', 1, 'O060', 'Order ID O060 status changed from Pending to Canceled.', 'Updated', 1),
(174, '2025-01-20 14:52:32', 'Order Status Update', 1, 'O059', 'Order ID O059 status changed from Pending to Completed.', 'Updated', 1),
(175, '2025-01-20 15:02:23', 'Order Status Update', 1, 'O058', 'Order ID O058 status changed from Pending to Completed.', 'Updated', 1),
(176, '2025-01-20 17:12:29', 'Order Created', 1, 'O077', 'ProductID: FC004, Quantity: 1, SubTotal: RM 11.00', 'pending', NULL),
(177, '2025-01-20 17:14:41', 'Order Created', 5, 'O078', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(181, '2025-01-20 17:45:27', 'Order Status Update', 5, 'O078', 'Order ID O078 status changed from In Process to Completed.', 'Updated', 1),
(183, '2025-01-20 17:45:48', 'Order Status Update', 1, 'O056', 'Order ID O056 status changed from Pending to Completed.', 'Updated', 1),
(184, '2025-01-20 17:46:07', 'Order Status Update', 1, 'O055', 'Order ID O055 status changed from Pending to Completed.', 'Updated', 1),
(186, '2025-01-20 17:47:07', 'Order Status Update', 5, 'O078', 'Order ID O078 status changed from Pending to In Process.', 'Updated', 1),
(189, '2025-01-20 17:51:10', 'Order Created', 1, 'O079', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(190, '2025-01-20 17:51:15', 'Order Created', 1, 'O080', 'ProductID: FC001, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(194, '2025-01-20 17:58:13', 'Order Status Update', 1, 'O054', 'Order ID O054 status changed from Pending to Canceled.', 'Updated', 1),
(195, '2025-01-20 17:58:21', 'Order Status Update', 1, 'O053', 'Order ID O053 status changed from Pending to Completed.', 'Updated', 1),
(196, '2025-01-20 17:58:30', 'Order Status Update', 1, 'O052', 'Order ID O052 status changed from Pending to Completed.', 'Updated', 1),
(197, '2025-01-20 17:58:37', 'Order Status Update', 1, 'O050', 'Order ID O050 status changed from Pending to Completed.', 'Updated', 1),
(198, '2025-01-20 17:58:49', 'Order Status Update', 1, 'O046', 'Order ID O046 status changed from Pending to Completed.', 'Updated', 1),
(199, '2025-01-20 17:58:55', 'Order Status Update', 1, 'O048', 'Order ID O048 status changed from Pending to Completed.', 'Updated', 1),
(200, '2025-01-20 17:59:05', 'Order Status Update', 1, 'O047', 'Order ID O047 status changed from Pending to Canceled.', 'Updated', 1),
(201, '2025-01-20 17:59:13', 'Order Status Update', 1, 'O049', 'Order ID O049 status changed from Pending to Completed.', 'Updated', 1),
(203, '2025-01-20 18:01:00', 'Order Created', 1, 'O081', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(204, '2025-01-20 18:16:41', 'Order Created', 1, 'O082', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(205, '2025-01-20 18:21:47', 'Order Created', 1, 'O083', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(206, '2025-01-20 18:29:32', 'Order Created', 0, 'O084', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(209, '2025-01-20 18:41:05', 'Order Created', 1, 'O087', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(210, '2025-01-20 18:48:50', 'Order Created', 1, 'O088', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(211, '2025-01-20 18:56:04', 'Order Created', 1, 'O089', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(214, '2025-01-20 19:04:38', 'Order Created', 0, 'O090', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(215, '2025-01-20 19:22:13', 'Product Update', NULL, NULL, 'ProductID: FC011 updated. Stock: Not Available changed to Available', 'Updated', NULL),
(216, '2025-01-20 19:23:53', 'Order Created', 0, 'O091', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(218, '2025-01-20 19:28:29', 'Order Created', 0, 'O092', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(222, '2025-01-20 19:38:00', 'Order Created', 0, 'O093', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(230, '2025-01-20 20:11:51', 'Order Status Update', 0, 'O093', 'Order ID O093 status changed from Pending to In Process.', 'Updated', 1),
(234, '2025-01-21 22:56:01', 'Order Status Update', 0, 'O090', 'Order ID O090 status changed from Pending to In Process.', 'Updated', 1),
(235, '2025-01-21 22:56:22', 'Order Status Update', 0, 'O092', 'Order ID O092 status changed from In Process to Completed.', 'Updated', 1),
(236, '2025-01-21 22:56:38', 'Order Status Update', 1, 'O051', 'Order ID O051 status changed from Pending to Completed.', 'Updated', 1),
(237, '2025-01-22 00:14:24', 'Order Status Update', 1, 'O077', 'Order ID O077 status changed from Pending to Canceled.', 'Updated', 1),
(238, '2025-01-22 00:14:42', 'Order Status Update', 1, 'O079', 'Order ID O079 status changed from Pending to Canceled.', 'Updated', 1),
(239, '2025-01-22 00:14:54', 'Order Status Update', 1, 'O081', 'Order ID O081 status changed from Pending to Canceled.', 'Updated', 1),
(240, '2025-01-22 00:15:08', 'Order Status Update', 1, 'O082', 'Order ID O082 status changed from Pending to Canceled.', 'Updated', 1),
(241, '2025-01-22 00:15:18', 'Order Status Update', 1, 'O083', 'Order ID O083 status changed from Pending to Completed.', 'Updated', 1),
(242, '2025-01-22 00:15:35', 'Order Status Update', 0, 'O084', 'Order ID O084 status changed from Pending to Completed.', 'Updated', 1),
(243, '2025-01-22 17:37:42', 'Order Created', 1, 'O094', 'ProductID: FC005, Quantity: 1, SubTotal: RM 14.00', 'pending', NULL),
(250, '2025-01-22 18:32:44', 'Order Created', 1, 'O095', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(255, '2025-01-22 18:48:20', 'Product Add', NULL, NULL, 'New product Ayam Goreng (ID: SF002) added.', 'Added', NULL),
(256, '2025-01-22 23:23:20', 'Order Status Update', 1, 'O095', 'Order ID O095 status changed from Completed to Pending.', 'Updated', 1),
(257, '2025-01-22 23:23:27', 'Order Status Update', 1, 'O095', 'Order ID O095 status changed from Pending to In Process.', 'Updated', 1),
(258, '2025-01-22 23:24:34', 'Order Status Update', 1, 'O095', 'Order ID O095 status changed from In Process to Pending.', 'Updated', 1),
(259, '2025-01-22 23:26:59', 'Product Delete', NULL, NULL, 'Product Ayam Goreng (ID: SF002) deleted.', 'Deleted', 1),
(260, '2025-01-22 23:28:44', 'Product Update', NULL, NULL, 'ProductID: FC001 updated. Stock: Available changed to Not Available', 'Updated', 1),
(261, '2025-01-22 23:29:48', 'Order Status Update', 1, 'O095', 'Order ID O095 status changed from Pending to Canceled.', 'Updated', 1),
(262, '2025-01-22 23:30:17', 'Order Status Update', 1, 'O095', 'Order ID O095 status changed from Canceled to Completed.', 'Updated', 1),
(263, '2025-01-22 23:31:11', 'Order Status Update', 1, 'O095', 'Order ID O095 status changed from Completed to Canceled.', 'Updated', 1),
(264, '2025-01-22 23:31:58', 'Order Status Update', 1, 'O094', 'Order ID O094 status changed from Completed to Pending.', 'Updated', 1),
(265, '2025-01-22 23:35:02', 'Order Status Update', 1, 'O094', 'Order ID O094 status changed from Pending to In Process.', 'Updated', 1),
(266, '2025-01-22 23:38:59', 'Product Update', NULL, NULL, 'ProductID: FC002 updated. Price: 8.00 changed to 10.30', 'Updated', 1),
(267, '2025-01-22 23:39:56', 'Product Update', NULL, NULL, 'ProductID: FC002 updated. Price: 10.30 changed to 8.00, Stock: Available changed to Not Available', 'Updated', 1),
(268, '2025-01-22 23:40:20', 'Product Update', NULL, NULL, 'ProductID: FC002 updated. Stock: Not Available changed to Available', 'Updated', 1),
(269, '2025-01-22 23:40:49', 'Product Update', NULL, NULL, 'ProductID: FC001 updated. Stock: Not Available changed to Available', 'Updated', 1),
(270, '2025-01-22 23:43:04', 'Order Status Update', 1, 'O094', 'Order ID O094 status changed from In Process to Canceled.', 'Updated', 1),
(271, '2025-01-22 23:43:18', 'Order Status Update', 1, 'O094', 'Order ID O094 status changed from Canceled to Completed.', 'Updated', 1),
(272, '2025-01-23 11:34:13', 'Order Created', 1, 'O096', 'ProductID: FC001, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(275, '2025-01-23 11:53:38', 'Order Created', 1, 'O097', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL),
(276, '2025-01-23 12:02:34', 'Order Created', 1, 'O098', 'ProductID: FC003, Quantity: 1, SubTotal: RM 13.00', 'pending', NULL),
(279, '2025-01-23 12:08:43', 'Order Created', 1, 'O099', 'ProductID: FC004, Quantity: 1, SubTotal: RM 11.00', 'pending', NULL),
(280, '2025-01-23 12:11:58', 'Order Created', 1, 'O100', 'ProductID: FC006, Quantity: 1, SubTotal: RM 20.00', 'pending', NULL),
(281, '2025-01-23 12:13:09', 'Order Status Update', 1, 'O100', 'Order ID O100 status changed from Pending to Completed.', 'Updated', 1),
(282, '2025-01-23 12:28:14', 'Order Created', 1, 'O101', 'ProductID: FC005, Quantity: 1, SubTotal: RM 14.00', 'pending', NULL),
(283, '2025-01-23 12:30:11', 'Order Status Update', 1, 'O100', 'Order ID O100 status changed from Completed to Completed.', 'Updated', 1),
(284, '2025-01-23 13:23:32', 'Order Created', 1, 'O102', 'ProductID: FC002, Quantity: 1, SubTotal: RM 8.00', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `DepartmentID` varchar(10) NOT NULL,
  `Department_NAME` varchar(100) NOT NULL,
  `Department_LOCATION` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`DepartmentID`, `Department_NAME`, `Department_LOCATION`) VALUES
('D001', 'Human Resources', 'New York'),
('D002', 'IT Support', 'San Francisco'),
('D003', 'Sales', 'Chicago');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrdersID` varchar(5) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `StaffID` int(11) DEFAULT NULL,
  `OrderDate` datetime NOT NULL DEFAULT current_timestamp(),
  `Status` enum('In Process','Completed','Canceled','Pending') NOT NULL DEFAULT 'Pending',
  `TotalAmount` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrdersID`, `customer_id`, `StaffID`, `OrderDate`, `Status`, `TotalAmount`) VALUES
('O001', 1, 1, '2024-11-10 00:00:00', 'Completed', 24.00),
('O003', 3, 3, '2024-11-12 00:00:00', 'Completed', 65.00),
('O005', 5, 5, '2024-11-14 00:00:00', 'Completed', 48.00),
('O007', 1, 1, '2024-11-16 00:00:00', 'Completed', 40.00),
('O008', 1, NULL, '2025-01-17 12:58:52', 'Canceled', 108.00),
('O046', 1, NULL, '2025-01-18 17:44:35', 'Completed', 14.00),
('O047', 1, NULL, '2025-01-18 18:17:35', 'Canceled', 108.00),
('O048', 1, NULL, '2025-01-18 22:10:54', 'Completed', 8.00),
('O049', 1, NULL, '2025-01-18 22:13:30', 'Completed', 13.00),
('O050', 1, NULL, '2025-01-18 22:15:38', 'Completed', 8.00),
('O051', 1, NULL, '2025-01-18 22:45:00', 'Completed', 13.00),
('O052', 1, NULL, '2025-01-18 22:47:06', 'Completed', 14.00),
('O053', 1, NULL, '2025-01-18 22:55:00', 'Completed', 8.00),
('O054', 1, NULL, '2025-01-18 23:01:31', 'Canceled', 8.00),
('O055', 1, NULL, '2025-01-18 23:16:37', 'Completed', 8.00),
('O056', 1, NULL, '2025-01-18 23:17:59', 'Completed', 8.00),
('O057', 1, NULL, '2025-01-18 23:22:20', 'Completed', 8.00),
('O058', 1, NULL, '2025-01-19 11:58:08', 'Completed', 8.00),
('O059', 1, NULL, '2025-01-19 11:58:41', 'Completed', 8.00),
('O060', 1, NULL, '2025-01-19 12:00:55', 'Canceled', 8.00),
('O061', 1, NULL, '2025-01-19 12:02:09', 'Completed', 8.00),
('O062', 1, NULL, '2025-01-19 12:09:52', 'Completed', 34.00),
('O063', 1, NULL, '2025-01-19 12:11:23', 'Completed', 8.00),
('O064', 1, NULL, '2025-01-19 12:16:22', 'Canceled', 13.00),
('O065', 1, NULL, '2025-01-19 12:53:54', 'Canceled', 68.00),
('O066', 1, NULL, '2025-01-19 13:40:43', 'Completed', 11.00),
('O067', 1, NULL, '2025-01-19 13:42:18', 'Completed', 11.00),
('O070', 1, NULL, '2025-01-19 13:52:07', 'Completed', 8.00),
('O071', 1, NULL, '2025-01-19 15:37:57', 'Completed', 22.00),
('O073', 1, NULL, '2025-01-19 15:53:00', 'Completed', 42.00),
('O074', 1, NULL, '2025-01-19 15:53:45', 'Canceled', 8.00),
('O075', 1, NULL, '2025-01-19 15:53:56', 'Completed', 8.00),
('O076', 1, NULL, '2025-01-19 15:54:05', 'Completed', 8.00),
('O077', 1, NULL, '2025-01-20 17:12:29', 'Canceled', 11.00),
('O078', 5, NULL, '2025-01-20 17:14:41', 'Completed', 8.00),
('O079', 1, NULL, '2025-01-20 17:51:10', 'Canceled', 8.00),
('O080', 1, NULL, '2025-01-20 17:51:15', 'Completed', 8.00),
('O081', 1, NULL, '2025-01-20 18:01:00', 'Canceled', 8.00),
('O082', 1, NULL, '2025-01-20 18:16:41', 'Canceled', 8.00),
('O083', 1, NULL, '2025-01-20 18:21:47', 'Completed', 8.00),
('O084', 0, NULL, '2025-01-20 18:29:32', 'Completed', 8.00),
('O087', 1, NULL, '2025-01-20 18:41:05', 'Completed', 8.00),
('O088', 1, NULL, '2025-01-20 18:48:50', 'In Process', 8.00),
('O089', 1, NULL, '2025-01-20 18:56:03', 'Completed', 8.00),
('O090', 0, NULL, '2025-01-20 19:04:37', 'Completed', 8.00),
('O091', 0, NULL, '2025-01-20 19:23:53', 'Completed', 8.00),
('O092', 0, NULL, '2025-01-20 19:28:28', 'Completed', 8.00),
('O093', 0, NULL, '2025-01-20 19:38:00', 'In Process', 8.00),
('O094', 1, NULL, '2025-01-22 17:37:42', 'Completed', 14.00),
('O095', 1, NULL, '2025-01-22 18:32:44', 'Canceled', 8.00),
('O096', 1, NULL, '2025-01-23 11:34:13', 'Completed', 8.00),
('O097', 1, NULL, '2025-01-23 11:53:38', 'Canceled', 8.00),
('O098', 1, NULL, '2025-01-23 12:02:34', 'Completed', 13.00),
('O099', 1, NULL, '2025-01-23 12:08:43', 'Pending', 11.00),
('O100', 1, NULL, '2025-01-23 12:11:58', 'Completed', 34.00),
('O101', 1, NULL, '2025-01-23 12:28:14', 'Pending', 14.00),
('O102', 1, NULL, '2025-01-23 13:23:32', 'Pending', 8.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_product`
--

CREATE TABLE `order_product` (
  `ProductID` varchar(5) NOT NULL,
  `OrdersID` varchar(5) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `PricePerUnit` decimal(10,2) NOT NULL,
  `SubTotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_product`
--

INSERT INTO `order_product` (`ProductID`, `OrdersID`, `Quantity`, `PricePerUnit`, `SubTotal`) VALUES
('FC001', 'O050', 1, 8.00, 8.00),
('FC001', 'O071', 1, 8.00, 8.00),
('FC001', 'O080', 1, 8.00, 8.00),
('FC001', 'O096', 1, 8.00, 8.00),
('FC002', 'O047', 1, 8.00, 8.00),
('FC002', 'O048', 1, 8.00, 8.00),
('FC002', 'O053', 1, 8.00, 8.00),
('FC002', 'O054', 1, 8.00, 8.00),
('FC002', 'O055', 1, 8.00, 8.00),
('FC002', 'O056', 1, 8.00, 8.00),
('FC002', 'O057', 1, 8.00, 8.00),
('FC002', 'O058', 1, 8.00, 8.00),
('FC002', 'O059', 1, 8.00, 8.00),
('FC002', 'O060', 1, 8.00, 8.00),
('FC002', 'O061', 1, 8.00, 8.00),
('FC002', 'O063', 1, 8.00, 8.00),
('FC002', 'O070', 1, 8.00, 8.00),
('FC002', 'O073', 1, 8.00, 8.00),
('FC002', 'O074', 1, 8.00, 8.00),
('FC002', 'O075', 1, 8.00, 8.00),
('FC002', 'O076', 1, 8.00, 8.00),
('FC002', 'O078', 1, 8.00, 8.00),
('FC002', 'O079', 1, 8.00, 8.00),
('FC002', 'O081', 1, 8.00, 8.00),
('FC002', 'O082', 1, 8.00, 8.00),
('FC002', 'O083', 1, 8.00, 8.00),
('FC002', 'O084', 1, 8.00, 8.00),
('FC002', 'O087', 1, 8.00, 8.00),
('FC002', 'O088', 1, 8.00, 8.00),
('FC002', 'O089', 1, 8.00, 8.00),
('FC002', 'O090', 1, 8.00, 8.00),
('FC002', 'O092', 1, 8.00, 8.00),
('FC002', 'O093', 1, 8.00, 8.00),
('FC002', 'O095', 1, 8.00, 8.00),
('FC002', 'O097', 1, 8.00, 8.00),
('FC002', 'O102', 1, 8.00, 8.00),
('FC003', 'O049', 1, 13.00, 13.00),
('FC003', 'O051', 1, 13.00, 13.00),
('FC003', 'O064', 1, 13.00, 13.00),
('FC003', 'O098', 1, 13.00, 13.00),
('FC004', 'O065', 2, 11.00, 22.00),
('FC004', 'O066', 1, 11.00, 11.00),
('FC004', 'O067', 1, 11.00, 11.00),
('FC004', 'O077', 1, 11.00, 11.00),
('FC004', 'O099', 1, 11.00, 11.00),
('FC005', 'O046', 1, 14.00, 14.00),
('FC005', 'O052', 1, 14.00, 14.00),
('FC005', 'O065', 1, 14.00, 14.00),
('FC005', 'O071', 1, 14.00, 14.00),
('FC005', 'O094', 1, 14.00, 14.00),
('FC005', 'O100', 1, 14.00, 14.00),
('FC005', 'O101', 1, 14.00, 14.00),
('FC006', 'O100', 1, 20.00, 20.00),
('FC008', 'O065', 1, 32.00, 32.00),
('FC010', 'O062', 1, 34.00, 34.00),
('FC011', 'O047', 1, 100.00, 100.00),
('SD004', 'O073', 1, 34.00, 34.00);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `ProductID` varchar(5) NOT NULL,
  `ProductName` varchar(50) NOT NULL,
  `ProductPrice` decimal(10,2) NOT NULL,
  `StockStatus` varchar(20) NOT NULL DEFAULT 'Available',
  `ProductImage` varchar(255) DEFAULT NULL,
  `Description` text DEFAULT NULL
) ;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`ProductID`, `ProductName`, `ProductPrice`, `StockStatus`, `ProductImage`, `Description`) VALUES
('FC001', 'Original', 8.00, 'Available', 'ori.jpg', NULL),
('FC002', 'Spicy', 8.00, 'Available', 'spicy.jpg', NULL),
('FC003', 'Korean Spicy', 13.00, 'Available', 'koreanSpicy.jpg', NULL),
('FC004', 'Honey Glazed', 11.00, 'Available', 'honey.jpg', NULL),
('FC005', 'Garlic Permesan', 14.00, 'Available', 'garlic.jpg', NULL),
('FC006', 'Original Happy Box', 20.00, 'Available', 'ori.jpg', NULL),
('FC007', 'Spicy Happy Box', 20.00, 'Available', 'spicy.jpg', NULL),
('FC008', 'Korean Spicy Happy Box', 32.00, 'Available', 'spicy.jpg', NULL),
('FC009', 'Honey Glazed Happy Box', 27.00, 'Available', 'honey.jpg', NULL),
('FC010', 'Garlic Permesan Happy Box', 34.00, 'Available', 'garlic.jpg', NULL),
('FC011', 'Peanurttt', 100.00, 'Available', 'friedchicken2.jpg', NULL),
('SD004', 'DALGONA CAKE', 40.00, 'Not Available', 'google.com', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staffID` int(11) NOT NULL,
  `Staff_FNAME` varchar(50) NOT NULL,
  `Staff_LNAME` varchar(50) NOT NULL,
  `Staff_PHONE` varchar(15) DEFAULT NULL,
  `Staff_HIREDATE` date DEFAULT NULL,
  `Staff_EMAIL` varchar(100) NOT NULL,
  `Staff_ROLE` varchar(50) DEFAULT NULL,
  `DepartmentID` varchar(10) DEFAULT NULL,
  `Username` varchar(50) NOT NULL,
  `Password_hash` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staffID`, `Staff_FNAME`, `Staff_LNAME`, `Staff_PHONE`, `Staff_HIREDATE`, `Staff_EMAIL`, `Staff_ROLE`, `DepartmentID`, `Username`, `Password_hash`) VALUES
(1, 'John', 'Doe', '555-1234', '2023-01-15', 'john.doe@example.com', 'HR Manager', 'D001', 'user_1', 'staff'),
(2, 'Jane', 'Smith', '555-5678', '2022-04-10', 'jane.smith@example.com', 'IT Specialist', 'D002', 'user_2', 'staff'),
(3, 'Alice', 'Brown', '555-8765', '2021-11-05', 'alice.brown@example.com', 'Sales Representative', 'D003', 'user_3', 'staff'),
(4, 'Emma', 'Watson', '111-222-3333', '2023-01-01', 'emmawatson@example.com', 'Manager', 'D001', '', ''),
(5, 'Liam', 'Neeson', '222-333-4444', '2023-02-01', 'liamneeson@example.com', 'Cashier', 'D002', '', ''),
(6, 'Sophia', 'Turner', '333-444-5555', '2023-03-01', 'sophiaturner@example.com', 'Chef', 'D003', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audittrail`
--
ALTER TABLE `audittrail`
  ADD PRIMARY KEY (`AuditID`),
  ADD KEY `OrdersID` (`OrdersID`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `StaffID` (`StaffID`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`DepartmentID`),
  ADD UNIQUE KEY `Department_NAME` (`Department_NAME`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrdersID`),
  ADD KEY `StaffID` (`StaffID`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_product`
--
ALTER TABLE `order_product`
  ADD PRIMARY KEY (`ProductID`,`OrdersID`),
  ADD KEY `OrdersID` (`OrdersID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`ProductID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staffID`),
  ADD UNIQUE KEY `Staff_EMAIL` (`Staff_EMAIL`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audittrail`
--
ALTER TABLE `audittrail`
  MODIFY `AuditID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=285;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audittrail`
--
ALTER TABLE `audittrail`
  ADD CONSTRAINT `audittrail_ibfk_2` FOREIGN KEY (`OrdersID`) REFERENCES `orders` (`OrdersID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `audittrail_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `rpos_customers` (`customer_id`),
  ADD CONSTRAINT `audittrail_ibfk_4` FOREIGN KEY (`StaffID`) REFERENCES `staff` (`staffID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `customer_id` FOREIGN KEY (`customer_id`) REFERENCES `rpos_customers` (`customer_id`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `rpos_customers` (`customer_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`StaffID`) REFERENCES `staff` (`staffID`);

--
-- Constraints for table `order_product`
--
ALTER TABLE `order_product`
  ADD CONSTRAINT `order_product_ibfk_2` FOREIGN KEY (`OrdersID`) REFERENCES `orders` (`OrdersID`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
