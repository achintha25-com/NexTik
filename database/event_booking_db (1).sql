-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 19, 2026 at 03:51 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event_booking_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED NOT NULL,
  `ticket_option_id` bigint UNSIGNED DEFAULT NULL,
  `ticket_option_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `booking_reference` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'card',
  `payment_reference` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('paid','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'paid',
  `paid_at` timestamp NULL DEFAULT NULL,
  `status` enum('confirmed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirmed',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_reference` (`booking_reference`),
  UNIQUE KEY `idx_bookings_payment_reference` (`payment_reference`),
  KEY `idx_bookings_user` (`user_id`),
  KEY `idx_bookings_event` (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `event_id`, `ticket_option_id`, `ticket_option_name`, `booking_reference`, `quantity`, `unit_price`, `total_amount`, `payment_method`, `payment_reference`, `payment_status`, `paid_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 1, NULL, NULL, 'EB-DEMO2026', 2, 7000.00, 14000.00, 'card', 'NT-PAY-848D668F', 'paid', '2026-07-31 14:23:49', 'confirmed', '2026-07-31 14:23:49', '2026-08-06 08:40:26'),
(3, 3, 3, 3, 'General Admission', 'EB-16E2B907', 1, 4000.00, 4000.00, 'card', 'NT-PAY-6BD18C57', 'paid', '2026-07-31 15:21:05', 'confirmed', '2026-07-31 15:21:05', '2026-08-06 08:40:26'),
(4, 9, 11, 21, 'VIP Lounge', 'NT-6DE30541', 1, 9500.00, 9500.00, 'card', 'NT-PAY-9F49F602', 'paid', '2026-08-06 08:59:14', 'confirmed', '2026-08-06 08:59:14', '2026-08-06 08:59:14'),
(5, 10, 3, 3, 'General Admission', 'NT-60057013', 1, 4000.00, 4000.00, 'card', 'NT-PAY-B2F499BB', 'paid', '2026-08-10 08:02:29', 'confirmed', '2026-08-10 08:02:29', '2026-08-10 08:02:29'),
(6, 12, 3, 3, 'General Admission', 'NT-3C11A2E7', 1, 4000.00, 4000.00, 'card', 'NT-PAY-65387B53', 'paid', '2026-08-17 12:49:38', 'confirmed', '2026-08-17 12:49:38', '2026-08-17 12:49:38');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Trending Now', 'trending-now', '2026-07-31 14:23:49', '2026-07-31 14:23:49'),
(2, 'Concerts', 'concerts', '2026-07-31 14:23:49', '2026-07-31 14:23:49'),
(3, 'EDM', 'edm', '2026-07-31 14:23:49', '2026-07-31 14:23:49'),
(4, 'Sport', 'sport', '2026-07-31 14:23:49', '2026-07-31 14:23:49'),
(5, 'Family', 'family', '2026-07-31 14:23:49', '2026-07-31 14:23:49'),
(6, 'Tamil DJ', 'tamil-dj', '2026-07-31 14:23:49', '2026-07-31 14:23:49');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General enquiry',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','replied','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `reply_text` text COLLATE utf8mb4_unicode_ci,
  `replied_by` bigint UNSIGNED DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_contact_messages_admin` (`replied_by`),
  KEY `idx_contact_messages_status` (`status`),
  KEY `idx_contact_messages_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `organizer_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `venue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Colombo',
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_tickets` int UNSIGNED NOT NULL,
  `available_tickets` int UNSIGNED NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('draft','published','cancelled','postponed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_events_organizer` (`organizer_id`),
  KEY `fk_events_category` (`category_id`),
  KEY `idx_events_public` (`status`,`event_date`),
  KEY `idx_events_city` (`city`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `organizer_id`, `category_id`, `title`, `slug`, `description`, `venue`, `city`, `event_date`, `start_time`, `end_time`, `price`, `total_tickets`, `available_tickets`, `image`, `is_featured`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 'SUDBEAT SHOWCASE 2026', 'sudbeat-showcase-2026', 'Experience an unforgettable night of live performances, great vibes, and premium entertainment in Sri Lanka.', 'Port City Colombo', 'Colombo', '2026-08-31', '17:00:00', '23:59:00', 7000.00, 500, 498, 'events/sudbeat-showcase-2026.png', 1, 'published', '2026-07-31 14:23:49', '2026-08-17 08:05:38'),
(2, 2, 3, 'ANJUNADEEP OPEN AIR DEBUT', 'anjunadeep-open-air-debut', 'Dance under the stars with an exceptional open-air EDM experience.', 'Colombo Lotus Tower', 'Colombo', '2026-09-14', '11:00:00', '23:59:00', 6000.00, 500, 500, 'events/anjunadeep-open-air-2026.png', 1, 'published', '2026-07-31 14:23:49', '2026-08-17 08:05:38'),
(3, 2, 4, 'MAIN CHAMPIONSHIP', 'main-championship', 'A day of high-energy competition and unforgettable sporting moments.', 'Sirimavo Bandaranaike Memorial Exhibition Centre', 'Colombo', '2026-08-20', '15:00:00', '22:00:00', 4000.00, 500, 497, 'events/main-championship-2026.png', 1, 'published', '2026-07-31 14:23:49', '2026-08-17 12:49:38'),
(4, 2, 5, 'I LOVE BRUNCH', 'i-love-brunch', 'A relaxed family-friendly day of food, music, and good company.', 'King and Queen Cantina', 'Colombo', '2026-09-29', '10:00:00', '15:00:00', 4000.00, 300, 300, 'events/i-love-brunch-2026.png', 0, 'published', '2026-07-31 14:23:49', '2026-08-17 08:05:38'),
(5, 2, 6, 'BLOODY KATCHERI - TAMIL DJ NIGHT', 'bloody-katcheri-tamil-dj-night', 'A vibrant Tamil DJ night with live entertainment and dancing.', 'The Joint Pub and Grill', 'Colombo', '2026-08-25', '18:00:00', '23:59:00', 3000.00, 500, 500, 'events/bloody-katcheri-2026.png', 0, 'published', '2026-07-31 14:23:49', '2026-08-17 08:05:38'),
(6, 2, 1, 'GOLDEN HORIZON 2.0', 'golden-horizon-2', 'A premium coastal experience with music, food, and entertainment.', 'Ceylonica Beach Hotel', 'Kalutara', '2026-09-04', '10:00:00', '22:00:00', 4000.00, 500, 500, 'events/golden-horizon-2026.png', 0, 'published', '2026-07-31 14:23:49', '2026-08-17 08:05:38'),
(7, 2, 3, 'THE LOST CITY', 'the-lost-city', 'A next-generation EDM experience for music lovers.', 'Port City Colombo', 'Colombo', '2026-10-29', '18:00:00', '23:59:00', 5000.00, 500, 500, 'events/the-lost-city-2026.png', 1, 'published', '2026-07-31 14:23:49', '2026-08-17 08:05:38'),
(8, 2, 2, 'NIMNADA', 'nimnada', 'An intimate concert evening with outstanding local artists.', 'Dharmaraja College Auditorium', 'Kandy', '2026-08-28', '19:00:00', '22:00:00', 2500.00, 500, 500, 'events/nimnada-2026.png', 0, 'published', '2026-07-31 14:23:49', '2026-08-17 08:05:38'),
(9, 4, 2, 'Hill Country Live Sessions', 'hill-country-live-sessions', 'An open-air concert evening in the heart of Kandy with local bands, food stalls, and lakeside views.', 'Kandy Lake View Arena', 'Kandy', '2026-08-24', '18:30:00', '23:00:00', 4500.00, 400, 400, 'events/hill-country-live-sessions-2026.png', 1, 'published', '2026-08-06 08:42:17', '2026-08-17 08:05:38'),
(10, 5, 5, 'Fort Sunset Food Fest', 'fort-sunset-food-fest', 'A family-friendly evening of street food, live acoustic sets, and craft vendors inside the Galle Fort.', 'Galle Fort Green', 'Galle', '2026-08-30', '16:00:00', '22:00:00', 2500.00, 500, 500, 'events/fort-sunset-food-fest-2026.png', 0, 'published', '2026-08-06 08:42:17', '2026-08-17 08:05:38'),
(11, 6, 3, 'Coastal Beats By The Bay', 'coastal-beats-by-the-bay', 'Dance until midnight with international DJs, light shows, and a beachside bar at Negombo lagoon.', 'Lagoon Deck Negombo', 'Negombo', '2026-09-07', '20:00:00', '23:59:00', 5500.00, 500, 499, 'events/coastal-beats-by-the-bay-2026.png', 1, 'published', '2026-08-06 08:42:18', '2026-08-17 08:05:38'),
(12, 7, 6, 'Northern Lights Tamil Night', 'northern-lights-tamil-night', 'Celebrate Tamil music and culture with live DJs, traditional fusion performances, and local cuisine.', 'Jaffna Cultural Centre', 'Jaffna', '2026-09-02', '19:00:00', '23:30:00', 3500.00, 500, 500, 'events/northern-lights-tamil-night-2026.png', 0, 'published', '2026-08-06 08:42:19', '2026-08-17 08:05:38'),
(13, 8, 4, 'Ella Ridge Trail Run', 'ella-ridge-trail-run', 'A scenic mountain trail run and wellness morning with recovery zones, music, and healthy food pop-ups.', 'Ella Rock Base Camp', 'Ella', '2026-09-15', '06:00:00', '12:00:00', 3200.00, 400, 400, 'events/ella-ridge-trail-run-2026.png', 0, 'published', '2026-08-06 08:42:20', '2026-08-17 08:05:38');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_options`
--

DROP TABLE IF EXISTS `ticket_options`;
CREATE TABLE IF NOT EXISTS `ticket_options` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_tickets` int UNSIGNED NOT NULL,
  `available_tickets` int UNSIGNED NOT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ticket_options_event` (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_options`
--

INSERT INTO `ticket_options` (`id`, `event_id`, `name`, `price`, `total_tickets`, `available_tickets`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'VIP', 12000.00, 100, 98, 0, '2026-08-06 08:30:22', '2026-08-06 08:30:22'),
(2, 2, 'General Admission', 6000.00, 500, 500, 0, '2026-08-06 08:30:22', '2026-08-06 08:30:22'),
(3, 3, 'General Admission', 4000.00, 500, 497, 0, '2026-08-06 08:30:22', '2026-08-17 12:49:38'),
(4, 4, 'General Admission', 4000.00, 300, 300, 0, '2026-08-06 08:30:22', '2026-08-06 08:30:22'),
(5, 5, 'General Admission', 3000.00, 500, 500, 0, '2026-08-06 08:30:22', '2026-08-06 08:30:22'),
(6, 6, 'General Admission', 4000.00, 500, 500, 0, '2026-08-06 08:30:22', '2026-08-06 08:30:22'),
(7, 7, 'General Admission', 5000.00, 500, 500, 0, '2026-08-06 08:30:22', '2026-08-06 08:30:22'),
(8, 8, 'General Admission', 2500.00, 500, 500, 0, '2026-08-06 08:30:22', '2026-08-06 08:30:22'),
(16, 1, 'Standing', 7000.00, 400, 400, 1, '2026-08-06 08:30:22', '2026-08-06 08:30:22'),
(17, 9, 'VIP', 8500.00, 80, 80, 0, '2026-08-06 08:42:17', '2026-08-06 08:42:17'),
(18, 9, 'Standing', 4500.00, 320, 320, 1, '2026-08-06 08:42:17', '2026-08-06 08:42:17'),
(19, 10, 'Family Pass', 5000.00, 150, 150, 0, '2026-08-06 08:42:17', '2026-08-06 08:42:17'),
(20, 10, 'General Admission', 2500.00, 350, 350, 1, '2026-08-06 08:42:17', '2026-08-06 08:42:17'),
(21, 11, 'VIP Lounge', 9500.00, 60, 59, 0, '2026-08-06 08:42:18', '2026-08-06 08:59:14'),
(22, 11, 'Standing', 5500.00, 440, 440, 1, '2026-08-06 08:42:18', '2026-08-06 08:42:18'),
(23, 12, 'Premium', 6000.00, 100, 100, 0, '2026-08-06 08:42:19', '2026-08-06 08:42:19'),
(24, 12, 'General', 3500.00, 400, 400, 1, '2026-08-06 08:42:19', '2026-08-06 08:42:19'),
(25, 13, 'Early Bird', 3200.00, 120, 120, 0, '2026-08-06 08:42:20', '2026-08-06 08:42:20'),
(26, 13, 'Standard Entry', 4200.00, 280, 280, 1, '2026-08-06 08:42:20', '2026-08-06 08:42:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('customer','organizer','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'System Admin', 'admin@eventbook.lk', '0771234567', '$2y$10$Iq/irBATt1eABeyNSk56beZvQhpZQNc2hGSGSmj/tL2D76qrCm0qS', 'admin', '2026-07-31 14:23:49', '2026-07-31 14:23:49'),
(2, 'Colombo Events Co.', 'organizer@eventbook.lk', '0772345678', '$2y$10$Iq/irBATt1eABeyNSk56beZvQhpZQNc2hGSGSmj/tL2D76qrCm0qS', 'organizer', '2026-07-31 14:23:49', '2026-07-31 14:23:49'),
(3, 'Achintha', 'achintham2007@gmail.com', '0774850133', '$2y$10$k3975/OXxb0RhNGLPnh4vuXE/MrrhGk4vWCoK38EReR09mDxjZpT.', 'customer', '2026-07-31 14:23:49', '2026-08-01 11:20:01'),
(4, 'Kandy Live Events', 'kandy.live@nextik.lk', '0774000001', '$2y$10$Iq/irBATt1eABeyNSk56beZvQhpZQNc2hGSGSmj/tL2D76qrCm0qS', 'organizer', '2026-08-06 08:42:15', '2026-08-06 08:42:15'),
(5, 'Galle Festival Group', 'galle.fest@nextik.lk', '0774000002', '$2y$10$Iq/irBATt1eABeyNSk56beZvQhpZQNc2hGSGSmj/tL2D76qrCm0qS', 'organizer', '2026-08-06 08:42:17', '2026-08-06 08:42:17'),
(6, 'Negombo Nightlife', 'negombo.nights@nextik.lk', '0774000003', '$2y$10$Iq/irBATt1eABeyNSk56beZvQhpZQNc2hGSGSmj/tL2D76qrCm0qS', 'organizer', '2026-08-06 08:42:17', '2026-08-06 08:42:17'),
(7, 'Jaffna Cultural Circle', 'jaffna.culture@nextik.lk', '0774000004', '$2y$10$Iq/irBATt1eABeyNSk56beZvQhpZQNc2hGSGSmj/tL2D76qrCm0qS', 'organizer', '2026-08-06 08:42:18', '2026-08-06 08:42:18'),
(8, 'Ella Mountain Sounds', 'ella.sounds@nextik.lk', '0774000005', '$2y$10$Iq/irBATt1eABeyNSk56beZvQhpZQNc2hGSGSmj/tL2D76qrCm0qS', 'organizer', '2026-08-06 08:42:19', '2026-08-06 08:42:19'),
(9, 'kisara', 'kisara@gmail.com', '0778904563', '$2y$10$jF6Nv3b2WgWWZJkJ54XHie9xcsMv24O6tEBWpNXSfqhCNkPnWZJZ6', 'customer', '2026-08-06 08:50:08', '2026-08-06 08:50:08'),
(10, 'Ayodya kalupakaya', 'ayodya@gmail.com', '0775432100', '$2y$10$QEvIhXAi4X8G78BIF457GuoZ2w62ai2Y.1JHru17NsW4RD8ii77sm', 'customer', '2026-08-10 08:01:47', '2026-08-10 08:01:47'),
(12, 'Achintha Madushan', 'achintha@gmail.com', '0774850133', '$2y$10$t66qfxrxgx5MYri1SFjroOuNbGInEIPRFNPmaNFru7n1Qke0xTaha', 'customer', '2026-08-17 12:48:06', '2026-08-17 12:48:06');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `fk_contact_messages_admin` FOREIGN KEY (`replied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_events_organizer` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `ticket_options`
--
ALTER TABLE `ticket_options`
  ADD CONSTRAINT `fk_ticket_options_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
