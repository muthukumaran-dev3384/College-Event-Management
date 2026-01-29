-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 28, 2026 at 01:28 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin@123');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `organizer_id` int(11) DEFAULT NULL,
  `event_name` varchar(100) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_details` varchar(200) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `rules_file` varchar(255) DEFAULT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `organizer_id`, `event_name`, `event_date`, `event_time`, `venue`, `fee`, `payment_details`, `department`, `rules_file`, `status`) VALUES
(6, 7, 'new', '2026-01-23', '10:00:00', 'Auditorium', 0.00, 'FREE EVENT', 'cs', '', 'open'),
(7, 8, 'pongal', '2026-01-23', '10:00:00', 'Auditorium', 100.00, '1234567890', 'it', '', 'open');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `game_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `event_id`, `game_name`) VALUES
(19, 2, 'Cricket'),
(20, 2, 'hockey'),
(21, 2, 'gogo'),
(22, 2, 'kabadi'),
(23, 3, 'Cricket'),
(24, 3, 'hockey'),
(25, 1, 'Cricket'),
(26, 1, 'vollyball'),
(31, 6, 'Cricket'),
(32, 6, 'hockey'),
(33, 7, 'Cricket'),
(34, 7, 'hockey');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `event_name` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `notify_date` date NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `event_id` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `event_name`, `message`, `notify_date`, `created_at`, `event_id`) VALUES
(1, '', 'hi', '2026-01-26', '2026-01-21 14:09:39', NULL),
(2, '', 'hi', '2026-01-26', '2026-01-21 16:08:08', '5'),
(3, '', 'hi', '2026-01-26', '2026-01-21 16:49:08', '5'),
(4, '', 'this end soon', '2026-01-22', '2026-01-22 18:07:36', '7'),
(5, '', 'hi', '2026-01-22', '2026-01-22 18:27:18', '6');

-- --------------------------------------------------------

--
-- Table structure for table `organizer`
--

CREATE TABLE `organizer` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `department` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizer`
--

INSERT INTO `organizer` (`id`, `username`, `password`, `department`) VALUES
(7, 'cs', '1234', 'cs'),
(8, 'it', '1234', 'it');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `games_selected` varchar(200) DEFAULT NULL,
  `payment_ref` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `student_id`, `event_id`, `games_selected`, `payment_ref`, `phone`, `email`) VALUES
(17, 2, 7, 'Cricket', 'PAY_2_7_1769339384.jpg', '1234567899', 'new@gmail.com'),
(18, 4, 7, 'hockey', 'PAY_4_7_1769339774.jpg', '1234567899', 'new@gmail.com'),
(19, 49, 6, 'Cricket', '', '9876543210', 'admin@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `serial_no` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `roll_no` varchar(50) NOT NULL,
  `degree` varchar(50) DEFAULT NULL,
  `branch` varchar(10) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `serial_no`, `name`, `roll_no`, `degree`, `branch`, `year`, `username`, `password`) VALUES
(1, 1, 'student1', '231IT001', 'MSC', 'it', '2023', '231IT001', 'std@123'),
(2, 2, 'student2', '231IT002', 'MSC', 'it', '2023', '231IT002', 'std@123'),
(3, 3, 'student3', '231IT003', 'MSC', 'it', '2023', '231IT003', 'std@123'),
(4, 4, 'student4', '231IT004', 'MSC', 'it', '2023', '231IT004', 'std@123'),
(49, 5, 'Student1', '231CS001', 'Msc', 'CS', '2023', '231CS001', 'std@123'),
(50, 6, 'Student2', '231CS002', 'Msc', 'CS', '2023', '231CS002', 'std@123'),
(51, 7, 'Student3', '231CS003', 'Msc', 'CS', '2023', '231CS003', 'std@123'),
(52, 8, 'Student4', '231CS004', 'Msc', 'CS', '2023', '231CS004', 'std@123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `organizer`
--
ALTER TABLE `organizer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roll_no` (`roll_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `organizer`
--
ALTER TABLE `organizer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `organizer` (`id`);

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`),
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
