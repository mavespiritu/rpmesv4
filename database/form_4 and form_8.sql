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

/*Table structure for table `project_problem_solving_session` */

DROP TABLE IF EXISTS `project_problem_solving_session`;

CREATE TABLE `project_problem_solving_session` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `year` int(4) DEFAULT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `project_id` int(255) DEFAULT NULL,
  `pss_date` date DEFAULT NULL,
  `agreement_reached` text DEFAULT NULL,
  `next_step` text DEFAULT NULL,
  `slippage` text DEFAULT NULL,
  `submitted_by` int(255) DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_result` */

DROP TABLE IF EXISTS `project_result`;

CREATE TABLE `project_result` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `year` int(5) DEFAULT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `project_id` int(255) NOT NULL,
  `objective` text DEFAULT NULL,
  `observed_results` text DEFAULT NULL,
  `submitted_by` int(255) DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  `action` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_result` (`project_id`),
  CONSTRAINT `FK_project_result` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=504 DEFAULT CHARSET=utf8mb4;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
