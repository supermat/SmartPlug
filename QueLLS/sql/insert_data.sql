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
INSERT INTO `action` VALUES 
(1,'Allumer/Eteindre',NULL,'http://#ip_address#/goform/GreenAP','{\"GAPAction#time_slot#\":\"ON\", \"GAPSHour#time_slot#\":\"#start_time_hr#\", \"GAPSMinute#time_slot#\":\"#start_time_mn#\", \"GAPEHour#time_slot#\":\"#end_time_hr#\", \"GAPEMinute#time_slot#\":\"#end_time_mn#\"}'),
(2,'Activer le Wifi 50%',NULL,'http://#ip_address#/goform/GreenAP','{\"GAPAction#time_slot#\":\"TX50\", \"GAPSHour#time_slot#\":\"#start_time_hr#\", \"GAPSMinute#time_slot#\":\"#start_time_mn#\", \"GAPEHour#time_slot#\":\"#end_time_hr#\", \"GAPEMinute#time_slot#\":\"#end_time_mn#\"}'),
(5,'Eteindre le Wifi',NULL,'http://#ip_address#/goform/GreenAP','{\"command\":\"ifconfig ra0 down\", \"GAPAction#time_slot#\":\"ON\", \"GAPSHour#time_slot#\":\"#start_time_hr#\", \"GAPSMinute#time_slot#\":\"#start_time_mn#\"}');
/*!40000 ALTER TABLE `action` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `plug`
--

LOCK TABLES `plug` WRITE;
/*!40000 ALTER TABLE `plug` DISABLE KEYS */;
INSERT INTO `plug` VALUES 
(1,'Séjour 1','192.168.0.91'),
(2,'Séjour 2','192.168.0.92'),
(3,'Cuisine 1','192.168.0.93'),
(4,'Chambre 1','192.168.0.94');
/*!40000 ALTER TABLE `plug` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-12-15  9:00:46
