/*
SQLyog Enterprise - MySQL GUI v7.02 
MySQL - 5.5.5-10.4.18-MariaDB : Database - db_neda_ppmp
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_neda_ppmp` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `db_neda_ppmp`;

/*Table structure for table `ppmp_obj` */

DROP TABLE IF EXISTS `ppmp_obj`;

CREATE TABLE `ppmp_obj` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `obj_id` int(255) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `active` enum('1','0') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ppmp_obj` (`obj_id`),
  CONSTRAINT `FK_ppmp_obj` FOREIGN KEY (`obj_id`) REFERENCES `ppmp_obj` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4;

/*Data for the table `ppmp_obj` */

insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (1,NULL,'5020000000','Maintenance and Other Operating Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (2,1,'5020100000','Traveling Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (3,2,'5020101000','Traveling Expenses - Local','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (4,2,'5020102000','Traveling Expenses - Foreign','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (5,1,'5020200000','Training and Scholarship Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (6,5,'5020201000','Training Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (7,6,'5020201001','ICT Training Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (8,6,'5020201002','Training Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (9,5,'5020202000','Scholarship Grants/Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (10,1,'5020300000','Supplies and Materials Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (11,10,'5020301000','Office Supplies Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (12,11,'5020301001','ICT Office Supplies Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (13,11,'5020301002','Office Supplies Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (14,78,'5020321003','Information and Communications Technology Equipment','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (15,10,'5020302000','Accountable Forms Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (16,10,'5020306000','Welfare Goods Expense','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (17,10,'5020308000','Medical, Dental and Laboratory Supplies Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (18,10,'5020322000','Semi-Expendable Furniture, Fixtures and Books Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (19,10,'5020399000','Other Supplies and Materials Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (20,1,'5020400000','Utility Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (21,20,'5020401000','Water Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (22,20,'5020402000','Electricity Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (23,1,'5020500000','Communication Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (24,23,'5020501000','Postage and Courier Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (25,23,'5020502000','Telephone Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (26,25,'5020502001','Mobile','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (27,25,'5020502002','Landline','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (28,23,'5020503000','Internet Subscription Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (29,23,'5020504000','Cable, Satellite, Telegraph and Radio Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (30,1,'5021000000','Confidential, Intelligence, and Extraordinary Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (31,30,'5021003000','Extraordinary and Miscellaneous Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (32,79,'5021103000','Consultancy Services','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (33,32,'5021103002','Consultancy Services','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (34,79,'5021199000','Other Professional Services','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (35,1,'5021200000','General Services','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (36,35,'5021202000','Janitorial Services','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (37,35,'5021203000','Security Services','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (38,35,'5021299000','Other General Services','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (39,38,'5021299001','Other General Services - ICT Services','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (40,38,'5021299099','Other General Services','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (41,1,'5021300000','Repairs and Maintenance','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (42,41,'5021304000','Repairs and Maintenance - Buildings and Other Structures','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (43,42,'5021304001','Buildings','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (44,42,'5021304099','Other Structures','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (45,41,'5021307000','Repairs and Maintenance - Furniture and Fixtures','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (46,41,'5021309000','Repairs and Maintenance - Leased Assets Improvements','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (47,41,'5021305000','Repairs and Maintenance - Machinery and Equipment','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (48,47,'5021305002','Office Equipment','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (49,47,'5021305003','ICT Equipment','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (50,47,'5021305099','Other Equipment','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (51,41,'5021399000','Repairs and Maintenance - Other Property, Plant and Equipment','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (52,51,'5021399099','Other Property, Plant and Equipment','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (53,41,'5021321000','Repairs and Maintenance - Semi Expendable Machinery and Equipment','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (54,53,'5021321007','Communications Equipment','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (55,41,'5021306000','Repairs and Maintenance - Transportation Equipment','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (56,55,'5021306001','Motor Vehicles','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (57,1,'5021500000','Taxes, Insurance Premiums and Other Fees','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (58,59,'5021501001','Taxes, Duties and Licenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (59,57,'5021501000','Taxes, Duties and Licenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (60,57,'5021502000','Fidelity Bond Premiums','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (61,57,'5021503000','Insurance Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (62,1,'5021601000','Labor and Wages','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (63,1,'5029900000','Other Maintenance and Operating Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (64,63,'5029901000','Advertising, Promotional and Marketing Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (65,63,'5029902000','Printing and Publication Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (66,63,'5029904000','Transportation and Delivery Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (67,63,'5029905000','Rent/Lease Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (68,67,'5029905001','Rents - Building and Structures','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (69,67,'5029905003','Rents - Motor Vehicles','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (70,67,'5029905004','Rents - Equipment','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (71,67,'5029905005','Rents - Living Quarters','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (72,63,'5029906000','Membership Due Contributions to Organizations','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (73,63,'5029907000','Subscription Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (74,73,'5029907001','ICT Software Subscription','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (75,80,'5029999099','Other Maintenance and Operating Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (76,10,'5020309000','Fuel, Oil and Lubricants Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (77,63,'5029903000','Representation Expenses','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (78,10,'5020321000','Semi-Expendable Machinery and Equipment Expenses','','0');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (79,1,'5021100000','Professional Services','','1');
insert  into `ppmp_obj`(`id`,`obj_id`,`code`,`title`,`description`,`active`) values (80,63,'5029999000','Other Maintenance and Operating Expenses','','0');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
