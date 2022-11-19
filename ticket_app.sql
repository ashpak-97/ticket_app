-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2022 at 08:17 PM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 7.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ticket_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '81dc9bdb52d04dc20036dbd8313ed055');

-- --------------------------------------------------------

--
-- Table structure for table `tb_customer`
--

CREATE TABLE `tb_customer` (
  `Customer_code` int(100) NOT NULL,
  `Name` text NOT NULL,
  `Type` text NOT NULL,
  `Location` text NOT NULL,
  `Address` text NOT NULL,
  `Contact` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_customer`
--

INSERT INTO `tb_customer` (`Customer_code`, `Name`, `Type`, `Location`, `Address`, `Contact`) VALUES
(1, 'Darshan', 'customer', 'sahgshf', 'ahdvavd', '7350807077'),
(2, 'Ashpak', 'Customer', 'sws', 'wsw', '7350807077');

-- --------------------------------------------------------

--
-- Table structure for table `tb_customersite`
--

CREATE TABLE `tb_customersite` (
  `id` int(100) NOT NULL,
  `Site_Name` text NOT NULL,
  `Site_Address` text NOT NULL,
  `Contact_Person` text NOT NULL,
  `Site_Location` text NOT NULL,
  `Job_Code` text NOT NULL,
  `Job_Detail` text NOT NULL,
  `Job_Type` text NOT NULL,
  `Job_Status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tb_machine`
--

CREATE TABLE `tb_machine` (
  `Machine_Code` int(100) NOT NULL,
  `Name` text NOT NULL,
  `Type` text NOT NULL,
  `Brand` text NOT NULL,
  `Country_of_Origin` text NOT NULL,
  `Serial_No` text NOT NULL,
  `Purchase_Date` date NOT NULL,
  `Warranty_Period` text NOT NULL,
  `Contract_Period` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_customer`
--
ALTER TABLE `tb_customer`
  ADD PRIMARY KEY (`Customer_code`);

--
-- Indexes for table `tb_customersite`
--
ALTER TABLE `tb_customersite`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_machine`
--
ALTER TABLE `tb_machine`
  ADD PRIMARY KEY (`Machine_Code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_customer`
--
ALTER TABLE `tb_customer`
  MODIFY `Customer_code` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_customersite`
--
ALTER TABLE `tb_customersite`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_machine`
--
ALTER TABLE `tb_machine`
  MODIFY `Machine_Code` int(100) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
