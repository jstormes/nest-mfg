-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mariadb
-- Generation Time: Mar 27, 2025 at 06:05 PM
-- Server version: 11.6.2-MariaDB-ubu2404
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Jobs`
--
CREATE DATABASE IF NOT EXISTS `Jobs` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `Jobs`;

-- --------------------------------------------------------

--
-- Table structure for table `Job`
--

CREATE TABLE IF NOT EXISTS `Job` (
  `JobId` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `FirstJobStepId` bigint(20) NOT NULL,
  PRIMARY KEY (`JobId`),
  KEY `JobStepIdFK` (`FirstJobStepId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `JobStatus`
--

CREATE TABLE IF NOT EXISTS `JobStatus` (
  `JobStatusId` int(11) NOT NULL AUTO_INCREMENT,
  `Status` varchar(15) NOT NULL,
  `Color` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`JobStatusId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `JobStatus`
--

INSERT INTO `JobStatus` (`JobStatusId`, `Status`, `Color`) VALUES
(1, 'Not Started', NULL),
(2, 'Started', NULL),
(3, 'Paused', NULL),
(4, 'Problem', NULL),
(5, 'Finished', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `JobStep`
--

CREATE TABLE IF NOT EXISTS `JobStep` (
  `JobStepId` bigint(20) NOT NULL AUTO_INCREMENT,
  `Priority` int(11) NOT NULL,
  `Name` varchar(25) NOT NULL,
  `Status` int(11) DEFAULT NULL,
  PRIMARY KEY (`JobStepId`),
  KEY `StatusFK` (`Status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `JobStepCost`
--

CREATE TABLE IF NOT EXISTS `JobStepCost` (
  `JobStepCostId` bigint(20) NOT NULL AUTO_INCREMENT,
  `JobStepId` bigint(20) NOT NULL,
  `JobStepCostTypeId` int(11) NOT NULL,
  `Cost` decimal(6,0) NOT NULL,
  PRIMARY KEY (`JobStepCostId`),
  KEY `StepPK` (`JobStepId`),
  KEY `JobStepCostTypeFK` (`JobStepCostTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `JobStepCostType`
--

CREATE TABLE IF NOT EXISTS `JobStepCostType` (
  `JobStepCostType` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(25) NOT NULL,
  `CostCode` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`JobStepCostType`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `JobStepCostType`
--

INSERT INTO `JobStepCostType` (`JobStepCostType`, `Name`, `CostCode`) VALUES
(1, 'Material Cost', NULL),
(2, 'Labor Cost', NULL),
(3, 'Misc Cost', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `JobStepRelationship`
--

CREATE TABLE IF NOT EXISTS `JobStepRelationship` (
  `JobStepRelationshipId` bigint(20) NOT NULL AUTO_INCREMENT,
  `ParrentJobStepId` bigint(20) NOT NULL,
  `ChildJobStepId` bigint(20) NOT NULL,
  PRIMARY KEY (`JobStepRelationshipId`),
  KEY `ParentFK` (`ParrentJobStepId`),
  KEY `ChildFK` (`ChildJobStepId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `JobTemplate`
--

CREATE TABLE IF NOT EXISTS `JobTemplate` (
  `TemplateId` bigint(20) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Version` int(11) NOT NULL,
  `FirstJobStepId` bigint(20) NOT NULL,
  PRIMARY KEY (`TemplateId`),
  KEY `FirstStepFK` (`FirstJobStepId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Job`
--
ALTER TABLE `Job`
  ADD CONSTRAINT `JobStepIdFK` FOREIGN KEY (`FirstJobStepId`) REFERENCES `JobStep` (`JobStepId`);

--
-- Constraints for table `JobStep`
--
ALTER TABLE `JobStep`
  ADD CONSTRAINT `StatusFK` FOREIGN KEY (`Status`) REFERENCES `JobStatus` (`JobStatusId`);

--
-- Constraints for table `JobStepCost`
--
ALTER TABLE `JobStepCost`
  ADD CONSTRAINT `JobStepCostTypeFK` FOREIGN KEY (`JobStepCostTypeId`) REFERENCES `JobStepCostType` (`JobStepCostType`),
  ADD CONSTRAINT `StepPK` FOREIGN KEY (`JobStepId`) REFERENCES `JobStep` (`JobStepId`);

--
-- Constraints for table `JobStepRelationship`
--
ALTER TABLE `JobStepRelationship`
  ADD CONSTRAINT `ChildFK` FOREIGN KEY (`ChildJobStepId`) REFERENCES `JobStep` (`JobStepId`),
  ADD CONSTRAINT `ParentFK` FOREIGN KEY (`ParrentJobStepId`) REFERENCES `JobStep` (`JobStepId`);

--
-- Constraints for table `JobTemplate`
--
ALTER TABLE `JobTemplate`
  ADD CONSTRAINT `FirstStepFK` FOREIGN KEY (`FirstJobStepId`) REFERENCES `JobStep` (`JobStepId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
