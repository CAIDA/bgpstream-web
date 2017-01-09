-- MySQL dump 10.13  Distrib 5.6.23, for FreeBSD10.0 (amd64)
--
-- Host: localhost    Database: bgparchive
-- ------------------------------------------------------
-- Server version	5.6.23-log
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping routines for database 'bgparchive'
--
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`routing`@`localhost` PROCEDURE `available_data`()
BEGIN
set time_zone='+0:0';

SELECT collectors.name, t1.begin, t2.end 
FROM  
(SELECT collector_id, FROM_UNIXTIME(MIN(file_time), '%Y-%c-%d') as begin
 FROM bgp_data 
 GROUP BY collector_id) as t1, 
(SELECT collector_id, FROM_UNIXTIME(MAX(file_time), '%Y-%c-%d') as end 
 FROM bgp_data 
 GROUP BY collector_id) as t2,
collectors 
WHERE t1.collector_id = collectors.id AND t2.collector_id = collectors.id;

END ;;
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
CREATE DEFINER=`routing`@`localhost` PROCEDURE `cur_processes`()
BEGIN
set time_zone='+0:0';
SELECT FROM_UNIXTIME(month_time,'%Y.%m') AS month,
       collectors.name AS collector,
       bgp_types.name as type,
       ts AS timestamp
FROM   processes JOIN projects JOIN collectors JOIN bgp_types
WHERE  processes.collector_id = collectors.id AND
       collectors.project_id = projects.id AND
       processes.bgp_type_id = bgp_types.id
ORDER BY month, projects.name, type, collector;
END ;;
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
CREATE DEFINER=`routing`@`localhost` PROCEDURE `month_status`()
BEGIN
set time_zone='+0:0';
SELECT project, collector, type, indb, expected, exp_to_dwl
FROM (
SELECT projects.name AS project, collectors.name AS collector, 
       bgp_types.name AS type, sum(downloaded) AS indb,
       FLOOR((UNIX_TIMESTAMP() - month_time - on_web_frequency.offset) / on_web_frequency.on_web_freq) + 1 AS expected,
       FLOOR((UNIX_TIMESTAMP() - month_time - on_web_frequency.offset) / on_web_frequency.on_web_freq) + 1 - sum(downloaded)  AS exp_to_dwl
FROM   bgp_month_summary join collectors join projects join bgp_types join on_web_frequency 
WHERE bgp_month_summary.collector_id=collectors.id     AND
            bgp_month_summary.bgp_type_id=bgp_types.id AND
            collectors.project_id=projects.id          AND
            on_web_frequency.project_id=projects.id    AND
            on_web_frequency.bgp_type_id=bgp_types.id  AND
            FROM_UNIXTIME(month_time,'%Y.%m') LIKE DATE_FORMAT(NOW(), '%Y.%m')
GROUP BY project, collector, type     
) AS t
ORDER BY exp_to_dwl, project, type;
END ;;
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
CREATE DEFINER=`routing`@`localhost` PROCEDURE `pending_collectors`()
BEGIN
set time_zone='+0:0';

SELECT project, type, COUNT(*)
FROM (
SELECT projects.name AS project, collectors.name AS collector, 
       bgp_types.name AS type, sum(downloaded) AS indb,
       FLOOR((UNIX_TIMESTAMP() - month_time - on_web_frequency.offset) / on_web_frequency.on_web_freq) + 1 AS expected,
       FLOOR((UNIX_TIMESTAMP() - month_time - on_web_frequency.offset) / on_web_frequency.on_web_freq) + 1 - sum(downloaded)  AS exp_to_dwl
FROM   bgp_month_summary join collectors join projects join bgp_types join on_web_frequency 
WHERE bgp_month_summary.collector_id=collectors.id     AND
            bgp_month_summary.bgp_type_id=bgp_types.id AND
            collectors.project_id=projects.id          AND
            on_web_frequency.project_id=projects.id    AND
            on_web_frequency.bgp_type_id=bgp_types.id  AND
            FROM_UNIXTIME(month_time,'%Y.%m') LIKE DATE_FORMAT(NOW(), '%Y.%m')
GROUP BY project, collector, type     
) AS t
WHERE t.exp_to_dwl != 0
GROUP BY project, type     
ORDER BY project, type;
END ;;
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
CREATE DEFINER=`routing`@`localhost` PROCEDURE `run_months`()
BEGIN
set time_zone='+0:0';
SELECT FROM_UNIXTIME(month_time,'%Y.%m') AS month,
       projects.name AS project,  bgp_types.name as type,
       COUNT(collectors.name) AS coll_sum       
FROM   processes JOIN projects JOIN collectors  JOIN bgp_types
WHERE  processes.collector_id = collectors.id AND
       collectors.project_id = projects.id AND
       processes.bgp_type_id = bgp_types.id
GROUP BY month, project, type;
END ;;
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
CREATE DEFINER=`routing`@`localhost` PROCEDURE `summary`()
BEGIN
set time_zone='+0:0';
SELECT projects.name AS project, collectors.name AS collector, 
            bgp_types.name AS type, sum(missing) AS missing, sum(downloaded) AS indb
FROM   bgp_month_summary join collectors join projects join bgp_types 
WHERE bgp_month_summary.collector_id=collectors.id   AND
            bgp_month_summary.bgp_type_id=bgp_types.id AND
            collectors.project_id=projects.id                           AND
            FROM_UNIXTIME(month_time,'%Y.%m') LIKE DATE_FORMAT(NOW(), '%Y.%m')
GROUP BY projects.name, collectors.name, bgp_types.name;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-08-18  9:58:27
