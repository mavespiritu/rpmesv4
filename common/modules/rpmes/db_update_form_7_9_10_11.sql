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

/*Table structure for table `project_finding` */

DROP TABLE IF EXISTS `project_finding`;

CREATE TABLE `project_finding` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `year` int(255) DEFAULT NULL,
  `project_id` int(255) DEFAULT NULL,
  `inspection_date` date DEFAULT NULL,
  `major_finding` text DEFAULT NULL,
  `issues` text DEFAULT NULL,
  `action` text DEFAULT NULL,
  `submitted_by` int(255) DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_problem` */

DROP TABLE IF EXISTS `project_problem`;

CREATE TABLE `project_problem` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `year` int(4) DEFAULT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `project_id` int(255) DEFAULT NULL,
  `nature` enum('Government / Funding Institution Approvals and Other Preconditions','Design, Scope, Technical','Procurement','Site Condition / Availability','Budget and Funds Flow','Inputs and Cost','Contract Management / Administration','Project Monitoring Office, Manpower Capacity / Capability','Institutional Support','Legal and Policy Issuances','Sustainability, Operations and Maintenance','Force Majeure','Peace and Order Situation') DEFAULT NULL,
  `detail` text DEFAULT NULL,
  `strategy` text DEFAULT NULL,
  `responsible_entity` text DEFAULT NULL,
  `lesson_learned` text DEFAULT NULL,
  `submitted_by` int(255) DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `resolution` */

DROP TABLE IF EXISTS `resolution`;

CREATE TABLE `resolution` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `resolution_number` varchar(255) DEFAULT NULL,
  `resolution` text DEFAULT NULL,
  `date_approved` date DEFAULT NULL,
  `rpmc_action` text DEFAULT NULL,
  `submitted_by` int(255) DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `training` */

DROP TABLE IF EXISTS `training`;

CREATE TABLE `training` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `title` text DEFAULT NULL,
  `objective` text DEFAULT NULL,
  `office` text DEFAULT NULL,
  `organization` text DEFAULT NULL,
  `male_participant` int(11) DEFAULT NULL,
  `female_participant` int(11) DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
