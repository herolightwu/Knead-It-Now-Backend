-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 14, 2018 at 07:49 PM
-- Server version: 5.6.39-cll-lve
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kneaditnow`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_groups`
--

CREATE TABLE `admin_groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin_groups`
--

INSERT INTO `admin_groups` (`id`, `name`, `description`) VALUES
(1, 'webmaster', 'Webmaster'),
(2, 'admin', 'Administrator'),
(3, 'manager', 'Manager'),
(4, 'staff', 'Staff');

-- --------------------------------------------------------

--
-- Table structure for table `admin_login_attempts`
--

CREATE TABLE `admin_login_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`) VALUES
(1, '127.0.0.1', 'webmaster', '$2y$08$/X5gzWjesYi78GqeAv5tA.dVGBVP7C1e1PzqnYCVe5s1qhlDIPPES', NULL, NULL, NULL, NULL, NULL, NULL, 1451900190, 1531703307, 1, 'Webmaster', ''),
(2, '127.0.0.1', 'admin', '$2y$08$7Bkco6JXtC3Hu6g9ngLZDuHsFLvT7cyAxiz1FzxlX5vwccvRT7nKW', NULL, NULL, NULL, NULL, NULL, NULL, 1451900228, 1536223540, 1, 'Admin', ''),
(3, '127.0.0.1', 'manager', '$2y$08$snzIJdFXvg/rSHe0SndIAuvZyjktkjUxBXkrrGdkPy1K6r5r/dMLa', NULL, NULL, NULL, NULL, NULL, NULL, 1451900430, 1465489585, 1, 'Manager', ''),
(4, '127.0.0.1', 'staff', '$2y$08$NigAXjN23CRKllqe3KmjYuWXD5iSRPY812SijlhGeKfkrMKde9da6', NULL, NULL, NULL, NULL, NULL, NULL, 1451900439, 1465489590, 1, 'Staff', '');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users_groups`
--

CREATE TABLE `admin_users_groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin_users_groups`
--

INSERT INTO `admin_users_groups` (`id`, `user_id`, `group_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `api_access`
--

CREATE TABLE `api_access` (
  `id` int(11) UNSIGNED NOT NULL,
  `key` varchar(40) NOT NULL DEFAULT '',
  `controller` varchar(50) NOT NULL DEFAULT '',
  `date_created` datetime DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

CREATE TABLE `api_keys` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `key` varchar(40) NOT NULL,
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT '0',
  `is_private_key` tinyint(1) NOT NULL DEFAULT '0',
  `ip_addresses` text,
  `date_created` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `api_keys`
--

INSERT INTO `api_keys` (`id`, `user_id`, `key`, `level`, `ignore_limits`, `is_private_key`, `ip_addresses`, `date_created`) VALUES
(1, 0, 'anonymous', 1, 1, 0, NULL, 1463388382);

-- --------------------------------------------------------

--
-- Table structure for table `api_limits`
--

CREATE TABLE `api_limits` (
  `id` int(11) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `count` int(10) NOT NULL,
  `hour_started` int(11) NOT NULL,
  `api_key` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `api_logs`
--

CREATE TABLE `api_logs` (
  `id` int(11) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `method` varchar(6) NOT NULL,
  `params` text,
  `api_key` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `time` int(11) NOT NULL,
  `rtime` float DEFAULT NULL,
  `authorized` varchar(1) NOT NULL,
  `response_code` smallint(3) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL,
  `pos` int(11) NOT NULL DEFAULT '0',
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `pos`, `title`) VALUES
(1, 1, 'Category 1'),
(2, 2, 'Category 2'),
(3, 3, 'Category 3');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT '1',
  `author_id` int(11) NOT NULL,
  `title` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `image_url` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `content_brief` text COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `publish_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('draft','active','hidden') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `category_id`, `author_id`, `title`, `image_url`, `content_brief`, `content`, `publish_time`, `status`) VALUES
(1, 1, 2, 'Blog Post 1', '', '<p>\r\n	Blog Post 1 Content Brief</p>\r\n', '<p>\r\n	Blog Post 1 Content</p>\r\n', '2015-09-25 16:00:00', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts_tags`
--

CREATE TABLE `blog_posts_tags` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `blog_posts_tags`
--

INSERT INTO `blog_posts_tags` (`id`, `post_id`, `tag_id`) VALUES
(1, 1, 2),
(2, 1, 1),
(3, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `blog_tags`
--

CREATE TABLE `blog_tags` (
  `id` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `blog_tags`
--

INSERT INTO `blog_tags` (`id`, `title`) VALUES
(1, 'Tag 1'),
(2, 'Tag 2'),
(3, 'Tag 3');

-- --------------------------------------------------------

--
-- Table structure for table `book_posts`
--

CREATE TABLE `book_posts` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `start_date` varchar(64) NOT NULL,
  `start_time` varchar(64) NOT NULL,
  `duration` varchar(32) NOT NULL DEFAULT '30',
  `cost` varchar(32) NOT NULL DEFAULT '50',
  `auto_confirm` varchar(16) NOT NULL DEFAULT '0',
  `seller_note` varchar(512) NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'posted',
  `buyer_id` int(11) NOT NULL,
  `massage_type` varchar(16) NOT NULL,
  `buyer_note` varchar(512) NOT NULL,
  `book_time` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `book_posts`
--

INSERT INTO `book_posts` (`id`, `seller_id`, `start_date`, `start_time`, `duration`, `cost`, `auto_confirm`, `seller_note`, `status`, `buyer_id`, `massage_type`, `buyer_note`, `book_time`) VALUES
(1, 1, '02/07/2018', '01:00 PM', '30', '55', '1', 'Appart number:12345678', 'posted', 0, '', '', ''),
(2, 4, '04/07/2018', '10:00 AM', '60', '55', '1', '', 'posted', 0, '', '', ''),
(3, 1, '09/07/2018', '06:00 PM', '30', '55', '0', 'Dsfsdfsdfasfas', 'finished', 2, '1', 'Fhhf hh', '11:06 PM'),
(4, 1, '10/07/2018', '06:00 PM', '30', '60', '1', 'Dfsdfafsdfasdf', 'finished', 2, '1', 'Txbgdhj', '01:39 AM'),
(5, 1, '11/07/2018', '01:00 PM', '30', '55', '0', 'Test', 'posted', 0, '', '', ''),
(6, 1, '11/07/2018', '02:00 PM', '30', '55', '0', '', 'posted', 0, '', '', ''),
(7, 4, '13/07/2018', '03:00 PM', '60', '65', '1', '', 'posted', 0, '', '', ''),
(8, 1, '16/07/2018', '01:00 PM', '30', '55', '0', 'Apart number 123456', 'finished', 2, '7', '', '05:27 PM'),
(9, 1, '16/07/2018', '01:00 PM', '30', '60', '0', 'Apart number: 123456', 'finished', 2, '1', 'Test', '09:09 PM'),
(10, 4, '20/07/2018', '08:00 PM', '60', '55', '1', '', 'finished', 5, '1', '', '06:36 PM'),
(11, 1, '22/07/2018', '01:00 PM', '30', '55', '0', '', 'posted', 0, '', '', ''),
(12, 1, '22/07/2018', '10:30 AM', '60', '55', '0', '', 'posted', 0, '', '', ''),
(13, 4, '21/07/2018', '01:00 PM', '60', '55', '1', '', 'posted', 0, '', '', ''),
(14, 4, '26/07/2018', '01:00 PM', '60', '55', '1', '', 'posted', 0, '', '', ''),
(15, 4, '12/08/2018', '01:00 PM', '90', '80', '1', '', 'posted', 0, '', '', ''),
(16, 4, '18/08/2018', '09:00 PM', '60', '55', '1', '', 'posted', 0, '', '', ''),
(17, 4, '18/08/2018', '09:30 PM', '60', '5', '1', '', 'finished', 7, '1', '', '08:43 PM'),
(18, 4, '22/08/2018', '10:00 PM', '60', '5', '1', '', 'finished', 7, '1', '', '09:35 PM'),
(19, 4, '28/08/2018', '01:00 PM', '60', '55', '0', '', 'posted', 0, '', '', ''),
(20, 1, '04/09/2018', '01:00 PM', '30', '55', '0', 'Apart number : 123456', 'posted', 0, '', '', ''),
(21, 1, '13/09/2018', '01:00 PM', '30', '55', '0', '', 'posted', 0, '', '', ''),
(22, 4, '13/09/2018', '03:30 PM', '60', '55', '1', '', 'posted', 0, '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `business_infos`
--

CREATE TABLE `business_infos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `address` varchar(512) NOT NULL,
  `zipcode` varchar(16) NOT NULL,
  `license_code` varchar(32) NOT NULL,
  `active_year` varchar(16) NOT NULL DEFAULT '2018',
  `parking` varchar(64) NOT NULL,
  `massage_types` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `business_infos`
--

INSERT INTO `business_infos` (`id`, `user_id`, `name`, `address`, `zipcode`, `license_code`, `active_year`, `parking`, `massage_types`) VALUES
(1, 1, 'Massage', 'Buscon St,Utica,NY', '13501', '123', '2016', 'Free lot parking', '1,2,3,4,6,7,8,10,11,12'),
(2, 4, 'The Alternative Practitioner', '507 S Washington St STE 60,Spokane,Washington', '99204', '60168451', '2010', 'Meter pay street parking', '1,2,3,7,8');

-- --------------------------------------------------------

--
-- Table structure for table `cover_photos`
--

CREATE TABLE `cover_photos` (
  `id` int(11) NOT NULL,
  `pos` int(11) NOT NULL DEFAULT '0',
  `image_url` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('active','hidden') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `cover_photos`
--

INSERT INTO `cover_photos` (`id`, `pos`, `image_url`, `status`) VALUES
(1, 2, '45296-2.jpg', 'active'),
(2, 1, '2934f-1.jpg', 'active'),
(3, 3, '3717d-3.jpg', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `event_posts`
--

CREATE TABLE `event_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` varchar(512) NOT NULL,
  `book_id` int(11) NOT NULL,
  `type_id` varchar(16) NOT NULL,
  `isread` varchar(8) NOT NULL,
  `event_time` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `event_posts`
--

INSERT INTO `event_posts` (`id`, `user_id`, `content`, `book_id`, `type_id`, `isread`, `event_time`) VALUES
(34, 1, '<b>aaa A</b> wants to book appointment for <b>Deep Tissue</b> massage <br> Today at 06:00 PM, duration 30 minutes', 3, '11', '1', '11:06 PM'),
(35, 2, 'You have a new message from <b>test TA</b>', 3, '4', '1', '11:06 PM'),
(36, 1, '<b>aaa A</b> send you a message', 3, '14', '1', '11:06 PM'),
(37, 2, '<b>test TA</b> confirmed your appointment for <b>Deep Tissue</b> massage <br> Today at 06:00 PM, duration 30 minutes at $55 rate', 3, '1', '1', '11:17 PM'),
(38, 1, 'Appointment finished. Please rate <b>aaa A</b> <br> Today at 06:00 PM, duration 30 minutes, Deep Tissue massage', 3, '12', '1', '09/07/2018 11:20 PM'),
(39, 2, 'Appointment finished. Please rate <b>test TA</b> <br> Today at 06:00 PM, Deep Tissue massage, duration 30 minutes at $55 rate', 3, '2', '1', '09/07/2018 11:20 PM'),
(40, 2, '<b>test TA</b> gave you a 5.0 star rating.', 3, '3', '1', '11:22 PM'),
(41, 1, '<b>aaa A</b> gave you a 5.0 star rating.', 3, '13', '1', '11:23 PM'),
(62, 2, '<b>test TA</b> confirmed your appointment for <b>Deep Tissue</b> massage <br> Today at 06:00 PM, duration 30 minutes at $60 rate', 4, '1', '1', '01:39 AM'),
(63, 2, 'You have a new message from <b>test TA</b>', 4, '4', '1', '01:39 AM'),
(64, 1, '<b>aaa A</b> send you a message', 4, '14', '1', '01:39 AM'),
(65, 1, 'Appointment finished. Please rate <b>aaa A</b> <br> Today at 06:00 PM, duration 30 minutes, Deep Tissue massage', 4, '12', '1', '10/07/2018 01:42 AM'),
(66, 2, 'Appointment finished. Please rate <b>test TA</b> <br> Today at 06:00 PM, Deep Tissue massage, duration 30 minutes at $60 rate', 4, '2', '1', '10/07/2018 01:42 AM'),
(67, 1, '<b>aaa A</b> gave you a 4.0 star rating.', 4, '13', '1', '09:56 PM'),
(68, 1, '<b>aaa A</b> wants to book appointment for <b>Sports</b> massage <br> Today at 01:00 PM, duration 30 minutes', 8, '11', '1', '05:27 PM'),
(69, 2, 'You have a new message from <b>test TA</b>', 8, '4', '1', '05:27 PM'),
(70, 2, '<b>test TA</b> confirmed your appointment for <b>Sports</b> massage <br> Today at 01:00 PM, duration 30 minutes at $55 rate', 8, '1', '1', '05:27 PM'),
(71, 1, 'Appointment finished. Please rate <b>aaa A</b> <br> Today at 01:00 PM, duration 30 minutes, Sports massage', 8, '12', '0', '16/07/2018 05:28 PM'),
(72, 2, 'Appointment finished. Please rate <b>test TA</b> <br> Today at 01:00 PM, Sports massage, duration 30 minutes at $55 rate', 8, '2', '0', '16/07/2018 05:28 PM'),
(73, 1, '<b>aaa A</b> wants to book appointment for <b>Deep Tissue</b> massage <br> Today at 01:00 PM, duration 30 minutes', 9, '11', '1', '09:09 PM'),
(74, 2, 'You have a new message from <b>test TA</b>', 9, '4', '1', '09:09 PM'),
(75, 1, '<b>aaa A</b> send you a message', 9, '14', '1', '09:09 PM'),
(76, 2, 'You have a new message from <b>aaa A</b>', 9, '4', '1', '09:12 PM'),
(77, 1, 'You have a new message from <b>test TA</b>', 9, '14', '1', '09:13 PM'),
(78, 2, 'You have a new message from <b>aaa A</b>', 9, '4', '1', '09:14 PM'),
(79, 2, '<b>test TA</b> confirmed your appointment for <b>Deep Tissue</b> massage <br> Today at 01:00 PM, duration 30 minutes at $60 rate', 9, '1', '1', '09:14 PM'),
(80, 1, 'You have a new message from <b>test TA</b>', 9, '14', '1', '09:15 PM'),
(81, 2, 'You have a new message from <b>aaa A</b>', 9, '4', '1', '09:16 PM'),
(82, 1, 'Appointment finished. Please rate <b>aaa A</b> <br> Today at 01:00 PM, duration 30 minutes, Deep Tissue massage', 9, '12', '0', '16/07/2018 09:16 PM'),
(83, 2, 'Appointment finished. Please rate <b>test TA</b> <br> Today at 01:00 PM, Deep Tissue massage, duration 30 minutes at $60 rate', 9, '2', '0', '16/07/2018 09:16 PM'),
(85, 5, '<b>Katie Wilcox</b> confirmed your appointment for <b>Deep Tissue</b> massage <br> Today at 08:00 PM, duration 60 minutes at $55 rate', 10, '1', '1', '06:36 PM'),
(86, 4, 'Appointment finished. Please rate <b>katie wilcox</b> <br> Today at 08:00 PM, duration 60 minutes, Deep Tissue massage', 10, '12', '1', '20/07/2018 06:37 PM'),
(87, 5, 'Appointment finished. Please rate <b>Katie Wilcox</b> <br> Today at 08:00 PM, Deep Tissue massage, duration 60 minutes at $55 rate', 10, '2', '1', '20/07/2018 06:37 PM'),
(88, 4, '<b>katie wilcox</b> gave you a 5.0 star rating.', 10, '13', '1', '08:56 PM'),
(89, 5, '<b>Katie Wilcox</b> gave you a 5.0 star rating.', 10, '3', '0', '09:01 PM'),
(90, 7, '<b>Katie Wilcox</b> confirmed your appointment for <b>Deep Tissue</b> massage <br> Today at 09:30 PM, duration 60 minutes at $5 rate', 17, '1', '1', '08:43 PM'),
(91, 4, 'Appointment finished. Please rate <b>eric faux</b> <br> Today at 09:30 PM, duration 60 minutes, Deep Tissue massage', 17, '12', '0', '18/08/2018 08:45 PM'),
(92, 7, 'Appointment finished. Please rate <b>Katie Wilcox</b> <br> Today at 09:30 PM, Deep Tissue massage, duration 60 minutes at $5 rate', 17, '2', '0', '18/08/2018 08:45 PM'),
(93, 7, '<b>Katie Wilcox</b> confirmed your appointment for <b>Deep Tissue</b> massage <br> Today at 10:00 PM, duration 60 minutes at $5 rate', 18, '1', '1', '09:35 PM'),
(94, 4, 'Appointment finished. Please rate <b>eric faux</b> <br> Today at 10:00 PM, duration 60 minutes, Deep Tissue massage', 18, '12', '0', '22/08/2018 09:36 PM'),
(95, 7, 'Appointment finished. Please rate <b>Katie Wilcox</b> <br> Today at 10:00 PM, Deep Tissue massage, duration 60 minutes at $5 rate', 18, '2', '0', '22/08/2018 09:36 PM');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'Customer', 'General User'),
(2, 'Therapist', 'Service Provider');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `massage_types`
--

CREATE TABLE `massage_types` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `massage_types`
--

INSERT INTO `massage_types` (`id`, `name`) VALUES
(1, 'Deep Tissue'),
(2, 'Swedish'),
(3, 'Pre-natal'),
(4, 'Lymphatic drainage'),
(5, 'Craniosacral'),
(6, 'Reflexology'),
(7, 'Sports'),
(8, 'Aromatherapy'),
(9, 'Acupressure'),
(10, 'Myofascial release'),
(11, 'Reiki'),
(12, 'Shiatsu'),
(13, 'Trigger Point');

-- --------------------------------------------------------

--
-- Table structure for table `message_posts`
--

CREATE TABLE `message_posts` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` varchar(1024) NOT NULL,
  `send_time` varchar(64) NOT NULL,
  `read_status` varchar(16) NOT NULL DEFAULT '0',
  `book_id` int(11) NOT NULL,
  `book_status` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payment_posts`
--

CREATE TABLE `payment_posts` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `price` varchar(128) NOT NULL,
  `paytime` varchar(256) NOT NULL,
  `charge_id` varchar(512) NOT NULL,
  `card_name` varchar(256) NOT NULL,
  `status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `payment_posts`
--

INSERT INTO `payment_posts` (`id`, `book_id`, `buyer_id`, `seller_id`, `price`, `paytime`, `charge_id`, `card_name`, `status`) VALUES
(3, 3, 2, 1, '55', '09/07/2018 11:20 PM', '', '', ''),
(4, 4, 2, 1, '60', '10/07/2018 01:42 AM', '', '', ''),
(5, 8, 2, 1, '55', '16/07/2018 05:28 PM', 'ch_1CoT0jCd8tEaTIxZpSv85X55', 'Visa **** **** **** 4242', 'succeeded'),
(6, 9, 2, 1, '60', '16/07/2018 09:16 PM', 'ch_1CoWYzCd8tEaTIxZXO4HQGX3', 'MasterCard **** **** **** 4444', 'succeeded'),
(7, 10, 5, 4, '55', '20/07/2018 06:37 PM', 'ch_1CqA2VCd8tEaTIxZoqOprTFV', 'MasterCard **** **** **** 8640', 'succeeded'),
(8, 17, 7, 4, '5', '18/08/2018 08:45 PM', 'ch_1D0hqpCd8tEaTIxZO7f0EcDD', 'Visa **** **** **** 1788', 'succeeded'),
(9, 18, 7, 4, '5', '22/08/2018 09:36 PM', 'ch_1D2AYICd8tEaTIxZGxIUbSxs', 'Visa **** **** **** 1788', 'succeeded');

-- --------------------------------------------------------

--
-- Table structure for table `review_posts`
--

CREATE TABLE `review_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `given_id` int(11) NOT NULL,
  `rate` varchar(8) NOT NULL,
  `comment` text NOT NULL,
  `postdate` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `review_posts`
--

INSERT INTO `review_posts` (`id`, `user_id`, `given_id`, `rate`, `comment`, `postdate`) VALUES
(1, 1, 2, '5.0', 'tfbfdhjj', '07-09-2018'),
(2, 2, 1, '5.0', 'dfdsgdfsgdsfgsdfgdfsgsdfgdfsg', '07-09-2018'),
(3, 1, 2, '5.0', 'dhdxjfcjfc', '07-09-2018'),
(4, 1, 2, '4.0', 'dfrdhktfvurrgjrhh', '07-10-2018'),
(5, 4, 5, '5.0', 'great massage!  deep on problem areas but still very relaxing.  ', '07-20-2018'),
(6, 5, 4, '5.0', 'Friendly, punctual, thorough intake and feedback.  would love to have her back!   ', '07-20-2018');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `rate` varchar(8) NOT NULL DEFAULT '0.0',
  `rate_count` varchar(8) NOT NULL DEFAULT '0',
  `type` varchar(16) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(512) NOT NULL,
  `location` varchar(256) NOT NULL,
  `photo` varchar(512) NOT NULL,
  `gender` varchar(128) NOT NULL DEFAULT 'Male',
  `birthday` varchar(256) NOT NULL,
  `gl_id` varchar(256) NOT NULL,
  `fb_id` varchar(256) NOT NULL,
  `tw_id` varchar(256) NOT NULL,
  `device_token` varchar(512) NOT NULL,
  `stripe_id` varchar(512) NOT NULL,
  `about` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `rate`, `rate_count`, `type`, `phone`, `address`, `location`, `photo`, `gender`, `birthday`, `gl_id`, `fb_id`, `tw_id`, `device_token`, `stripe_id`, `about`) VALUES
(1, '85.203.47.173', 'test', '$2y$08$D2Gi4lV8tfAwuisz2wrZBOAUDLtUgwO6ZQOaMfLNvIoHBnc9CxVpa', NULL, 'test@email.com', NULL, NULL, NULL, NULL, 1530122649, 1536028784, 1, 'test', 'TA', NULL, '4.666666', '3', '2', '1234567890', 'Buscon St,Utica,NY', '43.080883,-75.218735', '1_1530153384.jpg', 'Male', '10/28/1984', '', '', '', 'a727d587-0898-4f63-aee1-2c57591affb3', 'acct_1CoSraCPtF73ikRM', NULL),
(2, '85.203.47.8', NULL, '$2y$08$2LiC7UEi9F4P3dPwv08PEu6M/K5lDLq0mka4eFFgGI.a7d60aTfKC', NULL, 'aaa@email.com', NULL, NULL, NULL, NULL, 1530200687, 1536029042, 1, 'aaa', 'A', NULL, '5', '1', '1', '123-456-7891', 'Brooklyn, NY 11213, USA', '40.671758,-73.935919', '2_1530242883.jpg', 'Male', '', '', '', '', '', 'cus_DDQSlNixHWUjhG', NULL),
(3, '85.203.47.8', 'Katie', '$2y$08$EKSpJmcxpdXYStKd5VjHVupYFP8537DpRH2ggEyYhmaEIv6YQH00y', NULL, 'spokatie32@gmail.com', NULL, NULL, NULL, NULL, 1530243182, NULL, 1, 'Katie', 'Wilcox', NULL, '0.0', '0', '1', NULL, '', '', '', 'Male', '', '103647825731353302063', '', '', '', '', NULL),
(4, '174.216.21.156', 'Katie Wilcox ', '$2y$08$/rt/ShXx0oR0vKLf4wdS7OAtvU2O4T5ptuT7VOxsjADSd33bjbdE2', NULL, 'katie__aw@hotmail.com', NULL, NULL, NULL, NULL, 1530756001, 1536861077, 1, 'Katie', 'Wilcox', NULL, '5', '1', '2', '5095702765 ', '507 S Washington St STE 60,Spokane,Washington', '47.651012,-117.417378', '4_1530756056.jpg', 'Female', '04/19/1986', '', '', '', '99b917b2-a775-4c2b-b2c6-acb6d4150a4a', 'acct_1Cq9s3FXZ2WASDy7', NULL),
(5, '174.216.4.202', 'katie', '$2y$08$S6wyHF1BC5jEa6I/QHV8uOfPYKFrU8hROEW0OIcamniwqWcAft.fS', NULL, 'katie@kneaditnowapp.com', NULL, NULL, NULL, NULL, 1531955742, 1536863169, 1, 'katie', 'wilcox', NULL, '5', '1', '1', '5095702765', '', '', '5_1532226724.jpg', 'Male', '', '', '', '', '99b917b2-a775-4c2b-b2c6-acb6d4150a4a', 'cus_DGhQT5dp60W8uu', NULL),
(6, '174.216.21.190', ' Brea', '$2y$08$4rYgFINp4zRHZ0yoqhihjOWsLJhRLd6MyLeNNt60R43Fklb5vgkXS', NULL, 'breannalfaux@gmail.com', NULL, NULL, NULL, NULL, 1533605208, NULL, 1, ' Brea', 'Faux', NULL, '0.0', '0', '1', '5098445965', '', '', '', 'Male', '', '', '', '', '99b917b2-a775-4c2b-b2c6-acb6d4150a4a', '', NULL),
(7, '73.42.153.57', 'eric', '$2y$08$GJtipD.usrpRqheuiQvAPOUdpRJmhqWc554qN7pYyP8UqrAFWPHIO', NULL, 'motofaux182@gmail.com', NULL, NULL, NULL, NULL, 1534649982, 1534998919, 1, 'eric', 'faux', NULL, '0.0', '0', '1', '5098683740', '', '', '7_1534999072.jpg', 'Male', '', '', '', '', '99b917b2-a775-4c2b-b2c6-acb6d4150a4a', 'cus_DRb2IwdaQFRPyM', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE `users_groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 3, 1),
(5, 4, 2),
(6, 5, 1),
(7, 6, 1),
(8, 7, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_groups`
--
ALTER TABLE `admin_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_login_attempts`
--
ALTER TABLE `admin_login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_users_groups`
--
ALTER TABLE `admin_users_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_access`
--
ALTER TABLE `api_access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_limits`
--
ALTER TABLE `api_limits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_logs`
--
ALTER TABLE `api_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_posts_tags`
--
ALTER TABLE `blog_posts_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_tags`
--
ALTER TABLE `blog_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_posts`
--
ALTER TABLE `book_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `business_infos`
--
ALTER TABLE `business_infos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cover_photos`
--
ALTER TABLE `cover_photos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_posts`
--
ALTER TABLE `event_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `massage_types`
--
ALTER TABLE `massage_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message_posts`
--
ALTER TABLE `message_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_posts`
--
ALTER TABLE `payment_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `review_posts`
--
ALTER TABLE `review_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_groups`
--
ALTER TABLE `admin_groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_login_attempts`
--
ALTER TABLE `admin_login_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_users_groups`
--
ALTER TABLE `admin_users_groups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `api_access`
--
ALTER TABLE `api_access`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_keys`
--
ALTER TABLE `api_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `api_limits`
--
ALTER TABLE `api_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_logs`
--
ALTER TABLE `api_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blog_posts_tags`
--
ALTER TABLE `blog_posts_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blog_tags`
--
ALTER TABLE `blog_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `book_posts`
--
ALTER TABLE `book_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `business_infos`
--
ALTER TABLE `business_infos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cover_photos`
--
ALTER TABLE `cover_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `event_posts`
--
ALTER TABLE `event_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `massage_types`
--
ALTER TABLE `massage_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `message_posts`
--
ALTER TABLE `message_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_posts`
--
ALTER TABLE `payment_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `review_posts`
--
ALTER TABLE `review_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users_groups`
--
ALTER TABLE `users_groups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
