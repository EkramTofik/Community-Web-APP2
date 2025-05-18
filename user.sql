-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2025 at 11:28 AM
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
-- Database: `community web app`
--

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('student','admin','faculty') DEFAULT NULL,
  `Department` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Id` int(11) NOT NULL,
  `reset_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserName`, `Email`, `Password`, `Role`, `Department`, `created_at`, `Id`, `reset_code`) VALUES
('bruk', 'bruk45@gmail.com', '$2y$10$.kld9EKgXYxguMZW3CZfFuJFOq0aGvkqY868hk.lm7UGp27XMX.Du', 'faculty', 'software engineering', '2025-05-13 06:01:33', 7, NULL),
('ekram', 'ekru37@gmail.com', '$2y$10$dnWnwJmKsjnV5JTwTyN.k.m8grzpk0W8o7nA3TEh6n0MZ7en1OUb.', 'student', 'software engineering', '2025-05-13 06:28:50', 14, NULL),
('Ekru', 'ekru6482@gmail.com', '$2y$10$MmNoKmXNWey7qm7iPn5cHOQQiPJb5nqdL/q6nMDElqMxs.FVtHPxy', 'student', 'software engineering', '2025-05-15 04:38:55', 17, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
