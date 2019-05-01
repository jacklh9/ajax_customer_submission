-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 02, 2019 at 01:26 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ctest`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `street_line1` varchar(255) NOT NULL,
  `street_line2` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `FK_cust_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `street_line1`, `street_line2`, `city`, `state`, `zip`, `FK_cust_id`) VALUES
(1, 'U.S.S. Enterprise', 'Mail Stop: Bridge', '', 'CA', '12345', 1),
(2, 'U.S.S. Enterprise', 'Mail Stop: Bridge', '', 'no', '', 2),
(3, '', '', '', 'TX', '', 1),
(4, '1 Infinity Circle', 'Planet Vulcan', '', 'no', '00001', 2),
(5, 'Star Fleet H.Q.', 'United Federation of Planets', 'San Francisco', 'CA', '91932', 1),
(84, 'U.S.S. Excelsior', 'Mail Stop: Bridge', '', 'no', '', 3),
(85, '', '', '', 'no', '', 3),
(86, '123 Rodeo Dr', '', 'Beverly Hills', 'CA', '', 3),
(90, 'Pineapple', 'Under The Sea', 'No where', 'CA', '', 4),
(91, '', '', '', 'no', '', 4),
(92, '', '', '', 'no', '', 4),
(93, 'a', 'b', 'c', 'AK', '92129', 2),
(94, 'Under a Rock', '', 'Bikini Bottom', 'no', '', 5),
(95, '', '', '', 'no', '', 5),
(96, '', '', '', 'no', '', 5),
(97, 'Rodeo Dr', '', 'Beverly Hills', 'CA', '90210', 6),
(98, 'CBS', '', 'Hollywood', 'CA', '91234', 6);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `first` varchar(255) NOT NULL,
  `last` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `profile` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `first`, `last`, `email`, `phone`, `profile`) VALUES
(1, 'James', 'Kirk', 'jtkirk@starfleet.org', '619-555-0001', ''),
(2, 'Spock', '', 'spock@starfleet.org', '619-555-0002', ''),
(3, 'Hikaru', 'Sulu', 'sulu@starfleet.org', '619-555-1003', ''),
(4, 'Spongebob', 'Squarepants', 'spongebob@bikinibottom.com', '858-555-1122', '4.webp'),
(5, 'Patrick', 'Starfish', 'patrick@bikinibottom.com', '619-555-1234', ''),
(6, 'Bob', 'Barker', 'bbarker@priceisright.com', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `FK_cust_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
