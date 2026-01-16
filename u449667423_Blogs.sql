-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 15, 2026 at 11:44 AM
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
-- Database: `u449667423_Blogs`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`u449667423_sneha`@`127.0.0.1` PROCEDURE `sp_get_blogs_by_category` (IN `category_slug` VARCHAR(50), IN `limit_count` INT)   BEGIN
    SELECT * FROM v_published_blogs
    WHERE category_slug = category_slug
    ORDER BY published_at DESC
    LIMIT limit_count;
END$$

CREATE DEFINER=`u449667423_sneha`@`127.0.0.1` PROCEDURE `sp_get_featured_blogs` (IN `limit_count` INT)   BEGIN
    SELECT * FROM v_published_blogs
    WHERE is_featured = TRUE
    ORDER BY published_at DESC
    LIMIT limit_count;
END$$

CREATE DEFINER=`u449667423_sneha`@`127.0.0.1` PROCEDURE `sp_increment_blog_views` (IN `blog_id` INT)   BEGIN
    UPDATE blogs 
    SET views_count = views_count + 1 
    WHERE id = blog_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `featured_image` varchar(500) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `views_count` int(11) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `slug`, `content`, `excerpt`, `category_id`, `image_url`, `featured_image`, `is_featured`, `is_published`, `published_at`, `views_count`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(1, 'Is 2026 the Right Time to Invest in Indian Real Estate?', 'is-2026-right-time-invest-indian-real-estate', 'Explore market trends, government policies, and expert predictions to make informed investment decisions. The Indian real estate market has shown remarkable resilience and growth in recent years. With government initiatives like RERA, affordable housing schemes, and infrastructure development, the sector presents numerous opportunities for investors. This comprehensive guide will help you understand the current market dynamics, identify the best investment opportunities, and make informed decisions about your real estate investments in 2026.', 'Explore market trends, government policies, and expert predictions to make informed investment decisions.', 3, 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6', NULL, 1, 1, '2026-01-15 10:43:00', 0, NULL, NULL, NULL, '2026-01-15 10:43:00', '2026-01-15 10:43:00'),
(2, 'Top 7 Mistakes First-Time Home Buyers Make in India', 'top-7-mistakes-first-time-home-buyers-india', 'Avoid common pitfalls and make your first property purchase smooth and successful with these expert tips. Buying your first home is one of the most significant financial decisions you will make. However, many first-time buyers fall into common traps that can cost them time, money, and peace of mind. This article outlines the seven most common mistakes and provides practical advice on how to avoid them.', 'Avoid common pitfalls and make your first property purchase smooth and successful with these expert tips.', 1, 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9', NULL, 1, 1, '2026-01-15 10:43:00', 0, NULL, NULL, NULL, '2026-01-15 10:43:00', '2026-01-15 10:43:00'),
(3, 'RERA Act: Complete Guide for Property Buyers', 'rera-act-complete-guide-property-buyers', 'Understand how RERA protects your interests and what you need to know before buying property. The Real Estate (Regulation and Development) Act, 2016 (RERA) was introduced to bring transparency and accountability to the real estate sector. This comprehensive guide explains how RERA protects homebuyers, what rights you have under the act, and how to file complaints if needed.', 'Understand how RERA protects your interests and what you need to know before buying property.', 4, 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c', NULL, 1, 1, '2026-01-15 10:43:00', 0, NULL, NULL, NULL, '2026-01-15 10:43:00', '2026-01-15 10:43:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `icon` varchar(10) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Buy', 'buy', '🏠', 'Complete guides for property buyers', 1, 1, '2026-01-15 10:43:00', '2026-01-15 10:43:00'),
(2, 'Rent', 'rent', '🔑', 'Rental guides and lease tips', 2, 1, '2026-01-15 10:43:00', '2026-01-15 10:43:00'),
(3, 'Investment', 'investment', '📈', 'Smart property investment strategies', 3, 1, '2026-01-15 10:43:00', '2026-01-15 10:43:00'),
(4, 'Legal', 'legal', '📋', 'Legal guides and document checklists', 4, 1, '2026-01-15 10:43:00', '2026-01-15 10:43:00'),
(5, 'Tips', 'tips', '💡', 'Property tips and advice', 5, 1, '2026-01-15 10:43:00', '2026-01-15 10:43:00'),
(6, 'News', 'news', '📰', 'Latest real estate news and updates', 6, 1, '2026-01-15 10:43:00', '2026-01-15 10:43:00');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied','archived') DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_contact_messages`
-- (See below for the actual view)
--
CREATE TABLE `v_contact_messages` (
`id` int(11)
,`name` varchar(100)
,`email` varchar(100)
,`mobile` varchar(20)
,`message` text
,`status` enum('new','read','replied','archived')
,`created_at` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_published_blogs`
-- (See below for the actual view)
--
CREATE TABLE `v_published_blogs` (
`id` int(11)
,`title` varchar(255)
,`slug` varchar(255)
,`content` text
,`excerpt` text
,`image_url` varchar(500)
,`is_featured` tinyint(1)
,`views_count` int(11)
,`published_at` timestamp
,`created_at` timestamp
,`updated_at` timestamp
,`category_name` varchar(50)
,`category_slug` varchar(50)
,`category_icon` varchar(10)
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_published` (`is_published`,`published_at`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure for view `v_contact_messages`
--
DROP TABLE IF EXISTS `v_contact_messages`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u449667423_sneha`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_contact_messages`  AS SELECT `contact_messages`.`id` AS `id`, `contact_messages`.`name` AS `name`, `contact_messages`.`email` AS `email`, `contact_messages`.`mobile` AS `mobile`, `contact_messages`.`message` AS `message`, `contact_messages`.`status` AS `status`, `contact_messages`.`created_at` AS `created_at`, `contact_messages`.`updated_at` AS `updated_at` FROM `contact_messages` ORDER BY `contact_messages`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `v_published_blogs`
--
DROP TABLE IF EXISTS `v_published_blogs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u449667423_sneha`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_published_blogs`  AS SELECT `b`.`id` AS `id`, `b`.`title` AS `title`, `b`.`slug` AS `slug`, `b`.`content` AS `content`, `b`.`excerpt` AS `excerpt`, `b`.`image_url` AS `image_url`, `b`.`is_featured` AS `is_featured`, `b`.`views_count` AS `views_count`, `b`.`published_at` AS `published_at`, `b`.`created_at` AS `created_at`, `b`.`updated_at` AS `updated_at`, `c`.`name` AS `category_name`, `c`.`slug` AS `category_slug`, `c`.`icon` AS `category_icon` FROM (`blogs` `b` left join `categories` `c` on(`b`.`category_id` = `c`.`id`)) WHERE `b`.`is_published` = 1 ORDER BY `b`.`published_at` DESC ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
