-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.31-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for db_hirewing
CREATE DATABASE IF NOT EXISTS `db_hirewing` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `db_hirewing`;

-- Dumping structure for table db_hirewing.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`email`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_hirewing.password_resets: ~0 rows (approximately)
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;

-- Dumping structure for table db_hirewing.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_hirewing.permissions: ~0 rows (approximately)
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;

-- Dumping structure for table db_hirewing.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Dumping data for table db_hirewing.roles: ~4 rows (approximately)
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`) VALUES
	(1, 'Administrator'),
	(4, 'Parent'),
	(3, 'Student'),
	(2, 'Teacher');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Dumping structure for table db_hirewing.role_permissions
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_hirewing.role_permissions: ~0 rows (approximately)
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;

-- Dumping structure for table db_hirewing.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `access_token` text NOT NULL,
  `last_login` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT '0',
  `locked` int(11) DEFAULT '0',
  `verification_token` varchar(100) DEFAULT NULL,
  `is_deleted` int(11) DEFAULT '0',
  `is_active` int(11) DEFAULT '1',
  `verified_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

-- Dumping data for table db_hirewing.users: ~5 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `access_token`, `last_login`, `ip_address`, `role_id`, `created_at`, `updated_at`, `failed_attempts`, `locked`, `verification_token`, `is_deleted`, `is_active`, `verified_at`) VALUES
	(4, 'parthdev', 'parthdptl@gmail.com', '$2y$10$E9Qy9cQv67Hf3/ZaJZPA8e39QIrA8sa0bkWm92Yve0TJ/6f7iGzTW', 'Parth', 'Patel', '64fc8c9a3d77e13a1c4cd20b3c987389b587535208757dd15f7a077bdb25dd4f19dd231de210c1710f56a0e273bd7711e5fed83cb854d05e4dd210b855ee55da', '1708338010', '192.168.2.17', 3, '2024-02-18 14:49:21', '2024-02-19 11:20:10', 1, 0, NULL, 0, 1, NULL),
	(6, 'parthdptl11', 'parthdptl11@gmail.com', '$2y$10$Q0zh.lnpgatvrEVgPKhMUuHGW.xnrPCJqQhNoaSIntSUYugdo3pdK', 'Daxesh', 'Patel', '', '', '', 1, '2024-02-18 15:06:45', '2024-02-19 11:16:48', 0, 0, NULL, 0, 1, NULL),
	(13, 'parthdptl33', 'parthdptl33@gmail.com', '$2y$10$jFF9dIRSnJnlgC3q1H/pduHVCdzMj/0NQLOBMth0SkP4qUbJ2fADS', 'Nancy', 'Patel', 'df47e136c0aecffc1b2939560d44b71dc03676161d7b674b1dbb044a39040b410de50a83d83af8231021930e6ecba503cf0fe06b16f699b1744b05c5332ec86b', '1708335951', '192.168.2.17', 3, '2024-02-18 21:10:26', '2024-02-19 10:45:51', 0, 0, NULL, 0, 1, NULL),
	(14, 'parthdptl44', 'parthdptl44@gmail.com', '$2y$10$ahMXz9bF0sq/ZcMADjQfiu0gvvGLO6Lk1xL10cPoJm4cBrmCOktXS', 'Parth', 'Patel', 'bbce8d90af9603a74dc4200496d8d201d184f391c7e692bc54dc91111416a312a74eb9dd71829e5f9416205e1b98f6468c71b40020e73a1578364b08636d5d98', '1708326866', '192.168.2.17', 3, '2024-02-19 07:50:21', '2024-02-19 09:52:02', 2, 0, NULL, 0, 1, NULL),
	(15, 'parthdptl55', 'parthdptl55@gmail.com', '$2y$10$E9Qy9cQv67Hf3/ZaJZPA8e39QIrA8sa0bkWm92Yve0TJ/6f7iGzTW', 'Parth', 'Patel', '057542ef249b352697fcb2b787d7e1b0533da678927660dec56b099366953fbfb39475b1602a895b67b98f570b8c66a4e61e5dda07fd6d6b8f2ebdd1654c26c9', '1708337865', '192.168.2.17', 1, '2024-02-19 07:51:06', '2024-02-19 11:17:45', 1, 0, NULL, 0, 1, NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
