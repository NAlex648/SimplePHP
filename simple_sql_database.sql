-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2023 at 04:00 AM
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
-- Database: `simple_php_app`
--
create database simple_php_database;
use simple_php_database;
-- --------------------------------------------------------
--
-- Table structure for table `users`
--
CREATE TABLE `users` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `login_attempts` int(11) NOT NULL DEFAULT '0',
  `last_login_attempt` datetime DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `users` (`username`, `password`, `ip_address`, `login_attempts`, `last_login_attempt`) VALUES
('user123', 'ed91e7d524bd28ef96c51d81167457c09652604b7288b51299de629b0623a4be', NULL, 0, NULL),
('user234', '062ed1a8a2d50cf1c159a8b46f3c17dc1d8d9c2dd60b48323428a4d8145e481a', NULL, 0, NULL);
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
