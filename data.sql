-- MySQL dump 10.13  Distrib 5.5.49, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: xivsync
-- ------------------------------------------------------
-- Server version	5.5.49-0ubuntu0.14.04.1

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
-- Table structure for table `characters`
--

DROP TABLE IF EXISTS `characters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characters` (
  `lodestone_id` int(32) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` timestamp NULL DEFAULT NULL,
  `last_active` timestamp NULL DEFAULT NULL,
  `queue` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(64) NOT NULL,
  `server` varchar(32) NOT NULL,
  `avatar` varchar(256) NOT NULL,
  `portrait` varchar(256) NOT NULL,
  `data` text COMMENT 'JSON Data',
  `achievements_last_updated` timestamp NULL DEFAULT NULL,
  `achievements_public` tinyint(1) DEFAULT '0',
  `achievements_score_reborn` int(8) NOT NULL,
  `achievements_score_legacy` int(8) NOT NULL DEFAULT '0',
  `achievements_score_reborn_total` int(11) NOT NULL,
  `achievements_score_legacy_total` int(11) NOT NULL,
  PRIMARY KEY (`lodestone_id`),
  KEY `achievements` (`achievements_last_updated`,`achievements_public`,`achievements_score_reborn`,`achievements_score_legacy`),
  KEY `profile` (`added`,`last_active`,`queue`,`name`,`server`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `characters`
--

LOCK TABLES `characters` WRITE;
/*!40000 ALTER TABLE `characters` DISABLE KEYS */;
INSERT INTO `characters` VALUES (730968,'2016-07-20 18:57:28','2016-07-20 18:57:28',NULL,1,'Premium Virtue','Adamantoise','http://img2.finalfantasyxiv.com/f/73cbde3df508fa147006c64859727aae_1f5fd239b885860b7c2bfc72ad1d97effc0_96x96.jpg?1471288110','http://img2.finalfantasyxiv.com/f/73cbde3df508fa147006c64859727aae_1f5fd239b885860b7c2bfc72ad1d97effl0_640x873.jpg?1471288110','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','2016-08-06 12:12:40',1,2730,245,11265,1745);
/*!40000 ALTER TABLE `characters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `characters_achievements`
--

DROP TABLE IF EXISTS `characters_achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characters_achievements` (
  `lodestone_id` int(32) NOT NULL,
  `achievement_id` int(12) NOT NULL,
  `obtained` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `points` int(3) NOT NULL,
  UNIQUE KEY `unique` (`lodestone_id`,`achievement_id`),
  KEY `data` (`obtained`,`points`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `characters_achievements`
--

LOCK TABLES `characters_achievements` WRITE;
/*!40000 ALTER TABLE `characters_achievements` DISABLE KEYS */;
INSERT INTO `characters_achievements` VALUES (730968,1,'2013-08-16 12:11:01',5),(730968,2,'2013-08-16 12:11:01',5),(730968,16,'2013-08-16 12:11:01',5),(730968,18,'2013-08-16 12:11:01',5),(730968,19,'2013-08-16 12:11:01',5),(730968,20,'2013-08-16 12:11:01',5),(730968,21,'2013-08-16 12:11:01',5),(730968,22,'2013-08-16 12:11:01',5),(730968,23,'2013-08-16 12:11:01',5),(730968,24,'2013-08-16 12:11:01',5),(730968,25,'2013-08-16 12:11:01',5),(730968,26,'2013-08-16 12:11:01',5),(730968,27,'2013-08-16 12:11:01',5),(730968,28,'2013-08-16 12:11:01',5),(730968,29,'2013-08-16 12:11:01',5),(730968,30,'2013-08-16 12:11:01',5),(730968,31,'2013-08-16 12:11:01',5),(730968,32,'2013-08-16 12:11:01',5),(730968,33,'2013-08-16 12:11:01',5),(730968,34,'2013-08-16 12:11:01',5),(730968,35,'2013-08-16 12:11:01',5),(730968,36,'2013-08-16 12:11:01',5),(730968,37,'2013-08-16 12:11:01',5),(730968,38,'2013-08-16 12:11:01',5),(730968,39,'2013-08-16 12:11:01',5),(730968,40,'2013-08-16 12:11:01',5),(730968,41,'2013-08-16 12:11:01',5),(730968,42,'2013-08-16 12:11:01',5),(730968,43,'2013-08-16 12:11:01',5),(730968,45,'2013-08-16 12:11:01',5),(730968,46,'2013-08-16 12:11:01',5),(730968,47,'2013-08-16 12:11:01',5),(730968,48,'2013-08-16 12:11:01',5),(730968,49,'2013-08-16 12:11:01',5),(730968,50,'2013-08-16 12:11:01',5),(730968,51,'2013-08-16 12:11:01',5),(730968,52,'2013-08-16 12:11:01',5),(730968,53,'2013-08-16 12:11:01',5),(730968,54,'2013-08-16 12:11:01',5),(730968,62,'2013-08-16 12:11:01',5),(730968,67,'2013-08-16 12:11:01',5),(730968,68,'2013-08-16 12:11:01',5),(730968,69,'2013-08-16 12:11:01',5),(730968,70,'2013-08-16 12:11:01',5),(730968,71,'2013-08-16 12:11:01',5),(730968,72,'2013-08-16 12:11:01',5),(730968,77,'2013-08-16 12:11:01',5),(730968,78,'2013-08-16 12:11:01',5),(730968,82,'2013-08-16 12:11:01',5),(730968,87,'2013-08-16 12:11:01',5),(730968,88,'2013-08-16 12:11:01',5),(730968,89,'2013-08-16 12:11:01',5),(730968,90,'2013-08-16 12:11:01',5),(730968,92,'2013-08-16 12:11:01',5),(730968,93,'2013-08-16 12:11:01',5),(730968,108,'2013-08-16 12:11:01',5),(730968,109,'2013-08-16 12:11:01',5),(730968,114,'2013-08-16 12:11:01',5),(730968,115,'2013-08-16 12:11:01',5),(730968,116,'2013-08-16 12:11:01',5),(730968,120,'2013-08-16 12:11:01',5),(730968,121,'2013-08-16 12:11:01',5),(730968,122,'2013-08-16 12:11:01',5),(730968,173,'2013-08-16 12:11:01',5),(730968,174,'2013-08-16 12:11:01',5),(730968,305,'2013-08-16 12:11:01',5),(730968,306,'2013-08-16 12:11:01',5),(730968,312,'2013-08-16 12:11:01',5),(730968,313,'2013-08-16 12:11:01',5),(730968,323,'2013-08-16 12:11:01',5),(730968,324,'2013-08-16 12:11:01',5),(730968,325,'2013-08-16 12:11:01',5),(730968,326,'2013-08-16 12:11:01',5),(730968,330,'2013-08-16 12:11:01',5),(730968,331,'2013-08-16 12:11:01',5),(730968,332,'2013-08-16 12:11:01',5),(730968,408,'2013-08-16 12:11:01',5),(730968,409,'2013-08-16 12:11:01',5),(730968,410,'2013-08-16 12:11:01',5),(730968,411,'2013-08-16 12:11:01',5),(730968,412,'2013-08-16 12:11:01',5),(730968,413,'2013-08-16 12:11:01',5),(730968,454,'2013-08-16 12:11:01',5),(730968,487,'2013-08-16 12:11:01',5),(730968,488,'2013-08-16 12:11:01',5),(730968,489,'2013-08-16 12:11:01',5),(730968,490,'2013-08-16 12:11:01',5),(730968,497,'2013-08-16 12:11:01',5),(730968,511,'2013-08-16 12:11:01',5),(730968,512,'2013-08-16 12:11:01',5),(730968,513,'2013-08-16 12:11:01',5),(730968,525,'2013-08-16 12:11:01',5),(730968,529,'2013-08-16 12:11:01',5),(730968,538,'2013-08-16 12:11:01',5),(730968,539,'2013-08-16 12:11:01',5),(730968,550,'2013-08-16 12:11:01',5),(730968,574,'2013-08-16 12:11:01',5),(730968,590,'2013-08-16 12:11:01',5),(730968,3,'2013-08-16 12:11:01',10),(730968,4,'2013-08-16 12:11:01',10),(730968,5,'2013-08-16 12:11:01',10),(730968,8,'2013-08-16 12:11:01',10),(730968,9,'2013-08-16 12:11:01',10),(730968,10,'2013-08-16 12:11:01',10),(730968,11,'2013-08-16 12:11:01',10),(730968,12,'2013-08-16 12:11:01',10),(730968,14,'2013-08-16 12:11:01',10),(730968,540,'2013-08-16 12:11:01',10),(730968,44,'2013-08-16 12:11:01',20),(730968,55,'2013-08-16 12:11:01',20),(730968,486,'2013-08-16 12:11:01',20),(730968,491,'2013-08-16 12:11:01',20),(730968,496,'2013-08-16 12:11:01',20),(730968,510,'2013-08-16 12:11:01',20),(730968,15,'2013-08-16 12:11:01',30),(730968,56,'2013-08-16 12:11:01',30),(730968,789,'2013-08-16 21:47:29',5),(730968,647,'2013-08-17 04:28:37',10),(730968,310,'2013-08-17 22:07:04',5),(730968,648,'2013-08-17 22:07:04',10),(730968,662,'2013-08-17 22:07:04',10),(730968,790,'2013-08-17 22:07:04',10),(730968,592,'2013-08-19 10:17:21',5),(730968,661,'2013-08-19 10:17:21',10),(730968,803,'2013-08-26 19:41:45',5),(730968,653,'2013-08-26 19:41:45',10),(730968,663,'2013-08-27 10:56:09',10),(730968,669,'2013-08-27 14:58:37',10),(730968,666,'2013-08-28 01:05:04',10),(730968,791,'2013-08-28 23:05:21',20),(730968,739,'2013-08-30 15:11:12',5),(730968,673,'2013-08-30 15:11:12',10),(730968,659,'2013-08-31 09:00:29',10),(730968,783,'2013-08-31 14:59:15',5),(730968,742,'2013-09-01 18:57:54',5),(730968,784,'2013-09-01 18:57:54',5),(730968,664,'2013-09-05 00:09:28',10),(730968,785,'2013-09-05 03:29:49',5),(730968,786,'2013-09-07 14:59:10',5),(730968,672,'2013-09-07 14:59:10',10),(730968,740,'2013-09-08 17:43:35',10),(730968,787,'2013-09-08 17:43:35',10),(730968,678,'2013-09-09 17:47:01',10),(730968,808,'2013-09-09 20:20:20',5),(730968,679,'2013-09-09 20:20:20',10),(730968,788,'2013-09-09 20:20:20',20),(730968,667,'2013-09-09 21:04:11',10),(730968,676,'2013-09-10 20:10:13',10),(730968,671,'2013-09-11 21:53:13',10),(730968,743,'2013-09-13 13:24:31',5),(730968,307,'2013-09-13 16:33:39',5),(730968,655,'2013-09-15 21:38:39',10),(730968,657,'2013-09-18 14:56:57',10),(730968,63,'2013-09-21 20:27:09',5),(730968,614,'2013-09-21 20:27:09',5),(730968,649,'2013-09-22 09:04:21',10),(730968,677,'2013-09-25 09:24:01',10),(730968,629,'2013-09-25 15:44:14',5),(730968,91,'2013-09-25 20:43:14',5),(730968,608,'2013-09-25 20:43:14',5),(730968,593,'2013-09-26 14:55:54',5),(730968,594,'2013-09-26 14:55:54',5),(730968,804,'2013-09-26 14:55:54',5),(730968,301,'2013-09-26 14:55:54',10),(730968,800,'2013-09-26 14:55:54',10),(730968,801,'2013-09-26 14:55:54',10),(730968,651,'2013-09-27 17:57:28',10),(730968,802,'2013-09-27 17:57:28',10),(730968,656,'2013-09-29 23:09:20',10),(730968,796,'2013-09-29 23:09:20',10),(730968,805,'2013-09-30 18:27:10',10),(730968,798,'2013-10-01 14:59:51',10),(730968,595,'2013-10-01 18:22:38',5),(730968,794,'2013-10-01 18:22:38',10),(730968,795,'2013-10-02 14:57:14',10),(730968,799,'2013-10-02 14:57:14',10),(730968,6,'2013-10-04 22:49:00',10),(730968,596,'2013-10-06 07:01:15',5),(730968,599,'2013-10-06 07:01:15',5),(730968,744,'2013-10-06 14:02:22',5),(730968,134,'2013-10-07 13:01:09',10),(730968,600,'2013-10-08 22:36:57',5),(730968,670,'2013-10-08 22:36:57',10),(730968,197,'2013-10-10 22:05:31',5),(730968,98,'2013-10-11 11:32:38',5),(730968,652,'2013-10-11 14:02:53',10),(730968,133,'2013-10-13 00:57:58',10),(730968,601,'2013-10-13 14:55:29',5),(730968,635,'2013-10-13 14:55:29',5),(730968,198,'2013-10-15 14:58:41',5),(730968,99,'2013-10-17 03:00:25',5),(730968,719,'2013-10-17 14:59:46',5),(730968,597,'2013-10-17 14:59:46',10),(730968,774,'2013-10-17 19:45:54',5),(730968,100,'2013-10-19 05:46:04',5),(730968,199,'2013-10-19 05:46:04',5),(730968,200,'2013-10-19 05:46:04',5),(730968,101,'2013-10-20 10:14:57',5),(730968,102,'2013-10-20 10:14:57',5),(730968,189,'2013-10-20 10:14:57',5),(730968,103,'2013-10-21 14:59:55',5),(730968,104,'2013-10-21 14:59:55',5),(730968,223,'2013-10-21 14:59:55',5),(730968,228,'2013-10-21 14:59:55',5),(730968,235,'2013-10-21 14:59:55',5),(730968,777,'2013-10-21 14:59:55',5),(730968,105,'2013-10-22 08:31:12',5),(730968,230,'2013-10-22 08:31:12',5),(730968,302,'2013-10-22 12:06:21',20),(730968,303,'2013-10-22 12:06:21',30),(730968,681,'2013-10-26 01:29:58',10),(730968,682,'2013-10-26 17:47:27',10),(730968,665,'2013-10-28 15:28:57',10),(730968,741,'2013-10-28 15:28:57',20),(730968,131,'2013-10-28 16:56:19',10),(730968,745,'2013-10-30 03:31:42',10),(730968,674,'2013-11-13 23:22:23',10),(730968,296,'2013-11-17 10:06:45',5),(730968,193,'2013-11-18 14:55:28',5),(730968,775,'2013-11-18 14:55:28',5),(730968,720,'2013-11-19 14:57:56',5),(730968,660,'2013-12-17 19:28:32',10),(730968,823,'2013-12-18 08:04:58',5),(730968,843,'2013-12-18 08:04:58',5),(730968,844,'2013-12-18 08:04:58',5),(730968,849,'2013-12-18 08:04:58',5),(730968,885,'2013-12-18 22:16:49',5),(730968,598,'2013-12-18 22:16:49',10),(730968,341,'2013-12-19 02:28:11',5),(730968,644,'2013-12-19 02:28:11',5),(730968,645,'2013-12-19 02:28:11',5),(730968,883,'2013-12-19 22:13:44',10),(730968,884,'2013-12-19 22:13:44',10),(730968,873,'2013-12-20 01:59:58',10),(730968,342,'2013-12-20 14:08:04',5),(730968,858,'2013-12-21 21:16:40',0),(730968,856,'2013-12-21 23:06:05',10),(730968,407,'2013-12-23 14:59:50',5),(730968,414,'2013-12-23 14:59:50',20),(730968,872,'2013-12-24 02:55:02',10),(730968,874,'2013-12-24 04:37:47',10),(730968,117,'2013-12-25 23:57:38',5),(730968,333,'2013-12-25 23:57:38',5),(730968,334,'2013-12-25 23:57:38',5),(730968,632,'2013-12-25 23:57:38',5),(730968,236,'2013-12-28 14:59:51',5),(730968,722,'2013-12-29 00:12:08',5),(730968,106,'2013-12-30 20:22:05',5),(730968,237,'2013-12-30 20:22:05',5),(730968,680,'2014-01-04 17:03:38',10),(730968,778,'2014-01-05 03:39:28',5),(730968,130,'2014-01-09 22:19:08',10),(730968,315,'2014-01-09 22:19:08',10),(730968,129,'2014-01-17 03:30:42',10),(730968,857,'2014-01-19 06:39:08',10),(730968,855,'2014-01-25 00:36:00',10),(730968,876,'2014-02-04 22:49:25',10),(730968,747,'2014-02-23 04:50:04',5),(730968,684,'2014-02-23 04:50:04',10),(730968,658,'2014-02-26 15:03:27',10),(730968,793,'2014-03-15 18:58:51',10),(730968,748,'2014-03-22 17:47:36',10),(730968,900,'2014-03-27 21:47:37',10),(730968,898,'2014-03-27 21:47:37',20),(730968,897,'2014-03-28 00:38:01',10),(730968,899,'2014-03-28 20:28:36',20),(730968,895,'2014-03-28 21:05:46',10),(730968,896,'2014-03-28 22:32:10',10),(730968,893,'2014-03-31 20:06:29',10),(730968,912,'2014-04-02 20:56:01',5),(730968,859,'2014-04-05 17:42:45',0),(730968,541,'2014-04-06 02:56:22',20),(730968,913,'2014-04-10 18:08:46',5),(730968,914,'2014-04-13 22:30:08',10),(730968,327,'2015-12-20 21:31:00',5),(730968,1046,'2015-12-20 21:31:00',5),(730968,328,'2015-12-20 21:31:00',10),(730968,1222,'2016-05-30 23:52:41',20),(730968,1001,'2016-05-31 21:45:47',20),(730968,654,'2016-05-31 23:56:59',10),(730968,1039,'2016-05-31 23:56:59',10),(730968,1029,'2016-05-31 23:56:59',20),(730968,1223,'2016-05-31 23:56:59',20),(730968,1072,'2016-06-01 00:47:39',10),(730968,1065,'2016-06-01 23:19:08',10),(730968,1067,'2016-06-01 23:19:08',10),(730968,1129,'2016-06-01 23:19:08',20),(730968,1133,'2016-06-02 21:33:20',20),(730968,1134,'2016-06-02 23:11:38',20),(730968,1083,'2016-06-03 01:08:58',5),(730968,1100,'2016-06-03 01:08:58',5),(730968,1103,'2016-06-03 01:08:58',5),(730968,1115,'2016-06-03 01:08:58',5),(730968,1125,'2016-06-03 01:08:58',5),(730968,1157,'2016-06-03 01:08:58',5),(730968,1208,'2016-06-03 01:08:58',10),(730968,1491,'2016-06-03 01:08:58',10),(730968,1084,'2016-06-03 22:30:35',5),(730968,1209,'2016-06-04 00:01:48',10),(730968,1092,'2016-06-04 15:01:51',5),(730968,1096,'2016-06-04 15:01:51',5),(730968,1158,'2016-06-04 22:55:38',5),(730968,1210,'2016-06-05 00:42:21',10),(730968,1105,'2016-06-05 03:55:25',5),(730968,1203,'2016-06-05 03:55:25',10),(730968,1121,'2016-06-05 13:46:17',5),(730968,1118,'2016-06-05 15:01:06',5),(730968,1085,'2016-06-05 17:38:51',5),(730968,1126,'2016-06-05 17:38:51',5),(730968,1135,'2016-06-05 23:35:40',20),(730968,1116,'2016-06-07 20:44:37',5),(730968,7,'2016-06-07 23:47:31',30),(730968,1211,'2016-06-08 20:37:09',10),(730968,1136,'2016-06-08 20:37:09',20),(730968,1161,'2016-06-08 21:56:37',5),(730968,1137,'2016-06-08 21:56:37',20),(730968,1180,'2016-06-08 22:30:12',5),(730968,1160,'2016-06-11 01:59:47',5),(730968,1159,'2016-06-11 06:07:18',5),(730968,1162,'2016-06-11 06:07:18',5),(730968,1163,'2016-06-11 06:07:18',10),(730968,1212,'2016-06-11 06:07:18',10),(730968,1138,'2016-06-11 06:07:18',20),(730968,1224,'2016-06-11 06:07:18',20),(730968,1145,'2016-06-11 16:36:37',5),(730968,1171,'2016-06-11 16:36:37',5),(730968,1184,'2016-06-11 16:36:37',5),(730968,1213,'2016-06-11 17:50:14',10),(730968,1139,'2016-06-11 17:50:14',20),(730968,1387,'2016-06-11 22:46:13',10),(730968,1176,'2016-06-12 03:23:04',5),(730968,1204,'2016-06-12 16:55:49',10),(730968,1207,'2016-06-12 16:55:49',10),(730968,1486,'2016-06-15 21:18:46',10),(730968,1493,'2016-06-15 21:18:46',10),(730968,1228,'2016-06-17 23:00:05',5),(730968,1164,'2016-06-17 23:00:05',10),(730968,1216,'2016-06-17 23:00:05',10),(730968,1217,'2016-06-17 23:00:05',10),(730968,1218,'2016-06-17 23:00:05',10),(730968,1599,'2016-06-17 23:00:05',10),(730968,860,'2016-06-17 23:21:29',0),(730968,1482,'2016-06-17 23:21:29',10),(730968,1476,'2016-06-18 00:51:01',5),(730968,1483,'2016-06-18 00:51:01',10),(730968,1484,'2016-06-18 00:51:01',10),(730968,1494,'2016-06-18 00:51:01',10),(730968,863,'2016-06-18 03:47:35',5),(730968,806,'2016-06-18 03:47:35',10),(730968,809,'2016-06-18 03:47:35',10),(730968,1038,'2016-06-19 01:36:50',10),(730968,1071,'2016-06-19 17:12:11',10),(730968,1070,'2016-06-19 19:26:46',10),(730968,1477,'2016-06-19 19:26:46',10),(730968,1478,'2016-06-21 01:18:45',20),(730968,1047,'2016-06-21 23:59:17',5),(730968,1594,'2016-06-28 22:22:11',10),(730968,1388,'2016-06-28 23:17:06',10),(730968,1399,'2016-06-28 23:17:06',10),(730968,1401,'2016-06-28 23:17:06',10),(730968,1574,'2016-06-29 01:03:19',10),(730968,1602,'2016-06-29 01:03:19',10),(730968,1215,'2016-07-02 23:25:08',10),(730968,1177,'2016-07-03 02:27:33',5),(730968,343,'2016-07-05 00:27:32',5),(730968,993,'2016-07-05 16:07:48',10),(730968,1600,'2016-07-05 17:17:08',10);
/*!40000 ALTER TABLE `characters_achievements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `characters_gearsets`
--

DROP TABLE IF EXISTS `characters_gearsets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characters_gearsets` (
  `lodestone_id` int(32) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `classjob_id` int(3) NOT NULL,
  `level` int(3) NOT NULL,
  `gear` varchar(2048) NOT NULL,
  `stats` varchar(5000) NOT NULL,
  UNIQUE KEY `unique` (`lodestone_id`,`classjob_id`,`level`),
  KEY `role` (`classjob_id`,`level`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `characters_gearsets`
--

LOCK TABLES `characters_gearsets` WRITE;
/*!40000 ALTER TABLE `characters_gearsets` DISABLE KEYS */;
INSERT INTO `characters_gearsets` VALUES (730968,'2016-08-13 21:25:55',1,50,'[{\"slot\":\"main-hand\",\"id\":10586,\"other\":null},{\"slot\":\"head\",\"id\":10670,\"other\":null},{\"slot\":\"body\",\"id\":10677,\"other\":null},{\"slot\":\"hands\",\"id\":10684,\"other\":null},{\"slot\":\"waist\",\"id\":10691,\"other\":null},{\"slot\":\"legs\",\"id\":10698,\"other\":null},{\"slot\":\"feet\",\"id\":10705,\"other\":null},{\"slot\":\"off-hand\",\"id\":10664,\"other\":null},{\"slot\":\"necklace\",\"id\":10964,\"other\":null},{\"slot\":\"earrings\",\"id\":10976,\"other\":null},{\"slot\":\"bracelets\",\"id\":10970,\"other\":null},{\"slot\":\"ring1\",\"id\":10982,\"other\":null},{\"slot\":\"ring2\",\"id\":10982,\"other\":null},{\"slot\":\"soul_crystal\",\"id\":4542,\"other\":null}]','{\"core\":{\"hp\":{\"name\":\"HP\",\"value\":7825},\"mp\":{\"name\":\"MP\",\"value\":1478},\"tp\":{\"name\":\"TP\",\"value\":1000}},\"attributes\":{\"strength\":{\"name\":\"Strength\",\"value\":406},\"dexterity\":{\"name\":\"Dexterity\",\"value\":190},\"vitality\":{\"name\":\"Vitality\",\"value\":601},\"intelligence\":{\"name\":\"Intelligence\",\"value\":122},\"mind\":{\"name\":\"Mind\",\"value\":200},\"piety\":{\"name\":\"Piety\",\"value\":172}},\"elemental\":{\"fire\":{\"name\":\"Fire\",\"value\":271,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/fire.png?1467272264\"},\"ice\":{\"name\":\"Ice\",\"value\":270,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/ice.png?1467272264\"},\"wind\":{\"name\":\"Wind\",\"value\":267,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/wind.png?1467272264\"},\"earth\":{\"name\":\"Earth\",\"value\":269,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/earth.png?1467272264\"},\"lightning\":{\"name\":\"Lightning\",\"value\":269,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/thunder.png?1467272264\"},\"water\":{\"name\":\"Water\",\"value\":269,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/water.png?1467272264\"}},\"properties\":{\"accuracy\":{\"name\":\"Accuracy\",\"value\":473},\"critical_hit_rate\":{\"name\":\"Critical Hit Rate\",\"value\":429},\"determination\":{\"name\":\"Determination\",\"value\":268},\"defense\":{\"name\":\"Defense\",\"value\":700},\"parry\":{\"name\":\"Parry\",\"value\":434},\"magic_defense\":{\"name\":\"Magic Defense\",\"value\":700},\"attack_power\":{\"name\":\"Attack Power\",\"value\":452},\"skill_speed\":{\"name\":\"Skill Speed\",\"value\":432},\"attack_magic_potency\":{\"name\":\"Attack Magic Potency\",\"value\":122},\"healing_magic_potency\":{\"name\":\"Healing Magic Potency\",\"value\":200},\"spell_speed\":{\"name\":\"Spell Speed\",\"value\":341}},\"resistances\":{\"slow_resistance\":{\"name\":\"Slow Resistance\",\"value\":0},\"silence_resistance\":{\"name\":\"Silence Resistance\",\"value\":0},\"blind_resistance\":{\"name\":\"Blind Resistance\",\"value\":0},\"poison_resistance\":{\"name\":\"Poison Resistance\",\"value\":0},\"stun_resistance\":{\"name\":\"Stun Resistance\",\"value\":0},\"sleep_resistance\":{\"name\":\"Sleep Resistance\",\"value\":0},\"bind_resistance\":{\"name\":\"Bind Resistance\",\"value\":0},\"heavy_resistance\":{\"name\":\"Heavy Resistance\",\"value\":0},\"slashing_resistance\":{\"name\":\"Slashing Resistance\",\"value\":100},\"piercing_resistance\":{\"name\":\"Piercing Resistance\",\"value\":100},\"blunt_resistance\":{\"name\":\"Blunt Resistance\",\"value\":100}}}'),(730968,'2016-08-13 23:19:20',3,52,'[{\"slot\":\"main-hand\",\"id\":10598,\"other\":null},{\"slot\":\"head\",\"id\":10670,\"other\":null},{\"slot\":\"body\",\"id\":10677,\"other\":null},{\"slot\":\"hands\",\"id\":10684,\"other\":null},{\"slot\":\"waist\",\"id\":10691,\"other\":null},{\"slot\":\"legs\",\"id\":10698,\"other\":null},{\"slot\":\"feet\",\"id\":10705,\"other\":null},{\"slot\":\"necklace\",\"id\":10964,\"other\":null},{\"slot\":\"earrings\",\"id\":10976,\"other\":null},{\"slot\":\"bracelets\",\"id\":10970,\"other\":null},{\"slot\":\"ring1\",\"id\":10982,\"other\":null},{\"slot\":\"ring2\",\"id\":10982,\"other\":null},{\"slot\":\"soul_crystal\",\"id\":4544,\"other\":null}]','{\"core\":{\"hp\":{\"name\":\"HP\",\"value\":8577},\"mp\":{\"name\":\"MP\",\"value\":1069},\"tp\":{\"name\":\"TP\",\"value\":1000}},\"attributes\":{\"strength\":{\"name\":\"Strength\",\"value\":419},\"dexterity\":{\"name\":\"Dexterity\",\"value\":193},\"vitality\":{\"name\":\"Vitality\",\"value\":604},\"intelligence\":{\"name\":\"Intelligence\",\"value\":83},\"mind\":{\"name\":\"Mind\",\"value\":110},\"piety\":{\"name\":\"Piety\",\"value\":93}},\"elemental\":{\"fire\":{\"name\":\"Fire\",\"value\":273,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/fire.png?1467272264\"},\"ice\":{\"name\":\"Ice\",\"value\":272,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/ice.png?1467272264\"},\"wind\":{\"name\":\"Wind\",\"value\":269,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/wind.png?1467272264\"},\"earth\":{\"name\":\"Earth\",\"value\":271,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/earth.png?1467272264\"},\"lightning\":{\"name\":\"Lightning\",\"value\":271,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/thunder.png?1467272264\"},\"water\":{\"name\":\"Water\",\"value\":271,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/water.png?1467272264\"}},\"properties\":{\"accuracy\":{\"name\":\"Accuracy\",\"value\":476},\"critical_hit_rate\":{\"name\":\"Critical Hit Rate\",\"value\":465},\"determination\":{\"name\":\"Determination\",\"value\":276},\"defense\":{\"name\":\"Defense\",\"value\":700},\"parry\":{\"name\":\"Parry\",\"value\":437},\"magic_defense\":{\"name\":\"Magic Defense\",\"value\":700},\"attack_power\":{\"name\":\"Attack Power\",\"value\":459},\"skill_speed\":{\"name\":\"Skill Speed\",\"value\":394},\"attack_magic_potency\":{\"name\":\"Attack Magic Potency\",\"value\":83},\"healing_magic_potency\":{\"name\":\"Healing Magic Potency\",\"value\":110},\"spell_speed\":{\"name\":\"Spell Speed\",\"value\":344}},\"resistances\":{\"slow_resistance\":{\"name\":\"Slow Resistance\",\"value\":0},\"silence_resistance\":{\"name\":\"Silence Resistance\",\"value\":0},\"blind_resistance\":{\"name\":\"Blind Resistance\",\"value\":0},\"poison_resistance\":{\"name\":\"Poison Resistance\",\"value\":0},\"stun_resistance\":{\"name\":\"Stun Resistance\",\"value\":0},\"sleep_resistance\":{\"name\":\"Sleep Resistance\",\"value\":0},\"bind_resistance\":{\"name\":\"Bind Resistance\",\"value\":0},\"heavy_resistance\":{\"name\":\"Heavy Resistance\",\"value\":0},\"slashing_resistance\":{\"name\":\"Slashing Resistance\",\"value\":100},\"piercing_resistance\":{\"name\":\"Piercing Resistance\",\"value\":100},\"blunt_resistance\":{\"name\":\"Blunt Resistance\",\"value\":100}}}'),(730968,'2016-08-03 22:15:06',5,60,'[{\"slot\":\"main-hand\",\"id\":13601,\"other\":null},{\"slot\":\"head\",\"id\":14405,\"other\":null},{\"slot\":\"body\",\"id\":14522,\"other\":null},{\"slot\":\"hands\",\"id\":14529,\"other\":null},{\"slot\":\"waist\",\"id\":14536,\"other\":null},{\"slot\":\"legs\",\"id\":14543,\"other\":null},{\"slot\":\"feet\",\"id\":14440,\"other\":null},{\"slot\":\"necklace\",\"id\":11752,\"other\":null},{\"slot\":\"earrings\",\"id\":14370,\"other\":null},{\"slot\":\"bracelets\",\"id\":15411,\"other\":null},{\"slot\":\"ring1\",\"id\":15412,\"other\":null},{\"slot\":\"ring2\",\"id\":14571,\"other\":null},{\"slot\":\"soul_crystal\",\"id\":4546,\"other\":null}]','{\"core\":{\"hp\":{\"name\":\"HP\",\"value\":15993},\"mp\":{\"name\":\"MP\",\"value\":6935},\"tp\":{\"name\":\"TP\",\"value\":1000}},\"attributes\":{\"strength\":{\"name\":\"Strength\",\"value\":197},\"dexterity\":{\"name\":\"Dexterity\",\"value\":1148},\"vitality\":{\"name\":\"Vitality\",\"value\":865},\"intelligence\":{\"name\":\"Intelligence\",\"value\":186},\"mind\":{\"name\":\"Mind\",\"value\":172},\"piety\":{\"name\":\"Piety\",\"value\":186}},\"elemental\":{\"fire\":{\"name\":\"Fire\",\"value\":284,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/fire.png?1467272264\"},\"ice\":{\"name\":\"Ice\",\"value\":283,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/ice.png?1467272264\"},\"wind\":{\"name\":\"Wind\",\"value\":280,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/wind.png?1467272264\"},\"earth\":{\"name\":\"Earth\",\"value\":282,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/earth.png?1467272264\"},\"lightning\":{\"name\":\"Lightning\",\"value\":282,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/thunder.png?1467272264\"},\"water\":{\"name\":\"Water\",\"value\":282,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/water.png?1467272264\"}},\"properties\":{\"accuracy\":{\"name\":\"Accuracy\",\"value\":735},\"critical_hit_rate\":{\"name\":\"Critical Hit Rate\",\"value\":898},\"determination\":{\"name\":\"Determination\",\"value\":597},\"defense\":{\"name\":\"Defense\",\"value\":1217},\"parry\":{\"name\":\"Parry\",\"value\":354},\"magic_defense\":{\"name\":\"Magic Defense\",\"value\":1217},\"attack_power\":{\"name\":\"Attack Power\",\"value\":1148},\"skill_speed\":{\"name\":\"Skill Speed\",\"value\":430},\"attack_magic_potency\":{\"name\":\"Attack Magic Potency\",\"value\":186},\"healing_magic_potency\":{\"name\":\"Healing Magic Potency\",\"value\":172},\"spell_speed\":{\"name\":\"Spell Speed\",\"value\":354}},\"resistances\":{\"slow_resistance\":{\"name\":\"Slow Resistance\",\"value\":0},\"silence_resistance\":{\"name\":\"Silence Resistance\",\"value\":0},\"blind_resistance\":{\"name\":\"Blind Resistance\",\"value\":0},\"poison_resistance\":{\"name\":\"Poison Resistance\",\"value\":0},\"stun_resistance\":{\"name\":\"Stun Resistance\",\"value\":0},\"sleep_resistance\":{\"name\":\"Sleep Resistance\",\"value\":0},\"bind_resistance\":{\"name\":\"Bind Resistance\",\"value\":0},\"heavy_resistance\":{\"name\":\"Heavy Resistance\",\"value\":0},\"slashing_resistance\":{\"name\":\"Slashing Resistance\",\"value\":100},\"piercing_resistance\":{\"name\":\"Piercing Resistance\",\"value\":100},\"blunt_resistance\":{\"name\":\"Blunt Resistance\",\"value\":100}}}'),(730968,'2016-08-13 18:58:31',29,27,'[{\"slot\":\"main-hand\",\"id\":9193,\"other\":null},{\"slot\":\"body\",\"id\":3034,\"other\":null},{\"slot\":\"hands\",\"id\":3530,\"other\":null},{\"slot\":\"legs\",\"id\":3313,\"other\":null},{\"slot\":\"feet\",\"id\":3765,\"other\":null},{\"slot\":\"ring1\",\"id\":4422,\"other\":null},{\"slot\":\"ring2\",\"id\":4422,\"other\":null}]','{\"core\":{\"hp\":{\"name\":\"HP\",\"value\":562},\"mp\":{\"name\":\"MP\",\"value\":208},\"tp\":{\"name\":\"TP\",\"value\":1000}},\"attributes\":{\"strength\":{\"name\":\"Strength\",\"value\":77},\"dexterity\":{\"name\":\"Dexterity\",\"value\":120},\"vitality\":{\"name\":\"Vitality\",\"value\":87},\"intelligence\":{\"name\":\"Intelligence\",\"value\":52},\"mind\":{\"name\":\"Mind\",\"value\":57},\"piety\":{\"name\":\"Piety\",\"value\":52}},\"elemental\":{\"fire\":{\"name\":\"Fire\",\"value\":127,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/fire.png?1467272264\"},\"ice\":{\"name\":\"Ice\",\"value\":126,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/ice.png?1467272264\"},\"wind\":{\"name\":\"Wind\",\"value\":123,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/wind.png?1467272264\"},\"earth\":{\"name\":\"Earth\",\"value\":125,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/earth.png?1467272264\"},\"lightning\":{\"name\":\"Lightning\",\"value\":125,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/thunder.png?1467272264\"},\"water\":{\"name\":\"Water\",\"value\":125,\"icon\":\"http://img.finalfantasyxiv.com/lds/pc//global/images/character/ic/water.png?1467272264\"}},\"properties\":{\"accuracy\":{\"name\":\"Accuracy\",\"value\":160},\"critical_hit_rate\":{\"name\":\"Critical Hit Rate\",\"value\":155},\"determination\":{\"name\":\"Determination\",\"value\":86},\"defense\":{\"name\":\"Defense\",\"value\":51},\"parry\":{\"name\":\"Parry\",\"value\":155},\"magic_defense\":{\"name\":\"Magic Defense\",\"value\":51},\"attack_power\":{\"name\":\"Attack Power\",\"value\":120},\"skill_speed\":{\"name\":\"Skill Speed\",\"value\":158},\"attack_magic_potency\":{\"name\":\"Attack Magic Potency\",\"value\":52},\"healing_magic_potency\":{\"name\":\"Healing Magic Potency\",\"value\":57},\"spell_speed\":{\"name\":\"Spell Speed\",\"value\":155}},\"resistances\":{\"slow_resistance\":{\"name\":\"Slow Resistance\",\"value\":0},\"silence_resistance\":{\"name\":\"Silence Resistance\",\"value\":0},\"blind_resistance\":{\"name\":\"Blind Resistance\",\"value\":0},\"poison_resistance\":{\"name\":\"Poison Resistance\",\"value\":0},\"stun_resistance\":{\"name\":\"Stun Resistance\",\"value\":0},\"sleep_resistance\":{\"name\":\"Sleep Resistance\",\"value\":0},\"bind_resistance\":{\"name\":\"Bind Resistance\",\"value\":0},\"heavy_resistance\":{\"name\":\"Heavy Resistance\",\"value\":0},\"slashing_resistance\":{\"name\":\"Slashing Resistance\",\"value\":100},\"piercing_resistance\":{\"name\":\"Piercing Resistance\",\"value\":100},\"blunt_resistance\":{\"name\":\"Blunt Resistance\",\"value\":100}}}');
/*!40000 ALTER TABLE `characters_gearsets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `characters_grandcompany`
--

DROP TABLE IF EXISTS `characters_grandcompany`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characters_grandcompany` (
  `lodestone_id` int(32) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `name` varchar(64) NOT NULL,
  `rank` varchar(64) NOT NULL,
  `icon` varchar(128) NOT NULL,
  UNIQUE KEY `unique` (`lodestone_id`,`name`),
  KEY `name` (`name`,`rank`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `characters_grandcompany`
--

LOCK TABLES `characters_grandcompany` WRITE;
/*!40000 ALTER TABLE `characters_grandcompany` DISABLE KEYS */;
INSERT INTO `characters_grandcompany` VALUES (730968,'2016-08-03 22:15:06','Immortal Flames','Second Flame Lieutenant','http://img.finalfantasyxiv.com/lds/pc/global/images/freecompany/ic/gcrank/uldah_9.png?1467272266');
/*!40000 ALTER TABLE `characters_grandcompany` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `characters_minions`
--

DROP TABLE IF EXISTS `characters_minions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characters_minions` (
  `lodestone_id` int(32) NOT NULL,
  `minion_id` int(32) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `other` varchar(512) DEFAULT NULL,
  UNIQUE KEY `unique` (`lodestone_id`,`minion_id`,`other`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `characters_minions`
--

LOCK TABLES `characters_minions` WRITE;
/*!40000 ALTER TABLE `characters_minions` DISABLE KEYS */;
INSERT INTO `characters_minions` VALUES (730968,3,'2016-08-03 22:15:06',NULL),(730968,54,'2016-08-03 22:15:06',NULL),(730968,75,'2016-08-03 22:15:06',NULL),(730968,128,'2016-08-03 22:15:06',NULL),(730968,119,'2016-08-03 22:15:06',NULL),(730968,1,'2016-08-03 22:15:06',NULL),(730968,5,'2016-08-03 22:15:06',NULL),(730968,17,'2016-08-03 22:15:06',NULL),(730968,25,'2016-08-03 22:15:06',NULL),(730968,28,'2016-08-03 22:15:06',NULL),(730968,31,'2016-08-03 22:15:06',NULL),(730968,32,'2016-08-03 22:15:06',NULL),(730968,34,'2016-08-03 22:15:06',NULL),(730968,18,'2016-08-03 22:15:06',NULL),(730968,36,'2016-08-03 22:15:06',NULL),(730968,37,'2016-08-03 22:15:06',NULL),(730968,38,'2016-08-03 22:15:06',NULL),(730968,41,'2016-08-03 22:15:06',NULL),(730968,42,'2016-08-03 22:15:06',NULL),(730968,44,'2016-08-03 22:15:06',NULL),(730968,149,'2016-08-03 22:15:06',NULL),(730968,181,'2016-08-03 22:15:06',NULL),(730968,2,'2016-08-03 22:15:06',NULL),(730968,4,'2016-08-03 22:15:06',NULL),(730968,22,'2016-08-03 22:15:06',NULL),(730968,79,'2016-08-03 22:15:06',NULL),(730968,49,'2016-08-03 22:15:06',NULL),(730968,51,'2016-08-03 22:15:06',NULL),(730968,52,'2016-08-03 22:15:06',NULL),(730968,70,'2016-08-03 22:15:06',NULL),(730968,71,'2016-08-03 22:15:06',NULL),(730968,76,'2016-08-03 22:15:06',NULL),(730968,85,'2016-08-03 22:15:06',NULL),(730968,77,'2016-08-03 22:15:06',NULL),(730968,167,'2016-08-03 22:15:06',NULL),(730968,129,'2016-08-03 22:15:06',NULL),(730968,130,'2016-08-03 22:15:06',NULL),(730968,133,'2016-08-03 22:15:06',NULL),(730968,84,'2016-08-03 22:15:06',NULL),(730968,173,'2016-08-03 22:15:06',NULL),(730968,193,'2016-08-03 22:15:06',NULL),(730968,3,'2016-08-03 22:15:17',NULL),(730968,54,'2016-08-03 22:15:17',NULL),(730968,75,'2016-08-03 22:15:17',NULL),(730968,128,'2016-08-03 22:15:17',NULL),(730968,119,'2016-08-03 22:15:17',NULL),(730968,1,'2016-08-03 22:15:17',NULL),(730968,5,'2016-08-03 22:15:17',NULL),(730968,17,'2016-08-03 22:15:17',NULL),(730968,25,'2016-08-03 22:15:17',NULL),(730968,28,'2016-08-03 22:15:17',NULL),(730968,31,'2016-08-03 22:15:17',NULL),(730968,32,'2016-08-03 22:15:17',NULL),(730968,34,'2016-08-03 22:15:17',NULL),(730968,18,'2016-08-03 22:15:17',NULL),(730968,36,'2016-08-03 22:15:17',NULL),(730968,37,'2016-08-03 22:15:17',NULL),(730968,38,'2016-08-03 22:15:17',NULL),(730968,41,'2016-08-03 22:15:17',NULL),(730968,42,'2016-08-03 22:15:17',NULL),(730968,44,'2016-08-03 22:15:17',NULL),(730968,149,'2016-08-03 22:15:17',NULL),(730968,181,'2016-08-03 22:15:17',NULL),(730968,2,'2016-08-03 22:15:17',NULL),(730968,4,'2016-08-03 22:15:17',NULL),(730968,22,'2016-08-03 22:15:17',NULL),(730968,79,'2016-08-03 22:15:17',NULL),(730968,49,'2016-08-03 22:15:17',NULL),(730968,51,'2016-08-03 22:15:17',NULL),(730968,52,'2016-08-03 22:15:17',NULL),(730968,70,'2016-08-03 22:15:17',NULL),(730968,71,'2016-08-03 22:15:17',NULL),(730968,76,'2016-08-03 22:15:17',NULL),(730968,85,'2016-08-03 22:15:17',NULL),(730968,77,'2016-08-03 22:15:17',NULL),(730968,167,'2016-08-03 22:15:17',NULL),(730968,129,'2016-08-03 22:15:17',NULL),(730968,130,'2016-08-03 22:15:17',NULL),(730968,133,'2016-08-03 22:15:17',NULL),(730968,84,'2016-08-03 22:15:17',NULL),(730968,173,'2016-08-03 22:15:17',NULL),(730968,193,'2016-08-03 22:15:17',NULL),(730968,3,'2016-08-07 00:12:19',NULL),(730968,54,'2016-08-07 00:12:19',NULL),(730968,75,'2016-08-07 00:12:19',NULL),(730968,128,'2016-08-07 00:12:19',NULL),(730968,119,'2016-08-07 00:12:19',NULL),(730968,1,'2016-08-07 00:12:19',NULL),(730968,5,'2016-08-07 00:12:19',NULL),(730968,17,'2016-08-07 00:12:19',NULL),(730968,25,'2016-08-07 00:12:19',NULL),(730968,28,'2016-08-07 00:12:19',NULL),(730968,31,'2016-08-07 00:12:19',NULL),(730968,32,'2016-08-07 00:12:19',NULL),(730968,34,'2016-08-07 00:12:19',NULL),(730968,18,'2016-08-07 00:12:19',NULL),(730968,36,'2016-08-07 00:12:19',NULL),(730968,37,'2016-08-07 00:12:19',NULL),(730968,38,'2016-08-07 00:12:19',NULL),(730968,41,'2016-08-07 00:12:19',NULL),(730968,42,'2016-08-07 00:12:19',NULL),(730968,44,'2016-08-07 00:12:19',NULL),(730968,149,'2016-08-07 00:12:19',NULL),(730968,181,'2016-08-07 00:12:19',NULL),(730968,2,'2016-08-07 00:12:19',NULL),(730968,4,'2016-08-07 00:12:19',NULL),(730968,22,'2016-08-07 00:12:19',NULL),(730968,79,'2016-08-07 00:12:19',NULL),(730968,49,'2016-08-07 00:12:19',NULL),(730968,51,'2016-08-07 00:12:19',NULL),(730968,52,'2016-08-07 00:12:19',NULL),(730968,70,'2016-08-07 00:12:19',NULL),(730968,71,'2016-08-07 00:12:19',NULL),(730968,76,'2016-08-07 00:12:19',NULL),(730968,85,'2016-08-07 00:12:19',NULL),(730968,77,'2016-08-07 00:12:19',NULL),(730968,167,'2016-08-07 00:12:19',NULL),(730968,129,'2016-08-07 00:12:19',NULL),(730968,130,'2016-08-07 00:12:19',NULL),(730968,133,'2016-08-07 00:12:19',NULL),(730968,84,'2016-08-07 00:12:19',NULL),(730968,173,'2016-08-07 00:12:19',NULL),(730968,193,'2016-08-07 00:12:19',NULL),(730968,3,'2016-08-13 18:58:31',NULL),(730968,54,'2016-08-13 18:58:31',NULL),(730968,75,'2016-08-13 18:58:31',NULL),(730968,128,'2016-08-13 18:58:31',NULL),(730968,119,'2016-08-13 18:58:31',NULL),(730968,1,'2016-08-13 18:58:31',NULL),(730968,5,'2016-08-13 18:58:31',NULL),(730968,17,'2016-08-13 18:58:31',NULL),(730968,25,'2016-08-13 18:58:31',NULL),(730968,28,'2016-08-13 18:58:31',NULL),(730968,31,'2016-08-13 18:58:31',NULL),(730968,32,'2016-08-13 18:58:31',NULL),(730968,34,'2016-08-13 18:58:31',NULL),(730968,18,'2016-08-13 18:58:31',NULL),(730968,36,'2016-08-13 18:58:31',NULL),(730968,37,'2016-08-13 18:58:31',NULL),(730968,38,'2016-08-13 18:58:31',NULL),(730968,41,'2016-08-13 18:58:31',NULL),(730968,42,'2016-08-13 18:58:31',NULL),(730968,44,'2016-08-13 18:58:31',NULL),(730968,149,'2016-08-13 18:58:31',NULL),(730968,181,'2016-08-13 18:58:31',NULL),(730968,2,'2016-08-13 18:58:31',NULL),(730968,4,'2016-08-13 18:58:31',NULL),(730968,22,'2016-08-13 18:58:31',NULL),(730968,79,'2016-08-13 18:58:31',NULL),(730968,49,'2016-08-13 18:58:31',NULL),(730968,51,'2016-08-13 18:58:31',NULL),(730968,52,'2016-08-13 18:58:31',NULL),(730968,70,'2016-08-13 18:58:31',NULL),(730968,71,'2016-08-13 18:58:31',NULL),(730968,76,'2016-08-13 18:58:31',NULL),(730968,85,'2016-08-13 18:58:31',NULL),(730968,77,'2016-08-13 18:58:31',NULL),(730968,167,'2016-08-13 18:58:31',NULL),(730968,129,'2016-08-13 18:58:31',NULL),(730968,130,'2016-08-13 18:58:31',NULL),(730968,133,'2016-08-13 18:58:31',NULL),(730968,84,'2016-08-13 18:58:31',NULL),(730968,173,'2016-08-13 18:58:31',NULL),(730968,193,'2016-08-13 18:58:31',NULL),(730968,3,'2016-08-13 21:25:55',NULL),(730968,54,'2016-08-13 21:25:55',NULL),(730968,75,'2016-08-13 21:25:55',NULL),(730968,128,'2016-08-13 21:25:55',NULL),(730968,119,'2016-08-13 21:25:55',NULL),(730968,1,'2016-08-13 21:25:55',NULL),(730968,5,'2016-08-13 21:25:55',NULL),(730968,17,'2016-08-13 21:25:55',NULL),(730968,25,'2016-08-13 21:25:55',NULL),(730968,28,'2016-08-13 21:25:55',NULL),(730968,31,'2016-08-13 21:25:55',NULL),(730968,32,'2016-08-13 21:25:55',NULL),(730968,34,'2016-08-13 21:25:55',NULL),(730968,18,'2016-08-13 21:25:55',NULL),(730968,36,'2016-08-13 21:25:55',NULL),(730968,37,'2016-08-13 21:25:55',NULL),(730968,38,'2016-08-13 21:25:55',NULL),(730968,41,'2016-08-13 21:25:55',NULL),(730968,42,'2016-08-13 21:25:55',NULL),(730968,44,'2016-08-13 21:25:55',NULL),(730968,149,'2016-08-13 21:25:55',NULL),(730968,181,'2016-08-13 21:25:55',NULL),(730968,2,'2016-08-13 21:25:55',NULL),(730968,4,'2016-08-13 21:25:55',NULL),(730968,22,'2016-08-13 21:25:55',NULL),(730968,79,'2016-08-13 21:25:55',NULL),(730968,49,'2016-08-13 21:25:55',NULL),(730968,51,'2016-08-13 21:25:55',NULL),(730968,52,'2016-08-13 21:25:55',NULL),(730968,70,'2016-08-13 21:25:55',NULL),(730968,71,'2016-08-13 21:25:55',NULL),(730968,76,'2016-08-13 21:25:55',NULL),(730968,85,'2016-08-13 21:25:55',NULL),(730968,77,'2016-08-13 21:25:55',NULL),(730968,167,'2016-08-13 21:25:55',NULL),(730968,129,'2016-08-13 21:25:55',NULL),(730968,130,'2016-08-13 21:25:55',NULL),(730968,133,'2016-08-13 21:25:55',NULL),(730968,84,'2016-08-13 21:25:55',NULL),(730968,173,'2016-08-13 21:25:55',NULL),(730968,193,'2016-08-13 21:25:55',NULL),(730968,3,'2016-08-13 21:29:09',NULL),(730968,54,'2016-08-13 21:29:09',NULL),(730968,75,'2016-08-13 21:29:09',NULL),(730968,128,'2016-08-13 21:29:09',NULL),(730968,119,'2016-08-13 21:29:09',NULL),(730968,1,'2016-08-13 21:29:09',NULL),(730968,5,'2016-08-13 21:29:09',NULL),(730968,17,'2016-08-13 21:29:09',NULL),(730968,25,'2016-08-13 21:29:09',NULL),(730968,28,'2016-08-13 21:29:09',NULL),(730968,31,'2016-08-13 21:29:09',NULL),(730968,32,'2016-08-13 21:29:09',NULL),(730968,34,'2016-08-13 21:29:09',NULL),(730968,18,'2016-08-13 21:29:09',NULL),(730968,36,'2016-08-13 21:29:09',NULL),(730968,37,'2016-08-13 21:29:09',NULL),(730968,38,'2016-08-13 21:29:09',NULL),(730968,41,'2016-08-13 21:29:09',NULL),(730968,42,'2016-08-13 21:29:09',NULL),(730968,44,'2016-08-13 21:29:09',NULL),(730968,149,'2016-08-13 21:29:09',NULL),(730968,181,'2016-08-13 21:29:09',NULL),(730968,2,'2016-08-13 21:29:09',NULL),(730968,4,'2016-08-13 21:29:09',NULL),(730968,22,'2016-08-13 21:29:09',NULL),(730968,79,'2016-08-13 21:29:09',NULL),(730968,49,'2016-08-13 21:29:09',NULL),(730968,51,'2016-08-13 21:29:09',NULL),(730968,52,'2016-08-13 21:29:09',NULL),(730968,70,'2016-08-13 21:29:09',NULL),(730968,71,'2016-08-13 21:29:09',NULL),(730968,76,'2016-08-13 21:29:09',NULL),(730968,85,'2016-08-13 21:29:09',NULL),(730968,77,'2016-08-13 21:29:09',NULL),(730968,167,'2016-08-13 21:29:09',NULL),(730968,129,'2016-08-13 21:29:09',NULL),(730968,130,'2016-08-13 21:29:09',NULL),(730968,133,'2016-08-13 21:29:09',NULL),(730968,84,'2016-08-13 21:29:09',NULL),(730968,173,'2016-08-13 21:29:09',NULL),(730968,193,'2016-08-13 21:29:09',NULL),(730968,3,'2016-08-13 21:32:20',NULL),(730968,54,'2016-08-13 21:32:20',NULL),(730968,75,'2016-08-13 21:32:20',NULL),(730968,128,'2016-08-13 21:32:20',NULL),(730968,119,'2016-08-13 21:32:20',NULL),(730968,1,'2016-08-13 21:32:20',NULL),(730968,5,'2016-08-13 21:32:20',NULL),(730968,17,'2016-08-13 21:32:20',NULL),(730968,25,'2016-08-13 21:32:20',NULL),(730968,28,'2016-08-13 21:32:20',NULL),(730968,31,'2016-08-13 21:32:20',NULL),(730968,32,'2016-08-13 21:32:20',NULL),(730968,34,'2016-08-13 21:32:20',NULL),(730968,18,'2016-08-13 21:32:20',NULL),(730968,36,'2016-08-13 21:32:20',NULL),(730968,37,'2016-08-13 21:32:20',NULL),(730968,38,'2016-08-13 21:32:20',NULL),(730968,41,'2016-08-13 21:32:20',NULL),(730968,42,'2016-08-13 21:32:20',NULL),(730968,44,'2016-08-13 21:32:20',NULL),(730968,149,'2016-08-13 21:32:20',NULL),(730968,181,'2016-08-13 21:32:20',NULL),(730968,2,'2016-08-13 21:32:20',NULL),(730968,4,'2016-08-13 21:32:20',NULL),(730968,22,'2016-08-13 21:32:20',NULL),(730968,79,'2016-08-13 21:32:20',NULL),(730968,49,'2016-08-13 21:32:20',NULL),(730968,51,'2016-08-13 21:32:20',NULL),(730968,52,'2016-08-13 21:32:20',NULL),(730968,70,'2016-08-13 21:32:20',NULL),(730968,71,'2016-08-13 21:32:20',NULL),(730968,76,'2016-08-13 21:32:20',NULL),(730968,85,'2016-08-13 21:32:20',NULL),(730968,77,'2016-08-13 21:32:20',NULL),(730968,167,'2016-08-13 21:32:20',NULL),(730968,129,'2016-08-13 21:32:20',NULL),(730968,130,'2016-08-13 21:32:20',NULL),(730968,133,'2016-08-13 21:32:20',NULL),(730968,84,'2016-08-13 21:32:20',NULL),(730968,173,'2016-08-13 21:32:20',NULL),(730968,193,'2016-08-13 21:32:20',NULL),(730968,3,'2016-08-13 21:33:04',NULL),(730968,54,'2016-08-13 21:33:04',NULL),(730968,75,'2016-08-13 21:33:04',NULL),(730968,128,'2016-08-13 21:33:04',NULL),(730968,119,'2016-08-13 21:33:04',NULL),(730968,1,'2016-08-13 21:33:04',NULL),(730968,5,'2016-08-13 21:33:04',NULL),(730968,17,'2016-08-13 21:33:04',NULL),(730968,25,'2016-08-13 21:33:04',NULL),(730968,28,'2016-08-13 21:33:04',NULL),(730968,31,'2016-08-13 21:33:04',NULL),(730968,32,'2016-08-13 21:33:04',NULL),(730968,34,'2016-08-13 21:33:04',NULL),(730968,18,'2016-08-13 21:33:04',NULL),(730968,36,'2016-08-13 21:33:04',NULL),(730968,37,'2016-08-13 21:33:04',NULL),(730968,38,'2016-08-13 21:33:04',NULL),(730968,41,'2016-08-13 21:33:04',NULL),(730968,42,'2016-08-13 21:33:04',NULL),(730968,44,'2016-08-13 21:33:04',NULL),(730968,149,'2016-08-13 21:33:04',NULL),(730968,181,'2016-08-13 21:33:04',NULL),(730968,2,'2016-08-13 21:33:04',NULL),(730968,4,'2016-08-13 21:33:04',NULL),(730968,22,'2016-08-13 21:33:04',NULL),(730968,79,'2016-08-13 21:33:04',NULL),(730968,49,'2016-08-13 21:33:04',NULL),(730968,51,'2016-08-13 21:33:04',NULL),(730968,52,'2016-08-13 21:33:04',NULL),(730968,70,'2016-08-13 21:33:04',NULL),(730968,71,'2016-08-13 21:33:04',NULL),(730968,76,'2016-08-13 21:33:04',NULL),(730968,85,'2016-08-13 21:33:04',NULL),(730968,77,'2016-08-13 21:33:04',NULL),(730968,167,'2016-08-13 21:33:04',NULL),(730968,129,'2016-08-13 21:33:04',NULL),(730968,130,'2016-08-13 21:33:04',NULL),(730968,133,'2016-08-13 21:33:04',NULL),(730968,84,'2016-08-13 21:33:04',NULL),(730968,173,'2016-08-13 21:33:04',NULL),(730968,193,'2016-08-13 21:33:04',NULL),(730968,3,'2016-08-13 21:43:18',NULL),(730968,54,'2016-08-13 21:43:18',NULL),(730968,75,'2016-08-13 21:43:18',NULL),(730968,128,'2016-08-13 21:43:18',NULL),(730968,119,'2016-08-13 21:43:18',NULL),(730968,1,'2016-08-13 21:43:18',NULL),(730968,5,'2016-08-13 21:43:18',NULL),(730968,17,'2016-08-13 21:43:18',NULL),(730968,25,'2016-08-13 21:43:18',NULL),(730968,28,'2016-08-13 21:43:18',NULL),(730968,31,'2016-08-13 21:43:18',NULL),(730968,32,'2016-08-13 21:43:18',NULL),(730968,34,'2016-08-13 21:43:18',NULL),(730968,18,'2016-08-13 21:43:18',NULL),(730968,36,'2016-08-13 21:43:18',NULL),(730968,37,'2016-08-13 21:43:18',NULL),(730968,38,'2016-08-13 21:43:18',NULL),(730968,41,'2016-08-13 21:43:18',NULL),(730968,42,'2016-08-13 21:43:18',NULL),(730968,44,'2016-08-13 21:43:18',NULL),(730968,149,'2016-08-13 21:43:18',NULL),(730968,181,'2016-08-13 21:43:18',NULL),(730968,2,'2016-08-13 21:43:18',NULL),(730968,4,'2016-08-13 21:43:18',NULL),(730968,22,'2016-08-13 21:43:18',NULL),(730968,79,'2016-08-13 21:43:18',NULL),(730968,49,'2016-08-13 21:43:18',NULL),(730968,51,'2016-08-13 21:43:18',NULL),(730968,52,'2016-08-13 21:43:18',NULL),(730968,70,'2016-08-13 21:43:18',NULL),(730968,71,'2016-08-13 21:43:18',NULL),(730968,76,'2016-08-13 21:43:18',NULL),(730968,85,'2016-08-13 21:43:18',NULL),(730968,77,'2016-08-13 21:43:18',NULL),(730968,167,'2016-08-13 21:43:18',NULL),(730968,129,'2016-08-13 21:43:18',NULL),(730968,130,'2016-08-13 21:43:18',NULL),(730968,133,'2016-08-13 21:43:18',NULL),(730968,84,'2016-08-13 21:43:18',NULL),(730968,173,'2016-08-13 21:43:18',NULL),(730968,193,'2016-08-13 21:43:18',NULL),(730968,3,'2016-08-13 21:43:37',NULL),(730968,54,'2016-08-13 21:43:37',NULL),(730968,75,'2016-08-13 21:43:37',NULL),(730968,128,'2016-08-13 21:43:37',NULL),(730968,119,'2016-08-13 21:43:37',NULL),(730968,1,'2016-08-13 21:43:37',NULL),(730968,5,'2016-08-13 21:43:37',NULL),(730968,17,'2016-08-13 21:43:37',NULL),(730968,25,'2016-08-13 21:43:37',NULL),(730968,28,'2016-08-13 21:43:37',NULL),(730968,31,'2016-08-13 21:43:37',NULL),(730968,32,'2016-08-13 21:43:37',NULL),(730968,34,'2016-08-13 21:43:37',NULL),(730968,18,'2016-08-13 21:43:37',NULL),(730968,36,'2016-08-13 21:43:37',NULL),(730968,37,'2016-08-13 21:43:37',NULL),(730968,38,'2016-08-13 21:43:37',NULL),(730968,41,'2016-08-13 21:43:37',NULL),(730968,42,'2016-08-13 21:43:37',NULL),(730968,44,'2016-08-13 21:43:37',NULL),(730968,149,'2016-08-13 21:43:37',NULL),(730968,181,'2016-08-13 21:43:37',NULL),(730968,2,'2016-08-13 21:43:37',NULL),(730968,4,'2016-08-13 21:43:37',NULL),(730968,22,'2016-08-13 21:43:37',NULL),(730968,79,'2016-08-13 21:43:37',NULL),(730968,49,'2016-08-13 21:43:37',NULL),(730968,51,'2016-08-13 21:43:37',NULL),(730968,52,'2016-08-13 21:43:37',NULL),(730968,70,'2016-08-13 21:43:37',NULL),(730968,71,'2016-08-13 21:43:37',NULL),(730968,76,'2016-08-13 21:43:37',NULL),(730968,85,'2016-08-13 21:43:37',NULL),(730968,77,'2016-08-13 21:43:37',NULL),(730968,167,'2016-08-13 21:43:37',NULL),(730968,129,'2016-08-13 21:43:37',NULL),(730968,130,'2016-08-13 21:43:37',NULL),(730968,133,'2016-08-13 21:43:37',NULL),(730968,84,'2016-08-13 21:43:37',NULL),(730968,173,'2016-08-13 21:43:37',NULL),(730968,193,'2016-08-13 21:43:37',NULL),(730968,3,'2016-08-13 23:19:20',NULL),(730968,54,'2016-08-13 23:19:20',NULL),(730968,75,'2016-08-13 23:19:20',NULL),(730968,128,'2016-08-13 23:19:20',NULL),(730968,119,'2016-08-13 23:19:20',NULL),(730968,1,'2016-08-13 23:19:20',NULL),(730968,5,'2016-08-13 23:19:20',NULL),(730968,17,'2016-08-13 23:19:20',NULL),(730968,25,'2016-08-13 23:19:20',NULL),(730968,28,'2016-08-13 23:19:20',NULL),(730968,31,'2016-08-13 23:19:20',NULL),(730968,32,'2016-08-13 23:19:20',NULL),(730968,34,'2016-08-13 23:19:20',NULL),(730968,18,'2016-08-13 23:19:20',NULL),(730968,36,'2016-08-13 23:19:20',NULL),(730968,37,'2016-08-13 23:19:20',NULL),(730968,38,'2016-08-13 23:19:20',NULL),(730968,41,'2016-08-13 23:19:20',NULL),(730968,42,'2016-08-13 23:19:20',NULL),(730968,44,'2016-08-13 23:19:20',NULL),(730968,149,'2016-08-13 23:19:20',NULL),(730968,181,'2016-08-13 23:19:20',NULL),(730968,2,'2016-08-13 23:19:20',NULL),(730968,4,'2016-08-13 23:19:20',NULL),(730968,22,'2016-08-13 23:19:20',NULL),(730968,79,'2016-08-13 23:19:20',NULL),(730968,49,'2016-08-13 23:19:20',NULL),(730968,51,'2016-08-13 23:19:20',NULL),(730968,52,'2016-08-13 23:19:20',NULL),(730968,70,'2016-08-13 23:19:20',NULL),(730968,71,'2016-08-13 23:19:20',NULL),(730968,76,'2016-08-13 23:19:20',NULL),(730968,85,'2016-08-13 23:19:20',NULL),(730968,77,'2016-08-13 23:19:20',NULL),(730968,167,'2016-08-13 23:19:20',NULL),(730968,129,'2016-08-13 23:19:20',NULL),(730968,130,'2016-08-13 23:19:20',NULL),(730968,133,'2016-08-13 23:19:20',NULL),(730968,84,'2016-08-13 23:19:20',NULL),(730968,173,'2016-08-13 23:19:20',NULL),(730968,193,'2016-08-13 23:19:20',NULL),(730968,3,'2016-08-13 23:19:29',NULL),(730968,54,'2016-08-13 23:19:29',NULL),(730968,75,'2016-08-13 23:19:29',NULL),(730968,128,'2016-08-13 23:19:29',NULL),(730968,119,'2016-08-13 23:19:29',NULL),(730968,1,'2016-08-13 23:19:29',NULL),(730968,5,'2016-08-13 23:19:29',NULL),(730968,17,'2016-08-13 23:19:29',NULL),(730968,25,'2016-08-13 23:19:29',NULL),(730968,28,'2016-08-13 23:19:29',NULL),(730968,31,'2016-08-13 23:19:29',NULL),(730968,32,'2016-08-13 23:19:29',NULL),(730968,34,'2016-08-13 23:19:29',NULL),(730968,18,'2016-08-13 23:19:29',NULL),(730968,36,'2016-08-13 23:19:29',NULL),(730968,37,'2016-08-13 23:19:29',NULL),(730968,38,'2016-08-13 23:19:29',NULL),(730968,41,'2016-08-13 23:19:29',NULL),(730968,42,'2016-08-13 23:19:29',NULL),(730968,44,'2016-08-13 23:19:29',NULL),(730968,149,'2016-08-13 23:19:29',NULL),(730968,181,'2016-08-13 23:19:29',NULL),(730968,2,'2016-08-13 23:19:29',NULL),(730968,4,'2016-08-13 23:19:29',NULL),(730968,22,'2016-08-13 23:19:29',NULL),(730968,79,'2016-08-13 23:19:29',NULL),(730968,49,'2016-08-13 23:19:29',NULL),(730968,51,'2016-08-13 23:19:29',NULL),(730968,52,'2016-08-13 23:19:29',NULL),(730968,70,'2016-08-13 23:19:29',NULL),(730968,71,'2016-08-13 23:19:29',NULL),(730968,76,'2016-08-13 23:19:29',NULL),(730968,85,'2016-08-13 23:19:29',NULL),(730968,77,'2016-08-13 23:19:29',NULL),(730968,167,'2016-08-13 23:19:29',NULL),(730968,129,'2016-08-13 23:19:29',NULL),(730968,130,'2016-08-13 23:19:29',NULL),(730968,133,'2016-08-13 23:19:29',NULL),(730968,84,'2016-08-13 23:19:29',NULL),(730968,173,'2016-08-13 23:19:29',NULL),(730968,193,'2016-08-13 23:19:29',NULL),(730968,3,'2016-08-15 18:35:41',NULL),(730968,54,'2016-08-15 18:35:41',NULL),(730968,75,'2016-08-15 18:35:41',NULL),(730968,128,'2016-08-15 18:35:41',NULL),(730968,119,'2016-08-15 18:35:41',NULL),(730968,1,'2016-08-15 18:35:41',NULL),(730968,5,'2016-08-15 18:35:41',NULL),(730968,17,'2016-08-15 18:35:41',NULL),(730968,25,'2016-08-15 18:35:41',NULL),(730968,28,'2016-08-15 18:35:41',NULL),(730968,31,'2016-08-15 18:35:41',NULL),(730968,32,'2016-08-15 18:35:41',NULL),(730968,34,'2016-08-15 18:35:41',NULL),(730968,18,'2016-08-15 18:35:41',NULL),(730968,36,'2016-08-15 18:35:41',NULL),(730968,37,'2016-08-15 18:35:41',NULL),(730968,38,'2016-08-15 18:35:41',NULL),(730968,41,'2016-08-15 18:35:41',NULL),(730968,42,'2016-08-15 18:35:41',NULL),(730968,44,'2016-08-15 18:35:41',NULL),(730968,149,'2016-08-15 18:35:41',NULL),(730968,181,'2016-08-15 18:35:41',NULL),(730968,2,'2016-08-15 18:35:41',NULL),(730968,4,'2016-08-15 18:35:41',NULL),(730968,22,'2016-08-15 18:35:41',NULL),(730968,79,'2016-08-15 18:35:41',NULL),(730968,49,'2016-08-15 18:35:41',NULL),(730968,51,'2016-08-15 18:35:41',NULL),(730968,52,'2016-08-15 18:35:41',NULL),(730968,70,'2016-08-15 18:35:41',NULL),(730968,71,'2016-08-15 18:35:41',NULL),(730968,76,'2016-08-15 18:35:41',NULL),(730968,85,'2016-08-15 18:35:41',NULL),(730968,77,'2016-08-15 18:35:41',NULL),(730968,167,'2016-08-15 18:35:41',NULL),(730968,129,'2016-08-15 18:35:41',NULL),(730968,130,'2016-08-15 18:35:41',NULL),(730968,133,'2016-08-15 18:35:41',NULL),(730968,84,'2016-08-15 18:35:41',NULL),(730968,173,'2016-08-15 18:35:41',NULL),(730968,193,'2016-08-15 18:35:41',NULL),(730968,3,'2016-08-15 19:03:54',NULL),(730968,54,'2016-08-15 19:03:54',NULL),(730968,75,'2016-08-15 19:03:54',NULL),(730968,128,'2016-08-15 19:03:54',NULL),(730968,119,'2016-08-15 19:03:54',NULL),(730968,1,'2016-08-15 19:03:54',NULL),(730968,5,'2016-08-15 19:03:54',NULL),(730968,17,'2016-08-15 19:03:54',NULL),(730968,25,'2016-08-15 19:03:54',NULL),(730968,28,'2016-08-15 19:03:54',NULL),(730968,31,'2016-08-15 19:03:54',NULL),(730968,32,'2016-08-15 19:03:54',NULL),(730968,34,'2016-08-15 19:03:54',NULL),(730968,18,'2016-08-15 19:03:54',NULL),(730968,36,'2016-08-15 19:03:54',NULL),(730968,37,'2016-08-15 19:03:54',NULL),(730968,38,'2016-08-15 19:03:54',NULL),(730968,41,'2016-08-15 19:03:54',NULL),(730968,42,'2016-08-15 19:03:54',NULL),(730968,44,'2016-08-15 19:03:54',NULL),(730968,149,'2016-08-15 19:03:54',NULL),(730968,181,'2016-08-15 19:03:54',NULL),(730968,2,'2016-08-15 19:03:54',NULL),(730968,4,'2016-08-15 19:03:54',NULL),(730968,22,'2016-08-15 19:03:54',NULL),(730968,79,'2016-08-15 19:03:54',NULL),(730968,49,'2016-08-15 19:03:54',NULL),(730968,51,'2016-08-15 19:03:54',NULL),(730968,52,'2016-08-15 19:03:54',NULL),(730968,70,'2016-08-15 19:03:54',NULL),(730968,71,'2016-08-15 19:03:54',NULL),(730968,76,'2016-08-15 19:03:54',NULL),(730968,85,'2016-08-15 19:03:54',NULL),(730968,77,'2016-08-15 19:03:54',NULL),(730968,167,'2016-08-15 19:03:54',NULL),(730968,129,'2016-08-15 19:03:54',NULL),(730968,130,'2016-08-15 19:03:54',NULL),(730968,133,'2016-08-15 19:03:54',NULL),(730968,84,'2016-08-15 19:03:54',NULL),(730968,173,'2016-08-15 19:03:54',NULL),(730968,193,'2016-08-15 19:03:54',NULL),(730968,3,'2016-08-15 19:04:13',NULL),(730968,54,'2016-08-15 19:04:13',NULL),(730968,75,'2016-08-15 19:04:13',NULL),(730968,128,'2016-08-15 19:04:13',NULL),(730968,119,'2016-08-15 19:04:13',NULL),(730968,1,'2016-08-15 19:04:13',NULL),(730968,5,'2016-08-15 19:04:13',NULL),(730968,17,'2016-08-15 19:04:13',NULL),(730968,25,'2016-08-15 19:04:13',NULL),(730968,28,'2016-08-15 19:04:13',NULL),(730968,31,'2016-08-15 19:04:13',NULL),(730968,32,'2016-08-15 19:04:13',NULL),(730968,34,'2016-08-15 19:04:13',NULL),(730968,18,'2016-08-15 19:04:13',NULL),(730968,36,'2016-08-15 19:04:13',NULL),(730968,37,'2016-08-15 19:04:13',NULL),(730968,38,'2016-08-15 19:04:13',NULL),(730968,41,'2016-08-15 19:04:13',NULL),(730968,42,'2016-08-15 19:04:13',NULL),(730968,44,'2016-08-15 19:04:13',NULL),(730968,149,'2016-08-15 19:04:13',NULL),(730968,181,'2016-08-15 19:04:13',NULL),(730968,2,'2016-08-15 19:04:13',NULL),(730968,4,'2016-08-15 19:04:13',NULL),(730968,22,'2016-08-15 19:04:13',NULL),(730968,79,'2016-08-15 19:04:13',NULL),(730968,49,'2016-08-15 19:04:13',NULL),(730968,51,'2016-08-15 19:04:13',NULL),(730968,52,'2016-08-15 19:04:13',NULL),(730968,70,'2016-08-15 19:04:13',NULL),(730968,71,'2016-08-15 19:04:13',NULL),(730968,76,'2016-08-15 19:04:13',NULL),(730968,85,'2016-08-15 19:04:13',NULL),(730968,77,'2016-08-15 19:04:13',NULL),(730968,167,'2016-08-15 19:04:13',NULL),(730968,129,'2016-08-15 19:04:13',NULL),(730968,130,'2016-08-15 19:04:13',NULL),(730968,133,'2016-08-15 19:04:13',NULL),(730968,84,'2016-08-15 19:04:13',NULL),(730968,173,'2016-08-15 19:04:13',NULL),(730968,193,'2016-08-15 19:04:13',NULL),(730968,3,'2016-08-15 19:06:21',NULL),(730968,54,'2016-08-15 19:06:21',NULL),(730968,75,'2016-08-15 19:06:21',NULL),(730968,128,'2016-08-15 19:06:21',NULL),(730968,119,'2016-08-15 19:06:21',NULL),(730968,1,'2016-08-15 19:06:21',NULL),(730968,5,'2016-08-15 19:06:21',NULL),(730968,17,'2016-08-15 19:06:21',NULL),(730968,25,'2016-08-15 19:06:21',NULL),(730968,28,'2016-08-15 19:06:21',NULL),(730968,31,'2016-08-15 19:06:21',NULL),(730968,32,'2016-08-15 19:06:21',NULL),(730968,34,'2016-08-15 19:06:21',NULL),(730968,18,'2016-08-15 19:06:21',NULL),(730968,36,'2016-08-15 19:06:21',NULL),(730968,37,'2016-08-15 19:06:21',NULL),(730968,38,'2016-08-15 19:06:21',NULL),(730968,41,'2016-08-15 19:06:21',NULL),(730968,42,'2016-08-15 19:06:21',NULL),(730968,44,'2016-08-15 19:06:21',NULL),(730968,149,'2016-08-15 19:06:21',NULL),(730968,181,'2016-08-15 19:06:21',NULL),(730968,2,'2016-08-15 19:06:21',NULL),(730968,4,'2016-08-15 19:06:21',NULL),(730968,22,'2016-08-15 19:06:21',NULL),(730968,79,'2016-08-15 19:06:21',NULL),(730968,49,'2016-08-15 19:06:21',NULL),(730968,51,'2016-08-15 19:06:21',NULL),(730968,52,'2016-08-15 19:06:21',NULL),(730968,70,'2016-08-15 19:06:21',NULL),(730968,71,'2016-08-15 19:06:21',NULL),(730968,76,'2016-08-15 19:06:21',NULL),(730968,85,'2016-08-15 19:06:21',NULL),(730968,77,'2016-08-15 19:06:21',NULL),(730968,167,'2016-08-15 19:06:21',NULL),(730968,129,'2016-08-15 19:06:21',NULL),(730968,130,'2016-08-15 19:06:21',NULL),(730968,133,'2016-08-15 19:06:21',NULL),(730968,84,'2016-08-15 19:06:21',NULL),(730968,173,'2016-08-15 19:06:21',NULL),(730968,193,'2016-08-15 19:06:21',NULL),(730968,3,'2016-08-15 19:10:07',NULL),(730968,54,'2016-08-15 19:10:07',NULL),(730968,75,'2016-08-15 19:10:07',NULL),(730968,128,'2016-08-15 19:10:07',NULL),(730968,119,'2016-08-15 19:10:07',NULL),(730968,1,'2016-08-15 19:10:07',NULL),(730968,5,'2016-08-15 19:10:07',NULL),(730968,17,'2016-08-15 19:10:07',NULL),(730968,25,'2016-08-15 19:10:07',NULL),(730968,28,'2016-08-15 19:10:07',NULL),(730968,31,'2016-08-15 19:10:07',NULL),(730968,32,'2016-08-15 19:10:07',NULL),(730968,34,'2016-08-15 19:10:07',NULL),(730968,18,'2016-08-15 19:10:07',NULL),(730968,36,'2016-08-15 19:10:07',NULL),(730968,37,'2016-08-15 19:10:07',NULL),(730968,38,'2016-08-15 19:10:07',NULL),(730968,41,'2016-08-15 19:10:07',NULL),(730968,42,'2016-08-15 19:10:07',NULL),(730968,44,'2016-08-15 19:10:07',NULL),(730968,149,'2016-08-15 19:10:07',NULL),(730968,181,'2016-08-15 19:10:07',NULL),(730968,2,'2016-08-15 19:10:07',NULL),(730968,4,'2016-08-15 19:10:07',NULL),(730968,22,'2016-08-15 19:10:07',NULL),(730968,79,'2016-08-15 19:10:07',NULL),(730968,49,'2016-08-15 19:10:07',NULL),(730968,51,'2016-08-15 19:10:07',NULL),(730968,52,'2016-08-15 19:10:07',NULL),(730968,70,'2016-08-15 19:10:07',NULL),(730968,71,'2016-08-15 19:10:07',NULL),(730968,76,'2016-08-15 19:10:07',NULL),(730968,85,'2016-08-15 19:10:07',NULL),(730968,77,'2016-08-15 19:10:07',NULL),(730968,167,'2016-08-15 19:10:07',NULL),(730968,129,'2016-08-15 19:10:07',NULL),(730968,130,'2016-08-15 19:10:07',NULL),(730968,133,'2016-08-15 19:10:07',NULL),(730968,84,'2016-08-15 19:10:07',NULL),(730968,173,'2016-08-15 19:10:07',NULL),(730968,193,'2016-08-15 19:10:07',NULL),(730968,3,'2016-08-15 19:11:21',NULL),(730968,54,'2016-08-15 19:11:21',NULL),(730968,75,'2016-08-15 19:11:21',NULL),(730968,128,'2016-08-15 19:11:21',NULL),(730968,119,'2016-08-15 19:11:21',NULL),(730968,1,'2016-08-15 19:11:21',NULL),(730968,5,'2016-08-15 19:11:21',NULL),(730968,17,'2016-08-15 19:11:21',NULL),(730968,25,'2016-08-15 19:11:21',NULL),(730968,28,'2016-08-15 19:11:21',NULL),(730968,31,'2016-08-15 19:11:21',NULL),(730968,32,'2016-08-15 19:11:21',NULL),(730968,34,'2016-08-15 19:11:21',NULL),(730968,18,'2016-08-15 19:11:21',NULL),(730968,36,'2016-08-15 19:11:21',NULL),(730968,37,'2016-08-15 19:11:21',NULL),(730968,38,'2016-08-15 19:11:21',NULL),(730968,41,'2016-08-15 19:11:21',NULL),(730968,42,'2016-08-15 19:11:21',NULL),(730968,44,'2016-08-15 19:11:21',NULL),(730968,149,'2016-08-15 19:11:21',NULL),(730968,181,'2016-08-15 19:11:21',NULL),(730968,2,'2016-08-15 19:11:21',NULL),(730968,4,'2016-08-15 19:11:21',NULL),(730968,22,'2016-08-15 19:11:21',NULL),(730968,79,'2016-08-15 19:11:21',NULL),(730968,49,'2016-08-15 19:11:21',NULL),(730968,51,'2016-08-15 19:11:21',NULL),(730968,52,'2016-08-15 19:11:21',NULL),(730968,70,'2016-08-15 19:11:21',NULL),(730968,71,'2016-08-15 19:11:21',NULL),(730968,76,'2016-08-15 19:11:21',NULL),(730968,85,'2016-08-15 19:11:21',NULL),(730968,77,'2016-08-15 19:11:21',NULL),(730968,167,'2016-08-15 19:11:21',NULL),(730968,129,'2016-08-15 19:11:21',NULL),(730968,130,'2016-08-15 19:11:21',NULL),(730968,133,'2016-08-15 19:11:21',NULL),(730968,84,'2016-08-15 19:11:21',NULL),(730968,173,'2016-08-15 19:11:21',NULL),(730968,193,'2016-08-15 19:11:21',NULL);
/*!40000 ALTER TABLE `characters_minions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `characters_mounts`
--

DROP TABLE IF EXISTS `characters_mounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `characters_mounts` (
  `lodestone_id` int(32) NOT NULL,
  `mount_id` int(32) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `other` varchar(512) DEFAULT NULL,
  UNIQUE KEY `unique` (`lodestone_id`,`mount_id`,`other`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `characters_mounts`
--

LOCK TABLES `characters_mounts` WRITE;
/*!40000 ALTER TABLE `characters_mounts` DISABLE KEYS */;
INSERT INTO `characters_mounts` VALUES (730968,1,'2016-08-03 22:15:06',NULL),(730968,5,'2016-08-03 22:15:06',NULL),(730968,45,'2016-08-03 22:15:06',NULL),(730968,25,'2016-08-03 22:15:06',NULL),(730968,6,'2016-08-03 22:15:06',NULL),(730968,4,'2016-08-03 22:15:06',NULL),(730968,8,'2016-08-03 22:15:06',NULL),(730968,9,'2016-08-03 22:15:06',NULL),(730968,18,'2016-08-03 22:15:06',NULL),(730968,54,'2016-08-03 22:15:06',NULL),(730968,74,'2016-08-03 22:15:06',NULL),(730968,55,'2016-08-03 22:15:06',NULL),(730968,15,'2016-08-03 22:15:06',NULL),(730968,50,'2016-08-03 22:15:06',NULL),(730968,1,'2016-08-03 22:15:17',NULL),(730968,5,'2016-08-03 22:15:17',NULL),(730968,45,'2016-08-03 22:15:17',NULL),(730968,25,'2016-08-03 22:15:17',NULL),(730968,6,'2016-08-03 22:15:17',NULL),(730968,4,'2016-08-03 22:15:17',NULL),(730968,8,'2016-08-03 22:15:17',NULL),(730968,9,'2016-08-03 22:15:17',NULL),(730968,18,'2016-08-03 22:15:17',NULL),(730968,54,'2016-08-03 22:15:17',NULL),(730968,74,'2016-08-03 22:15:17',NULL),(730968,55,'2016-08-03 22:15:17',NULL),(730968,15,'2016-08-03 22:15:17',NULL),(730968,50,'2016-08-03 22:15:17',NULL),(730968,1,'2016-08-07 00:12:19',NULL),(730968,5,'2016-08-07 00:12:19',NULL),(730968,45,'2016-08-07 00:12:19',NULL),(730968,25,'2016-08-07 00:12:19',NULL),(730968,6,'2016-08-07 00:12:19',NULL),(730968,4,'2016-08-07 00:12:19',NULL),(730968,8,'2016-08-07 00:12:19',NULL),(730968,9,'2016-08-07 00:12:19',NULL),(730968,18,'2016-08-07 00:12:19',NULL),(730968,54,'2016-08-07 00:12:19',NULL),(730968,74,'2016-08-07 00:12:19',NULL),(730968,55,'2016-08-07 00:12:19',NULL),(730968,15,'2016-08-07 00:12:19',NULL),(730968,50,'2016-08-07 00:12:19',NULL),(730968,1,'2016-08-13 18:58:31',NULL),(730968,5,'2016-08-13 18:58:31',NULL),(730968,45,'2016-08-13 18:58:31',NULL),(730968,25,'2016-08-13 18:58:31',NULL),(730968,6,'2016-08-13 18:58:31',NULL),(730968,4,'2016-08-13 18:58:31',NULL),(730968,8,'2016-08-13 18:58:31',NULL),(730968,9,'2016-08-13 18:58:31',NULL),(730968,18,'2016-08-13 18:58:31',NULL),(730968,54,'2016-08-13 18:58:31',NULL),(730968,74,'2016-08-13 18:58:31',NULL),(730968,55,'2016-08-13 18:58:31',NULL),(730968,15,'2016-08-13 18:58:31',NULL),(730968,50,'2016-08-13 18:58:31',NULL),(730968,1,'2016-08-13 21:25:55',NULL),(730968,5,'2016-08-13 21:25:55',NULL),(730968,45,'2016-08-13 21:25:55',NULL),(730968,25,'2016-08-13 21:25:55',NULL),(730968,6,'2016-08-13 21:25:55',NULL),(730968,4,'2016-08-13 21:25:55',NULL),(730968,8,'2016-08-13 21:25:55',NULL),(730968,9,'2016-08-13 21:25:55',NULL),(730968,18,'2016-08-13 21:25:55',NULL),(730968,54,'2016-08-13 21:25:55',NULL),(730968,74,'2016-08-13 21:25:55',NULL),(730968,55,'2016-08-13 21:25:55',NULL),(730968,15,'2016-08-13 21:25:55',NULL),(730968,50,'2016-08-13 21:25:55',NULL),(730968,1,'2016-08-13 21:29:09',NULL),(730968,5,'2016-08-13 21:29:09',NULL),(730968,45,'2016-08-13 21:29:09',NULL),(730968,25,'2016-08-13 21:29:09',NULL),(730968,6,'2016-08-13 21:29:09',NULL),(730968,4,'2016-08-13 21:29:09',NULL),(730968,8,'2016-08-13 21:29:09',NULL),(730968,9,'2016-08-13 21:29:09',NULL),(730968,18,'2016-08-13 21:29:09',NULL),(730968,54,'2016-08-13 21:29:09',NULL),(730968,74,'2016-08-13 21:29:09',NULL),(730968,55,'2016-08-13 21:29:09',NULL),(730968,15,'2016-08-13 21:29:09',NULL),(730968,50,'2016-08-13 21:29:09',NULL),(730968,1,'2016-08-13 21:32:20',NULL),(730968,5,'2016-08-13 21:32:20',NULL),(730968,45,'2016-08-13 21:32:20',NULL),(730968,25,'2016-08-13 21:32:20',NULL),(730968,6,'2016-08-13 21:32:20',NULL),(730968,4,'2016-08-13 21:32:20',NULL),(730968,8,'2016-08-13 21:32:20',NULL),(730968,9,'2016-08-13 21:32:20',NULL),(730968,18,'2016-08-13 21:32:20',NULL),(730968,54,'2016-08-13 21:32:20',NULL),(730968,74,'2016-08-13 21:32:20',NULL),(730968,55,'2016-08-13 21:32:20',NULL),(730968,15,'2016-08-13 21:32:20',NULL),(730968,50,'2016-08-13 21:32:20',NULL),(730968,1,'2016-08-13 21:33:04',NULL),(730968,5,'2016-08-13 21:33:04',NULL),(730968,45,'2016-08-13 21:33:04',NULL),(730968,25,'2016-08-13 21:33:04',NULL),(730968,6,'2016-08-13 21:33:04',NULL),(730968,4,'2016-08-13 21:33:04',NULL),(730968,8,'2016-08-13 21:33:04',NULL),(730968,9,'2016-08-13 21:33:04',NULL),(730968,18,'2016-08-13 21:33:04',NULL),(730968,54,'2016-08-13 21:33:04',NULL),(730968,74,'2016-08-13 21:33:04',NULL),(730968,55,'2016-08-13 21:33:04',NULL),(730968,15,'2016-08-13 21:33:04',NULL),(730968,50,'2016-08-13 21:33:04',NULL),(730968,1,'2016-08-13 21:43:18',NULL),(730968,5,'2016-08-13 21:43:18',NULL),(730968,45,'2016-08-13 21:43:18',NULL),(730968,25,'2016-08-13 21:43:18',NULL),(730968,6,'2016-08-13 21:43:18',NULL),(730968,4,'2016-08-13 21:43:18',NULL),(730968,8,'2016-08-13 21:43:18',NULL),(730968,9,'2016-08-13 21:43:18',NULL),(730968,18,'2016-08-13 21:43:18',NULL),(730968,54,'2016-08-13 21:43:18',NULL),(730968,74,'2016-08-13 21:43:18',NULL),(730968,55,'2016-08-13 21:43:18',NULL),(730968,15,'2016-08-13 21:43:18',NULL),(730968,50,'2016-08-13 21:43:18',NULL),(730968,1,'2016-08-13 21:43:37',NULL),(730968,5,'2016-08-13 21:43:37',NULL),(730968,45,'2016-08-13 21:43:37',NULL),(730968,25,'2016-08-13 21:43:37',NULL),(730968,6,'2016-08-13 21:43:37',NULL),(730968,4,'2016-08-13 21:43:37',NULL),(730968,8,'2016-08-13 21:43:37',NULL),(730968,9,'2016-08-13 21:43:37',NULL),(730968,18,'2016-08-13 21:43:37',NULL),(730968,54,'2016-08-13 21:43:37',NULL),(730968,74,'2016-08-13 21:43:37',NULL),(730968,55,'2016-08-13 21:43:37',NULL),(730968,15,'2016-08-13 21:43:37',NULL),(730968,50,'2016-08-13 21:43:37',NULL),(730968,1,'2016-08-13 23:19:20',NULL),(730968,5,'2016-08-13 23:19:20',NULL),(730968,45,'2016-08-13 23:19:20',NULL),(730968,25,'2016-08-13 23:19:20',NULL),(730968,6,'2016-08-13 23:19:20',NULL),(730968,4,'2016-08-13 23:19:20',NULL),(730968,8,'2016-08-13 23:19:20',NULL),(730968,9,'2016-08-13 23:19:20',NULL),(730968,18,'2016-08-13 23:19:20',NULL),(730968,54,'2016-08-13 23:19:20',NULL),(730968,74,'2016-08-13 23:19:20',NULL),(730968,55,'2016-08-13 23:19:20',NULL),(730968,15,'2016-08-13 23:19:20',NULL),(730968,50,'2016-08-13 23:19:20',NULL),(730968,1,'2016-08-13 23:19:29',NULL),(730968,5,'2016-08-13 23:19:29',NULL),(730968,45,'2016-08-13 23:19:29',NULL),(730968,25,'2016-08-13 23:19:29',NULL),(730968,6,'2016-08-13 23:19:29',NULL),(730968,4,'2016-08-13 23:19:29',NULL),(730968,8,'2016-08-13 23:19:29',NULL),(730968,9,'2016-08-13 23:19:29',NULL),(730968,18,'2016-08-13 23:19:29',NULL),(730968,54,'2016-08-13 23:19:29',NULL),(730968,74,'2016-08-13 23:19:29',NULL),(730968,55,'2016-08-13 23:19:29',NULL),(730968,15,'2016-08-13 23:19:29',NULL),(730968,50,'2016-08-13 23:19:29',NULL),(730968,1,'2016-08-15 18:35:41',NULL),(730968,5,'2016-08-15 18:35:41',NULL),(730968,45,'2016-08-15 18:35:41',NULL),(730968,25,'2016-08-15 18:35:41',NULL),(730968,6,'2016-08-15 18:35:41',NULL),(730968,4,'2016-08-15 18:35:41',NULL),(730968,8,'2016-08-15 18:35:41',NULL),(730968,9,'2016-08-15 18:35:41',NULL),(730968,18,'2016-08-15 18:35:41',NULL),(730968,54,'2016-08-15 18:35:41',NULL),(730968,74,'2016-08-15 18:35:41',NULL),(730968,55,'2016-08-15 18:35:41',NULL),(730968,15,'2016-08-15 18:35:41',NULL),(730968,50,'2016-08-15 18:35:41',NULL),(730968,1,'2016-08-15 19:03:54',NULL),(730968,5,'2016-08-15 19:03:54',NULL),(730968,45,'2016-08-15 19:03:54',NULL),(730968,25,'2016-08-15 19:03:54',NULL),(730968,6,'2016-08-15 19:03:54',NULL),(730968,4,'2016-08-15 19:03:54',NULL),(730968,8,'2016-08-15 19:03:54',NULL),(730968,9,'2016-08-15 19:03:54',NULL),(730968,18,'2016-08-15 19:03:54',NULL),(730968,54,'2016-08-15 19:03:54',NULL),(730968,74,'2016-08-15 19:03:54',NULL),(730968,55,'2016-08-15 19:03:54',NULL),(730968,15,'2016-08-15 19:03:54',NULL),(730968,50,'2016-08-15 19:03:54',NULL),(730968,1,'2016-08-15 19:04:13',NULL),(730968,5,'2016-08-15 19:04:13',NULL),(730968,45,'2016-08-15 19:04:13',NULL),(730968,25,'2016-08-15 19:04:13',NULL),(730968,6,'2016-08-15 19:04:13',NULL),(730968,4,'2016-08-15 19:04:13',NULL),(730968,8,'2016-08-15 19:04:13',NULL),(730968,9,'2016-08-15 19:04:13',NULL),(730968,18,'2016-08-15 19:04:13',NULL),(730968,54,'2016-08-15 19:04:13',NULL),(730968,74,'2016-08-15 19:04:13',NULL),(730968,55,'2016-08-15 19:04:13',NULL),(730968,15,'2016-08-15 19:04:13',NULL),(730968,50,'2016-08-15 19:04:13',NULL),(730968,1,'2016-08-15 19:06:21',NULL),(730968,5,'2016-08-15 19:06:21',NULL),(730968,45,'2016-08-15 19:06:21',NULL),(730968,25,'2016-08-15 19:06:21',NULL),(730968,6,'2016-08-15 19:06:21',NULL),(730968,4,'2016-08-15 19:06:21',NULL),(730968,8,'2016-08-15 19:06:21',NULL),(730968,9,'2016-08-15 19:06:21',NULL),(730968,18,'2016-08-15 19:06:21',NULL),(730968,54,'2016-08-15 19:06:21',NULL),(730968,74,'2016-08-15 19:06:21',NULL),(730968,55,'2016-08-15 19:06:21',NULL),(730968,15,'2016-08-15 19:06:21',NULL),(730968,50,'2016-08-15 19:06:21',NULL),(730968,1,'2016-08-15 19:10:07',NULL),(730968,5,'2016-08-15 19:10:07',NULL),(730968,45,'2016-08-15 19:10:07',NULL),(730968,25,'2016-08-15 19:10:07',NULL),(730968,6,'2016-08-15 19:10:07',NULL),(730968,4,'2016-08-15 19:10:07',NULL),(730968,8,'2016-08-15 19:10:07',NULL),(730968,9,'2016-08-15 19:10:07',NULL),(730968,18,'2016-08-15 19:10:07',NULL),(730968,54,'2016-08-15 19:10:07',NULL),(730968,74,'2016-08-15 19:10:07',NULL),(730968,55,'2016-08-15 19:10:07',NULL),(730968,15,'2016-08-15 19:10:07',NULL),(730968,50,'2016-08-15 19:10:07',NULL),(730968,1,'2016-08-15 19:11:21',NULL),(730968,5,'2016-08-15 19:11:21',NULL),(730968,45,'2016-08-15 19:11:21',NULL),(730968,25,'2016-08-15 19:11:21',NULL),(730968,6,'2016-08-15 19:11:21',NULL),(730968,4,'2016-08-15 19:11:21',NULL),(730968,8,'2016-08-15 19:11:21',NULL),(730968,9,'2016-08-15 19:11:21',NULL),(730968,18,'2016-08-15 19:11:21',NULL),(730968,54,'2016-08-15 19:11:21',NULL),(730968,74,'2016-08-15 19:11:21',NULL),(730968,55,'2016-08-15 19:11:21',NULL),(730968,15,'2016-08-15 19:11:21',NULL),(730968,50,'2016-08-15 19:11:21',NULL);
/*!40000 ALTER TABLE `characters_mounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events_exp_new`
--

DROP TABLE IF EXISTS `events_exp_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events_exp_new` (
  `lodestone_id` int(32) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `jobclass` smallint(3) NOT NULL,
  `gained` int(16) NOT NULL,
  `old` int(16) NOT NULL,
  `new` int(16) NOT NULL,
  UNIQUE KEY `unique` (`lodestone_id`,`time`,`jobclass`),
  KEY `time` (`time`),
  KEY `jobclass` (`jobclass`),
  KEY `lodestone_id` (`lodestone_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
/*!50100 PARTITION BY RANGE (lodestone_id)
(PARTITION p0 VALUES LESS THAN (250000) ENGINE = InnoDB,
 PARTITION p1 VALUES LESS THAN (500000) ENGINE = InnoDB,
 PARTITION p2 VALUES LESS THAN (750000) ENGINE = InnoDB,
 PARTITION p3 VALUES LESS THAN (1000000) ENGINE = InnoDB,
 PARTITION p4 VALUES LESS THAN (1250000) ENGINE = InnoDB,
 PARTITION p5 VALUES LESS THAN (1500000) ENGINE = InnoDB,
 PARTITION p6 VALUES LESS THAN (1750000) ENGINE = InnoDB,
 PARTITION p7 VALUES LESS THAN (2000000) ENGINE = InnoDB,
 PARTITION p8 VALUES LESS THAN (2250000) ENGINE = InnoDB,
 PARTITION p9 VALUES LESS THAN (2500000) ENGINE = InnoDB,
 PARTITION p10 VALUES LESS THAN (2750000) ENGINE = InnoDB,
 PARTITION p11 VALUES LESS THAN (3000000) ENGINE = InnoDB,
 PARTITION p12 VALUES LESS THAN (3250000) ENGINE = InnoDB,
 PARTITION p13 VALUES LESS THAN (3500000) ENGINE = InnoDB,
 PARTITION p14 VALUES LESS THAN (3750000) ENGINE = InnoDB,
 PARTITION p15 VALUES LESS THAN (4000000) ENGINE = InnoDB,
 PARTITION p16 VALUES LESS THAN (4250000) ENGINE = InnoDB,
 PARTITION p17 VALUES LESS THAN (4500000) ENGINE = InnoDB,
 PARTITION p18 VALUES LESS THAN (4750000) ENGINE = InnoDB,
 PARTITION p19 VALUES LESS THAN (5000000) ENGINE = InnoDB,
 PARTITION p20 VALUES LESS THAN (5250000) ENGINE = InnoDB,
 PARTITION p21 VALUES LESS THAN (5500000) ENGINE = InnoDB,
 PARTITION p22 VALUES LESS THAN (5750000) ENGINE = InnoDB,
 PARTITION p23 VALUES LESS THAN (6000000) ENGINE = InnoDB,
 PARTITION p24 VALUES LESS THAN (6250000) ENGINE = InnoDB,
 PARTITION p25 VALUES LESS THAN (6500000) ENGINE = InnoDB,
 PARTITION p26 VALUES LESS THAN (6750000) ENGINE = InnoDB,
 PARTITION p27 VALUES LESS THAN (7000000) ENGINE = InnoDB,
 PARTITION p28 VALUES LESS THAN (7250000) ENGINE = InnoDB,
 PARTITION p29 VALUES LESS THAN (7500000) ENGINE = InnoDB,
 PARTITION p30 VALUES LESS THAN (7750000) ENGINE = InnoDB,
 PARTITION p31 VALUES LESS THAN (8000000) ENGINE = InnoDB,
 PARTITION p32 VALUES LESS THAN (8250000) ENGINE = InnoDB,
 PARTITION p33 VALUES LESS THAN (8500000) ENGINE = InnoDB,
 PARTITION p34 VALUES LESS THAN (8750000) ENGINE = InnoDB,
 PARTITION p35 VALUES LESS THAN (9000000) ENGINE = InnoDB,
 PARTITION p36 VALUES LESS THAN (9250000) ENGINE = InnoDB,
 PARTITION p37 VALUES LESS THAN (9500000) ENGINE = InnoDB,
 PARTITION p38 VALUES LESS THAN (9750000) ENGINE = InnoDB,
 PARTITION p39 VALUES LESS THAN (10000000) ENGINE = InnoDB,
 PARTITION p40 VALUES LESS THAN (10250000) ENGINE = InnoDB,
 PARTITION p41 VALUES LESS THAN (10500000) ENGINE = InnoDB,
 PARTITION p42 VALUES LESS THAN (10750000) ENGINE = InnoDB,
 PARTITION p43 VALUES LESS THAN (11000000) ENGINE = InnoDB,
 PARTITION p44 VALUES LESS THAN (11250000) ENGINE = InnoDB,
 PARTITION p45 VALUES LESS THAN (11500000) ENGINE = InnoDB,
 PARTITION p46 VALUES LESS THAN (11750000) ENGINE = InnoDB,
 PARTITION p47 VALUES LESS THAN (12000000) ENGINE = InnoDB,
 PARTITION p48 VALUES LESS THAN (12250000) ENGINE = InnoDB,
 PARTITION p49 VALUES LESS THAN (12500000) ENGINE = InnoDB,
 PARTITION p50 VALUES LESS THAN (12750000) ENGINE = InnoDB,
 PARTITION p51 VALUES LESS THAN (13000000) ENGINE = InnoDB,
 PARTITION p52 VALUES LESS THAN (13250000) ENGINE = InnoDB,
 PARTITION p53 VALUES LESS THAN (13500000) ENGINE = InnoDB,
 PARTITION p54 VALUES LESS THAN (13750000) ENGINE = InnoDB,
 PARTITION p55 VALUES LESS THAN (14000000) ENGINE = InnoDB,
 PARTITION p56 VALUES LESS THAN (14250000) ENGINE = InnoDB,
 PARTITION p57 VALUES LESS THAN (14500000) ENGINE = InnoDB,
 PARTITION p58 VALUES LESS THAN (14750000) ENGINE = InnoDB,
 PARTITION p59 VALUES LESS THAN (15000000) ENGINE = InnoDB,
 PARTITION p60 VALUES LESS THAN (15250000) ENGINE = InnoDB,
 PARTITION p61 VALUES LESS THAN (15500000) ENGINE = InnoDB,
 PARTITION p62 VALUES LESS THAN (15750000) ENGINE = InnoDB,
 PARTITION p63 VALUES LESS THAN (16000000) ENGINE = InnoDB,
 PARTITION p64 VALUES LESS THAN (16250000) ENGINE = InnoDB,
 PARTITION p65 VALUES LESS THAN (16500000) ENGINE = InnoDB,
 PARTITION p66 VALUES LESS THAN (16750000) ENGINE = InnoDB,
 PARTITION p67 VALUES LESS THAN (17000000) ENGINE = InnoDB,
 PARTITION p68 VALUES LESS THAN (17250000) ENGINE = InnoDB,
 PARTITION p69 VALUES LESS THAN (17500000) ENGINE = InnoDB,
 PARTITION p70 VALUES LESS THAN (17750000) ENGINE = InnoDB,
 PARTITION p71 VALUES LESS THAN (18000000) ENGINE = InnoDB,
 PARTITION p72 VALUES LESS THAN (18250000) ENGINE = InnoDB,
 PARTITION p73 VALUES LESS THAN (18500000) ENGINE = InnoDB,
 PARTITION p74 VALUES LESS THAN (18750000) ENGINE = InnoDB,
 PARTITION p75 VALUES LESS THAN (19000000) ENGINE = InnoDB,
 PARTITION p76 VALUES LESS THAN (19250000) ENGINE = InnoDB,
 PARTITION p77 VALUES LESS THAN (19500000) ENGINE = InnoDB,
 PARTITION p78 VALUES LESS THAN (19750000) ENGINE = InnoDB,
 PARTITION p79 VALUES LESS THAN (20000000) ENGINE = InnoDB,
 PARTITION p80 VALUES LESS THAN (20250000) ENGINE = InnoDB,
 PARTITION p81 VALUES LESS THAN (20500000) ENGINE = InnoDB,
 PARTITION p82 VALUES LESS THAN (20750000) ENGINE = InnoDB,
 PARTITION p83 VALUES LESS THAN (21000000) ENGINE = InnoDB,
 PARTITION p84 VALUES LESS THAN (21250000) ENGINE = InnoDB,
 PARTITION p85 VALUES LESS THAN (21500000) ENGINE = InnoDB,
 PARTITION p86 VALUES LESS THAN (21750000) ENGINE = InnoDB,
 PARTITION p87 VALUES LESS THAN (22000000) ENGINE = InnoDB,
 PARTITION p88 VALUES LESS THAN (22250000) ENGINE = InnoDB,
 PARTITION p89 VALUES LESS THAN (22500000) ENGINE = InnoDB,
 PARTITION p90 VALUES LESS THAN (22750000) ENGINE = InnoDB,
 PARTITION p91 VALUES LESS THAN (23000000) ENGINE = InnoDB,
 PARTITION p92 VALUES LESS THAN (23250000) ENGINE = InnoDB,
 PARTITION p93 VALUES LESS THAN (23500000) ENGINE = InnoDB,
 PARTITION p94 VALUES LESS THAN (23750000) ENGINE = InnoDB,
 PARTITION p95 VALUES LESS THAN (24000000) ENGINE = InnoDB,
 PARTITION p96 VALUES LESS THAN (24250000) ENGINE = InnoDB,
 PARTITION p97 VALUES LESS THAN (24500000) ENGINE = InnoDB,
 PARTITION p98 VALUES LESS THAN (24750000) ENGINE = InnoDB,
 PARTITION p99 VALUES LESS THAN (25000000) ENGINE = InnoDB,
 PARTITION p100 VALUES LESS THAN (25250000) ENGINE = InnoDB,
 PARTITION p101 VALUES LESS THAN (25500000) ENGINE = InnoDB,
 PARTITION p102 VALUES LESS THAN (25750000) ENGINE = InnoDB,
 PARTITION p103 VALUES LESS THAN (26000000) ENGINE = InnoDB,
 PARTITION p104 VALUES LESS THAN (26250000) ENGINE = InnoDB,
 PARTITION p105 VALUES LESS THAN (26500000) ENGINE = InnoDB,
 PARTITION p106 VALUES LESS THAN (26750000) ENGINE = InnoDB,
 PARTITION p107 VALUES LESS THAN (27000000) ENGINE = InnoDB,
 PARTITION p108 VALUES LESS THAN (27250000) ENGINE = InnoDB,
 PARTITION p109 VALUES LESS THAN (27500000) ENGINE = InnoDB,
 PARTITION p110 VALUES LESS THAN (27750000) ENGINE = InnoDB,
 PARTITION p111 VALUES LESS THAN (28000000) ENGINE = InnoDB,
 PARTITION p112 VALUES LESS THAN (28250000) ENGINE = InnoDB,
 PARTITION p113 VALUES LESS THAN (28500000) ENGINE = InnoDB,
 PARTITION p114 VALUES LESS THAN (28750000) ENGINE = InnoDB,
 PARTITION p115 VALUES LESS THAN (29000000) ENGINE = InnoDB,
 PARTITION p116 VALUES LESS THAN (29250000) ENGINE = InnoDB,
 PARTITION p117 VALUES LESS THAN (29500000) ENGINE = InnoDB,
 PARTITION p118 VALUES LESS THAN (29750000) ENGINE = InnoDB,
 PARTITION p119 VALUES LESS THAN (30000000) ENGINE = InnoDB,
 PARTITION p120 VALUES LESS THAN (30250000) ENGINE = InnoDB,
 PARTITION p121 VALUES LESS THAN (30500000) ENGINE = InnoDB,
 PARTITION p122 VALUES LESS THAN (30750000) ENGINE = InnoDB,
 PARTITION p123 VALUES LESS THAN (31000000) ENGINE = InnoDB,
 PARTITION p124 VALUES LESS THAN (31250000) ENGINE = InnoDB,
 PARTITION p125 VALUES LESS THAN (31500000) ENGINE = InnoDB,
 PARTITION p126 VALUES LESS THAN (31750000) ENGINE = InnoDB,
 PARTITION p127 VALUES LESS THAN (32000000) ENGINE = InnoDB,
 PARTITION p128 VALUES LESS THAN (32250000) ENGINE = InnoDB,
 PARTITION p129 VALUES LESS THAN (32500000) ENGINE = InnoDB,
 PARTITION p130 VALUES LESS THAN (32750000) ENGINE = InnoDB,
 PARTITION p131 VALUES LESS THAN (33000000) ENGINE = InnoDB,
 PARTITION p132 VALUES LESS THAN (33250000) ENGINE = InnoDB,
 PARTITION p133 VALUES LESS THAN (33500000) ENGINE = InnoDB,
 PARTITION p134 VALUES LESS THAN (33750000) ENGINE = InnoDB,
 PARTITION p135 VALUES LESS THAN (34000000) ENGINE = InnoDB,
 PARTITION p136 VALUES LESS THAN (34250000) ENGINE = InnoDB,
 PARTITION p137 VALUES LESS THAN (34500000) ENGINE = InnoDB,
 PARTITION p138 VALUES LESS THAN (34750000) ENGINE = InnoDB,
 PARTITION p139 VALUES LESS THAN (35000000) ENGINE = InnoDB,
 PARTITION p140 VALUES LESS THAN (35250000) ENGINE = InnoDB,
 PARTITION p141 VALUES LESS THAN (35500000) ENGINE = InnoDB,
 PARTITION p142 VALUES LESS THAN (35750000) ENGINE = InnoDB,
 PARTITION p143 VALUES LESS THAN (36000000) ENGINE = InnoDB,
 PARTITION p144 VALUES LESS THAN (36250000) ENGINE = InnoDB,
 PARTITION p145 VALUES LESS THAN (36500000) ENGINE = InnoDB,
 PARTITION p146 VALUES LESS THAN (36750000) ENGINE = InnoDB,
 PARTITION p147 VALUES LESS THAN (37000000) ENGINE = InnoDB,
 PARTITION p148 VALUES LESS THAN (37250000) ENGINE = InnoDB,
 PARTITION p149 VALUES LESS THAN (37500000) ENGINE = InnoDB,
 PARTITION p150 VALUES LESS THAN (37750000) ENGINE = InnoDB,
 PARTITION p151 VALUES LESS THAN (38000000) ENGINE = InnoDB,
 PARTITION p152 VALUES LESS THAN (38250000) ENGINE = InnoDB,
 PARTITION p153 VALUES LESS THAN (38500000) ENGINE = InnoDB,
 PARTITION p154 VALUES LESS THAN (38750000) ENGINE = InnoDB,
 PARTITION p155 VALUES LESS THAN (39000000) ENGINE = InnoDB,
 PARTITION p156 VALUES LESS THAN (39250000) ENGINE = InnoDB,
 PARTITION p157 VALUES LESS THAN (39500000) ENGINE = InnoDB,
 PARTITION p158 VALUES LESS THAN (39750000) ENGINE = InnoDB,
 PARTITION p159 VALUES LESS THAN (40000000) ENGINE = InnoDB,
 PARTITION p160 VALUES LESS THAN (40250000) ENGINE = InnoDB,
 PARTITION p161 VALUES LESS THAN (40500000) ENGINE = InnoDB,
 PARTITION p162 VALUES LESS THAN (40750000) ENGINE = InnoDB,
 PARTITION p163 VALUES LESS THAN (41000000) ENGINE = InnoDB,
 PARTITION p164 VALUES LESS THAN (41250000) ENGINE = InnoDB,
 PARTITION p165 VALUES LESS THAN (41500000) ENGINE = InnoDB,
 PARTITION p166 VALUES LESS THAN (41750000) ENGINE = InnoDB,
 PARTITION p167 VALUES LESS THAN (42000000) ENGINE = InnoDB,
 PARTITION p168 VALUES LESS THAN (42250000) ENGINE = InnoDB,
 PARTITION p169 VALUES LESS THAN (42500000) ENGINE = InnoDB,
 PARTITION p170 VALUES LESS THAN (42750000) ENGINE = InnoDB,
 PARTITION p171 VALUES LESS THAN (43000000) ENGINE = InnoDB,
 PARTITION p172 VALUES LESS THAN (43250000) ENGINE = InnoDB,
 PARTITION p173 VALUES LESS THAN (43500000) ENGINE = InnoDB,
 PARTITION p174 VALUES LESS THAN (43750000) ENGINE = InnoDB,
 PARTITION p175 VALUES LESS THAN (44000000) ENGINE = InnoDB,
 PARTITION p176 VALUES LESS THAN (44250000) ENGINE = InnoDB,
 PARTITION p177 VALUES LESS THAN (44500000) ENGINE = InnoDB,
 PARTITION p178 VALUES LESS THAN (44750000) ENGINE = InnoDB,
 PARTITION p179 VALUES LESS THAN (45000000) ENGINE = InnoDB,
 PARTITION p180 VALUES LESS THAN (45250000) ENGINE = InnoDB,
 PARTITION p181 VALUES LESS THAN (45500000) ENGINE = InnoDB,
 PARTITION p182 VALUES LESS THAN (45750000) ENGINE = InnoDB,
 PARTITION p183 VALUES LESS THAN (46000000) ENGINE = InnoDB,
 PARTITION p184 VALUES LESS THAN (46250000) ENGINE = InnoDB,
 PARTITION p185 VALUES LESS THAN (46500000) ENGINE = InnoDB,
 PARTITION p186 VALUES LESS THAN (46750000) ENGINE = InnoDB,
 PARTITION p187 VALUES LESS THAN (47000000) ENGINE = InnoDB,
 PARTITION p188 VALUES LESS THAN (47250000) ENGINE = InnoDB,
 PARTITION p189 VALUES LESS THAN (47500000) ENGINE = InnoDB,
 PARTITION p190 VALUES LESS THAN (47750000) ENGINE = InnoDB,
 PARTITION p191 VALUES LESS THAN (48000000) ENGINE = InnoDB,
 PARTITION p192 VALUES LESS THAN (48250000) ENGINE = InnoDB,
 PARTITION p193 VALUES LESS THAN (48500000) ENGINE = InnoDB,
 PARTITION p194 VALUES LESS THAN (48750000) ENGINE = InnoDB,
 PARTITION p195 VALUES LESS THAN (49000000) ENGINE = InnoDB,
 PARTITION p196 VALUES LESS THAN (49250000) ENGINE = InnoDB,
 PARTITION p197 VALUES LESS THAN (49500000) ENGINE = InnoDB,
 PARTITION p198 VALUES LESS THAN (49750000) ENGINE = InnoDB,
 PARTITION p199 VALUES LESS THAN (50000000) ENGINE = InnoDB,
 PARTITION p200 VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events_exp_new`
--

LOCK TABLES `events_exp_new` WRITE;
/*!40000 ALTER TABLE `events_exp_new` DISABLE KEYS */;
INSERT INTO `events_exp_new` VALUES (730968,'2013-08-26 22:49:43',26,18866,53532,72398),(730968,'2013-09-07 09:07:04',26,48194,88239,136433),(730968,'2013-09-09 22:19:09',14,598649,3981126,4579775),(730968,'2013-09-22 06:45:18',9,137485,201111,338596),(730968,'2013-09-22 18:24:18',26,12954,136942,149896),(730968,'2013-09-26 04:19:15',14,3150165,4583835,7734000),(730968,'2013-09-26 04:19:15',26,29581,149896,179477),(730968,'2013-09-27 06:26:24',26,1140574,179477,1320051),(730968,'2013-09-29 05:43:16',26,260158,1689666,1949824),(730968,'2013-09-30 10:09:57',26,765538,1949824,2715362),(730968,'2013-10-01 09:57:33',26,654785,2715362,3370147),(730968,'2013-10-02 09:46:22',26,572742,3370147,3942889),(730968,'2013-10-04 09:33:13',26,293228,6344103,6637331),(730968,'2013-10-05 15:30:14',26,19722,6637331,6657053),(730968,'2013-10-06 21:32:09',26,1076947,6657053,7734000),(730968,'2013-10-12 10:39:17',16,57999,1297,59296),(730968,'2013-10-13 04:53:09',16,40311,59296,99607),(730968,'2013-10-16 06:05:08',15,84073,473848,557921),(730968,'2013-10-16 06:05:08',16,19797,179004,198801),(730968,'2013-10-19 14:03:02',16,2163021,950780,3113801),(730968,'2013-10-21 03:17:35',16,4620199,3113801,7734000),(730968,'2013-10-21 21:56:33',17,827372,7530,834902),(730968,'2013-10-22 22:55:14',17,949785,834902,1784687),(730968,'2013-12-26 05:48:17',15,584394,557921,1142315),(730968,'2013-12-28 16:49:24',17,59204,1786525,1845729),(730968,'2013-12-29 03:23:18',17,486470,1845729,2332199),(730968,'2014-01-05 06:46:15',17,84845,3930974,4015819),(730968,'2014-01-10 02:00:03',17,142476,4300874,4443350),(730968,'2015-12-20 22:03:33',29,31104,6377,37481),(730968,'2016-01-05 22:49:15',29,20197,37481,57678),(730968,'2016-05-31 10:32:00',1,1470,7734000,7735470),(730968,'2016-05-31 10:32:00',5,229776,7734100,7963876),(730968,'2016-05-31 10:32:00',6,2772,7734000,7736772),(730968,'2016-05-31 10:32:00',29,58685,60290,118975),(730968,'2016-06-01 13:27:24',5,492426,7963876,8456302),(730968,'2016-06-02 15:34:58',5,248203,8456302,8704505),(730968,'2016-06-03 18:23:48',5,2134479,8704505,10838984),(730968,'2016-06-04 21:08:05',5,2584295,10838984,13423279),(730968,'2016-06-06 00:28:31',5,3183587,13423279,16606866),(730968,'2016-06-06 00:28:31',29,51458,118975,170433),(730968,'2016-06-07 17:39:56',5,1150434,16606866,17757300),(730968,'2016-06-11 00:44:15',5,5667256,17757300,23424556),(730968,'2016-06-14 11:31:47',5,6046244,23424556,29470800),(730968,'2016-06-14 11:31:47',6,70918,7736772,7807690),(730968,'2016-06-14 11:31:47',31,19583,1317680,1337263),(730968,'2016-06-19 21:29:50',3,67638,7734000,7801638),(730968,'2016-06-19 21:29:50',29,45167,170433,215600),(730968,'2016-06-22 18:07:19',29,331042,215600,546642),(730968,'2016-06-24 03:54:56',32,471995,1317680,1789675),(730968,'2016-06-26 21:45:39',6,91510,7807690,7899200),(730968,'2016-06-26 21:45:39',29,345433,546642,892075),(730968,'2016-06-26 21:45:39',32,455868,1789675,2245543),(730968,'2016-06-28 05:34:51',3,460200,7801638,8261838),(730968,'2016-06-28 05:34:51',6,8488,7899200,7907688),(730968,'2016-06-28 05:34:51',32,415061,2245543,2660604),(730968,'2016-06-30 21:32:27',16,69909,7734347,7804256),(730968,'2016-06-30 21:32:27',32,313831,2660604,2974435),(730968,'2016-07-02 05:37:20',3,530443,8261838,8792281),(730968,'2016-07-02 05:37:20',16,110696,7804256,7914952),(730968,'2016-07-02 05:37:20',32,295389,2974435,3269824),(730968,'2016-07-03 14:12:33',3,121464,8792281,8913745),(730968,'2016-07-03 14:12:33',16,173114,7914952,8088066),(730968,'2016-07-03 14:12:33',32,346787,3269824,3616611),(730968,'2016-07-04 22:00:13',16,231733,8088066,8319799),(730968,'2016-07-04 22:00:13',32,559661,3616611,4176272),(730968,'2016-07-06 04:07:55',3,352079,8913745,9265824),(730968,'2016-07-06 04:07:55',16,60196,8319799,8379995),(730968,'2016-07-06 04:07:55',32,70897,4176272,4247169),(730968,'2016-07-08 13:50:45',16,3434,8379995,8383429),(730968,'2016-07-09 18:25:41',3,398189,9265824,9664013),(730968,'2016-07-09 18:25:41',16,119584,8383429,8503013),(730968,'2016-07-10 22:49:25',3,413697,9664013,10077710),(730968,'2016-08-13 20:05:15',29,12174,892075,904249),(730968,'2016-08-14 00:53:17',3,383400,10077710,10461110);
/*!40000 ALTER TABLE `events_exp_new` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events_lvs_new`
--

DROP TABLE IF EXISTS `events_lvs_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events_lvs_new` (
  `lodestone_id` int(32) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `jobclass` smallint(3) NOT NULL,
  `gained` int(16) NOT NULL,
  `old` int(16) NOT NULL,
  `new` int(16) NOT NULL,
  UNIQUE KEY `unique` (`lodestone_id`,`time`,`jobclass`),
  KEY `time` (`time`),
  KEY `jobclass` (`jobclass`),
  KEY `lodestone_id` (`lodestone_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
/*!50100 PARTITION BY RANGE (lodestone_id)
(PARTITION p0 VALUES LESS THAN (250000) ENGINE = InnoDB,
 PARTITION p1 VALUES LESS THAN (500000) ENGINE = InnoDB,
 PARTITION p2 VALUES LESS THAN (750000) ENGINE = InnoDB,
 PARTITION p3 VALUES LESS THAN (1000000) ENGINE = InnoDB,
 PARTITION p4 VALUES LESS THAN (1250000) ENGINE = InnoDB,
 PARTITION p5 VALUES LESS THAN (1500000) ENGINE = InnoDB,
 PARTITION p6 VALUES LESS THAN (1750000) ENGINE = InnoDB,
 PARTITION p7 VALUES LESS THAN (2000000) ENGINE = InnoDB,
 PARTITION p8 VALUES LESS THAN (2250000) ENGINE = InnoDB,
 PARTITION p9 VALUES LESS THAN (2500000) ENGINE = InnoDB,
 PARTITION p10 VALUES LESS THAN (2750000) ENGINE = InnoDB,
 PARTITION p11 VALUES LESS THAN (3000000) ENGINE = InnoDB,
 PARTITION p12 VALUES LESS THAN (3250000) ENGINE = InnoDB,
 PARTITION p13 VALUES LESS THAN (3500000) ENGINE = InnoDB,
 PARTITION p14 VALUES LESS THAN (3750000) ENGINE = InnoDB,
 PARTITION p15 VALUES LESS THAN (4000000) ENGINE = InnoDB,
 PARTITION p16 VALUES LESS THAN (4250000) ENGINE = InnoDB,
 PARTITION p17 VALUES LESS THAN (4500000) ENGINE = InnoDB,
 PARTITION p18 VALUES LESS THAN (4750000) ENGINE = InnoDB,
 PARTITION p19 VALUES LESS THAN (5000000) ENGINE = InnoDB,
 PARTITION p20 VALUES LESS THAN (5250000) ENGINE = InnoDB,
 PARTITION p21 VALUES LESS THAN (5500000) ENGINE = InnoDB,
 PARTITION p22 VALUES LESS THAN (5750000) ENGINE = InnoDB,
 PARTITION p23 VALUES LESS THAN (6000000) ENGINE = InnoDB,
 PARTITION p24 VALUES LESS THAN (6250000) ENGINE = InnoDB,
 PARTITION p25 VALUES LESS THAN (6500000) ENGINE = InnoDB,
 PARTITION p26 VALUES LESS THAN (6750000) ENGINE = InnoDB,
 PARTITION p27 VALUES LESS THAN (7000000) ENGINE = InnoDB,
 PARTITION p28 VALUES LESS THAN (7250000) ENGINE = InnoDB,
 PARTITION p29 VALUES LESS THAN (7500000) ENGINE = InnoDB,
 PARTITION p30 VALUES LESS THAN (7750000) ENGINE = InnoDB,
 PARTITION p31 VALUES LESS THAN (8000000) ENGINE = InnoDB,
 PARTITION p32 VALUES LESS THAN (8250000) ENGINE = InnoDB,
 PARTITION p33 VALUES LESS THAN (8500000) ENGINE = InnoDB,
 PARTITION p34 VALUES LESS THAN (8750000) ENGINE = InnoDB,
 PARTITION p35 VALUES LESS THAN (9000000) ENGINE = InnoDB,
 PARTITION p36 VALUES LESS THAN (9250000) ENGINE = InnoDB,
 PARTITION p37 VALUES LESS THAN (9500000) ENGINE = InnoDB,
 PARTITION p38 VALUES LESS THAN (9750000) ENGINE = InnoDB,
 PARTITION p39 VALUES LESS THAN (10000000) ENGINE = InnoDB,
 PARTITION p40 VALUES LESS THAN (10250000) ENGINE = InnoDB,
 PARTITION p41 VALUES LESS THAN (10500000) ENGINE = InnoDB,
 PARTITION p42 VALUES LESS THAN (10750000) ENGINE = InnoDB,
 PARTITION p43 VALUES LESS THAN (11000000) ENGINE = InnoDB,
 PARTITION p44 VALUES LESS THAN (11250000) ENGINE = InnoDB,
 PARTITION p45 VALUES LESS THAN (11500000) ENGINE = InnoDB,
 PARTITION p46 VALUES LESS THAN (11750000) ENGINE = InnoDB,
 PARTITION p47 VALUES LESS THAN (12000000) ENGINE = InnoDB,
 PARTITION p48 VALUES LESS THAN (12250000) ENGINE = InnoDB,
 PARTITION p49 VALUES LESS THAN (12500000) ENGINE = InnoDB,
 PARTITION p50 VALUES LESS THAN (12750000) ENGINE = InnoDB,
 PARTITION p51 VALUES LESS THAN (13000000) ENGINE = InnoDB,
 PARTITION p52 VALUES LESS THAN (13250000) ENGINE = InnoDB,
 PARTITION p53 VALUES LESS THAN (13500000) ENGINE = InnoDB,
 PARTITION p54 VALUES LESS THAN (13750000) ENGINE = InnoDB,
 PARTITION p55 VALUES LESS THAN (14000000) ENGINE = InnoDB,
 PARTITION p56 VALUES LESS THAN (14250000) ENGINE = InnoDB,
 PARTITION p57 VALUES LESS THAN (14500000) ENGINE = InnoDB,
 PARTITION p58 VALUES LESS THAN (14750000) ENGINE = InnoDB,
 PARTITION p59 VALUES LESS THAN (15000000) ENGINE = InnoDB,
 PARTITION p60 VALUES LESS THAN (15250000) ENGINE = InnoDB,
 PARTITION p61 VALUES LESS THAN (15500000) ENGINE = InnoDB,
 PARTITION p62 VALUES LESS THAN (15750000) ENGINE = InnoDB,
 PARTITION p63 VALUES LESS THAN (16000000) ENGINE = InnoDB,
 PARTITION p64 VALUES LESS THAN (16250000) ENGINE = InnoDB,
 PARTITION p65 VALUES LESS THAN (16500000) ENGINE = InnoDB,
 PARTITION p66 VALUES LESS THAN (16750000) ENGINE = InnoDB,
 PARTITION p67 VALUES LESS THAN (17000000) ENGINE = InnoDB,
 PARTITION p68 VALUES LESS THAN (17250000) ENGINE = InnoDB,
 PARTITION p69 VALUES LESS THAN (17500000) ENGINE = InnoDB,
 PARTITION p70 VALUES LESS THAN (17750000) ENGINE = InnoDB,
 PARTITION p71 VALUES LESS THAN (18000000) ENGINE = InnoDB,
 PARTITION p72 VALUES LESS THAN (18250000) ENGINE = InnoDB,
 PARTITION p73 VALUES LESS THAN (18500000) ENGINE = InnoDB,
 PARTITION p74 VALUES LESS THAN (18750000) ENGINE = InnoDB,
 PARTITION p75 VALUES LESS THAN (19000000) ENGINE = InnoDB,
 PARTITION p76 VALUES LESS THAN (19250000) ENGINE = InnoDB,
 PARTITION p77 VALUES LESS THAN (19500000) ENGINE = InnoDB,
 PARTITION p78 VALUES LESS THAN (19750000) ENGINE = InnoDB,
 PARTITION p79 VALUES LESS THAN (20000000) ENGINE = InnoDB,
 PARTITION p80 VALUES LESS THAN (20250000) ENGINE = InnoDB,
 PARTITION p81 VALUES LESS THAN (20500000) ENGINE = InnoDB,
 PARTITION p82 VALUES LESS THAN (20750000) ENGINE = InnoDB,
 PARTITION p83 VALUES LESS THAN (21000000) ENGINE = InnoDB,
 PARTITION p84 VALUES LESS THAN (21250000) ENGINE = InnoDB,
 PARTITION p85 VALUES LESS THAN (21500000) ENGINE = InnoDB,
 PARTITION p86 VALUES LESS THAN (21750000) ENGINE = InnoDB,
 PARTITION p87 VALUES LESS THAN (22000000) ENGINE = InnoDB,
 PARTITION p88 VALUES LESS THAN (22250000) ENGINE = InnoDB,
 PARTITION p89 VALUES LESS THAN (22500000) ENGINE = InnoDB,
 PARTITION p90 VALUES LESS THAN (22750000) ENGINE = InnoDB,
 PARTITION p91 VALUES LESS THAN (23000000) ENGINE = InnoDB,
 PARTITION p92 VALUES LESS THAN (23250000) ENGINE = InnoDB,
 PARTITION p93 VALUES LESS THAN (23500000) ENGINE = InnoDB,
 PARTITION p94 VALUES LESS THAN (23750000) ENGINE = InnoDB,
 PARTITION p95 VALUES LESS THAN (24000000) ENGINE = InnoDB,
 PARTITION p96 VALUES LESS THAN (24250000) ENGINE = InnoDB,
 PARTITION p97 VALUES LESS THAN (24500000) ENGINE = InnoDB,
 PARTITION p98 VALUES LESS THAN (24750000) ENGINE = InnoDB,
 PARTITION p99 VALUES LESS THAN (25000000) ENGINE = InnoDB,
 PARTITION p100 VALUES LESS THAN (25250000) ENGINE = InnoDB,
 PARTITION p101 VALUES LESS THAN (25500000) ENGINE = InnoDB,
 PARTITION p102 VALUES LESS THAN (25750000) ENGINE = InnoDB,
 PARTITION p103 VALUES LESS THAN (26000000) ENGINE = InnoDB,
 PARTITION p104 VALUES LESS THAN (26250000) ENGINE = InnoDB,
 PARTITION p105 VALUES LESS THAN (26500000) ENGINE = InnoDB,
 PARTITION p106 VALUES LESS THAN (26750000) ENGINE = InnoDB,
 PARTITION p107 VALUES LESS THAN (27000000) ENGINE = InnoDB,
 PARTITION p108 VALUES LESS THAN (27250000) ENGINE = InnoDB,
 PARTITION p109 VALUES LESS THAN (27500000) ENGINE = InnoDB,
 PARTITION p110 VALUES LESS THAN (27750000) ENGINE = InnoDB,
 PARTITION p111 VALUES LESS THAN (28000000) ENGINE = InnoDB,
 PARTITION p112 VALUES LESS THAN (28250000) ENGINE = InnoDB,
 PARTITION p113 VALUES LESS THAN (28500000) ENGINE = InnoDB,
 PARTITION p114 VALUES LESS THAN (28750000) ENGINE = InnoDB,
 PARTITION p115 VALUES LESS THAN (29000000) ENGINE = InnoDB,
 PARTITION p116 VALUES LESS THAN (29250000) ENGINE = InnoDB,
 PARTITION p117 VALUES LESS THAN (29500000) ENGINE = InnoDB,
 PARTITION p118 VALUES LESS THAN (29750000) ENGINE = InnoDB,
 PARTITION p119 VALUES LESS THAN (30000000) ENGINE = InnoDB,
 PARTITION p120 VALUES LESS THAN (30250000) ENGINE = InnoDB,
 PARTITION p121 VALUES LESS THAN (30500000) ENGINE = InnoDB,
 PARTITION p122 VALUES LESS THAN (30750000) ENGINE = InnoDB,
 PARTITION p123 VALUES LESS THAN (31000000) ENGINE = InnoDB,
 PARTITION p124 VALUES LESS THAN (31250000) ENGINE = InnoDB,
 PARTITION p125 VALUES LESS THAN (31500000) ENGINE = InnoDB,
 PARTITION p126 VALUES LESS THAN (31750000) ENGINE = InnoDB,
 PARTITION p127 VALUES LESS THAN (32000000) ENGINE = InnoDB,
 PARTITION p128 VALUES LESS THAN (32250000) ENGINE = InnoDB,
 PARTITION p129 VALUES LESS THAN (32500000) ENGINE = InnoDB,
 PARTITION p130 VALUES LESS THAN (32750000) ENGINE = InnoDB,
 PARTITION p131 VALUES LESS THAN (33000000) ENGINE = InnoDB,
 PARTITION p132 VALUES LESS THAN (33250000) ENGINE = InnoDB,
 PARTITION p133 VALUES LESS THAN (33500000) ENGINE = InnoDB,
 PARTITION p134 VALUES LESS THAN (33750000) ENGINE = InnoDB,
 PARTITION p135 VALUES LESS THAN (34000000) ENGINE = InnoDB,
 PARTITION p136 VALUES LESS THAN (34250000) ENGINE = InnoDB,
 PARTITION p137 VALUES LESS THAN (34500000) ENGINE = InnoDB,
 PARTITION p138 VALUES LESS THAN (34750000) ENGINE = InnoDB,
 PARTITION p139 VALUES LESS THAN (35000000) ENGINE = InnoDB,
 PARTITION p140 VALUES LESS THAN (35250000) ENGINE = InnoDB,
 PARTITION p141 VALUES LESS THAN (35500000) ENGINE = InnoDB,
 PARTITION p142 VALUES LESS THAN (35750000) ENGINE = InnoDB,
 PARTITION p143 VALUES LESS THAN (36000000) ENGINE = InnoDB,
 PARTITION p144 VALUES LESS THAN (36250000) ENGINE = InnoDB,
 PARTITION p145 VALUES LESS THAN (36500000) ENGINE = InnoDB,
 PARTITION p146 VALUES LESS THAN (36750000) ENGINE = InnoDB,
 PARTITION p147 VALUES LESS THAN (37000000) ENGINE = InnoDB,
 PARTITION p148 VALUES LESS THAN (37250000) ENGINE = InnoDB,
 PARTITION p149 VALUES LESS THAN (37500000) ENGINE = InnoDB,
 PARTITION p150 VALUES LESS THAN (37750000) ENGINE = InnoDB,
 PARTITION p151 VALUES LESS THAN (38000000) ENGINE = InnoDB,
 PARTITION p152 VALUES LESS THAN (38250000) ENGINE = InnoDB,
 PARTITION p153 VALUES LESS THAN (38500000) ENGINE = InnoDB,
 PARTITION p154 VALUES LESS THAN (38750000) ENGINE = InnoDB,
 PARTITION p155 VALUES LESS THAN (39000000) ENGINE = InnoDB,
 PARTITION p156 VALUES LESS THAN (39250000) ENGINE = InnoDB,
 PARTITION p157 VALUES LESS THAN (39500000) ENGINE = InnoDB,
 PARTITION p158 VALUES LESS THAN (39750000) ENGINE = InnoDB,
 PARTITION p159 VALUES LESS THAN (40000000) ENGINE = InnoDB,
 PARTITION p160 VALUES LESS THAN (40250000) ENGINE = InnoDB,
 PARTITION p161 VALUES LESS THAN (40500000) ENGINE = InnoDB,
 PARTITION p162 VALUES LESS THAN (40750000) ENGINE = InnoDB,
 PARTITION p163 VALUES LESS THAN (41000000) ENGINE = InnoDB,
 PARTITION p164 VALUES LESS THAN (41250000) ENGINE = InnoDB,
 PARTITION p165 VALUES LESS THAN (41500000) ENGINE = InnoDB,
 PARTITION p166 VALUES LESS THAN (41750000) ENGINE = InnoDB,
 PARTITION p167 VALUES LESS THAN (42000000) ENGINE = InnoDB,
 PARTITION p168 VALUES LESS THAN (42250000) ENGINE = InnoDB,
 PARTITION p169 VALUES LESS THAN (42500000) ENGINE = InnoDB,
 PARTITION p170 VALUES LESS THAN (42750000) ENGINE = InnoDB,
 PARTITION p171 VALUES LESS THAN (43000000) ENGINE = InnoDB,
 PARTITION p172 VALUES LESS THAN (43250000) ENGINE = InnoDB,
 PARTITION p173 VALUES LESS THAN (43500000) ENGINE = InnoDB,
 PARTITION p174 VALUES LESS THAN (43750000) ENGINE = InnoDB,
 PARTITION p175 VALUES LESS THAN (44000000) ENGINE = InnoDB,
 PARTITION p176 VALUES LESS THAN (44250000) ENGINE = InnoDB,
 PARTITION p177 VALUES LESS THAN (44500000) ENGINE = InnoDB,
 PARTITION p178 VALUES LESS THAN (44750000) ENGINE = InnoDB,
 PARTITION p179 VALUES LESS THAN (45000000) ENGINE = InnoDB,
 PARTITION p180 VALUES LESS THAN (45250000) ENGINE = InnoDB,
 PARTITION p181 VALUES LESS THAN (45500000) ENGINE = InnoDB,
 PARTITION p182 VALUES LESS THAN (45750000) ENGINE = InnoDB,
 PARTITION p183 VALUES LESS THAN (46000000) ENGINE = InnoDB,
 PARTITION p184 VALUES LESS THAN (46250000) ENGINE = InnoDB,
 PARTITION p185 VALUES LESS THAN (46500000) ENGINE = InnoDB,
 PARTITION p186 VALUES LESS THAN (46750000) ENGINE = InnoDB,
 PARTITION p187 VALUES LESS THAN (47000000) ENGINE = InnoDB,
 PARTITION p188 VALUES LESS THAN (47250000) ENGINE = InnoDB,
 PARTITION p189 VALUES LESS THAN (47500000) ENGINE = InnoDB,
 PARTITION p190 VALUES LESS THAN (47750000) ENGINE = InnoDB,
 PARTITION p191 VALUES LESS THAN (48000000) ENGINE = InnoDB,
 PARTITION p192 VALUES LESS THAN (48250000) ENGINE = InnoDB,
 PARTITION p193 VALUES LESS THAN (48500000) ENGINE = InnoDB,
 PARTITION p194 VALUES LESS THAN (48750000) ENGINE = InnoDB,
 PARTITION p195 VALUES LESS THAN (49000000) ENGINE = InnoDB,
 PARTITION p196 VALUES LESS THAN (49250000) ENGINE = InnoDB,
 PARTITION p197 VALUES LESS THAN (49500000) ENGINE = InnoDB,
 PARTITION p198 VALUES LESS THAN (49750000) ENGINE = InnoDB,
 PARTITION p199 VALUES LESS THAN (50000000) ENGINE = InnoDB,
 PARTITION p200 VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events_lvs_new`
--

LOCK TABLES `events_lvs_new` WRITE;
/*!40000 ALTER TABLE `events_lvs_new` DISABLE KEYS */;
INSERT INTO `events_lvs_new` VALUES (730968,'2013-08-26 22:49:43',26,1,11,12),(730968,'2013-09-01 18:15:13',26,1,12,13),(730968,'2013-09-07 09:07:04',26,2,13,15),(730968,'2013-09-09 22:19:09',14,1,41,42),(730968,'2013-09-22 06:45:18',9,3,17,20),(730968,'2013-09-26 04:19:15',14,8,42,50),(730968,'2013-09-26 04:19:15',26,1,15,16),(730968,'2013-09-27 06:26:24',26,14,16,30),(730968,'2013-09-28 17:53:28',26,2,30,32),(730968,'2013-09-29 05:43:15',26,1,32,33),(730968,'2013-09-30 10:09:56',26,3,33,36),(730968,'2013-10-01 09:57:33',26,3,36,39),(730968,'2013-10-02 09:46:22',26,2,39,41),(730968,'2013-10-06 21:32:09',26,3,47,50),(730968,'2013-10-11 04:13:20',16,2,1,3),(730968,'2013-10-12 10:39:17',16,8,3,11),(730968,'2013-10-13 04:53:08',16,2,11,13),(730968,'2013-10-14 05:13:23',16,3,13,16),(730968,'2013-10-16 06:05:08',15,1,22,23),(730968,'2013-10-16 06:05:08',16,1,16,17),(730968,'2013-10-19 14:03:02',16,11,27,38),(730968,'2013-10-21 03:17:34',16,12,38,50),(730968,'2013-10-21 21:56:33',17,20,6,26),(730968,'2013-10-22 22:55:14',17,6,26,32),(730968,'2013-12-26 05:48:17',15,5,23,28),(730968,'2013-12-28 16:49:24',17,1,32,33),(730968,'2013-12-29 03:23:18',17,2,33,35),(730968,'2014-01-05 06:46:15',17,1,40,41),(730968,'2014-01-07 12:21:04',17,1,41,42),(730968,'2015-12-20 22:03:33',29,4,6,10),(730968,'2016-01-05 22:49:15',29,1,10,11),(730968,'2016-05-31 10:32:00',29,3,11,14),(730968,'2016-06-02 15:34:58',5,1,50,51),(730968,'2016-06-03 18:23:48',5,1,51,52),(730968,'2016-06-04 21:08:05',5,2,52,54),(730968,'2016-06-06 00:28:31',5,2,54,56),(730968,'2016-06-06 00:28:31',29,2,14,16),(730968,'2016-06-11 00:44:15',5,2,56,58),(730968,'2016-06-14 11:31:47',5,2,58,60),(730968,'2016-06-19 21:29:50',29,1,16,17),(730968,'2016-06-22 18:07:19',29,6,17,23),(730968,'2016-06-24 03:54:56',32,2,30,32),(730968,'2016-06-26 21:45:39',29,3,23,26),(730968,'2016-06-26 21:45:39',32,2,32,34),(730968,'2016-06-28 05:34:51',32,2,34,36),(730968,'2016-06-30 21:32:27',32,1,36,37),(730968,'2016-07-02 05:37:20',3,1,50,51),(730968,'2016-07-02 05:37:20',32,1,37,38),(730968,'2016-07-03 14:12:33',32,2,38,40),(730968,'2016-07-04 22:00:13',32,1,40,41),(730968,'2016-07-09 18:25:41',3,1,51,52),(730968,'2016-08-13 20:05:15',29,1,26,27);
/*!40000 ALTER TABLE `events_lvs_new` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events_tracking`
--

DROP TABLE IF EXISTS `events_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events_tracking` (
  `lodestone_id` int(32) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` varchar(32) NOT NULL,
  `old_value` varchar(128) NOT NULL,
  `new_value` varchar(128) NOT NULL,
  UNIQUE KEY `unique` (`lodestone_id`,`type`,`new_value`,`old_value`),
  KEY `time` (`time`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events_tracking`
--

LOCK TABLES `events_tracking` WRITE;
/*!40000 ALTER TABLE `events_tracking` DISABLE KEYS */;
INSERT INTO `events_tracking` VALUES (730968,'2016-08-15 19:11:21','city','Test city','Limsa Lominsa'),(730968,'2016-08-15 19:11:21','clan','Awesome Clan','Midlander'),(730968,'2016-08-15 19:11:21','free_company','123','9233645873504722757'),(730968,'2016-08-15 19:11:21','gender','male','female'),(730968,'2016-08-15 19:11:21','grand_company_name','Test Grand Company','Immortal Flames'),(730968,'2016-08-15 19:11:21','grand_company_rank','Test Rank','Second Flame Lieutenant'),(730968,'2016-08-15 19:11:21','name','Test Virtue','Premium Virtue'),(730968,'2016-08-15 19:11:21','nameday','yesterday','2nd Sun of the 6th Umbral Moon'),(730968,'2016-08-15 19:11:21','race','Galka','Hyur'),(730968,'2016-08-15 19:11:21','server','Excaliber','Adamantoise'),(730968,'2016-08-15 19:11:21','title','Test Title','Lady Protector');
/*!40000 ALTER TABLE `events_tracking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pending_characters`
--

DROP TABLE IF EXISTS `pending_characters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pending_characters` (
  `lodestone_id` int(32) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `processed` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lodestone_id`),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pending_characters`
--

LOCK TABLES `pending_characters` WRITE;
/*!40000 ALTER TABLE `pending_characters` DISABLE KEYS */;
INSERT INTO `pending_characters` VALUES (730968,'2016-07-20 18:57:28','2016-07-20 18:57:28',0);
/*!40000 ALTER TABLE `pending_characters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pending_freecompanies`
--

DROP TABLE IF EXISTS `pending_freecompanies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pending_freecompanies` (
  `fc_id` varchar(64) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `processed` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`fc_id`),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pending_freecompanies`
--

LOCK TABLES `pending_freecompanies` WRITE;
/*!40000 ALTER TABLE `pending_freecompanies` DISABLE KEYS */;
INSERT INTO `pending_freecompanies` VALUES ('123','2016-08-15 19:03:54',NULL),('9233645873504722757','2016-08-03 22:15:06',NULL);
/*!40000 ALTER TABLE `pending_freecompanies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pending_linkshells`
--

DROP TABLE IF EXISTS `pending_linkshells`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pending_linkshells` (
  `ls_id` varchar(64) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `processed` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ls_id`),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pending_linkshells`
--

LOCK TABLES `pending_linkshells` WRITE;
/*!40000 ALTER TABLE `pending_linkshells` DISABLE KEYS */;
/*!40000 ALTER TABLE `pending_linkshells` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ranking_stats`
--

DROP TABLE IF EXISTS `ranking_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ranking_stats` (
  `lodestone_id` int(32) NOT NULL,
  `name` varchar(64) NOT NULL,
  `type` varchar(32) NOT NULL,
  `value` int(8) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`name`),
  KEY `keys` (`type`,`value`,`lodestone_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ranking_stats`
--

LOCK TABLES `ranking_stats` WRITE;
/*!40000 ALTER TABLE `ranking_stats` DISABLE KEYS */;
INSERT INTO `ranking_stats` VALUES (730968,'accuracy','properties',735,'2016-08-03 22:15:06'),(730968,'attack_magic_potency','properties',186,'2016-08-03 22:15:06'),(730968,'attack_power','properties',1148,'2016-08-03 22:15:06'),(730968,'bind_resistance','resistances',0,'2016-08-03 22:15:06'),(730968,'blind_resistance','resistances',0,'2016-08-03 22:15:06'),(730968,'blunt_resistance','resistances',100,'2016-08-03 22:15:06'),(730968,'critical_hit_rate','properties',898,'2016-08-03 22:15:06'),(730968,'defense','properties',1217,'2016-08-03 22:15:06'),(730968,'determination','properties',597,'2016-08-03 22:15:06'),(730968,'dexterity','attributes',1148,'2016-08-03 22:15:06'),(730968,'earth','elemental',282,'2016-08-03 22:15:06'),(730968,'fire','elemental',284,'2016-08-03 22:15:06'),(730968,'healing_magic_potency','properties',200,'2016-08-13 21:25:55'),(730968,'heavy_resistance','resistances',0,'2016-08-03 22:15:06'),(730968,'hp','core',15993,'2016-08-03 22:15:06'),(730968,'ice','elemental',283,'2016-08-03 22:15:06'),(730968,'intelligence','attributes',186,'2016-08-03 22:15:06'),(730968,'lightning','elemental',282,'2016-08-03 22:15:06'),(730968,'magic_defense','properties',1217,'2016-08-03 22:15:06'),(730968,'mind','attributes',200,'2016-08-13 21:25:55'),(730968,'mp','core',6935,'2016-08-03 22:15:06'),(730968,'parry','properties',437,'2016-08-13 23:19:20'),(730968,'piercing_resistance','resistances',100,'2016-08-03 22:15:06'),(730968,'piety','attributes',186,'2016-08-03 22:15:06'),(730968,'poison_resistance','resistances',0,'2016-08-03 22:15:06'),(730968,'silence_resistance','resistances',0,'2016-08-03 22:15:06'),(730968,'skill_speed','properties',432,'2016-08-13 21:25:55'),(730968,'slashing_resistance','resistances',100,'2016-08-03 22:15:06'),(730968,'sleep_resistance','resistances',0,'2016-08-03 22:15:06'),(730968,'slow_resistance','resistances',0,'2016-08-03 22:15:06'),(730968,'spell_speed','properties',354,'2016-08-03 22:15:06'),(730968,'strength','attributes',419,'2016-08-13 23:19:20'),(730968,'stun_resistance','resistances',0,'2016-08-03 22:15:06'),(730968,'tp','core',1000,'2016-08-03 22:15:06'),(730968,'vitality','attributes',865,'2016-08-03 22:15:06'),(730968,'water','elemental',282,'2016-08-03 22:15:06'),(730968,'wind','elemental',280,'2016-08-03 22:15:06');
/*!40000 ALTER TABLE `ranking_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sync_achievements`
--

DROP TABLE IF EXISTS `sync_achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sync_achievements` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `character_id` int(32) NOT NULL,
  `public` int(1) NOT NULL DEFAULT '0',
  `queue` int(11) NOT NULL DEFAULT '1',
  `code` int(32) NOT NULL,
  `last_updated` int(32) NOT NULL,
  `last_active` int(32) NOT NULL,
  `score` int(8) NOT NULL,
  `sync` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `character_id` (`character_id`),
  KEY `queue` (`queue`),
  KEY `last_updated` (`last_updated`),
  KEY `last_active` (`last_active`),
  KEY `code` (`code`),
  KEY `public` (`public`),
  KEY `score` (`score`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sync_achievements`
--

LOCK TABLES `sync_achievements` WRITE;
/*!40000 ALTER TABLE `sync_achievements` DISABLE KEYS */;
/*!40000 ALTER TABLE `sync_achievements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sync_characters`
--

DROP TABLE IF EXISTS `sync_characters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sync_characters` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `lodestone_id` int(32) NOT NULL,
  `last_updated` int(32) NOT NULL,
  `last_active` int(32) NOT NULL,
  `added` int(32) NOT NULL,
  `queue` int(2) NOT NULL DEFAULT '1',
  `code` int(5) NOT NULL,
  `sync` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lodestone_id` (`lodestone_id`),
  KEY `added` (`added`),
  KEY `queue` (`queue`),
  KEY `sync` (`sync`),
  KEY `last_updated` (`last_updated`),
  KEY `last_active` (`last_active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sync_characters`
--

LOCK TABLES `sync_characters` WRITE;
/*!40000 ALTER TABLE `sync_characters` DISABLE KEYS */;
/*!40000 ALTER TABLE `sync_characters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sync_characters_data`
--

DROP TABLE IF EXISTS `sync_characters_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sync_characters_data` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `character_id` int(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `server` varchar(32) NOT NULL,
  `avatar` varchar(128) NOT NULL,
  `sync` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `character_id` (`character_id`),
  KEY `name` (`name`),
  KEY `server` (`server`),
  KEY `avatar` (`avatar`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sync_characters_data`
--

LOCK TABLES `sync_characters_data` WRITE;
/*!40000 ALTER TABLE `sync_characters_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `sync_characters_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sync_fc`
--

DROP TABLE IF EXISTS `sync_fc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sync_fc` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `lodestone_id` varchar(64) NOT NULL,
  `last_updated` int(32) NOT NULL,
  `added` int(32) NOT NULL,
  `queue` int(2) NOT NULL DEFAULT '1',
  `code` int(2) NOT NULL DEFAULT '0' COMMENT 'Last code during update',
  `stats_sync` timestamp NULL DEFAULT NULL,
  `sync` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lodestone_id` (`lodestone_id`),
  KEY `last_updated` (`last_updated`,`added`,`queue`,`code`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sync_fc`
--

LOCK TABLES `sync_fc` WRITE;
/*!40000 ALTER TABLE `sync_fc` DISABLE KEYS */;
/*!40000 ALTER TABLE `sync_fc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sync_fc_data`
--

DROP TABLE IF EXISTS `sync_fc_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sync_fc_data` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `fc_id` int(32) NOT NULL,
  `name` varchar(64) NOT NULL,
  `server` varchar(32) NOT NULL,
  `emblum` varchar(512) NOT NULL COMMENT 'Json of all 3 pieces',
  `members` int(4) NOT NULL,
  `sync` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fc_id` (`fc_id`),
  KEY `name` (`name`),
  KEY `server` (`server`),
  KEY `active_members` (`members`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sync_fc_data`
--

LOCK TABLES `sync_fc_data` WRITE;
/*!40000 ALTER TABLE `sync_fc_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `sync_fc_data` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-15 20:06:54
