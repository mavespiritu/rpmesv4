/*
SQLyog Enterprise - MySQL GUI v7.02 
MySQL - 5.5.5-10.4.24-MariaDB : Database - db_neda_rpmes
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_neda_rpmes` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `db_neda_rpmes`;

/*Table structure for table `event_image` */

DROP TABLE IF EXISTS `event_image`;

CREATE TABLE `event_image` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` text DEFAULT NULL,
  `uploaded_by` int(255) DEFAULT NULL,
  `date_uploaded` datetime DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
