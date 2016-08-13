-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2016 at 09:37 AM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 7.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `masjidy`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `mosque_id` int(11) NOT NULL,
  `text` varchar(200) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `followers` int(11) NOT NULL,
  `mosque_id` int(11) NOT NULL,
  `date_time` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fav_mosque`
--

CREATE TABLE `fav_mosque` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mosque_id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `id` int(11) NOT NULL,
  `mosque_id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mosques`
--

CREATE TABLE `mosques` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `address` varchar(75) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `gm_id` int(11) NOT NULL,
  `followers` int(11) NOT NULL,
  `Fajar` time NOT NULL,
  `Zuhar` time NOT NULL,
  `Asr` time NOT NULL,
  `Maghrib` time NOT NULL,
  `Esha` time NOT NULL,
  `Jumma` time NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mosques`
--

INSERT INTO `mosques` (`id`, `name`, `address`, `latitude`, `longitude`, `gm_id`, `followers`, `Fajar`, `Zuhar`, `Asr`, `Maghrib`, `Esha`, `Jumma`, `updated_at`, `created_at`) VALUES
(4, 'masjid nimra', 'pakistan', 62.99999, 23.88888, 123, 0, '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '2016-08-12 05:36:03', '2016-08-12 05:36:03');

-- --------------------------------------------------------

--
-- Table structure for table `my_events`
--

CREATE TABLE `my_events` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `device_token` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tokens`
--

INSERT INTO `tokens` (`id`, `user_id`, `device_token`, `created_at`, `updated_at`) VALUES
(1, 4, '1122', '2016-08-10 05:52:07', '2016-08-10 05:52:07'),
(2, 5, 'adat', '2016-08-12 02:26:28', '2016-08-12 02:26:28'),
(8, 5, 'null', '2016-08-12 08:45:40', '2016-08-12 08:45:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(200) NOT NULL,
  `profile_picture` varchar(200) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `profile_picture`, `updated_at`, `created_at`) VALUES
(4, 'user12345', 'user@test.com', '$2y$10$9.dVpCQBqZzPPppIHg6NA.fXlT2IpJaoPIfG4i4LhDu7DBvm0ZWe.', '', '2016-08-10 05:49:00', '2016-08-10 05:49:00'),
(5, 'admin', 'admin@admin.com', '$2y$10$yStXUHtO1T/ZLBjXsC2ZjOCRkhXOvbJPvlur.QHdGrbH89Ei1D0Oy', '', '2016-08-12 02:22:41', '2016-08-12 02:22:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_announcments` (`mosque_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_eventMosque` (`mosque_id`);

--
-- Indexes for table `fav_mosque`
--
ALTER TABLE `fav_mosque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_favMosque` (`user_id`),
  ADD KEY `fk_idMosque` (`mosque_id`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manager_id` (`manager_id`),
  ADD KEY `mosque_id` (`mosque_id`);

--
-- Indexes for table `mosques`
--
ALTER TABLE `mosques`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `my_events`
--
ALTER TABLE `my_events`
  ADD KEY `fk_eventsUser` (`user_id`),
  ADD KEY `fk_eventsMy` (`event_id`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fav_mosque`
--
ALTER TABLE `fav_mosque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `manager`
--
ALTER TABLE `manager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mosques`
--
ALTER TABLE `mosques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `fk_announcments` FOREIGN KEY (`mosque_id`) REFERENCES `mosques` (`id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_eventMosque` FOREIGN KEY (`mosque_id`) REFERENCES `mosques` (`id`);

--
-- Constraints for table `fav_mosque`
--
ALTER TABLE `fav_mosque`
  ADD CONSTRAINT `fk_favMosque` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_idMosque` FOREIGN KEY (`mosque_id`) REFERENCES `mosques` (`id`);

--
-- Constraints for table `manager`
--
ALTER TABLE `manager`
  ADD CONSTRAINT `manager_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `manager_ibfk_2` FOREIGN KEY (`mosque_id`) REFERENCES `mosques` (`id`);

--
-- Constraints for table `my_events`
--
ALTER TABLE `my_events`
  ADD CONSTRAINT `fk_eventsMy` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  ADD CONSTRAINT `fk_eventsUser` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
