-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2024 at 02:22 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zoom_participants`
--

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `id` int(11) NOT NULL,
  `meeting_id` varchar(50) NOT NULL,
  `participant_id` varchar(50) NOT NULL,
  `participant_name` varchar(255) DEFAULT NULL,
  `status` enum('joined','left') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`id`, `meeting_id`, `participant_id`, `participant_name`, `status`, `timestamp`) VALUES
(1, '78788954365', 'lien9sCyQ_ipnGl2N-aGSw', 'Rene Sarsalejo', 'joined', '2024-08-15 04:31:18'),
(2, '78788954365', '', 'Amielyn', 'joined', '2024-08-15 04:28:23'),
(3, '78788954367', '', 'Amielyn2', 'joined', '2024-08-15 04:28:23'),
(4, '78788954368', '', 'Amielyn3', 'joined', '2024-08-15 04:28:23'),
(5, '78788954360', 'aGSw', 'Rene Sarsalejo2', 'joined', '2024-08-15 04:31:18'),
(6, '78788954362', '', 'Rene', 'joined', '2024-08-15 04:28:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
