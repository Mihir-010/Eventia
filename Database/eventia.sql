-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2024 at 11:16 AM
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
-- Database: `eventia`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`) VALUES
(1, 'admin@gmail.com', '$2y$10$h9AGbleg84ozv.1DQmnFXO7ILChofhYOTCZ/TQvuApmpsKP8ALu7u');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `event_date` date DEFAULT NULL,
  `people_attending` int(11) DEFAULT NULL,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `total_cost` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('Pending','Paid') DEFAULT 'Pending',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `team_id`, `event_id`, `booking_date`, `event_date`, `people_attending`, `status`, `total_cost`, `payment_status`, `updated_at`) VALUES
(22, 1, 10, NULL, '2024-10-24 12:24:40', '2024-10-27', 500, 'Completed', 5000.00, 'Paid', '2024-10-24 12:28:19'),
(23, 1, 12, NULL, '2024-11-03 15:20:33', '2024-11-20', 1200, 'Completed', 5000.00, 'Paid', '2024-11-03 15:21:54'),
(24, 14, 12, NULL, '2024-11-05 06:05:33', '2024-11-14', 500, 'Completed', 15000.00, 'Paid', '2024-11-05 06:09:48'),
(27, 26, 11, NULL, '2024-11-07 06:17:02', '2024-11-20', 500, 'Completed', 5000.00, 'Paid', '2024-11-07 06:20:21'),
(28, 27, 11, NULL, '2024-11-07 06:32:24', '2024-11-25', 1000, 'Completed', 80000.00, 'Paid', '2024-11-07 06:40:39'),
(29, 1, 37, NULL, '2024-11-07 08:46:41', '2024-11-11', 5000, 'Pending', NULL, 'Pending', '2024-11-07 08:46:41');

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `chat_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sender` enum('User','Team') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`chat_id`, `booking_id`, `user_id`, `team_id`, `message`, `sender`, `created_at`) VALUES
(1, 5, 1, 6, 'hey', 'User', '2024-10-07 18:42:40'),
(2, 5, 1, 6, 'we have reciva', 'Team', '2024-10-07 18:42:59'),
(3, 5, 1, 6, 'hey', 'User', '2024-10-07 18:43:06'),
(4, 5, 1, 6, 'hey', 'User', '2024-10-07 18:44:14'),
(5, 5, 1, 6, 'hey', 'User', '2024-10-07 18:46:16'),
(6, 9, 8, 6, 'hi', 'User', '2024-10-14 18:04:11'),
(7, 9, 8, 6, 'what is ur req', 'Team', '2024-10-14 18:04:55'),
(8, 9, 8, 6, 'price', 'User', '2024-10-14 18:05:17'),
(9, 9, 8, 6, 'i will update', 'Team', '2024-10-14 18:05:34'),
(10, 10, 9, 6, 'price', 'User', '2024-10-14 18:19:50'),
(11, 10, 9, 6, '500', 'Team', '2024-10-14 18:20:06'),
(12, 10, 9, 6, 'kk', 'User', '2024-10-14 18:20:16'),
(13, 11, 1, 13, 'price', 'User', '2024-10-15 17:58:30'),
(14, 11, 1, 13, '500', 'Team', '2024-10-15 17:59:27'),
(15, 11, 1, 13, 'kk update it', 'User', '2024-10-15 17:59:54'),
(16, 12, 1, 10, 'hi', 'User', '2024-10-16 05:33:56'),
(17, 12, 1, 10, 'how can i help ', 'Team', '2024-10-16 05:34:19'),
(18, 12, 1, 10, '500', 'User', '2024-10-16 05:34:37'),
(19, 15, 13, 16, 'hi', 'User', '2024-10-17 06:13:22'),
(20, 15, 13, 16, '500', 'Team', '2024-10-17 06:13:36'),
(21, 16, 14, 10, 'hlo', 'User', '2024-10-17 06:23:47'),
(22, 18, 1, 10, 'hi', 'Team', '2024-10-17 10:35:53'),
(23, 22, 1, 10, '5000', 'User', '2024-10-24 12:24:52'),
(24, 22, 1, 10, 'that is fine by our side', 'Team', '2024-10-24 12:25:10'),
(25, 22, 1, 10, 'kk', 'User', '2024-10-24 12:25:47'),
(26, 22, 1, 10, 'update it ', 'User', '2024-10-24 12:27:20'),
(27, 24, 14, 12, 'hello we seen ur booking. Tell about ur requirements', 'Team', '2024-11-05 06:06:39'),
(28, 24, 14, 12, '500 people non veg food what r ur services', 'User', '2024-11-05 06:07:11'),
(29, 24, 14, 12, 'payment 15000', 'Team', '2024-11-05 06:07:26'),
(30, 24, 14, 12, 'sure ', 'User', '2024-11-05 06:07:36'),
(31, 24, 14, 12, 'check pay now option u could able to make payment', 'Team', '2024-11-05 06:08:02'),
(32, 25, 16, 13, 'can you get some free food some complements', 'User', '2024-11-05 06:33:30'),
(33, 25, 16, 13, 'please discount it', 'User', '2024-11-05 06:33:58'),
(34, 25, 16, 13, 'poda', 'Team', '2024-11-05 06:34:55'),
(35, 27, 26, 11, 'hello we seen ur booking. Tell about ur requirements', 'Team', '2024-11-07 06:18:13'),
(36, 27, 26, 11, '5000', 'User', '2024-11-07 06:18:21'),
(37, 28, 27, 11, 'hlo', 'User', '2024-11-07 06:34:27'),
(38, 28, 27, 11, 'contact me', 'Team', '2024-11-07 06:37:31'),
(39, 28, 27, 11, 'no.9876543210', 'Team', '2024-11-07 06:37:47'),
(40, 28, 27, 11, 'ok', 'User', '2024-11-07 06:38:07'),
(41, 28, 27, 11, 'total price:80000', 'Team', '2024-11-07 06:38:35'),
(42, 28, 27, 11, 'ok', 'User', '2024-11-07 06:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaint_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `complaint_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`complaint_id`, `user_id`, `team_id`, `complaint_text`, `created_at`) VALUES
(1, 1, 10, 'sdk fdfk dhsdf sdafb wia fsdvfb say esjk bsdj vwdbbs', '2024-11-03 14:17:53');

-- --------------------------------------------------------

--
-- Table structure for table `eventteams`
--

CREATE TABLE `eventteams` (
  `team_id` int(11) NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `category` enum('Catering','Decoration','Venue') NOT NULL,
  `description` text DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `availability_status` tinyint(1) DEFAULT 1,
  `rating` float DEFAULT 5,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `review_count` int(11) DEFAULT 0,
  `min_price` decimal(10,2) DEFAULT NULL,
  `max_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eventteams`
--

INSERT INTO `eventteams` (`team_id`, `team_name`, `category`, `description`, `contact_info`, `availability_status`, `rating`, `status`, `created_at`, `profile_pic`, `email`, `password`, `review_count`, `min_price`, `max_price`) VALUES
(10, 'Vijaya Convention Centre', 'Venue', 'Welcome to Vijaya Convention Centre, your premier venue team dedicated to creating unforgettable experiences for your events! Our team specializes in providing top-notch venue services tailored to meet your unique needs..', '80788 82200', 1, 4, 'Approved', '2024-10-15 17:13:53', '2023-03-26.jpg', 'vcc@gmail.com', '$2y$10$25tV6talGyRDX3I7xZaBHO7B7R0iWwHRC0GUgUUJV27ePK0IsPyyu', 1, 10000.00, 100000.00),
(11, 'The Windsor Castle Convention Center', 'Venue', 'Expertise in Diverse Venues: We have access to a wide range of venues, from elegant banquet halls to stunning outdoor spaces. Our team will help you choose the perfect setting for your event.', '1122334455', 1, 2.5, 'Approved', '2024-10-15 17:22:08', '2019-06-29.jpg', 'wcc@gmail.com', '$2y$10$yjE/eHLTJ716WxFrG9smTO12C8ihHw1vGzllFstN8W/q3C3.8mI1y', 2, 5000.00, 50000.00),
(12, 'Harvest Caters', 'Catering', 'Harvest explore all avenues of catering services to serve our guests. With devoted professionals, we organize all types of functions including weddings, anniversaries, corporate events, get together, birthday parties, celebration of life services and any other types of small and grand events.\r\n\r\nHarvest specialises in high class catering services in Kerala state. Memories are created in breaking bread with others. We create formal and informal meal plans, complete with the decorations, cutlery, crockery and other items to make it experience flow easily.', '+91 93871 77774', 1, 3.5, 'Approved', '2024-10-15 17:35:08', '2023-07-04.jpg', 'h@gmail.com', '$2y$10$FQmEjQulGoYWy6TsNixp1Ozp3o1oYES6VvJuvGB/ItomNWOOvSIG6', 2, 10000.00, 50000.00),
(13, 'Wedding Bazzar', 'Decoration', 'Altar arrangements: Decorative arrangements that are placed on opposite sides of the wedding altar. They are typically made from flora and fauna. Arbor: A type of altar that is usually used in outdoor wedding decor. They are made out of tree branches and/or climbing plants.', '9988776655', 1, 5, 'Approved', '2024-10-15 17:47:02', 'mini_magick20231207-32660-ge7gwl.avif', 'w@gmail.com', '$2y$10$O9Hb7pFKOJaJY81LVshGPOMg0j0342eoHmqzpUpeW9DXxKotlrzHu', 0, 5000.00, 75000.00),
(37, 'Best venue', 'Venue', 'hydtcvjye uj3 yfuty', '9865455555', 1, 5, 'Approved', '2024-11-07 08:42:22', 'Screenshot 2024-11-03 204142.png', 'z@gmail.com', '$2y$10$ftYw32Tze3EJk9fIjgbguufm3T2r1P02rtfxOdSpNH3X.QxnNdF1O', 0, 10000.00, 100000.00);

-- --------------------------------------------------------

--
-- Table structure for table `event_team_post`
--

CREATE TABLE `event_team_post` (
  `post_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `likes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_team_post`
--

INSERT INTO `event_team_post` (`post_id`, `team_id`, `title`, `description`, `image`, `created_at`, `likes`) VALUES
(5, 10, 'Interior', 'This image show the interior of our center. You could book our venue for your events. We will make sure you are not disappointed.', '670ea369245d5.jpg', '2024-10-15 17:16:25', 1),
(6, 10, 'New attraction', 'We recently added a foundain to the venue center which can be an eye catcher for the people attending the event.', '670ea3bbd7ca8.jpg', '2024-10-15 17:17:47', 1),
(7, 11, 'Our venue beauty', 'This is the complete view of our venue with backwater. Backwater view make it more beautifull and the would be a highlight in your event', '670ea63ded205.jpg', '2024-10-15 17:28:29', 1),
(8, 12, 'Our recent work', 'we had a very big event where many celebrities participated. Everyone enjoyed the event and we were very happy.', '670ea98d1b37f.jpg', '2024-10-15 17:42:37', 3),
(9, 13, 'Recent work', 'we did this recent work at Kottayam where everyone who appeared for the event paised for our design......', 'confetti-collectorate-kottayam-stage-decorators-8a3w1it55u.avif', '2024-10-15 17:48:28', 2);

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `like_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_likes`
--

INSERT INTO `post_likes` (`like_id`, `post_id`, `user_id`, `created_at`) VALUES
(9, 9, 1, '2024-10-15 18:02:18'),
(15, 5, 14, '2024-10-17 06:23:10'),
(16, 6, 14, '2024-10-17 06:23:15'),
(22, 8, 14, '2024-11-05 06:03:33'),
(23, 8, 1, '2024-11-05 06:04:06'),
(25, 7, 26, '2024-11-07 06:16:50');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `team_id`, `rating`, `comment`, `created_at`) VALUES
(8, 1, 10, 4, 'Nice work', '2024-10-24 12:28:35'),
(9, 1, 12, 4, 'Great work', '2024-11-03 15:22:27'),
(10, 14, 12, 3, 'very well managed program but food was not good', '2024-11-05 06:10:21'),
(13, 26, 11, 4, 'good work', '2024-11-07 06:20:46'),
(14, 27, 11, 1, 'Nice work ..Thank you ', '2024-11-07 06:41:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `phone`, `created_at`) VALUES
(1, 'mihir', 'mihir@gmail.com', '$2y$10$konhLJvavRDVN4Tr4O/19.pxGkvy5W5RiCR04mFdsstMuao3B8H6.', '9744996346', '2024-10-03 17:59:40'),
(14, 'nandana', 'nandana@gmail.com', '$2y$10$QnavartdjY.SyZ9PKRh5q.v9FKRGQpvRgQ2BgM5xsOQhXyVmZZAJK', '1234567890', '2024-10-17 06:22:05'),
(26, 'kiran', 'k@gmail.com', '$2y$10$FaEoTunNB8ICfMpuTbKtsOFyXDBK3GRxnsLJwV95dkjJ3is8ehnRK', '1122334455', '2024-11-07 06:15:32'),
(27, 'Arjun', 'arjun@gmail.com', '$2y$10$TPcwKlTU11uMNWOViM3dLeFUS6JBQA.H2TmfFC8cFJ8ybmsLhxXzu', '1236547892', '2024-11-07 06:29:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `team_id` (`team_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`chat_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaint_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `eventteams`
--
ALTER TABLE `eventteams`
  ADD PRIMARY KEY (`team_id`);

--
-- Indexes for table `event_team_post`
--
ALTER TABLE `event_team_post`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`like_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `eventteams`
--
ALTER TABLE `eventteams`
  MODIFY `team_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `event_team_post`
--
ALTER TABLE `event_team_post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `eventteams` (`team_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE;

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `eventteams` (`team_id`) ON DELETE CASCADE;

--
-- Constraints for table `event_team_post`
--
ALTER TABLE `event_team_post`
  ADD CONSTRAINT `event_team_post_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `eventteams` (`team_id`) ON DELETE CASCADE;

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `event_team_post` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `eventteams` (`team_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
