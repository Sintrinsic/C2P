-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 28, 2014 at 09:04 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pwns_c2p`
--

-- --------------------------------------------------------

--
-- Table structure for table `archived`
--

CREATE TABLE IF NOT EXISTS `archived` (
  `id` int(16) NOT NULL,
  `passer_name` varchar(32) NOT NULL,
  `cust_name` varchar(32) NOT NULL,
  `billing_url` varchar(64) NOT NULL,
  `deets` text NOT NULL,
  `time_submitted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_accepted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `receiver` varchar(32) NOT NULL,
  `chat_id` int(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chat_id` (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE IF NOT EXISTS `chats` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `passer_name` varchar(32) NOT NULL,
  `cust_name` varchar(32) NOT NULL,
  `billing_url` varchar(64) NOT NULL,
  `deets` text NOT NULL,
  `time_submitted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_accepted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `receiver_name` varchar(32) NOT NULL,
  `chat_id` int(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chat_id` (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `sender` varchar(32) NOT NULL,
  `receiver` varchar(32) NOT NULL,
  `msg` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
