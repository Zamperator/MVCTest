START TRANSACTION;
SET time_zone = "UTC";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mvctest`
--
CREATE DATABASE IF NOT EXISTS `mvctest` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mvctest`;

-- --------------------------------------------------------
--
-- Table structure for table `cache`
--
CREATE TABLE IF NOT EXISTS cache (
    `cache_key` varchar(255) NOT NULL,
    `value` longtext NOT NULL,
    `expires` int(11) NOT NULL,
    PRIMARY KEY (`cache_key`)
) ENGINE=InnoDB;

-- --------------------------------------------------------
--
-- Table structure for table `user`
--
CREATE TABLE IF NOT EXISTS user (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `status` enum('active', 'inactive', 'pending') NOT NULL DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`),
    KEY `status` (`status`)
) ENGINE=InnoDB;

-- --------------------------------------------------------
--
-- Table structure for table `auth`
--

-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS auth (
    `user_id` int(11) NOT NULL,
    `identifier` varchar(255) NOT NULL,
    `token` varchar(255) NOT NULL,
    `expires` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `token` (`token`),
    CONSTRAINT `auth_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

# Privileges for `mvctest`@`%`
GRANT ALL PRIVILEGES ON `cache`.* TO 'mvctest'@'%';
GRANT ALL PRIVILEGES ON `user`.* TO 'mvctest'@'%';
GRANT ALL PRIVILEGES ON `auth`.* TO 'mvctest'@'%';
