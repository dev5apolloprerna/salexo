-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 07, 2025 at 10:42 AM
-- Server version: 5.7.23-23
-- PHP Version: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smaar1u8_lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `card_payment`
--

CREATE TABLE `card_payment` (
  `id` int(11) NOT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `oid` int(11) NOT NULL DEFAULT '0' COMMENT 'Order id of order table',
  `razorpay_payment_id` varchar(255) DEFAULT NULL,
  `razorpay_order_id` varchar(255) DEFAULT NULL,
  `razorpay_signature` varchar(500) DEFAULT NULL,
  `receipt` varchar(255) DEFAULT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `currency` varchar(50) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `iPaymentType` int(11) NOT NULL DEFAULT '0' COMMENT '1:Online\r\n2:Offline\r\n3:Free',
  `Remarks` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `card_payment`
--

INSERT INTO `card_payment` (`id`, `order_id`, `oid`, `razorpay_payment_id`, `razorpay_order_id`, `razorpay_signature`, `receipt`, `amount`, `currency`, `status`, `iPaymentType`, `Remarks`, `created_at`, `updated_at`) VALUES
(2, 'order_RHt06ivpi6P52m', 15, 'pay_RHt0e4F07hZEZs', 'order_RHt06ivpi6P52m', 'ecfc6422bae7e3e23f48953b3e7013bfef7eff724aee72d9953dede1efa4cfc4', '15-20250915183012', 885, 'INR', 'Success', 1, 'Online Payment', '2025-09-15 13:00:13', '2025-09-15 13:00:59'),
(3, 'order_RHtl7O9EORJRKP', 16, 'pay_RHtlLHJS5AdGvU', 'order_RHtl7O9EORJRKP', '1e644333d68c8f5f647ac36da8bd1d89b5d252a8d83b8e9f2dbec48ab6c18d25', '16-20250915191442', 885, 'INR', 'Success', 1, 'Online Payment', '2025-09-15 13:44:43', '2025-09-15 13:45:12'),
(4, 'order_RHtqWT2xaWTmPD', 17, NULL, NULL, NULL, '17-20250915191949', 5900, 'INR', 'Fail', 0, 'Payment window closed', '2025-09-15 13:49:50', '2025-09-15 13:55:24'),
(5, 'order_RHuB6CVPfB4REX', 18, NULL, NULL, NULL, '18-20250915193918', 5900, 'INR', 'Fail', 0, 'Payment window closed', '2025-09-15 14:09:19', '2025-09-15 14:09:26');

-- --------------------------------------------------------

--
-- Table structure for table `company_client_master`
--

CREATE TABLE `company_client_master` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `GST` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_person_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Address` text COLLATE utf8_unicode_ci NOT NULL,
  `pincode` int(11) NOT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subscription_start_date` datetime NOT NULL,
  `subscription_end_date` datetime NOT NULL,
  `plan_id` int(11) NOT NULL,
  `plan_amount` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `plan_days` int(11) NOT NULL,
  `iStatus` tinyint(4) NOT NULL DEFAULT '1',
  `isDeleted` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `company_client_master`
--

INSERT INTO `company_client_master` (`company_id`, `company_name`, `GST`, `contact_person_name`, `mobile`, `email`, `Address`, `pincode`, `city`, `state_id`, `password`, `subscription_start_date`, `subscription_end_date`, `plan_id`, `plan_amount`, `plan_days`, `iStatus`, `isDeleted`, `created_at`, `updated_at`) VALUES
(5, 'Apollo Infotech', NULL, 'Krunal Shah', '9824773136', 'shahkrunal83@gmail.com', 'Maninagar', 380008, 'Ahmedabad', 1, '$2y$10$wtwp5bVe4tu8NclL638G9.C2KZwBZDw.GiXv4leiiFAC9i2faEPH2', '2025-09-26 17:36:00', '2026-09-26 17:36:00', 4, '5000', 365, 1, 0, '2025-07-03 10:58:23', '2025-09-26 17:36:00'),
(6, 'Navdeep Products', NULL, 'Kashyap Parikh', '9409202530', 'kashyap1790@gmail.com', 'Paldi', 380007, 'Ahmedabad', 1, '$2y$10$.GzO3tibHZCpbMjArNqQUeW95oHa1JIOEb6f2UwSnlUy1cbB2pEJ.', '2025-07-03 11:00:40', '2026-08-02 11:00:40', 1, '750', 30, 1, 0, '2025-07-03 11:00:40', '2025-09-15 19:15:12'),
(8, 'Testing', NULL, 'Test Apollo', '9837013233', 'test6@gmail.com', 'A-1 , Anubhav Flat , bhairavnath cross road , Maninagar , Ahmedabad\r\ntt', 382443, 'Ahmedabad', 1, '$2y$10$sHU8hKZ75g9XsPHXNNnyS.5ZAp8uQInsRcV28Ep99/BbQK.lZKP3G', '2025-07-03 12:57:36', '2026-07-03 12:57:36', 1, '5000', 365, 1, 0, '2025-07-03 12:57:37', '2025-07-24 11:09:38'),
(10, 'Innovex Business Advisers LLP', NULL, 'Sajjth Ezhava', '9979294421', 'sajith@ebca.in', 'Ahmedabad', 382443, 'ahmedabad', 1, '$2y$10$/wcDBtRCbtlXdR9.l7mzweuSerNJmmsVBBlsI1eOvKfjGr8AOWtuq', '2025-08-05 15:08:50', '2026-08-05 15:08:50', 1, '5000', 365, 1, 0, '2025-08-05 15:08:50', '2025-08-07 15:51:44'),
(11, 'grapits', NULL, 'Keyur soni', '9898540008', 'info@grapits.com', 'K-102 S.G Business Hub,Gota Ahmedabad', 380078, 'ahm', 1, '$2y$10$gjEHhJ0FL4SzU2q1Z27B.ureD2cSPe8LarldCCvzkoM31.CMH3M6G', '2025-08-05 17:34:59', '2026-08-05 17:34:59', 1, '5000', 365, 1, 0, '2025-08-05 17:34:59', '2025-08-05 17:34:59'),
(12, 'Groath Spectrum Pvt Ltd', NULL, 'Ruchi shah', '7405067311', 'ruchi.shah@groath.in', '307, Titanium One , Pakvan cross roads , SG highway , Ahmedabad', 380054, 'ahmedabad', 1, '$2y$10$2FcjL4ZYnsglRszN6Ipzw.t8d5VF1SIZn/LfGHjR1HF.vyNYIfsL6', '2025-08-08 20:09:56', '2026-08-08 20:09:56', 1, '5000', 365, 1, 0, '2025-08-08 20:09:57', '2025-08-08 20:09:57'),
(13, 'vacoholic tours and travels', NULL, 'parth shah', '9429355643', 'vacoholi@gmail.com', 'ahmedabad', 380054, 'Ahmedabad', 1, '$2y$10$D3xEEfw5RhCdkuUOXpTpNu8pf9.E5a7fyBAB1ON6cS4VApGLjf.oW', '2025-08-21 14:06:04', '2026-08-21 14:06:04', 1, '5000', 365, 1, 0, '2025-08-21 14:06:04', '2025-08-21 14:06:04'),
(14, 'Py Engineering Co', NULL, 'Yash', '9016970829', 'pyengineering@zoho.com', '102-B, Harikrupa Shopping Center Beside City Gold, Ashram Road, Ahmedabad.', 380009, 'Ahmedabad', 1, '$2y$10$Ioq1YR90fkfsYIryGvw2sO6Hk1RL5lpykUVqsz9CCIw1zjlPR5JQm', '2025-09-01 15:09:45', '2026-09-01 15:09:45', 1, '5000', 365, 1, 0, '2025-09-01 15:09:45', '2025-09-01 15:09:45'),
(15, 'Tarang Enterprise', NULL, 'Tarang Parmar', '1234567890', 'dev2.apolloinfotech@gmail.com', 'A-1 , Anubhav Flat , bhairavnath cross road , Maninagar , Ahmedabad', 380028, 'Ahmedabad', 1, '$2y$10$KJQqmUi7O7nMFzNrve8mpODkno.g4wh8HB4oLNeC6tOgDEHULRjvK', '2025-09-15 18:31:00', '2025-10-15 18:31:00', 2, '750', 30, 1, 0, '2025-09-15 18:31:00', '2025-09-15 18:31:00'),
(16, 'Outline Business Communications', NULL, 'Deepak Mudaliar', '9925111567', 'deepak@outlinebusiness.com', '506, Satya One Complex, opp. Manav Mandir Road, Sushil Nagar Society, Memnagar, Ahmedabad, Gujarat', 380052, 'Ahmedabad', 1, '$2y$10$WqPyY95CN.Lf8m49BCGNF.Ls7h.AjF.vUFzw31IRVQxvWYUermCnq', '2025-09-16 18:02:21', '2025-10-16 18:02:21', 2, '750', 30, 1, 0, '2025-09-16 18:02:21', '2025-09-16 18:02:21');

-- --------------------------------------------------------

--
-- Table structure for table `deal_cancel`
--

CREATE TABLE `deal_cancel` (
  `lead_id` int(11) NOT NULL,
  `iCustomerId` int(11) NOT NULL DEFAULT '0' COMMENT 'as a company_id',
  `iemployeeId` int(11) NOT NULL DEFAULT '0',
  `company_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `GST_No` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `alternative_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8_unicode_ci,
  `product_service_id` int(11) NOT NULL DEFAULT '0',
  `LeadSourceId` int(11) NOT NULL DEFAULT '0',
  `lead_history_id` int(11) NOT NULL DEFAULT '0',
  `comments` text COLLATE utf8_unicode_ci,
  `followup_by` int(11) DEFAULT '0',
  `next_followup_date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `cancel_reason_id` int(11) NOT NULL DEFAULT '0',
  `amount` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iStatus` int(11) NOT NULL DEFAULT '1',
  `isDelete` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_id` int(11) NOT NULL DEFAULT '0',
  `initially_contacted` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iEnterBy` int(11) NOT NULL DEFAULT '0',
  `deal_converted_at` timestamp NULL DEFAULT NULL,
  `deal_done_at` timestamp NULL DEFAULT NULL,
  `deal_cancel_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `deal_cancel`
--

INSERT INTO `deal_cancel` (`lead_id`, `iCustomerId`, `iemployeeId`, `company_name`, `GST_No`, `customer_name`, `email`, `mobile`, `address`, `alternative_no`, `remarks`, `product_service_id`, `LeadSourceId`, `lead_history_id`, `comments`, `followup_by`, `next_followup_date`, `status`, `cancel_reason_id`, `amount`, `iStatus`, `isDelete`, `created_at`, `updated_at`, `employee_id`, `initially_contacted`, `iEnterBy`, `deal_converted_at`, `deal_done_at`, `deal_cancel_at`) VALUES
(11, 5, 5, 'Demo Apollo', '111222333444555', 'Demo Mignesh', NULL, '9904500629', NULL, '1234567890', 'Test remarks', 6, 2, 23, 'Test Demo Cancel Deal..', 5, NULL, 20, 14, '0', 1, 0, '2025-07-22 05:01:09', NULL, 5, 'Yes', 5, NULL, NULL, NULL),
(10, 5, 5, NULL, '0', 'Mr Om Acharya', NULL, '8320740711', NULL, NULL, 'Looking for dynamic website design and development', 5, 1, 32, 'Partner comments needs , out of budget , now looking for static website', 5, NULL, 20, 15, '0', 1, 0, '2025-07-11 12:04:47', NULL, 5, 'Yes', 5, NULL, NULL, NULL),
(1, 5, 5, 'Jess Control', NULL, 'Abhishek Bhavsar', NULL, '7490082940', 'Vatva', NULL, 'Quotation for Quotation software send\r\nTry to sold out LMS', 6, 1, 34, 'he is now looking for other options , some ready software', 5, NULL, 20, 15, '0', 1, 0, '2025-07-03 05:37:02', NULL, 0, NULL, 5, NULL, NULL, NULL),
(22, 6, 6, 'Radhe Radhe Company', 'GST123123RADHE123', 'Radhe Shyam', 'radhe123@gmail.com', '1235468768', NULL, NULL, 'remartrs testing comment on this song remix in the world of the day of the day', 19, 11, 59, 'deal cancel', 6, NULL, 24, 18, '0', 1, 0, '2025-07-24 06:08:27', NULL, 10, 'Yes', 6, NULL, NULL, NULL),
(26, 5, 5, NULL, NULL, 'Nikunj bhai', NULL, '9712402309', NULL, NULL, 'semi e-commerce website\nfront product add to list send quotation to client', 5, 1, 68, 'they deal with other company\r\nmay be we give them long time to complete the project.', 5, NULL, 20, 14, '0', 1, 0, '2025-07-24 09:25:52', NULL, 5, 'Yes', 5, NULL, NULL, '2025-07-25 07:30:58'),
(41, 6, 6, 'test apollo', NULL, 'meet', NULL, '8080808080', NULL, NULL, '-', 20, 16, 70, 'Deal cancel.......', 6, NULL, 24, 17, '0', 1, 0, '2025-07-25 10:37:30', NULL, 6, 'No', 6, NULL, NULL, NULL),
(42, 6, 6, NULL, NULL, 'Prit patel', NULL, '6060606060', NULL, NULL, '-', 21, 16, 75, 'no interest', 6, NULL, 24, 16, '0', 1, 0, '2025-07-25 10:38:04', NULL, 6, 'No', 6, NULL, NULL, NULL),
(17, 6, 6, 'Patel Brother PTV LTD', 'GS12345134PATEL', 'Mignesh Chhatrala', 'mignesh123@gmail.com', '9904500629', NULL, '1234567890', 'remarks test comment', 17, 14, 84, 'deal cancel......', 6, NULL, 24, 16, '0', 1, 0, '2025-07-24 05:51:09', NULL, 6, 'Yes', 6, NULL, NULL, NULL),
(53, 11, 18, 'NAVPAD ENTERPRISE', '0', 'NAVPAD ENTERPRISE', NULL, '9316273183', 'HIMMATNAGAR', NULL, 'As per requirement \r\nSpary Paint And Perfomer Roll SS trowel \r\nWe will send spary Paint And Perfomer Roll Yellow Perfomer Invoice Date 08-08-25\r\nparty most requirement Small S S Trowel', 52, 23, 95, 'He said  not pay Advance payment Then Deal cancel', 18, NULL, 41, 31, '0', 1, 0, '2025-08-08 09:12:46', NULL, 19, 'Yes', 18, NULL, NULL, '2025-08-12 09:54:11'),
(14, 5, 5, NULL, '0', 'Veer', NULL, '7069662718', NULL, NULL, 'website requiremdnt', 5, 1, 111, 'not responding', 5, NULL, 20, 13, '0', 1, 0, '2025-07-22 12:55:47', NULL, 5, 'Yes', 5, NULL, NULL, '2025-08-26 11:02:30'),
(40, 5, 5, NULL, '0', 'Harsh', NULL, '7096156357', NULL, NULL, 'E-commerce website', 5, 1, 112, 'not picking up the phone since logn', 5, NULL, 20, 13, '0', 1, 0, '2025-07-25 10:04:39', NULL, 5, 'Yes', 5, NULL, NULL, '2025-08-26 11:03:44'),
(32, 5, 5, NULL, '0', 'Namra', NULL, '9023329085', NULL, NULL, 'Plastic website', 5, 1, 113, 'not picking up the phone', 5, NULL, 20, 13, '0', 1, 0, '2025-07-24 12:49:51', NULL, 5, 'Yes', 5, NULL, NULL, '2025-08-26 11:05:29'),
(35, 5, 5, NULL, '0', 'Mr Vasu', NULL, '6353101820', NULL, NULL, 'for website development', 5, 1, 114, 'WE are not working in that technology', 5, NULL, 20, 13, '0', 1, 0, '2025-07-25 06:58:22', NULL, 5, 'Yes', 5, NULL, NULL, '2025-08-26 11:06:06'),
(67, 14, 22, 'Demo', '0', 'test test', 'tets@gmail.com', '9898989898', 'ahmedabad', NULL, 'test', 55, 28, 118, 'test', 22, NULL, 57, 39, '0', 1, 0, '2025-09-01 11:26:12', NULL, 22, 'No', 22, NULL, NULL, '2025-09-01 11:30:09'),
(69, 14, 22, 'NEO ENGINEERING SERVICES', '0', 'Jain Tanvi Jigneshbhai', NULL, '8401317797', 'Sanand, Gujarat, India', NULL, 'WHATSAPP', 59, 27, 210, 'rate issue', 22, '30-09-2025 12:00 PM', 57, 41, '0', 1, 0, '2025-09-04 11:12:28', NULL, 23, 'Yes', 22, NULL, NULL, '2025-09-30 08:56:20'),
(71, 14, 22, 'AASHUTOSH INTERIORS', '0', 'Pankaj', NULL, '997938777', 'Vadodara, Gujarat, India', NULL, 'resell', 59, 27, 211, 'not require now', 22, NULL, 57, 39, '0', 1, 0, '2025-09-04 11:22:50', NULL, 23, 'Yes', 22, NULL, NULL, '2025-09-30 08:56:50'),
(159, 14, 22, 'KML UNIT 7', '0', 'PARSOTAM', NULL, '9054455964', 'BECHRAJI', NULL, 'TROLLEY', 55, 35, 227, 'WILL TELL LATER AFTR APPROVAL', 22, NULL, 57, 40, '0', 1, 0, '2025-09-25 12:14:33', NULL, 22, 'Yes', 22, NULL, NULL, '2025-10-04 08:07:42'),
(148, 14, 22, 'SUSPA', '0', 'ANAND', NULL, '9384360870', 'SANAND', NULL, 'UNDERAPPROVAL', 61, 35, 228, 'WILL TELL IF ANY LATER', 22, NULL, 57, 40, '0', 1, 0, '2025-09-25 11:45:38', '2025-09-25 11:45:50', 22, 'Yes', 22, NULL, NULL, '2025-10-04 08:08:26');

-- --------------------------------------------------------

--
-- Table structure for table `deal_done`
--

CREATE TABLE `deal_done` (
  `lead_id` int(11) NOT NULL,
  `iCustomerId` int(11) NOT NULL DEFAULT '0' COMMENT 'as a company_id',
  `iemployeeId` int(11) NOT NULL DEFAULT '0',
  `company_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `GST_No` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `alternative_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8_unicode_ci,
  `product_service_id` int(11) NOT NULL DEFAULT '0',
  `LeadSourceId` int(11) NOT NULL DEFAULT '0',
  `lead_history_id` int(11) NOT NULL DEFAULT '0',
  `comments` text COLLATE utf8_unicode_ci,
  `followup_by` int(11) DEFAULT '0',
  `next_followup_date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `cancel_reason_id` int(11) NOT NULL DEFAULT '0',
  `amount` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iStatus` int(11) NOT NULL DEFAULT '1',
  `isDelete` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_id` int(11) NOT NULL DEFAULT '0',
  `initially_contacted` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iEnterBy` int(11) NOT NULL DEFAULT '0',
  `deal_converted_at` timestamp NULL DEFAULT NULL,
  `deal_done_at` timestamp NULL DEFAULT NULL,
  `deal_cancel_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `deal_done`
--

INSERT INTO `deal_done` (`lead_id`, `iCustomerId`, `iemployeeId`, `company_name`, `GST_No`, `customer_name`, `email`, `mobile`, `address`, `alternative_no`, `remarks`, `product_service_id`, `LeadSourceId`, `lead_history_id`, `comments`, `followup_by`, `next_followup_date`, `status`, `cancel_reason_id`, `amount`, `iStatus`, `isDelete`, `created_at`, `updated_at`, `employee_id`, `initially_contacted`, `iEnterBy`, `deal_converted_at`, `deal_done_at`, `deal_cancel_at`) VALUES
(13, 5, 5, 'Demo Apollo 2', '1212455APOLO1212', 'Apollo Testing 2', 'apollotest123@gmail.com', '8888888888', NULL, '1111111111', 'TEST Remarks testing', 7, 8, 30, 'Deal Done Successfully', 5, NULL, 18, 0, '5000', 1, 0, '2025-07-22 10:23:29', NULL, 5, 'Yes', 5, NULL, NULL, NULL),
(15, 5, 5, 'Zenith Solutions Pvt Ltd', '0', 'Rahul Sharma', NULL, '9876543210', NULL, NULL, 'Interested in CRM package, asked for a demo next week', 5, 1, 39, 'Test again', 5, NULL, 18, 0, '25000', 1, 0, '2025-07-22 13:09:27', NULL, 5, 'Yes', 5, NULL, NULL, NULL),
(16, 5, 5, 'NovaTech Innovations', '0', 'Sneha Desai', NULL, '9845123456', NULL, NULL, 'Asked for a product brochure', 6, 1, 41, 'Test', 5, NULL, 18, 0, '30000', 1, 0, '2025-07-22 13:21:01', NULL, 5, 'Yes', 5, NULL, NULL, NULL),
(20, 6, 6, 'Apollo PVT LTD', 'GST4443311APO300', 'Tarang Permar', 'tarangparmar123@gmail.com', '9904500629', NULL, '1234567890', 'remartrs testing comment on this song remix', 20, 10, 58, 'deal done', 6, NULL, 22, 0, '5000', 1, 0, '2025-07-24 06:00:53', NULL, 6, 'Yes', 6, NULL, NULL, NULL),
(27, 6, 6, NULL, NULL, 'Test meet', NULL, '9904599045', NULL, NULL, '-', 17, 14, 71, 'Deal done.......', 6, NULL, 22, 0, '15000', 1, 0, '2025-07-24 12:27:42', NULL, 6, 'No', 6, NULL, NULL, NULL),
(5, 5, 5, NULL, '0', 'Kamlesh Bhai Ref Google', NULL, '7600029299', NULL, NULL, 'looking product display website with product code and product have multi photo', 5, 1, 76, 'Deal Done', 5, NULL, 18, 0, '0', 1, 0, '2025-07-09 05:57:53', NULL, 5, 'Yes', 5, NULL, '2025-07-25 15:09:12', NULL),
(19, 6, 6, 'Chhatrala PTV. LTD.', 'GST99900CHH100', 'Rutvik Chhatrala', 'migneshpatel202@gmail.com', '9904500629', NULL, '1414141141', 'Remartrs testing comment', 16, 13, 81, 'done', 6, NULL, 22, 0, '20000', 1, 0, '2025-07-24 05:57:23', NULL, 10, 'Yes', 6, NULL, '2025-07-26 10:29:44', NULL),
(38, 6, 6, NULL, NULL, 'aaaaaaa', NULL, '1111111111', NULL, NULL, '-', 15, 14, 82, 'done.', 6, NULL, 22, 0, '200000', 1, 0, '2025-07-25 07:26:17', NULL, 6, 'No', 6, NULL, '2025-07-26 10:34:41', NULL),
(54, 11, 18, 'Barma Hardware (Junagadh)', '24AHBPV7644M1ZS', 'Barma Hardware (Junagadh)', NULL, '9998402452', 'Junagadh', NULL, '18/08/2025 Follow Up For New Order', 29, 24, 99, 'High Gloss Paint Order', 18, NULL, 39, 0, '8184', 1, 0, '2025-08-12 09:40:55', NULL, 19, 'No', 18, NULL, '2025-08-13 07:15:27', NULL),
(33, 5, 12, 'Apollo', NULL, 'krunL', NULL, '9824773136', NULL, NULL, 'test', 5, 9, 101, '9824773136', 5, NULL, 18, 0, '0', 1, 0, '2025-07-25 04:52:20', NULL, 12, 'No', 12, NULL, '2025-08-16 12:29:33', NULL),
(34, 5, 5, NULL, '0', 'Rahul Dixit', NULL, '857000464', NULL, NULL, 'App develoment', 6, 1, 102, '9824773136', 5, NULL, 18, 0, '0', 1, 0, '2025-07-25 06:57:12', NULL, 5, 'Yes', 5, NULL, '2025-08-16 12:30:07', NULL),
(185, 12, 20, 'Prannuts', '0', 'Pradeep', 'Prahitdfc@gmail.com', '9825482424', NULL, NULL, 'Referred by Madhavi Kapoor', 0, 31, 175, 'Joined Optima', 20, NULL, 45, 0, '38000', 1, 0, '2025-09-26 09:34:45', NULL, 26, 'No', 20, NULL, '2025-09-26 11:30:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_master`
--

CREATE TABLE `employee_master` (
  `emp_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `guid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emp_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emp_mobile` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emp_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emp_loginId` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isCompanyAdmin` int(11) NOT NULL DEFAULT '0',
  `can_access_LMS` int(11) NOT NULL DEFAULT '1',
  `role_id` int(11) NOT NULL DEFAULT '0',
  `otp` int(11) NOT NULL DEFAULT '0',
  `otp_expire_time` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firebaseDeviceToken` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iStatus` int(11) NOT NULL DEFAULT '1',
  `isDelete` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `employee_master`
--

INSERT INTO `employee_master` (`emp_id`, `company_id`, `guid`, `emp_name`, `emp_mobile`, `emp_email`, `emp_loginId`, `password`, `isCompanyAdmin`, `can_access_LMS`, `role_id`, `otp`, `otp_expire_time`, `firebaseDeviceToken`, `iStatus`, `isDelete`, `created_at`, `updated_at`, `last_login`) VALUES
(5, 5, '3a407839-860f-4312-a33f-748e8ca12345', 'Apollo Infotech', '9824773136', 'shahkrunal83@gmail.com', 'shahkrunal83@gmail.com', '$2y$10$wtwp5bVe4tu8NclL638G9.C2KZwBZDw.GiXv4leiiFAC9i2faEPH2', 1, 1, 2, 8313, '2025-07-16 14:58:35', 'fxF_MmaBRbGmUky7EmXktK:APA91bGXOzNYcoBOrS4ycEJ5iUMBaTnZtME1kh2yCmDOphpNDerIwRuU4-WHICtY4ZLYlNy-5ZOsgdqmobwcCU2SFDytF0CSKJSeYY4CdacQr7Yr8sHnCoA', 1, 0, '2025-07-03 05:28:23', '2025-10-04 16:49:15', '2025-10-04 16:49:15'),
(6, 6, '542fd323-64d2-4ff0-9a5e-54234a015a74', 'Navdeep Products', '9409202530', 'kashyap1790@gmail.com', 'kashyap1790@gmail.com', '$2y$10$.GzO3tibHZCpbMjArNqQUeW95oHa1JIOEb6f2UwSnlUy1cbB2pEJ.', 1, 1, 2, 0, NULL, 'dvfuZf5ZSM2tAlAv2hAfAK:APA91bEF7sNMPtlBongQFBDQ6vZj2_NjTi3bNVeBPZqGfKvXG28jFoUxdgdNrshbxsrIGs9yaelNJY4IKjbCnn4JNfL9wsyotbM0sYNb0IEyqZUcnpbzH7k', 1, 0, '2025-07-03 05:30:40', '2025-10-03 10:03:22', '2025-07-29 09:05:19'),
(9, 8, '1b8d138c-01d7-4dda-9002-5fe510ecfc28', 'Testing', '9725123569', 'test6@gmail.com', 'test6@gmail.com', '$2y$10$oFBf3uWAliiEGsaKyO4WxOPZJJfb7BzFSjt2b1g4IA0lANFoXMVfO', 1, 1, 2, 0, NULL, NULL, 1, 0, '2025-07-03 07:27:37', '2025-10-03 10:03:22', '2025-09-05 05:04:14'),
(11, 8, '04347e91-6fa2-4e8b-b706-1db15c997e42', 'ketan patel', '9856231245', 'ketan@gmail.com', NULL, '$2y$10$equjIvQ4KhJJvKmqVfcRB.pQM.3UnqeyupybXWulYmYAU4jDveGru', 0, 1, 3, 0, NULL, NULL, 1, 0, '2025-07-08 11:20:27', '2025-10-03 10:03:22', NULL),
(12, 5, '94e8c0f4-f5c2-4fd5-b6a0-e930572bc237', 'Krupali', '9427534693', NULL, NULL, '$2y$10$svQHLCBAs577qnyON59iceiUxVySoQAbY792qHrHlHWa32DfiuSsm', 0, 1, 3, 0, NULL, 'fsREBfhPQyupJfj6qRAq_R:APA91bEmbckvPieDdYM8-fMxwrpn3FioEh6LJkisnPFVPxvHxuUD9DkBVstKIjV211-s7lMuBaCOmY8j00hHAScJPd_O83AEugD1odKhgnSsvRBQhT2rTOM', 1, 0, '2025-07-10 09:48:14', '2025-10-03 10:03:22', '2025-09-05 18:18:05'),
(13, 6, '31f690ad-79f4-46ad-9727-d76e68ced15b', 'Mukesh', '9898289098', 'admin@gmail.com', NULL, '$2y$10$ghdIwkTCv93izrfvc8mtseU8nV2TUWZyp09U0BwtwsFlLaYi.P9x.', 0, 1, 3, 0, NULL, 'droTDcAKQIKCwNYPTKW3TI:APA91bEObBQPF4JCc6Rg5PTtTkFILTRexHFjs-no53byACF4vvLCdLQ54GGf7XNBEs6DQtgvs7QrXIlgBnUFiR3bKcJ5j_AUvEqUSQm5Xu3Mv_7o3vpDgJM', 1, 0, '2025-07-25 08:46:25', '2025-10-03 10:03:22', '2025-07-26 10:21:50'),
(15, 6, '0e4dc6af-98f8-4075-a9a1-29bc4ba54732', 'Lokesh', '9876543210', NULL, NULL, '$2y$10$dpRalljWHLFDHMIZEbL4Leua35LnKheqn2gaOlLOGbeOqt7/oCEku', 0, 1, 3, 0, NULL, NULL, 1, 0, '2025-07-28 09:35:29', '2025-10-03 10:03:22', NULL),
(17, 10, 'ee07c14d-8eb8-4b37-b205-9a80b5e8c150', 'Innovex Business Advisers LLP', '9979294421', 'sajith@ebca.in', 'sajith@ebca.in', '$2y$10$/wcDBtRCbtlXdR9.l7mzweuSerNJmmsVBBlsI1eOvKfjGr8AOWtuq', 1, 1, 2, 0, NULL, NULL, 1, 0, '2025-08-05 09:38:50', '2025-10-03 10:03:22', NULL),
(18, 11, '37b2503e-e02c-4d87-b3d0-4e7adc201379', 'grapits', '9898540008', 'info@grapits.com', 'info@grapits.com', '$2y$10$gjEHhJ0FL4SzU2q1Z27B.ureD2cSPe8LarldCCvzkoM31.CMH3M6G', 1, 1, 2, 0, NULL, NULL, 1, 0, '2025-08-05 12:04:59', '2025-10-03 10:03:22', NULL),
(19, 11, '9f4059b1-b01f-477e-a751-95b516b43cdc', 'Galaxy comfort Team', '9016468190', NULL, NULL, '$2y$10$vlIpNiyFyZRWzSyW.0pS6eyVjmCIhGQtZTG/ySrfDUzuV1q6j92I6', 0, 1, 3, 0, NULL, NULL, 1, 0, '2025-08-08 06:20:45', '2025-10-03 10:03:22', NULL),
(20, 12, '7dd184b9-b351-471f-b680-031542d9846a', 'Groath Spectrum Pvt Ltd', '7405067311', 'ruchi.shah@groath.in', 'ruchi.shah@groath.in', '$2y$10$2FcjL4ZYnsglRszN6Ipzw.t8d5VF1SIZn/LfGHjR1HF.vyNYIfsL6', 1, 1, 2, 0, NULL, NULL, 1, 0, '2025-08-08 14:39:57', '2025-10-03 10:03:22', NULL),
(21, 13, 'a1993b89-fcda-4d1d-836b-41a2894df276', 'vacoholic tours and travels', '9429355643', 'vacoholi@gmail.com', 'vacoholi@gmail.com', '$2y$10$D3xEEfw5RhCdkuUOXpTpNu8pf9.E5a7fyBAB1ON6cS4VApGLjf.oW', 1, 1, 2, 0, NULL, NULL, 1, 0, '2025-08-21 08:36:04', '2025-10-03 10:03:22', NULL),
(22, 14, '29ff85f9-1694-43d2-a2ad-e88c661b631a', 'Py Engineering Co', '9016970829', 'pyengineering@zoho.com', 'pyengineering@zoho.com', '$2y$10$Ioq1YR90fkfsYIryGvw2sO6Hk1RL5lpykUVqsz9CCIw1zjlPR5JQm', 1, 1, 2, 0, NULL, 'eW2rOyfLS_e3arf5tky3vu:APA91bHsJbiwVzawQioWibre-BWucgLZ5qMe9mpGuucaCRU0CHVWs6d8oAFfedWR5JlrWgF_OLdzTIyWg2bS-9Ob2V2Xp2Nb578KWC6YvLnViu9uF34LWRA', 1, 0, '2025-09-01 09:39:45', '2025-10-03 10:03:22', '2025-09-26 09:24:01'),
(23, 14, 'e7a5d1e3-b8ca-4159-8640-005a34390073', 'karan', '9978948820', 'pyengineeringco@gmail.com', NULL, '$2y$10$QLl3X/XhS9W7uLhc91vR9.S4GrLPldrE02kN61DIUemswdrhhFN2y', 0, 1, 3, 0, NULL, NULL, 1, 0, '2025-09-04 07:46:37', '2025-10-03 10:03:22', NULL),
(24, 15, 'c5e9467e-0324-4461-8829-7f7ff28b7268', 'Tarang Parmar', '1234567890', 'dev2.apolloinfotech@gmail.com', 'dev2.apolloinfotech@gmail.com', '$2y$10$xetM9kvGQ9tei6tsGCVunezq0SN9ldnGBr9RvHz6ocE6V5fAlf15S', 1, 1, 2, 0, NULL, NULL, 1, 0, '2025-09-15 13:01:00', '2025-10-03 10:03:22', NULL),
(25, 16, 'cca318a3-6249-421b-b72d-7b6cc3afee3a', 'Outline Business Communications', '9925111567', 'deepak@outlinebusiness.com', 'deepak@outlinebusiness.com', '$2y$10$WqPyY95CN.Lf8m49BCGNF.Ls7h.AjF.vUFzw31IRVQxvWYUermCnq', 1, 1, 2, 0, NULL, NULL, 1, 0, '2025-09-16 12:32:21', '2025-10-03 10:03:22', NULL),
(26, 12, '23254cd6-2b71-447a-94a7-021954fd8920', 'Member Reference', '9974897311', NULL, NULL, '$2y$10$/fSZkmXS8iPDu0yNDiZryeD9cA7V7cncPgEkHmJVkboHbZ43Bg0yu', 0, 1, 3, 0, NULL, NULL, 1, 0, '2025-09-25 10:37:21', '2025-10-03 10:03:22', NULL),
(27, 14, '58779a76-03c2-4a1e-b693-ad55f785a828', 'SAGAR', '7069821687', 'pyengineeringco@gmail.com', NULL, '$2y$10$sB6lLhCI7xBDxJ4TYj9JaeTVXoonadyfDbF4JyqhzZiEGuVrWnbUi', 0, 1, 3, 0, NULL, NULL, 1, 0, '2025-09-30 07:17:49', '2025-10-03 10:03:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_cancel_reason`
--

CREATE TABLE `lead_cancel_reason` (
  `lead_cancel_reason_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `reason` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lead_cancel_reason`
--

INSERT INTO `lead_cancel_reason` (`lead_cancel_reason_id`, `company_id`, `reason`) VALUES
(1, 1, 'No longer interested'),
(2, 1, 'Budget constraints'),
(3, 1, NULL),
(4, 2, 'No longer interested'),
(5, 2, 'Budget constraints'),
(6, 2, 'Project postponed or canceled'),
(7, 3, 'No longer interested'),
(8, 3, 'Budget constraints'),
(9, 3, 'Project postponed or canceled'),
(10, 4, 'No longer interested'),
(11, 4, 'Budget constraints'),
(12, 4, 'Project postponed or canceled'),
(13, 5, 'No longer interested'),
(14, 5, 'Budget constraints'),
(15, 5, 'Project postponed or canceled'),
(16, 6, 'No longer interested'),
(17, 6, 'Budget constraints'),
(19, 7, 'No longer interested'),
(20, 7, 'Budget constraints'),
(21, 7, 'Project postponed or canceled'),
(22, 8, 'No longer interested'),
(23, 8, 'Budget constraints'),
(24, 8, 'Project postponed or canceled'),
(26, 10, 'No longer interested'),
(27, 10, 'Budget constraints'),
(28, 10, 'Project postponed or canceled'),
(29, 11, 'No longer interested'),
(30, 11, 'Budget constraints'),
(31, 11, 'Project postponed or canceled'),
(32, 12, 'No longer interested'),
(33, 12, 'Budget constraints'),
(35, 13, 'No longer interested'),
(36, 13, 'Budget constraints'),
(37, 13, 'Project postponed or canceled'),
(38, 14, 'No longer interested'),
(39, 14, 'Budget constraints'),
(40, 14, 'Project postponed or canceled'),
(41, 14, 'price high'),
(42, 15, 'No longer interested'),
(43, 15, 'Budget constraints'),
(44, 15, 'Project postponed or canceled'),
(45, 16, 'No longer interested'),
(46, 16, 'Budget constraints'),
(47, 16, 'Project postponed or canceled'),
(48, 12, 'Category Clash with Interested Chapter'),
(49, 12, 'No good market review'),
(50, 12, 'Called just for the sake of Inquiry'),
(51, 12, 'No proper response to our messages');

-- --------------------------------------------------------

--
-- Table structure for table `lead_history`
--

CREATE TABLE `lead_history` (
  `iLeadHistoryId` int(11) NOT NULL,
  `iLeadId` int(11) NOT NULL DEFAULT '0',
  `iCustomerId` int(11) NOT NULL DEFAULT '0',
  `Comments` text COLLATE utf8_unicode_ci,
  `followup_by` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `next_followup_date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `cancel_reason_id` int(11) NOT NULL DEFAULT '0',
  `amount` int(11) NOT NULL DEFAULT '0',
  `iStatus` int(11) NOT NULL DEFAULT '1',
  `isDelete` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `iEnterBy` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lead_history`
--

INSERT INTO `lead_history` (`iLeadHistoryId`, `iLeadId`, `iCustomerId`, `Comments`, `followup_by`, `next_followup_date`, `status`, `cancel_reason_id`, `amount`, `iStatus`, `isDelete`, `created_at`, `updated_at`, `iEnterBy`) VALUES
(46, 0, 6, 'Meeting with Tarang parmar \nDate : 24 /07 time 11 : 40', '0', '24-07-2025 11:40 AM', 23, 0, 0, 1, 0, '2025-07-24 11:30:53', '2025-07-24 11:30:53', 6),
(2, 0, 5, 'Office visited call and confirm the project', NULL, '10-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-08 16:30:31', '2025-07-08 16:30:31', 0),
(3, 0, 5, 'Meeting today and get value', NULL, '09-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-09 11:27:53', '2025-07-09 11:27:53', 0),
(4, 5, 5, 'Visited to client, quotation given they will informed us', NULL, '10-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-09 19:18:05', '2025-07-09 19:18:05', 0),
(5, 1, 5, 'Call and ask for quotation management software', NULL, '14-07-2025 4:00 PM', 19, 0, 0, 1, 0, '2025-07-10 10:49:52', '2025-07-10 10:49:52', 0),
(6, 0, 5, 'Call him back if back if payment not received', NULL, '10-07-2025 4:00 PM', 19, 0, 0, 1, 0, '2025-07-10 14:19:03', '2025-07-10 14:19:03', 0),
(7, 5, 5, 'He wil discuss with his head and confirm on monday', NULL, '14-07-2025 3:00 PM', 19, 0, 0, 1, 0, '2025-07-10 14:48:34', '2025-07-10 14:48:34', 0),
(8, 4, 5, 'Quotation around 50k given', NULL, '10-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-10 14:54:55', '2025-07-10 14:54:55', 0),
(9, 0, 5, 'She is in discussion with her boss\r\nshe will update us on whatsapp', NULL, '14-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-10 17:10:37', '2025-07-10 17:10:37', 0),
(10, 0, 5, 'test comment', NULL, '2025/07/10 6:41 PM', 25, 0, 0, 1, 0, '2025-07-10 18:42:00', '2025-07-10 18:42:00', 0),
(11, 0, 5, 'test', NULL, '2025-07-10 7:45 PM', 25, 0, 0, 1, 0, '2025-07-10 18:45:40', '2025-07-10 18:45:40', 0),
(12, 4, 5, 'he will provide new module detail , cash module', NULL, '11-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-11 10:02:46', '2025-07-11 10:02:46', 0),
(13, 6, 5, 'Send him email with conformation about the timings, and wait for advance', NULL, '11-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-11 10:03:20', '2025-07-11 10:03:20', 0),
(14, 0, 5, 'Quotation sent ,\r\nhe will confirm once he will have words with partner', NULL, '12-07-2025 3:00 PM', 19, 0, 0, 1, 0, '2025-07-11 17:34:47', '2025-07-11 17:34:47', 0),
(15, 6, 5, 'Quotation sent , waiting for advance from client.', '0', '12-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-11 17:35:32', '2025-07-11 17:35:32', 0),
(16, 4, 5, 'Send him whats app and goth the comments ok, he will provide us cash flow document', '0', '12-07-2025 3:00 PM', 19, 0, 0, 1, 0, '2025-07-11 18:12:32', '2025-07-11 18:12:32', 0),
(17, 4, 5, 'Send him whats app and goth the comments ok, he will provide us cash flow document', '5', '14-07-2025 3:00 PM', 19, 0, 0, 1, 0, '2025-07-12 12:52:29', '2025-07-12 12:52:29', 0),
(18, 4, 5, 'Send him whats app and goth the comments ok, he will provide us cash flow document', '5', '14-07-2025 3:00 PM', 19, 0, 0, 1, 0, '2025-07-12 12:52:45', '2025-07-12 12:52:45', 0),
(19, 6, 5, 'we have to create admin and font design\r\nAdvance received\r\nAdmin on : 17-07-2025\r\nFront layout by or before  23-07-2025', '5', NULL, 18, 0, 0, 1, 0, '2025-07-17 13:22:12', '2025-07-17 13:22:12', 0),
(20, 0, 5, 'Test Commment Cancel Deal Testing', '0', '2025-07-22 11:00 AM', 19, 0, 0, 1, 0, '2025-07-22 10:31:09', '2025-07-22 10:31:09', 5),
(21, 11, 5, 'Testing Deal Cancelled', '5', NULL, 20, 15, 0, 1, 0, '2025-07-22 10:36:46', '2025-07-22 10:36:46', 0),
(22, 11, 5, 'Test Demo Cancel Deal..', '5', NULL, 20, 14, 0, 1, 0, '2025-07-22 10:58:16', '2025-07-22 10:58:16', 0),
(23, 11, 5, 'Test Demo Cancel Deal..', '5', NULL, 20, 14, 0, 1, 0, '2025-07-22 11:00:03', '2025-07-22 11:00:03', 0),
(24, 12, 5, 'Demo Testing', '5', '2025/07/22 4:27 PM', 19, 0, 0, 1, 0, '2025-07-22 15:27:21', '2025-07-22 15:27:21', 0),
(25, 12, 5, 'TESTING Comment', '5', '2025/07/22 4:30 PM', 25, 0, 0, 1, 0, '2025-07-22 15:38:25', '2025-07-22 15:38:25', 0),
(26, 0, 5, 'TEst commentssss', '0', NULL, 17, 0, 0, 1, 0, '2025-07-22 15:53:29', '2025-07-22 15:53:29', 5),
(27, 13, 5, 'tesrt', '5', '2025/07/22 12:53 PM', 19, 0, 0, 1, 0, '2025-07-22 15:54:11', '2025-07-22 15:54:11', 0),
(28, 13, 5, 'Twesting 13323', '5', '2025/07/22 8:00 PM', 25, 0, 0, 1, 0, '2025-07-22 15:54:45', '2025-07-22 15:54:45', 0),
(29, 13, 5, 'Comment apollo123131212', '5', '2025/07/22 9:00 PM', 19, 0, 0, 1, 0, '2025-07-22 15:55:21', '2025-07-22 15:55:21', 0),
(30, 13, 5, 'Deal Done Successfully', '5', NULL, 18, 0, 5000, 1, 0, '2025-07-22 15:56:26', '2025-07-22 15:56:26', 0),
(31, 4, 5, 'Tender will open on 27th so follup on 29', '5', '29-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-22 18:15:48', '2025-07-22 18:15:48', 0),
(32, 10, 5, 'Partner comments needs , out of budget , now looking for static website', '5', NULL, 20, 15, 0, 1, 0, '2025-07-22 18:19:18', '2025-07-22 18:19:18', 0),
(33, 14, 5, 'Discussion done need to confirm\r\nhe will visit the offfice', '0', '23-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-22 18:25:47', '2025-07-22 18:25:47', 5),
(34, 1, 5, 'he is now looking for other options , some ready software', '5', NULL, 20, 15, 0, 1, 0, '2025-07-22 18:26:24', '2025-07-22 18:26:24', 0),
(35, 5, 5, 'ping on whatss app waiting for conformation.', '5', '23-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-22 18:27:12', '2025-07-22 18:27:12', 0),
(36, 7, 5, 'we have ping may time\r\nhe will discuss with his sir and update us.', '5', '24-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-22 18:27:43', '2025-07-22 18:27:43', 0),
(37, 15, 5, 'Test', '0', NULL, 17, 0, 0, 1, 0, '2025-07-22 18:39:27', '2025-07-22 18:39:27', 5),
(38, 15, 5, 'Test', '5', NULL, 17, 0, 0, 1, 0, '2025-07-22 18:39:56', '2025-07-22 18:39:56', 0),
(39, 15, 5, 'Test again', '5', NULL, 18, 0, 25000, 1, 0, '2025-07-22 18:40:15', '2025-07-22 18:40:15', 0),
(40, 16, 5, 'test', '0', NULL, 17, 0, 0, 1, 0, '2025-07-22 18:51:01', '2025-07-22 18:51:01', 5),
(41, 16, 5, 'Test', '5', NULL, 18, 0, 30000, 1, 0, '2025-07-22 18:51:20', '2025-07-22 18:51:20', 0),
(42, 0, 6, 'Demo meeting', '0', '24-07-2025 11:45 AM', 23, 0, 0, 1, 0, '2025-07-24 11:21:09', '2025-07-24 11:21:09', 6),
(43, 18, 8, 'Test purpose entry 1', '0', NULL, 30, 0, 0, 1, 0, '2025-07-24 11:22:04', '2025-07-24 11:22:04', 9),
(44, 18, 8, 'first follow up', '9', '24-07-2025 12:12 PM', 32, 0, 0, 1, 0, '2025-07-24 11:25:17', '2025-07-24 11:25:17', 0),
(45, 0, 6, 'Google Meet with client 24/07 time : 11:35', '0', '24-07-2025 11:35 AM', 23, 0, 0, 1, 0, '2025-07-24 11:27:23', '2025-07-24 11:27:23', 6),
(47, 0, 6, 'test firebase notification', '0', '24-07-2025 11:00 AM', 23, 0, 0, 1, 0, '2025-07-24 11:36:11', '2025-07-24 11:36:11', 6),
(48, 0, 6, 'test Notification', '0', '24-07-2025 11:30 AM', 23, 0, 0, 1, 0, '2025-07-24 11:38:27', '2025-07-24 11:38:27', 6),
(49, 23, 8, 'test', '0', NULL, 30, 0, 0, 1, 0, '2025-07-24 12:27:29', '2025-07-24 12:27:29', 9),
(50, 23, 8, 'first follow up', '9', '24-07-2025 1:05 PM', 32, 0, 0, 1, 0, '2025-07-24 12:46:42', '2025-07-24 12:46:42', 0),
(51, 24, 8, 'test', '0', NULL, 30, 0, 0, 1, 0, '2025-07-24 12:47:36', '2025-07-24 12:47:36', 9),
(52, 24, 8, 'first follow up', '9', '24-07-2025 1:45 PM', 32, 0, 0, 1, 0, '2025-07-24 12:47:58', '2025-07-24 12:47:58', 0),
(53, 25, 8, 'test', '0', NULL, 30, 0, 0, 1, 0, '2025-07-24 12:48:50', '2025-07-24 12:48:50', 9),
(54, 25, 8, 'first follow up', '9', '24-07-2025 2:00 PM', 32, 0, 0, 1, 0, '2025-07-24 12:50:02', '2025-07-24 12:50:02', 0),
(55, 0, 5, 'he will discuss with partner', '0', '2025-07-25 12:30 PM', 19, 0, 0, 1, 0, '2025-07-24 14:55:52', '2025-07-24 14:55:52', 5),
(56, 5, 5, 'send google link for discussion', '5', '28-07-2025 2:00 AM', 25, 0, 0, 1, 0, '2025-07-24 18:17:04', '2025-07-24 18:17:04', 0),
(57, 32, 5, 'Portfilio sent', '0', '25-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-24 18:19:51', '2025-07-24 18:19:51', 5),
(58, 20, 6, 'deal done', '6', NULL, 22, 0, 5000, 1, 0, '2025-07-24 18:42:35', '2025-07-24 18:42:35', 0),
(59, 22, 6, 'deal cancel', '6', NULL, 24, 18, 0, 1, 0, '2025-07-24 18:43:00', '2025-07-24 18:43:00', 0),
(60, 7, 5, 'whatsapp sent and we are waiting for comments', '5', '28-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-25 11:51:50', '2025-07-25 11:51:50', 0),
(61, 14, 5, 'comments send \r\nwaiting for reply', '5', '28-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-25 11:52:21', '2025-07-25 11:52:21', 0),
(62, 32, 5, 'hi good morning \r\nhope you are doing well !\r\nPlease let me know if we can connect and discuss about your website development in detail', '5', '26-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-25 11:56:57', '2025-07-25 11:56:57', 0),
(63, 34, 5, 'call to mr rahu diskit and discuss the app development concept in detail.', '0', '25-07-2025 2:00 PM', 19, 0, 0, 1, 0, '2025-07-25 12:27:12', '2025-07-25 12:27:12', 5),
(64, 35, 5, 'Client will call us discuss on whatsapp', '0', '26-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-25 12:28:22', '2025-07-25 12:28:22', 5),
(65, 0, 6, 'comment', '0', '26-07-2025 1:15 PM', 23, 0, 0, 1, 0, '2025-07-25 12:43:33', '2025-07-25 12:43:33', 6),
(66, 37, 6, 'test', '0', '27-07-2025 12:00 AM', 23, 0, 0, 1, 0, '2025-07-25 12:48:17', '2025-07-25 12:48:17', 6),
(67, 38, 6, 'commen6tt', '6', '25-07-2025 3:15 PM', 23, 0, 0, 1, 0, '2025-07-25 12:56:50', '2025-07-25 12:56:50', 0),
(68, 26, 5, 'they deal with other company\r\nmay be we give them long time to complete the project.', '5', NULL, 20, 14, 0, 1, 0, '2025-07-25 13:00:58', '2025-07-25 13:00:58', 0),
(69, 40, 5, 'He will visit or office for discussion', '0', '26-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-25 15:34:39', '2025-07-25 15:34:39', 5),
(70, 41, 6, 'Deal cancel.......', '6', NULL, 24, 17, 0, 1, 0, '2025-07-25 16:10:03', '2025-07-25 16:10:03', 0),
(71, 27, 6, 'Deal done.......', '6', NULL, 22, 0, 15000, 1, 0, '2025-07-25 16:11:03', '2025-07-25 16:11:03', 0),
(72, 43, 6, '-', '6', '26-07-2025 11:00 AM', 23, 0, 0, 1, 0, '2025-07-25 18:01:33', '2025-07-25 18:01:33', 0),
(73, 39, 6, '-', '6', '26-07-2025 11:00 AM', 23, 0, 0, 1, 0, '2025-07-25 18:03:02', '2025-07-25 18:03:02', 0),
(74, 39, 6, 'test Commment', '13', '27-07-2025 9:00 PM', 23, 0, 0, 1, 0, '2025-07-25 18:06:08', '2025-07-25 18:06:08', 0),
(75, 42, 6, 'no interest', '6', NULL, 24, 16, 0, 1, 0, '2025-07-25 18:17:05', '2025-07-25 18:17:05', 0),
(76, 5, 5, 'Deal Done', '5', NULL, 18, 0, 0, 1, 0, '2025-07-25 20:39:12', '2025-07-25 20:39:12', 0),
(77, 45, 6, 'test Comment', '0', NULL, 21, 0, 0, 1, 0, '2025-07-26 12:43:17', '2025-07-26 12:43:17', 6),
(78, 46, 6, 'cOMMENT', '0', NULL, 21, 0, 0, 1, 0, '2025-07-26 12:51:01', '2025-07-26 12:51:01', 6),
(79, 47, 6, 'Commeenj', '0', NULL, 21, 0, 0, 1, 0, '2025-07-26 12:52:25', '2025-07-26 12:52:25', 6),
(80, 0, 6, 'commen', '0', NULL, 21, 0, 0, 1, 0, '2025-07-26 12:53:36', '2025-07-26 12:53:36', 6),
(81, 19, 6, 'done', '6', NULL, 22, 0, 20000, 1, 0, '2025-07-26 15:59:44', '2025-07-26 15:59:44', 0),
(82, 38, 6, 'done.', '6', NULL, 22, 0, 200000, 1, 0, '2025-07-26 16:04:41', '2025-07-26 16:04:41', 0),
(83, 17, 6, 'monday call.', '6', '28-07-2025 12:00 PM', 23, 0, 0, 1, 0, '2025-07-26 16:08:24', '2025-07-26 16:08:24', 0),
(84, 17, 6, 'deal cancel......', '6', NULL, 24, 16, 0, 1, 0, '2025-07-26 16:18:51', '2025-07-26 16:18:51', 0),
(85, 49, 5, 'call him or he will call us on monday as per his free time', '0', '28-07-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-07-26 19:10:34', '2025-07-26 19:10:34', 5),
(86, 12, 5, 'call for update', '5', '06-08-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-08-05 16:07:16', '2025-08-05 16:07:16', 0),
(87, 12, 5, 'call for price confirmation', '5', '06-08-2025 12:00 PM', 25, 0, 0, 1, 0, '2025-08-05 16:08:00', '2025-08-05 16:08:00', 0),
(88, 50, 5, 'sample logo sent, waiting for approval to start website designing.', '0', '07-08-2025 2:00 PM', 19, 0, 0, 1, 0, '2025-08-05 19:18:38', '2025-08-05 19:18:38', 12),
(89, 51, 11, 'new lead', '0', NULL, 38, 0, 0, 1, 0, '2025-08-08 12:28:23', '2025-08-08 12:28:23', 18),
(90, 51, 11, 'call', '18', NULL, 38, 0, 0, 1, 0, '2025-08-08 12:28:59', '2025-08-08 12:28:59', 0),
(91, 52, 11, 'Our Reference to give some one', '0', NULL, 38, 0, 0, 1, 0, '2025-08-08 12:54:43', '2025-08-08 12:54:43', 18),
(92, 52, 11, 'call again', '18', '18-08-2025 12:00 PM', 40, 0, 0, 1, 0, '2025-08-08 13:22:31', '2025-08-08 13:22:31', 0),
(93, 52, 11, 'call again', '18', '08-08-2025 12:05 PM', 40, 0, 0, 1, 0, '2025-08-08 13:24:30', '2025-08-08 13:24:30', 0),
(94, 53, 11, 'We Are send Perfomer Invoice follow up Monday \r\n11-08-25', '0', NULL, 38, 0, 0, 1, 0, '2025-08-08 14:42:46', '2025-08-08 14:42:46', 18),
(95, 53, 11, 'He said  not pay Advance payment Then Deal cancel', '18', NULL, 41, 31, 0, 1, 0, '2025-08-12 15:24:11', '2025-08-12 15:24:11', 0),
(96, 56, 11, 'LIME PLASTER CATLOG', '0', NULL, 38, 0, 0, 1, 0, '2025-08-12 15:58:13', '2025-08-12 15:58:13', 18),
(97, 56, 11, 'LIME PLASTER CATLOG', '18', '14-08-2025 9:00 AM', 40, 0, 0, 1, 0, '2025-08-12 16:02:40', '2025-08-12 16:02:40', 0),
(98, 57, 11, 'Texture Gun Ave etle janavu', '0', '13-08-2025 12:00 PM', 40, 0, 0, 1, 0, '2025-08-13 12:39:19', '2025-08-13 12:39:19', 18),
(99, 54, 11, 'High Gloss Paint Order', '18', NULL, 39, 0, 8184, 1, 0, '2025-08-13 12:45:27', '2025-08-13 12:45:27', 0),
(100, 58, 11, 'Texture Gun 5 Pcs Req..Ave etle Janavu', '0', NULL, 38, 0, 0, 1, 0, '2025-08-13 12:49:52', '2025-08-13 12:49:52', 18),
(101, 33, 5, '9824773136', '5', NULL, 18, 0, 0, 1, 0, '2025-08-16 17:59:33', '2025-08-16 17:59:33', 0),
(102, 34, 5, '9824773136', '5', NULL, 18, 0, 0, 1, 0, '2025-08-16 18:00:07', '2025-08-16 18:00:07', 0),
(103, 59, 5, 'for more demo detail', '0', '22-08-2025 2:00 PM', 19, 0, 0, 1, 0, '2025-08-20 14:14:25', '2025-08-20 14:14:25', 5),
(104, 60, 11, 'send sample', '0', '21-08-2025 4:00 PM', 40, 0, 0, 1, 0, '2025-08-20 14:34:53', '2025-08-20 14:34:53', 18),
(105, 61, 11, 'LIME PASTER , LUXURY  EFECT SAMPLE 25% DARK L-183 TWO SAMPLE\r\n Ready Karva and porter karva', '0', '23-08-2025 12:00 PM', 40, 0, 0, 1, 0, '2025-08-20 17:41:51', '2025-08-20 17:41:51', 18),
(106, 62, 11, 'Tomorrow call', '0', '21-08-2025 12:00 PM', 40, 0, 0, 1, 0, '2025-08-20 17:56:19', '2025-08-20 17:56:19', 18),
(107, 63, 11, 'call', '18', '21-08-2025 3:30 PM', 42, 0, 0, 1, 0, '2025-08-21 15:15:58', '2025-08-21 15:15:58', 0),
(108, 64, 5, 'Call him tomorrow', '0', '23-08-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-08-22 18:02:10', '2025-08-22 18:02:10', 5),
(109, 63, 11, 'Looking for roller', '18', '22-08-2025 6:50 PM', 42, 0, 0, 1, 0, '2025-08-22 18:45:39', '2025-08-22 18:45:39', 0),
(110, 64, 5, 'Scope of work given to client he will revert us after having discussion with his tech team', '5', '28-08-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-08-26 16:30:43', '2025-08-26 16:30:43', 0),
(111, 14, 5, 'not responding', '5', NULL, 20, 13, 0, 1, 0, '2025-08-26 16:32:30', '2025-08-26 16:32:30', 0),
(112, 40, 5, 'not picking up the phone since logn', '5', NULL, 20, 13, 0, 1, 0, '2025-08-26 16:33:44', '2025-08-26 16:33:44', 0),
(113, 32, 5, 'not picking up the phone', '5', NULL, 20, 13, 0, 1, 0, '2025-08-26 16:35:29', '2025-08-26 16:35:29', 0),
(114, 35, 5, 'WE are not working in that technology', '5', NULL, 20, 13, 0, 1, 0, '2025-08-26 16:36:06', '2025-08-26 16:36:06', 0),
(115, 64, 5, 'get conformation for teach aupdads', '5', '02-09-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-08-30 15:29:46', '2025-08-30 15:29:46', 0),
(116, 65, 5, 'call after 15 days', '5', '30-08-2025 3:42 PM', 19, 0, 0, 1, 0, '2025-08-30 15:42:29', '2025-08-30 15:42:29', 0),
(117, 66, 5, 'Call back for update', '5', '02-09-2025 2:00 PM', 19, 0, 0, 1, 0, '2025-09-01 16:49:29', '2025-09-01 16:49:29', 0),
(118, 67, 14, 'test', '22', NULL, 57, 39, 0, 1, 0, '2025-09-01 17:00:09', '2025-09-01 17:00:09', 0),
(119, 68, 14, 'connect tomorrow', '0', NULL, 54, 0, 0, 1, 0, '2025-09-01 17:13:51', '2025-09-01 17:13:51', 22),
(120, 59, 5, 'call back', '5', '02-09-2025 4:30 PM', 19, 0, 0, 1, 0, '2025-09-02 15:47:23', '2025-09-02 15:47:23', 0),
(121, 68, 14, '322 Q', '22', '05-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-04 15:30:34', '2025-09-04 15:30:34', 0),
(122, 69, 14, '8-9-2025 follow up', '0', '08-09-2025 2:00 PM', 56, 0, 0, 1, 0, '2025-09-04 16:42:28', '2025-09-04 16:42:28', 22),
(123, 70, 14, 'fix pallet 1200X1200X150 mm load 1.5 ton qty : 11', '0', '06-09-2025 12:00 PM', 54, 0, 0, 1, 0, '2025-09-04 16:47:19', '2025-09-04 16:47:19', 22),
(124, 71, 14, 'whatsapp', '0', '08-09-2025 12:00 PM', 54, 0, 0, 1, 0, '2025-09-04 16:52:50', '2025-09-04 16:52:50', 22),
(125, 70, 14, 'quotation pending', '22', '10-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-04 16:55:09', '2025-09-04 16:55:09', 0),
(126, 70, 14, 'QUTOTATION SEND', '22', '15-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-10 12:16:36', '2025-09-10 12:16:36', 0),
(127, 85, 5, 'dynamic web site', '0', '18-09-2025 2:00 PM', 19, 0, 0, 1, 0, '2025-09-11 12:56:30', '2025-09-11 12:56:30', 5),
(128, 85, 5, 'for formal demo', '5', '01-10-2025 12:00 PM', 48, 0, 0, 1, 0, '2025-09-11 12:59:46', '2025-09-11 12:59:46', 0),
(129, 86, 5, 'he will visit our office in evening to finalized the e-commerce website.', '0', '24-09-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-09-19 10:56:45', '2025-09-19 10:56:45', 5),
(130, 64, 5, 'whatsa app done \r\nall quotaiton given he will get back to us next week.', '5', '25-09-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-09-19 11:01:30', '2025-09-19 11:01:30', 0),
(131, 87, 5, 'ping to customer on whatsapp again and try to book appointment', '0', '23-09-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-09-19 11:04:06', '2025-09-19 11:04:06', 5),
(132, 89, 5, 'Quotation for REact with next js given.\r\nhe will confirm soon with us.', '0', '23-09-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-09-20 16:24:23', '2025-09-20 16:24:23', 5),
(133, 90, 5, 'we have to visit client office @ 12:00 owner is from America and want to develop application.', '0', '23-09-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-09-20 16:26:02', '2025-09-20 16:26:02', 5),
(134, 93, 12, 'TCF 3.0', '0', NULL, 44, 0, 0, 1, 0, '2025-09-22 16:50:25', '2025-09-22 16:50:25', 20),
(135, 64, 5, 'He will reply soon', '5', '30-09-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-09-25 13:03:24', '2025-09-25 13:03:24', 0),
(136, 141, 12, 'Niyant Parikh was interactive visitor however found little casual - Ruchi', '0', NULL, 47, 32, 0, 1, 0, '2025-09-25 15:58:27', '2025-09-25 15:58:27', 20),
(137, 142, 14, 'IT IS IN PROCESS', '0', '10-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 16:59:12', '2025-09-25 16:59:12', 22),
(138, 143, 14, 'IT IS IN PROCESS', '0', '10-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 16:59:13', '2025-09-25 16:59:13', 22),
(139, 144, 14, 'FOLLOW UP', '0', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:05:53', '2025-09-25 17:05:53', 22),
(140, 145, 14, '6-10-25 FOLOW UP', '0', '01-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:09:36', '2025-09-25 17:09:36', 22),
(141, 146, 14, 'IT IS IN PROCESS', '0', '10-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:11:46', '2025-09-25 17:11:46', 22),
(142, 147, 14, 'IT IS IN PROCESS', '0', '10-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:13:57', '2025-09-25 17:13:57', 22),
(143, 148, 14, 'IT IS IN PROCESS', '0', '16-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:15:38', '2025-09-25 17:15:38', 22),
(144, 149, 14, 'UNDER APPROVAL  PROCESS', '0', '16-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:19:18', '2025-09-25 17:19:18', 22),
(145, 150, 14, 'IT IS IN PROCESS', '0', '17-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:21:08', '2025-09-25 17:21:08', 22),
(146, 151, 14, 'IT IS IN PROCESS', '0', '26-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:22:35', '2025-09-25 17:22:35', 22),
(147, 152, 14, 'UNDER APPROVAL', '0', '30-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:24:41', '2025-09-25 17:24:41', 22),
(148, 153, 14, 'UNDER APPROVAL', '0', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:28:33', '2025-09-25 17:28:33', 22),
(149, 154, 14, 'UNDER APPROVAL', '0', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:32:00', '2025-09-25 17:32:00', 22),
(150, 155, 14, 'SPECIFICATION CHANGES', '0', '27-09-2025 12:00 PM', 61, 0, 0, 1, 0, '2025-09-25 17:36:50', '2025-09-25 17:36:50', 22),
(151, 156, 14, 'UNDER APPROVAL', '0', '29-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:38:15', '2025-09-25 17:38:15', 22),
(152, 157, 14, 'UNDER APPROVAL', '0', '29-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:41:22', '2025-09-25 17:41:22', 22),
(153, 158, 14, 'TROLLEY REWORK UNDER PROCESS', '0', '29-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:42:52', '2025-09-25 17:42:52', 22),
(154, 159, 14, 'UNDER APPORVAL', '0', '29-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:44:33', '2025-09-25 17:44:33', 22),
(155, 160, 14, 'UNDER APPROVAL', '0', '02-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:55:33', '2025-09-25 17:55:33', 22),
(156, 161, 14, 'FOLLOW UP', '0', '29-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:57:45', '2025-09-25 17:57:45', 22),
(157, 162, 14, 'FOLLOW UP', '0', '29-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 17:59:40', '2025-09-25 17:59:40', 22),
(158, 163, 14, 'SAGAR QUOT PENDING TO SUBMIT', '0', '26-09-2025 12:00 PM', 61, 0, 0, 1, 0, '2025-09-25 18:01:12', '2025-09-25 18:01:12', 22),
(159, 164, 14, 'FOLLOW UP', '0', '29-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 18:02:32', '2025-09-25 18:02:32', 22),
(160, 165, 14, 'FOLOW UP', '0', '29-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 18:03:45', '2025-09-25 18:03:45', 22),
(161, 166, 14, 'FOLLOW UP', '0', '29-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 18:04:47', '2025-09-25 18:04:47', 22),
(162, 167, 14, 'FOLLOW UP', '0', '29-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 18:06:02', '2025-09-25 18:06:02', 22),
(163, 168, 14, 'FOLLOW UP', '0', '29-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 18:07:32', '2025-09-25 18:07:32', 22),
(164, 169, 14, 'FOLLOW UP', '0', '29-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 18:09:06', '2025-09-25 18:09:06', 22),
(165, 170, 14, 'FOLLOW UP', '0', '26-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-25 18:11:35', '2025-09-25 18:11:35', 22),
(166, 70, 14, 'follow up', '22', '30-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-26 11:26:41', '2025-09-26 11:26:41', 0),
(167, 146, 14, 'follow up', '22', '30-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-26 11:36:30', '2025-09-26 11:36:30', 0),
(168, 145, 14, 'follow up', '22', '30-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-26 11:37:12', '2025-09-26 11:37:12', 0),
(169, 143, 14, 'follow up', '22', '30-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-26 11:37:44', '2025-09-26 11:37:44', 0),
(170, 147, 14, 'follow up', '22', '30-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-26 11:38:07', '2025-09-26 11:38:07', 0),
(171, 148, 14, 'follow up', '22', '30-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-26 11:38:27', '2025-09-26 11:38:27', 0),
(172, 149, 14, 'follow up', '22', '30-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-26 11:38:45', '2025-09-26 11:38:45', 0),
(173, 150, 14, 'follow up', '22', '30-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-26 11:39:00', '2025-09-26 11:39:00', 0),
(174, 198, 14, 'FOLLOW UP', '0', '29-09-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-26 16:56:14', '2025-09-26 16:56:14', 22),
(175, 185, 12, 'Joined Optima', '20', NULL, 45, 0, 38000, 1, 0, '2025-09-26 17:00:24', '2025-09-26 17:00:24', 0),
(176, 199, 5, 'salexo CRM', '0', '29-09-2025 2:00 PM', 19, 0, 0, 1, 0, '2025-09-26 17:30:34', '2025-09-26 17:30:34', 5),
(177, 215, 5, 'his ref in us and she want to make website.\r\nwe have to discuss with her in detail on google meet.', '0', '29-09-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-09-26 18:18:10', '2025-09-26 18:18:10', 5),
(178, 216, 5, 'he will call us, confirm the time and visit for discussion with update in website and seo.', '0', '27-09-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-09-26 18:19:34', '2025-09-26 18:19:34', 5),
(179, 217, 5, 'call to client @7:30', '0', '26-09-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-09-26 18:20:50', '2025-09-26 18:20:50', 5),
(180, 225, 14, 'FOLLOW UP', '0', '03-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-29 17:03:24', '2025-09-29 17:03:24', 22),
(181, 226, 14, 'FOLLOW UP', '0', '03-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-29 17:04:48', '2025-09-29 17:04:48', 22),
(182, 227, 14, 'FOLLOW UP', '0', '03-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 12:46:34', '2025-09-30 12:46:34', 22),
(183, 157, 14, 'follow up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 13:46:25', '2025-09-30 13:46:25', 0),
(184, 145, 14, 'follow up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 13:47:41', '2025-09-30 13:47:41', 0),
(185, 146, 14, 'F UP', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 13:48:06', '2025-09-30 13:48:06', 0),
(186, 143, 14, 'F UP', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 13:48:27', '2025-09-30 13:48:27', 0),
(187, 147, 14, 'F UP', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 13:49:43', '2025-09-30 13:49:43', 0),
(188, 148, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:04:11', '2025-09-30 14:04:11', 0),
(189, 149, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:05:13', '2025-09-30 14:05:13', 0),
(190, 150, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:05:57', '2025-09-30 14:05:57', 0),
(191, 151, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:06:17', '2025-09-30 14:06:17', 0),
(192, 152, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:11:46', '2025-09-30 14:11:46', 0),
(193, 153, 14, 'f up', '22', '01-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:15:52', '2025-09-30 14:15:52', 0),
(194, 157, 14, 'fifo rack f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:16:09', '2025-09-30 14:16:09', 0),
(195, 158, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:17:10', '2025-09-30 14:17:10', 0),
(196, 159, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:17:39', '2025-09-30 14:17:39', 0),
(197, 161, 14, 'f up', '22', '02-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:18:02', '2025-09-30 14:18:02', 0),
(198, 162, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:20:15', '2025-09-30 14:20:15', 0),
(199, 164, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:20:32', '2025-09-30 14:20:32', 0),
(200, 165, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:20:45', '2025-09-30 14:20:45', 0),
(201, 166, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:20:59', '2025-09-30 14:20:59', 0),
(202, 167, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:21:14', '2025-09-30 14:21:14', 0),
(203, 168, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:21:55', '2025-09-30 14:21:55', 0),
(204, 169, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:23:13', '2025-09-30 14:23:13', 0),
(205, 170, 14, 'f up', '22', '01-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:23:33', '2025-09-30 14:23:33', 0),
(206, 156, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:24:04', '2025-09-30 14:24:04', 0),
(207, 198, 14, 'f up', '22', '03-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:24:49', '2025-09-30 14:24:49', 0),
(208, 163, 14, 'f up', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:25:14', '2025-09-30 14:25:14', 0),
(209, 155, 14, 'he will provide detail then submit quote', '22', '06-10-2025 12:00 PM', 61, 0, 0, 1, 0, '2025-09-30 14:25:34', '2025-09-30 14:25:34', 0),
(210, 69, 14, 'rate issue', '22', '30-09-2025 12:00 PM', 57, 41, 0, 1, 0, '2025-09-30 14:26:20', '2025-09-30 14:26:20', 0),
(211, 71, 14, 'not require now', '22', NULL, 57, 39, 0, 1, 0, '2025-09-30 14:26:50', '2025-09-30 14:26:50', 0),
(212, 70, 14, 'f up', '22', '01-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-09-30 14:27:11', '2025-09-30 14:27:11', 0),
(213, 228, 5, 'website development for his US contact', '0', '30-09-2025 7:00 PM', 19, 0, 0, 1, 0, '2025-09-30 15:26:02', '2025-09-30 15:26:02', 5),
(214, 229, 5, 'travel website developemnt', '0', '30-09-2025 2:00 PM', 19, 0, 0, 1, 0, '2025-09-30 15:32:23', '2025-09-30 15:32:23', 5),
(215, 230, 5, 'React JS resource hiring', '0', '30-09-2025 5:00 PM', 19, 0, 0, 1, 0, '2025-09-30 15:34:02', '2025-09-30 15:34:02', 5),
(216, 231, 5, 'quotation for e-commerce website and dynamic website sent.', '0', '06-10-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-10-03 16:04:55', '2025-10-03 16:04:55', 5),
(217, 232, 5, 'meeting with client at mohanish ji office income tax', '0', '04-10-2025 12:00 PM', 19, 0, 0, 1, 0, '2025-10-03 16:06:44', '2025-10-03 16:06:44', 5),
(218, 236, 14, 'process in PO', '0', '31-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-03 17:11:14', '2025-10-03 17:11:14', 22),
(219, 237, 14, 'process', '0', '20-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-03 17:12:48', '2025-10-03 17:12:48', 22),
(220, 238, 14, 'process', '0', '15-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-03 17:14:43', '2025-10-03 17:14:43', 22),
(221, 239, 14, 'disscuss', '0', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-03 17:16:47', '2025-10-03 17:16:47', 22),
(222, 241, 14, 'processs', '0', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-03 17:33:44', '2025-10-03 17:33:44', 22),
(223, 242, 14, 'processing', '0', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-03 17:35:18', '2025-10-03 17:35:18', 22),
(224, 243, 14, 'visit plant', '0', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-03 17:36:45', '2025-10-03 17:36:45', 22),
(225, 232, 5, 'busy with some task call and confirm meeting at income tax', '5', '06-10-2025 1:00 AM', 19, 0, 0, 1, 0, '2025-10-04 10:54:21', '2025-10-04 10:54:21', 0),
(226, 87, 5, 'send quotation', '5', '06-10-2025 2:00 AM', 19, 0, 0, 1, 0, '2025-10-04 10:55:08', '2025-10-04 10:55:08', 0),
(227, 159, 14, 'WILL TELL LATER AFTR APPROVAL', '22', NULL, 57, 40, 0, 1, 0, '2025-10-04 13:37:42', '2025-10-04 13:37:42', 0),
(228, 148, 14, 'WILL TELL IF ANY LATER', '22', NULL, 57, 40, 0, 1, 0, '2025-10-04 13:38:26', '2025-10-04 13:38:26', 0),
(229, 153, 14, 'F UP', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-04 13:41:03', '2025-10-04 13:41:03', 0),
(230, 161, 14, 'F UP', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-04 13:41:17', '2025-10-04 13:41:17', 0),
(231, 170, 14, 'F UP', '22', '09-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-04 13:41:30', '2025-10-04 13:41:30', 0),
(232, 225, 14, 'F UP FOR PO', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-04 13:41:46', '2025-10-04 13:41:46', 0),
(233, 227, 14, 'F UP', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-04 13:42:01', '2025-10-04 13:42:01', 0),
(234, 226, 14, 'AFTER QUYE SEND', '22', '06-10-2025 12:00 PM', 61, 0, 0, 1, 0, '2025-10-04 13:42:22', '2025-10-04 13:42:22', 0),
(235, 198, 14, 'FOLLOW UP WITH LOKESH PPAP CORPO', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-04 13:42:44', '2025-10-04 13:42:44', 0),
(236, 160, 14, 'FUP ON CALL', '22', '06-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-04 13:43:53', '2025-10-04 13:43:53', 0),
(237, 246, 14, 'FOLLOW UP', '0', '10-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-06 11:08:52', '2025-10-06 11:08:52', 22),
(238, 247, 14, 'FOLLOW UP', '0', '10-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-06 11:08:53', '2025-10-06 11:08:53', 22),
(239, 157, 14, 'f up', '22', '09-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-06 13:19:49', '2025-10-06 13:19:49', 0),
(240, 146, 14, 'f up', '22', '09-10-2025 12:00 PM', 56, 0, 0, 1, 0, '2025-10-06 13:20:08', '2025-10-06 13:20:08', 0);

-- --------------------------------------------------------

--
-- Table structure for table `lead_master`
--

CREATE TABLE `lead_master` (
  `lead_id` int(11) NOT NULL,
  `iCustomerId` int(11) NOT NULL DEFAULT '0' COMMENT 'as a company_id',
  `iemployeeId` int(11) NOT NULL DEFAULT '0',
  `company_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `GST_No` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `alternative_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8_unicode_ci,
  `product_service_id` int(11) NOT NULL DEFAULT '0',
  `product_service_other` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `LeadSourceId` int(11) NOT NULL DEFAULT '0',
  `LeadSource_other` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lead_history_id` int(11) NOT NULL DEFAULT '0',
  `comments` text COLLATE utf8_unicode_ci,
  `followup_by` int(11) DEFAULT '0',
  `next_followup_date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `cancel_reason_id` int(11) NOT NULL DEFAULT '0',
  `amount` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iStatus` int(11) NOT NULL DEFAULT '1',
  `isDelete` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_id` int(11) NOT NULL DEFAULT '0',
  `initially_contacted` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iEnterBy` int(11) NOT NULL DEFAULT '0',
  `deal_converted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lead_master`
--

INSERT INTO `lead_master` (`lead_id`, `iCustomerId`, `iemployeeId`, `company_name`, `GST_No`, `customer_name`, `email`, `mobile`, `address`, `alternative_no`, `remarks`, `product_service_id`, `product_service_other`, `LeadSourceId`, `LeadSource_other`, `lead_history_id`, `comments`, `followup_by`, `next_followup_date`, `status`, `cancel_reason_id`, `amount`, `iStatus`, `isDelete`, `created_at`, `updated_at`, `employee_id`, `initially_contacted`, `iEnterBy`, `deal_converted_at`) VALUES
(49, 5, 5, NULL, '0', 'Shubham Aagarwal', NULL, '931683220', NULL, NULL, 'Ref Vrajesh bhai vadodara\r\ncar rental website', 5, NULL, 2, NULL, 85, 'call him or he will call us on monday as per his free time', 0, '28-07-2025 12:00 PM', 19, 0, NULL, 1, 0, '2025-07-26 13:40:34', NULL, 5, 'Yes', 5, NULL),
(18, 8, 9, 'Zenbyte Technologies Pvt. Ltd', '0', 'Rahul Mehta', 'rahul.mehta@zenbyte.com', '9876543210', '3rd Floor, Corporate Park,\r\nBaner Road, Pune, Maharashtra - 411045', '9123456789', 'Interested in CRM solution. Requested demo and pricing. Follow-up scheduled.', 9, NULL, 7, NULL, 44, 'first follow up', 9, '24-07-2025 12:12 PM', 32, 0, '0', 1, 0, '2025-07-24 05:52:04', NULL, 9, 'Yes', 9, NULL),
(4, 5, 5, NULL, '0', 'Aadip Bhai', NULL, '9328228517', NULL, NULL, 'Nagar palika portal required', 5, NULL, 1, NULL, 31, 'Tender will open on 27th so follup on 29', 5, '29-07-2025 12:00 PM', 19, 0, '0', 1, 0, '2025-07-08 11:00:31', NULL, 5, 'Yes', 5, NULL),
(7, 5, 5, 'Gopinath Chemtech', '0', 'Ms Murthi', NULL, '7228881040', NULL, NULL, 'Website development', 5, NULL, 1, NULL, 60, 'whatsapp sent and we are waiting for comments', 5, '28-07-2025 12:00 PM', 19, 0, '0', 1, 0, '2025-07-10 11:40:37', NULL, 5, 'Yes', 5, NULL),
(45, 6, 6, NULL, '0', 'Rahul Test', NULL, '8888888888', NULL, NULL, 'Remarks Demo', 28, NULL, 10, NULL, 77, 'test Comment', 0, NULL, 21, 0, NULL, 1, 0, '2025-07-26 07:13:17', NULL, 13, 'Yes', 6, NULL),
(46, 6, 6, NULL, '0', 'Mihir Test', NULL, '1111111111', NULL, '2222222222', 'Remark test', 28, NULL, 10, NULL, 78, 'cOMMENT', 0, NULL, 21, 0, NULL, 1, 0, '2025-07-26 07:21:01', NULL, 13, 'Yes', 6, NULL),
(6, 5, 5, NULL, '0', 'Saurabh', NULL, '7040032562', NULL, NULL, 'Quotation approved call for payment', 5, NULL, 1, NULL, 19, 'we have to create admin and font design\r\nAdvance received\r\nAdmin on : 17-07-2025\r\nFront layout by or before  23-07-2025', 5, NULL, 18, 0, '0', 1, 0, '2025-07-10 08:49:03', NULL, 5, 'Yes', 5, NULL),
(8, 5, 5, 'Apollo', '231341244', 'Apollo Test', 'apollo123@gmail.com', '9904500629', NULL, '1111111111', 'test remarks', 6, NULL, 1, NULL, 10, 'test comment', 0, NULL, 25, 0, '0', 1, 0, '2025-07-10 13:12:00', NULL, 5, 'Yes', 0, NULL),
(9, 5, 5, 'Apollo', '231341244', 'Apollo Test', 'apollo123@gmail.com', '9904500629', NULL, '1111111111', 'test remarks', 5, NULL, 1, NULL, 11, 'test', 0, NULL, 25, 0, '0', 1, 0, '2025-07-10 13:15:40', NULL, 12, 'Yes', 0, NULL),
(85, 5, 5, 'Groath Spectrum Pvt Ltd', '0', 'Ruchi shah', 'ruchi.shah@groath.in', '7405067311', '307, Titanium One , Pakvan cross roads , SG highway , Ahmedabad', NULL, 'business networking site', 5, NULL, 26, NULL, 128, 'for formal demo', 5, '01-10-2025 12:00 PM', 48, 0, '0', 1, 0, '2025-09-11 07:26:30', NULL, 12, 'Yes', 5, NULL),
(70, 14, 22, 'VISAMAN INFRA PROJECTS PVT LTD', '0', 'Rajeev', NULL, '9099277758', 'Rajkot, Gujarat, India', NULL, 'fix pallet 1200X1200X150 mm load 1.5 ton qty : 11', 63, NULL, 27, NULL, 212, 'f up', 22, '01-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-04 11:17:19', NULL, 23, 'Yes', 22, NULL),
(12, 5, 5, 'Demo Apollo Infotech', '111GST12454550', 'Demo Meet Patel', 'mignesh123@gmail.com', '9904500629', NULL, NULL, 'Test remarks apollo infotect', 6, NULL, 9, NULL, 87, 'call for price confirmation', 5, '06-08-2025 12:00 PM', 25, 0, '0', 1, 0, '2025-07-22 09:56:44', NULL, 5, 'No', 5, NULL),
(36, 6, 6, 'Vivo Pvt Ltd.', NULL, 'Mignesh Patel', NULL, '9904500629', NULL, '1234567890', 'remarks comment', 17, NULL, 14, NULL, 65, 'comment', 0, '26-07-2025 1:15 PM', 23, 0, '0', 1, 0, '2025-07-25 07:13:33', NULL, 6, 'Yes', 6, NULL),
(51, 11, 18, 'keyur', '0', 'keyur', NULL, '9428560901', 'umreth', NULL, 'roller', 29, NULL, 19, NULL, 90, 'call', 18, NULL, 38, 0, '0', 1, 0, '2025-08-08 06:58:23', NULL, 19, 'Yes', 18, NULL),
(64, 5, 5, 'NA', '0', 'Bhadresh shah', 'bhadreshshah15@yahoo.com', '9824022202', NULL, NULL, '-', 5, NULL, 1, NULL, 135, 'He will reply soon', 5, '30-09-2025 12:00 PM', 19, 0, '0', 1, 0, '2025-08-22 12:32:10', NULL, 5, 'Yes', 5, NULL),
(135, 12, 20, 'R legals', 'NA', 'Suraj Bohra', 'surajcsllm@gmail.com', '9413110877', NULL, NULL, 'Got the lead from instagram - probably from recent marketing of tcf 3.0', 0, 'Corporate & Commercial Law, Intellectual Property (IPR), Central Administrative Tribunal (CAT) & Service Law Practice, Legal Drafting & Research, Taxation Laws', 33, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-25 09:26:31', '2025-09-25 10:53:53', 20, 'No', 20, NULL),
(21, 6, 6, 'Radhe Krishna PVT LTD', 'GST111222RK123', 'Krishna', 'krishna123@gmail.com', '9904500629', NULL, '1122334455', 'remaining amount of the month thaya for friend and family', 21, NULL, 12, NULL, 47, 'test firebase notification', 0, '24-07-2025 11:00 AM', 23, 0, '0', 1, 0, '2025-07-24 06:06:11', NULL, 6, 'Yes', 6, NULL),
(59, 5, 5, 'vacoholic', '0', 'parth shah', 'parth@gmail.com', '9876541234', 'ahmedabad', NULL, 'ahmedabad', 6, NULL, 1, NULL, 120, 'call back', 5, '02-09-2025 4:30 PM', 19, 0, '0', 1, 0, '2025-08-20 08:44:25', NULL, 12, 'Yes', 5, NULL),
(23, 8, 9, 'Nexora Solutions', '0', 'Arjun Kapoor', 'arjun.k@nexora.in', '9876012345', '501, Galaxy Towers, MG Road, Bengaluru, Karnataka – 560001', '9008765432', 'Looking for ERP software integration. Follow-up after product demo.', 9, NULL, 7, NULL, 50, 'first follow up', 9, '24-07-2025 1:05 PM', 32, 0, '0', 1, 0, '2025-07-24 06:57:28', NULL, 9, 'Yes', 9, NULL),
(24, 8, 9, 'Vitech Machinery Works', '0', 'Shweta Rane', 'shweta.r@vitechworks.com', '9823456780', 'D-44, Industrial Area, Okhla Phase II, New Delhi – 110020', '9812345678', 'Interested in automation solutions for CNC machines.', 9, NULL, 7, NULL, 52, 'first follow up', 9, '24-07-2025 1:45 PM', 32, 0, '0', 1, 0, '2025-07-24 07:17:36', NULL, 9, 'Yes', 9, NULL),
(25, 8, 9, 'GreenEarth Organics', '0', 'Priya Nair', 'priya.nair@greenearth.org', '9897654321', '11/3, Riverside Lane, Surat, Gujarat – 395003', '9871234567', 'Looking to set up an e-commerce platform for organic products.', 9, NULL, 7, NULL, 54, 'first follow up', 9, '24-07-2025 2:00 PM', 32, 0, '0', 1, 0, '2025-07-24 07:18:50', NULL, 9, 'Yes', 9, NULL),
(44, 6, 13, 'Smaart Apollo', NULL, 'Rutvik Patel', NULL, '9904500629', NULL, NULL, '-', 28, NULL, 10, NULL, 0, NULL, 0, NULL, 21, 0, '0', 1, 0, '2025-07-25 12:30:05', NULL, 13, 'No', 13, NULL),
(28, 6, 6, NULL, NULL, 'test bsvajsga', NULL, '5555555555', NULL, NULL, '-', 17, NULL, 14, NULL, 0, NULL, 0, NULL, 21, 0, '0', 1, 0, '2025-07-24 12:29:14', NULL, 6, 'No', 6, NULL),
(29, 6, 6, 'shhhshshs', NULL, 'shshahhss', NULL, '6666666666', NULL, NULL, '-', 16, NULL, 14, NULL, 0, NULL, 0, NULL, 21, 0, '0', 1, 0, '2025-07-24 12:29:27', NULL, 6, 'No', 6, NULL),
(30, 6, 6, NULL, NULL, 'Kkkkkkk', NULL, '3333333333', NULL, NULL, '-', 19, NULL, 14, NULL, 0, NULL, 0, NULL, 21, 0, '0', 1, 0, '2025-07-24 12:36:12', NULL, 6, 'No', 6, NULL),
(31, 6, 6, NULL, NULL, 'bbababbb', NULL, '6166161666', NULL, NULL, '-', 21, NULL, 12, NULL, 0, NULL, 0, NULL, 21, 0, '0', 1, 0, '2025-07-24 12:36:24', NULL, 6, 'No', 6, NULL),
(231, 5, 5, NULL, '0', 'Ravi Shankar', NULL, '9899077809', NULL, '9307873051', 'Clothing webiste', 5, NULL, 1, NULL, 216, 'quotation for e-commerce website and dynamic website sent.', 0, '06-10-2025 12:00 PM', 19, 0, NULL, 1, 0, '2025-10-03 10:34:55', NULL, 5, 'Yes', 5, NULL),
(229, 5, 5, NULL, '0', 'Rohit', NULL, '7573078546', 'narol', NULL, 'travel website', 5, NULL, 1, NULL, 214, 'travel website developemnt', 0, '30-09-2025 2:00 PM', 19, 0, NULL, 1, 0, '2025-09-30 10:02:23', NULL, 5, 'Yes', 5, NULL),
(37, 6, 6, NULL, '0', 'Meet patel', NULL, '9999999999', NULL, NULL, 'REmarks test', 16, NULL, 12, NULL, 66, 'test', 0, '27-07-2025 12:00 AM', 23, 0, NULL, 1, 0, '2025-07-25 07:18:17', NULL, 6, 'Yes', 6, NULL),
(50, 5, 12, 'miza naturals', '0', 'arva baldiwala', NULL, '8780418312', NULL, NULL, 'logo, website', 5, NULL, 2, NULL, 88, 'sample logo sent, waiting for approval to start website designing.', 0, '07-08-2025 2:00 PM', 19, 0, NULL, 1, 0, '2025-08-05 13:48:38', NULL, 12, 'Yes', 12, NULL),
(39, 6, 13, 'jay ambe electronics', NULL, 'karan patel', NULL, '9898289098', NULL, NULL, '-', 16, NULL, 10, NULL, 74, 'test Commment', 13, '27-07-2025 9:00 PM', 23, 0, '0', 1, 0, '2025-07-25 09:29:50', NULL, 13, 'No', 13, NULL),
(47, 6, 6, NULL, '0', 'Tarang Test', NULL, '5555555555', NULL, NULL, 'Remark comment test', 28, NULL, 10, NULL, 79, 'Commeenj', 0, NULL, 21, 0, NULL, 1, 0, '2025-07-26 07:22:25', NULL, 13, 'Yes', 6, NULL),
(43, 6, 6, 'jay ambe electronics', NULL, 'karan patel', NULL, '9898289095', NULL, NULL, '-', 28, NULL, 10, NULL, 72, '-', 6, '26-07-2025 11:00 AM', 23, 0, '0', 1, 0, '2025-07-25 12:23:17', NULL, 6, 'No', 6, NULL),
(48, 6, 6, NULL, NULL, 'Vaibhav test', NULL, '3333333333', NULL, NULL, 'remark common', 28, NULL, 16, NULL, 80, 'commen', 0, NULL, 21, 0, '0', 1, 0, '2025-07-26 07:23:36', NULL, 13, 'Yes', 6, NULL),
(52, 11, 18, NULL, '0', 'AR. Malay sir', NULL, '9825035896', 'ahmedabad', NULL, 'Lime plaster enuiry Refrance', 30, NULL, 23, NULL, 0, NULL, 0, NULL, 40, 0, NULL, 1, 0, '2025-08-08 07:24:43', '2025-08-12 10:48:19', 18, NULL, 18, NULL),
(58, 11, 18, 'Krishna Colour', '0', 'Krishna Colour', NULL, '9904880107', 'Nikol', NULL, 'Texture Gun 5 Pcs Req..', 46, NULL, 24, NULL, 100, 'Texture Gun 5 Pcs Req..Ave etle Janavu', 0, NULL, 38, 0, NULL, 1, 0, '2025-08-13 07:19:52', NULL, 19, 'Yes', 18, NULL),
(65, 5, 5, NULL, NULL, 'testing customer', NULL, '9824613136', NULL, NULL, 'request for demo', 5, NULL, 17, NULL, 116, 'call after 15 days', 5, '30-08-2025 3:42 PM', 19, 0, '0', 1, 0, '2025-08-30 10:09:42', NULL, 5, 'No', 5, NULL),
(61, 11, 18, NULL, '0', 'ABHI SIR BAREJA', NULL, '9428560901', 'BAREJA', NULL, 'LIME PASTER , LUXURY  EFECT SAMPLE 25% DARK L-183  TWO SAMPLE Ready karva and porter karva', 30, NULL, 20, NULL, 105, 'LIME PASTER , LUXURY  EFECT SAMPLE 25% DARK L-183 TWO SAMPLE\r\n Ready Karva and porter karva', 0, '23-08-2025 12:00 PM', 40, 0, NULL, 1, 0, '2025-08-20 12:11:51', NULL, 18, 'Yes', 18, NULL),
(55, 11, 18, 'Porval Enterprise', '0', 'PRAFULLA', NULL, '8103818565', 'Mndsor MP', NULL, 'I came across your company\'s profile for my requirement. I am looking for High Gloss Water Based Enamel Paints.\r\n\r\nSending all product details Followp 13-08-25', 44, NULL, 19, NULL, 0, NULL, 0, NULL, 38, 0, NULL, 1, 0, '2025-08-12 09:43:16', '2025-08-20 09:06:30', 19, NULL, 18, NULL),
(62, 11, 18, NULL, '0', 'Ar. Hardik Sir', NULL, '9909026082', 'TIME 40 Ahmedabad', NULL, 'Tomorrow call', 30, NULL, 24, NULL, 106, 'Tomorrow call', 0, '21-08-2025 12:00 PM', 40, 0, NULL, 1, 0, '2025-08-20 12:26:19', NULL, 18, 'Yes', 18, NULL),
(66, 5, 5, 'PY engg', '0', 'Yash shah', 'pyengineering@zoho.com', '9016970829', '102-B, Harikrupa Shopping Center Beside City Gold, Ashram Road, Ahmedabad.', NULL, 'salexo CRM', 54, NULL, 2, NULL, 117, 'Call back for update', 5, '02-09-2025 2:00 PM', 19, 0, '0', 1, 0, '2025-09-01 11:18:24', NULL, 5, 'No', 5, NULL),
(84, 5, 5, 'test', NULL, 'krunal', NULL, '9824773136', NULL, NULL, 'test', 54, NULL, 1, NULL, 0, NULL, 0, NULL, 17, 0, '0', 1, 0, '2025-09-05 06:54:17', NULL, 5, 'No', 5, NULL),
(87, 5, 5, 'Podar borewell', '0', 'Rahul bhai', NULL, '9824448082', NULL, NULL, '-', 5, NULL, 1, NULL, 226, 'send quotation', 5, '06-10-2025 2:00 AM', 19, 0, '0', 1, 0, '2025-09-19 05:34:06', NULL, 5, 'Yes', 5, NULL),
(86, 5, 5, NULL, '0', 'Utpal bhai', NULL, '9998439987', NULL, NULL, '-', 5, NULL, 2, NULL, 129, 'he will visit our office in evening to finalized the e-commerce website.', 0, '24-09-2025 12:00 PM', 19, 0, NULL, 1, 0, '2025-09-19 05:26:45', NULL, 5, 'Yes', 5, NULL),
(88, 8, 9, NULL, '0', 'mihir test', NULL, '7894561230', NULL, NULL, 'mihir test', 0, 'test', 0, 'mihir', 0, NULL, 0, NULL, 30, 0, NULL, 1, 0, '2025-09-19 10:03:41', '2025-09-19 10:49:00', 9, 'No', 9, NULL),
(89, 5, 5, NULL, '0', 'Yes Ref Aniket', NULL, '9016970829', NULL, NULL, 'Old client', 5, NULL, 2, NULL, 132, 'Quotation for REact with next js given.\r\nhe will confirm soon with us.', 0, '23-09-2025 12:00 PM', 19, 0, NULL, 1, 0, '2025-09-20 10:54:23', NULL, 5, 'Yes', 5, NULL),
(90, 5, 5, NULL, '0', 'Rohit prajapati', NULL, '997436295', NULL, NULL, '-', 6, NULL, 1, NULL, 133, 'we have to visit client office @ 12:00 owner is from America and want to develop application.', 0, '23-09-2025 12:00 PM', 19, 0, NULL, 1, 0, '2025-09-20 10:56:02', NULL, 5, 'Yes', 5, NULL),
(91, 12, 20, 'Finnati', '0', 'Amee Patel', 'amee@finnati.com', '9909955813', NULL, NULL, 'TCF 3.0-Ruchi Shah', 0, 'Jewellery', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:07:08', '2025-09-25 12:11:17', 20, 'No', 20, NULL),
(92, 12, 20, 'Gifts World', '0', 'Anish Parikh', 'anish@thegiftsworld.com', '9426987249', NULL, NULL, 'TCF 3.0-Dr. Hemal Shah', 0, 'Ecommerce and Corporate Gifting', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:16:57', '2025-09-25 12:10:43', 20, 'No', 20, NULL),
(93, 12, 20, 'Casakeeper Management Pvt Ltd', '0', 'Arjunssinh Chudasama', 'Hello@arjunssinh.com', '9687699996', NULL, NULL, 'TCF 3.0-Rachana Tated', 0, 'Facility Management', 34, NULL, 134, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:20:25', '2025-09-25 12:10:10', 20, 'Yes', 20, NULL),
(157, 14, 22, 'SML PLASTO', '0', 'PARMOD', NULL, '8447941175', 'VITTHALAPUR', NULL, 'UNDER APPPROVAL', 0, 'FIFO RACK', 35, NULL, 239, 'f up', 22, '09-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:11:22', NULL, 22, 'Yes', 22, NULL),
(94, 12, 20, 'Ha Mera Pata Realtors Management', '0', 'Ashish Gargi', 'Ashish@mera-pata.com', '9099827827', NULL, NULL, 'TCF 3.0- Sachin Shah', 0, 'Real Estate Consultant', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:23:32', '2025-09-25 12:08:41', 20, 'No', 20, NULL),
(95, 12, 20, 'Shayona Finserve PVT LTD', '0', 'Ashish Gohil', 'ashishgohica@gmail.com', '8866577663', NULL, NULL, 'TCF 3.0-Sachin Shah', 0, 'Project Finance', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:25:20', '2025-09-25 12:06:45', 20, 'No', 20, NULL),
(96, 12, 20, 'Crystal Atelier', '0', 'Ashwani Singh', 'ashwani.gfx@gmail.com', '9558933843', NULL, NULL, 'TCF 3.0-Sachin Shah', 0, 'Digital Marketing and Branding', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:26:51', '2025-09-25 12:06:07', 20, 'No', 20, NULL),
(97, 12, 20, 'Buildbox solutions', '0', 'Bina Shah', 'bina@buildbox.co.in', '9824008089', NULL, NULL, 'TCF 3.0-Rasshmi Chhajjer', 0, 'Digital displays', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:28:18', '2025-09-25 12:05:28', 20, 'No', 20, NULL),
(98, 12, 20, 'Chaitali Tolia', '0', 'Chaitali Tolia', 'chaitgi.10@gmail.com', '8200135819', NULL, NULL, 'TCF 3.0-Nairuti Jambusaria', 0, 'Baking', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:29:53', '2025-09-26 12:07:53', 20, 'No', 20, NULL),
(99, 12, 20, 'Atharva Valuation', '0', 'CS Keyur Shah', 'keyur@atharva-valuation.com', '9909702182', NULL, NULL, 'TCF 3.0 - Dr. Hemal Shah', 0, 'Business Valuation, SME/Mainboard IPO, FEMA, Corporate Laws', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:31:42', '2025-09-25 12:03:02', 20, 'No', 20, NULL),
(100, 12, 20, 'The Prime Time', '0', 'Devansh Gandhi', 'devansh51g@gmail.com', '8141076763', NULL, NULL, 'TCF 3.0- Ruchi Shah', 0, 'Gifting and Watches', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:33:02', '2025-09-25 11:54:08', 20, 'No', 20, NULL),
(101, 12, 20, 'AANKHSHASTRA9', '0', 'Devashri Thaker', 'devashree_thaker@yahoo.co.in', '9726057109', NULL, NULL, 'TCF 3.0 - Rachana Tated', 0, 'Numerologist', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:34:43', '2025-09-25 11:53:36', 20, 'No', 20, NULL),
(102, 12, 20, 'Avionic consulting solution', '0', 'Devika Nennakti', 'Devika@avionicconsulting.com', '9998257143', NULL, NULL, 'TCF 3.0 - Isha Thakkar', 0, 'Palcement agency', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:36:32', '2025-09-25 11:51:15', 20, 'No', 20, NULL),
(103, 12, 20, 'PR HVAC PVT LTD', '0', 'Dharmesh parekh', 'director@prhvac.in', '9825098768', NULL, NULL, 'TCF 3.0 - Sachin Shah', 0, 'dealing in the premium brands like DAIKIN , MITSUBISHI ELECTRIC .', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:38:02', '2025-09-25 11:50:28', 20, 'No', 20, NULL),
(104, 12, 20, 'Signiix Advisors- Business & Legal Consultant', '0', 'Drashti Sharma', 'drashti@signiixadvisors.com', '9998733006', NULL, NULL, 'TCF 3.0 Rachana Tated', 0, 'Company Secretary', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:39:34', '2025-09-25 11:49:53', 20, 'No', 20, NULL),
(105, 12, 20, 'Inspire Living Solutions', '0', 'Ilesh Rathod', 'inspirelivingsolutions@gmail.com', '9173909742', NULL, NULL, 'TCF 3.0 - Pramesh Shah', 0, 'Home Automation & Theater', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:42:36', '2025-09-25 11:49:00', 20, 'No', 20, NULL),
(106, 12, 20, 'Profex Resources Limited', '0', 'Karan Punjabi', 'ed.ops@profexresources.com', '9054749555', NULL, NULL, 'TCF 3.0 - Rutul Kamdar', 0, 'Security Agency', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:49:17', '2025-09-25 11:48:15', 20, 'No', 20, NULL),
(107, 12, 20, 'Fitpops', '0', 'Ketan Bengani', 'Ketanpk.bengani@gmail.com', '823970539', NULL, NULL, 'TCF 3.0-Rutul Kamdar', 0, 'Manufacturer and Exporter', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:53:46', '2025-09-25 11:47:08', 20, 'No', 20, NULL),
(108, 12, 20, 'kashyap@infigrityit.com', '0', 'Kashyap Parikh', 'kashyap@infigrityit.com', '7698313435', NULL, NULL, 'TCF 3.0 - Arpan Desai', 0, 'E-commerce', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:55:50', '2025-09-25 11:46:35', 20, 'No', 20, NULL),
(110, 12, 20, 'New line', '0', 'Keyur Shah', 'newlinegraphics@gmail.com', '9879478822', NULL, NULL, 'TCF 3.0 - Neil Shah', 0, 'Branding and Logo Design', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:57:21', '2025-09-25 11:45:14', 20, 'No', 20, NULL),
(111, 12, 20, 'Witty Technical Solutions', '0', 'Kiran Rajgiri', 'kiran@wittytechnicalsolutions.com', '9998487797', NULL, NULL, 'TCF 3.0 - Sachin Shah', 0, 'Information and Technology', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 11:59:04', '2025-09-25 11:44:27', 20, 'No', 20, NULL),
(112, 12, 20, 'Aura Clap', '0', 'Mubin mir', 'mubinmir@auraclap.com', '9327701171', NULL, NULL, 'TCF 3.0 - Krupa Shah', 0, 'Cleaning Services', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:00:39', '2025-09-25 11:43:55', 20, 'No', 20, NULL),
(113, 12, 20, 'Angel Isspl', '0', 'Nalini Patel', 'Info@angelisspl.com', '8511190971', NULL, NULL, 'TCF 3.0 - Nairuti Jambusaria', 0, 'Security system', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:02:43', '2025-09-25 11:42:54', 20, 'No', 20, NULL),
(114, 12, 20, 'Suvarnakala Pvt Ltd', '0', 'Nidhi Soni', 'darshan.soni@suvarnakala.com', '9924093900', NULL, NULL, 'TCF 3.0 - Ruchi Shah', 0, 'Jewellery', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:07:55', '2025-09-25 11:42:16', 20, 'No', 20, NULL),
(115, 12, 20, 'Nihar life style', '0', 'Nihar', 'niharcreations@gmail.com', '9824276686', NULL, NULL, 'Krupa Shah', 0, '-', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:10:27', '2025-09-25 11:41:21', 20, 'No', 20, NULL),
(146, 14, 22, 'ALP NISHIKAWA', '0', 'MAULIK', NULL, '9723755301', 'SANAND', NULL, 'IT IS IN FINAL STAGE', 55, NULL, 35, NULL, 240, 'f up', 22, '09-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 11:41:46', NULL, 22, 'Yes', 22, NULL),
(116, 12, 20, 'Vibrant BizCom Limited', '0', 'Nirav Shah', 'Nirav@vibrantbizcom.in', '6351013103', NULL, NULL, 'TCF 3.0 - Kaushal Shah', 0, 'Real Estate Developer', 34, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:12:43', '2025-09-26 12:06:40', 26, 'No', 20, NULL),
(117, 12, 20, 'Neo-Progress Fintech Pvt Ltd', '0', 'Nishit Thakkar', 'info@neoprogress.in', '9909971715', NULL, NULL, 'TCF 3.0 - Dr. Hemal', 0, 'Stock Broking', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:14:30', '2025-09-26 12:05:56', 26, 'No', 20, NULL),
(118, 12, 20, 'Plan Karo India', '0', 'Paras Tanna', 'Plankaroindia@gmail.com', '9900479700', NULL, NULL, 'TCF 3.0 - Nilesh Ghedia', 0, 'Mutual Fund', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:16:47', '2025-09-26 12:03:55', 26, 'No', 20, NULL),
(145, 14, 22, 'POLY PLASTIC', '0', 'DEEPAK', NULL, '8169136166', 'DASLANA', NULL, '6-10-2025 FOLLOW UP', 62, NULL, 35, NULL, 184, 'follow up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 11:39:36', NULL, 22, 'Yes', 22, NULL),
(119, 12, 20, 'Krishna paint', '0', 'Parul Joshi', 'paruljoshi16@gmail.com', '8849698317', NULL, NULL, 'TCF 3.0 - Sachin Shah', 0, 'Wallpaper paint texture', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:20:52', '2025-09-26 12:02:49', 26, 'No', 20, NULL),
(120, 12, 20, 'Smart Abacus', '0', 'Pinkesh Shah', 'smartcorpoff@gmail.com', '9328299433', NULL, NULL, 'TCF 3.0 - Dr Hemal', 0, 'Education', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:24:23', '2025-09-26 12:01:53', 26, 'No', 20, NULL),
(121, 12, 20, 'Picnify.in', '0', 'Pratham Nirav Purohit', 'Info@Picnify.in', '7575877760', NULL, NULL, 'TCF 3.0 - Sachin Shah', 0, 'Niche Category in Tours & Travel', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:26:42', '2025-09-26 12:00:51', 26, 'No', 20, NULL),
(122, 12, 20, 'Aakar Media & Entertainment', '0', 'Priyank Desai', 'Aakar.hello@gmail.com', '7698230642', NULL, NULL, 'TCF 3.0 - Dr Hemal Shah', 0, 'Entertainment', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:29:20', '2025-09-26 12:00:18', 26, 'No', 20, NULL),
(123, 12, 20, 'Prime wealth & Health IMF LLP', '0', 'Ram Chandak', 'primewealthandhealthimfllp@gmail.com', '9998819898', NULL, NULL, 'TCF 3.0 - Nilesh Ghedia', 0, 'Financial services', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:45:28', '2025-09-26 12:04:28', 26, 'No', 20, NULL),
(124, 12, 20, 'The Property Company', '0', 'Sachin Bhatt', 'info.thepropertyco@gmail.com', '8980413131', NULL, NULL, 'TCF 3.0 - Kaushal Shah', 0, 'Real estate', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:47:06', '2025-09-26 11:59:09', 26, 'No', 20, NULL),
(125, 12, 20, 'Rajeshwar corporation', '0', 'Samir Khandhar', 'Samir.khandhar@gmail', '9426173589', NULL, NULL, 'TCF 3.0 - Kaushal Shah', 0, 'Building materials supply', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:48:34', '2025-09-26 11:58:39', 26, 'No', 20, NULL),
(126, 12, 20, 'Welathzest Financial Services PVT LTD', '0', 'Sanket Khelurkar', 'Info@wealthzest.global', '9978272798', NULL, NULL, 'TCF 3.0 - Shubham', 0, 'Wealth Management', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:51:48', '2025-09-26 11:57:38', 20, 'No', 20, NULL),
(127, 12, 20, 'Taphook Technologies', '0', 'Satyam Iyer', 'Taphooktechnologies@gmail.com', '8780648124', NULL, NULL, 'TCF 3.0 - Darshana Soni', 0, 'NFC-QR Enabled Digital products', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:53:24', '2025-09-26 11:56:50', 26, 'No', 20, NULL),
(128, 12, 20, 'Vision enterprise', '0', 'Shaival Shah', 'Shahshaival51@yahoo.com', '8980348111', NULL, NULL, 'TCF 3.0 - Neil Shah', 0, 'Manufacturing and trading', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:54:50', '2025-09-26 11:56:24', 26, 'No', 20, NULL),
(129, 12, 20, 'D&M business consultancy', '0', 'Subodh', 'subodhmalasi11@gmail.com', '998490887', NULL, NULL, 'TCF 3,.0 - Sachin', 0, 'Business consultancy', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:56:17', '2025-09-26 11:55:14', 26, 'No', 20, NULL),
(130, 12, 20, 'Arch Axis architects', '0', 'Supal Modi', 'supal455@gmail.com', '8980405501', NULL, NULL, 'TCF 3.0 - Sachin Shah', 0, 'architecture', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 12:57:33', '2025-09-26 11:54:19', 26, 'No', 20, NULL),
(131, 12, 20, 'tantrum media', '0', 'Tanishi Tater', 'tanishi.works@gmail.com', '7977185734', NULL, NULL, 'TCF 3.0 - Rasshmi', 0, 'Marketing', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 13:01:52', '2025-09-26 11:52:30', 26, 'No', 20, NULL),
(132, 12, 20, 'The Crrest International', '0', 'Vishal Agarwal', 'vishal@thecrrestinternational.com', '7575880123', NULL, NULL, 'TCF 3.0 - Vivek Shah', 0, 'Flexible Packaging', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 13:04:47', '2025-09-26 12:09:11', 20, 'No', 20, NULL),
(133, 12, 20, 'Clip Interio', '0', 'Vishal Panchal', 'vishal@clipinterio.com', '9374433033', NULL, NULL, 'TCF 3.0 - Pramesh Shah', 0, 'Interior Fit-Out Projects', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 13:06:21', '2025-09-26 11:51:37', 26, 'No', 20, NULL),
(134, 12, 20, 'Crystal Info Solutions', '0', 'VViral Patel', 'viral@crystalinfo.co.in', '9737362121', NULL, NULL, 'TCF 3.0 - Dr Hemal', 0, 'Interior Fit-Out Projects', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-22 13:07:31', '2025-09-26 11:50:33', 26, 'No', 20, NULL),
(136, 12, 20, 'RUDRA CONSULTANCY', '0', 'NAINSINGH BANESINGH RAJPUT', 'ca.nayansingh@gmail.com', '9879895764', NULL, NULL, 'Accompanied with Suraj', 0, 'INVESTMENTS AND INSURANCE ADVISORY SERVICES', 33, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-25 09:30:03', '2025-09-25 10:53:18', 26, 'No', 20, NULL),
(137, 12, 20, 'Health Priority Physiotherapy Center', '0', 'Neel Upadhyay', 'Info@health-priority.in', '9974045207', NULL, NULL, 'Referred by Pramesh Shah', 0, 'Medical Services', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-25 09:52:52', '2025-09-25 10:52:31', 26, 'No', 20, NULL),
(138, 12, 20, 'Health Priority Physiotherapy Center', '0', 'Falgun Upadhyay', 'Info@health-priority.in', '8866003401', NULL, NULL, 'Referred by Pramesh Shah', 0, 'Medical services', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-25 09:55:14', '2025-09-26 12:07:16', 26, 'No', 20, NULL),
(141, 12, 20, 'MG Ceramic & Glass', '0', 'Niyant Parikh', 'niyantparikh07@gmail.com', '9016123274', NULL, NULL, 'Joined us as visitor in Optima Launch meet on 17th July.', 0, 'Tiles & Senetary', 31, NULL, 136, NULL, 0, NULL, 47, 32, NULL, 1, 0, '2025-09-25 10:28:27', '2025-09-25 10:50:29', 26, 'Yes', 20, NULL),
(140, 12, 20, 'Enact Consultancy for Tourism', '0', 'Monika Shah', 'info@enactconsultancy.com', '9825730683', NULL, NULL, 'Referred by Tejash Shah', 0, 'Service industry', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-25 10:05:18', '2025-09-25 10:44:45', 20, 'No', 20, NULL),
(144, 14, 22, 'RELIANCE', '0', 'DHRUV', NULL, '7863806984', 'JAMANAGAR', NULL, '5-10-2025 REVISE FOLLOW UP', 63, NULL, 27, NULL, 139, 'FOLLOW UP', 0, '06-10-2025 12:00 PM', 56, 0, NULL, 1, 0, '2025-09-25 11:35:53', NULL, 23, 'Yes', 22, NULL),
(143, 14, 22, 'GE STAMP', '0', 'RAHUL AND KRISHNA.', NULL, '9099025232', 'DETROJ', NULL, 'IT IS IN PROCESS', 55, NULL, 35, NULL, 186, 'F UP', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 11:29:13', NULL, 22, 'Yes', 22, NULL),
(147, 14, 22, 'TENNECO', '0', 'SANKET', NULL, '9427477473', 'SANAND', NULL, 'IT IS FINAL STAGE', 60, NULL, 35, NULL, 187, 'F UP', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 11:43:57', NULL, 22, 'Yes', 22, NULL),
(149, 14, 22, 'I J L', '0', 'HIMANSHU', NULL, '9978683181', 'SANAND', NULL, 'IT IS IN PROCESS', 55, NULL, 35, NULL, 189, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 11:49:18', NULL, 22, 'Yes', 22, NULL),
(150, 14, 22, 'ROCKEY MINDA', '0', 'GAURAV', NULL, '9998837465', 'VITTHALAPUR', NULL, 'UNDER APRROVAL', 55, NULL, 35, NULL, 190, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 11:51:08', NULL, 22, 'Yes', 22, NULL),
(171, 12, 20, 'Bhavii Joshii Branding', '0', 'Bhavi Joshi', 'bhv.joshi@gmail.com', '9313001573', NULL, NULL, 'Referred by Uttam Suthar', 0, 'Branding & Marketing', 32, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 08:28:00', NULL, 26, 'No', 20, NULL),
(151, 14, 22, 'DLJM', '0', 'MANISH', NULL, '6351890611', 'VITTHALAPUR', NULL, 'UNDER APPROVAL', 59, NULL, 35, NULL, 191, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 11:52:35', NULL, 22, 'Yes', 22, NULL),
(152, 14, 22, 'SHREE MAHAVIR METAL', '0', 'DUSHYANT', NULL, '8200998284', 'JAMNAGAR', NULL, 'UNDER APPROVAL', 62, NULL, 35, NULL, 192, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 11:54:41', NULL, 22, 'Yes', 22, NULL),
(153, 14, 22, 'NTF UNIT 9', '0', 'AMITESH', NULL, '8860001202', 'SANAND', NULL, 'UNDER APPROVAL', 0, 'FIFO RACK', 35, NULL, 229, 'F UP', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 11:58:33', NULL, 22, 'Yes', 22, NULL),
(154, 14, 22, 'LZWL', '0', 'JOYAL CRISTIN', NULL, '9106410265', 'HALOL', NULL, 'UNDER APPORVAL', 60, NULL, 35, NULL, 149, 'UNDER APPROVAL', 0, '06-10-2025 12:00 PM', 56, 0, NULL, 1, 0, '2025-09-25 12:02:00', NULL, 22, 'Yes', 22, NULL),
(155, 14, 22, 'ALU PLASTO', '0', 'AVANISH', NULL, '9712960993', 'VADODARA', NULL, 'SIZE PENDING', 60, NULL, 35, NULL, 209, 'he will provide detail then submit quote', 22, '06-10-2025 12:00 PM', 61, 0, '0', 1, 0, '2025-09-25 12:06:50', NULL, 22, 'Yes', 22, NULL),
(156, 14, 22, 'HARSHA ENGG', '0', 'PRATIK', NULL, '8238299921', 'CHANGODAR', NULL, 'UNDER APPROVAL', 62, NULL, 35, NULL, 206, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:08:15', NULL, 22, 'Yes', 22, NULL),
(158, 14, 22, 'MINDA KYURAKO', '0', 'DHAVAL', NULL, '7600509524', 'VITTHALAPUR', NULL, 'TROLLEY REWORK', 55, NULL, 35, NULL, 195, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:12:52', NULL, 22, 'Yes', 22, NULL),
(230, 5, 5, NULL, '0', 'nishita', NULL, '9265279666', NULL, NULL, 'React JS resource hiring', 5, NULL, 1, NULL, 215, 'React JS resource hiring', 0, '30-09-2025 5:00 PM', 19, 0, NULL, 1, 0, '2025-09-30 10:04:02', NULL, 5, 'Yes', 5, NULL),
(160, 14, 22, 'AMRON HORN', '0', 'MAHESH', NULL, '9825021883', 'BECHARAJI', NULL, 'UNDER APPROVAL', 60, NULL, 29, NULL, 236, 'FUP ON CALL', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:25:33', NULL, 23, 'Yes', 22, NULL),
(161, 14, 22, 'SUZUKI', '0', 'SHIVA', NULL, '7383631058', 'BECHARAJI', NULL, 'FOLLOW UP CONVEYOR', 0, 'ROLLER TROLLEY', 35, NULL, 230, 'F UP', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:27:45', NULL, 22, 'Yes', 22, NULL),
(162, 14, 22, 'YAZAKI', '0', 'GAURAV TRIPATHI', NULL, '9752170577', 'VITTHALAPUR', NULL, 'FOLLOW UP', 0, 'ROLLER CONVEYOR', 35, NULL, 198, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:29:40', NULL, 22, 'Yes', 22, NULL),
(163, 14, 22, 'GABRIEL', '0', 'RAJESH', NULL, '7904503453', 'SANAND', NULL, 'IT IS IN PROCESS', 61, NULL, 35, NULL, 208, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:31:12', NULL, 22, 'Yes', 22, NULL),
(164, 14, 22, 'VARUNA ELECTRIC', '0', 'VIJAY', NULL, '898083219', 'SANAND', NULL, 'FOLLOW UP', 55, NULL, 27, NULL, 199, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:32:32', NULL, 22, 'Yes', 22, NULL),
(165, 14, 22, 'TS TECH', '0', 'RAVAL', NULL, '8223058283', 'VITTHALAPUR', NULL, 'FOLLOW UP', 0, 'ROLLER CONVEYOR', 29, NULL, 200, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:33:45', NULL, 22, 'Yes', 22, NULL),
(166, 14, 22, 'ADIENT', '0', 'PARIMAL', NULL, '9227500474', 'SANAND', NULL, 'FOLLOW UP', 59, NULL, 29, NULL, 201, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:34:47', NULL, 22, 'Yes', 22, NULL),
(167, 14, 22, 'BUNDDY INDIA', '0', 'JALISHNA', NULL, '7490001957', 'VITTHALAPUR', NULL, 'FOLLOW UP', 55, NULL, 30, NULL, 202, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:36:02', NULL, 22, 'Yes', 22, NULL),
(168, 14, 22, 'T M SEATING', '0', 'RAM', NULL, '9574222440', 'DETROJ', NULL, 'FOLLOW UP', 0, 'STAND', 35, NULL, 203, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:37:32', NULL, 22, 'Yes', 22, NULL),
(169, 14, 22, 'HERO TEK', '0', 'JANVIR SINGH', NULL, '8160216922', 'BECHRAJI', NULL, 'FOLLOW UP', 63, NULL, 35, NULL, 204, 'f up', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:39:06', NULL, 22, 'Yes', 22, NULL),
(170, 14, 22, 'HIMALAYA PACK', '0', 'SUNIL', NULL, '7499340534', 'BECHARAJI', NULL, 'FOLLOW UP', 55, NULL, 30, NULL, 231, 'F UP', 22, '09-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-25 12:41:35', NULL, 22, 'Yes', 22, NULL),
(172, 12, 20, 'Polka Dots', '0', 'Khushi', 'sonalasnani22@gmail.com', '9157112551', NULL, NULL, 'Referred by Shubham and Ruchi', 0, 'Events n exhibition', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 08:37:03', NULL, 20, 'No', 20, NULL),
(173, 12, 20, 'Uncore Digital', '0', 'Rishva', 'rishva@uncoredigital.com', '9725518964', NULL, NULL, 'Referred by Dr. Namrata Tank', 0, 'IT Services', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 08:39:05', NULL, 26, 'No', 20, NULL),
(174, 12, 20, 'Friends Kitchen', '0', 'Jeet Parikh', 'jeet@parikhs.us', '9909259858', NULL, NULL, 'Referred by Chandani Verma', 0, 'Cafe and Catering', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 08:42:16', '2025-09-26 11:26:29', 26, 'No', 20, NULL),
(175, 12, 20, 'Dentistium', '0', 'Dr Jalak Modi', 'drjalakmodi@gmail.com', '9978983007', NULL, NULL, 'Referred by Vaishali Shrimal', 0, 'Dentist', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 08:48:37', NULL, 26, 'No', 20, NULL),
(176, 12, 20, 'Fragrance spray', '0', 'Hiral dixit', 'dixit32@gmail.com', '9913533313', NULL, NULL, 'Referred by Vaishali Soni', 0, 'Designer and niche perfumes brand seller', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 08:50:53', NULL, 26, 'No', 20, NULL),
(177, 12, 20, 'RB Buildcon', '0', 'Dharmen', 'rbbuildcon1929@gmail.com', '8128380781', NULL, NULL, 'Referred by Vanraj Modi', 0, 'Trunkey projects', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 08:57:58', NULL, 26, 'No', 20, NULL),
(178, 12, 20, 'Vitrag furniture', '0', 'Harsh vora', 'Vitragfurniture17@gmail.com', '9924145079', NULL, NULL, 'Referred by Parth Shah', 0, 'Furniture manufacturer', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 09:00:02', NULL, 26, 'No', 20, NULL),
(179, 12, 20, 'Sales Media', '0', 'Divya Patel', 'sonaradivya222@gmail.com', '7623899413', NULL, NULL, 'Referred by Devagnaya Shah', 0, 'Home Appliances', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 09:02:44', NULL, 26, 'No', 20, NULL),
(180, 12, 20, 'sharanamconsultants', '0', 'Pramesh Shah', 'pramesh.shah@sharanamconsultants.com', '9979865272', NULL, NULL, 'Referred by Shweta Jaiswal', 0, 'Construction Project Management Consultants', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 09:11:45', '2025-09-26 11:33:49', 26, 'No', 20, NULL),
(181, 12, 20, 'Health Culture The Diet Clinic', '0', 'Rajeshwareeba Jadeja', 'rajeshwaribajadeja108@gmail.com', '6353362476', NULL, NULL, 'Referred by Shalin Gandhi\'s Wife', 0, 'Health & Wellness', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 09:14:09', NULL, 26, 'No', 20, NULL),
(182, 12, 20, 'Rep Studio', '0', 'Pantha', 'Pantha.repstudio@gmail.fom', '7874721918', NULL, NULL, 'Referred by Namrata Tank', 0, 'Marketing Agency', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 09:17:48', NULL, 26, 'No', 20, NULL),
(183, 12, 20, 'Shaishya Pulse Arena', '0', 'Shenal Jhaveri', 'info.soel22@gmail.com', '9925236098', NULL, NULL, 'Refered by Shaymi Shaa', 0, 'Sports arena', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 09:24:37', NULL, 26, 'No', 20, NULL),
(199, 5, 5, 'suraliya', '0', 'darshan', NULL, '9909911462', NULL, NULL, 'Salexo demo', 56, NULL, 2, NULL, 176, 'salexo CRM', 0, '29-09-2025 2:00 PM', 19, 0, NULL, 1, 0, '2025-09-26 12:00:34', NULL, 12, 'Yes', 5, NULL),
(186, 12, 20, 'RM Studio', '0', 'Trilok Mistry', 'khamardhvani24@gmail.com', '9909722896', NULL, NULL, 'Referred by Groath member', 0, 'Architecture', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 09:41:28', NULL, 26, 'No', 20, NULL),
(187, 12, 20, 'netré', '0', 'Parthiv Patel', 'parthivpatel463@gmail.com', '9824690055', NULL, NULL, 'Referred by Rashmi', 0, 'Eyewear', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 09:43:31', NULL, 26, 'No', 20, NULL),
(188, 12, 20, 'Maahi Language Solution', '0', 'Dr Shachi Patel', 'maahilanguagesolution@gmail.com', '9824208617', NULL, NULL, 'Referred by Ruchi Shah', 0, 'Multilingual Content Writer & Podcaster', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 09:46:59', NULL, 26, 'No', 20, NULL),
(189, 12, 20, 'Palsa Design Studio', '0', 'Pauravi Desai', 'Pauravi@palsa.co', '9825295660', NULL, NULL, 'Referred by Vivek Shah', 0, 'Graphic design agency', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 09:50:03', NULL, 26, 'No', 20, NULL),
(190, 12, 20, 'Space Aura', '0', 'Urvi Dugar', 'urvidugarsdesign@gmail.com', '9558014763', NULL, NULL, 'Referred by Aditya Shukla', 0, 'Interior designer', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 09:52:56', NULL, 26, 'No', 20, NULL),
(191, 12, 20, 'Kashee HR Consultant', '0', 'Sheena Hardasani', 'Kasheehrconsultant@gmail.com', '8141253830', NULL, NULL, 'Referred by Kajal', 0, 'HR Services', 32, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 09:55:36', NULL, 20, 'No', 20, NULL),
(192, 12, 20, 'KT\'s Mart', '0', 'Krupali', 'rudravyas27@gmail.com', '9374323038', NULL, NULL, 'Referred by Yatin Shah', 0, 'Imitation jewellery', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 09:57:33', NULL, 26, 'No', 20, NULL),
(193, 12, 20, 'Beacon', '0', 'Dhaval Patel', 'dhaval@beaconworld.in', '6359701001', NULL, NULL, 'Reerred by Aditya Shukla', 0, 'Water and waste water treatment management', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 10:01:35', NULL, 26, 'No', 20, NULL),
(194, 12, 20, 'Elite Lifeversity', '0', 'Mohit Jagwani', 'jagwanimohit3@gmail.com', '7984929973', NULL, NULL, 'Referred by Rachana Tated', 0, 'Corporate Training', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 10:04:07', NULL, 26, 'No', 20, NULL),
(195, 12, 20, 'Chadar Mode', '0', 'Yash Dhandharia', 'YASHD1998@GMAIL.COM', '9687603431', NULL, NULL, 'Garima Tulsyan', 0, 'Home Furnishing', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 10:07:27', NULL, 26, 'No', 20, NULL),
(196, 12, 20, 'soosha', '0', 'akanksha soni', 'akankshakanskar93@gmail.com', '9752361276', NULL, NULL, 'Referred by Rachana', 0, 'jewellery', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 10:10:09', NULL, 26, 'No', 20, NULL),
(197, 12, 20, 'RK Commerce Trading', '0', 'Raggav Kabra', 'raghavvgkabrra@gmail.com', '9054103902', NULL, NULL, 'Referred by Rashmi Chajjer', 0, 'Clothing, Apparel, Shoes & Accessories', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 10:13:07', NULL, 26, 'No', 20, NULL),
(198, 14, 22, 'PPAP', '0', 'SATYA', NULL, '8393019104', 'VIRAMGAM', NULL, 'FOLLOW UP', 55, NULL, 35, NULL, 235, 'FOLLOW UP WITH LOKESH PPAP CORPO', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-26 11:26:14', NULL, 22, 'Yes', 22, NULL),
(200, 12, 20, 'RK Commerce Trading', '0', 'Raggav Gopal Kabra', 'raghavvgkabrra@gmail.com', '9054103902', NULL, NULL, 'Referred by Rasshmi', 0, 'Clothing, Apparel, Shoes & Accessories', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 12:13:57', NULL, 26, 'No', 20, NULL),
(201, 12, 20, 'JSRV Studios', '0', 'Vidit Vakharia', 'jsrvstudios@gmail.com', '8160028770', NULL, NULL, 'Referred by Ruchi Shah', 0, 'Podcast Shoot & Content Creation', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 12:15:27', NULL, 26, 'No', 20, NULL),
(203, 12, 20, 'Kayznutrizone', '0', 'Khyati	9824377013	Khyats30@gmail.com	Kayznutrizone	Dietician', 'Khyats30@gmail.com', '9824377013', NULL, NULL, 'Referred by Ruchi Shah', 0, 'Dietician', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 12:19:04', NULL, 26, 'No', 20, NULL),
(204, 12, 20, 'DEVAM Enterprise,iTronics and Techno Venture', '0', 'Amee Parikh', 'email2amee@gmail.com', '9662019662', NULL, NULL, 'Referred by Purva Bhavsar', 0, 'Mobile and accesories', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 12:20:48', NULL, 26, 'No', 20, NULL),
(207, 12, 20, 'Deval’s art', '0', 'Deval Patel', 'devalkanija@gmail.com', '9429357070', NULL, NULL, 'referred by ruchi shah', 0, 'Indian folk artist', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 12:26:16', NULL, 26, 'No', 20, NULL),
(206, 12, 20, 'Mysa', '0', 'Arya Kothari', 'kothari.arya@gmail.com', '9051682108', NULL, NULL, 'referred by GrOath Member', 0, 'Fitness studio', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 12:24:40', NULL, 26, 'No', 20, NULL),
(208, 12, 20, 'A-TaakE', '0', 'Minal Daga	7573873199	rathi17@gmail.com	A-TaakE	Sustainable living solutions', 'rathi17@gmail.com', '7573873199', NULL, NULL, 'referred by purva', 0, 'Sustainable living solution', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 12:27:53', NULL, 26, 'No', 20, NULL),
(209, 12, 20, 'PL Associates', '0', 'Bansil Thakkar		PL Associates	Home Decor Surface', 'ibansilsanjay@gmail.com', '8128883187', NULL, NULL, 'referred by purva', 0, 'Home Decor Surface', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 12:29:20', NULL, 26, 'No', 20, NULL),
(210, 12, 20, 'Keystone realty', '0', 'Nissarg parikh', 'Keystonerealty2016@gmail.com', '9909999954', NULL, NULL, 'Instagram', 0, 'Real Estate', 32, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 12:31:53', NULL, 20, 'No', 20, NULL),
(211, 12, 20, 'Nitramax', '0', 'Parul	8849698317	nitramaxwt@gmail.com	Nitramax	Water', 'nitramaxwt@gmail.com', '8849698317', NULL, NULL, 'referred by ruchi shah', 0, 'Water', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 12:33:21', NULL, 26, 'No', 20, NULL),
(212, 12, 20, 'Dr.archana s yoga', '0', 'Archana', 'Yogaarchana123@gmail.com', '8980460921', NULL, NULL, 'referred by sachin', 0, 'fitness', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 12:35:31', NULL, 26, 'No', 20, NULL),
(213, 12, 20, 'Vara Green builds & Realtors', '0', 'Arav Agarwal', 'varabuilds@gmail.com', '9836579043', NULL, NULL, 'referred by kaushal shah', 0, 'Green building constructio', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 12:36:55', NULL, 26, 'No', 20, NULL),
(214, 12, 20, 'Vibrant bizcom ltd.', '0', 'SAMIR KHANDHAR.		Vibrant biscom ltd.	Developer', 'samir@vibrantbuzcom.in', '9426173589', NULL, NULL, 'referred by kaushal shah', 0, 'Developer', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-26 12:38:19', NULL, 26, 'No', 20, NULL),
(215, 5, 5, NULL, '0', 'Venkat', NULL, '9662511341', NULL, NULL, 'ref from usa', 5, NULL, 1, NULL, 177, 'his ref in us and she want to make website.\r\nwe have to discuss with her in detail on google meet.', 0, '29-09-2025 12:00 PM', 19, 0, NULL, 1, 0, '2025-09-26 12:48:10', NULL, 5, 'Yes', 5, NULL),
(216, 5, 5, NULL, '0', 'Samir Gupta', NULL, '9327578369', NULL, NULL, 'SEO', 7, NULL, 1, NULL, 178, 'he will call us, confirm the time and visit for discussion with update in website and seo.', 0, '27-09-2025 12:00 PM', 19, 0, NULL, 1, 0, '2025-09-26 12:49:34', NULL, 5, 'Yes', 5, NULL),
(217, 5, 5, NULL, '0', 'V.P.Tejra', 'vptejra1958@gmail.com', '9327578369', NULL, NULL, '9327578369\r\n8866885232', 5, NULL, 1, NULL, 179, 'call to client @7:30', 0, '26-09-2025 12:00 PM', 19, 0, NULL, 1, 0, '2025-09-26 12:50:50', NULL, 5, 'Yes', 5, NULL),
(218, 12, 20, 'Plusmnf', '0', 'Anikesh Nahar', 'anikeshsjsnp@gmail.com', '8250767132', NULL, NULL, 'Facebook ad campaign', 0, 'Steel furnitures', 33, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-29 08:29:21', NULL, 20, 'No', 20, NULL),
(219, 12, 20, 'Nitramax water technology', '0', 'Parul', 'nitramaxwt@gmail.com', '8849698317', NULL, NULL, 'Referred by Ruchi Shah', 0, 'Water solution', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-29 08:31:36', NULL, 26, 'No', 20, NULL),
(220, 12, 20, 'Gravity Marketing', '0', 'Bhargav Trivedi', 'gravitymarketing.in@gmail.com', '7433062737', NULL, NULL, 'Referred by Yatin Shah', 0, 'Advertisement on OTT platform', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-29 08:33:49', NULL, 26, 'No', 20, NULL),
(221, 12, 20, 'Owsho marketplace', '0', 'Yash vyas', 'Yashvyas208@gmail.com', '7285854918', NULL, NULL, 'Referred by Dhruva', 0, 'E-commerce & local market place', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-29 08:36:15', NULL, 26, 'No', 20, NULL),
(222, 12, 20, 'Flash Fields by Kanisha Modi Photography', '0', 'Kanisha Modi', 'kanishaphotography@gmail.com', '9974044820', NULL, NULL, 'Referred by Priyanka Sirohia', 0, 'PHOTO/ Video', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-29 08:39:43', NULL, 26, 'No', 20, NULL),
(223, 12, 20, 'Shreeji Engineers', '0', 'Sanket Gohil', 'md@shreejiengineers.net', '9930293795', NULL, NULL, 'Referred by Rachana Tated', 0, 'Industrial Utility Products Co.', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-29 08:44:57', NULL, 26, 'No', 20, NULL),
(224, 12, 20, 'Granth Creations', '0', 'Krupa Shah', 'krupa@granth.info', '9428246632', NULL, NULL, 'Referred by Ruchi Shah', 0, 'Digital Branding and Marketing', 31, NULL, 0, NULL, 0, NULL, 44, 0, NULL, 1, 0, '2025-09-29 08:47:12', NULL, 26, 'No', 20, NULL),
(225, 14, 22, 'GE STAMP', '0', 'RAHUL PUVAR', NULL, '9099025232', 'VITHLAPUR DETROJ', NULL, 'PALLATE INQUIRY UNDER APPROVAL', 63, NULL, 35, NULL, 232, 'F UP FOR PO', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-29 11:33:24', NULL, 22, 'Yes', 22, NULL),
(226, 14, 22, 'Mitsubishi Electric Automotive India Pvt. Ltd.', '0', 'Anup Rai', NULL, '7861962270', 'SANAND', NULL, 'FOLLOW UP', 0, 'LOCKER', 35, NULL, 234, 'AFTER QUYE SEND', 22, '06-10-2025 12:00 PM', 61, 0, '0', 1, 0, '2025-09-29 11:34:48', NULL, 22, 'Yes', 22, NULL),
(227, 14, 22, 'NTF', '0', 'AMITESH', NULL, '8860001202', 'SANAND', NULL, 'FOLLOW UP', 66, NULL, 35, NULL, 233, 'F UP', 22, '06-10-2025 12:00 PM', 56, 0, '0', 1, 0, '2025-09-30 07:16:34', NULL, 22, 'Yes', 22, NULL),
(232, 5, 5, NULL, '0', 'satyanarayana', NULL, '9428360406', NULL, NULL, 'website development', 5, NULL, 1, NULL, 225, 'busy with some task call and confirm meeting at income tax', 5, '06-10-2025 1:00 AM', 19, 0, '0', 1, 0, '2025-10-03 10:36:44', NULL, 5, 'Yes', 5, NULL),
(234, 5, 5, 'ABC Pvt Ltd.', NULL, 'Prabhat', 'abcdeprabhat@gmail.com', '+91-9999999999', 'Sec 135, Noida, Uttar Pradesh', '+91-8888888888', 'I want to purchase an Empty Mineral Water Bottle. Kindly send me price and other details.Quantity: 100000 PieceProbable Order Value: Rs. 10 to 20 LakhProbable Requirement Type: Business Use', 70, NULL, 39, NULL, 0, NULL, 0, NULL, 17, 0, NULL, 1, 0, '2025-10-03 11:19:40', NULL, 0, NULL, 5, NULL),
(235, 5, 5, 'ABC Pvt Ltd.', NULL, 'Prabhat', 'abcdeprabhat@gmail.com', '+91-9999999999', 'Sec 135, Noida, Uttar Pradesh', '+91-8888888888', 'I want to purchase an Empty Mineral Water Bottle. Kindly send me price and other details.Quantity: 100000 PieceProbable Order Value: Rs. 10 to 20 LakhProbable Requirement Type: Business Use', 70, NULL, 39, NULL, 0, NULL, 0, NULL, 17, 0, NULL, 1, 0, '2025-10-03 11:20:03', NULL, 0, NULL, 5, NULL),
(236, 14, 22, 'Vijyalaxmi Fabrications Company', '0', 'Parth', NULL, '814036201', 'Becharaji', NULL, 'Reseller', 72, NULL, 27, NULL, 218, 'process in PO', 0, '31-10-2025 12:00 PM', 56, 0, NULL, 1, 0, '2025-10-03 11:41:14', NULL, 23, 'Yes', 22, NULL),
(237, 14, 22, 'saraswati industries', '0', 'Bahvik', NULL, '8859356060', 'sanand', NULL, 'Processing', 67, NULL, 27, NULL, 219, 'process', 0, '20-10-2025 12:00 PM', 56, 0, NULL, 1, 0, '2025-10-03 11:42:48', NULL, 23, 'Yes', 22, NULL),
(238, 14, 22, 'mayur hardware & electricals store', '0', 'tarunchoudhary', NULL, '968717392', 'vitthalapur', NULL, 'resell', 72, NULL, 27, NULL, 220, 'process', 0, '15-10-2025 12:00 PM', 56, 0, NULL, 1, 0, '2025-10-03 11:44:43', NULL, 23, 'Yes', 22, NULL),
(239, 14, 22, 'Haitian Huayun', '0', 'Hitesh Patel', NULL, '903356033', 'becharaji', NULL, 'processing', 63, NULL, 29, NULL, 221, 'disscuss', 0, '06-10-2025 12:00 PM', 56, 0, NULL, 1, 0, '2025-10-03 11:46:47', NULL, 23, 'Yes', 22, NULL),
(240, 5, 5, 'Future Office Solutions', '29AABCF2345E1Z7', 'Neha Sharma', 'neha.sharma@futureoffice.com', '+91-9812345678', 'Koramangala, Bangalore, Karnataka, India', '+91-9765432189', 'Looking for bulk purchase of ergonomic office chairs. Expected quantity: 500 units. Requirement Type: Corporate Office Setup.', 73, NULL, 40, NULL, 0, NULL, 0, NULL, 17, 0, NULL, 1, 0, '2025-10-03 11:58:03', NULL, 0, NULL, 5, NULL),
(241, 14, 22, 'Shree Ram Engineers', '0', 'Sunny Kumar', NULL, '8755833654', 'sanand', NULL, 'resell', 66, NULL, 27, NULL, 222, 'processs', 0, '06-10-2025 12:00 PM', 56, 0, NULL, 1, 0, '2025-10-03 12:03:44', NULL, 23, 'Yes', 22, NULL),
(242, 14, 22, 'Avanteclad Modular Solutions LLP', '0', 'ANUJ', NULL, '8894830120', 'ahmedabad', NULL, 'processing', 60, NULL, 27, NULL, 223, 'processing', 0, '06-10-2025 12:00 PM', 56, 0, NULL, 1, 0, '2025-10-03 12:05:18', NULL, 23, 'Yes', 22, NULL),
(243, 14, 22, 'Shree Radhe Industries', '0', 'GM Panchal', NULL, '9974634061', 'vadodara', NULL, 'processing', 55, NULL, 27, NULL, 224, 'visit plant', 0, '06-10-2025 12:00 PM', 56, 0, NULL, 1, 0, '2025-10-03 12:06:45', NULL, 23, 'Yes', 22, NULL),
(244, 5, 5, 'Startech Solutions Pvt. Ltd.', '24AAECS1234F1ZP', 'Rajesh Mehta', 'rajesh.mehta@startech.com', '9876543210', '403, Shree Arcade Complex, CG Road, Ahmedabad, Gujarat - 380009', '9123456789', 'Interested in CRM software solution, asked for proposal.', 74, NULL, 41, NULL, 0, NULL, 0, NULL, 17, 0, NULL, 1, 0, '2025-10-03 12:59:47', '2025-10-03 12:59:47', 26, '0', 5, NULL);
INSERT INTO `lead_master` (`lead_id`, `iCustomerId`, `iemployeeId`, `company_name`, `GST_No`, `customer_name`, `email`, `mobile`, `address`, `alternative_no`, `remarks`, `product_service_id`, `product_service_other`, `LeadSourceId`, `LeadSource_other`, `lead_history_id`, `comments`, `followup_by`, `next_followup_date`, `status`, `cancel_reason_id`, `amount`, `iStatus`, `isDelete`, `created_at`, `updated_at`, `employee_id`, `initially_contacted`, `iEnterBy`, `deal_converted_at`) VALUES
(245, 5, 5, 'Apex Builders & Developers', '07CCDFE5678H1Z3', 'Kavita Singh', 'kavita.singh@apexbuilders.com', '9998877665', '2nd Floor, Metro Plaza, Connaught Place, New Delhi - 110001', '9877001122', 'Interested in bulk order for construction equipment.', 75, NULL, 42, NULL, 0, NULL, 0, NULL, 17, 0, NULL, 1, 0, '2025-10-03 12:59:47', '2025-10-03 12:59:47', 24, '0', 5, NULL),
(247, 14, 22, 'NU VU CONAIR', '0', 'KALPESH', NULL, '9998003146', 'PIPLAJ', NULL, 'FOLLOW UP', 0, 'SCISSOR LIFT FOR 1 TON', 29, NULL, 238, 'FOLLOW UP', 0, '10-10-2025 12:00 PM', 56, 0, NULL, 1, 0, '2025-10-06 05:38:53', NULL, 27, 'Yes', 22, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_pipeline_master`
--

CREATE TABLE `lead_pipeline_master` (
  `pipeline_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `pipeline_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `slugname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin` int(11) NOT NULL DEFAULT '0',
  `followup_needed` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `followup_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `color` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lead_pipeline_master`
--

INSERT INTO `lead_pipeline_master` (`pipeline_id`, `company_id`, `pipeline_name`, `slugname`, `admin`, `followup_needed`, `followup_date`, `created_at`, `color`, `icon`) VALUES
(1, 1, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-07-02 19:42:48', '#000000', '<i class=\"fa-solid fa-plus\"></i>'),
(2, 1, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-07-02 19:42:48', '#000000', '<i class=\"fa-solid fa-check\"></i>'),
(3, 1, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-07-02 19:42:48', '#000000', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(4, 1, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-07-02 19:42:48', '#000000', '<i class=\"fa-solid fa-xmark\"></i>'),
(5, 2, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-07-02 21:10:20', '#000000', '<i class=\"fa-solid fa-plus\"></i>'),
(6, 2, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-07-02 21:10:20', '#000000', '<i class=\"fa-solid fa-check\"></i>'),
(7, 2, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-07-02 21:10:20', '#000000', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(8, 2, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-07-02 21:10:20', '#000000', '<i class=\"fa-solid fa-xmark\"></i>'),
(9, 3, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-07-02 21:11:03', '#33C1FF\n', '<i class=\"fa-solid fa-plus\"></i>'),
(10, 3, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-07-02 21:11:03', '#FFC107\n', '<i class=\"fa-solid fa-check\"></i>'),
(11, 3, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-07-02 21:11:03', '#FF5733', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(12, 3, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-07-02 21:11:03', '#28A745', '<i class=\"fa-solid fa-xmark\"></i>'),
(13, 4, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-07-03 08:50:28', '#33C1FF', '<i class=\"fa-solid fa-plus\"></i>'),
(14, 4, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-07-03 08:50:28', '#FFC107', '<i class=\"fa-solid fa-check\"></i>'),
(15, 4, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-07-03 08:50:28', '#28A745', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(16, 4, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-07-03 08:50:28', '#FF5733', '<i class=\"fa-solid fa-xmark\"></i>'),
(17, 5, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-07-03 10:58:23', '#FF5733', '<i class=\"fa-solid fa-plus\"></i>'),
(18, 5, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-07-03 10:58:23', '#33C1FF', '<i class=\"fa-solid fa-check\"></i>'),
(19, 5, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-07-03 10:58:23', '#28A745', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(20, 5, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-07-03 10:58:23', '#FFC107', '<i class=\"fa-solid fa-xmark\"></i>'),
(21, 6, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-07-03 11:00:40', '#FF5733', '<i class=\"fa-solid fa-plus\"></i>'),
(22, 6, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-07-03 11:00:40', '#33C1FF', '<i class=\"fa-solid fa-check\"></i>'),
(23, 6, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-07-03 11:00:40', '#28A745', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(24, 6, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-07-03 11:00:40', '#FFC107', '<i class=\"fa-solid fa-xmark\"></i>'),
(26, 7, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-07-03 12:55:49', '#FF5733', '<i class=\"fa-solid fa-plus\"></i>'),
(27, 7, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-07-03 12:55:49', '#33C1FF', '<i class=\"fa-solid fa-check\"></i>'),
(28, 7, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-07-03 12:55:49', '#28A745', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(29, 7, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-07-03 12:55:49', '#FFC107', '<i class=\"fa-solid fa-xmark\"></i>'),
(30, 8, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-07-03 12:57:37', '#FF5733', '<i class=\"fa-solid fa-plus\"></i>'),
(31, 8, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-07-03 12:57:37', '#33C1FF', '<i class=\"fa-solid fa-check\"></i>'),
(32, 8, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-07-03 12:57:37', '#28A745', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(33, 8, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-07-03 12:57:37', '#FFC107', '<i class=\"fa-solid fa-xmark\"></i>'),
(34, 10, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-08-05 15:08:50', '#FF5733', '<i class=\"fa-solid fa-plus\"></i>'),
(35, 10, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-08-05 15:08:50', '#33C1FF', '<i class=\"fa-solid fa-check\"></i>'),
(36, 10, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-08-05 15:08:50', '#28A745', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(37, 10, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-08-05 15:08:50', '#FFC107', '<i class=\"fa-solid fa-xmark\"></i>'),
(38, 11, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-08-05 17:34:59', '#FF5733', '<i class=\"fa-solid fa-plus\"></i>'),
(39, 11, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-08-05 17:34:59', '#33C1FF', '<i class=\"fa-solid fa-check\"></i>'),
(40, 11, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-08-05 17:34:59', '#28A745', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(41, 11, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-08-05 17:34:59', '#FFC107', '<i class=\"fa-solid fa-xmark\"></i>'),
(42, 11, 'galaxy comfort', 'galaxy-comfort', 0, 'yes', NULL, '2025-08-08 11:48:20', '#8a0f0f', NULL),
(43, 11, 'Grapits Premium texture', 'grapits-premium-texture', 0, 'yes', NULL, '2025-08-08 11:48:56', '#000000', NULL),
(44, 12, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-08-08 20:09:57', '#FF5733', '<i class=\"fa-solid fa-plus\"></i>'),
(45, 12, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-08-08 20:09:57', '#33C1FF', '<i class=\"fa-solid fa-check\"></i>'),
(46, 12, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-08-08 20:09:57', '#28A745', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(47, 12, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-08-08 20:09:57', '#FFC107', '<i class=\"fa-solid fa-xmark\"></i>'),
(48, 5, 'advance payment', 'advance-payment', 0, 'yes', NULL, '2025-08-15 21:46:34', '#1ec8bc', NULL),
(49, 5, 'In Production/Develpment', 'in-productiondevelpment', 0, 'yes', NULL, '2025-08-16 17:31:41', '#ffab00', NULL),
(50, 13, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-08-21 14:06:04', '#FF5733', '<i class=\"fa-solid fa-plus\"></i>'),
(51, 13, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-08-21 14:06:04', '#33C1FF', '<i class=\"fa-solid fa-check\"></i>'),
(52, 13, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-08-21 14:06:04', '#28A745', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(53, 13, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-08-21 14:06:04', '#FFC107', '<i class=\"fa-solid fa-xmark\"></i>'),
(54, 14, 'New Lead', 'new-lead', 1, 'yes', NULL, '2025-09-01 15:09:45', '#1da0d7', '<i class=\"fa-solid fa-plus\"></i>'),
(55, 14, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-09-01 15:09:45', '#33C1FF', '<i class=\"fa-solid fa-check\"></i>'),
(56, 14, 'quotation send', 'quotation-send', 1, 'yes', NULL, '2025-09-01 15:09:45', '#28a745', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(57, 14, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-09-01 15:09:45', '#FFC107', '<i class=\"fa-solid fa-xmark\"></i>'),
(58, 5, 'Dispatched', 'dispatched', 0, 'yes', NULL, '2025-09-01 16:45:51', '#db1fa9', NULL),
(60, 14, 'karan price send', 'karan-price-send', 0, 'yes', NULL, '2025-09-01 16:53:47', '#c16c0b', NULL),
(61, 14, 'pending quot', 'pending-quot', 0, 'yes', NULL, '2025-09-01 17:01:58', '#25d0a5', NULL),
(62, 5, 'Testing', 'testing', 0, 'yes', NULL, '2025-09-11 12:48:00', '#15cb18', NULL),
(63, 15, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-09-15 18:31:00', '#FF5733', '<i class=\"fa-solid fa-plus\"></i>'),
(64, 15, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-09-15 18:31:00', '#33C1FF', '<i class=\"fa-solid fa-check\"></i>'),
(65, 15, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-09-15 18:31:00', '#28A745', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(66, 15, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-09-15 18:31:00', '#FFC107', '<i class=\"fa-solid fa-xmark\"></i>'),
(67, 16, 'New Lead', 'new-lead', 1, 'no', NULL, '2025-09-16 18:02:21', '#FF5733', '<i class=\"fa-solid fa-plus\"></i>'),
(68, 16, 'Deal Done', 'deal-done', 1, 'no', NULL, '2025-09-16 18:02:21', '#33C1FF', '<i class=\"fa-solid fa-check\"></i>'),
(69, 16, 'Deal Pending', 'deal-pending', 1, 'no', NULL, '2025-09-16 18:02:21', '#28A745', '<i class=\"fa-solid fa-hourglass-start\"></i>'),
(70, 16, 'Deal Cancel', 'deal-cancel', 1, 'no', NULL, '2025-09-16 18:02:21', '#FFC107', '<i class=\"fa-solid fa-xmark\"></i>');

-- --------------------------------------------------------

--
-- Table structure for table `lead_source_master`
--

CREATE TABLE `lead_source_master` (
  `lead_source_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `lead_source_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lead_source_master`
--

INSERT INTO `lead_source_master` (`lead_source_id`, `company_id`, `lead_source_name`) VALUES
(1, 5, 'Google ads'),
(2, 5, 'Client Referance'),
(7, 8, 'Google'),
(8, 5, 'Linked Ad'),
(9, 5, 'Social Media'),
(16, 6, 'ondoor'),
(17, 5, 'India Mart'),
(18, 6, 'Google'),
(19, 11, 'India Mart'),
(20, 11, 'Display Visit'),
(21, 11, 'Google'),
(22, 11, 'Instagram'),
(23, 11, 'Whats app'),
(24, 11, 'Phone Call'),
(25, 11, 'indiamart'),
(26, 5, 'Groath'),
(27, 14, 'Indiamart'),
(29, 14, 'karan visit'),
(30, 14, 'kishan visit'),
(31, 12, 'Member Reference'),
(32, 12, 'Instagram Messenger'),
(33, 12, 'Social Media Campaign ( IG & FB )'),
(34, 12, 'The Cluster Fest Event'),
(35, 14, 'Yash sir'),
(39, 5, 'IndiaMart'),
(40, 5, 'JustDial'),
(41, 5, 'Linkedin'),
(42, 5, 'Reference');

-- --------------------------------------------------------

--
-- Table structure for table `lead_udf_data`
--

CREATE TABLE `lead_udf_data` (
  `id` int(11) NOT NULL,
  `lead_id` int(11) NOT NULL DEFAULT '0',
  `udf_id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(255) DEFAULT NULL,
  `iStatus` int(11) NOT NULL DEFAULT '1',
  `isDelete` int(11) NOT NULL DEFAULT '0',
  `created_at` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `strIP` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lead_udf_data`
--

INSERT INTO `lead_udf_data` (`id`, `lead_id`, `udf_id`, `value`, `iStatus`, `isDelete`, `created_at`, `updated_at`, `strIP`) VALUES
(3, 249, 4, 'Nisha', 1, 0, '2025-10-06 15:08:43', '2025-10-06 09:38:43', '103.1.100.226'),
(4, 249, 5, NULL, 1, 0, '2025-10-06 15:08:43', '2025-10-06 09:38:43', '103.1.100.226');

-- --------------------------------------------------------

--
-- Table structure for table `meta_data`
--

CREATE TABLE `meta_data` (
  `id` int(11) NOT NULL,
  `pagename` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `h1tag` text COLLATE utf8_unicode_ci,
  `h1taggrey` text COLLATE utf8_unicode_ci,
  `metaTitle` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `metaKeyword` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `metaDescription` varchar(8000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `head` text COLLATE utf8_unicode_ci,
  `body` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `meta_data`
--

INSERT INTO `meta_data` (`id`, `pagename`, `h1tag`, `h1taggrey`, `metaTitle`, `metaKeyword`, `metaDescription`, `head`, `body`, `created_at`, `updated_at`) VALUES
(1, 'home', NULL, NULL, 'Top Cataract Surgeon, Plastic Surgeon in Ahmedabad | Shine Aesthetic & Eye Clinic', 'Cataract surgery in Ahmedabad, Refractive surgery in Ahmedabad, Plastic surgery in Ahmedabad, Cataract Surgeon in Ahmedabad, Refractive Surgeon in Ahmedabad, plastic surgeon in Ahmedabad, Plastic surgery in Ahmedabad, Plastic Surgery Specialist in Ahmedabad', 'Shine Aesthetic & Eye Clinic, situated at Ahmedabad, Gujarat are a super speciality hospital providing a wide range of complete eye treatments and like Cataract surgery, Refractive surgery & Plastic surgery in Ahmedabad.', '<link rel=\"canonical\" href=\"https://www.shineclinic.org/\" />\r\n\r\n<meta property=\"og:url\" content=\"https://www.shineclinic.org/\">\r\n<meta property=\"og:type\" content=\"website\">\r\n<meta property=\"og:title\" content=\"Top Cataract Surgeon, Plastic Surgeon in Ahmedabad | Shine Aesthetic & Eye Clinic\">\r\n<meta property=\"og:description\" content=\"Shine Aesthetic & Eye Clinic, situated at Ahmedabad, Gujarat are a super speciality hospital providing a wide range of complete eye treatments and like Cataract surgery, Refractive surgery & Plastic surgery in Ahmedabad.\">\r\n<meta property=\"og:image\" content=\"https://www.shineclinic.org/Front/img/logo.png\">\r\n\r\n<meta name=\"twitter:card\" content=\"summary_large_image\">\r\n<meta property=\"twitter:domain\" content=\"shineclinic.org/\">\r\n<meta property=\"twitter:url\" content=\"https://www.shineclinic.org/\">\r\n<meta name=\"twitter:title\" content=\"Top Cataract Surgeon, Plastic Surgeon in Ahmedabad | Shine Aesthetic & Eye Clinic\">\r\n<meta name=\"twitter:description\" content=\"Shine Aesthetic & Eye Clinic, situated at Ahmedabad, Gujarat are a super speciality hospital providing a wide range of complete eye treatments and like Cataract surgery, Refractive surgery & Plastic surgery in Ahmedabad.\">\r\n<meta name=\"twitter:image\" content=\"https://www.shineclinic.org/Front/img/logo.png\">', NULL, '2021-06-09 08:31:40', '2025-05-22 12:44:43'),
(2, 'about', NULL, NULL, 'Dr. Gursimrat Paul Singh: Plastic Surgeons In Ahmedabad, Plastic Surgery Specialist Ahmedabad', 'Plastic Surgeon In Ahmedabad, Plastic Surgery Specialist Ahmedabad, Plastic surgery in Ahmedabad, best Plastic Surgeon In Ahmedabad, cosmetic plastic surgery in Ahmedabad, cosmetic plastic surgeon in Ahmedabad', 'Dr. Gursimrat Paul Singh from Shine Clinic is one of the leading plastic surgeon in Ahmedabad and a specialist in cosmetic plastic surgery in Ahmedabad.', '<link rel=\"canonical\" href=\"https://www.shineclinic.org/about-Dr-gursimrut\" />\r\n\r\n<meta property=\"og:url\" content=\"https://www.shineclinic.org/about-Dr-gursimrut\">\r\n<meta property=\"og:type\" content=\"website\">\r\n<meta property=\"og:title\" content=\"Dr. Gursimrat Paul Singh: Plastic Surgeons In Ahmedabad, Plastic Surgery Specialist Ahmedabad\">\r\n<meta property=\"og:description\" content=\"Dr. Gursimrat Paul Singh from Shine Clinic is one of the leading plastic surgeon in Ahmedabad and a specialist in cosmetic plastic surgery in Ahmedabad.\">\r\n<meta property=\"og:image\" content=\"https://www.shineclinic.org/Front/img/doctors/doctors-1.jpg\">\r\n\r\n<meta name=\"twitter:card\" content=\"summary_large_image\">\r\n<meta property=\"twitter:domain\" content=\"shineclinic.org/\">\r\n<meta property=\"twitter:url\" content=\"https://www.shineclinic.org/about-Dr-gursimrut\">\r\n<meta name=\"twitter:title\" content=\"Dr. Gursimrat Paul Singh: Plastic Surgeons In Ahmedabad, Plastic Surgery Specialist Ahmedabad\">\r\n<meta name=\"twitter:description\" content=\"Dr. Gursimrat Paul Singh from Shine Clinic is one of the leading plastic surgeon in Ahmedabad and a specialist in cosmetic plastic surgery in Ahmedabad.\">\r\n<meta name=\"twitter:image\" content=\"https://www.shineclinic.org/Front/img/doctors/doctors-1.jpg\">', NULL, '2021-06-09 08:31:40', '2024-10-24 08:58:55'),
(3, 'contact', NULL, NULL, 'Contact Us : Shine Aesthetic & Eye Clinic |Prahaladnagar, Ahmedabad', 'Cataract surgery in Ahmedabad, Refractive surgery in Ahmedabad, Plastic surgery in Ahmedabad, Cataract Surgeon in Ahmedabad, Refractive Surgeon in Ahmedabad, plastic surgeon in Ahmedabad, Plastic surgery in Ahmedabad', 'Shine Aesthetic & Eye Clinic, situated at Ahmedabad, Gujarat are a super speciality hospital providing a wide range of complete eye treatments and like Cataract surgery, Refractive surgery & Plastic surgery in Ahmedabad.', '<link rel=\"canonical\" href=\"https://www.shineclinic.org/contact-us\" />\r\n\r\n<meta property=\"og:url\" content=\"https://www.shineclinic.org/contact-us\">\r\n<meta property=\"og:type\" content=\"website\">\r\n<meta property=\"og:title\" content=\"Contact Us : Shine Aesthetic & Eye Clinic |Prahaladnagar, Ahmedabad\">\r\n<meta property=\"og:description\" content=\"Shine Aesthetic & Eye Clinic, situated at Ahmedabad, Gujarat are a super speciality hospital providing a wide range of complete eye treatments and like Cataract surgery, Refractive surgery & Plastic surgery in Ahmedabad. \">\r\n<meta property=\"og:image\" content=\"https://www.shineclinic.org/Front/img/logo.png\">\r\n\r\n<meta name=\"twitter:card\" content=\"summary_large_image\">\r\n<meta property=\"twitter:domain\" content=\"shineclinic.org\">\r\n<meta property=\"twitter:url\" content=\"https://www.shineclinic.org/contact-us\">\r\n<meta name=\"twitter:title\" content=\"Contact Us : Shine Aesthetic & Eye Clinic |Prahaladnagar, Ahmedabad\">\r\n<meta name=\"twitter:description\" content=\"Shine Aesthetic & Eye Clinic, situated at Ahmedabad, Gujarat are a super speciality hospital providing a wide range of complete eye treatments and like Cataract surgery, Refractive surgery & Plastic surgery in Ahmedabad. \">\r\n<meta name=\"twitter:image\" content=\"https://www.shineclinic.org/Front/img/logo.png\">', NULL, '2021-06-09 08:32:23', '2024-10-23 12:57:32'),
(4, 'Aesthetic Services', NULL, NULL, 'Best Plastic Surgeons In Ahmedabad, Plastic Surgery Specialist Ahmedabad', 'Plastic Surgeon In Ahmedabad, Plastic Surgery Specialist Ahmedabad, Plastic surgery in Ahmedabad, best Plastic Surgeon In Ahmedabad, cosmetic plastic surgery in Ahmedabad, cosmetic plastic surgeon in Ahmedabad, Plastic Surgery Specialist doctor in Ahmedabad, Plastic Surgery hospital in Ahmedabad', 'Shine Aesthetic & Eye Clinic Ahmedabad, Top rated hospital for Plastic surgery in Ahmedabad, offers Best Plastic & Cosmetic Surgery services and treatment by best plastic surgeons in Ahmedabad.', '<link rel=\"canonical\" href=\"https://www.shineclinic.org/aesthetic-services\" />\r\n\r\n<meta property=\"og:url\" content=\"https://www.shineclinic.org/aesthetic-services\">\r\n<meta property=\"og:type\" content=\"website\">\r\n<meta property=\"og:title\" content=\"Best Plastic Surgeons In Ahmedabad, Plastic Surgery Specialist Ahmedabad\">\r\n<meta property=\"og:description\" content=\"Shine Aesthetic & Eye Clinic Ahmedabad, Top rated hospital for Plastic surgery in Ahmedabad, offers Best Plastic & Cosmetic Surgery services and treatment by best plastic surgeons in Ahmedabad. \">\r\n<meta property=\"og:image\" content=\"https://www.shineclinic.org/Front/img/services/plastic-surgery.jpg\">\r\n\r\n<meta name=\"twitter:card\" content=\"summary_large_image\">\r\n<meta property=\"twitter:domain\" content=\"shineclinic.org\">\r\n<meta property=\"twitter:url\" content=\"https://www.shineclinic.org/aesthetic-services\">\r\n<meta name=\"twitter:title\" content=\"Best Plastic Surgeons In Ahmedabad, Plastic Surgery Specialist Ahmedabad\">\r\n<meta name=\"twitter:description\" content=\"Shine Aesthetic & Eye Clinic Ahmedabad, Top rated hospital for Plastic surgery in Ahmedabad, offers Best Plastic & Cosmetic Surgery services and treatment by best plastic surgeons in Ahmedabad. \">\r\n<meta name=\"twitter:image\" content=\"https://www.shineclinic.org/Front/img/services/plastic-surgery.jpg\">', NULL, '2021-06-09 08:32:23', '2024-10-24 07:24:14'),
(5, 'Eye treatments', NULL, NULL, 'Best Cataract Surgery Ahmedabad, Refractive Surgery for myopia in Ahmedabad', 'Cataract Surgery, Refractive Surgery, Cataract Surgery Ahmedabad, Refractive Surgery Ahmedabad, Cataract Surgery in Ahmedabad, Refractive Surgery in Ahmedabad, Refractive Surgery for Myopia, Cataract Surgeon in Ahmedabad, Refractive Surgeon in Ahmedabad, Cataract Surgery Specialist in Ahmedabad', 'Shine Aesthetic & Eye Clinic Ahmedabad, Top rated hospital for cataract surgery and Refractive surgery for myopia in Ahmedabad, We have expert Cataract and Refractive Surgeons and doctors.', '<link rel=\"canonical\" href=\"https://www.shineclinic.org/eye-treatments\" />\r\n\r\n<meta property=\"og:url\" content=\"https://www.shineclinic.org/eye-treatments\">\r\n<meta property=\"og:type\" content=\"website\">\r\n<meta property=\"og:title\" content=\"Best Cataract Surgery Ahmedabad, Refractive Surgery for myopia in Ahmedabad\">\r\n<meta property=\"og:description\" content=\"Shine Aesthetic & Eye Clinic Ahmedabad, Top rated hospital for cataract surgery and Refractive surgery for myopia in Ahmedabad, We have expert Cataract and Refractive Surgeons and doctors.\">\r\n<meta property=\"og:image\" content=\"https://www.shineclinic.org/Front/img/services/cataract.jpg\">\r\n\r\n<meta name=\"twitter:card\" content=\"summary_large_image\">\r\n<meta property=\"twitter:domain\" content=\"shineclinic.org\">\r\n<meta property=\"twitter:url\" content=\"https://www.shineclinic.org/eye-treatments\">\r\n<meta name=\"twitter:title\" content=\"Best Cataract Surgery Ahmedabad, Refractive Surgery for myopia in Ahmedabad\">\r\n<meta name=\"twitter:description\" content=\"Shine Aesthetic & Eye Clinic Ahmedabad, Top rated hospital for cataract surgery and Refractive surgery for myopia in Ahmedabad, We have expert Cataract and Refractive Surgeons and doctors.\">\r\n<meta name=\"twitter:image\" content=\"https://www.shineclinic.org/Front/img/services/cataract.jpg\">', NULL, '2021-06-09 08:32:54', '2024-10-23 13:03:48'),
(6, 'Eye treatments and  Aesthetic Services gallery', NULL, NULL, 'Gallery | Eye treatments | Aesthetic Services in ahmedabad', 'Cataract, Corneal transplantation, Refractive surgery, Ocular surface disorders, Ocular Trauma and Cosmetic Surgeon, Trauma Surgeon, Burns Management, A- V Fistula Surgeon, Plastic Surgeon', 'Best eye treatments & Aesthetic Services in Prahladnager, Ahmedabad', '<link rel=\"canonical\" href=\"https://www.shineclinic.org/photogallery\" />\r\n\r\n<meta property=\"og:url\" content=\"https://www.shineclinic.org/photogallery\">\r\n<meta property=\"og:type\" content=\"website\">\r\n<meta property=\"og:title\" content=\"Gallery | Eye treatments | Aesthetic Services in ahmedabad\">\r\n<meta property=\"og:description\" content=\"Best eye treatments & Aesthetic Services in Prahladnager, Ahmedabad\">\r\n<meta property=\"og:image\" content=\"https://www.shineclinic.org/Gallery/1726491705.JPG\">\r\n\r\n<meta name=\"twitter:card\" content=\"summary_large_image\">\r\n<meta property=\"twitter:domain\" content=\"shineclinic.org/\">\r\n<meta property=\"twitter:url\" content=\"https://www.shineclinic.org/photogallery\">\r\n<meta name=\"twitter:title\" content=\"Gallery | Eye treatments | Aesthetic Services in ahmedabad\">\r\n<meta name=\"twitter:description\" content=\"Best eye treatments & Aesthetic Services in Prahladnager, Ahmedabad\">\r\n<meta name=\"twitter:image\" content=\"https://www.shineclinic.org/Gallery/1726491705.JPG\">', NULL, '2021-06-09 08:32:54', '2024-10-23 13:07:16'),
(7, 'Eye treatments and  Aesthetic Services video', NULL, NULL, 'Video | Eye treatments | Aesthetic Services in ahmedabad', 'Cataract, Corneal transplantation, Refractive surgery, Ocular surface disorders, Ocular Trauma and Cosmetic Surgeon, Trauma Surgeon, Burns Management, A- V Fistula Surgeon, Plastic Surgeon', 'Videos for Best eye treatments & Aesthetic Services in Prahladnager, Ahmedabad', '<meta property=\"og:url\" content=\"https://www.shineclinic.org/videogallery\">\r\n<meta property=\"og:type\" content=\"website\">\r\n<meta property=\"og:title\" content=\"Video | Eye treatments | Aesthetic Services in ahmedabad\">\r\n<meta property=\"og:description\" content=\"Videos for Best eye treatments & Aesthetic Services in Prahladnager, Ahmedabad\">\r\n<meta property=\"og:image\" content=\"https://www.shineclinic.org/Front/img/logo.png\">\r\n\r\n<meta name=\"twitter:card\" content=\"summary_large_image\">\r\n<meta property=\"twitter:domain\" content=\"shineclinic.org\">\r\n<meta property=\"twitter:url\" content=\"https://www.shineclinic.org/videogallery\">\r\n<meta name=\"twitter:title\" content=\"Video | Eye treatments | Aesthetic Services in ahmedabad\">\r\n<meta name=\"twitter:description\" content=\"Videos for Best eye treatments & Aesthetic Services in Prahladnager, Ahmedabad\">\r\n<meta name=\"twitter:image\" content=\"https://www.shineclinic.org/Front/img/logo.png\">', NULL, '2021-06-09 08:32:54', '2024-10-23 13:10:49'),
(8, 'Know Your Shwetambari', NULL, NULL, 'Best Cataract Surgeon in Ahmedabad | Refractive Surgeon Ahmedabad | Dr. Shwetambari Singh', 'Cataract Surgeon in Ahmedabad, Refractive Surgeon in Ahmedabad, Cataract Surgeon Ahmedabad, Refractive Surgeon Ahmedabad, Cataract Operation Doctors In Ahmedabad, Cataract Surgery Specialist in Ahmedabad', 'Dr. Shwetambari Singh is one of the best Cataract Surgeon and Refractive Surgeon in Ahmedabad, She worked as Cataract, Cornea and Refractive surgery consultant for 15 years.', '<link rel=\"canonical\" href=\"https://www.shineclinic.org/about-Dr-shwetambari\" />\r\n\r\n<meta property=\"og:url\" content=\"https://www.shineclinic.org/about-Dr-shwetambari\">\r\n<meta property=\"og:type\" content=\"website\">\r\n<meta property=\"og:title\" content=\"Best Cataract Surgeon in Ahmedabad | Refractive Surgeon Ahmedabad | Dr. Shwetambari Singh\">\r\n<meta property=\"og:description\" content=\"Dr. Shwetambari Singh is one of the best Cataract Surgeon and Refractive Surgeon in Ahmedabad, She worked as Cataract, Cornea and Refractive surgery consultant for 15 years.\">\r\n<meta property=\"og:image\" content=\"https://www.shineclinic.org/Front/img/doctors/doctors-2.jpg\">\r\n\r\n<meta name=\"twitter:card\" content=\"summary_large_image\">\r\n<meta property=\"twitter:domain\" content=\"shineclinic.org/\">\r\n<meta property=\"twitter:url\" content=\"https://www.shineclinic.org/about-Dr-shwetambari\">\r\n<meta name=\"twitter:title\" content=\"Best Cataract Surgeon in Ahmedabad | Refractive Surgeon Ahmedabad | Dr. Shwetambari Singh\">\r\n<meta name=\"twitter:description\" content=\"Dr. Shwetambari Singh is one of the best Cataract Surgeon and Refractive Surgeon in Ahmedabad, She worked as Cataract, Cornea and Refractive surgery consultant for 15 years.\">\r\n<meta name=\"twitter:image\" content=\"https://www.shineclinic.org/Front/img/doctors/doctors-2.jpg\">', NULL, '2024-10-24 08:39:45', '2024-10-24 08:56:57'),
(9, 'Cataract', NULL, NULL, 'Best Cataract Surgery Ahmedabad, Cataract Operation Doctors In Ahmedabad', 'Cataract Surgery, Cataract Surgery Ahmedabad, Cataract Surgery in Ahmedabad, Cataract Surgeon in Ahmedabad, Cataract Surgery Specialist in Ahmedabad, Cataract Operation in Ahmedabad, Cataract Doctors In Ahmedabad, Cataract Specialist Doctors In Ahmedabad', 'Shine Eye Clinic Ahmedabad is the best hospital for cataract surgery and cataract removal in Ahmedabad. We have expert Cataract Surgeon and Cataract Specialist Doctors.', '<link rel=\"canonical\" href=\"https://www.shineclinic.org/cataract\" />\r\n\r\n<meta property=\"og:url\" content=\"https://www.shineclinic.org/cataract\">\r\n<meta property=\"og:type\" content=\"website\">\r\n<meta property=\"og:title\" content=\"Best Cataract Surgery Ahmedabad, Cataract Operation Doctors In Ahmedabad\">\r\n<meta property=\"og:description\" content=\"Shine Eye Clinic Ahmedabad is the best hospital for cataract surgery and cataract removal in Ahmedabad. We have expert Cataract Surgeon and Cataract Specialist Doctors.\">\r\n<meta property=\"og:image\" content=\"https://www.shineclinic.org/Front/img/cataract/4.jpg\">\r\n\r\n<meta name=\"twitter:card\" content=\"summary_large_image\">\r\n<meta property=\"twitter:domain\" content=\"shineclinic.org/\">\r\n<meta property=\"twitter:url\" content=\"https://www.shineclinic.org/cataract\">\r\n<meta name=\"twitter:title\" content=\"Best Cataract Surgery Ahmedabad, Cataract Operation Doctors In Ahmedabad\">\r\n<meta name=\"twitter:description\" content=\"Shine Eye Clinic Ahmedabad is the best hospital for cataract surgery and cataract removal in Ahmedabad. We have expert Cataract Surgeon and Cataract Specialist Doctors.\">\r\n<meta name=\"twitter:image\" content=\"https://www.shineclinic.org/Front/img/cataract/4.jpg\">', NULL, '2024-10-24 08:40:45', '2024-10-24 09:07:38'),
(10, 'Cornea', NULL, NULL, 'Cornea Surgeon - Cornea Specialist in Ahmedabad | Dr. Shwetambari Singh', 'Cornea Surgeon in Ahmedabad, Cornea Specialist in Ahmedabad, Cornea Specialist Doctor in Ahmedabad', 'Dr. Shwetambari Singh is a best Cornea specialist or the best Cornea Surgeon in Ahmedabad, she having experience of more than 20 years in this field.', '<link rel=\"canonical\" href=\"https://www.shineclinic.org/cornea\" />\r\n\r\n<meta property=\"og:url\" content=\"https://www.shineclinic.org/cornea\">\r\n<meta property=\"og:type\" content=\"website\">\r\n<meta property=\"og:title\" content=\"Cornea Surgeon - Cornea Specialist in Ahmedabad | Dr. Shwetambari Singh\">\r\n<meta property=\"og:description\" content=\"Dr. Shwetambari Singh is a best Cornea specialist or the best Cornea Surgeon in Ahmedabad, she having experience of more than 20 years in this field.\">\r\n<meta property=\"og:image\" content=\"https://www.shineclinic.org/Front/img/cornea/1.jpg\">\r\n\r\n<meta name=\"twitter:card\" content=\"summary_large_image\">\r\n<meta property=\"twitter:domain\" content=\"shineclinic.org/\">\r\n<meta property=\"twitter:url\" content=\"https://www.shineclinic.org/cornea\">\r\n<meta name=\"twitter:title\" content=\"Cornea Surgeon - Cornea Specialist in Ahmedabad | Dr. Shwetambari Singh\">\r\n<meta name=\"twitter:description\" content=\"Dr. Shwetambari Singh is a best Cornea specialist or the best Cornea Surgeon in Ahmedabad, she having experience of more than 20 years in this field.\">\r\n<meta name=\"twitter:image\" content=\"https://www.shineclinic.org/Front/img/cornea/1.jpg\">', NULL, '2024-10-24 08:40:45', '2024-10-24 09:26:02');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `getId` int(11) NOT NULL DEFAULT '0' COMMENT 'as emp_id',
  `name` varchar(255) DEFAULT NULL,
  `service` text,
  `title` varchar(255) DEFAULT NULL,
  `body` text,
  `guid` text,
  `type` text,
  `iTripId` text,
  `fcm_token` text,
  `status` text,
  `response` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `strIP` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `getId`, `name`, `service`, `title`, `body`, `guid`, `type`, `iTripId`, `fcm_token`, `status`, `response`, `created_at`, `updated_at`, `strIP`) VALUES
(2, 5, 'Apollo Infotech', '0', 'Smaart CRM', 'Reminder: Follow up with Rahul Mehta for lead ID: 18 at 24-07-2025 12:00 PM', '0', 'daily_lead', '0', 'c5X_IgiKRYCsmM8WU1CRVQ:APA91bFbT21RZLd00_6yvH4kGFc1my41M1gneYnuEBKE7pcEpf2NNly5qo9tLvE85CG-cxO3JLDSQJ9esU_C8Gcx2kjLKecrDvpIkegjXNEDsVgZRW6XWys', 'pending', NULL, '2025-07-24 06:26:00', '2025-07-24 06:26:00', NULL),
(3, 5, 'Apollo Infotech', '0', 'Smaart CRM', 'Reminder: Follow up with Rahul Mehta for lead ID: 18 at 24-07-2025 12:00 PM', '0', 'daily_lead', '0', 'c5X_IgiKRYCsmM8WU1CRVQ:APA91bFbT21RZLd00_6yvH4kGFc1my41M1gneYnuEBKE7pcEpf2NNly5qo9tLvE85CG-cxO3JLDSQJ9esU_C8Gcx2kjLKecrDvpIkegjXNEDsVgZRW6XWys', 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/lms-smaart/messages:send` resulted in a `404 Not Found` response:\n{\n  \"error\": {\n    \"code\": 404,\n    \"message\": \"Requested entity was not found.\",\n    \"status\": \"NOT_FOUND\",\n    \"detail (truncated...)\n', '2025-07-24 06:26:15', '2025-07-24 06:26:15', NULL),
(4, 6, 'Navdeep Products', '0', 'Smaart CRM', 'Reminder: Follow up with Rahul Mehta for lead ID: 18 at 24-07-2025 12:00 PM', '0', 'daily_lead', '0', 'eEC6AKP6Ql-Ik2kAVT1ZIG:APA91bE-lBgngUH8s9bHnBUlysP2BPuneou4WF2iH7onQEaYlxofzKU-eO_IxVMQXDof653m4S2J4tj1B-ZDWIhnUyOr20KevXTxoZzn7CQB1NvpsJKMljc', 'sent', '{\n  \"name\": \"projects/lms-smaart/messages/0:1753338375897237%2eeb4cca2eeb4cca\"\n}\n', '2025-07-24 06:26:15', '2025-07-24 06:26:15', NULL),
(5, 9, 'Testing', '0', 'Smaart CRM', 'Reminder: Follow up with Rahul Mehta for lead ID: 18 at 24-07-2025 12:00 PM', '0', 'daily_lead', '0', 'dvik0K0bQ3uAST_aZmRpCp:APA91bGWLDTGrz4uhM8NNVyOaEU3qMHN9tG2-KAlQfGFJJEfV2J_DIsMSg46Cu2b-wXW7N9hcH704xeiyWu6LOEjO_tvUF_8DOd1pWnJfhjXlp1Hf3gzugs', 'sent', '{\n  \"name\": \"projects/lms-smaart/messages/0:1753338376131951%2eeb4cca2eeb4cca\"\n}\n', '2025-07-24 06:26:15', '2025-07-24 06:26:16', NULL),
(6, 9, 'Testing', '0', 'Smaart CRM', 'Reminder: Follow up with Rahul Mehta for lead ID: 18 at 24-07-2025 12:12 PM', '0', 'daily_lead', '0', 'dvik0K0bQ3uAST_aZmRpCp:APA91bGWLDTGrz4uhM8NNVyOaEU3qMHN9tG2-KAlQfGFJJEfV2J_DIsMSg46Cu2b-wXW7N9hcH704xeiyWu6LOEjO_tvUF_8DOd1pWnJfhjXlp1Hf3gzugs', 'sent', '{\n  \"name\": \"projects/lms-smaart/messages/0:1753338603858414%2eeb4cca2eeb4cca\"\n}\n', '2025-07-24 06:30:03', '2025-07-24 06:30:03', NULL),
(7, 5, 'Apollo Infotech', '0', 'Smaart CRM', 'Reminder: Follow up with Namra for lead ID: 32 at 25-07-2025 12:00 PM', '0', 'daily_lead', '0', NULL, 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/lms-smaart/messages:send` resulted in a `400 Bad Request` response:\n{\n  \"error\": {\n    \"code\": 400,\n    \"message\": \"Recipient of the message is not set.\",\n    \"status\": \"INVALID_ARGUMENT\", (truncated...)\n', '2025-07-25 06:15:03', '2025-07-25 06:15:04', NULL),
(8, 5, 'Apollo Infotech', '0', 'Smaart CRM', 'Reminder: Follow up with Rahul Dixit for lead ID: 34 at 25-07-2025 2:00 PM', '0', 'daily_lead', '0', 'f3TfzfITQ6qUrj6cHVMwXm:APA91bFHVqNxp213iZaXKcFjivZ45gg-c43c5FIw_3HXkZ7ogQcn2nclLtLk267LQfhX10SmIhgeyRI1ntLr0B8RMGox4YvXeJWBQEgGXgrCuHrxKOeCiF0', 'sent', '{\n  \"name\": \"projects/lms-smaart/messages/0:1753431304414136%2eeb4cca2eeb4cca\"\n}\n', '2025-07-25 08:15:04', '2025-07-25 08:15:04', NULL),
(9, 6, 'Navdeep Products', '0', 'Smaart CRM', 'Reminder: Follow up with aaaaaaa for lead ID: 38 at 25-07-2025 3:15 PM', '0', 'daily_lead', '0', 'dlIhJmGUQf-bW1KiNKqZSw:APA91bF442tT_j-xsP7rQfEYUSz64ZvO3l0iRHZwWaHcj-Ia4jUaJUI48mq9IaYo5vn7rCk833AOIa48I_ZGa04DvuMpa-xYP2tUthp-Rn0zaNUl5gaqAtA', 'sent', '{\n  \"name\": \"projects/lms-smaart/messages/0:1753435804259765%2eeb4cca2eeb4cca\"\n}\n', '2025-07-25 09:30:03', '2025-07-25 09:30:04', NULL),
(10, 6, 'Navdeep Products', '0', 'Smaart CRM', 'Reminder: Follow up with karan patel for lead ID: 43 at 26-07-2025 11:00 AM', '0', 'daily_lead', '0', 'd45JZv5vTAK98qhyCIMCjI:APA91bGOWJ4Vl0oWMZlDvGAZl-5QkNFJU6T21ha7tYYB6p_JGHqyFUcVsfCxmwvfZiv6O9aRdoQwa2HV3mjqXa6a_LVvYHjbItdaYZTsPuB0Pd0bSWhi1xU', 'sent', '{\n  \"name\": \"projects/lms-smaart/messages/0:1753506903906396%2eeb4cca2eeb4cca\"\n}\n', '2025-07-26 05:15:03', '2025-07-26 05:15:03', NULL),
(11, 5, 'Apollo Infotech', '0', 'Smaart CRM', 'Reminder: Follow up with Mr Vasu for lead ID: 35 at 26-07-2025 12:00 PM', '0', 'daily_lead', '0', NULL, 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/lms-smaart/messages:send` resulted in a `400 Bad Request` response:\n{\n  \"error\": {\n    \"code\": 400,\n    \"message\": \"Recipient of the message is not set.\",\n    \"status\": \"INVALID_ARGUMENT\", (truncated...)\n', '2025-07-26 06:15:02', '2025-07-26 06:15:03', NULL),
(12, 5, 'Apollo Infotech', '0', 'Smaart CRM', 'Reminder: Follow up with Harsh for lead ID: 40 at 26-07-2025 12:00 PM', '0', 'daily_lead', '0', NULL, 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/lms-smaart/messages:send` resulted in a `400 Bad Request` response:\n{\n  \"error\": {\n    \"code\": 400,\n    \"message\": \"Recipient of the message is not set.\",\n    \"status\": \"INVALID_ARGUMENT\", (truncated...)\n', '2025-07-26 06:15:03', '2025-07-26 06:15:03', NULL),
(13, 5, 'Apollo Infotech', '0', 'Smaart CRM', 'Reminder: Follow up with Namra for lead ID: 32 at 26-07-2025 12:00 PM', '0', 'daily_lead', '0', NULL, 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/lms-smaart/messages:send` resulted in a `400 Bad Request` response:\n{\n  \"error\": {\n    \"code\": 400,\n    \"message\": \"Recipient of the message is not set.\",\n    \"status\": \"INVALID_ARGUMENT\", (truncated...)\n', '2025-07-26 06:15:03', '2025-07-26 06:15:03', NULL),
(14, 6, 'Navdeep Products', '0', 'Smaart CRM', 'Reminder: Follow up with Mignesh Patel for lead ID: 36 at 26-07-2025 1:15 PM', '0', 'daily_lead', '0', 'egXwA2qeTGuEJ2uydERxNT:APA91bFSz1pvcdBrO9eft8fcL89WGIULRCA34RGPVWkGzJBWDo75FKM6px5N-BkDX_L4adnC3SKHTfkiQt_JZ3HAvO4700YdioLDryWlU5ak0HmcvzoCptk', 'sent', '{\n  \"name\": \"projects/lms-smaart/messages/0:1753515004540943%2eeb4cca2eeb4cca\"\n}\n', '2025-07-26 07:30:04', '2025-07-26 07:30:04', NULL),
(15, 5, 'Apollo Infotech', '0', 'Smaart CRM', 'Reminder: Follow up with Shubham Aagarwal for lead ID: 49 at 28-07-2025 12:00 PM', '0', 'daily_lead', '0', NULL, 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/lms-smaart/messages:send` resulted in a `400 Bad Request` response:\n{\n  \"error\": {\n    \"code\": 400,\n    \"message\": \"Recipient of the message is not set.\",\n    \"status\": \"INVALID_ARGUMENT\", (truncated...)\n', '2025-07-28 06:15:04', '2025-07-28 06:15:04', NULL),
(16, 5, 'Apollo Infotech', '0', 'Smaart CRM', 'Reminder: Follow up with Ms Murthi for lead ID: 7 at 28-07-2025 12:00 PM', '0', 'daily_lead', '0', NULL, 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/lms-smaart/messages:send` resulted in a `400 Bad Request` response:\n{\n  \"error\": {\n    \"code\": 400,\n    \"message\": \"Recipient of the message is not set.\",\n    \"status\": \"INVALID_ARGUMENT\", (truncated...)\n', '2025-07-28 06:15:04', '2025-07-28 06:15:04', NULL),
(17, 5, 'Apollo Infotech', '0', 'Smaart CRM', 'Reminder: Follow up with Veer for lead ID: 14 at 28-07-2025 12:00 PM', '0', 'daily_lead', '0', NULL, 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/lms-smaart/messages:send` resulted in a `400 Bad Request` response:\n{\n  \"error\": {\n    \"code\": 400,\n    \"message\": \"Recipient of the message is not set.\",\n    \"status\": \"INVALID_ARGUMENT\", (truncated...)\n', '2025-07-28 06:15:04', '2025-07-28 06:15:05', NULL),
(18, 5, 'Apollo Infotech', '0', 'Salexo', 'Reminder: Follow up with Aadip Bhai for lead ID: 4 at 29-07-2025 12:00 PM', '0', 'daily_lead', '0', 'eWP7QAAuTZS2hLqG7HDNmj:APA91bHmY83Psue2MmSFCUS0mDChKMBE6PcfHrlFfWnMkWzxUwmrLNRR4cEzkwEUHz2YV_1FOpR8TEw_H3uKrZnxD-Ip_pJAosP8Oz8LtwtWPED1jzrdNHw', 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/lms-smaart/messages:send` resulted in a `403 Forbidden` response:\n{\n  \"error\": {\n    \"code\": 403,\n    \"message\": \"SenderId mismatch\",\n    \"status\": \"PERMISSION_DENIED\",\n    \"details\": [\n (truncated...)\n', '2025-07-29 06:15:03', '2025-07-29 06:15:04', NULL),
(19, 5, 'Apollo Infotech', '0', 'Salexo', 'Reminder: Follow up with Demo Meet Patel for lead ID: 12 at 06-08-2025 12:00 PM', '0', 'daily_lead', '0', 'eWP7QAAuTZS2hLqG7HDNmj:APA91bHmY83Psue2MmSFCUS0mDChKMBE6PcfHrlFfWnMkWzxUwmrLNRR4cEzkwEUHz2YV_1FOpR8TEw_H3uKrZnxD-Ip_pJAosP8Oz8LtwtWPED1jzrdNHw', 'sent', '{\n  \"name\": \"projects/salexo-e2788/messages/0:1754460903712998%166059d0166059d0\"\n}\n', '2025-08-06 06:15:03', '2025-08-06 06:15:03', NULL),
(20, 18, 'grapits', '0', 'Salexo', 'Reminder: Follow up with Ar. Hardik Sir for lead ID: 62 at 21-08-2025 12:00 PM', '0', 'daily_lead', '0', NULL, 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/salexo-e2788/messages:send` resulted in a `400 Bad Request` response:\n{\n  \"error\": {\n    \"code\": 400,\n    \"message\": \"Recipient of the message is not set.\",\n    \"status\": \"INVALID_ARGUMENT\", (truncated...)\n', '2025-08-21 06:15:04', '2025-08-21 06:15:04', NULL),
(21, 5, 'Apollo Infotech', '0', 'Salexo', 'Reminder: Follow up with parth shah for lead ID: 59 at 22-08-2025 2:00 PM', '0', 'daily_lead', '0', 'cMR0b7c2SEWm9ytI5XtO15:APA91bGEWlkerwt97nj6rLV0nG69rIMOuAFodEmfjTnpN7_lkuiCKoUwtSc9W4xebda12TpuUi7HVXtFVkHv6wimSngF9jGR_mu9pZQM1uSD4PkiPbAaKGM', 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/salexo-e2788/messages:send` resulted in a `404 Not Found` response:\n{\n  \"error\": {\n    \"code\": 404,\n    \"message\": \"Requested entity was not found.\",\n    \"status\": \"NOT_FOUND\",\n    \"detail (truncated...)\n', '2025-08-22 08:15:02', '2025-08-22 08:15:03', NULL),
(22, 5, 'Apollo Infotech', '0', 'Salexo', 'Reminder: Follow up with Bhadresh shah for lead ID: 64 at 23-08-2025 12:00 PM', '0', 'daily_lead', '0', 'cMR0b7c2SEWm9ytI5XtO15:APA91bGEWlkerwt97nj6rLV0nG69rIMOuAFodEmfjTnpN7_lkuiCKoUwtSc9W4xebda12TpuUi7HVXtFVkHv6wimSngF9jGR_mu9pZQM1uSD4PkiPbAaKGM', 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/salexo-e2788/messages:send` resulted in a `404 Not Found` response:\n{\n  \"error\": {\n    \"code\": 404,\n    \"message\": \"Requested entity was not found.\",\n    \"status\": \"NOT_FOUND\",\n    \"detail (truncated...)\n', '2025-08-23 06:15:04', '2025-08-23 06:15:04', NULL),
(23, 18, 'grapits', '0', 'Salexo', 'Reminder: Follow up with ABHI SIR BAREJA for lead ID: 61 at 23-08-2025 12:00 PM', '0', 'daily_lead', '0', NULL, 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/salexo-e2788/messages:send` resulted in a `400 Bad Request` response:\n{\n  \"error\": {\n    \"code\": 400,\n    \"message\": \"Recipient of the message is not set.\",\n    \"status\": \"INVALID_ARGUMENT\", (truncated...)\n', '2025-08-23 06:15:04', '2025-08-23 06:15:05', NULL),
(24, 5, 'Apollo Infotech', '0', 'Salexo', 'Reminder: Follow up with Bhadresh shah for lead ID: 64 at 28-08-2025 12:00 PM', '0', 'daily_lead', '0', 'cMR0b7c2SEWm9ytI5XtO15:APA91bGEWlkerwt97nj6rLV0nG69rIMOuAFodEmfjTnpN7_lkuiCKoUwtSc9W4xebda12TpuUi7HVXtFVkHv6wimSngF9jGR_mu9pZQM1uSD4PkiPbAaKGM', 'failed', 'Client error: `POST https://fcm.googleapis.com/v1/projects/salexo-e2788/messages:send` resulted in a `404 Not Found` response:\n{\n  \"error\": {\n    \"code\": 404,\n    \"message\": \"Requested entity was not found.\",\n    \"status\": \"NOT_FOUND\",\n    \"detail (truncated...)\n', '2025-08-28 06:15:04', '2025-08-28 06:15:04', NULL),
(25, 5, 'Apollo Infotech', '0', 'Salexo', 'Reminder: Follow up with Bhadresh shah for lead ID: 64 at 02-09-2025 12:00 PM', '0', 'daily_lead', '0', 'eszjavqKRbK2IVuiZvR6xk:APA91bFSJq8_TAzsArElTteC4sC2rHplG6NuvYRHEZqjZEEs4kLA8_uiiyNlcL8Ea3I4-ckIEG-0NG69fqyuhP5MKw0GATPA0WleiaMemPXug8PrsyMyaYM', 'sent', '{\n  \"name\": \"projects/salexo-e2788/messages/0:1756793704671166%166059d0166059d0\"\n}\n', '2025-09-02 06:15:03', '2025-09-02 06:15:04', NULL),
(26, 5, 'Apollo Infotech', '0', 'Salexo', 'Reminder: Follow up with Yash shah for lead ID: 66 at 02-09-2025 2:00 PM', '0', 'daily_lead', '0', 'eszjavqKRbK2IVuiZvR6xk:APA91bFSJq8_TAzsArElTteC4sC2rHplG6NuvYRHEZqjZEEs4kLA8_uiiyNlcL8Ea3I4-ckIEG-0NG69fqyuhP5MKw0GATPA0WleiaMemPXug8PrsyMyaYM', 'sent', '{\n  \"name\": \"projects/salexo-e2788/messages/0:1756800904910070%166059d0166059d0\"\n}\n', '2025-09-02 08:15:04', '2025-09-02 08:15:04', NULL),
(27, 5, 'Apollo Infotech', '0', 'Salexo', 'Reminder: Follow up with parth shah for lead ID: 59 at 02-09-2025 4:30 PM', '0', 'daily_lead', '0', 'eszjavqKRbK2IVuiZvR6xk:APA91bFSJq8_TAzsArElTteC4sC2rHplG6NuvYRHEZqjZEEs4kLA8_uiiyNlcL8Ea3I4-ckIEG-0NG69fqyuhP5MKw0GATPA0WleiaMemPXug8PrsyMyaYM', 'sent', '{\n  \"name\": \"projects/salexo-e2788/messages/0:1756809907001256%166059d0166059d0\"\n}\n', '2025-09-02 10:45:06', '2025-09-02 10:45:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL DEFAULT '0',
  `company_name` varchar(255) DEFAULT NULL,
  `contact_person_name` varchar(255) DEFAULT NULL,
  `gst` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `address` text,
  `pincode` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state_id` int(255) DEFAULT '0',
  `plan_name` int(11) DEFAULT '0',
  `duration_in_days` varchar(255) DEFAULT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `gst_percentage` int(11) NOT NULL DEFAULT '0',
  `gst_amount` int(11) NOT NULL DEFAULT '0',
  `net_amount` int(11) NOT NULL DEFAULT '0',
  `isPayment` int(11) NOT NULL DEFAULT '0' COMMENT '0=pending,1=success,2=failed',
  `iStatus` int(11) NOT NULL DEFAULT '1',
  `isDelete` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `strIP` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `emp_id`, `company_name`, `contact_person_name`, `gst`, `email`, `mobile`, `address`, `pincode`, `city`, `state_id`, `plan_name`, `duration_in_days`, `amount`, `gst_percentage`, `gst_amount`, `net_amount`, `isPayment`, `iStatus`, `isDelete`, `created_at`, `updated_at`, `strIP`) VALUES
(15, 24, 'Tarang Enterprise', 'Tarang Parmar', NULL, 'dev2.apolloinfotech@gmail.com', '1234567890', 'A-1 , Anubhav Flat , bhairavnath cross road , Maninagar , Ahmedabad', '380028', 'Ahmedabad', 1, 2, '30', 750, 18, 135, 885, 1, 1, 0, '2025-09-15 13:00:12', '2025-09-15 13:01:00', '103.1.100.226'),
(16, 15, 'test', 'test', NULL, 'dev2.apolloinfotech@gmail.com', '9876543210', 'A-1 , Anubhav Flat , bhairavnath cross road , Maninagar , Ahmedabad', '380028', 'Ahmedabad', 1, 2, '30', 750, 18, 135, 885, 1, 1, 0, '2025-09-15 13:44:42', '2025-09-15 13:45:12', '103.1.100.226'),
(17, 0, 'Nisha Enterprise', 'Nisha Baghel', NULL, 'ai.dev.laravel10@gmail.com', '9632288444', 'A-1 , Anubhav Flat , bhairavnath cross road , Maninagar , Ahmedabad', '380028', 'Ahmedabad', 1, 4, '365', 5000, 18, 900, 5900, 0, 1, 0, '2025-09-15 13:49:49', '2025-09-15 13:49:49', '103.1.100.226'),
(18, 0, 'test', 'test', NULL, 'test@gmail.com', '1234567890', 'A-1 , Anubhav Flat , bhairavnath cross road , Maninagar , Ahmedabad\r\ntt', '382443', 'Ahmedabad', 1, 4, '365', 5000, 18, 900, 5900, 0, 1, 0, '2025-09-15 14:09:18', '2025-09-15 14:09:18', '103.1.100.226');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `mobile`, `token`, `created_at`) VALUES
(2, '9824773136', NULL, 'jRwZB4otrhT3ax294HaeBISzHUe9hCdn4uOqMoPQQIij9XK4iznrgd0oREeF4X2u', '2025-08-01 14:38:34'),
(3, '9725123569', NULL, '6olR3EwHfxfD3JMu9nUp6cmAhyrWM7vj2sRaQMgpjdhVTA4vr2BmFSOpIPaAggDS', '2025-08-12 09:39:03');

-- --------------------------------------------------------

--
-- Table structure for table `plan_master`
--

CREATE TABLE `plan_master` (
  `plan_id` int(11) NOT NULL,
  `plan_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `plan_amount` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `plan_days` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `iStatus` int(11) NOT NULL DEFAULT '1',
  `isDelete` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `plan_master`
--

INSERT INTO `plan_master` (`plan_id`, `plan_name`, `plan_amount`, `plan_days`, `iStatus`, `isDelete`, `created_at`, `updated_at`) VALUES
(1, 'Free', '0', '0', 1, 0, '2025-09-15 17:50:15', '2025-09-15 17:50:15'),
(2, 'Monthly', '750', '30', 1, 0, '2025-09-15 17:50:26', '2025-09-15 17:50:26'),
(3, '6 Months', '3000', '180', 1, 0, '2025-09-15 17:50:38', '2025-09-15 17:50:38'),
(4, 'Yearly', '5000', '365', 1, 0, '2025-09-15 17:50:50', '2025-09-15 17:50:50');

-- --------------------------------------------------------

--
-- Table structure for table `request_for_demo`
--

CREATE TABLE `request_for_demo` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_person_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `situable_time` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iStatus` int(11) NOT NULL DEFAULT '1',
  `isDelete` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `strIP` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `request_for_demo`
--

INSERT INTO `request_for_demo` (`id`, `company_name`, `contact_person_name`, `mobile`, `email`, `situable_time`, `iStatus`, `isDelete`, `created_at`, `updated_at`, `strIP`) VALUES
(1, 'Apollo Infotech', 'Krunal Shah', '9876543210', NULL, 'tomorrow at 12 pm', 1, 0, '2025-09-15 12:44:19', '2025-09-15 12:44:19', '103.1.100.226');

-- --------------------------------------------------------

--
-- Table structure for table `request_for_joining`
--

CREATE TABLE `request_for_joining` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `GST` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_person_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Address` text COLLATE utf8_unicode_ci NOT NULL,
  `pincode` int(11) NOT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(11) NOT NULL,
  `subscription_start_date` datetime NOT NULL,
  `subscription_end_date` datetime NOT NULL,
  `plan_id` int(11) NOT NULL,
  `plan_amount` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `plan_days` int(11) NOT NULL,
  `iStatus` tinyint(4) NOT NULL DEFAULT '1',
  `isDeleted` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `request_for_joining`
--

INSERT INTO `request_for_joining` (`company_id`, `company_name`, `GST`, `contact_person_name`, `mobile`, `email`, `Address`, `pincode`, `city`, `state_id`, `subscription_start_date`, `subscription_end_date`, `plan_id`, `plan_amount`, `plan_days`, `iStatus`, `isDeleted`, `created_at`, `updated_at`) VALUES
(18, 'Test Entry', NULL, 'Mihir Rathod', '9632587410', 'dev2.apolloinfotech@gmail.com', 'A-1 , Anubhav Flat , bhairavnath cross road , Maninagar , Ahmedabad', 380028, 'Ahmedabad', 1, '2025-06-26 17:00:03', '2025-07-06 17:00:03', 2, '1500', 10, 1, 0, '2025-06-26 17:00:03', '2025-06-26 17:00:03');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2022-09-12 04:33:06', '2022-09-12 04:33:06'),
(2, 'Company', 'web', '2022-09-12 04:33:06', '2022-09-12 04:33:06'),
(3, 'Employee', 'web', '2022-09-12 04:33:06', '2022-09-12 04:33:06');

-- --------------------------------------------------------

--
-- Table structure for table `sendemaildetails`
--

CREATE TABLE `sendemaildetails` (
  `id` int(11) NOT NULL,
  `strSubject` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `strTitle` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `strFromMail` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ToMail` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `strCC` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `strBCC` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sendemaildetails`
--

INSERT INTO `sendemaildetails` (`id`, `strSubject`, `strTitle`, `strFromMail`, `ToMail`, `strCC`, `strBCC`) VALUES
(4, 'Schedule a Demo', 'Salexo', 'info@salexo.in', 'info@salexo.in', '', ''),
(8, 'Registration Detail', 'Salexo', 'info@salexo.in', 'info@salexo.in', NULL, NULL),
(9, 'Order Detail From The Wardrobe Fashion Order No', 'Shine Aestheticeye', 'info.shineclinic@gmail.com', NULL, NULL, NULL),
(10, 'Dispatch Order', 'Shine Aestheticeye', 'info.shineclinic@gmail.com', NULL, NULL, NULL),
(11, 'Contact Us', 'Shine Aestheticeye', 'info.shineclinic@gmail.com', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_master`
--

CREATE TABLE `service_master` (
  `service_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `service_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `service_image` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `service_description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iStatus` int(11) NOT NULL DEFAULT '1',
  `isDelete` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `service_master`
--

INSERT INTO `service_master` (`service_id`, `company_id`, `service_name`, `service_image`, `service_description`, `iStatus`, `isDelete`, `created_at`, `updated_at`) VALUES
(39, 11, 'Speaker Paint 5kg', NULL, 'Sand Finish', 1, 0, '2025-08-08 07:47:19', '2025-08-08 07:47:19'),
(40, 11, 'Speaker Piant 800gm', NULL, 'Putty', 1, 0, '2025-08-08 07:47:57', '2025-08-08 07:47:57'),
(4, 2, 'Hello', NULL, 'Test', 1, 0, '2025-06-18 10:40:33', '2025-06-18 10:40:33'),
(5, 5, 'Website Development', NULL, NULL, 1, 0, '2025-07-03 05:32:29', '2025-07-03 05:32:29'),
(6, 5, 'App Development', NULL, NULL, 1, 0, '2025-07-03 05:32:39', '2025-07-03 05:32:39'),
(7, 5, 'SEO', NULL, NULL, 1, 0, '2025-07-03 05:32:45', '2025-07-03 05:32:45'),
(8, 5, 'SMO', NULL, NULL, 1, 0, '2025-07-03 05:34:07', '2025-07-03 05:34:07'),
(9, 8, 'Product 1', NULL, 'Test', 1, 0, '2025-07-04 10:01:15', '2025-07-04 10:01:15'),
(38, 11, 'Speaker Paint 5kg', NULL, 'Spray Finish', 1, 0, '2025-08-08 07:46:42', '2025-08-08 07:46:42'),
(28, 6, 'Dealer Meet', NULL, '-', 1, 0, '2025-07-25 12:20:42', '2025-07-25 12:20:42'),
(29, 11, 'Roller', NULL, 'All Rollers', 1, 0, '2025-08-08 06:54:13', '2025-08-08 06:54:13'),
(30, 11, 'Lime Plaster', NULL, 'Lime Plster', 1, 0, '2025-08-08 07:27:00', '2025-08-08 07:27:00'),
(31, 11, 'concrate matt', NULL, 'concrate matt', 1, 0, '2025-08-08 07:27:14', '2025-08-08 07:27:14'),
(32, 11, 'Grapits Fossil', NULL, 'Grapits Fossil Rang', 1, 0, '2025-08-08 07:40:06', '2025-08-08 07:40:06'),
(33, 11, 'Grapits Fiber wall Coating', NULL, 'Grapits Fiber wall Coating', 1, 0, '2025-08-08 07:40:49', '2025-08-08 07:40:49'),
(34, 11, 'Grapits Archi Concrete', NULL, 'Grapits Archi Concrete', 1, 0, '2025-08-08 07:41:37', '2025-08-08 07:41:37'),
(35, 11, 'Granotone Series &  Base Coat', NULL, 'Granotone Series &  Base Coat', 1, 0, '2025-08-08 07:42:47', '2025-08-08 07:42:47'),
(36, 11, 'Speaker Paint 800gm', NULL, 'Roller Finish', 1, 0, '2025-08-08 07:44:00', '2025-08-08 07:44:00'),
(37, 11, 'Speaker Paint 5kg', NULL, 'Roller Finish', 1, 0, '2025-08-08 07:45:47', '2025-08-08 07:45:47'),
(41, 11, 'Speaker Paint 10kg', NULL, 'Putty', 1, 0, '2025-08-08 07:48:17', '2025-08-08 07:48:17'),
(42, 11, 'Granotone Venezia Metallic Paint', NULL, 'Granotone Venezia Metallic Paint All shade', 1, 0, '2025-08-08 07:48:59', '2025-08-08 07:48:59'),
(43, 11, 'Granotone Venezia Plaster', NULL, '( Stucco )', 1, 0, '2025-08-08 07:50:14', '2025-08-08 07:50:14'),
(44, 11, 'Granotone High Gloss Enamel Paint', NULL, 'Granotone High Gloss Enamel Paint', 1, 0, '2025-08-08 07:50:58', '2025-08-08 07:50:58'),
(45, 11, 'Grapits Glitter', NULL, 'Silver,Gold,Copper,Rainbow', 1, 0, '2025-08-08 07:52:01', '2025-08-08 07:52:01'),
(46, 11, 'Paint Tools', NULL, 'Paint Tools', 1, 0, '2025-08-08 09:01:02', '2025-08-08 09:01:02'),
(47, 11, 'Grapits Spray Paint', NULL, 'Grapits Spray Paint \r\n250 ml & 400 ml', 1, 0, '2025-08-08 09:01:56', '2025-08-08 09:01:56'),
(48, 11, 'Grapits cloth', NULL, 'Grapits Cloth', 1, 0, '2025-08-08 09:02:33', '2025-08-08 09:02:33'),
(49, 11, 'Grapits Wall Stencil', NULL, 'Grapits Wall Stencil \r\n12*12\r\n16*24', 1, 0, '2025-08-08 09:03:16', '2025-08-08 09:03:16'),
(50, 11, 'Grapits Paint Brush', NULL, 'Grapits Paint Brush', 1, 0, '2025-08-08 09:03:37', '2025-08-08 09:03:37'),
(51, 11, 'Grapits Putty Patra', NULL, 'Grapits Putty Patra\r\nLight \r\nHeavy \r\nEx.Heavy', 1, 0, '2025-08-08 09:04:40', '2025-08-08 09:04:40'),
(52, 11, 'Performer Adrasive Range', NULL, 'Performer Adrasive Range', 1, 0, '2025-08-08 09:05:13', '2025-08-08 09:05:13'),
(53, 11, 'LUXURY Efect', NULL, NULL, 1, 0, '2025-08-20 11:59:26', '2025-08-20 11:59:26'),
(54, 5, 'Graphics Design', NULL, NULL, 1, 0, '2025-09-01 11:16:31', '2025-09-01 11:16:31'),
(55, 14, 'Trolly', NULL, NULL, 1, 0, '2025-09-01 11:25:27', '2025-09-01 11:25:27'),
(56, 5, 'salexo CRM', NULL, NULL, 1, 0, '2025-09-02 10:21:43', '2025-09-02 10:21:43'),
(57, 14, 'Platform Trolley', NULL, 'Customized', 1, 0, '2025-09-04 07:50:05', '2025-09-04 07:50:05'),
(59, 14, 'INSPECTION TABLE', NULL, NULL, 1, 0, '2025-09-04 11:05:41', '2025-09-04 11:05:41'),
(60, 14, 'mezzanine floor', NULL, NULL, 1, 0, '2025-09-04 11:06:22', '2025-09-04 11:06:22'),
(61, 14, 'shed', NULL, NULL, 1, 0, '2025-09-04 11:06:31', '2025-09-04 11:06:31'),
(62, 14, 'STORAGE RACK', NULL, NULL, 1, 0, '2025-09-04 11:06:46', '2025-09-04 11:06:46'),
(63, 14, 'FIXED & FOLDABLE PALLET', NULL, NULL, 1, 0, '2025-09-04 11:07:02', '2025-09-04 11:07:02'),
(64, 14, 'CYLINDER TROLLEY', NULL, NULL, 1, 0, '2025-09-04 11:07:17', '2025-09-04 11:07:17'),
(65, 14, 'wiremesh trolley', NULL, NULL, 1, 0, '2025-09-13 05:23:41', '2025-09-13 05:23:41'),
(66, 14, 'FIFO RACK', NULL, NULL, 1, 0, '2025-09-26 12:07:55', '2025-09-26 12:07:55'),
(67, 14, 'PIPE AND JOINT MATERIAL', NULL, NULL, 1, 0, '2025-09-26 12:08:10', '2025-09-26 12:08:10'),
(70, 5, 'Mineral Water Bottle', NULL, NULL, 1, 0, '2025-10-03 11:19:40', '2025-10-03 11:19:40'),
(72, 14, 'roller track', NULL, NULL, 1, 0, '2025-10-03 11:39:41', '2025-10-03 11:39:41'),
(73, 5, 'Ergonomic Office Chairs', NULL, NULL, 1, 0, '2025-10-03 11:58:03', '2025-10-03 11:58:03'),
(74, 5, 'Crm Software', NULL, NULL, 1, 0, '2025-10-03 12:59:46', '2025-10-03 12:59:46'),
(75, 5, 'Cylinder Trolley', NULL, NULL, 1, 0, '2025-10-03 12:59:47', '2025-10-03 12:59:47');

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `stateId` int(11) NOT NULL,
  `stateName` varchar(100) NOT NULL,
  `istatus` int(11) NOT NULL DEFAULT '1',
  `isDelete` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`stateId`, `stateName`, `istatus`, `isDelete`) VALUES
(1, 'Gujarat', 1, 0),
(2, 'Kerala', 1, 0),
(3, 'Chhatisgarh', 1, 0),
(4, 'Paschim Bangal', 1, 0),
(5, 'Madhya Pradesh', 1, 0),
(6, 'Delhi', 1, 0),
(7, 'Karnataka', 1, 0),
(8, 'Maharastra', 1, 0),
(11, 'Rajasthan', 1, 0),
(12, 'Punjab', 1, 0),
(13, 'Andhra Pradesh', 1, 0),
(14, 'Haryana', 1, 0),
(15, 'Uttar Pradesh', 1, 0),
(16, 'TAMILNADU', 1, 0),
(17, 'Tamilnadu', 1, 0),
(18, 'Telangana', 1, 0),
(19, 'Arunachal Pradesh', 1, 0),
(20, 'Assam', 1, 0),
(21, 'Bihar', 1, 0),
(22, 'Goa', 1, 0),
(23, 'Himachal Pradesh', 1, 0),
(24, 'Jharkhand', 1, 0),
(25, 'Manipur', 1, 0),
(26, 'Meghalaya', 1, 0),
(27, 'Mizoram', 1, 0),
(28, 'Nagaland', 1, 0),
(29, 'Odisha', 1, 0),
(30, 'Sikkim', 1, 0),
(31, 'Tripura', 1, 0),
(32, 'Uttarakhand', 1, 0),
(33, 'Andaman and Nicobar', 1, 0),
(34, 'Chandigarh', 1, 0),
(35, 'Dadra Nagar Haveli', 1, 0),
(36, 'Daman and Diu', 1, 0),
(37, 'Jammu and Kashmir', 1, 0),
(38, 'Lakshadweep', 1, 0),
(39, 'Ladakh', 1, 0),
(40, 'Puducherry', 1, 0),
(50, 'West Bengal', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `udf_masters`
--

CREATE TABLE `udf_masters` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `label` varchar(255) DEFAULT NULL,
  `required` varchar(255) DEFAULT NULL,
  `iStatus` int(11) NOT NULL DEFAULT '1',
  `isDelete` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `strIP` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `udf_masters`
--

INSERT INTO `udf_masters` (`id`, `company_id`, `label`, `required`, `iStatus`, `isDelete`, `created_at`, `updated_at`, `strIP`) VALUES
(4, 5, 'First Name', 'Yes', 1, 0, '2025-10-06 09:24:32', '2025-10-06 09:24:32', '103.1.100.226'),
(5, 5, 'Last Name', 'No', 1, 0, '2025-10-06 09:24:47', '2025-10-06 09:24:47', '103.1.100.226');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT '2' COMMENT '1=Admin, 2=Customer,3=Reseller',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `mobile_number`, `email_verified_at`, `password`, `role_id`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super', 'Admin', 'admin@admin.com', '9028187696', NULL, '$2y$10$4x8DxxOmXXj82QVay0PKduuyXiHl7bpJDlC/Lno25CrtuLSGh8mm2', 1, 1, NULL, '2022-09-12 04:33:06', '2023-12-20 18:22:59'),
(2, 'Mihir', 'Rathod', 'test@gmail.com', '9999999999', NULL, '$2y$10$SnKyu4Xcoi.MBgjKVvOrFeXmTeChg96kk.OgtUrqHOzZ2yvvg.Cci', 2, 1, NULL, '2022-09-12 04:33:06', '2023-12-20 18:22:59'),
(3, 'dkkjgjlkgjjg', NULL, 'dev4.apolloinfotech@gmail.com', '9876543210', NULL, '$2y$10$CzVuGyARYVDQ.FV.zT8JmOHEdiZOO6IDKDAz/s6UFVIPT0u7xVAd2', 2, 1, NULL, '2024-07-15 11:47:04', '2024-07-15 11:47:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `card_payment`
--
ALTER TABLE `card_payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oid` (`oid`);

--
-- Indexes for table `company_client_master`
--
ALTER TABLE `company_client_master`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `employee_master`
--
ALTER TABLE `employee_master`
  ADD PRIMARY KEY (`emp_id`),
  ADD UNIQUE KEY `emploginId` (`emp_loginId`);

--
-- Indexes for table `lead_cancel_reason`
--
ALTER TABLE `lead_cancel_reason`
  ADD PRIMARY KEY (`lead_cancel_reason_id`);

--
-- Indexes for table `lead_history`
--
ALTER TABLE `lead_history`
  ADD PRIMARY KEY (`iLeadHistoryId`);

--
-- Indexes for table `lead_master`
--
ALTER TABLE `lead_master`
  ADD PRIMARY KEY (`lead_id`);

--
-- Indexes for table `lead_pipeline_master`
--
ALTER TABLE `lead_pipeline_master`
  ADD PRIMARY KEY (`pipeline_id`);

--
-- Indexes for table `lead_source_master`
--
ALTER TABLE `lead_source_master`
  ADD PRIMARY KEY (`lead_source_id`);

--
-- Indexes for table `lead_udf_data`
--
ALTER TABLE `lead_udf_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meta_data`
--
ALTER TABLE `meta_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `plan_master`
--
ALTER TABLE `plan_master`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `request_for_demo`
--
ALTER TABLE `request_for_demo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `request_for_joining`
--
ALTER TABLE `request_for_joining`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `sendemaildetails`
--
ALTER TABLE `sendemaildetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_master`
--
ALTER TABLE `service_master`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`stateId`);

--
-- Indexes for table `udf_masters`
--
ALTER TABLE `udf_masters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `card_payment`
--
ALTER TABLE `card_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `company_client_master`
--
ALTER TABLE `company_client_master`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `employee_master`
--
ALTER TABLE `employee_master`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `lead_cancel_reason`
--
ALTER TABLE `lead_cancel_reason`
  MODIFY `lead_cancel_reason_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `lead_history`
--
ALTER TABLE `lead_history`
  MODIFY `iLeadHistoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `lead_master`
--
ALTER TABLE `lead_master`
  MODIFY `lead_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

--
-- AUTO_INCREMENT for table `lead_pipeline_master`
--
ALTER TABLE `lead_pipeline_master`
  MODIFY `pipeline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `lead_source_master`
--
ALTER TABLE `lead_source_master`
  MODIFY `lead_source_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `lead_udf_data`
--
ALTER TABLE `lead_udf_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `meta_data`
--
ALTER TABLE `meta_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `plan_master`
--
ALTER TABLE `plan_master`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `request_for_demo`
--
ALTER TABLE `request_for_demo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `request_for_joining`
--
ALTER TABLE `request_for_joining`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sendemaildetails`
--
ALTER TABLE `sendemaildetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `service_master`
--
ALTER TABLE `service_master`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `state`
--
ALTER TABLE `state`
  MODIFY `stateId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `udf_masters`
--
ALTER TABLE `udf_masters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
