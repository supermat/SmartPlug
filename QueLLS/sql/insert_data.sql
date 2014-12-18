CREATE DATABASE  IF NOT EXISTS `smartplug` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `smartplug`;
-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: smartplug
-- ------------------------------------------------------
-- Server version	5.5.39

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
-- Dumping data for table `action`
--

LOCK TABLES `action` WRITE;
/*!40000 ALTER TABLE `action` DISABLE KEYS */;
INSERT INTO `action` (`id_action`, `name`, `type`, `command`, `parameters`) 
VALUES 
(1,'Allumer/Eteindre','ON','http://#ip_address#/goform/GreenAP','{\"Content-type\":\"application/x-www-form-urlencoded\", \"GAPAction#time_slot#\":\"ON\", \"GAPSHour#time_slot#\":\"#start_time_hr#\", \"GAPSMinute#time_slot#\":\"#start_time_mn#\", \"GAPEHour#time_slot#\":\"#end_time_hr#\", \"GAPEMinute#time_slot#\":\"#end_time_mn#\"}'),
(2,'Activer le Wifi 50%','WiFiOFF','http://#ip_address#/goform/GreenAP','{\"Content-type\":\"application/x-www-form-urlencoded\", \"GAPAction#time_slot#\":\"TX50\", \"GAPSHour#time_slot#\":\"#start_time_hr#\", \"GAPSMinute#time_slot#\":\"#start_time_mn#\", \"GAPEHour#time_slot#\":\"#end_time_hr#\", \"GAPEMinute#time_slot#\":\"#end_time_mn#\"}'),
(5,'Eteindre le Wifi',NULL,'http://#ip_address#/goform/SystemCommand','{\"command\":\"ifconfig ra0 down\", \"GAPSHour#time_slot#\":\"#start_time_hr#\", \"GAPSMinute#time_slot#\":\"#start_time_mn#\"}'),
(6,'Activer le Wifi 25%',NULL,'http://#ip_address#/goform/GreenAP','{\"Content-type\":\"application/x-www-form-urlencoded\", \"GAPAction#time_slot#\":\"TX25\", \"GAPSHour#time_slot#\":\"#start_time_hr#\", \"GAPSMinute#time_slot#\":\"#start_time_mn#\", \"GAPEHour#time_slot#\":\"#end_time_hr#\", \"GAPEMinute#time_slot#\":\"#end_time_mn#\"}'),
(7,'Enregistrer la conso',NULL,'http://localhost/quells/record_state.php','{\"mode\":\"fetch\", \"id\":\"#id_plug#\", \"ip_address\":\"#ip_address#\", \"nohelp\":\"true\"}');
/*!40000 ALTER TABLE `action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `plug`
--

LOCK TABLES `plug` WRITE;
/*!40000 ALTER TABLE `plug` DISABLE KEYS */;
INSERT INTO `plug` VALUES 
(1,'Séjour 1','192.168.0.11'),
(2,'Séjour 2','192.168.0.12'),
(3,'Cuisine 1','192.168.0.13'),
(4,'Chambre 1','192.168.0.14');
/*!40000 ALTER TABLE `plug` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `group`
--

LOCK TABLES `group` WRITE;
/*!40000 ALTER TABLE `group` DISABLE KEYS */;
INSERT INTO `group` VALUES 
(1,'Répéteurs Wifi',1,0,1,0),
(2,'Lumières permanentes',1,1,0,10),
(4,'Relevé de consommation',1,0,0,NULL);
/*!40000 ALTER TABLE `group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `plan`
--

LOCK TABLES `plan` WRITE;
/*!40000 ALTER TABLE `plan` DISABLE KEYS */;
INSERT INTO `plan` VALUES 
(1,2,1,1,1,1,1,1,1,1,0,0,'06:04:14','08:14:17'),
(2,2,3,1,1,1,1,1,1,1,0,0,'06:30:44','08:15:45'),
(4,1,4,4,2,1,1,1,1,1,1,1,'22:00:11','06:00:11'),
(5,2,1,2,1,1,1,1,1,1,0,0,'18:45:00','21:15:00'),
(6,1,1,4,6,1,1,1,1,1,1,1,'22:00:00','06:00:00'),
(7,2,3,2,1,1,1,1,1,1,0,0,'17:00:00','21:00:00'),
(8,2,2,1,1,1,1,1,1,1,0,0,'05:30:00','07:30:00'),
(9,2,2,2,1,1,1,1,1,0,0,0,'16:45:00','22:05:00'),
(10,2,2,2,1,0,0,0,0,1,1,1,'17:45:00','21:00:00'),
(11,1,2,4,6,1,1,1,1,1,1,1,'22:00:00','06:00:00'),
(12,4,4,NULL,7,1,1,1,1,1,1,1,NULL,NULL),
(13,4,1,NULL,7,1,1,1,1,1,1,1,NULL,NULL),
(14,4,3,NULL,7,1,1,1,1,1,1,1,NULL,NULL),
(15,4,2,NULL,7,1,1,1,1,1,1,1,NULL,NULL);
/*!40000 ALTER TABLE `plan` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-12-18 11:11:44
