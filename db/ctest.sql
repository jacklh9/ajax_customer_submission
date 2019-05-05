-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 05, 2019 at 04:23 PM
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
(2, 'U.S.S. Enterprise', 'Mail Stop: Bridge', '', '', '', 2),
(3, '', '', '', 'TX', '', 1),
(4, '1 Infinity Circle', 'Planet Vulcan', '', '', '00001', 2),
(5, 'Star Fleet H.Q.', 'United Federation of Planets', 'San Francisco', 'CA', '91932', 1),
(84, 'U.S.S. Excelsior', 'Mail Stop: Bridge', '', '', '', 3),
(85, '', '', '', '', '', 3),
(86, '123 Rodeo Dr', '', 'Beverly Hills', 'CA', '', 3),
(90, 'Pineapple', 'Under The Sea', 'No where', 'CA', '', 4),
(91, '', '', '', '', '', 4),
(92, '', '', '', '', '', 4),
(93, '1701 Rodeo Dr', '', 'Beverly Hills', 'CA', '90210', 2),
(94, 'Under a Rock', '', 'Bikini Bottom', '', '', 5),
(95, '', '', '', '', '', 5),
(96, '', '', '', '', '', 5),
(97, 'Rodeo Dr', '', 'Beverly Hills', 'CA', '90210', 6),
(98, 'CBS', '', 'Hollywood', 'CA', '91234', 6),
(115, '', '', '', '', '', 6),
(170, 'Somewhere near Jupiter', '', '', '', '02001', 14),
(171, '', '', 'Urbana-Champagne', 'IL', '09000', 14),
(172, 'Mission Control', '', 'Houston', 'TX', '70000', 14),
(176, '', '', '', '', '', 16),
(177, '', '', '', '', '', 16),
(178, '', '', '', '', '', 16);

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
(1, 'James', 'Kirk', 'jtkirk@starfleet.org', '619-555-0001', './profiles/1.jpg'),
(2, 'Spock', '', 'spock@starfleet.org', '619-555-0002', './profiles/2.webp'),
(3, 'Hikaru', 'Sulu', 'sulu@starfleet.org', '619-555-1003', './profiles/3.webp'),
(4, 'Spongebob', 'Squarepants', 'spongebob@bikinibottom.com', '858-555-1122', ''),
(5, 'Patrick', 'Starfish', 'patrick@bikinibottom.com', '619-555-1234', ''),
(6, 'Bob', 'Barker', 'bbarker@priceisright.com', '', './profiles/6.jpg'),
(14, 'HAL', '9000', 'hal9000@discovery.org', '929-000-2001', './profiles/14.jpg'),
(16, '', '', 'asdf@asfdsa.com', '', './profiles/16.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `FK_cust_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `filename`, `size`, `datetime`, `FK_cust_id`) VALUES
(88, 'Test_PDF.pdf', 14812, '2019-05-05 12:31:00', 1),
(89, 'Test_PDF (3rd copy).pdf', 14812, '2019-05-05 12:31:02', 1),
(90, 'Test_PDF (another copy).pdf', 14812, '2019-05-05 12:31:03', 1),
(91, 'Test_PDF (copy).pdf', 14812, '2019-05-05 12:31:05', 1),
(108, '20180606_IRS_statement.pdf', 3718172, '2019-05-05 15:43:29', 16),
(111, 'Test_PDF.pdf', 14812, '2019-05-05 15:50:03', 16),
(112, 'Test_PDF (3rd copy).pdf', 14812, '2019-05-05 15:50:05', 16),
(113, 'Test_PDF (another copy).pdf', 14812, '2019-05-05 15:50:06', 16),
(114, 'Test_PDF (copy).pdf', 14812, '2019-05-05 15:50:07', 16),
(115, 'pdf-test.pdf', 20597, '2019-05-05 15:53:28', 16),
(116, 'Test_PDF.pdf', 14812, '2019-05-05 15:53:29', 16),
(117, 'Test_PDF (3rd copy).pdf', 14812, '2019-05-05 15:53:30', 16),
(118, 'Test_PDF (another copy).pdf', 14812, '2019-05-05 15:53:31', 16),
(119, 'Test_PDF (copy).pdf', 14812, '2019-05-05 15:53:32', 16),
(120, 'pdf-test.pdf', 20597, '2019-05-05 15:58:30', 16),
(121, 'Test_PDF.pdf', 14812, '2019-05-05 15:58:31', 16),
(122, 'Test_PDF (3rd copy).pdf', 14812, '2019-05-05 15:58:32', 16),
(123, 'Test_PDF (another copy).pdf', 14812, '2019-05-05 15:58:33', 16),
(124, 'Test_PDF (copy).pdf', 14812, '2019-05-05 15:58:34', 16),
(125, 'pdf-test.pdf', 20597, '2019-05-05 16:03:12', 16),
(126, 'Test_PDF.pdf', 14812, '2019-05-05 16:03:14', 16),
(127, 'Test_PDF (3rd copy).pdf', 14812, '2019-05-05 16:03:15', 16),
(128, 'Test_PDF (another copy).pdf', 14812, '2019-05-05 16:03:16', 16),
(129, 'Test_PDF (copy).pdf', 14812, '2019-05-05 16:03:17', 16),
(130, 'pdf-test.pdf', 20597, '2019-05-05 16:03:37', 16),
(131, 'Test_PDF.pdf', 14812, '2019-05-05 16:03:38', 16),
(132, 'pdf-test.pdf', 20597, '2019-05-05 16:03:51', 16),
(133, 'Test_PDF.pdf', 14812, '2019-05-05 16:03:52', 16),
(134, 'Test_PDF (3rd copy).pdf', 14812, '2019-05-05 16:03:53', 16),
(135, 'Test_PDF (another copy).pdf', 14812, '2019-05-05 16:03:54', 16),
(136, 'Test_PDF (copy).pdf', 14812, '2019-05-05 16:03:55', 16),
(137, 'pdf-test.pdf', 20597, '2019-05-05 16:05:04', 16),
(138, 'Test_PDF.pdf', 14812, '2019-05-05 16:05:05', 16),
(139, 'Test_PDF (3rd copy).pdf', 14812, '2019-05-05 16:05:06', 16),
(140, 'Test_PDF (another copy).pdf', 14812, '2019-05-05 16:05:07', 16),
(141, 'Test_PDF (copy).pdf', 14812, '2019-05-05 16:05:09', 16);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
