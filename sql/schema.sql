-- MySQL dump 10.13  Distrib 5.6.23, for FreeBSD10.0 (amd64)
--
-- Host: localhost    Database: bgparchive
-- ------------------------------------------------------
-- Server version	5.6.23-log

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
-- Table structure for table `bgp_data`
--

DROP TABLE IF EXISTS `bgp_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bgp_data` (
  `collector_type_id` int(11) NOT NULL,
  `file_time` int(11) NOT NULL,
  `web_size` varchar(50) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`collector_type_id`,`file_time`),
  KEY `file_time_idx` (`file_time`),
  KEY `collector_type_filetime_idx` (`collector_type_id`,`file_time`),
  KEY `collector_type_idx` (`collector_type_id`),
  CONSTRAINT `bgp_data_ibfk_3` FOREIGN KEY (`collector_type_id`) REFERENCES `collector_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bgp_month_summary`
--

DROP TABLE IF EXISTS `bgp_month_summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bgp_month_summary` (
  `collector_id` int(11) NOT NULL,
  `bgp_type_id` int(11) NOT NULL,
  `month_time` int(11) NOT NULL,
  `downloaded` int(11) NOT NULL,
  `missing` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`collector_id`,`bgp_type_id`,`month_time`),
  KEY `bgp_type_id` (`bgp_type_id`),
  CONSTRAINT `bgp_month_summary_ibfk_1` FOREIGN KEY (`collector_id`) REFERENCES `collectors` (`id`),
  CONSTRAINT `bgp_month_summary_ibfk_2` FOREIGN KEY (`bgp_type_id`) REFERENCES `bgp_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bgp_types`
--

DROP TABLE IF EXISTS `bgp_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bgp_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `path` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `path` (`path`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collector_type`
--

DROP TABLE IF EXISTS `collector_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collector_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `collector_id` int(11) NOT NULL,
  `bgp_type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collector_id` (`collector_id`,`bgp_type_id`),
  KEY `collector_bgp_type` (`collector_id`,`bgp_type_id`),
  KEY `collector_type_ibfk_2` (`bgp_type_id`),
  CONSTRAINT `collector_type_ibfk_1` FOREIGN KEY (`collector_id`) REFERENCES `collectors` (`id`),
  CONSTRAINT `collector_type_ibfk_2` FOREIGN KEY (`bgp_type_id`) REFERENCES `bgp_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collectors`
--

DROP TABLE IF EXISTS `collectors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collectors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `path` varchar(50) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `path` (`path`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `collectors_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dump_info`
--

DROP TABLE IF EXISTS `dump_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dump_info` (
  `collector_type_id` int(11) NOT NULL,
  `period` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  PRIMARY KEY (`collector_type_id`),
  CONSTRAINT `dump_info_ibfk_1` FOREIGN KEY (`collector_type_id`) REFERENCES `collector_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `file_stats`
--

DROP TABLE IF EXISTS `file_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `collector_id` int(11) NOT NULL,
  `bgp_type_id` int(11) NOT NULL,
  `file_time` int(11) NOT NULL,
  `last_index` int(11) NOT NULL,
  `download_start` int(11) NOT NULL,
  `download_stop` int(11) NOT NULL,
  `in_db` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `collector_id` (`collector_id`),
  KEY `bgp_type_id` (`bgp_type_id`),
  CONSTRAINT `file_stats_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `file_stats_ibfk_2` FOREIGN KEY (`collector_id`) REFERENCES `collectors` (`id`),
  CONSTRAINT `file_stats_ibfk_3` FOREIGN KEY (`bgp_type_id`) REFERENCES `bgp_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2943313 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `info`
--

DROP TABLE IF EXISTS `info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info` (
  `path` varchar(50) NOT NULL,
  PRIMARY KEY (`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `on_web_frequency`
--

DROP TABLE IF EXISTS `on_web_frequency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `on_web_frequency` (
  `project_id` int(11) NOT NULL,
  `bgp_type_id` int(11) NOT NULL,
  `on_web_freq` int(11) NOT NULL,
  `offset` int(11) NOT NULL,
  PRIMARY KEY (`project_id`,`bgp_type_id`),
  KEY `bgp_type_id` (`bgp_type_id`),
  CONSTRAINT `on_web_frequency_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `on_web_frequency_ibfk_2` FOREIGN KEY (`bgp_type_id`) REFERENCES `bgp_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `process_stats`
--

DROP TABLE IF EXISTS `process_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `process_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `collector_id` int(11) NOT NULL,
  `bgp_type_id` int(11) NOT NULL,
  `month_time` int(11) NOT NULL,
  `process_start` int(11) NOT NULL,
  `process_stop` int(11) NOT NULL,
  `bgp_download` int(11) NOT NULL,
  `index_download` int(11) NOT NULL,
  `downloaded` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `wait_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `collector_id` (`collector_id`),
  KEY `bgp_type_id` (`bgp_type_id`),
  CONSTRAINT `process_stats_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `process_stats_ibfk_2` FOREIGN KEY (`collector_id`) REFERENCES `collectors` (`id`),
  CONSTRAINT `process_stats_ibfk_3` FOREIGN KEY (`bgp_type_id`) REFERENCES `bgp_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2577669 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `processes`
--

DROP TABLE IF EXISTS `processes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `processes` (
  `PID` int(11) NOT NULL,
  `collector_id` int(11) NOT NULL,
  `bgp_type_id` int(11) NOT NULL,
  `month_time` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`collector_id`,`bgp_type_id`,`month_time`),
  KEY `bgp_type_id` (`bgp_type_id`),
  CONSTRAINT `processes_ibfk_1` FOREIGN KEY (`collector_id`) REFERENCES `collectors` (`id`),
  CONSTRAINT `processes_ibfk_2` FOREIGN KEY (`bgp_type_id`) REFERENCES `bgp_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `path` varchar(50) NOT NULL,
  `file_ext` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `path` (`path`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-08-18  9:51:35
