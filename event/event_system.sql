-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 30, 2025 at 08:20 AM
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
  `fee` decimal(10,2) DEFAULT NULL,
  `payment_details` varchar(200) DEFAULT NULL,
  `event_scope` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `rules_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `organizer_id`, `event_name`, `event_date`, `event_time`, `venue`, `fee`, `payment_details`, `event_scope`, `department`, `rules_file`) VALUES
(1, 1, 'new year ', '2026-01-02', '10:00:00', 'Auditorium', 100.00, '1234567890', 'college', 'ALL', NULL),
(2, 1, 'pongal', '2026-12-01', '10:00:00', 'Auditorium', 0.00, 'FREE EVENT', 'department', 'Maths', '1767013300_fuel.docx');

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
(1, 1, 'Cricket'),
(2, 1, 'vollyball'),
(11, 2, 'Cricket'),
(12, 2, 'hockey'),
(13, 2, 'gogo'),
(14, 2, 'kabadi');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `notify_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `event_id`, `message`, `notify_date`) VALUES
(1, 1, 'this event is closed in today 9:00 pm', '2025-12-29'),
(2, 2, 'this end with today', '2025-12-30');

-- --------------------------------------------------------

--
-- Table structure for table `organizer`
--

CREATE TABLE `organizer` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizer`
--

INSERT INTO `organizer` (`id`, `name`, `email`, `phone`, `username`, `password`) VALUES
(1, 'staff', 'staff@gmail.com', '9876543210', 'staff1', '1234');

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
(1, 36, 1, 'Cricket', '1234567890', '1234567899', 'stu@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `roll_no` varchar(20) DEFAULT NULL,
  `degree` varchar(20) DEFAULT NULL,
  `branch` varchar(20) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `name`, `roll_no`, `degree`, `branch`, `year`, `username`, `password`) VALUES
(1, 'Student1', '231IT001', 'Msc', 'IT', 2023, '231IT001', 'std@123'),
(2, 'Student2', '231IT002', 'Msc', 'IT', 2023, '231IT002', 'std@123'),
(3, 'Student3', '231IT003', 'Msc', 'IT', 2023, '231IT003', 'std@123'),
(4, 'Student4', '231IT004', 'Msc', 'IT', 2023, '231IT004', 'std@123'),
(5, 'Student5', '231IT005', 'Msc', 'IT', 2023, '231IT005', 'std@123'),
(6, 'Student6', '231IT006', 'Msc', 'IT', 2023, '231IT006', 'std@123'),
(7, 'Student7', '231IT007', 'Msc', 'IT', 2023, '231IT007', 'std@123'),
(8, 'Student8', '231IT008', 'Msc', 'IT', 2023, '231IT008', 'std@123'),
(9, 'Student9', '231IT009', 'Msc', 'IT', 2023, '231IT009', 'std@123'),
(10, 'Student10', '231IT010', 'Msc', 'IT', 2023, '231IT010', 'std@123'),
(11, 'Student11', '231IT011', 'Msc', 'IT', 2023, '231IT011', 'std@123'),
(12, 'Student12', '231IT012', 'Msc', 'IT', 2023, '231IT012', 'std@123'),
(13, 'Student13', '231IT013', 'Msc', 'IT', 2023, '231IT013', 'std@123'),
(14, 'Student14', '231IT014', 'Msc', 'IT', 2023, '231IT014', 'std@123'),
(15, 'Student15', '231IT015', 'Msc', 'IT', 2023, '231IT015', 'std@123'),
(16, 'Student16', '231IT016', 'Msc', 'IT', 2023, '231IT016', 'std@123'),
(17, 'Student17', '231IT017', 'Msc', 'IT', 2023, '231IT017', 'std@123'),
(18, 'Student18', '231IT018', 'Msc', 'IT', 2023, '231IT018', 'std@123'),
(19, 'Student19', '231IT019', 'Msc', 'IT', 2023, '231IT019', 'std@123'),
(20, 'Student20', '231IT020', 'Msc', 'IT', 2023, '231IT020', 'std@123'),
(21, 'Student21', '231IT021', 'Msc', 'IT', 2023, '231IT021', 'std@123'),
(22, 'Student22', '231IT022', 'Msc', 'IT', 2023, '231IT022', 'std@123'),
(23, 'Student23', '231IT023', 'Msc', 'IT', 2023, '231IT023', 'std@123'),
(24, 'Student24', '231IT024', 'Msc', 'IT', 2023, '231IT024', 'std@123'),
(25, 'Student25', '231IT025', 'Msc', 'IT', 2023, '231IT025', 'std@123'),
(26, 'Student26', '231IT026', 'Msc', 'IT', 2023, '231IT026', 'std@123'),
(27, 'Student27', '231IT027', 'Msc', 'IT', 2023, '231IT027', 'std@123'),
(28, 'Student28', '231IT028', 'Msc', 'IT', 2023, '231IT028', 'std@123'),
(29, 'Student29', '231IT029', 'Msc', 'IT', 2023, '231IT029', 'std@123'),
(30, 'Student30', '231IT030', 'Msc', 'IT', 2023, '231IT030', 'std@123'),
(31, 'Student31', '231IT031', 'Msc', 'IT', 2023, '231IT031', 'std@123'),
(32, 'Student32', '231IT032', 'Msc', 'IT', 2023, '231IT032', 'std@123'),
(33, 'Student33', '231IT033', 'Msc', 'IT', 2023, '231IT033', 'std@123'),
(34, 'Student34', '231IT034', 'Msc', 'IT', 2023, '231IT034', 'std@123'),
(35, 'Student35', '231IT035', 'Msc', 'IT', 2023, '231IT035', 'std@123'),
(36, 'Student1', '231CS001', 'Msc', 'cs', 2024, '231CS001', 'std@123'),
(37, 'Student2', '231CS002', 'Msc', 'cs', 2024, '231CS002', 'std@123'),
(38, 'Student3', '231CS003', 'Msc', 'cs', 2024, '231CS003', 'std@123'),
(39, 'Student4', '231CS004', 'Msc', 'cs', 2024, '231CS004', 'std@123'),
(40, 'Student5', '231CS005', 'Msc', 'cs', 2024, '231CS005', 'std@123'),
(41, 'Student6', '231CS006', 'Msc', 'cs', 2024, '231CS006', 'std@123'),
(42, 'Student7', '231CS007', 'Msc', 'cs', 2024, '231CS007', 'std@123'),
(43, 'Student8', '231CS008', 'Msc', 'cs', 2024, '231CS008', 'std@123'),
(44, 'Student9', '231CS009', 'Msc', 'cs', 2024, '231CS009', 'std@123'),
(45, 'Student10', '231CS010', 'Msc', 'cs', 2024, '231CS010', 'std@123'),
(46, 'Student1', '231CT001', 'Msc', 'CT', 2024, '231CT001', 'std@123'),
(47, 'Student2', '231CT002', 'Msc', 'CT', 2024, '231CT002', 'std@123'),
(48, 'Student3', '231CT003', 'Msc', 'CT', 2024, '231CT003', 'std@123'),
(49, 'Student4', '231CT004', 'Msc', 'CT', 2024, '231CT004', 'std@123'),
(50, 'Student5', '231CT005', 'Msc', 'CT', 2024, '231CT005', 'std@123'),
(51, 'Student6', '231CT006', 'Msc', 'CT', 2024, '231CT006', 'std@123'),
(52, 'Student7', '231CT007', 'Msc', 'CT', 2024, '231CT007', 'std@123'),
(53, 'Student8', '231CT008', 'Msc', 'CT', 2024, '231CT008', 'std@123'),
(54, 'Student9', '231CT009', 'Msc', 'CT', 2024, '231CT009', 'std@123'),
(55, 'Student10', '231CT010', 'Msc', 'CT', 2024, '231CT010', 'std@123'),
(56, 'Student11', '231CT011', 'Msc', 'CT', 2024, '231CT011', 'std@123'),
(57, 'Student12', '231CT012', 'Msc', 'CT', 2024, '231CT012', 'std@123'),
(58, 'Student13', '231CT013', 'Msc', 'CT', 2024, '231CT013', 'std@123'),
(59, 'Student14', '231CT014', 'Msc', 'CT', 2024, '231CT014', 'std@123'),
(60, 'Student15', '231CT015', 'Msc', 'CT', 2024, '231CT015', 'std@123'),
(66, 'Student1', '231MATHS001', 'Bsc', 'MATHS', 2023, '231MATHS001', 'std@123'),
(67, 'Student2', '231MATHS002', 'Bsc', 'MATHS', 2023, '231MATHS002', 'std@123'),
(68, 'Student3', '231MATHS003', 'Bsc', 'MATHS', 2023, '231MATHS003', 'std@123'),
(69, 'Student4', '231MATHS004', 'Bsc', 'MATHS', 2023, '231MATHS004', 'std@123'),
(70, 'Student5', '231MATHS005', 'Bsc', 'MATHS', 2023, '231MATHS005', 'std@123');

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `organizer`
--
ALTER TABLE `organizer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

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
