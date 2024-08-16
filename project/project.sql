-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2024 at 10:31 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `proj_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `team` varchar(255) DEFAULT NULL,
  `date_approved` date DEFAULT NULL,
  `date_started` date DEFAULT NULL,
  `date_ended` date DEFAULT NULL,
  `status` enum('ongoing','stopped','completed') DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `proj_name`, `description`, `budget`, `type`, `team`, `date_approved`, `date_started`, `date_ended`, `status`, `remarks`) VALUES
(6, 'Annual Tech Conferences', 'A conference for tech enthusiasts to discuss latest trends.', 50000.00, 'Conference', 'John Doe, Jane Smith', '2024-07-17', '2024-07-22', '2024-08-15', 'stopped', 'Successfully conducted with over 500 participants.'),
(7, 'Summer Music Festival', 'A festival featuring local and international artists.', 75000.00, 'Festival', 'Emily White, Mike Brown', '2024-07-24', '2024-07-31', '2024-09-19', 'ongoing', 'Great feedback from attendees.'),
(8, 'Charity Run', 'A run to raise funds for local charities.', 20000.00, 'Fundraiser', 'Alice Green, Bob Blue', '2024-08-21', '2024-09-09', '2024-10-24', 'ongoing', 'Raised $15,000 for charity.'),
(9, 'Winter Galae', 'A formal gala to celebrate the end of the year.', 30000.00, 'Gala', 'Charlie Black, Eve Brown', '2024-07-02', '2024-07-04', '2024-07-11', 'stopped', 'Venue booked, catering confirmed.'),
(10, 'Startup Pitch Night', 'An event for startups to pitch their ideas to investors.', 10000.00, 'Networking', 'Frank White, Grace Green', '2024-07-03', '2024-07-15', '2024-07-30', 'stopped', 'Five startups secured funding.'),
(11, 'Art Exhibition', 'An exhibition showcasing local artists\\\' work.', 15000.00, 'Exhibition', 'Hannah Black, Ian White', '2024-07-17', '2024-07-23', '2024-11-13', 'ongoing', 'Over 200 artworks displayed.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'karenhagupit', '$2y$10$dGKaP5XoPc7qQSzGXVmO7uM7jlP4OtGDPMhLChhsvrEJXKZ6NDVOS', NULL, '2024-07-12 02:55:45'),
(3, 'Stanley Jamoragan', '$2y$10$ZNMfu6JY7is69uHds3f2zOWlE/Q.3GOONBk5yoHGhT1JJP37Pd9FG', NULL, '2024-07-12 04:30:20'),
(5, 'testuser', '$2y$10$0AC5IA1DesJgIX136djPZu6q1rKmB5ekJHdDIgAnTQlIGNrfbvtv2', NULL, '2024-07-12 04:34:19'),
(6, 'szdf', '$2y$10$Q/temvliruZLPad4Yfhi8.QDAfJuTdfFs7UcBvXjJV9EynwFoFaFO', NULL, '2024-07-13 00:59:37'),
(7, 'Boy Bawang', '$2y$10$NL5DR7c2F8YRePtzVXPrLO63O4priGCTRfSMY82nHOrYEZeI/Pfoe', NULL, '2024-07-13 07:26:31'),
(8, 'russ', '$2y$10$WTOzlt0GOOlsfr8Op1pjqe5ZoD6XMPn7t9JbxSqN73jQIuqq1NGLi', NULL, '2024-07-19 06:21:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
