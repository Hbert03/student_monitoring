-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2024 at 01:05 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_monitoring`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `student_id` int(11) NOT NULL,
  `status` enum('IN','OUT','LATE') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `date`, `student_id`, `status`) VALUES
(3, '2024-08-30 00:00:00', 25, 'OUT'),
(4, '2024-08-30 00:00:00', 25, 'OUT'),
(5, '2024-08-30 00:00:00', 25, 'OUT'),
(6, '2024-08-30 00:00:00', 25, 'OUT'),
(7, '2024-08-30 00:00:00', 25, 'OUT'),
(8, '2024-08-30 00:00:00', 25, 'OUT'),
(9, '2024-08-30 00:00:00', 25, 'OUT'),
(10, '2024-08-30 00:00:00', 25, 'OUT'),
(11, '2024-09-01 00:00:00', 25, 'OUT'),
(12, '2024-09-03 00:00:00', 25, ''),
(13, '2024-09-03 00:00:00', 20, ''),
(14, '2024-09-03 00:00:00', 20, ''),
(15, '2024-09-03 00:00:00', 20, 'IN'),
(16, '2024-09-03 00:00:00', 20, 'OUT'),
(17, '2024-09-03 00:00:00', 20, 'OUT'),
(18, '2024-09-03 00:00:00', 20, 'OUT'),
(19, '2024-10-02 00:00:00', 20, 'IN'),
(20, '2024-10-02 00:00:00', 20, 'IN'),
(21, '2024-10-02 00:00:00', 20, 'IN'),
(22, '2024-10-02 07:43:11', 20, 'IN'),
(23, '2024-10-07 07:39:07', 20, 'IN'),
(24, '2024-10-07 19:42:55', 20, 'OUT'),
(25, '2024-10-07 19:49:33', 20, 'OUT'),
(26, '2024-10-07 19:52:25', 20, 'OUT'),
(27, '2024-10-07 19:55:57', 20, 'OUT'),
(28, '2024-10-07 20:08:37', 20, 'OUT'),
(29, '2024-10-07 21:11:05', 26, 'OUT'),
(30, '2024-10-07 22:12:30', 20, 'OUT'),
(31, '2024-10-07 22:13:13', 20, 'OUT'),
(32, '2024-10-08 07:22:39', 20, 'IN'),
(33, '2024-10-08 20:29:14', 20, 'OUT'),
(34, '2024-10-08 21:48:38', 20, 'OUT'),
(35, '2024-10-09 07:22:58', 20, 'IN');

-- --------------------------------------------------------

--
-- Table structure for table `class_schedule`
--

CREATE TABLE `class_schedule` (
  `class_schedule_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `class_schedule`
--

INSERT INTO `class_schedule` (`class_schedule_id`, `subject_id`, `teacher_id`, `section_id`, `school_year_id`) VALUES
(3, 3, 20, 4, 6),
(4, 5, 21, 3, 5);

-- --------------------------------------------------------

--
-- Table structure for table `grade_level`
--

CREATE TABLE `grade_level` (
  `grade_level` int(11) NOT NULL,
  `grade_level_name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `grade_level`
--

INSERT INTO `grade_level` (`grade_level`, `grade_level_name`) VALUES
(1, 'Grade 1'),
(2, 'Grade 2'),
(3, 'Grade 3'),
(4, 'Grade 4'),
(5, 'Grade 5'),
(6, 'Grade 6'),
(7, 'Grade 7'),
(8, 'Grade 8'),
(9, 'Grade 9'),
(10, 'Grade 10'),
(11, 'Grade 11'),
(12, 'Grade 12');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `attendance_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `parent`
--

CREATE TABLE `parent` (
  `parent_id` int(11) NOT NULL,
  `parent_name` varchar(45) NOT NULL,
  `parent_mobile` varchar(45) NOT NULL,
  `email` varchar(55) NOT NULL,
  `parent_address` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `parent`
--

INSERT INTO `parent` (`parent_id`, `parent_name`, `parent_mobile`, `email`, `parent_address`) VALUES
(26, 'Hermis B. Limot', '09061266937', '', 'Lantungan'),
(27, 'Hermis B. Limot', '09061266937', '', 'Lantungan'),
(28, 'Lucia L. Limot', '09061266937', '', 'Lantungan'),
(29, 'Lucia L. Limot', '09061266937', '', 'Lantungan'),
(30, 'Lucia L. Limot', '09061266937', '', 'Lantungan'),
(31, 'Hermis B. Limot', '09061266937', '', 'Lantungan'),
(32, 'Karl Francis Genovio', '09700462138', '', 'Labuyo, Tangub City');

-- --------------------------------------------------------

--
-- Table structure for table `school_year`
--

CREATE TABLE `school_year` (
  `school_year_id` int(11) NOT NULL,
  `school_year_name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `school_year`
--

INSERT INTO `school_year` (`school_year_id`, `school_year_name`) VALUES
(4, '2022-2023'),
(5, '2023-2024'),
(6, '2024-2025');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `section_id` int(11) NOT NULL,
  `section_name` varchar(45) NOT NULL,
  `grade_level_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`section_id`, `section_name`, `grade_level_id`) VALUES
(3, 'Rizal', 1),
(4, 'Bonifacio', 2),
(5, 'Pilar', 3);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `student_firstname` varchar(45) NOT NULL,
  `student_middlename` varchar(45) NOT NULL,
  `student_lastname` varchar(45) NOT NULL,
  `student_mobile` varchar(45) NOT NULL,
  `student_address` varchar(45) NOT NULL,
  `student_status` varchar(45) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `grade_level_id` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `student_firstname`, `student_middlename`, `student_lastname`, `student_mobile`, `student_address`, `student_status`, `parent_id`, `grade_level_id`, `date`) VALUES
(20, 'Herbert', 'Lim', 'Limot', '09061266937', 'Lantungan', 'single', 26, 3, '2024-08-04 08:07:37'),
(22, 'Harees', 'Lim', 'Limot', '', 'Lantungan', 'single', 28, 1, '2024-08-04 12:04:58'),
(23, 'Haidee', 'Lim', 'Limot', '', 'Lantungan', 'single', 29, 3, '2024-08-04 12:05:10'),
(24, 'Honey Liz', 'Lim', 'Limot', '', 'Lantungan', 'single', 30, 2, '2024-08-04 12:05:23'),
(25, 'Herb', 'Lim', 'Limot', '09061266937', 'Lantungan', 'single', 31, 1, '2024-08-30 18:12:51'),
(26, 'Karl', 'Sasdas', 'Genovio', '', 'Labuyo, Tangub City', 'single', 32, 12, '2024-10-07 20:07:47');

-- --------------------------------------------------------

--
-- Table structure for table `student_section`
--

CREATE TABLE `student_section` (
  `student_section_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student_section`
--

INSERT INTO `student_section` (`student_section_id`, `section_id`, `school_year_id`, `student_id`) VALUES
(3, 4, 6, 20),
(4, 3, 5, 20),
(5, 3, 6, 24),
(6, 5, 4, 24),
(7, 4, 6, 26);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `subject_name`) VALUES
(3, 'Math'),
(4, 'Science'),
(5, 'Mathematics'),
(6, 'English'),
(7, 'Filipino'),
(8, 'EPP');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `teacher_firstname` varchar(45) NOT NULL,
  `teacher_middlename` varchar(55) NOT NULL,
  `teacher_lastname` varchar(55) NOT NULL,
  `teacher_address` varchar(45) NOT NULL,
  `teacher_mobile` varchar(45) NOT NULL,
  `teacher_status` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `teacher_firstname`, `teacher_middlename`, `teacher_lastname`, `teacher_address`, `teacher_mobile`, `teacher_status`) VALUES
(20, 'Lucia', 'Lim', 'Limot', 'Lantungan', '09061266937', 'married'),
(21, 'Honey Lee', 'Lim', 'Limot', 'Lantungan', '09061266937', 'married'),
(24, 'Herbert', 'Lim', 'Limot', 'Lantungan', '09061266937', 'married');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `teacher_id`, `created_at`) VALUES
(29, 'admin@bnhs', '$2y$10$1CAQwU4eXwO82HfObLdoZ.pkpDJri4o.RgODyyg.tgcztMeili/Ca', 'Admin', NULL, '2024-08-04 01:06:45'),
(30, 'lucia.limot@bnhs.gov.ph', '$2y$10$9qQOvjIUeBaV3Cd4FNw2ZuwzVuZlYQZ5oYsYa6RyNs7JaCxRf9L2C', '1', 20, '2024-08-04 01:08:22'),
(31, 'honey lee.limot@bnhs.gov.ph', '$2y$10$BAHxn6Z0k7rG9NGid.ohj.jlpCwR3vu7gqwhypPVshSXeDURjcR3C', '1', 21, '2024-08-04 05:06:38'),
(34, 'herbert.limot@bnhs.gov.ph', '$2y$10$t.fBUlRoQU8W9hIGt0fHLOg9U3JznS2EtEGU0l7OXwPR04EacDkQe', '1', 24, '2024-10-20 06:10:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `class_schedule`
--
ALTER TABLE `class_schedule`
  ADD PRIMARY KEY (`class_schedule_id`),
  ADD KEY `school_year_id` (`school_year_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `grade_level`
--
ALTER TABLE `grade_level`
  ADD PRIMARY KEY (`grade_level`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `attendance_id` (`attendance_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `parent`
--
ALTER TABLE `parent`
  ADD PRIMARY KEY (`parent_id`);

--
-- Indexes for table `school_year`
--
ALTER TABLE `school_year`
  ADD PRIMARY KEY (`school_year_id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `grade_level_id` (`grade_level_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `grade_level_id` (`grade_level_id`);

--
-- Indexes for table `student_section`
--
ALTER TABLE `student_section`
  ADD PRIMARY KEY (`student_section_id`),
  ADD KEY `school_year_id` (`school_year_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `class_schedule`
--
ALTER TABLE `class_schedule`
  MODIFY `class_schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `grade_level`
--
ALTER TABLE `grade_level`
  MODIFY `grade_level` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parent`
--
ALTER TABLE `parent`
  MODIFY `parent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `school_year`
--
ALTER TABLE `school_year`
  MODIFY `school_year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `student_section`
--
ALTER TABLE `student_section`
  MODIFY `student_section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);

--
-- Constraints for table `class_schedule`
--
ALTER TABLE `class_schedule`
  ADD CONSTRAINT `class_schedule_ibfk_1` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`school_year_id`),
  ADD CONSTRAINT `class_schedule_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `section` (`section_id`),
  ADD CONSTRAINT `class_schedule_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`),
  ADD CONSTRAINT `class_schedule_ibfk_4` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`attendance_id`) REFERENCES `attendance` (`attendance_id`),
  ADD CONSTRAINT `notification_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `parent` (`parent_id`);

--
-- Constraints for table `section`
--
ALTER TABLE `section`
  ADD CONSTRAINT `section_ibfk_1` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_level` (`grade_level`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parent` (`parent_id`),
  ADD CONSTRAINT `student_ibfk_2` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_level` (`grade_level`);

--
-- Constraints for table `student_section`
--
ALTER TABLE `student_section`
  ADD CONSTRAINT `student_section_ibfk_1` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`school_year_id`),
  ADD CONSTRAINT `student_section_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `section` (`section_id`),
  ADD CONSTRAINT `student_section_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
