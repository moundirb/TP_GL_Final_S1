-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2024 at 01:00 AM
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
-- Database: `tp_gl_final`
--

-- --------------------------------------------------------

--
-- Table structure for table `projet`
--

CREATE TABLE `projet` (
  `Id_projet` int(10) NOT NULL,
  `Id_u` int(10) NOT NULL,
  `Nom_projet` varchar(40) NOT NULL,
  `Date_dp` date NOT NULL,
  `Nbr_taches` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projet`
--

INSERT INTO `projet` (`Id_projet`, `Id_u`, `Nom_projet`, `Date_dp`, `Nbr_taches`) VALUES
(27, 5, 'TP', '2024-01-24', 8),
(29, 5, 'TD GL', '2023-10-25', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tache`
--

CREATE TABLE `tache` (
  `Id_tache` int(10) NOT NULL,
  `Id_projet` int(10) NOT NULL,
  `Nom_tache` varchar(40) NOT NULL,
  `Date_dt` date NOT NULL,
  `Date_ft` date NOT NULL,
  `Predecesseur` varchar(40) DEFAULT NULL,
  `Durée` float NOT NULL,
  `DTO` date NOT NULL,
  `FTO` date NOT NULL,
  `DTA` date NOT NULL,
  `FTA` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tache`
--

INSERT INTO `tache` (`Id_tache`, `Id_projet`, `Nom_tache`, `Date_dt`, `Date_ft`, `Predecesseur`, `Durée`, `DTO`, `FTO`, `DTA`, `FTA`) VALUES
(100, 27, 'R', '2024-01-22', '2024-01-24', '', 2, '2024-01-22', '2024-01-24', '2024-01-25', '2024-01-27'),
(101, 27, 'B', '2024-01-22', '2024-01-26', '', 4, '2024-01-22', '2024-01-26', '2024-01-22', '2024-01-26'),
(102, 27, 'F', '2024-01-22', '2024-01-26', 'R,B', 4, '2024-01-26', '2024-01-30', '2024-01-26', '2024-01-30'),
(103, 27, 'G', '2024-01-24', '2024-01-30', 'R', 6, '2024-01-24', '2024-01-30', '2024-01-28', '2024-02-03'),
(105, 27, 'D', '2024-01-24', '2024-01-27', 'R', 3, '2024-01-24', '2024-01-27', '2024-01-27', '2024-01-30'),
(107, 27, 'S', '2024-01-30', '2024-02-03', 'F,D', 4, '2024-01-30', '2024-02-03', '2024-01-30', '2024-02-03'),
(109, 27, 'E', '2024-02-03', '2024-02-08', 'R,G,S', 5, '2024-02-03', '2024-02-08', '2024-02-03', '2024-02-08'),
(111, 29, 'A', '2023-10-25', '2023-10-28', '', 3, '2023-10-25', '2023-10-28', '2023-10-27', '2023-10-30'),
(112, 29, 'H', '2023-10-25', '2023-10-30', '', 5, '2023-10-25', '2023-10-30', '2023-10-25', '2023-10-30'),
(113, 29, 'C', '2024-01-23', '2024-01-28', 'A,H', 5, '2023-10-30', '2023-11-04', '2023-11-02', '2023-11-07'),
(115, 29, 'I', '2024-01-23', '2024-01-31', 'A,H', 8, '2023-10-30', '2023-11-07', '2023-10-30', '2023-11-07'),
(116, 29, 'F', '2024-01-23', '2024-01-25', 'C,I', 2, '2023-11-07', '2023-11-09', '2023-11-07', '2023-11-09');

--
-- Triggers `tache`
--
DELIMITER $$
CREATE TRIGGER `update_task_count` AFTER INSERT ON `tache` FOR EACH ROW BEGIN
    DECLARE project_id INT;

    -- Get the project ID for the newly inserted task
    SET project_id = NEW.Id_projet;

    -- Update the task count in the projet table
    UPDATE projet
    SET Nbr_taches = (SELECT COUNT(*) FROM tache WHERE Id_projet = project_id)
    WHERE Id_projet = project_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `Id_u` int(10) NOT NULL,
  `u_email` varchar(40) NOT NULL,
  `u_pass` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`Id_u`, `u_email`, `u_pass`) VALUES
(5, 'email@1', '0000');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `projet`
--
ALTER TABLE `projet`
  ADD PRIMARY KEY (`Id_projet`),
  ADD KEY `FOREIGN` (`Id_u`);

--
-- Indexes for table `tache`
--
ALTER TABLE `tache`
  ADD PRIMARY KEY (`Id_tache`),
  ADD KEY `fk_key` (`Id_projet`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Id_u`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `projet`
--
ALTER TABLE `projet`
  MODIFY `Id_projet` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tache`
--
ALTER TABLE `tache`
  MODIFY `Id_tache` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `Id_u` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `projet`
--
ALTER TABLE `projet`
  ADD CONSTRAINT `FOREIGN` FOREIGN KEY (`Id_u`) REFERENCES `user` (`Id_u`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tache`
--
ALTER TABLE `tache`
  ADD CONSTRAINT `fk_key` FOREIGN KEY (`Id_projet`) REFERENCES `projet` (`Id_projet`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
