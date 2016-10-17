CREATE DATABASE  IF NOT EXISTS `mydb` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `mydb`;
-- MySQL dump 10.13  Distrib 5.5.24, for osx10.5 (i386)
--
-- Host: 127.0.0.1    Database: mydb
-- ------------------------------------------------------
-- Server version	5.5.29

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `division`
--

DROP TABLE IF EXISTS `division`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `division` (
  `division_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`division_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `division`
--

LOCK TABLES `division` WRITE;
/*!40000 ALTER TABLE `division` DISABLE KEYS */;
INSERT INTO `division` VALUES (1,'Operations'),(2,'Product');
/*!40000 ALTER TABLE `division` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission`
--

DROP TABLE IF EXISTS `permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission` (
  `permission` varchar(30) NOT NULL,
  PRIMARY KEY (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission`
--

LOCK TABLES `permission` WRITE;
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;
INSERT INTO `permission` VALUES ('admin'),('donation-edit'),('donation-view'),('event-edit'),('event-view'),('member-edit'),('member-view'),('panels-view'),('query');
/*!40000 ALTER TABLE `permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role` (
  `userid` varchar(45) NOT NULL,
  `role` varchar(30) NOT NULL,
  PRIMARY KEY (`userid`,`role`),
  KEY `userid_idx` (`userid`),
  KEY `xxx_idx` (`role`),
  CONSTRAINT `role_fk2` FOREIGN KEY (`role`) REFERENCES `role` (`role`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `userid_fk` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES ('marc','admin'),('aaa','member-maintenance'),('mary','member-maintenance'),('aaa','office'),('aaa','panels');
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `specialty`
--

DROP TABLE IF EXISTS `specialty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specialty` (
  `specialty_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`specialty_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `specialty`
--

LOCK TABLES `specialty` WRITE;
/*!40000 ALTER TABLE `specialty` DISABLE KEYS */;
INSERT INTO `specialty` VALUES (1,'brush-foots'),(2,'gossamer-wings'),(3,'sulphurs'),(4,'swallowtails'),(5,'whites');
/*!40000 ALTER TABLE `specialty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `division_id` int(11) NOT NULL,
  PRIMARY KEY (`department_id`),
  KEY `fk_department_division1_idx` (`division_id`),
  CONSTRAINT `fk_department_division1` FOREIGN KEY (`division_id`) REFERENCES `division` (`division_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department`
--

LOCK TABLES `department` WRITE;
/*!40000 ALTER TABLE `department` DISABLE KEYS */;
INSERT INTO `department` VALUES (1,'Accounting',1),(2,'Shipping',1),(3,'Sales',2);
/*!40000 ALTER TABLE `department` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager`
--

DROP TABLE IF EXISTS `manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager` (
  `last` varchar(45) NOT NULL,
  `first` varchar(45) DEFAULT NULL,
  `office` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`last`),
  UNIQUE KEY `ccc_UNIQUE` (`last`),
  UNIQUE KEY `ddd_UNIQUE` (`first`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager`
--

LOCK TABLES `manager` WRITE;
/*!40000 ALTER TABLE `manager` DISABLE KEYS */;
INSERT INTO `manager` VALUES ('Smith','John','TOKYO');
/*!40000 ALTER TABLE `manager` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 trigger table_manager_trigger1
      before insert on manager for each row begin
      call check_table_manager(new.last, new.first, new.office); end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 trigger table_manager_trigger2
      before update on manager for each row begin
      call check_table_manager(new.last, new.first, new.office); end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `userid` varchar(45) NOT NULL,
  `last` varchar(45) NOT NULL,
  `first` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password_hash` varchar(60) NOT NULL,
  `verification_hash` varchar(60) NOT NULL,
  `expiration` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `extratime` int(11) NOT NULL DEFAULT '0',
  `phone` varchar(45) NOT NULL,
  `phone_method` enum('sms','voice') NOT NULL DEFAULT 'sms',
  `identity` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `userid_UNIQUE` (`userid`),
  UNIQUE KEY `phone_UNIQUE` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 trigger table_user_trigger1
      before insert on user for each row begin
      call check_table_user(new.userid, new.last, new.first, new.email, new.password_hash, new.verification_hash, new.expiration, new.extratime, new.phone, new.phone_method); end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 trigger table_user_trigger2
      before update on user for each row begin
      call check_table_user(new.userid, new.last, new.first, new.email, new.password_hash, new.verification_hash, new.expiration, new.extratime, new.phone, new.phone_method); end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `query`
--

DROP TABLE IF EXISTS `query`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `query` (
  `query_id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `query` text NOT NULL,
  `permission` varchar(30) NOT NULL,
  PRIMARY KEY (`query_id`),
  UNIQUE KEY `name_UNIQUE` (`title`),
  KEY `permission_fk2` (`permission`),
  CONSTRAINT `permission_fk2` FOREIGN KEY (`permission`) REFERENCES `permission` (`permission`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `query`
--

LOCK TABLES `query` WRITE;
/*!40000 ALTER TABLE `query` DISABLE KEYS */;
INSERT INTO `query` VALUES (10,'General','Member Directory','select concat(last, \', \', first) as name, street, city, state, since from member order by last, first','member-view'),(11,'PHP Files','CWA Panels','file report-panels2.php','query'),(12,'PHP Files','Specialties','file report-specialty.php','query'),(13,'General','Specialty Count','select name, count(specialty_id) as count\r\nfrom member join specialty using (specialty_id)\r\ngroup by specialty_id order by name','member-view'),(14,'General','Members with most seniority','select last, first, since from member order by since limit 10;','member-view'),(15,'General','Members in New Jersey','select last, first, state from member where state = \'NJ\' order by last, first;','member-view');
/*!40000 ALTER TABLE `query` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skill`
--

DROP TABLE IF EXISTS `skill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skill` (
  `employee_id` int(11) NOT NULL,
  `os` varchar(45) NOT NULL,
  `language` varchar(45) NOT NULL,
  PRIMARY KEY (`employee_id`,`os`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skill`
--

LOCK TABLES `skill` WRITE;
/*!40000 ALTER TABLE `skill` DISABLE KEYS */;
INSERT INTO `skill` VALUES (1,'Linux','SQL'),(1,'MacOS','PHP'),(1,'Windows',''),(2,'','C++'),(2,'','Java'),(2,'','Lua'),(2,'','SQL'),(2,'Linux','PHP'),(2,'Windows','Python');
/*!40000 ALTER TABLE `skill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `panel`
--

DROP TABLE IF EXISTS `panel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `panel` (
  `panel_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`panel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `panel`
--

LOCK TABLES `panel` WRITE;
/*!40000 ALTER TABLE `panel` DISABLE KEYS */;
/*!40000 ALTER TABLE `panel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `city` (
  `city` varchar(45) NOT NULL,
  `state` varchar(45) NOT NULL,
  `population` int(11) NOT NULL,
  `mayor` varchar(45) NOT NULL,
  `governor` varchar(45) DEFAULT NULL,
  `airline` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`city`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `city`
--

LOCK TABLES `city` WRITE;
/*!40000 ALTER TABLE `city` DISABLE KEYS */;
INSERT INTO `city` VALUES ('Akron','OH',199110,'Plusquellic','Kasich','United','800-864-8331'),('Columbus','IN',44061,'Brown','Pence','Delta','800-221-1212'),('Columbus','OH',787033,'Coleman','Kasich','United','800-864-8331');
/*!40000 ALTER TABLE `city` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `msg` varchar(255) NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` VALUES (2,'2013-05-08 18:55:21','insert TOKYO'),(6,'2013-05-13 17:42:06','0'),(8,'2013-05-13 17:43:56','0'),(9,'2013-05-13 17:45:31','insert TOKYO');
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permission` (
  `role` varchar(30) NOT NULL,
  `permission` varchar(30) NOT NULL,
  PRIMARY KEY (`role`,`permission`),
  KEY `role_fk_idx` (`role`),
  KEY `permission_fk_idx` (`permission`),
  CONSTRAINT `permission_fk` FOREIGN KEY (`permission`) REFERENCES `permission` (`permission`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `role_fk` FOREIGN KEY (`role`) REFERENCES `role` (`role`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission`
--

LOCK TABLES `role_permission` WRITE;
/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;
INSERT INTO `role_permission` VALUES ('admin','admin'),('board-member','donation-view'),('board-member','event-view'),('board-member','member-view'),('donation-maintenance','donation-edit'),('donation-maintenance','donation-view'),('member-maintenance','member-edit'),('member-maintenance','member-view'),('panels','panels-view');
/*!40000 ALTER TABLE `role_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `panelist`
--

DROP TABLE IF EXISTS `panelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `panelist` (
  `panelist_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`panelist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `panelist`
--

LOCK TABLES `panelist` WRITE;
/*!40000 ALTER TABLE `panelist` DISABLE KEYS */;
/*!40000 ALTER TABLE `panelist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_specialty`
--

DROP TABLE IF EXISTS `member_specialty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member_specialty` (
  `member_id` int(11) NOT NULL,
  `specialty_id` varchar(45) NOT NULL,
  PRIMARY KEY (`member_id`,`specialty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_specialty`
--

LOCK TABLES `member_specialty` WRITE;
/*!40000 ALTER TABLE `member_specialty` DISABLE KEYS */;
INSERT INTO `member_specialty` VALUES (1,'1'),(1,'2'),(1,'5'),(4,'2'),(4,'4'),(24,'4'),(24,'5');
/*!40000 ALTER TABLE `member_specialty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `last` varchar(45) NOT NULL,
  `first` varchar(45) NOT NULL,
  `street` varchar(45) NOT NULL DEFAULT 'tmp',
  `city` varchar(45) NOT NULL DEFAULT 'tmp',
  `state` varchar(45) NOT NULL DEFAULT 'tmp',
  `specialty_id` int(11) DEFAULT NULL,
  `billing` enum('month','year','recurring') NOT NULL DEFAULT 'year',
  `premium` tinyint(4) NOT NULL DEFAULT '0',
  `contact` enum('phone','email','mail','none') NOT NULL DEFAULT 'email',
  `since` date NOT NULL DEFAULT '0000-00-00',
  `email` varchar(200) DEFAULT NULL,
  `subscribed` tinyint(4) NOT NULL DEFAULT '1',
  `token` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`member_id`),
  KEY `specialty_id_idx` (`specialty_id`),
  CONSTRAINT `specialty_id` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`specialty_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member`
--

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
INSERT INTO `member` VALUES (1,'Irwin','Blaze','4176 Habitant Rd.','Brownsville','VT',5,'recurring',1,'phone','2001-11-13',NULL,1,NULL),(3,'Flynn','Berk','Ap #365-2083 Ridiculus Rd.','Evanston','OK',3,'year',0,'email','2010-09-24','Flynn@basepath.com',0,'e11883686dcf'),(4,'Wolfe','Venus','8998 Maecenas St.','Worland','FL',3,'recurring',0,'email','1998-07-03',NULL,1,NULL),(6,'Cain','Willa','Ap #947-9090 Ut Street','Visalia','DE',2,'year',0,'email','2008-09-01',NULL,1,NULL),(7,'Morin','Allistair','336-3621 Congue Rd.','Flagstaff','ID',1,'recurring',1,'mail','1994-03-23',NULL,1,NULL),(8,'Frederick','Briar','P.O. Box 358, 7095 Neque. Street','Lubbock','NJ',5,'',0,'','2002-10-22','Frederick@basepath.com',0,'8bf49995d603'),(9,'Mendoza','Oliver','4134 Fermentum St.','West Memphis','UT',2,'year',0,'email','2001-08-25',NULL,1,NULL),(10,'Brewer','Amy','P.O. Box 603, 5375 In, St.','Latrobe','VA',4,'year',0,'email','2005-04-24',NULL,1,NULL),(11,'Powell','Jana','Ap #525-169 Id, Av.','Westlake Village','FL',3,'year',0,'email','1997-05-15',NULL,1,NULL),(12,'Mann','Zephania','1648 Rhoncus Street','Duarte','DC',5,'year',0,'email','2001-06-04',NULL,1,NULL),(13,'Brock','Kane','414-3841 Sed St.','Bandon','VT',5,'year',0,'email','1998-09-23',NULL,1,NULL),(14,'Wiley','Noel','4678 Facilisis Road','Brigham City','KS',2,'year',0,'email','2012-05-03',NULL,1,NULL),(15,'Morning','Juliet','219-9202 Eget Rd.','El Cerrito','IN',3,'year',0,'email','1999-02-18',NULL,1,NULL),(16,'Holman','Hilda','Ap #479-4482 Per Avenue','Weymouth','SC',5,'year',0,'email','2009-03-17',NULL,1,NULL),(18,'Conner','Avye','Ap #764-3261 Proin St.','Norman','AL',3,'year',0,'email','2001-03-26',NULL,1,NULL),(19,'Roth','Ingrid','665-4899 Arcu St.','Shamokin','NJ',5,'year',0,'email','2009-06-20',NULL,1,NULL),(20,'Galloway','Isaiah','P.O. Box 288, 6946 Feugiat Av.','Irwindale','WA',4,'year',0,'email','1998-01-14',NULL,1,NULL),(21,'Cash','Brittany','551-1430 Consectetuer Road','Norfolk','DE',1,'year',0,'email','1999-12-02',NULL,1,NULL),(22,'Dodson','Veronica','9550 Hendrerit. Street','Temecula','MS',1,'year',0,'email','2001-08-05',NULL,1,NULL),(23,'Johnson','Quinlan','Ap #905-9298 Nunc Avenue','Vermillion','NH',4,'year',0,'email','2011-01-10',NULL,1,NULL),(24,'Solomon-Walters','Eleanor','P.O. Box 909, 3112 Vehicula Avenue','Moline','AK',5,'year',0,'email','2006-04-15',NULL,1,NULL),(25,'Payne','Cassady','Ap #494-6506 Nec, Rd.','Phenix City','KS',4,'year',0,'email','2005-07-01',NULL,1,NULL),(26,'Jensen','Rhona','Ap #652-4802 Curabitur Rd.','Ogden','NE',3,'year',0,'email','2000-08-28',NULL,1,NULL),(28,'Mitchell','Noble','412-2587 Amet Av.','Aguadilla','AZ',2,'year',0,'email','1996-10-12',NULL,1,NULL),(29,'Cameron','Kaseem','P.O. Box 187, 6523 Urna. St.','Mobile','PA',2,'year',0,'email','2008-07-16',NULL,1,NULL),(30,'Montoya','Peter','974-4133 Consectetuer, Avenue','Olympia','NC',2,'year',0,'email','1995-09-09',NULL,1,NULL),(31,'Brady','Elliott','P.O. Box 526, 595 Vulputate Ave','Guañica','MS',4,'year',0,'email','2001-02-11',NULL,1,NULL),(32,'Vincent','Lysandra','Ap #737-4058 Non Street','Fontana','IN',5,'year',0,'email','2005-09-27',NULL,1,NULL),(33,'Kane','Tyler','1633 Sit St.','Auburn','IA',1,'year',0,'email','2004-11-16',NULL,1,NULL),(34,'Pate','Noelle','6150 Enim. Avenue','Branson','IA',3,'year',0,'email','2000-12-21',NULL,1,NULL),(35,'Hurst','Rama','428-6978 Mauris Road','Aurora','AK',4,'year',0,'email','2008-09-08',NULL,1,NULL),(36,'Golden','Gillian','230-9076 Vitae, Av.','Citrus Heights','IA',1,'year',0,'email','1996-10-17',NULL,1,NULL),(37,'Johnston','Boris','P.O. Box 691, 293 Aenean St.','Nichols Hills','PA',2,'year',0,'email','2004-12-10',NULL,1,NULL),(38,'Fletcher','Cruz','Ap #547-4176 Nunc St.','Frederick','NE',1,'year',0,'email','2007-07-13','Fletcher@basepath.com',1,'dd8bf3f526dd'),(39,'Henderson','Tanisha','Ap #199-7972 Pede Rd.','Nogales','IN',3,'year',0,'email','2007-03-27',NULL,1,NULL),(40,'Mcclure','Preston','2362 In, Street','Torrington','WI',4,'year',0,'email','1996-10-15',NULL,1,NULL),(41,'Neal','Elmo','281-5076 Enim. Av.','Thomasville','NM',4,'year',0,'email','1998-07-05',NULL,1,NULL),(42,'Holmes','Benedict','8717 Urna Street','Bandon','AZ',1,'year',0,'email','1995-11-02',NULL,1,NULL),(43,'Oneil','Francesca','P.O. Box 971, 5179 Nulla Street','Atwater','GA',4,'year',0,'email','2008-09-06',NULL,1,NULL),(46,'Ball','Bertha','575-7633 Curabitur Av.','Texarkana','NH',1,'year',0,'email','2009-06-24',NULL,1,NULL),(47,'Foley','Leilani','2337 Lacus. Road','Georgetown','NJ',3,'year',0,'email','2011-01-17','Foley@basepath.com',1,'ae467028fc9a'),(48,'Rosario','Cheryl','P.O. Box 810, 5710 Fringilla St.','Moorhead','TX',2,'year',0,'email','2008-01-22',NULL,1,NULL),(49,'Zamora','Winter','783-4798 Libero. St.','Victoria','MT',3,'year',0,'email','2008-07-08',NULL,1,NULL),(50,'Kim','Jermaine','129-9815 Nec St.','Portland','LA',4,'year',0,'email','2008-12-21',NULL,1,NULL),(51,'Graham','Candice','P.O. Box 995, 8125 Ligula. St.','San Marino','NH',3,'year',0,'email','2005-11-12',NULL,1,NULL),(52,'Pitts','Latifah','Ap #118-4597 Tincidunt Ave','Charlotte','WV',2,'year',0,'email','2003-03-21',NULL,1,NULL),(53,'Lambert','Ivana','Ap #949-2148 Tellus St.','Lubbock','NC',4,'year',0,'email','2012-08-03',NULL,1,NULL),(54,'Bradshaw','Ainsley','P.O. Box 962, 8625 Et Av.','Spartanburg','PA',3,'year',0,'email','2007-04-09',NULL,1,NULL),(55,'Koch','Jillian','P.O. Box 726, 1626 Non, St.','Nogales','TX',2,'year',0,'email','2004-12-06',NULL,1,NULL),(56,'Mcmahon','Gray','Ap #175-120 Sed St.','Reno','NJ',4,'year',0,'email','1995-10-17',NULL,1,NULL),(57,'Frye','Nathaniel','Ap #749-142 Ridiculus St.','Jackson','NH',5,'year',0,'email','2006-06-11','Frye@basepath.com',1,'e71962e1fa31'),(59,'Jennings','Meghan','Ap #393-1177 Libero St.','Poughkeepsie','AK',2,'year',0,'email','2003-05-08',NULL,1,NULL),(60,'Hanson','Hashim','965-7901 Vitae St.','Juneau','ME',5,'year',0,'email','2000-12-20',NULL,1,NULL),(61,'Conrad','Darrel','P.O. Box 695, 8588 Orci, Road','Pico Rivera','AK',5,'year',0,'email','2004-10-06',NULL,1,NULL),(62,'Burris','Kaden','Ap #361-2433 Hendrerit. Avenue','North Las Vegas','AL',5,'year',0,'email','2006-09-15',NULL,1,NULL),(63,'Gonzales','Hedy','212-7167 Massa Ave','East Hartford','NV',3,'year',0,'email','2002-09-03',NULL,1,NULL),(64,'Mcpherson','Piper','P.O. Box 518, 7196 Velit St.','Bakersfield','DC',5,'year',0,'email','2001-08-03',NULL,1,NULL),(65,'Whitaker','Edward','P.O. Box 811, 8252 Luctus Rd.','Johnstown','MA',2,'year',0,'email','2004-05-07',NULL,1,NULL),(66,'Pratt','Rama','Ap #724-5603 Posuere Road','Dunkirk','WV',5,'year',0,'email','1994-07-20',NULL,1,NULL),(67,'Glass','Kitra','Ap #161-1586 Nulla Ave','Garland','CT',5,'year',0,'email','2007-07-17',NULL,1,NULL),(68,'Blanchard','Autumn','Ap #816-1342 Facilisis Road','Kahului','HI',3,'year',0,'email','1998-06-19',NULL,1,NULL),(69,'Estes','Xandra','882-2058 Ac Rd.','West Hartford','CO',3,'year',0,'email','2011-06-21',NULL,1,NULL),(70,'Bird','Jamal','P.O. Box 377, 228 Ullamcorper, Avenue','Fayetteville','NY',5,'year',0,'email','1998-01-10',NULL,1,NULL),(71,'Mcmahon','Nola','P.O. Box 844, 131 Nulla St.','Claremore','IL',1,'year',0,'email','2007-05-26',NULL,1,NULL),(72,'Poole','Kennan','P.O. Box 635, 9383 Semper St.','Dover','NE',1,'year',0,'email','2000-01-10',NULL,1,NULL),(73,'Hancock','Macaulay','Ap #689-3947 Sit St.','Monroe','WV',1,'year',0,'email','2001-11-08',NULL,1,NULL),(74,'Patrick','Kitra','7898 In Avenue','Rialto','RI',5,'year',0,'email','2008-01-23',NULL,1,NULL),(75,'Alvarado','Zia','P.O. Box 786, 5969 Lobortis Rd.','San Fernando','OH',3,'year',0,'email','2012-05-24',NULL,1,NULL),(76,'Gibson','Philip','364-5230 Sed Avenue','Vallejo','ND',5,'year',0,'email','1996-02-10',NULL,1,NULL),(77,'Stanton','Nina','751-1946 Lacus. Road','Dickinson','MS',5,'year',0,'email','1999-05-04',NULL,1,NULL),(78,'Conrad','Jocelyn','P.O. Box 230, 6554 Pede. Avenue','Newton','WA',5,'year',0,'email','2001-08-28',NULL,1,NULL),(79,'Hurst','Donna','P.O. Box 432, 9710 Mollis Ave','Reno','IN',2,'year',0,'email','1995-04-13',NULL,1,NULL),(81,'Peters','Dana','P.O. Box 425, 8169 Cum Ave','Saipan','WY',2,'year',0,'email','1998-11-22',NULL,1,NULL),(82,'Cabrera','Jaden','P.O. Box 322, 6087 Id, Street','Bozeman','WY',3,'year',0,'email','1995-02-08',NULL,1,NULL),(83,'Reese','Sandra','669-9239 Phasellus St.','Sioux City','MD',2,'year',0,'email','1994-05-17',NULL,1,NULL),(84,'Norris','Brenna','3314 Mauris Avenue','Hackensack','NJ',2,'year',0,'email','1994-04-10',NULL,1,NULL),(85,'Goodwin','Ginger','520-9414 Curae; Av.','New York','AK',5,'year',0,'email','2009-12-16',NULL,1,NULL),(86,'Melendez','Xandra','P.O. Box 924, 9170 Lacinia Road','Oakland','TN',2,'year',0,'email','2009-07-03',NULL,1,NULL),(87,'Bauer','Ria','P.O. Box 485, 9629 Vestibulum, St.','Gardner','SC',3,'year',0,'email','2011-05-06',NULL,1,NULL),(88,'Hoffman','Caesar','3070 Mattis Rd.','Calabasas','VT',5,'year',0,'email','2008-04-06',NULL,1,NULL),(89,'Cooke','Victor','Ap #983-1911 Ligula. St.','Ogden','VT',4,'year',0,'email','2012-06-06',NULL,1,NULL),(90,'Kelly','Mary','P.O. Box 149, 3034 Tempor Street','Long Beach','AL',3,'year',0,'email','2005-07-19',NULL,1,NULL),(91,'Flynn','Leilani','Ap #740-3986 Mollis. Road','Saratoga Springs','TN',1,'year',0,'email','2010-04-27','Flynn@basepath.com',1,'2be9e86797b9'),(92,'Porter','Alvin','P.O. Box 962, 2593 Fusce Street','Highland Park','NM',2,'year',0,'email','2008-12-19',NULL,1,NULL),(93,'Daniel','Beverly','892-1686 Mattis. St.','Peru','ME',3,'year',0,'email','2001-11-05',NULL,1,NULL),(94,'Fuentes','Mufutau','P.O. Box 348, 899 Sed Road','Lynn','CA',5,'year',0,'email','1998-10-06','Fuentes@basepath.com',1,'4a6a22a0be88'),(95,'Russell','Russell','P.O. Box 247, 9847 Elit, St.','Grafton','NV',4,'year',0,'email','2006-09-14',NULL,1,NULL),(96,'Kelly','Quyn','1063 In Street','Clairton','AK',2,'year',0,'email','2000-04-13',NULL,1,NULL),(97,'Manning','Colleen','4830 Dui. Street','Hartford','IL',4,'year',0,'email','2000-05-24',NULL,1,NULL),(98,'Sweet','Gary','P.O. Box 494, 7215 Ut Ave','Easton','NJ',5,'year',0,'email','1994-09-11',NULL,1,NULL),(99,'Mcintyre','Cameron','9982 Arcu. St.','Hermosa Beach','SC',2,'year',0,'email','2007-07-13',NULL,1,NULL),(100,'Maddox','September','P.O. Box 996, 3688 Pede St.','Aspen','MS',3,'year',0,'email','2007-03-21',NULL,1,NULL);
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person` (
  `last` varchar(45) NOT NULL,
  `first` varchar(45) DEFAULT NULL,
  `street` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `moderator` tinyint(1) DEFAULT NULL,
  `donor` tinyint(1) DEFAULT NULL,
  `panelist` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`last`),
  KEY `fk_person_city1_idx` (`city`,`state`),
  CONSTRAINT `fk_person_city1` FOREIGN KEY (`city`, `state`) REFERENCES `city` (`city`, `state`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person`
--

LOCK TABLES `person` WRITE;
/*!40000 ALTER TABLE `person` DISABLE KEYS */;
/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `role` varchar(30) NOT NULL,
  PRIMARY KEY (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES ('admin'),('board-member'),('donation-maintenance'),('event-coordinator'),('member-maintenance'),('office'),('panels');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interest`
--

DROP TABLE IF EXISTS `interest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interest` (
  `interest_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `member_id` int(11) NOT NULL,
  PRIMARY KEY (`interest_id`),
  UNIQUE KEY `unique_interest` (`name`,`member_id`),
  KEY `member_id_idx` (`member_id`),
  CONSTRAINT `member_id` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interest`
--

LOCK TABLES `interest` WRITE;
/*!40000 ALTER TABLE `interest` DISABLE KEYS */;
INSERT INTO `interest` VALUES (3,'baseball',24),(9,'football',24),(10,'knitting',77);
/*!40000 ALTER TABLE `interest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) DEFAULT NULL,
  `last` varchar(45) NOT NULL,
  `first` varchar(45) DEFAULT NULL,
  `manager` int(11) DEFAULT NULL,
  `assistant` int(11) DEFAULT NULL,
  PRIMARY KEY (`employee_id`),
  KEY `fk_employee_department_idx` (`department_id`),
  KEY `fk_employee_employee1_idx` (`manager`),
  KEY `fk_employee_employee2_idx` (`assistant`),
  CONSTRAINT `fk_employee_department` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_employee_employee1` FOREIGN KEY (`manager`) REFERENCES `employee` (`employee_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_employee_employee2` FOREIGN KEY (`assistant`) REFERENCES `employee` (`employee_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee`
--

LOCK TABLES `employee` WRITE;
/*!40000 ALTER TABLE `employee` DISABLE KEYS */;
INSERT INTO `employee` VALUES (1,2,'Smith','John',4,NULL),(2,2,'Jones','Mary',4,NULL),(3,1,'Gonzalez','Ivan',4,NULL),(4,NULL,'Chu','Nancy',NULL,2),(5,3,'Doe','Jane',NULL,2);
/*!40000 ALTER TABLE `employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `panel_has_panelist`
--

DROP TABLE IF EXISTS `panel_has_panelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `panel_has_panelist` (
  `panel_id` int(11) NOT NULL,
  `panelist_id` int(11) NOT NULL,
  PRIMARY KEY (`panel_id`,`panelist_id`),
  KEY `fk_panel_has_panelist_panelist1_idx` (`panelist_id`),
  KEY `fk_panel_has_panelist_panel1_idx` (`panel_id`),
  CONSTRAINT `fk_panel_has_panelist_panel1` FOREIGN KEY (`panel_id`) REFERENCES `panel` (`panel_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_panel_has_panelist_panelist1` FOREIGN KEY (`panelist_id`) REFERENCES `panelist` (`panelist_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `panel_has_panelist`
--

LOCK TABLES `panel_has_panelist` WRITE;
/*!40000 ALTER TABLE `panel_has_panelist` DISABLE KEYS */;
/*!40000 ALTER TABLE `panel_has_panelist` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-07-12 10:12:59
