-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2026 at 04:44 PM
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
-- Database: `finalproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `activityDate` date NOT NULL,
  `name` varchar(255) NOT NULL,
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `equipment` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `userID`, `activityDate`, `name`, `startTime`, `endTime`, `location`, `equipment`, `notes`, `created_at`) VALUES
(80, 7, '2026-03-12', 'test', '09:00:00', '17:00:00', 'test', 'test', 'test', '2026-03-10 21:55:13'),
(82, 7, '2026-03-20', 'Conditioned Staff Test', '09:00:00', '17:00:00', 'house', 'boat', 'just testing', '2026-03-11 13:42:12');

-- --------------------------------------------------------

--
-- Table structure for table `activityassignments`
--

CREATE TABLE `activityassignments` (
  `id` int(11) NOT NULL,
  `activityID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('assigned','confirmed','cancelled') DEFAULT 'assigned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activityassignments`
--

INSERT INTO `activityassignments` (`id`, `activityID`, `userID`, `assigned_at`, `status`) VALUES
(49, 80, 4, '2026-03-10 21:55:13', 'assigned');

-- --------------------------------------------------------

--
-- Table structure for table `conditions`
--

CREATE TABLE `conditions` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `conditionDate` date NOT NULL,
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conditions`
--

INSERT INTO `conditions` (`id`, `userID`, `conditionDate`, `startTime`, `endTime`, `reason`, `created_at`) VALUES
(1, 8, '2026-03-20', '09:00:00', '12:00:00', 'Morning only', '2026-03-10 21:46:30'),
(2, 9, '2026-03-21', '13:00:00', '17:00:00', 'Afternoon only', '2026-03-10 21:46:30'),
(3, 10, '2026-03-22', '10:00:00', '14:00:00', 'Limited hours', '2026-03-10 21:46:30'),
(4, 23, '2026-03-16', '13:00:00', '17:00:00', 'Part-time - afternoons only', '2026-03-10 23:06:31'),
(5, 24, '2026-03-17', '09:00:00', '13:00:00', 'Part-time - mornings only', '2026-03-10 23:06:31'),
(6, 25, '2026-03-18', '14:00:00', '18:00:00', 'Part-time - late shift', '2026-03-10 23:06:31'),
(7, 26, '2026-03-19', '08:00:00', '12:00:00', 'Part-time - early shift', '2026-03-10 23:06:31');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `roleID` int(11) NOT NULL,
  `roleName` varchar(30) NOT NULL,
  `roleDescription` varchar(255) NOT NULL,
  `colour` varchar(7) DEFAULT '#1c0696'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`roleID`, `roleName`, `roleDescription`, `colour`) VALUES
(1, 'admin', 'Full access to all features, can manage users and schedules', '#7b2eda'),
(2, 'manager', 'Can manage staff schedules and approve requests', '#dc3545'),
(3, 'staff', 'Limited access, can view personal schedule and submit requests', '#0066cc'),
(4, 'Senior Staff', 'Experienced staff members with additional responsibilities', '#ff6b00'),
(5, 'Part-Time Staff', 'Staff working reduced hours', '#ff1493');

-- --------------------------------------------------------

--
-- Table structure for table `unavailabledates`
--

CREATE TABLE `unavailabledates` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `unavailableDate` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unavailabledates`
--

INSERT INTO `unavailabledates` (`id`, `userID`, `unavailableDate`, `created_at`) VALUES
(127, 8, '2026-03-10', '2026-03-10 21:46:30'),
(128, 9, '2026-03-11', '2026-03-10 21:46:30'),
(129, 10, '2026-03-12', '2026-03-10 21:46:30'),
(130, 11, '2026-03-13', '2026-03-10 21:46:30'),
(131, 12, '2026-03-14', '2026-03-10 21:46:30'),
(132, 13, '2026-03-15', '2026-03-10 21:46:30'),
(133, 14, '2026-03-16', '2026-03-10 21:46:30'),
(134, 15, '2026-03-17', '2026-03-10 21:46:30'),
(135, 16, '2026-03-18', '2026-03-10 21:46:30'),
(136, 17, '2026-03-19', '2026-03-10 21:46:30'),
(137, 19, '2026-03-02', '2026-03-10 23:07:24'),
(138, 19, '2026-03-07', '2026-03-10 23:07:24'),
(139, 19, '2026-03-12', '2026-03-10 23:07:24'),
(140, 19, '2026-03-18', '2026-03-10 23:07:24'),
(141, 19, '2026-03-25', '2026-03-10 23:07:24'),
(142, 20, '2026-03-05', '2026-03-10 23:07:24'),
(143, 20, '2026-03-11', '2026-03-10 23:07:24'),
(144, 20, '2026-03-19', '2026-03-10 23:07:24'),
(145, 20, '2026-03-28', '2026-03-10 23:07:24'),
(146, 21, '2026-03-01', '2026-03-10 23:07:24'),
(147, 21, '2026-03-08', '2026-03-10 23:07:24'),
(148, 21, '2026-03-14', '2026-03-10 23:07:24'),
(149, 21, '2026-03-20', '2026-03-10 23:07:24'),
(150, 21, '2026-03-26', '2026-03-10 23:07:24'),
(151, 21, '2026-03-31', '2026-03-10 23:07:24'),
(152, 22, '2026-03-09', '2026-03-10 23:07:24'),
(153, 22, '2026-03-16', '2026-03-10 23:07:24'),
(154, 22, '2026-03-23', '2026-03-10 23:07:24'),
(155, 23, '2026-03-03', '2026-03-10 23:07:24'),
(156, 23, '2026-03-10', '2026-03-10 23:07:24'),
(157, 23, '2026-03-17', '2026-03-10 23:07:24'),
(158, 23, '2026-03-24', '2026-03-10 23:07:24'),
(159, 23, '2026-03-29', '2026-03-10 23:07:24'),
(160, 24, '2026-03-04', '2026-03-10 23:07:24'),
(161, 24, '2026-03-13', '2026-03-10 23:07:24'),
(162, 24, '2026-03-21', '2026-03-10 23:07:24'),
(163, 24, '2026-03-27', '2026-03-10 23:07:24'),
(164, 25, '2026-03-06', '2026-03-10 23:07:24'),
(165, 25, '2026-03-15', '2026-03-10 23:07:24'),
(166, 25, '2026-03-22', '2026-03-10 23:07:24'),
(167, 25, '2026-03-29', '2026-03-10 23:07:24'),
(168, 25, '2026-03-30', '2026-03-10 23:07:24'),
(169, 26, '2026-03-02', '2026-03-10 23:07:24'),
(170, 26, '2026-03-14', '2026-03-10 23:07:24'),
(171, 26, '2026-03-26', '2026-03-10 23:07:24');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstName` varchar(30) NOT NULL,
  `lastName` varchar(30) NOT NULL,
  `roleID` int(11) NOT NULL,
  `status` enum('pending','active','disabled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `email`, `password`, `firstName`, `lastName`, `roleID`, `status`) VALUES
(4, 'test@example.com', '$2y$10$ofglMsVOOAKRMRoktIHZruRjEBIlOIxVpsqtgjLeyhASnM1.9h/nG', 'Test', 'Example', 3, 'active'),
(5, 'shwethonediri@gmail.com', '$2y$10$l3va4KJzEMYgqQhh8552qOljbkOOonbPQRDJOdZOuHi3uc2.pjQvC', 'Shwe', 'Diri', 3, 'pending'),
(7, 'a@a.com', '$2y$10$ZU0zW.GpQ4cVE5wemMx6EegYzQBx5qdyKfbm6cARSl9caqgbYzdlu', 'Ronnie', 'Finlayson', 1, 'active'),
(8, 'staff1@test.com', 'dummyhash', 'John', 'Smith', 3, 'active'),
(9, 'staff2@test.com', 'dummyhash', 'Sarah', 'Johnson', 3, 'active'),
(10, 'staff3@test.com', 'dummyhash', 'Michael', 'Williams', 3, 'active'),
(11, 'staff4@test.com', 'dummyhash', 'Emma', 'Brown', 3, 'active'),
(12, 'staff5@test.com', 'dummyhash', 'James', 'Jones', 3, 'active'),
(13, 'staff6@test.com', 'dummyhash', 'Maria', 'Garcia', 3, 'active'),
(14, 'staff7@test.com', 'dummyhash', 'David', 'Miller', 3, 'active'),
(15, 'staff8@test.com', 'dummyhash', 'Lisa', 'Davis', 3, 'active'),
(16, 'staff9@test.com', 'dummyhash', 'Robert', 'Wilson', 3, 'active'),
(17, 'staff10@test.com', 'dummyhash', 'Jennifer', 'Taylor', 3, 'active'),
(18, 'shinaye14@gmail.com', '$2y$10$yuMlEmx3ocpM5.QSX6KWz.oSJ1O4qIMLg30DoC./uMYNMLXnMMgxu', 'Shinaye', 'Aung', 3, 'active'),
(19, 'robert.senior@test.com', 'dummyhash', 'Robert', 'Chen', 4, 'active'),
(20, 'patricia.senior@test.com', 'dummyhash', 'Patricia', 'Kumar', 4, 'active'),
(21, 'james.senior@test.com', 'dummyhash', 'James', 'O\'Brien', 4, 'active'),
(22, 'elizabeth.senior@test.com', 'dummyhash', 'Elizabeth', 'Wong', 4, 'active'),
(23, 'chris.parttime@test.com', 'dummyhash', 'Chris', 'Martinez', 5, 'active'),
(24, 'amanda.parttime@test.com', 'dummyhash', 'Amanda', 'Taylor', 5, 'active'),
(25, 'kevin.parttime@test.com', 'dummyhash', 'Kevin', 'Patel', 5, 'active'),
(26, 'nina.parttime@test.com', 'dummyhash', 'Nina', 'Williams', 5, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `activityassignments`
--
ALTER TABLE `activityassignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment` (`activityID`,`userID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `conditions`
--
ALTER TABLE `conditions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`userID`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`roleID`);

--
-- Indexes for table `unavailabledates`
--
ALTER TABLE `unavailabledates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_date` (`userID`,`unavailableDate`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`),
  ADD KEY `roleID` (`roleID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `activityassignments`
--
ALTER TABLE `activityassignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `conditions`
--
ALTER TABLE `conditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `roleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `unavailabledates`
--
ALTER TABLE `unavailabledates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`);

--
-- Constraints for table `activityassignments`
--
ALTER TABLE `activityassignments`
  ADD CONSTRAINT `activityassignments_ibfk_1` FOREIGN KEY (`activityID`) REFERENCES `activities` (`id`),
  ADD CONSTRAINT `activityassignments_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`);

--
-- Constraints for table `conditions`
--
ALTER TABLE `conditions`
  ADD CONSTRAINT `conditions_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`);

--
-- Constraints for table `unavailabledates`
--
ALTER TABLE `unavailabledates`
  ADD CONSTRAINT `unavailabledates_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`roleID`) REFERENCES `roles` (`roleID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
