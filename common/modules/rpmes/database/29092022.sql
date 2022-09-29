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

/*Table structure for table `accomplishment` */

DROP TABLE IF EXISTS `accomplishment`;

CREATE TABLE `accomplishment` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `action` text DEFAULT NULL,
  `submitted_by` int(255) DEFAULT NULL,
  `date_submitted` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_accomplishment_project` (`project_id`),
  CONSTRAINT `FK_accomplishment_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=800 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `acknowledgment` */

DROP TABLE IF EXISTS `acknowledgment`;

CREATE TABLE `acknowledgment` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `submission_id` int(255) DEFAULT NULL,
  `control_no` varchar(50) DEFAULT NULL,
  `recipient_name` varchar(100) DEFAULT NULL,
  `recipient_designation` varchar(100) DEFAULT NULL,
  `recipient_office` text DEFAULT NULL,
  `recipient_address` text DEFAULT NULL,
  `findings` text DEFAULT NULL,
  `action_taken` text DEFAULT NULL,
  `acknowledged_by` int(255) DEFAULT NULL,
  `date_acknowledged` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_acknowledgment_submission` (`submission_id`),
  CONSTRAINT `FK_acknowledgment_submission` FOREIGN KEY (`submission_id`) REFERENCES `submission` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `agency` */

DROP TABLE IF EXISTS `agency`;

CREATE TABLE `agency` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `agency_type_id` int(255) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `head` varchar(100) DEFAULT NULL,
  `salutation` varchar(100) DEFAULT NULL,
  `head_designation` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_agency_agency_type` (`agency_type_id`),
  CONSTRAINT `FK_agency_agency_type` FOREIGN KEY (`agency_type_id`) REFERENCES `agency_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `agency_type` */

DROP TABLE IF EXISTS `agency_type`;

CREATE TABLE `agency_type` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `auth_assignment` */

DROP TABLE IF EXISTS `auth_assignment`;

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `idx-auth_assignment-user_id` (`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `auth_item` */

DROP TABLE IF EXISTS `auth_item`;

CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `auth_item_child` */

DROP TABLE IF EXISTS `auth_item_child`;

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `auth_rule` */

DROP TABLE IF EXISTS `auth_rule`;

CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `beneficiaries_accomplishment` */

DROP TABLE IF EXISTS `beneficiaries_accomplishment`;

CREATE TABLE `beneficiaries_accomplishment` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `male` int(10) DEFAULT NULL,
  `female` int(10) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_beneficiaries_accomplishment_project` (`project_id`),
  CONSTRAINT `FK_beneficiaries_accomplishment_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=792 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `category` */

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `due_date` */

DROP TABLE IF EXISTS `due_date`;

CREATE TABLE `due_date` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `report` text DEFAULT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `semester` enum('First','Second') DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `file` */

DROP TABLE IF EXISTS `file`;

CREATE TABLE `file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `itemId` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `mime` varchar(255) NOT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `date_upload` int(11) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `file_model` (`model`),
  KEY `file_item_id` (`itemId`)
) ENGINE=InnoDB AUTO_INCREMENT=594 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `financial_accomplishment` */

DROP TABLE IF EXISTS `financial_accomplishment`;

CREATE TABLE `financial_accomplishment` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `allocation` text DEFAULT NULL,
  `releases` text DEFAULT NULL,
  `obligation` text DEFAULT NULL,
  `expenditures` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_financial_accomplishment_project` (`project_id`),
  CONSTRAINT `FK_financial_accomplishment_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=792 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `fund_source` */

DROP TABLE IF EXISTS `fund_source`;

CREATE TABLE `fund_source` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `fund_type` enum('Local Funds','Foreign Assistance') DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `allow_typhoon` enum('Yes','No') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `group_accomplishment` */

DROP TABLE IF EXISTS `group_accomplishment`;

CREATE TABLE `group_accomplishment` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_project_id_2479_00` (`project_id`),
  CONSTRAINT `fk_project_2472_00` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=778 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `key_result_area` */

DROP TABLE IF EXISTS `key_result_area`;

CREATE TABLE `key_result_area` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `category_id` int(255) DEFAULT NULL,
  `kra_no` int(3) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `NewIndex1` (`category_id`),
  CONSTRAINT `FK_key_result_area_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `location_scope` */

DROP TABLE IF EXISTS `location_scope`;

CREATE TABLE `location_scope` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `type` enum('Single','Multiple') DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `migration` */

DROP TABLE IF EXISTS `migration`;

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `mode_of_implementation` */

DROP TABLE IF EXISTS `mode_of_implementation`;

CREATE TABLE `mode_of_implementation` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `person_employed_accomplishment` */

DROP TABLE IF EXISTS `person_employed_accomplishment`;

CREATE TABLE `person_employed_accomplishment` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `male` int(10) DEFAULT NULL,
  `female` int(10) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_person_employed_accomplishment_project` (`project_id`),
  CONSTRAINT `FK_person_employed_accomplishment_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=792 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `physical_accomplishment` */

DROP TABLE IF EXISTS `physical_accomplishment`;

CREATE TABLE `physical_accomplishment` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `value` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_physical_accomplishment_project` (`project_id`),
  CONSTRAINT `FK_physical_accomplishment_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=793 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `plan` */

DROP TABLE IF EXISTS `plan`;

CREATE TABLE `plan` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` varchar(5) DEFAULT NULL,
  `date_submitted` datetime DEFAULT current_timestamp(),
  `submitted_by` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_plan_project` (`project_id`),
  CONSTRAINT `FK_plan_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=577 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `profile` */

DROP TABLE IF EXISTS `profile`;

CREATE TABLE `profile` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `public_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gravatar_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gravatar_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_user_profile` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `program` */

DROP TABLE IF EXISTS `program`;

CREATE TABLE `program` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project` */

DROP TABLE IF EXISTS `project`;

CREATE TABLE `project` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `source_id` int(255) DEFAULT NULL,
  `project_no` varchar(100) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `agency_id` int(255) DEFAULT NULL,
  `program_id` int(255) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sector_id` int(255) DEFAULT NULL,
  `sub_sector_id` int(255) DEFAULT NULL,
  `location_scope_id` int(255) DEFAULT NULL,
  `mode_of_implementation_id` int(255) DEFAULT NULL,
  `other_mode` varchar(100) DEFAULT NULL,
  `fund_source_id` int(255) DEFAULT NULL,
  `typhoon` varchar(100) DEFAULT NULL,
  `data_type` enum('Default','Cumulative','Maintained') DEFAULT NULL,
  `period` enum('Current Year','Carry-Over') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `submitted_by` int(255) DEFAULT NULL,
  `date_submitted` datetime DEFAULT current_timestamp(),
  `draft` enum('Yes','No') DEFAULT NULL,
  `complete` enum('Yes','No') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_agency` (`agency_id`),
  KEY `FK_project_sector` (`sector_id`),
  KEY `FK_project_sub_sector` (`sub_sector_id`),
  KEY `FK_project_mode_of_implementation` (`mode_of_implementation_id`),
  CONSTRAINT `FK_project_agency` FOREIGN KEY (`agency_id`) REFERENCES `agency` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_project_mode_of_implementation` FOREIGN KEY (`mode_of_implementation_id`) REFERENCES `mode_of_implementation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_project_sector` FOREIGN KEY (`sector_id`) REFERENCES `sector` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_project_sub_sector` FOREIGN KEY (`sub_sector_id`) REFERENCES `sub_sector` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=825 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_barangay` */

DROP TABLE IF EXISTS `project_barangay`;

CREATE TABLE `project_barangay` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `region_id` varchar(4) DEFAULT NULL,
  `province_id` varchar(3) DEFAULT NULL,
  `citymun_id` varchar(3) DEFAULT NULL,
  `barangay_id` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_barangay_project` (`project_id`),
  CONSTRAINT `FK_project_barangay_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=230 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_category` */

DROP TABLE IF EXISTS `project_category`;

CREATE TABLE `project_category` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `category_id` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_category_project` (`project_id`),
  KEY `FK_project_category_category` (`category_id`),
  CONSTRAINT `FK_project_category_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_project_category_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=685 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_citymun` */

DROP TABLE IF EXISTS `project_citymun`;

CREATE TABLE `project_citymun` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `region_id` varchar(4) DEFAULT NULL,
  `province_id` varchar(3) DEFAULT NULL,
  `citymun_id` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_citymun_project` (`project_id`),
  CONSTRAINT `FK_project_citymun_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=386 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_exception` */

DROP TABLE IF EXISTS `project_exception`;

CREATE TABLE `project_exception` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `findings` text DEFAULT NULL,
  `causes` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL,
  `submitted_by` int(255) DEFAULT NULL,
  `date_submitted` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_project_exception_project` (`project_id`),
  CONSTRAINT `FK_project_exception_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_expected_output` */

DROP TABLE IF EXISTS `project_expected_output`;

CREATE TABLE `project_expected_output` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `indicator` varchar(200) DEFAULT NULL,
  `target` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_expected_output_project` (`project_id`),
  CONSTRAINT `FK_project_expected_output_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=872 DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_kra` */

DROP TABLE IF EXISTS `project_kra`;

CREATE TABLE `project_kra` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `key_result_area_id` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_kra_project` (`project_id`),
  KEY `FK_project_kra_ra` (`key_result_area_id`),
  CONSTRAINT `FK_project_kra_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_project_kra_ra` FOREIGN KEY (`key_result_area_id`) REFERENCES `key_result_area` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=447 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_outcome` */

DROP TABLE IF EXISTS `project_outcome`;

CREATE TABLE `project_outcome` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `outcome` text DEFAULT NULL,
  `performance_indicator` text DEFAULT NULL,
  `target` text DEFAULT NULL,
  `timeline` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_outcome` (`project_id`),
  CONSTRAINT `FK_project_outcome` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=855 DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

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
  `submitted_by` int(255) DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_province` */

DROP TABLE IF EXISTS `project_province`;

CREATE TABLE `project_province` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `province_id` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_province_project` (`project_id`),
  CONSTRAINT `FK_project_province_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=662 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_rdp_chapter` */

DROP TABLE IF EXISTS `project_rdp_chapter`;

CREATE TABLE `project_rdp_chapter` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `rdp_chapter_id` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_rdp_chapter_project` (`project_id`),
  KEY `FK_project_rdp_chapter` (`rdp_chapter_id`),
  CONSTRAINT `FK_project_rdp_chapter` FOREIGN KEY (`rdp_chapter_id`) REFERENCES `rdp_chapter` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK_project_rdp_chapter_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=790 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_rdp_chapter_outcome` */

DROP TABLE IF EXISTS `project_rdp_chapter_outcome`;

CREATE TABLE `project_rdp_chapter_outcome` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `rdp_chapter_outcome_id` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_rdp_chapter_outcome` (`project_id`),
  KEY `FK_project_rdp_chapter_outcome_project` (`rdp_chapter_outcome_id`),
  CONSTRAINT `FK_project_rdp_chapter_outcome` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_project_rdp_chapter_outcome_project` FOREIGN KEY (`rdp_chapter_outcome_id`) REFERENCES `rdp_chapter_outcome` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=416 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_rdp_sub_chapter_outcome` */

DROP TABLE IF EXISTS `project_rdp_sub_chapter_outcome`;

CREATE TABLE `project_rdp_sub_chapter_outcome` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `rdp_sub_chapter_outcome_id` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_rdp_sub_chapter_outcome_project'` (`project_id`),
  KEY `FK_project_rdp_sub_chapter_outcome_sub` (`rdp_sub_chapter_outcome_id`),
  CONSTRAINT `FK_project_rdp_sub_chapter_outcome_project'` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_project_rdp_sub_chapter_outcome_sub` FOREIGN KEY (`rdp_sub_chapter_outcome_id`) REFERENCES `rdp_sub_chapter_outcome` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=196 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_region` */

DROP TABLE IF EXISTS `project_region`;

CREATE TABLE `project_region` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `region_id` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_region_project` (`project_id`),
  CONSTRAINT `FK_project_region_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=681 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_sdg_goal` */

DROP TABLE IF EXISTS `project_sdg_goal`;

CREATE TABLE `project_sdg_goal` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `sdg_goal_id` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_sdg_goal_project` (`project_id`),
  KEY `FK_project_sdg_goal` (`sdg_goal_id`),
  CONSTRAINT `FK_project_sdg_goal` FOREIGN KEY (`sdg_goal_id`) REFERENCES `sdg_goal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_project_sdg_goal_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=870 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `project_target` */

DROP TABLE IF EXISTS `project_target`;

CREATE TABLE `project_target` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `project_id` int(255) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `target_type` enum('Physical','Financial','Male Employed','Female Employed','Beneficiaries','Group Beneficiaries') DEFAULT NULL,
  `indicator` varchar(100) DEFAULT NULL,
  `q1` text DEFAULT NULL,
  `q2` text DEFAULT NULL,
  `q3` text DEFAULT NULL,
  `q4` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_project_target_project` (`project_id`),
  CONSTRAINT `FK_project_target_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4316 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `rdp_chapter` */

DROP TABLE IF EXISTS `rdp_chapter`;

CREATE TABLE `rdp_chapter` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `chapter_no` int(4) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `rdp_chapter_outcome` */

DROP TABLE IF EXISTS `rdp_chapter_outcome`;

CREATE TABLE `rdp_chapter_outcome` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `rdp_chapter_id` int(255) DEFAULT NULL,
  `level` varchar(10) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_rdp_chapter_outcome_rdp` (`rdp_chapter_id`),
  CONSTRAINT `FK_rdp_chapter_outcome_rdp` FOREIGN KEY (`rdp_chapter_id`) REFERENCES `rdp_chapter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `rdp_sub_chapter_outcome` */

DROP TABLE IF EXISTS `rdp_sub_chapter_outcome`;

CREATE TABLE `rdp_sub_chapter_outcome` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `rdp_chapter_id` int(255) DEFAULT NULL,
  `rdp_chapter_outcome_id` int(255) DEFAULT NULL,
  `level` varchar(10) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_rdp_sub_chapter_outcome_rdp` (`rdp_chapter_outcome_id`),
  CONSTRAINT `FK_rdp_sub_chapter_outcome_rdp` FOREIGN KEY (`rdp_chapter_outcome_id`) REFERENCES `rdp_chapter_outcome` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `sdg_goal` */

DROP TABLE IF EXISTS `sdg_goal`;

CREATE TABLE `sdg_goal` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `sdg_no` int(3) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `sector` */

DROP TABLE IF EXISTS `sector`;

CREATE TABLE `sector` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `title` varchar(200) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Table structure for table `social_account` */

DROP TABLE IF EXISTS `social_account`;

CREATE TABLE `social_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_unique` (`provider`,`client_id`),
  UNIQUE KEY `account_unique_code` (`code`),
  KEY `fk_user_account` (`user_id`),
  CONSTRAINT `fk_user_account` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `sub_sector` */

DROP TABLE IF EXISTS `sub_sector`;

CREATE TABLE `sub_sector` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `sub_sector_per_sector` */

DROP TABLE IF EXISTS `sub_sector_per_sector`;

CREATE TABLE `sub_sector_per_sector` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `sector_id` int(255) DEFAULT NULL,
  `sub_sector_id` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_sub_sector_per_sector_sector` (`sector_id`),
  KEY `FK_sub_sector_per_sector_sub_sector` (`sub_sector_id`),
  CONSTRAINT `FK_sub_sector_per_sector_sector` FOREIGN KEY (`sector_id`) REFERENCES `sector` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_sub_sector_per_sector_sub_sector` FOREIGN KEY (`sub_sector_id`) REFERENCES `sub_sector` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `submission` */

DROP TABLE IF EXISTS `submission`;

CREATE TABLE `submission` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `agency_id` int(255) DEFAULT NULL,
  `report` text DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') DEFAULT NULL,
  `semester` enum('First','Second') DEFAULT NULL,
  `submitted_by` int(255) DEFAULT NULL,
  `date_submitted` datetime DEFAULT current_timestamp(),
  `draft` enum('Yes','No') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=205 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `tblbarangay` */

DROP TABLE IF EXISTS `tblbarangay`;

CREATE TABLE `tblbarangay` (
  `region_c` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `province_c` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `citymun_c` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `barangay_c` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `district_c` varbinary(3) NOT NULL,
  `barangay_m` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  KEY `idx_citymun_c_8337_00` (`citymun_c`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tblcitymun` */

DROP TABLE IF EXISTS `tblcitymun`;

CREATE TABLE `tblcitymun` (
  `region_c` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `province_c` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `district_c` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `citymun_c` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `citymun_m` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `lgu_type` varchar(3) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tbloffice` */

DROP TABLE IF EXISTS `tbloffice`;

CREATE TABLE `tbloffice` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abbreviation` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `color` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tblprovince` */

DROP TABLE IF EXISTS `tblprovince`;

CREATE TABLE `tblprovince` (
  `region_c` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `province_c` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `province_m` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`province_c`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tblregion` */

DROP TABLE IF EXISTS `tblregion`;

CREATE TABLE `tblregion` (
  `region_c` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `region_m` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `abbreviation` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `region_sort` int(2) DEFAULT NULL,
  PRIMARY KEY (`region_c`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tblsection` */

DROP TABLE IF EXISTS `tblsection`;

CREATE TABLE `tblsection` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `office_id` int(255) DEFAULT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abbreviation` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tblsection_2105_02` (`office_id`),
  CONSTRAINT `fk_tblsection_2105_02` FOREIGN KEY (`office_id`) REFERENCES `tbloffice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tblunit` */

DROP TABLE IF EXISTS `tblunit`;

CREATE TABLE `tblunit` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `office_id` int(255) DEFAULT NULL,
  `section_id` int(255) DEFAULT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abbreviation` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tblunit_2105_02` (`office_id`),
  KEY `fk_tblunit_2106_02` (`section_id`),
  CONSTRAINT `fk_tblunit_2105_02` FOREIGN KEY (`office_id`) REFERENCES `tbloffice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tblunit_2106_02` FOREIGN KEY (`section_id`) REFERENCES `tblsection` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `token` */

DROP TABLE IF EXISTS `token`;

CREATE TABLE `token` (
  `user_id` int(11) NOT NULL,
  `code` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL,
  `type` smallint(6) NOT NULL,
  UNIQUE KEY `token_unique` (`user_id`,`code`,`type`),
  CONSTRAINT `fk_user_token` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `confirmed_at` int(11) DEFAULT NULL,
  `unconfirmed_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `blocked_at` int(11) DEFAULT NULL,
  `registration_ip` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `flags` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_unique_email` (`email`),
  UNIQUE KEY `user_unique_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `user_info` */

DROP TABLE IF EXISTS `user_info`;

CREATE TABLE `user_info` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `EMP_N` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `LAST_M` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `FIRST_M` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `MIDDLE_M` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `SUFFIX` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `BIRTH_D` date NOT NULL,
  `SEX_C` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `OFFICE_C` int(2) DEFAULT NULL,
  `SECTION_C` int(3) DEFAULT NULL,
  `UNIT_C` int(3) DEFAULT NULL,
  `POSITION_C` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `DESIGNATION` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `REGION_C` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `PROVINCE_C` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `CITYMUN_C` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `MOBILEPHONE` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `LANDPHONE` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `FAX_NO` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `EMAIL` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `PHOTO` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ALTER_EMAIL` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `BARANGAY_C` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `AGENCY_C` int(255) DEFAULT NULL,
  `TERM` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `idx_OFFICE_C_211_00` (`OFFICE_C`),
  KEY `idx_PROVINCE_C_211_01` (`PROVINCE_C`),
  KEY `idx_REGION_C_211_02` (`REGION_C`),
  KEY `fk_tblsection_2104_02` (`SECTION_C`),
  KEY `fk_tblunit_2104_02` (`UNIT_C`),
  KEY `FK_user_info_agency` (`AGENCY_C`),
  CONSTRAINT `FK_user_info_agency` FOREIGN KEY (`AGENCY_C`) REFERENCES `agency` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbloffice_2104_02` FOREIGN KEY (`OFFICE_C`) REFERENCES `tbloffice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tblsection_2104_02` FOREIGN KEY (`SECTION_C`) REFERENCES `tblsection` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tblunit_2104_02` FOREIGN KEY (`UNIT_C`) REFERENCES `tblunit` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_2104_03` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
