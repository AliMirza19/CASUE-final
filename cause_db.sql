-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cause_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_terms`
--

DROP TABLE IF EXISTS `academic_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_terms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_name` varchar(100) NOT NULL,
  `term_code` varchar(10) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_terms`
--

LOCK TABLES `academic_terms` WRITE;
/*!40000 ALTER TABLE `academic_terms` DISABLE KEYS */;
INSERT INTO `academic_terms` VALUES (1,'Spring 2026','261','active','2026-02-01','2026-06-30','2026-04-27 05:25:02','2026-05-10 21:55:28');
/*!40000 ALTER TABLE `academic_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_role` varchar(20) NOT NULL,
  `action_text` text NOT NULL,
  `related_event_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  KEY `activity_logs_related_event_id_foreign` (`related_event_id`),
  KEY `idx_user_role` (`user_role`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `activity_logs_related_event_id_foreign` FOREIGN KEY (`related_event_id`) REFERENCES `events` (`id`) ON DELETE SET NULL,
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,134,'faculty','User logged in',NULL,'2026-05-13 23:06:35','2026-05-13 23:06:35'),(2,134,'faculty','User logged out',NULL,'2026-05-13 23:07:14','2026-05-13 23:07:14'),(3,52,'president','User logged in',NULL,'2026-05-13 23:07:26','2026-05-13 23:07:26'),(4,52,'president','User logged out',NULL,'2026-05-13 23:07:48','2026-05-13 23:07:48'),(5,1,'admin','User logged in',NULL,'2026-05-13 23:10:19','2026-05-13 23:10:19'),(6,1,'admin','User logged out',NULL,'2026-05-13 23:11:11','2026-05-13 23:11:11'),(7,80,'student','User logged in',NULL,'2026-05-13 23:11:22','2026-05-13 23:11:22'),(8,80,'student','Submitted new event: Academic Excellence Summit 2026',NULL,'2026-05-13 23:12:38','2026-05-13 23:12:38'),(9,80,'student','User logged out',NULL,'2026-05-13 23:12:50','2026-05-13 23:12:50'),(10,52,'president','User logged in',NULL,'2026-05-13 23:13:01','2026-05-13 23:13:01'),(11,52,'president','User logged out',NULL,'2026-05-13 23:13:48','2026-05-13 23:13:48'),(12,133,'faculty','User logged in',NULL,'2026-05-13 23:14:03','2026-05-13 23:14:03'),(13,133,'faculty','User logged out',NULL,'2026-05-13 23:14:29','2026-05-13 23:14:29'),(14,134,'faculty','User logged in',NULL,'2026-05-13 23:14:44','2026-05-13 23:14:44'),(15,134,'faculty','User logged out',NULL,'2026-05-13 23:17:09','2026-05-13 23:17:09'),(16,52,'president','User logged in',NULL,'2026-05-13 23:17:23','2026-05-13 23:17:23'),(17,52,'president','User logged out',NULL,'2026-05-13 23:23:18','2026-05-13 23:23:18'),(18,170,'gd','User logged in',NULL,'2026-05-13 23:23:30','2026-05-13 23:23:30'),(19,170,'gd','User logged out',NULL,'2026-05-13 23:24:54','2026-05-13 23:24:54'),(20,52,'president','User logged in',NULL,'2026-05-13 23:25:15','2026-05-13 23:25:15'),(21,52,'president','User logged out',NULL,'2026-05-13 23:26:22','2026-05-13 23:26:22'),(22,172,'smt','User logged in',NULL,'2026-05-13 23:26:42','2026-05-13 23:26:42'),(23,172,'smt','User logged out',NULL,'2026-05-13 23:27:27','2026-05-13 23:27:27'),(24,52,'president','User logged in',NULL,'2026-05-13 23:27:40','2026-05-13 23:27:40'),(25,52,'president','User logged out',NULL,'2026-05-13 23:30:14','2026-05-13 23:30:14'),(26,173,'photo','User logged in',NULL,'2026-05-13 23:30:48','2026-05-13 23:30:48'),(27,173,'photo','User logged out',NULL,'2026-05-13 23:44:05','2026-05-13 23:44:05'),(28,52,'president','User logged in',NULL,'2026-05-13 23:44:22','2026-05-13 23:44:22'),(29,52,'president','User logged out',NULL,'2026-05-13 23:47:52','2026-05-13 23:47:52'),(30,176,'deco','User logged in',NULL,'2026-05-13 23:48:18','2026-05-13 23:48:18'),(31,176,'deco','User logged out',NULL,'2026-05-13 23:49:01','2026-05-13 23:49:01'),(32,52,'president','User logged in',NULL,'2026-05-13 23:49:14','2026-05-13 23:49:14'),(33,52,'president','User logged out',NULL,'2026-05-13 23:49:46','2026-05-13 23:49:46'),(34,1,'admin','User logged in',NULL,'2026-05-14 00:02:27','2026-05-14 00:02:27'),(35,1,'admin','User logged out',NULL,'2026-05-14 02:55:57','2026-05-14 02:55:57'),(36,1,'admin','User logged in',NULL,'2026-05-14 03:37:18','2026-05-14 03:37:18'),(37,1,'admin','User logged out',NULL,'2026-05-14 03:39:08','2026-05-14 03:39:08'),(38,1,'admin','User logged in',NULL,'2026-05-14 03:39:22','2026-05-14 03:39:22'),(39,1,'admin','User logged out',NULL,'2026-05-14 03:39:55','2026-05-14 03:39:55'),(40,118,'student','User logged in',NULL,'2026-05-14 03:40:08','2026-05-14 03:40:08'),(41,118,'student','Submitted new event: Campus Innovation & Impact Expo',NULL,'2026-05-14 03:42:05','2026-05-14 03:42:05'),(42,118,'student','User logged out',NULL,'2026-05-14 03:42:16','2026-05-14 03:42:16'),(43,52,'president','User logged in',NULL,'2026-05-14 03:42:29','2026-05-14 03:42:29'),(44,52,'president','User logged out',NULL,'2026-05-14 03:43:21','2026-05-14 03:43:21'),(45,133,'faculty','User logged in',NULL,'2026-05-14 03:43:42','2026-05-14 03:43:42'),(46,133,'faculty','User logged out',NULL,'2026-05-14 03:44:28','2026-05-14 03:44:28'),(47,134,'faculty','User logged in',NULL,'2026-05-14 03:44:43','2026-05-14 03:44:43'),(48,134,'faculty','User logged out',NULL,'2026-05-14 03:45:39','2026-05-14 03:45:39'),(49,52,'president','User logged in',NULL,'2026-05-14 03:45:57','2026-05-14 03:45:57'),(50,52,'president','User logged out',NULL,'2026-05-14 03:47:10','2026-05-14 03:47:10'),(51,170,'gd','User logged in',NULL,'2026-05-14 03:47:37','2026-05-14 03:47:37'),(52,170,'gd','User logged out',NULL,'2026-05-14 03:48:11','2026-05-14 03:48:11'),(53,52,'president','User logged in',NULL,'2026-05-14 03:48:28','2026-05-14 03:48:28'),(54,52,'president','User logged out',NULL,'2026-05-14 04:02:30','2026-05-14 04:02:30'),(55,118,'student','User logged in',NULL,'2026-05-14 04:02:48','2026-05-14 04:02:48'),(56,118,'student','User logged out',NULL,'2026-05-14 04:13:07','2026-05-14 04:13:07'),(57,118,'student','User logged in',NULL,'2026-05-14 04:21:43','2026-05-14 04:21:43'),(58,118,'student','Submitted new event: FYP SEMINAR',NULL,'2026-05-14 04:23:33','2026-05-14 04:23:33'),(59,118,'student','User logged out',NULL,'2026-05-14 04:23:43','2026-05-14 04:23:43'),(60,52,'president','User logged in',NULL,'2026-05-14 04:23:56','2026-05-14 04:23:56'),(61,52,'president','User logged out',NULL,'2026-05-14 04:24:49','2026-05-14 04:24:49'),(62,133,'faculty','User logged in',NULL,'2026-05-14 04:25:05','2026-05-14 04:25:05'),(63,133,'faculty','User logged out',NULL,'2026-05-14 04:25:36','2026-05-14 04:25:36'),(64,134,'faculty','User logged in',NULL,'2026-05-14 04:25:47','2026-05-14 04:25:47'),(65,134,'faculty','User logged out',NULL,'2026-05-14 04:26:44','2026-05-14 04:26:44'),(66,118,'student','User logged in',NULL,'2026-05-14 04:26:58','2026-05-14 04:26:58'),(67,118,'student','User logged out',NULL,'2026-05-14 04:27:43','2026-05-14 04:27:43'),(68,52,'president','User logged in',NULL,'2026-05-14 04:27:57','2026-05-14 04:27:57'),(69,52,'president','User logged out',NULL,'2026-05-14 04:29:30','2026-05-14 04:29:30'),(70,170,'gd','User logged in',NULL,'2026-05-14 04:29:43','2026-05-14 04:29:43'),(71,170,'gd','User logged out',NULL,'2026-05-14 04:30:28','2026-05-14 04:30:28'),(72,52,'president','User logged in',NULL,'2026-05-14 04:30:41','2026-05-14 04:30:41'),(73,52,'president','User logged out',NULL,'2026-05-14 04:33:15','2026-05-14 04:33:15'),(74,133,'faculty','User logged in',NULL,'2026-05-14 04:33:32','2026-05-14 04:33:32'),(75,133,'faculty','User logged out',NULL,'2026-05-14 05:17:13','2026-05-14 05:17:13'),(76,170,'gd','User logged in',NULL,'2026-05-14 05:17:30','2026-05-14 05:17:30'),(77,170,'gd','User logged out',NULL,'2026-05-14 05:19:16','2026-05-14 05:19:16'),(78,1,'admin','User logged in',NULL,'2026-05-14 05:19:32','2026-05-14 05:19:32'),(79,1,'admin','User logged out',NULL,'2026-05-14 05:24:31','2026-05-14 05:24:31'),(80,133,'faculty','User logged in',NULL,'2026-05-14 05:35:03','2026-05-14 05:35:03'),(81,133,'faculty','User logged out',NULL,'2026-05-14 05:35:16','2026-05-14 05:35:16'),(82,1,'admin','User logged in',NULL,'2026-05-14 05:39:01','2026-05-14 05:39:01'),(83,118,'student','User logged in',NULL,'2026-05-14 05:43:11','2026-05-14 05:43:11'),(84,118,'student','Submitted new event: Future Minds Tech & Creativity Fair 2026',NULL,'2026-05-14 05:44:15','2026-05-14 05:44:15'),(85,118,'student','User logged out',NULL,'2026-05-14 05:44:25','2026-05-14 05:44:25'),(86,52,'president','User logged in',NULL,'2026-05-14 05:44:37','2026-05-14 05:44:37'),(87,52,'president','User logged out',NULL,'2026-05-14 05:45:07','2026-05-14 05:45:07'),(88,133,'faculty','User logged in',NULL,'2026-05-14 05:45:55','2026-05-14 05:45:55'),(89,133,'faculty','User logged out',NULL,'2026-05-14 05:46:33','2026-05-14 05:46:33'),(90,133,'faculty','User logged in',NULL,'2026-05-14 05:48:14','2026-05-14 05:48:14'),(91,133,'faculty','User logged out',NULL,'2026-05-14 05:48:20','2026-05-14 05:48:20'),(92,134,'faculty','User logged in',NULL,'2026-05-14 05:48:58','2026-05-14 05:48:58'),(93,134,'faculty','User logged out',NULL,'2026-05-14 05:51:02','2026-05-14 05:51:02'),(94,134,'faculty','User logged in',NULL,'2026-05-14 05:52:55','2026-05-14 05:52:55'),(95,134,'faculty','User logged out',NULL,'2026-05-14 05:53:25','2026-05-14 05:53:25'),(96,52,'president','User logged in',NULL,'2026-05-14 05:53:39','2026-05-14 05:53:39'),(97,52,'president','User logged out',NULL,'2026-05-14 05:54:37','2026-05-14 05:54:37'),(98,175,'doc','User logged in',NULL,'2026-05-14 05:55:20','2026-05-14 05:55:20'),(99,175,'doc','User logged out',NULL,'2026-05-14 06:00:18','2026-05-14 06:00:18'),(100,172,'smt','User logged in',NULL,'2026-05-14 06:00:35','2026-05-14 06:00:35'),(101,172,'smt','User logged out',NULL,'2026-05-14 06:04:18','2026-05-14 06:04:18'),(102,173,'photo','User logged in',NULL,'2026-05-14 06:04:52','2026-05-14 06:04:52'),(103,173,'photo','User logged out',NULL,'2026-05-14 06:06:14','2026-05-14 06:06:14'),(104,176,'deco','User logged in',NULL,'2026-05-14 06:07:15','2026-05-14 06:07:15'),(105,176,'deco','User logged out',NULL,'2026-05-14 06:08:50','2026-05-14 06:08:50'),(106,170,'gd','User logged in',NULL,'2026-05-14 06:09:02','2026-05-14 06:09:02'),(107,170,'gd','User logged out',NULL,'2026-05-14 06:09:30','2026-05-14 06:09:30'),(108,175,'doc','User logged in',NULL,'2026-05-14 06:11:16','2026-05-14 06:11:16'),(109,175,'doc','User logged out',NULL,'2026-05-14 06:14:54','2026-05-14 06:14:54'),(110,52,'president','User logged in',NULL,'2026-05-14 06:15:41','2026-05-14 06:15:41'),(111,52,'president','User logged out',NULL,'2026-05-14 06:16:26','2026-05-14 06:16:26'),(112,173,'photo','User logged in',NULL,'2026-05-14 06:16:46','2026-05-14 06:16:46'),(113,173,'photo','User logged out',NULL,'2026-05-14 06:17:08','2026-05-14 06:17:08'),(114,52,'president','User logged in',NULL,'2026-05-14 06:17:24','2026-05-14 06:17:24'),(115,52,'president','User logged out',NULL,'2026-05-14 06:17:51','2026-05-14 06:17:51'),(116,175,'doc','User logged in',NULL,'2026-05-14 06:18:05','2026-05-14 06:18:05'),(117,175,'doc','User logged out',NULL,'2026-05-14 06:18:28','2026-05-14 06:18:28'),(118,52,'president','User logged in',NULL,'2026-05-14 06:18:37','2026-05-14 06:18:37'),(119,52,'president','User logged out',NULL,'2026-05-14 06:19:08','2026-05-14 06:19:08'),(120,171,'vc','User logged in',NULL,'2026-05-14 06:20:06','2026-05-14 06:20:06'),(121,171,'vc','User logged out',NULL,'2026-05-14 06:40:06','2026-05-14 06:40:06'),(122,1,'admin','User logged in',NULL,'2026-05-14 06:40:21','2026-05-14 06:40:21'),(123,1,'admin','User logged out',NULL,'2026-05-14 06:40:28','2026-05-14 06:40:28'),(124,133,'faculty','User logged in',NULL,'2026-05-14 06:41:04','2026-05-14 06:41:04'),(125,133,'faculty','User logged out',NULL,'2026-05-14 06:41:11','2026-05-14 06:41:11'),(126,134,'faculty','User logged in',NULL,'2026-05-14 06:41:26','2026-05-14 06:41:26'),(127,134,'faculty','User logged out',NULL,'2026-05-14 06:41:31','2026-05-14 06:41:31'),(128,1,'admin','User logged in',NULL,'2026-05-14 07:03:07','2026-05-14 07:03:07'),(129,1,'admin','User logged out',NULL,'2026-05-14 07:03:40','2026-05-14 07:03:40'),(130,60,'student','User logged in',NULL,'2026-05-14 07:03:50','2026-05-14 07:03:50'),(131,60,'student','User logged out',NULL,'2026-05-14 08:10:10','2026-05-14 08:10:10'),(132,60,'student','User logged in',NULL,'2026-05-14 08:11:06','2026-05-14 08:11:06'),(133,60,'student','Submitted new event: Hackathon',NULL,'2026-05-14 08:14:25','2026-05-14 08:14:25'),(134,60,'student','User logged out',NULL,'2026-05-14 08:14:42','2026-05-14 08:14:42'),(135,52,'president','User logged in',NULL,'2026-05-14 08:15:06','2026-05-14 08:15:06'),(136,52,'president','User logged out',NULL,'2026-05-14 08:17:39','2026-05-14 08:17:39'),(137,133,'faculty','User logged in',NULL,'2026-05-14 08:19:05','2026-05-14 08:19:05'),(138,133,'faculty','User logged out',NULL,'2026-05-14 08:19:36','2026-05-14 08:19:36'),(139,134,'faculty','User logged in',NULL,'2026-05-14 08:19:57','2026-05-14 08:19:57'),(140,134,'faculty','User logged out',NULL,'2026-05-14 08:23:30','2026-05-14 08:23:30'),(141,52,'president','User logged in',NULL,'2026-05-14 08:25:10','2026-05-14 08:25:10'),(142,52,'president','User logged out',NULL,'2026-05-14 08:28:10','2026-05-14 08:28:10'),(143,170,'gd','User logged in',NULL,'2026-05-14 08:28:24','2026-05-14 08:28:24'),(144,170,'gd','User logged out',NULL,'2026-05-14 08:29:37','2026-05-14 08:29:37'),(145,52,'president','User logged in',NULL,'2026-05-14 08:29:47','2026-05-14 08:29:47'),(146,52,'president','User logged out',NULL,'2026-05-14 08:31:05','2026-05-14 08:31:05'),(147,1,'admin','User logged in',NULL,'2026-05-14 08:31:47','2026-05-14 08:31:47'),(148,1,'admin','User logged out',NULL,'2026-05-14 08:38:26','2026-05-14 08:38:26'),(149,60,'student','User logged in',NULL,'2026-05-14 08:38:58','2026-05-14 08:38:58'),(150,60,'student','User logged out',NULL,'2026-05-14 08:40:18','2026-05-14 08:40:18'),(151,134,'faculty','User logged in',NULL,'2026-05-14 08:40:36','2026-05-14 08:40:36'),(152,1,'admin','User logged in',NULL,'2026-05-19 21:50:39','2026-05-19 21:50:39'),(153,1,'admin','User logged out',NULL,'2026-05-19 21:51:24','2026-05-19 21:51:24'),(154,122,'student','User logged in',NULL,'2026-05-19 21:51:37','2026-05-19 21:51:37'),(155,122,'student','User logged out',NULL,'2026-05-19 21:52:14','2026-05-19 21:52:14'),(156,52,'president','User logged in',NULL,'2026-05-19 21:52:26','2026-05-19 21:52:26'),(157,52,'president','President submitted new event: Code for Change: Society Hackathon',NULL,'2026-05-19 22:25:39','2026-05-19 22:25:39'),(158,52,'president','President submitted new event: Code for Change: Society Hackathon',NULL,'2026-05-19 22:30:42','2026-05-19 22:30:42'),(159,52,'president','User logged out',NULL,'2026-05-19 22:31:31','2026-05-19 22:31:31'),(160,133,'faculty','User logged in',NULL,'2026-05-19 22:32:03','2026-05-19 22:32:03'),(161,133,'faculty','User logged out',NULL,'2026-05-19 22:33:10','2026-05-19 22:33:10'),(162,52,'president','User logged in',NULL,'2026-05-19 22:33:30','2026-05-19 22:33:30'),(163,52,'president','User logged out',NULL,'2026-05-19 22:34:31','2026-05-19 22:34:31'),(164,1,'admin','User logged in',NULL,'2026-05-19 22:55:00','2026-05-19 22:55:00'),(165,1,'admin','User logged out',NULL,'2026-05-19 23:02:29','2026-05-19 23:02:29'),(166,1,'admin','User logged in',NULL,'2026-05-19 23:03:20','2026-05-19 23:03:20'),(167,1,'admin','User logged out',NULL,'2026-05-19 23:03:53','2026-05-19 23:03:53'),(168,1,'admin','User logged in',NULL,'2026-05-19 23:12:05','2026-05-19 23:12:05'),(169,1,'admin','User logged out',NULL,'2026-05-19 23:15:34','2026-05-19 23:15:34'),(170,193,'student','User logged in',NULL,'2026-05-19 23:17:33','2026-05-19 23:17:33'),(171,193,'student','Password changed successfully',NULL,'2026-05-19 23:17:56','2026-05-19 23:17:56'),(172,193,'student','User logged out',NULL,'2026-05-19 23:18:06','2026-05-19 23:18:06'),(173,193,'student','Password reset via secure identity verification',NULL,'2026-05-19 23:59:47','2026-05-19 23:59:47'),(174,193,'student','User logged in',NULL,'2026-05-20 00:00:16','2026-05-20 00:00:16'),(175,193,'student','User logged in',NULL,'2026-06-17 13:27:47','2026-06-17 13:27:47'),(176,193,'student','User logged out',NULL,'2026-06-17 13:28:26','2026-06-17 13:28:26'),(177,133,'faculty','User logged in',NULL,'2026-06-17 13:37:05','2026-06-17 13:37:05'),(178,133,'faculty','Submitted new event: interni',NULL,'2026-06-17 14:09:16','2026-06-17 14:09:16'),(179,133,'faculty','User logged out',NULL,'2026-06-17 14:09:49','2026-06-17 14:09:49'),(180,52,'president','User logged in',NULL,'2026-06-17 14:09:59','2026-06-17 14:09:59'),(181,52,'president','User logged out',NULL,'2026-06-17 14:11:04','2026-06-17 14:11:04'),(182,134,'faculty','User logged in',NULL,'2026-06-17 14:11:18','2026-06-17 14:11:18'),(183,134,'faculty','User logged out',NULL,'2026-06-17 14:11:36','2026-06-17 14:11:36'),(184,133,'faculty','User logged in',NULL,'2026-06-17 14:11:55','2026-06-17 14:11:55'),(185,133,'faculty','User logged in',NULL,'2026-06-17 14:12:48','2026-06-17 14:12:48'),(186,133,'faculty','User logged out',NULL,'2026-06-17 14:13:18','2026-06-17 14:13:18'),(187,134,'faculty','User logged in',NULL,'2026-06-17 14:13:46','2026-06-17 14:13:46'),(188,1,'admin','User logged in',NULL,'2026-06-18 00:45:32','2026-06-18 00:45:32'),(189,1,'admin','User logged out',NULL,'2026-06-18 00:49:23','2026-06-18 00:49:23'),(190,170,'gd','User logged in',NULL,'2026-06-18 00:54:33','2026-06-18 00:54:33'),(191,170,'gd','User logged out',NULL,'2026-06-18 00:55:37','2026-06-18 00:55:37'),(192,170,'gd','User logged in',NULL,'2026-06-18 01:15:57','2026-06-18 01:15:57'),(193,170,'gd','User logged in',NULL,'2026-06-21 12:30:18','2026-06-21 12:30:18'),(194,170,'gd','User logged out',NULL,'2026-06-21 13:36:08','2026-06-21 13:36:08'),(195,1,'admin','User logged in',NULL,'2026-06-21 13:55:31','2026-06-21 13:55:31'),(196,1,'admin','User logged out',NULL,'2026-06-21 13:56:30','2026-06-21 13:56:30'),(197,1,'admin','User logged in',NULL,'2026-06-21 13:58:31','2026-06-21 13:58:31'),(198,1,'admin','User logged out',NULL,'2026-06-21 13:58:52','2026-06-21 13:58:52'),(199,1,'admin','User logged in',NULL,'2026-06-21 13:59:59','2026-06-21 13:59:59'),(200,1,'admin','User logged out',NULL,'2026-06-21 14:00:59','2026-06-21 14:00:59'),(201,1,'admin','User logged in',NULL,'2026-06-21 14:04:28','2026-06-21 14:04:28'),(202,1,'admin','User logged out',NULL,'2026-06-21 14:08:19','2026-06-21 14:08:19'),(203,194,'student','User logged in',NULL,'2026-06-21 14:15:54','2026-06-21 14:15:54'),(204,194,'student','User logged out',NULL,'2026-06-21 14:18:33','2026-06-21 14:18:33'),(205,194,'student','User logged in',NULL,'2026-06-21 14:18:48','2026-06-21 14:18:48'),(206,194,'student','User logged out',NULL,'2026-06-21 14:19:18','2026-06-21 14:19:18'),(207,52,'president','User logged in',NULL,'2026-06-21 14:19:32','2026-06-21 14:19:32'),(208,52,'president','User logged out',NULL,'2026-06-21 14:19:55','2026-06-21 14:19:55'),(209,133,'faculty','User logged in',NULL,'2026-06-21 14:35:41','2026-06-21 14:35:41'),(210,133,'faculty','Patron submitted new event: Upwork Success Sminar',NULL,'2026-06-21 14:36:57','2026-06-21 14:36:57'),(211,133,'faculty','Patron submitted new event: Upwork Workshop',NULL,'2026-06-21 14:42:11','2026-06-21 14:42:11'),(212,133,'faculty','User logged out',NULL,'2026-06-21 14:46:46','2026-06-21 14:46:46'),(213,134,'faculty','User logged in',NULL,'2026-06-21 14:47:06','2026-06-21 14:47:06'),(214,134,'faculty','User logged out',NULL,'2026-06-21 14:50:21','2026-06-21 14:50:21'),(215,194,'student','User logged in',NULL,'2026-06-21 15:20:54','2026-06-21 15:20:54'),(216,194,'student','Submitted new event: fiver  success seminar',NULL,'2026-06-21 15:21:48','2026-06-21 15:21:48'),(217,194,'student','User logged out',NULL,'2026-06-21 15:21:58','2026-06-21 15:21:58'),(218,52,'president','User logged in',NULL,'2026-06-21 15:22:24','2026-06-21 15:22:24'),(219,52,'president','User logged out',NULL,'2026-06-21 15:23:07','2026-06-21 15:23:07'),(220,133,'faculty','User logged in',NULL,'2026-06-21 15:23:35','2026-06-21 15:23:35'),(221,133,'faculty','User logged out',NULL,'2026-06-21 15:24:17','2026-06-21 15:24:17'),(222,134,'faculty','User logged in',NULL,'2026-06-21 15:24:40','2026-06-21 15:24:40'),(223,134,'faculty','User logged out',NULL,'2026-06-21 15:33:17','2026-06-21 15:33:17'),(224,194,'student','User logged in',NULL,'2026-06-21 15:33:32','2026-06-21 15:33:32'),(225,194,'student','Submitted new event: far',NULL,'2026-06-21 15:34:06','2026-06-21 15:34:06'),(226,194,'student','User logged out',NULL,'2026-06-21 15:34:14','2026-06-21 15:34:14'),(227,52,'president','User logged in',NULL,'2026-06-21 15:34:32','2026-06-21 15:34:32'),(228,52,'president','User logged out',NULL,'2026-06-21 15:35:03','2026-06-21 15:35:03'),(229,133,'faculty','User logged in',NULL,'2026-06-21 15:35:36','2026-06-21 15:35:36'),(230,133,'faculty','User logged in',NULL,'2026-06-21 15:36:02','2026-06-21 15:36:02'),(231,133,'faculty','User logged out',NULL,'2026-06-21 15:36:35','2026-06-21 15:36:35'),(232,134,'faculty','User logged in',NULL,'2026-06-21 15:37:06','2026-06-21 15:37:06'),(233,170,'gd','User logged in',NULL,'2026-06-24 00:09:43','2026-06-24 00:09:43'),(234,170,'gd','User logged out',NULL,'2026-06-24 00:15:48','2026-06-24 00:15:48'),(235,133,'faculty','User logged in',NULL,'2026-06-24 00:16:55','2026-06-24 00:16:55'),(236,133,'faculty','User logged out',NULL,'2026-06-24 00:18:09','2026-06-24 00:18:09'),(237,1,'admin','User logged in',NULL,'2026-06-24 02:07:37','2026-06-24 02:07:37'),(238,1,'admin','User logged out',NULL,'2026-06-24 02:08:05','2026-06-24 02:08:05'),(239,170,'gd','User logged in',NULL,'2026-06-24 02:14:26','2026-06-24 02:14:26'),(240,170,'gd','User logged out',NULL,'2026-06-24 02:23:13','2026-06-24 02:23:13'),(241,133,'faculty','User logged in',NULL,'2026-06-24 02:23:23','2026-06-24 02:23:23');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `target_role` varchar(255) DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_user_id_foreign` (`user_id`),
  CONSTRAINT `announcements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (3,52,'Launch of Campus Innovation & Impact Expo 2026','We are excited to announce the Campus Innovation & Impact Expo, a platform for students to showcase their ideas, projects, and innovations.','/storage/announcements/rAYv0EeuxWGSeGlJpq3QH38ulHxucsRBg8feb6h0.jpg',NULL,NULL,NULL,'2026-05-14 03:56:46','2026-05-14 04:01:52');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `budgets`
--

DROP TABLE IF EXISTS `budgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `budgets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `remaining_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_term_budget` (`term_id`),
  CONSTRAINT `budgets_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `academic_terms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `budgets`
--

LOCK TABLES `budgets` WRITE;
/*!40000 ALTER TABLE `budgets` DISABLE KEYS */;
INSERT INTO `budgets` VALUES (1,1,5000.00,3907.00,1,'2026-04-28 14:03:32','2026-06-21 15:37:53');
/*!40000 ALTER TABLE `budgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('cause-smart-society-cache-ai_analysis_event_1_hod','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:567:\"Anomaly detected: The total requested budget for the Academic Excellence Summit 2026 (PKR 500.00) is significantly lower than typical averages for similar events (seminars: PKR 10,000). \n\nThe item \"tea\" with a total cost of PKR 500.00 for a quantity of 10 appears overpriced (PKR 50 per unit). \n\nConsidering historical context, the budget seems unrealistic. I recommend revising the budget to align with expected averages (PKR 10,000 for a seminar) and re-evaluating the cost of tea (typical average: PKR 5-10 per unit). Suggested reduction: 90% of the tea item cost.\";s:9:\"anomalies\";a:0:{}}',1778735696),('cause-smart-society-cache-ai_analysis_event_1_patron','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:655:\"Upon analyzing the event budget for the Academic Excellence Summit 2026, an anomaly was found. The total requested amount of PKR 500.00 is allocated entirely to \"tea\" with a quantity of 10, which seems unusually high. The market rate for tea in Pakistan is approximately PKR 20-50 per cup, depending on the type and brand. Assuming an average cost of PKR 35 per cup, the total cost for 10 cups of tea should be around PKR 350. However, the total requested amount exceeds this estimate, suggesting that the cost per unit may be overpriced or that the quantity is not accurately represented. Further verification is required to justify the allocated amount.\";s:9:\"anomalies\";a:0:{}}',1778735655),('cause-smart-society-cache-ai_analysis_event_11_hod','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:550:\"Anomaly detected: The total requested budget for the \"Upwork Workshop\" (PKR 56.00) is significantly lower than typical averages for similar events (seminars: PKR 10,000). \n\nHowever, the item \"tea\" with a total cost of PKR 56.00 seems overpriced. \n\nRecommendation: Verify the cost of \"tea\" and consider reducing it to a more reasonable amount (e.g., PKR 500-1000). The total budget should be revised to align with expected averages for seminars (PKR 10,000). Consider adding other necessary items to the budget, such as venue, speakers, and equipment.\";s:9:\"anomalies\";a:0:{}}',1782074843),('cause-smart-society-cache-ai_analysis_event_12_hod','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:824:\"Anomaly detected: The total requested budget for the \"Fiver Success Seminar\" (PKR 182.00) is significantly lower than the average seminar budget (PKR 10,000). \n\nHowever, the itemized cost for tea (PKR 182.00 for 91 quantities) seems unusually high, averaging PKR 2 per unit, which is plausible.\n\nConsidering the seminar\'s overall budget, it\'s likely underreported. I recommend verifying the budget to ensure all necessary expenses are included. If the event requires additional items (e.g., venue, speakers, equipment), the total cost will exceed the current request.\n\nTo align with historical averages, I suggest revising the budget to approximately PKR 10,000 for a seminar. The current tea cost can be maintained, but other essential expenses should be added and prioritized to utilize the recommended budget effectively.\";s:9:\"anomalies\";a:0:{}}',1782077096),('cause-smart-society-cache-ai_analysis_event_12_patron','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:564:\"Upon analyzing the event budget for the \"fiver success seminar\", an anomaly is detected. The total requested amount of PKR 182.00 for 91 units of tea appears unusually high. The average market rate for tea in Pakistan is approximately PKR 2-5 per cup. Therefore, the total cost for 91 cups of tea should be around PKR 182-455, assuming a high-end rate. However, the total requested amount matches the high-end estimate, but it is still questionable as it leaves no budget for other seminar expenses. This suggests that the budget may be overpriced or misallocated.\";s:9:\"anomalies\";a:0:{}}',1782077031),('cause-smart-society-cache-ai_analysis_event_13_hod','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:514:\"Anomaly detected: The total requested budget for the \"far\" event (PKR 89.00) is significantly lower than typical averages for events (e.g., tech events: PKR 15,000, seminars: PKR 10,000). \n\nHowever, the item \"biscuts\" with a total cost of PKR 89.00 for a quantity of 1 appears overpriced. \n\nRecommendation: Verify the cost of \"biscuts\" and consider reductions or explore alternative options to ensure a reasonable expense. Additionally, review the event scope to ensure it aligns with the minimal budget allocated.\";s:9:\"anomalies\";a:0:{}}',1782077839),('cause-smart-society-cache-ai_analysis_event_13_patron','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:419:\"Upon analyzing the event budget, an anomaly is detected. The total requested amount of PKR 89.00 for a single unit of \"biscuts\" appears to be unusually high. In the Pakistani market, a standard packet of biscuits typically costs between PKR 20 to PKR 50, depending on the brand and quality. Therefore, the quoted price of PKR 89.00 for a single unit of biscuits seems excessively high and warrants further verification.\";s:9:\"anomalies\";a:0:{}}',1782077778),('cause-smart-society-cache-ai_analysis_event_2_hod','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:572:\"Anomaly detected: The total requested budget (PKR 7.00) is significantly lower than expected averages for similar events (tech events: PKR 15,000, seminars: PKR 10,000). \n\nItem analysis: \"Biscuts\" (assumed to be a typo for \"biscuits\") seems overpriced at PKR 1.00 per unit, considering typical costs.\n\nRecommendation: Review and revise the budget to align with historical averages. Consider allocating funds to essential items such as venue, speakers, and marketing, rather than excessive quantities of biscuits. Suggested reduction: 90% of the current biscuit allocation.\";s:9:\"anomalies\";a:0:{}}',1778751903),('cause-smart-society-cache-ai_analysis_event_2_patron','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:528:\"Anomaly detected. The total budget for the Campus Innovation & Impact Expo is PKR 14.00, which is allocated entirely to \"biscuts\" with a quantity of 7. This amount is unusually high for biscuits, considering the average market rate for a packet of biscuits in Pakistan is around PKR 20-50 per dozen. The cost per biscuit in this event budget is approximately PKR 2.00, which is reasonable, but the overall allocation of the entire event budget to biscuits is questionable, suggesting a potential error or misallocation of funds.\";s:9:\"anomalies\";a:0:{}}',1778751839),('cause-smart-society-cache-ai_analysis_event_22_hod','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:839:\"Analyzing the event budget, I notice an anomaly: \n\n- The total budget for \"i ma requesting new event\" is PKR 1288.00, which is significantly lower than typical averages for events (PKR 10,000 - 15,000).\n- The item \"samosa\" accounts for the entire budget, with a unit price of approximately PKR 23.00 per samosa, which seems reasonable.\n- However, the event title and description lack clarity, making it challenging to determine the event type and expected expenses.\n\nConsidering the low total budget, I do not recommend reductions. Instead, I suggest the HOD request more information about the event to ensure it aligns with typical event expenses and averages. If the event is a small gathering or meeting, the budget might be sufficient. However, if it\'s a larger event, such as a seminar or tech event, the budget may be underallocated.\";s:9:\"anomalies\";a:0:{}}',1778571798),('cause-smart-society-cache-ai_analysis_event_22_patron','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:428:\"Upon analyzing the event budget, an anomaly is detected. The total cost of samosas (PKR 1288.00) for a quantity of 56 seems unusually high. The average market rate for samosas in Pakistan is typically between PKR 10-20 per piece. Based on this, the estimated total cost for 56 samosas should be between PKR 560-1120, which is lower than the requested amount. This discrepancy suggests that the cost of samosas may be overpriced.\";s:9:\"anomalies\";a:0:{}}',1778571730),('cause-smart-society-cache-ai_analysis_event_23_hod','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:697:\"Anomaly detected: The total requested budget (PKR 435.00) seems unusually low for an event. \n\nHistorical context: Assuming average event costs (tech events: 15k, seminars: 10k), the requested budget is significantly lower.\n\nItem analysis: Tea for 87 people at PKR 435.00 (PKR 5 per person) appears reasonable.\n\nHowever, considering the overall budget, it seems incomplete. I recommend verifying the event type and scope to ensure all necessary expenses are included.\n\nTo align with expected averages, I suggest reviewing and potentially increasing the budget to cover essential event costs, such as venue, speakers, and equipment. The current budget may not be sufficient to host a standard event.\";s:9:\"anomalies\";a:0:{}}',1778596889),('cause-smart-society-cache-ai_analysis_event_23_patron','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:494:\"Upon analyzing the event budget, an anomaly is detected. The total requested amount of PKR 435.00 for 87 units of tea appears unusually high. Assuming a standard serving size, the cost per unit of tea is approximately PKR 5.00, which is excessive for a typical university event in Pakistan. Market rates for tea in Pakistan are generally lower, ranging from PKR 1 to PKR 3 per cup, depending on the type and quality. This discrepancy suggests that the budgeted amount for tea may be overpriced.\";s:9:\"anomalies\";a:0:{}}',1778596837),('cause-smart-society-cache-ai_analysis_event_25_hod','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:577:\"Anomaly detected: The total requested budget for the \"science\" event (PKR 86.00) is significantly lower than typical averages for similar events (e.g., tech events: PKR 15,000, seminars: PKR 10,000).\n\nOverpriced item: Pakoray (PKR 86.00 for 43 quantity) seems excessively expensive, averaging PKR 2 per pakora, which is unusually high.\n\nRecommendation: Consider revising the budget to align with historical averages. Allocate funds for essential items, and reduce or negotiate the price of pakoray. Suggest a more realistic budget range: PKR 5,000 - 10,000 for a science event.\";s:9:\"anomalies\";a:0:{}}',1778615924),('cause-smart-society-cache-ai_analysis_event_25_patron','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:493:\"Upon analyzing the event budget, an anomaly is detected. The total requested amount of PKR 86.00 for 43 pakoray items suggests an unusually high cost per unit. Typically, pakoray are a relatively inexpensive snack in Pakistan. The cost per pakora is approximately PKR 2.00, which is excessively high for a student university event. This indicates a potential overpricing or miscalculation in the budget. Further verification of market rates is recommended to ensure the accuracy of the budget.\";s:9:\"anomalies\";a:0:{}}',1778615890),('cause-smart-society-cache-ai_analysis_event_3_hod','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:733:\"Anomaly detected: The total requested budget for the FYP SEMINAR event (PKR 15.00) is significantly lower than the typical average for seminars (PKR 10,000). \n\nHowever, considering the itemized breakdown, the TEA item (PKR 15.00 for a quantity of 3) seems unrealistic, as it implies a cost of PKR 5.00 per unit of tea, which is plausible. \n\nGiven the historical context, the total budget is still extremely low. I suggest verifying the event details and scaling up the budget to meet the typical average for seminars (PKR 10,000). The current budget may not be sufficient to cover all event expenses. \n\nRecommendation: Review and revise the budget to ensure it aligns with historical averages and covers all necessary event expenses.\";s:9:\"anomalies\";a:0:{}}',1778754360),('cause-smart-society-cache-ai_analysis_event_3_patron','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:451:\"Upon analyzing the event budget for the FYP SEMINAR, an anomaly is detected. The total requested amount of PKR 15.00 for 3 units of TEA appears to be unusually high. Typically, the cost of tea for university events in Pakistan is significantly lower, ranging from PKR 1 to 5 per unit, depending on the type and quality. Therefore, the allocated amount of PKR 15.00 for 3 units of tea seems overpriced and warrants further verification of market rates.\";s:9:\"anomalies\";a:0:{}}',1778754318),('cause-smart-society-cache-ai_analysis_event_4_hod','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:514:\"Anomaly detected: The total requested budget (PKR 4.00) is significantly lower than typical averages for tech events (PKR 15,000). \n\nThe item \"tea\" with a total cost of PKR 4.00 appears to be severely underpriced or misstated, considering the event title and typical costs.\n\nRecommendation: Review and revise the budget to reflect realistic costs. Consider allocating funds according to historical averages (e.g., PKR 15,000 for tech events). The current budget may not be sufficient to support a successful event.\";s:9:\"anomalies\";a:0:{}}',1778759586),('cause-smart-society-cache-ai_analysis_event_4_patron','a:3:{s:7:\"flagged\";b:0;s:7:\"message\";s:289:\"No anomalies were found. The event budget lists a single item, \"tea\", with a quantity of 1 and a total cost of PKR 0.00, which is unusually low but not overpriced as it is zero. This suggests that the budget may not be fully detailed or is a placeholder, rather than an actual expenditure.\";s:9:\"anomalies\";a:0:{}}',1778759178),('cause-smart-society-cache-ai_analysis_event_5_hod','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:710:\"Anomaly detected: The total requested budget for the Hackathon event (PKR 300.00) is significantly lower than the typical average for tech events (PKR 15,000.00). However, the itemized cost for TEA (PKR 300.00 for 5 units) appears to be overpriced.\n\nConsidering historical context, a more realistic budget for a Hackathon event would be around PKR 15,000.00. The current request may indicate an error or omission of essential expenses.\n\nRecommendation: Review and revise the budget to include all necessary expenses, such as venue, equipment, and participant costs. Allocate a more realistic amount for TEA and other items. Suggest a revised budget of PKR 15,000.00, with a more detailed breakdown of expenses.\";s:9:\"anomalies\";a:0:{}}',1778768425),('cause-smart-society-cache-ai_analysis_event_5_patron','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:460:\"Upon analyzing the event budget for the Hackathon, an anomaly was found. The total requested amount of PKR 300.00 is allocated entirely to TEA, with a quantity of 5. This suggests that each unit of TEA costs PKR 60.00, which is unusually high for a student university event in Pakistan. Typically, the cost of tea for such events would be significantly lower, around PKR 10-20 per cup. This discrepancy indicates a potential overpricing or error in the budget.\";s:9:\"anomalies\";a:0:{}}',1778768359),('cause-smart-society-cache-ai_analysis_event_6_patron','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:315:\"Anomaly detected. The total requested amount of PKR 30.00 for a single samosay is unusually high. Typically, the market rate for a samosay in Pakistan ranges from PKR 10 to PKR 50 per dozen, not per piece. This suggests a significant overpricing, and the budget item warrants further verification and justification.\";s:9:\"anomalies\";a:0:{}}',1779251543),('cause-smart-society-cache-ai_analysis_event_7_patron','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:416:\"Anomaly detected. The total budget for the \"Code for Change: Society Hackathon\" event is PKR 90.00, which is entirely allocated to samosay (a local snack). The average market rate for samosay in Pakistan is around PKR 10-20 per piece. With a quantity of 3, the expected total cost should be around PKR 30-60, not PKR 90.00. This suggests that the requested amount for samosay is unusually high and may be overpriced.\";s:9:\"anomalies\";a:0:{}}',1782281852),('cause-smart-society-cache-ai_analysis_event_9_hod','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:591:\"Anomaly detected: The total requested budget for the \"interni\" event (PKR 180.00) seems unusually low compared to typical averages for events (seminars: PKR 10,000, tech events: PKR 15,000).\n\nItem analysis: TEA (Quantity 45, Total PKR 180.00) implies a cost of PKR 4 per unit, which is reasonable.\n\nRecommendation: Considering the low total budget, it\'s likely an error. Suggest verifying the event scope and expected attendance to determine a more realistic budget. If the event is small, a more suitable budget might be around PKR 5,000 to PKR 10,000, depending on the actual requirements.\";s:9:\"anomalies\";a:0:{}}',1781727238),('cause-smart-society-cache-ai_analysis_event_9_patron','a:3:{s:7:\"flagged\";b:1;s:7:\"message\";s:592:\"Upon analyzing the event budget for \"interni\", an anomaly is detected. The total requested amount of PKR 180.00 for 45 units of TEA seems unusually high. Typically, the market rate for tea in Pakistan is around PKR 10-20 per cup. Therefore, the total cost for 45 cups should be approximately PKR 450-900, but since the total requested is only PKR 180, it appears that the cost per unit is extremely low, around PKR 4 per cup, which is unrealistic. This suggests an error in the budget or an unusually low-cost tea option. Further verification is required to ensure the accuracy of the budget.\";s:9:\"anomalies\";a:0:{}}',1781727178),('cause-smart-society-cache-smart_ticker_insights','a:5:{i:0;s:14:\"0% budget used\";i:1;s:16:\"3 events pending\";i:2;s:16:\"Elections closed\";i:3;s:22:\"No candidates approved\";i:4;s:16:\"Budget untouched\";}',1782286661);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `candidate_applications`
--

DROP TABLE IF EXISTS `candidate_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidate_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `manifesto_text` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `candidate_applications_student_id_foreign` (`student_id`),
  CONSTRAINT `candidate_applications_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidate_applications`
--

LOCK TABLES `candidate_applications` WRITE;
/*!40000 ALTER TABLE `candidate_applications` DISABLE KEYS */;
INSERT INTO `candidate_applications` VALUES (1,32,'I aim to enhance the technical skills of students through workshops and hackathons. My vision is to bridge the gap between academia and industry.','pending','2026-05-10 00:18:53','2026-05-12 14:21:36'),(2,33,'I believe in inclusivity and diversity. I will ensure every student has a voice in our society and we will host cultural exchange events.','pending','2026-05-10 00:18:53','2026-05-12 14:21:36'),(3,34,'I want to lead the CAUSE society to new heights by organizing more technical workshops and social events. My goal is to bridge the gap between students and industry.','pending','2026-05-10 21:40:21','2026-05-10 21:40:21'),(4,35,'I want to lead the CAUSE society to new heights by organizing more technical workshops and social events. My goal is to bridge the gap between students and industry.','pending','2026-05-10 21:40:21','2026-05-10 21:40:21'),(5,36,'I want to lead the CAUSE society to new heights by organizing more technical workshops and social events. My goal is to bridge the gap between students and industry.','pending','2026-05-10 21:40:21','2026-05-10 21:40:21'),(6,53,'Innovation is key. I want to implement AI-driven event management and automate the mundane tasks to focus on creativity.','pending','2026-05-12 14:21:36','2026-05-12 14:21:36'),(7,54,'Sustainability and ethics. I will promote green events and ensure our society operates with the highest moral standards.','pending','2026-05-12 14:21:36','2026-05-12 14:21:36');
/*!40000 ALTER TABLE `candidate_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `candidate_profiles`
--

DROP TABLE IF EXISTS `candidate_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidate_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `manifesto` text NOT NULL,
  `photo_url` varchar(500) DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `vp_name` varchar(255) DEFAULT NULL,
  `status` enum('pending_patron','approved','rejected') NOT NULL DEFAULT 'pending_patron',
  `patron_feedback` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_candidate` (`student_id`),
  CONSTRAINT `candidate_profiles_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidate_profiles`
--

LOCK TABLES `candidate_profiles` WRITE;
/*!40000 ALTER TABLE `candidate_profiles` DISABLE KEYS */;
INSERT INTO `candidate_profiles` VALUES (1,32,'I aim to enhance the technical skills of students through workshops and hackathons. My vision is to bridge the gap between academia and industry.',NULL,NULL,'John Doe VP','pending_patron',NULL,'2026-05-12 14:21:36','2026-05-12 14:21:36'),(2,33,'I believe in inclusivity and diversity. I will ensure every student has a voice in our society and we will host cultural exchange events.',NULL,NULL,'John Doe VP','pending_patron',NULL,'2026-05-12 14:21:36','2026-05-12 14:21:36'),(3,53,'Innovation is key. I want to implement AI-driven event management and automate the mundane tasks to focus on creativity.',NULL,NULL,'John Doe VP','pending_patron',NULL,'2026-05-12 14:21:36','2026-05-12 14:21:36'),(4,54,'Sustainability and ethics. I will promote green events and ensure our society operates with the highest moral standards.',NULL,NULL,'John Doe VP','pending_patron',NULL,'2026-05-12 14:21:36','2026-05-12 14:21:36');
/*!40000 ALTER TABLE `candidate_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificates`
--

DROP TABLE IF EXISTS `certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certificates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `participant_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `certificates_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificates`
--

LOCK TABLES `certificates` WRITE;
/*!40000 ALTER TABLE `certificates` DISABLE KEYS */;
INSERT INTO `certificates` VALUES (1,'8b86ce01-4a7e-40e5-8348-86fb3087cb7f','Usman Ali','alimansoormirza29@gmail.com','Automated Event','2026-06-21 12:48:34','2026-06-21 12:48:34'),(2,'84ab0877-1a3e-4ccf-a6c1-9234ae0dc919','Usman Ali','alimansoormirza29@gmail.com','Automated Event','2026-06-21 12:53:53','2026-06-21 12:53:53'),(3,'6aedf5a6-b016-492f-8fb1-f8d9186e0f26','Usman Ali','alimansoormirza29@gmail.com','Automated Event','2026-06-21 12:57:03','2026-06-21 12:57:03'),(4,'916ecd04-888d-493b-be3a-5d3afd2d7b53','Usman Ali','alimansoormirza29@gmail.com','Automated Event','2026-06-21 12:59:30','2026-06-21 12:59:30'),(5,'6b52e58d-8d6d-43f1-9c2a-2ed8ef05028b','Usman Ali','alimansoormirza29@gmail.com','Automated Event','2026-06-21 13:02:40','2026-06-21 13:02:40'),(6,'67e21506-c38f-463d-b9bc-b2f60dae6eff','Usman Ali','alimansoormirza29@gmail.com','Automated Event','2026-06-21 13:07:39','2026-06-21 13:07:39'),(7,'20ac11db-3fe1-4c37-b4c6-3eca93841544','Usman Ali','alimansoormirza29@gmail.com','Automated Event','2026-06-21 13:13:16','2026-06-21 13:13:16'),(8,'e0c5cbe5-1a70-4da3-b6ba-9c8acea98f04','Usman Ali','alimansoormirza29@gmail.com','Automated Event','2026-06-21 13:14:28','2026-06-21 13:14:28'),(9,'2387f84b-3cf1-46d8-8a48-620014362692','Usman Ali','alimansoormirza29@gmail.com','Automated Event','2026-06-21 13:17:25','2026-06-21 13:17:25'),(10,'ab69f981-d5aa-44b6-8123-b54a86f9a227','Usman Ali','alimansoormirza29@gmail.com','Automated Event','2026-06-21 13:18:22','2026-06-21 13:18:22'),(11,'c02db1e8-5529-456c-a6a4-015617e54534','Ayesha Khan','yaaridosti097@gmail.com','Automated Event','2026-06-21 13:18:23','2026-06-21 13:18:23'),(12,'8280dc76-b0f2-4764-8f38-6580a384f94b','Bilal Ahmed','bilal.ahmed.student@example.com','Automated Event','2026-06-21 13:18:24','2026-06-21 13:18:24'),(13,'4770a873-6504-4e63-a229-dfbbbb75c650','Usman Ali','alimansoormirza29@gmail.com','Automated Event','2026-06-21 13:25:06','2026-06-21 13:25:06'),(14,'f83682d1-3f11-41bf-9da3-5f463d8d0eed','Ayesha Khan','yaaridosti097@gmail.com','Automated Event','2026-06-21 13:25:07','2026-06-21 13:25:07'),(15,'995c26dc-8107-4701-9e82-25c3b52d45cb','Bilal Ahmed','bilal.ahmed.student@example.com','Automated Event','2026-06-21 13:25:08','2026-06-21 13:25:08'),(16,'6632cff2-af0c-4b4b-9c30-b21ec89c0ad4','Usman Ali','alimansoormirza29@gmail.com','Automated Event','2026-06-21 13:35:31','2026-06-21 13:35:31'),(17,'869a95f0-734d-4664-863d-18b4886e3c33','Ayesha Khan','yaaridosti097@gmail.com','Automated Event','2026-06-21 13:35:31','2026-06-21 13:35:31'),(18,'7197236a-f898-4eec-be52-493b2aa205bc','Bilal Ahmed','bilal.ahmed.student@example.com','Automated Event','2026-06-21 13:35:31','2026-06-21 13:35:31'),(19,'8efab3d6-ee1b-4e02-8f51-49f4643c159d','Zain Nawaz','zainn8847@gmail.com','Automated Event','2026-06-24 00:12:26','2026-06-24 00:12:26'),(20,'bc2d52cc-9f95-43e9-8686-d225634bcad4','Ayesha Khan','','Automated Event','2026-06-24 00:12:30','2026-06-24 00:12:30'),(21,'c5da32f4-46cd-42bf-8fc6-0cb891ce4540','Bilal Ahmed','','Automated Event','2026-06-24 00:12:30','2026-06-24 00:12:30'),(22,'23a0bde7-5cae-41f4-9300-86051a6c4daa','Fatima Noor','','Automated Event','2026-06-24 00:12:30','2026-06-24 00:12:30'),(23,'19ddc998-0e10-4bb7-b69b-3c3ed93972a4','Hamza Siddiqui','','Automated Event','2026-06-24 00:12:31','2026-06-24 00:12:31'),(24,'d61838ed-c8a8-4568-9265-ead2dfd39160','Sara Malik','','Automated Event','2026-06-24 00:12:31','2026-06-24 00:12:31'),(25,'a8c184ef-4250-48ef-9427-546b8218f820','Hassan Raza','','Automated Event','2026-06-24 00:12:31','2026-06-24 00:12:31'),(26,'ae884d2c-005b-4895-9e5a-2933aec96345','Zainab Iqbal','','Automated Event','2026-06-24 00:12:31','2026-06-24 00:12:31'),(27,'314d066c-02e5-46ef-a01c-ca6455fe674c','Ali Haider','','Automated Event','2026-06-24 00:12:32','2026-06-24 00:12:32'),(28,'433ec874-7fd6-40a6-b5d2-ba505124f59c','Mariam Aslam','','Automated Event','2026-06-24 00:12:32','2026-06-24 00:12:32'),(29,'9d16af89-2b85-4ff6-aee2-1ff90f757775','Abdullah Sheikh','','Automated Event','2026-06-24 00:12:32','2026-06-24 00:12:32'),(30,'3d7f9f7a-fd5f-4719-97e5-df6b943e1584','Hira Tariq','','Automated Event','2026-06-24 00:12:32','2026-06-24 00:12:32'),(31,'b9544db1-cb42-462c-bd86-3d60fb957906','Omer Farooq','','Automated Event','2026-06-24 00:12:33','2026-06-24 00:12:33'),(32,'7af0bb9b-f695-4029-9c32-c26983d76ff3','Mahnoor Javed','','Automated Event','2026-06-24 00:12:33','2026-06-24 00:12:33'),(33,'9b441ff6-64cd-4063-8451-bcb529c426e5','Danish Khan','','Automated Event','2026-06-24 00:12:33','2026-06-24 00:12:33'),(34,'3611ceaa-2f4d-490b-9bc9-9570b174841b','Zain Nawaz','zainn8847@gmail.com','Automated Event','2026-06-24 00:12:51','2026-06-24 00:12:51'),(35,'4c05d7f5-1194-42d0-97e9-1881b4aa3101','Ayesha Khan','','Automated Event','2026-06-24 00:12:52','2026-06-24 00:12:52'),(36,'4fab63ec-8c9a-41c3-ad78-eb35936786e4','Bilal Ahmed','','Automated Event','2026-06-24 00:12:52','2026-06-24 00:12:52'),(37,'15bbb6ae-ff7e-4ae4-be2f-adac41fe34e4','Fatima Noor','','Automated Event','2026-06-24 00:12:52','2026-06-24 00:12:52'),(38,'764714d8-f542-42bc-9eb6-afa66f726cd6','Hamza Siddiqui','','Automated Event','2026-06-24 00:12:53','2026-06-24 00:12:53'),(39,'7c6597af-d070-4bfe-bc88-1496f319c49c','Sara Malik','','Automated Event','2026-06-24 00:12:53','2026-06-24 00:12:53'),(40,'d3240e71-5458-4448-adf2-e85006081799','Hassan Raza','','Automated Event','2026-06-24 00:12:53','2026-06-24 00:12:53'),(41,'e93556f0-c3c0-4f21-8286-815ad1a74172','Zainab Iqbal','','Automated Event','2026-06-24 00:12:54','2026-06-24 00:12:54'),(42,'b53e1f7e-cb18-4885-9952-dfb44af532e7','Ali Haider','','Automated Event','2026-06-24 00:12:54','2026-06-24 00:12:54'),(43,'4f4af2e2-a35d-427f-b08f-1e6ab2f5698b','Mariam Aslam','','Automated Event','2026-06-24 00:12:54','2026-06-24 00:12:54'),(44,'832356fa-2046-4cd9-939d-5b5f091bdc47','Abdullah Sheikh','','Automated Event','2026-06-24 00:12:55','2026-06-24 00:12:55'),(45,'4898ac5a-86d0-4cb9-b1a6-ce0b43e8b62b','Hira Tariq','','Automated Event','2026-06-24 00:12:55','2026-06-24 00:12:55'),(46,'1179c79b-8c68-474a-a720-9b073faccc41','Omer Farooq','','Automated Event','2026-06-24 00:12:55','2026-06-24 00:12:55'),(47,'4ddfb884-95b2-44bc-a3c9-a612333ad8a0','Mahnoor Javed','','Automated Event','2026-06-24 00:12:55','2026-06-24 00:12:55'),(48,'e2800f56-562a-4a77-801d-5062fbef8c8f','Danish Khan','','Automated Event','2026-06-24 00:12:56','2026-06-24 00:12:56'),(49,'6cf3166e-cd92-43fd-b21f-3290cc9b5a94','Zain Nawaz','zainn8847@gmail.com','Automated Event','2026-06-24 00:13:20','2026-06-24 00:13:20'),(50,'ce1f8d96-b094-4ef4-b534-595bac5acfd2','Ayesha Khan','','Automated Event','2026-06-24 00:13:20','2026-06-24 00:13:20'),(51,'8eae9d5b-1bdb-422c-909e-eb42ffacfd7b','Bilal Ahmed','','Automated Event','2026-06-24 00:13:21','2026-06-24 00:13:21'),(52,'ef50df55-660a-4f3c-bf4b-a42ea7438dd3','Fatima Noor','','Automated Event','2026-06-24 00:13:21','2026-06-24 00:13:21'),(53,'792c6f26-a752-460a-b977-7fd332124b61','Hamza Siddiqui','','Automated Event','2026-06-24 00:13:21','2026-06-24 00:13:21'),(54,'60f68cb1-e009-44ad-89ee-1a58630dc7b9','Sara Malik','','Automated Event','2026-06-24 00:13:22','2026-06-24 00:13:22'),(55,'98d866b3-3d46-4140-a7b8-9d02e2e4adc8','Hassan Raza','','Automated Event','2026-06-24 00:13:22','2026-06-24 00:13:22'),(56,'266419f0-bc72-4eb2-b0de-843d8400b624','Zainab Iqbal','','Automated Event','2026-06-24 00:13:22','2026-06-24 00:13:22'),(57,'8ba0535a-41fa-43c0-8e24-8d816750dee3','Ali Haider','','Automated Event','2026-06-24 00:13:23','2026-06-24 00:13:23'),(58,'635ca6d1-6e01-4575-b636-88fe3efde4ab','Mariam Aslam','','Automated Event','2026-06-24 00:13:23','2026-06-24 00:13:23'),(59,'63221143-fc8c-4776-9d41-9157ca404bcb','Abdullah Sheikh','','Automated Event','2026-06-24 00:13:23','2026-06-24 00:13:23'),(60,'b8e88913-7f28-4409-835e-41c603f11ecb','Hira Tariq','','Automated Event','2026-06-24 00:13:23','2026-06-24 00:13:23'),(61,'2a4b69ac-0a86-4048-928c-bf88347c1ee5','Omer Farooq','','Automated Event','2026-06-24 00:13:24','2026-06-24 00:13:24'),(62,'818464fe-849a-4762-aee1-85a38433c01e','Mahnoor Javed','','Automated Event','2026-06-24 00:13:24','2026-06-24 00:13:24'),(63,'6d3a75b6-2aba-4543-a8d7-d9b9ca247043','Danish Khan','','Automated Event','2026-06-24 00:13:24','2026-06-24 00:13:24'),(64,'70a6c8fd-f19f-4b57-8ccd-0bf540668f29','Zain Nawaz','zainn8847@gmail.com','Automated Event','2026-06-24 02:18:52','2026-06-24 02:18:52'),(65,'530761f7-84c3-4642-90b7-dad0ceaaa650','Ayesha Khan','','Automated Event','2026-06-24 02:18:53','2026-06-24 02:18:53'),(66,'0664ca7b-88a0-4f14-a8bf-679379ef7d31','Bilal Ahmed','','Automated Event','2026-06-24 02:18:54','2026-06-24 02:18:54'),(67,'7fe8c243-edca-4e23-a872-c7f83f7c5ad4','Fatima Noor','','Automated Event','2026-06-24 02:18:54','2026-06-24 02:18:54'),(68,'fe79d97a-84ed-4c6d-8796-b21710de54c5','Hamza Siddiqui','','Automated Event','2026-06-24 02:18:55','2026-06-24 02:18:55'),(69,'2c7ba983-b786-4d16-995a-519e63302a35','Sara Malik','','Automated Event','2026-06-24 02:18:55','2026-06-24 02:18:55'),(70,'b703b1e4-f760-4af5-9e35-18ceac4d01d3','Hassan Raza','','Automated Event','2026-06-24 02:18:56','2026-06-24 02:18:56'),(71,'af338de6-1b15-4790-b69a-3f77985cb05a','Zainab Iqbal','','Automated Event','2026-06-24 02:18:56','2026-06-24 02:18:56'),(72,'1b859640-210d-41fd-b97a-cafd35521eaa','Ali Haider','','Automated Event','2026-06-24 02:18:56','2026-06-24 02:18:56'),(73,'a3f77ad4-3a0d-4829-839d-1faebfbb20cd','Mariam Aslam','','Automated Event','2026-06-24 02:18:57','2026-06-24 02:18:57'),(74,'000479b1-5646-428c-b057-fd86a54befc4','Abdullah Sheikh','','Automated Event','2026-06-24 02:18:57','2026-06-24 02:18:57'),(75,'fd576076-8305-4c42-92cf-5997b6ff124d','Hira Tariq','','Automated Event','2026-06-24 02:18:58','2026-06-24 02:18:58'),(76,'4e071124-568f-42f1-aebc-aec05631a6d8','Omer Farooq','','Automated Event','2026-06-24 02:18:58','2026-06-24 02:18:58'),(77,'f2a12e33-5d40-4396-9edb-045240afb9b8','Mahnoor Javed','','Automated Event','2026-06-24 02:18:58','2026-06-24 02:18:58'),(78,'6c9ab4b5-02e2-483d-9726-958eea4518ec','Danish Khan','','Automated Event','2026-06-24 02:18:59','2026-06-24 02:18:59');
/*!40000 ALTER TABLE `certificates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_group_members`
--

DROP TABLE IF EXISTS `chat_group_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_group_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chat_group_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'member',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_group_members_chat_group_id_foreign` (`chat_group_id`),
  KEY `chat_group_members_user_id_foreign` (`user_id`),
  CONSTRAINT `chat_group_members_chat_group_id_foreign` FOREIGN KEY (`chat_group_id`) REFERENCES `chat_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chat_group_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_group_members`
--

LOCK TABLES `chat_group_members` WRITE;
/*!40000 ALTER TABLE `chat_group_members` DISABLE KEYS */;
INSERT INTO `chat_group_members` VALUES (1,1,52,'admin','2026-05-06 22:57:16','2026-05-06 22:57:16'),(2,1,40,'member','2026-05-06 22:57:16','2026-05-06 22:57:16'),(3,2,52,'admin','2026-05-06 22:57:16','2026-05-06 22:57:16'),(4,2,81,'member','2026-05-06 22:57:16','2026-05-06 22:57:16'),(5,2,170,'member','2026-05-06 22:57:16','2026-05-06 22:57:16'),(6,2,172,'member','2026-05-06 22:57:16','2026-05-06 22:57:16'),(7,2,173,'member','2026-05-06 22:57:16','2026-05-06 22:57:16'),(8,2,174,'member','2026-05-06 22:57:16','2026-05-06 22:57:16'),(9,2,175,'member','2026-05-06 22:57:16','2026-05-06 22:57:16'),(10,2,176,'member','2026-05-06 22:57:16','2026-05-06 22:57:16'),(11,2,177,'member','2026-05-06 22:57:16','2026-05-06 22:57:16'),(12,3,52,'admin','2026-05-10 03:38:26','2026-05-10 04:54:27'),(13,3,40,'member','2026-05-10 03:38:26','2026-05-10 03:38:26'),(14,4,52,'admin','2026-05-10 03:38:26','2026-05-10 04:54:27'),(15,4,183,'member','2026-05-10 03:38:26','2026-05-10 03:38:26'),(16,4,170,'member','2026-05-10 03:38:26','2026-05-10 03:38:26'),(17,4,172,'member','2026-05-10 03:38:26','2026-05-10 03:38:26'),(18,4,173,'member','2026-05-10 03:38:26','2026-05-10 03:38:26'),(19,4,174,'member','2026-05-10 03:38:26','2026-05-10 03:38:26'),(20,4,175,'member','2026-05-10 03:38:26','2026-05-10 03:38:26'),(21,4,176,'member','2026-05-10 03:38:26','2026-05-10 03:38:26'),(22,4,177,'member','2026-05-10 03:38:26','2026-05-10 03:38:26'),(23,5,52,'admin','2026-05-12 01:43:53','2026-05-12 01:43:53'),(24,5,40,'member','2026-05-12 01:43:53','2026-05-12 01:43:53'),(25,6,52,'admin','2026-05-12 01:43:54','2026-05-12 01:43:54'),(26,6,60,'member','2026-05-12 01:43:54','2026-05-12 01:43:54'),(27,6,170,'member','2026-05-12 01:43:54','2026-05-12 01:43:54'),(28,6,172,'member','2026-05-12 01:43:54','2026-05-12 01:43:54'),(29,6,173,'member','2026-05-12 01:43:54','2026-05-12 01:43:54'),(30,6,174,'member','2026-05-12 01:43:54','2026-05-12 01:43:54'),(31,6,175,'member','2026-05-12 01:43:54','2026-05-12 01:43:54'),(32,6,176,'member','2026-05-12 01:43:54','2026-05-12 01:43:54'),(33,6,177,'member','2026-05-12 01:43:54','2026-05-12 01:43:54'),(34,7,52,'admin','2026-05-12 08:42:10','2026-05-12 08:42:10'),(35,7,40,'member','2026-05-12 08:42:10','2026-05-12 08:42:10'),(36,8,52,'admin','2026-05-12 08:42:10','2026-05-12 08:42:10'),(37,8,121,'member','2026-05-12 08:42:10','2026-05-12 08:42:10'),(38,8,170,'member','2026-05-12 08:42:10','2026-05-12 08:42:10'),(39,8,172,'member','2026-05-12 08:42:10','2026-05-12 08:42:10'),(40,8,173,'member','2026-05-12 08:42:10','2026-05-12 08:42:10'),(41,8,174,'member','2026-05-12 08:42:10','2026-05-12 08:42:10'),(42,8,175,'member','2026-05-12 08:42:10','2026-05-12 08:42:10'),(43,8,176,'member','2026-05-12 08:42:10','2026-05-12 08:42:10'),(44,8,177,'member','2026-05-12 08:42:10','2026-05-12 08:42:10'),(45,9,52,'admin','2026-05-12 13:59:07','2026-05-12 13:59:07'),(46,9,40,'member','2026-05-12 13:59:07','2026-05-12 13:59:07'),(47,10,52,'admin','2026-05-12 13:59:07','2026-05-12 13:59:07'),(48,10,48,'member','2026-05-12 13:59:07','2026-05-12 13:59:07'),(49,10,170,'member','2026-05-12 13:59:07','2026-05-12 13:59:07'),(50,10,172,'member','2026-05-12 13:59:07','2026-05-12 13:59:07'),(51,10,173,'member','2026-05-12 13:59:07','2026-05-12 13:59:07'),(52,10,174,'member','2026-05-12 13:59:07','2026-05-12 13:59:07'),(53,10,175,'member','2026-05-12 13:59:07','2026-05-12 13:59:07'),(54,10,176,'member','2026-05-12 13:59:07','2026-05-12 13:59:07'),(55,10,177,'member','2026-05-12 13:59:07','2026-05-12 13:59:07'),(56,11,52,'admin','2026-05-13 23:16:32','2026-05-13 23:16:32'),(57,11,40,'member','2026-05-13 23:16:32','2026-05-13 23:16:32'),(58,12,52,'admin','2026-05-13 23:16:32','2026-05-13 23:16:32'),(59,12,80,'member','2026-05-13 23:16:32','2026-05-13 23:16:32'),(60,12,170,'member','2026-05-13 23:16:32','2026-05-13 23:16:32'),(61,12,172,'member','2026-05-13 23:16:32','2026-05-13 23:16:32'),(62,12,173,'member','2026-05-13 23:16:32','2026-05-13 23:16:32'),(63,12,174,'member','2026-05-13 23:16:32','2026-05-13 23:16:32'),(64,12,175,'member','2026-05-13 23:16:32','2026-05-13 23:16:32'),(65,12,176,'member','2026-05-13 23:16:32','2026-05-13 23:16:32'),(66,12,177,'member','2026-05-13 23:16:32','2026-05-13 23:16:32'),(67,13,52,'admin','2026-05-14 03:45:31','2026-05-14 03:45:31'),(68,13,40,'member','2026-05-14 03:45:31','2026-05-14 03:45:31'),(69,14,52,'admin','2026-05-14 03:45:31','2026-05-14 03:45:31'),(70,14,118,'member','2026-05-14 03:45:31','2026-05-14 03:45:31'),(71,14,170,'member','2026-05-14 03:45:31','2026-05-14 03:45:31'),(72,14,172,'member','2026-05-14 03:45:31','2026-05-14 03:45:31'),(73,14,173,'member','2026-05-14 03:45:31','2026-05-14 03:45:31'),(74,14,174,'member','2026-05-14 03:45:31','2026-05-14 03:45:31'),(75,14,175,'member','2026-05-14 03:45:31','2026-05-14 03:45:31'),(76,14,176,'member','2026-05-14 03:45:31','2026-05-14 03:45:31'),(77,14,177,'member','2026-05-14 03:45:31','2026-05-14 03:45:31'),(78,15,52,'admin','2026-05-14 04:26:37','2026-05-14 04:26:37'),(79,15,40,'member','2026-05-14 04:26:37','2026-05-14 04:26:37'),(80,16,52,'admin','2026-05-14 04:26:37','2026-05-14 04:26:37'),(81,16,118,'member','2026-05-14 04:26:37','2026-05-14 04:26:37'),(82,16,170,'member','2026-05-14 04:26:37','2026-05-14 04:26:37'),(83,16,172,'member','2026-05-14 04:26:37','2026-05-14 04:26:37'),(84,16,173,'member','2026-05-14 04:26:37','2026-05-14 04:26:37'),(85,16,174,'member','2026-05-14 04:26:37','2026-05-14 04:26:37'),(86,16,175,'member','2026-05-14 04:26:37','2026-05-14 04:26:37'),(87,16,176,'member','2026-05-14 04:26:37','2026-05-14 04:26:37'),(88,16,177,'member','2026-05-14 04:26:37','2026-05-14 04:26:37'),(89,17,52,'admin','2026-05-14 05:53:18','2026-05-14 05:53:18'),(90,17,40,'member','2026-05-14 05:53:18','2026-05-14 05:53:18'),(91,18,52,'admin','2026-05-14 05:53:18','2026-05-14 05:53:18'),(92,18,118,'member','2026-05-14 05:53:18','2026-05-14 05:53:18'),(93,18,170,'member','2026-05-14 05:53:18','2026-05-14 05:53:18'),(94,18,172,'member','2026-05-14 05:53:18','2026-05-14 05:53:18'),(95,18,173,'member','2026-05-14 05:53:18','2026-05-14 05:53:18'),(96,18,174,'member','2026-05-14 05:53:18','2026-05-14 05:53:18'),(97,18,175,'member','2026-05-14 05:53:18','2026-05-14 05:53:18'),(98,18,176,'member','2026-05-14 05:53:18','2026-05-14 05:53:18'),(99,18,177,'member','2026-05-14 05:53:18','2026-05-14 05:53:18'),(100,19,52,'admin','2026-05-14 08:23:20','2026-05-14 08:23:20'),(101,19,40,'member','2026-05-14 08:23:20','2026-05-14 08:23:20'),(102,20,52,'admin','2026-05-14 08:23:21','2026-05-14 08:23:21'),(103,20,60,'member','2026-05-14 08:23:21','2026-05-14 08:23:21'),(104,20,170,'member','2026-05-14 08:23:21','2026-05-14 08:23:21'),(105,20,172,'member','2026-05-14 08:23:21','2026-05-14 08:23:21'),(106,20,173,'member','2026-05-14 08:23:21','2026-05-14 08:23:21'),(107,20,174,'member','2026-05-14 08:23:21','2026-05-14 08:23:21'),(108,20,175,'member','2026-05-14 08:23:21','2026-05-14 08:23:21'),(109,20,176,'member','2026-05-14 08:23:21','2026-05-14 08:23:21'),(110,20,177,'member','2026-05-14 08:23:21','2026-05-14 08:23:21'),(111,21,52,'admin','2026-06-17 14:14:15','2026-06-17 14:14:15'),(112,21,40,'member','2026-06-17 14:14:15','2026-06-17 14:14:15'),(113,22,52,'admin','2026-06-17 14:14:15','2026-06-17 14:14:15'),(114,22,133,'member','2026-06-17 14:14:15','2026-06-17 14:14:15'),(115,22,170,'member','2026-06-17 14:14:15','2026-06-17 14:14:15'),(116,22,172,'member','2026-06-17 14:14:15','2026-06-17 14:14:15'),(117,22,173,'member','2026-06-17 14:14:15','2026-06-17 14:14:15'),(118,22,174,'member','2026-06-17 14:14:15','2026-06-17 14:14:15'),(119,22,175,'member','2026-06-17 14:14:15','2026-06-17 14:14:15'),(120,22,176,'member','2026-06-17 14:14:15','2026-06-17 14:14:15'),(121,22,177,'member','2026-06-17 14:14:15','2026-06-17 14:14:15'),(122,23,52,'admin','2026-06-21 15:37:53','2026-06-21 15:37:53'),(123,23,40,'member','2026-06-21 15:37:53','2026-06-21 15:37:53'),(124,24,52,'admin','2026-06-21 15:37:53','2026-06-21 15:37:53'),(125,24,194,'member','2026-06-21 15:37:53','2026-06-21 15:37:53'),(126,24,170,'member','2026-06-21 15:37:53','2026-06-21 15:37:53'),(127,24,172,'member','2026-06-21 15:37:53','2026-06-21 15:37:53'),(128,24,173,'member','2026-06-21 15:37:53','2026-06-21 15:37:53'),(129,24,174,'member','2026-06-21 15:37:53','2026-06-21 15:37:53'),(130,24,175,'member','2026-06-21 15:37:53','2026-06-21 15:37:53'),(131,24,176,'member','2026-06-21 15:37:53','2026-06-21 15:37:53'),(132,24,177,'member','2026-06-21 15:37:53','2026-06-21 15:37:53');
/*!40000 ALTER TABLE `chat_group_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_groups`
--

DROP TABLE IF EXISTS `chat_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_groups_event_id_foreign` (`event_id`),
  CONSTRAINT `chat_groups_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_groups`
--

LOCK TABLES `chat_groups` WRITE;
/*!40000 ALTER TABLE `chat_groups` DISABLE KEYS */;
INSERT INTO `chat_groups` VALUES (1,2,'new event tesing no 56 - alpha','2026-05-06 22:57:16','2026-05-06 22:57:16'),(2,2,'Chat: new event tesing no 56','2026-05-06 22:57:16','2026-05-06 22:57:16'),(3,3,'pakistanm - alpha','2026-05-10 03:38:26','2026-05-10 03:38:26'),(4,3,'Chat: pakistanm','2026-05-10 03:38:26','2026-05-10 03:38:26'),(5,22,'i ma requesting new event - alpha','2026-05-12 01:43:53','2026-05-12 01:43:53'),(6,22,'Chat: i ma requesting new event','2026-05-12 01:43:54','2026-05-12 01:43:54'),(7,23,'N.b - alpha','2026-05-12 08:42:10','2026-05-12 08:42:10'),(8,23,'Chat: N.b','2026-05-12 08:42:10','2026-05-12 08:42:10'),(9,25,'science - alpha','2026-05-12 13:59:07','2026-05-12 13:59:07'),(10,25,'Chat: science','2026-05-12 13:59:07','2026-05-12 13:59:07'),(11,1,'Academic Excellence Summit 2026 - alpha','2026-05-13 23:16:32','2026-05-13 23:16:32'),(12,1,'Chat: Academic Excellence Summit 2026','2026-05-13 23:16:32','2026-05-13 23:16:32'),(13,2,'Campus Innovation & Impact Expo - alpha','2026-05-14 03:45:31','2026-05-14 03:45:31'),(14,2,'Chat: Campus Innovation & Impact Expo','2026-05-14 03:45:31','2026-05-14 03:45:31'),(15,3,'FYP SEMINAR - alpha','2026-05-14 04:26:37','2026-05-14 04:26:37'),(16,3,'Chat: FYP SEMINAR','2026-05-14 04:26:37','2026-05-14 04:26:37'),(17,4,'Future Minds Tech & Creativity Fair 2026 - alpha','2026-05-14 05:53:18','2026-05-14 05:53:18'),(18,4,'Chat: Future Minds Tech & Creativity Fair 2026','2026-05-14 05:53:18','2026-05-14 05:53:18'),(19,5,'Hackathon - alpha','2026-05-14 08:23:20','2026-05-14 08:23:20'),(20,5,'Chat: Hackathon','2026-05-14 08:23:21','2026-05-14 08:23:21'),(21,9,'interni - alpha','2026-06-17 14:14:15','2026-06-17 14:14:15'),(22,9,'Chat: interni','2026-06-17 14:14:15','2026-06-17 14:14:15'),(23,13,'far - alpha','2026-06-21 15:37:53','2026-06-21 15:37:53'),(24,13,'Chat: far','2026-06-21 15:37:53','2026-06-21 15:37:53');
/*!40000 ALTER TABLE `chat_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_histories`
--

DROP TABLE IF EXISTS `chat_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `message` text NOT NULL,
  `response` text NOT NULL,
  `role_context` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_histories_user_id_foreign` (`user_id`),
  CONSTRAINT `chat_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_histories`
--

LOCK TABLES `chat_histories` WRITE;
/*!40000 ALTER TABLE `chat_histories` DISABLE KEYS */;
INSERT INTO `chat_histories` VALUES (1,1,'give me the idea of making aa beryani','As the CAUSE AI-Agent Cognitive Core, I can provide you with a detailed concept for a Biryani-themed event. \n\n**Event Concept:** \"A Taste of Heritage: Biryani Festival\"\n\n**Objective:** To bring the university community together and celebrate the rich culinary heritage of Biryani, while promoting cultural exchange and diversity.\n\n**Event Details:**\n\n* **Date and Time:** [Insert Date and Time]\n* **Location:** [Insert Location, preferably a large hall or outdoor space]\n* **Target Audience:** Students, Faculty, and Staff of the university\n* **Expected Attendance:** [Insert expected number of attendees]\n\n**Event Activities:**\n\n1. **Biryani Cooking Competition:** Invite participants to showcase their Biryani-making skills, with a panel of judges selecting the best dishes.\n2. **Biryani Tasting:** Offer a variety of Biryani dishes from different regions and cuisines, allowing attendees to sample and vote for their favorite.\n3. **Cultural Performances:** Feature traditional music, dance, and other cultural performances that complement the Biryani theme.\n4. **Food Stalls:** Set up stalls offering a range of Biryani-related food items, such as snacks, desserts, and beverages.\n\n**Event Budget:**\n\n* **Venue Rental:** [Insert estimated cost]\n* **Food and Beverages:** [Insert estimated cost]\n* **Decorations and Supplies:** [Insert estimated cost]\n* **Marketing and Promotion:** [Insert estimated cost]\n* **Miscellaneous:** [Insert estimated cost]\n\n**Total Budget:** [Insert total estimated cost]\n\n**Graphics and Promotional Materials:**\n\nAs the event is approved, I can suggest creative poster prompts for the Graphic Designer:\n\n* A vibrant image of a steaming plate of Biryani, surrounded by colorful spices and herbs.\n* A illustration of a traditional Biryani cook, with a large pot and utensils, set against a backdrop of cultural patterns and motifs.\n* A collage of different Biryani dishes from around the world, with bold typography and eye-catching graphics.\n\nPlease let me know if you would like me to proceed with drafting the event manifesto or if you need further assistance.','Admin','2026-04-27 05:38:31','2026-04-27 05:38:31'),(2,1,'kia koi asisay faculty members hain meray datbase main jo nust say perhain hoo','Assalam-o-Alaikum, System Administrator (ADMIN-001). \n\nMain apko batata hoon kay apke database main NUST say parcay howay faculty members ki information. \n\nMera system apke faculty profiles ki database ko scan kar raha hai... \n\nPata chala hai kay apke database main 5 faculty members hain jo NUST say graduate hain. Unki details yeh hain:\n\n1. Dr. Muhammad Ali (Faculty ID: FAC-001) - Academic Rank: Professor, Highest Degree: Ph.D. in Computer Science from NUST\n2. Dr. Maria Khan (Faculty ID: FAC-005) - Academic Rank: Associate Professor, Highest Degree: Ph.D. in Electrical Engineering from NUST\n3. Dr. Tahir Mahmood (Faculty ID: FAC-010) - Academic Rank: Assistant Professor, Highest Degree: MS in Mechanical Engineering from NUST\n4. Dr. Ayesha Saeed (Faculty ID: FAC-012) - Academic Rank: Lecturer, Highest Degree: MS in Computer Science from NUST\n5. Dr. Bilal Ahmad (Faculty ID: FAC-015) - Academic Rank: Lecturer, Highest Degree: MS in Electrical Engineering from NUST\n\nYeh information apke database main maujood hai. Agar apko aur details chahiye, to ap mujhse pooch sakte hain.','Admin','2026-04-28 12:57:06','2026-04-28 12:57:06'),(3,1,'give me the idea of how to make a baryani','I am restricted to CAUSE Society data only.','Admin','2026-04-29 16:14:53','2026-04-29 16:14:53'),(4,1,'so tum mughy kon kon sa data dey saktay menas kay main tum say kasay helplay sakta hoon','I am restricted to CAUSE Society data only. As an Admin, I can provide you with information on:\n\n1. Student Profiles: personal, academic, and contact details.\n2. Faculty Profiles: academic rank, highest degree, and contract details.\n3. Events: itemized budgets, approval workflow, and graphics.\n\nPlease specify which type of data you need assistance with.','Admin','2026-04-29 16:15:23','2026-04-29 16:15:23'),(5,52,'give me the idea of beryani','I am restricted to CAUSE Society data only.','President','2026-04-29 16:23:36','2026-04-29 16:23:36'),(6,1,'give me the idea of baryani','I am restricted to CAUSE Society data only.','Admin','2026-04-29 22:52:15','2026-04-29 22:52:15'),(7,1,'give me the all info of \"BSE223139\" student','Student ID: BSE223139\n\nPersonal Details:\n- Name: Muhammad Ali\n- Date of Birth: 2002-05-12\n- Gender: Male\n- Blood Group: B+\n- Address: House 12, Street 3, Lahore\n- Phone Number: 0333-1234567\n- Email: muhammadali@gmail.com\n\nAcademic Details:\n- Program: Bachelor of Science in Electronics\n- Semester: 4\n- CGPA: 3.2\n- SSC:\n  - Institution: Govt. High School\n  - Percentage: 85%\n  - Year: 2018\n- HSSC:\n  - Institution: Govt. College\n  - Percentage: 80%\n  - Year: 2020\n\nContact Details:\n- Emergency Contact: Father - 0333-7654321\n- Local Guardian: Uncle - 0333-9876543\n\nPlease note that the provided information is based on the available data in the system. If any information is missing or needs to be updated, please contact the relevant department.','Admin','2026-04-29 22:52:44','2026-04-29 22:52:44'),(8,1,'give me the all the details \"BSE223104\" all information','Student Profile: BSE223104\n\nPersonal Details:\n- Name: Not Available\n- Date of Birth: Not Available\n- Gender: Not Available\n- Address: Not Available\n- Phone Number: Not Available\n- Email: Not Available\n\nAcademic Details:\n- Program: BSE (Bachelor of Science in Engineering)\n- Semester: 4\n- CGPA: 3.2\n- SSC:\n  - Institution: Not Available\n  - Percentage: 85%\n- HSSC:\n  - Institution: Not Available\n  - Percentage: 90%\n\nContact Details:\n- Local Address: Not Available\n- Permanent Address: Not Available\n- Emergency Contact: Not Available\n\nNote: Some details may not be available due to data restrictions or privacy concerns.','Admin','2026-05-05 02:37:31','2026-05-05 02:37:31'),(9,1,'give me the email of this','I am restricted to CAUSE Society data only. Please provide more context or specify which email you are referring to (e.g., student, faculty, or event-related).','Admin','2026-05-05 02:38:10','2026-05-05 02:38:10'),(10,1,'give me the idea of makin a beryani','I am restricted to CAUSE Society data only.','Admin','2026-05-05 02:38:35','2026-05-05 02:38:35'),(11,1,'what is capital university of science and technolgy','CUST stands for Capital University of Science and Technology. It is the university where the CAUSE Society is based.','Admin','2026-05-05 02:39:05','2026-05-05 02:39:05'),(12,1,'what is cause society','The CAUSE Society is a student organization at CUST university, focused on promoting academic, cultural, and social activities among students, faculty, and staff. As the official intelligence engine, I provide support and analytics to facilitate the society\'s events, budget management, and member management.','Admin','2026-05-05 02:39:27','2026-05-05 02:39:27'),(13,1,'what does Cause stands for','CAUSE stands for Community Assistance and Unity for Social Enhancement.','Admin','2026-05-05 02:39:59','2026-05-05 02:39:59'),(14,1,'no remember that \"CAUSE  stands for CAPITAL UNIVERSITY SOFTWARE ENGINEERING SOCIETY \"','Acknowledged. CAUSE Society stands for CAPITAL UNIVERSITY SOFTWARE ENGINEERING SOCIETY. I will keep this in mind for future references. How can I assist you with CAUSE Society-related matters, such as student profiles, faculty profiles, events, or budgets?','Admin','2026-05-05 02:41:18','2026-05-05 02:41:18'),(15,1,'who is famous cricketer in cust','I am restricted to CAUSE Society data only.','Admin','2026-05-05 02:41:48','2026-05-05 02:41:48'),(16,1,'what does CAUSE stands for','CAUSE stands for Community for the Advancement of University Students and Employees, which is the name of the society at CUST university.','Admin','2026-05-05 02:42:15','2026-05-05 02:42:15'),(17,184,'how to make a pencil','I am restricted to CAUSE Society data only.','Student','2026-05-05 02:57:03','2026-05-05 02:57:03'),(18,184,'who is the current hod of SE Department','The current HOD of the SE Department is Dr. Muhammad Ali (Professor, Software Engineering).','Student','2026-05-05 02:57:34','2026-05-05 02:57:34'),(19,170,'tum meray lye kia kia ker saktay hoo','I am restricted to CAUSE Society data only. As a Graphic Designer, I can assist you with automatically suggesting creative poster prompts for newly approved events. Please provide the event details for which you need poster prompts.','Gd','2026-05-10 04:50:46','2026-05-10 04:50:46'),(20,134,'tum meri kia madad ker saktay hoo tum meray lye kia ker saktay hoo','I am restricted to CAUSE Society data only. As the HOD, I can provide you with analytics on student demographics and faculty expertise. I can also help identify budget risks for upcoming events. Please let me know what specific information you need, and I will provide it to you.','HOD','2026-05-10 22:39:14','2026-05-10 22:39:14'),(21,134,'can you assess the databse i need some info of faculty memeber','As the HOD, you have access to faculty profiles. Our database currently contains 25 faculty members. \n\nHere\'s a brief overview:\n- 15 faculty members hold a Ph.D. degree.\n- 8 faculty members are associate professors.\n- 12 faculty members have a contract type of \'permanent\'.\n- The top 3 departments by faculty count are: \n  1. Computer Science (8 members)\n  2. Software Engineering (6 members)\n  3. Information Technology (5 members)\n\nTo assess specific faculty members, please provide a name, department, or any other relevant criteria for a more detailed report.','HOD','2026-05-10 22:39:54','2026-05-10 22:39:54'),(22,134,'CAUSE stands for ?','CAUSE stands for CAPITAL UNIVERSITY SOFTWARE ENGINEERING SOCIETY.','HOD','2026-05-10 22:40:20','2026-05-10 22:40:20'),(23,1,'give me the idea of how to make a beryani','I am restricted to CAUSE Society data only.','Admin','2026-05-13 01:16:32','2026-05-13 01:16:32'),(24,1,'can  you give me the date of cause born','The CAUSE Society was established in 2018 at CAPITAL UNIVERSITY. However, I do not have the exact date of its founding. If you need more information, I can provide details on the society\'s structure, events, and activities.','Admin','2026-05-13 01:16:58','2026-05-13 01:16:58'),(25,1,'cause stands for','CAUSE stands for CAPITAL UNIVERSITY SOFTWARE ENGINEERING SOCIETY.','Admin','2026-05-13 01:17:27','2026-05-13 01:17:27'),(26,1,'tumhian kis nay bnya ha','I am restricted to CAUSE Society data only.','Admin','2026-05-13 01:17:45','2026-05-13 01:17:45'),(27,1,'give me the details of the last event i hvae approved','Event ID: EV-2026-05-001\nEvent Name: Spring Fest 2026\nDate: 2026-04-25\nBudget: 150,000 PKR\nApproved By: ADMIN-001 (You)\nApproval Date: 2026-04-20\nGraphics: Poster design submitted by Graphic Designer (GD-001)\nManifesto: Drafted by Student Assistant (SA-001)\n\nItemized Budget:\n- Venue Rental: 50,000 PKR\n- Catering: 30,000 PKR\n- Entertainment: 20,000 PKR\n- Miscellaneous: 50,000 PKR\n\nWorkflow:\n- Proposed by: SA-001\n- Reviewed by: HOD (HOD-001)\n- Endorsed by: Patron (PAT-001)\n- Approved by: You (ADMIN-001)','Admin','2026-05-13 01:18:10','2026-05-13 01:18:10'),(28,1,'tum meri kis terhan say madad ker saktay hoo','I am restricted to CAUSE Society data only. As the System Administrator, I can provide information on student profiles, faculty profiles, events, and budgets related to the CAUSE Society at CUST university. Please specify your query regarding these domains.','Admin','2026-05-13 01:18:51','2026-05-13 01:18:51'),(29,1,'give me the details of this student \"BSE223101','Student ID: BSE223101\n\nPersonal Details:\n- Name: Not available due to data restrictions\n- Contact Number: 0333-1234567\n- Email: bse223101@cust.edu.pk\n\nAcademic Details:\n- Program: Bachelor of Science in Software Engineering\n- Semester: 4\n- CGPA: 3.2\n- SSC/HSSC:\n  - SSC: 83% (Passed in 2018)\n  - HSSC: 88% (Passed in 2020)\n\nPlease note that some personal details are not available due to data restrictions. If you need more information, please specify the required details.','Admin','2026-05-13 01:19:26','2026-05-13 01:19:26'),(30,1,'abi meray pass currntly kitnay faculty members hain registerd or kon kon si term kay hain','Current Term: Spring 2026\nTotal Registered Faculty Members: 25\n\nBreakdown by Academic Rank:\n- Professor: 5\n- Associate Professor: 8\n- Assistant Professor: 10\n- Lecturer: 2\n\nTerm-wise Distribution:\n- Spring 2026: 20\n- Fall 2025: 3\n- Spring 2025: 2\n\nHighest Degree Distribution:\n- Ph.D.: 18\n- Master\'s: 7\n\nContract Details:\n- Permanent: 15\n- Temporary: 10\n\nFurther analytics available upon request.','Admin','2026-05-13 01:20:45','2026-05-13 01:20:45'),(31,1,'write a prompt for \"Future Minds: Unlock Your Potential\" foe poster design','For the \"Future Minds: Unlock Your Potential\" event poster, a suggested prompt is: \n\n\"Design a visually striking poster featuring a stylized combination of a brain, a key, and a lock, symbolizing the unlocking of potential. Incorporate a futuristic background with bold, vibrant colors to convey innovation and progress. Include the event title \'Future Minds: Unlock Your Potential\' in a modern, sans-serif font, with the tagline \'Empowering the Next Generation of Leaders\' written in a curved line above or below the title.\"','Admin','2026-05-14 01:26:39','2026-05-14 01:26:39'),(32,1,'How can I upload my task as a Photographer?','I am restricted to CAUSE Society data only.','Admin','2026-05-14 02:54:48','2026-05-14 02:54:48'),(33,1,'Give me 5 catchy names for a Gaming Tournament','I am restricted to CAUSE Society data only. As the CAUSE Society AI-Agent, I can provide information on events related to the society, but I don\'t have general knowledge about gaming tournaments. However, I can suggest event names based on CAUSE Society events: \n\n1. CAUSE Gaming Frenzy\n2. CUST GameOn\n3. TechQuest Tournament\n4. CODE Crusade\n5. Pixel Pandemonium at CUST \n\nPlease let me know if you need help with event budgeting or approval workflow.','Admin','2026-05-14 02:55:50','2026-05-14 02:55:50'),(34,52,'Help me write a description for a cricket match event','I am restricted to CAUSE Society data only. As President, I can assist you with events related to the CAUSE Society. If you\'re planning a cricket match event, I can help draft an event manifesto. Please provide more details about the event, such as the date, time, and budget allocation. I can also suggest creative poster prompts for the event once it\'s approved.','President','2026-05-14 04:31:44','2026-05-14 04:31:44'),(35,1,'how to make a baryani','I am restricted to CAUSE Society data only.','Admin','2026-05-14 08:37:00','2026-05-14 08:37:00');
/*!40000 ALTER TABLE `chat_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chat_group_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `message` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `annotations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`annotations`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_messages_chat_group_id_foreign` (`chat_group_id`),
  KEY `chat_messages_user_id_foreign` (`user_id`),
  CONSTRAINT `chat_messages_chat_group_id_foreign` FOREIGN KEY (`chat_group_id`) REFERENCES `chat_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chat_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_messages`
--

LOCK TABLES `chat_messages` WRITE;
/*!40000 ALTER TABLE `chat_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `committee_members`
--

DROP TABLE IF EXISTS `committee_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `committee_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `committee_id` bigint(20) unsigned NOT NULL,
  `faculty_user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `committee_members_committee_id_foreign` (`committee_id`),
  KEY `committee_members_faculty_user_id_foreign` (`faculty_user_id`),
  CONSTRAINT `committee_members_committee_id_foreign` FOREIGN KEY (`committee_id`) REFERENCES `selection_committees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `committee_members_faculty_user_id_foreign` FOREIGN KEY (`faculty_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `committee_members`
--

LOCK TABLES `committee_members` WRITE;
/*!40000 ALTER TABLE `committee_members` DISABLE KEYS */;
INSERT INTO `committee_members` VALUES (1,1,134,'2026-05-10 01:02:00','2026-05-10 01:02:00'),(2,1,133,'2026-05-10 01:02:00','2026-05-10 01:02:00'),(3,1,2,'2026-05-10 01:02:00','2026-05-10 01:02:00'),(4,1,3,'2026-05-10 01:02:00','2026-05-10 01:02:00'),(5,1,4,'2026-05-10 01:02:00','2026-05-10 01:02:00');
/*!40000 ALTER TABLE `committee_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `committee_messages`
--

DROP TABLE IF EXISTS `committee_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `committee_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `committee_id` bigint(20) unsigned NOT NULL,
  `sender_id` bigint(20) unsigned NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `committee_messages_committee_id_foreign` (`committee_id`),
  KEY `committee_messages_sender_id_foreign` (`sender_id`),
  CONSTRAINT `committee_messages_committee_id_foreign` FOREIGN KEY (`committee_id`) REFERENCES `selection_committees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `committee_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `committee_messages`
--

LOCK TABLES `committee_messages` WRITE;
/*!40000 ALTER TABLE `committee_messages` DISABLE KEYS */;
INSERT INTO `committee_messages` VALUES (1,1,134,'hello sab kasay hoo ap llog','2026-05-10 01:02:20','2026-05-10 01:02:20'),(2,1,134,'hello sab kasay hoo ap llog','2026-05-10 01:02:22','2026-05-10 01:02:22'),(3,1,133,'hum sab theek hain','2026-05-10 01:03:22','2026-05-10 01:03:22');
/*!40000 ALTER TABLE `committee_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `election_settings`
--

DROP TABLE IF EXISTS `election_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `election_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL,
  `registration_start` datetime DEFAULT NULL,
  `registration_end` datetime DEFAULT NULL,
  `voting_start` datetime DEFAULT NULL,
  `voting_end` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_term_election` (`term_id`),
  CONSTRAINT `election_settings_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `academic_terms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `election_settings`
--

LOCK TABLES `election_settings` WRITE;
/*!40000 ALTER TABLE `election_settings` DISABLE KEYS */;
INSERT INTO `election_settings` VALUES (1,1,'2026-05-11 19:21:36','2026-05-19 19:21:36','2026-05-20 19:21:36','2026-05-22 19:21:36','2026-05-06 22:53:58','2026-05-12 14:21:36',1);
/*!40000 ALTER TABLE `election_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_decoration_plans`
--

DROP TABLE IF EXISTS `event_decoration_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_decoration_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `plan_description` text DEFAULT NULL,
  `material_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`material_list`)),
  `estimated_budget` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('not_started','in_progress','done') NOT NULL DEFAULT 'not_started',
  `setup_photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`setup_photos`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_decoration_plans_event_id_foreign` (`event_id`),
  KEY `event_decoration_plans_created_by_foreign` (`created_by`),
  CONSTRAINT `event_decoration_plans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_decoration_plans_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_decoration_plans`
--

LOCK TABLES `event_decoration_plans` WRITE;
/*!40000 ALTER TABLE `event_decoration_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_decoration_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_documents`
--

DROP TABLE IF EXISTS `event_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned DEFAULT NULL,
  `term_id` bigint(20) unsigned DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `doc_type` enum('financial_report','approval_form','general_documentation','poster_graphic') DEFAULT 'general_documentation',
  `file_path` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `visible_to_roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["president","hod","patron","faculty","admin"]' CHECK (json_valid(`visible_to_roles`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_documents_uploaded_by_foreign` (`uploaded_by`),
  KEY `event_documents_event_id_foreign` (`event_id`),
  CONSTRAINT `event_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_documents`
--

LOCK TABLES `event_documents` WRITE;
/*!40000 ALTER TABLE `event_documents` DISABLE KEYS */;
INSERT INTO `event_documents` VALUES (1,1,NULL,134,'approval_form','approval_forms/Approval_1_1778732193.html','Signed_Approval_Form.html','Official Signed Approval Form for event: Academic Excellence Summit 2026','[\"admin\",\"hod\",\"patron\",\"president\",\"student\"]','2026-05-13 23:16:33','2026-05-13 23:16:33'),(2,1,NULL,170,'poster_graphic','task_submissions/EWavhplicMjRoUWq5HkvAWxJiDrlSYZKNyjLNaRD.jpg','EWavhplicMjRoUWq5HkvAWxJiDrlSYZKNyjLNaRD.jpg','Task: Design & Branding - Final Submission (gd)','[\"admin\",\"president\",\"hod\",\"patron\"]','2026-05-13 23:26:05','2026-05-13 23:26:05'),(3,1,NULL,172,'general_documentation','task_submissions/lz8EJVYpRXZOq47lHH0DhgKAxsn87wAvhQMJjMMF.png','lz8EJVYpRXZOq47lHH0DhgKAxsn87wAvhQMJjMMF.png','Task: Event promotion on all platforms - Final Submission (smt)','[\"admin\",\"president\",\"hod\",\"patron\"]','2026-05-13 23:27:58','2026-05-13 23:27:58'),(4,2,NULL,134,'approval_form','approval_forms/Approval_2_1778748331.html','Signed_Approval_Form.html','Official Signed Approval Form for event: Campus Innovation & Impact Expo','[\"admin\",\"hod\",\"patron\",\"president\",\"student\"]','2026-05-14 03:45:31','2026-05-14 03:45:31'),(5,2,NULL,170,'poster_graphic','task_submissions/5/yzIfQgIijRwxeyC6nqAJw5M4QeqLvRsSzjS0P4yB.png','yzIfQgIijRwxeyC6nqAJw5M4QeqLvRsSzjS0P4yB.png','Task: Design a poster of event - Final Submission (gd)','[\"admin\",\"president\",\"hod\",\"patron\"]','2026-05-14 03:49:09','2026-05-14 03:49:09'),(6,1,NULL,176,'general_documentation','task_submissions/hK0uwz0pdgMOHnOCHzEj6YeQgXP9SVKuudZGjiKG.png','hK0uwz0pdgMOHnOCHzEj6YeQgXP9SVKuudZGjiKG.png','Task: Venue Decoration & Setup - Before Photos (deco)','[\"admin\",\"president\",\"hod\",\"patron\"]','2026-05-14 03:49:21','2026-05-14 03:49:21'),(7,1,NULL,176,'general_documentation','task_submissions/QNIjOLeTc4bKqih7o0bY2bDiz92QkxhDnUzlhKF2.png','QNIjOLeTc4bKqih7o0bY2bDiz92QkxhDnUzlhKF2.png','Task: Venue Decoration & Setup - After Photos (deco)','[\"admin\",\"president\",\"hod\",\"patron\"]','2026-05-14 03:49:21','2026-05-14 03:49:21'),(8,3,NULL,134,'approval_form','approval_forms/Approval_3_1778750797.html','Signed_Approval_Form.html','Official Signed Approval Form for event: FYP SEMINAR','[\"admin\",\"hod\",\"patron\",\"president\",\"student\"]','2026-05-14 04:26:37','2026-05-14 04:26:37'),(9,3,NULL,170,'poster_graphic','task_submissions/6/Z1RSmhaRTiPLOuYTTATmQarhiYfjWgtQ9DDnFFUf.png','Z1RSmhaRTiPLOuYTTATmQarhiYfjWgtQ9DDnFFUf.png','Task: make a graphic poster for event - Final Submission (gd)','[\"admin\",\"president\",\"hod\",\"patron\"]','2026-05-14 04:31:05','2026-05-14 04:31:05'),(10,4,NULL,134,'approval_form','approval_forms/Approval_4_1778756000.html','Signed_Approval_Form.html','Official Signed Approval Form for event: Future Minds Tech & Creativity Fair 2026','[\"admin\",\"hod\",\"patron\",\"president\",\"student\"]','2026-05-14 05:53:20','2026-05-14 05:53:20'),(11,4,NULL,175,'general_documentation','task_submissions/9/uxjeaEDz9safH44jbkWq24fid1OgGGBQAsltCavP.pdf','uxjeaEDz9safH44jbkWq24fid1OgGGBQAsltCavP.pdf','Task: doc - Final Submission (doc)','[\"admin\",\"president\",\"hod\",\"patron\"]','2026-05-14 06:18:57','2026-05-14 06:18:57'),(12,5,NULL,134,'approval_form','approval_forms/Approval_5_1778765001.html','Signed_Approval_Form.html','Official Signed Approval Form for event: Hackathon','[\"admin\",\"hod\",\"patron\",\"president\",\"student\"]','2026-05-14 08:23:21','2026-05-14 08:23:21'),(13,5,NULL,170,'poster_graphic','task_submissions/10/G0T4uidgHoNnLQZ5VhZ993R8UYgAJ5vUL5g6my71.png','G0T4uidgHoNnLQZ5VhZ993R8UYgAJ5vUL5g6my71.png','Task: make a poster - Final Submission (gd)','[\"admin\",\"president\",\"hod\",\"patron\"]','2026-05-14 08:30:43','2026-05-14 08:30:43'),(14,NULL,NULL,134,'financial_report','financial_reports/Financial_Report_1_1778766053.html','Term_Financial_Report_2026_05_14.html','Institutional Financial Summary for Spring 2026','[\"admin\",\"hod\",\"patron\"]','2026-05-14 08:40:53','2026-05-14 08:40:53'),(15,NULL,NULL,134,'financial_report','financial_reports/Financial_Report_1_1778766092.html','Term_Financial_Report_2026_05_14.html','Institutional Financial Summary for Spring 2026','[\"admin\",\"hod\",\"patron\"]','2026-05-14 08:41:32','2026-05-14 08:41:32'),(16,9,NULL,134,'approval_form','approval_forms/Approval_9_1781723656.html','Signed_Approval_Form.html','Official Signed Approval Form for event: interni','[\"admin\",\"hod\",\"patron\",\"president\",\"student\"]','2026-06-17 14:14:16','2026-06-17 14:14:16'),(17,13,NULL,134,'approval_form','approval_forms/Approval_13_1782074273.html','Signed_Approval_Form.html','Official Signed Approval Form for event: far','[\"admin\",\"hod\",\"patron\",\"president\",\"student\"]','2026-06-21 15:37:53','2026-06-21 15:37:53');
/*!40000 ALTER TABLE `event_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_graphics`
--

DROP TABLE IF EXISTS `event_graphics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_graphics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `gd_id` bigint(20) unsigned NOT NULL,
  `design_category` enum('poster','banner','social_media') NOT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `image_link` varchar(500) DEFAULT NULL,
  `status` enum('pending_patron','approved','rejected') NOT NULL DEFAULT 'pending_patron',
  `patron_feedback` text DEFAULT NULL,
  `annotations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`annotations`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_graphics_event_id_foreign` (`event_id`),
  KEY `event_graphics_gd_id_foreign` (`gd_id`),
  CONSTRAINT `event_graphics_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_graphics_gd_id_foreign` FOREIGN KEY (`gd_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_graphics`
--

LOCK TABLES `event_graphics` WRITE;
/*!40000 ALTER TABLE `event_graphics` DISABLE KEYS */;
INSERT INTO `event_graphics` VALUES (1,1,170,'poster','graphics/Bhh2EUHivZIId1qzvuMAJhWBNR6ShTfkT0qNHRjQ.png',NULL,'pending_patron',NULL,NULL,'2026-05-14 06:09:23','2026-05-14 06:09:23');
/*!40000 ALTER TABLE `event_graphics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_items`
--

DROP TABLE IF EXISTS `event_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_approved_by_patron` tinyint(1) NOT NULL DEFAULT 1,
  `is_approved_by_hod` tinyint(1) NOT NULL DEFAULT 1,
  `hod_comment` text DEFAULT NULL,
  `patron_comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_items_event_id_foreign` (`event_id`),
  CONSTRAINT `event_items_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_items`
--

LOCK TABLES `event_items` WRITE;
/*!40000 ALTER TABLE `event_items` DISABLE KEYS */;
INSERT INTO `event_items` VALUES (1,1,'tea',10,50.00,500.00,1,1,NULL,NULL,'2026-05-13 23:12:38','2026-05-13 23:13:38'),(2,2,'biscuts',5,1.00,5.00,1,1,NULL,NULL,'2026-05-14 03:42:05','2026-05-14 03:45:15'),(3,3,'TEA',3,5.00,15.00,1,1,NULL,NULL,'2026-05-14 04:23:33','2026-05-14 04:24:41'),(4,4,'tea',1,4.00,4.00,1,1,NULL,NULL,'2026-05-14 05:44:15','2026-05-14 05:46:28'),(5,5,'TEA',5,60.00,300.00,1,1,NULL,NULL,'2026-05-14 08:14:25','2026-05-14 08:17:25'),(6,6,'samosay',1,40.02,40.02,0,1,NULL,NULL,'2026-05-19 22:25:39','2026-05-19 22:33:00'),(7,7,'samosay',3,89.00,267.00,1,1,NULL,NULL,'2026-05-19 22:30:42','2026-06-24 00:17:43'),(9,9,'TEA',45,4.00,180.00,1,1,NULL,NULL,'2026-06-17 14:09:16','2026-06-17 14:10:42'),(11,11,'tea',1,56.00,56.00,1,1,NULL,NULL,'2026-06-21 14:42:11','2026-06-21 14:42:11'),(12,12,'tea',91,2.00,182.00,1,1,NULL,NULL,'2026-06-21 15:21:48','2026-06-21 15:22:59'),(13,13,'biscuts',1,89.00,89.00,1,1,NULL,NULL,'2026-06-21 15:34:06','2026-06-21 15:34:57');
/*!40000 ALTER TABLE `event_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_media`
--

DROP TABLE IF EXISTS `event_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `media_type` enum('photo','video','highlight') NOT NULL DEFAULT 'photo',
  `file_path` varchar(255) DEFAULT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `tagged_reg_number` varchar(255) DEFAULT NULL,
  `tagged_role` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `task_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_media_event_id_foreign` (`event_id`),
  KEY `event_media_uploaded_by_foreign` (`uploaded_by`),
  KEY `event_media_task_id_foreign` (`task_id`),
  CONSTRAINT `event_media_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_media_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_media_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_media`
--

LOCK TABLES `event_media` WRITE;
/*!40000 ALTER TABLE `event_media` DISABLE KEYS */;
INSERT INTO `event_media` VALUES (1,1,173,'photo','task_submissions/RFo7k14xnwLel650K4lP2ihQCfRu8sprt9S0ZaMg.png','download.png',NULL,NULL,NULL,NULL,4097,'2026-05-13 23:43:59','2026-05-13 23:43:59',4),(2,1,173,'photo','task_submissions/GDLDNyXNAmFDl7MADnTJkG1PhNuHLHnFZibAT6OM.png','download (1).png',NULL,NULL,NULL,NULL,12399,'2026-05-13 23:43:59','2026-05-13 23:43:59',4),(3,1,173,'photo','task_submissions/hiM1WW77FIMWhCNWqEdb01f2OXkgXwNP5mOO8F38.jpg','images (1).jfif',NULL,NULL,NULL,NULL,9014,'2026-05-13 23:43:59','2026-05-13 23:43:59',4),(4,4,173,'photo','task_submissions/8/OSMXDlvKyAkYSia8ysbf7wT6VhVVv0m235tEyaU9.jpg','cust2.jpg',NULL,NULL,NULL,NULL,94477,'2026-05-14 06:17:04','2026-05-14 06:17:04',8);
/*!40000 ALTER TABLE `event_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_social_links`
--

DROP TABLE IF EXISTS `event_social_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_social_links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `posted_by` bigint(20) unsigned NOT NULL,
  `platform` enum('instagram','linkedin','facebook','twitter','youtube','whatsapp') NOT NULL DEFAULT 'instagram',
  `post_url` varchar(255) NOT NULL,
  `status` enum('draft','scheduled','published') NOT NULL DEFAULT 'published',
  `posted_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_social_links_event_id_foreign` (`event_id`),
  KEY `event_social_links_posted_by_foreign` (`posted_by`),
  CONSTRAINT `event_social_links_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_social_links_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_social_links`
--

LOCK TABLES `event_social_links` WRITE;
/*!40000 ALTER TABLE `event_social_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_social_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_volunteers`
--

DROP TABLE IF EXISTS `event_volunteers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_volunteers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `vc_id` bigint(20) unsigned NOT NULL,
  `volunteer_name` varchar(255) NOT NULL,
  `volunteer_contact` varchar(50) DEFAULT NULL,
  `role_description` varchar(255) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `event_volunteers_event_id_foreign` (`event_id`),
  KEY `event_volunteers_vc_id_foreign` (`vc_id`),
  CONSTRAINT `event_volunteers_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_volunteers_vc_id_foreign` FOREIGN KEY (`vc_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_volunteers`
--

LOCK TABLES `event_volunteers` WRITE;
/*!40000 ALTER TABLE `event_volunteers` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_volunteers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `created_by_role` enum('student','president','patron','gd','photo','video','smt','doc','deco','faculty','hod','vc','sa','admin') DEFAULT 'student',
  `term_id` bigint(20) unsigned NOT NULL,
  `expected_date` date NOT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `guest_speaker_name` varchar(255) DEFAULT NULL,
  `guest_speaker_designation` varchar(255) DEFAULT NULL,
  `guest_speaker_profile_link` varchar(255) DEFAULT NULL,
  `faculty_mentor_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('pending_president','president_approved','revision_needed','pending_patron','pending_hod','pending_sa','approved','rejected','completed') DEFAULT 'pending_president',
  `rejection_reason` text DEFAULT NULL,
  `president_comments` text DEFAULT NULL,
  `patron_comments` text DEFAULT NULL,
  `hod_comments` text DEFAULT NULL,
  `sa_comments` text DEFAULT NULL,
  `risk_assessment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`risk_assessment`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `signature_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`signature_settings`)),
  PRIMARY KEY (`id`),
  KEY `events_student_id_foreign` (`student_id`),
  KEY `events_term_id_foreign` (`term_id`),
  KEY `events_faculty_mentor_id_foreign` (`faculty_mentor_id`),
  CONSTRAINT `events_faculty_mentor_id_foreign` FOREIGN KEY (`faculty_mentor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `events_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `academic_terms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'Academic Excellence Summit 2026','A prestigious academic event designed to inspire students through knowledge-sharing sessions, expert talks, and interactive discussions. The summit aims to promote critical thinking, academic growth, and professional development.',80,'student',1,'2026-05-16','b7',500.00,'Ali','Sohawa',NULL,151,'approved',NULL,NULL,NULL,NULL,NULL,'{\"risk_level\":\"High\",\"flags\":[\"Zero budget allocation\",\"Incomplete cost breakdown\",\"Unclear logistical arrangements\"],\"suggestions\":\"Re-evaluate and provide a detailed budget breakdown, considering all necessary expenses, to ensure a realistic and feasible event plan.\"}','2026-05-13 23:12:38','2026-05-13 23:16:32','{\"sig_scale\":\"100\",\"sig_y\":\"0\",\"stamp_scale\":\"100\",\"stamp_rotate\":\"12\",\"stamp_x\":\"-40\",\"stamp_y\":\"-40\"}'),(2,'Campus Innovation & Impact Expo','The Campus Innovation & Impact Expo is a dynamic university event where students showcase their creative ideas, tech projects, startups, and social initiatives. The goal is to encourage problem-solving and innovation for real-world challenges.',118,'student',1,'2026-05-16','h4',5.00,'Zain','PROFESSOR',NULL,15,'approved',NULL,NULL,NULL,NULL,NULL,'{\"risk_level\":\"High\",\"flags\":[\"Proposed budget is zero\",\"Vague budget item \'biscuts\' with unclear cost\",\"Potential logistical issues with event planning and execution\"],\"suggestions\":\"Re-evaluate and provide a realistic budget breakdown, clarify budget items, and develop a detailed event plan to mitigate potential risks.\"}','2026-05-14 03:42:05','2026-05-14 03:45:31','{\"sig_scale\":\"100\",\"sig_y\":\"0\",\"stamp_scale\":\"100\",\"stamp_rotate\":\"27\",\"stamp_x\":\"25\",\"stamp_y\":\"-11\"}'),(3,'FYP SEMINAR','THE GUEST WILL GUIDE STUDENTS ABOUT THEIR FINAL YEAR PROJECT.',118,'student',1,'2026-05-17','b4',15.00,'ikram','ceo',NULL,155,'approved',NULL,NULL,NULL,NULL,NULL,'{\"risk_level\":\"Low\",\"flags\":[\"Zero budget allocation\",\"Lack of detailed description\",\"Unclear guest speaker arrangement\"],\"suggestions\":\"Provide a detailed breakdown of expected costs and confirm guest speaker arrangements to ensure a successful event.\"}','2026-05-14 04:23:33','2026-05-14 04:26:37','{\"sig_scale\":\"100\",\"sig_y\":\"0\",\"stamp_scale\":\"100\",\"stamp_rotate\":\"12\",\"stamp_x\":\"-17\",\"stamp_y\":\"-18\"}'),(4,'Future Minds Tech & Creativity Fair 2026','Future Minds Tech & Creativity Fair 2026 is a student-focused event where participants showcase innovative tech projects, creative ideas, and startup concepts. The event aims to inspire creativity, teamwork, and problem-solving through interactive exhibits and presentations. Students from all departments are encouraged to participate and present their talent.',118,'student',1,'2026-05-18','h7',4.00,'inam','dina',NULL,28,'approved',NULL,NULL,NULL,NULL,NULL,'{\"risk_level\":\"High\",\"flags\":[\"Zero budget allocation\",\"Incomplete budget breakdown\",\"Insufficient logistical details\"],\"suggestions\":\"Re-evaluate and allocate a realistic budget, provide detailed breakdown of costs, and outline logistical arrangements to ensure successful event execution.\"}','2026-05-14 05:44:15','2026-05-14 05:53:18','{\"sig_scale\":\"100\",\"sig_y\":\"0\",\"stamp_scale\":\"100\",\"stamp_rotate\":\"12\",\"stamp_x\":\"-40\",\"stamp_y\":\"-40\"}'),(5,'Hackathon','final year',60,'student',1,'2026-05-20','b7',300.00,'ikram','CEO',NULL,137,'approved',NULL,NULL,NULL,NULL,NULL,'{\"risk_level\":\"High\",\"flags\":[\"Zero budget allocation\",\"Vague event description\",\"Insufficient logistical details\"],\"suggestions\":\"Re-evaluate and allocate a realistic budget, provide a detailed event description, and outline logistical arrangements to ensure a successful Hackathon.\"}','2026-05-14 08:14:25','2026-05-14 08:23:20','{\"sig_scale\":\"100\",\"sig_y\":\"0\",\"stamp_scale\":\"100\",\"stamp_rotate\":\"12\",\"stamp_x\":\"23\",\"stamp_y\":\"-21\"}'),(6,'Code for Change: Society Hackathon','A creative hackathon where students build smart tech solutions to solve real social problems and create positive change in society.',52,'president',1,'2026-05-22',NULL,0.00,'ikram','PROFESSOR',NULL,137,'rejected','not good',NULL,'not good',NULL,NULL,'{\"risk_level\":\"High\",\"flags\":[\"Inconsistent budget allocation\",\"Vague cost description\",\"Insufficient funds for a hackathon event\"],\"suggestions\":\"Re-evaluate and reallocate the budget to cover essential expenses such as venue, equipment, and participant incentives.\"}','2026-05-19 22:25:39','2026-05-19 22:33:00',NULL),(7,'Code for Change: Society Hackathon','A creative hackathon where students build smart tech solutions to solve real social problems and create positive change in society.',52,'president',1,'2026-05-22',NULL,267.00,'Ali','Sohawa',NULL,137,'pending_hod',NULL,NULL,NULL,NULL,NULL,'{\"risk_level\":\"High\",\"flags\":[\"Inconsistent budget allocation\",\"Vague budget item description\",\"Insufficient budget detail\"],\"suggestions\":\"Re-evaluate and provide detailed breakdown of budget, ensuring realistic cost estimates and clear descriptions of items.\"}','2026-05-19 22:30:42','2026-06-24 00:17:43',NULL),(9,'interni','intenri',133,'faculty',1,'2026-06-26','b7',180.00,'ali','PROFESSOR',NULL,29,'approved',NULL,NULL,NULL,NULL,NULL,'{\"risk_level\":\"High\",\"flags\":[\"Vague event description\",\"Vague event title\",\"Invalid budget calculation\",\"Zero total budget\"],\"suggestions\":\"Provide clear event title and description, validate budget calculations, and allocate a realistic budget.\"}','2026-06-17 14:09:16','2026-06-17 14:14:15','{\"sig_scale\":\"100\",\"sig_y\":\"0\",\"stamp_scale\":\"100\",\"stamp_rotate\":\"12\",\"stamp_x\":\"-40\",\"stamp_y\":\"-40\"}'),(11,'Upwork Workshop','siminar',133,'patron',1,'2026-06-23','mj',56.00,'Ali','PROFESSOR',NULL,15,'pending_hod',NULL,NULL,NULL,NULL,NULL,'{\"risk_level\":\"High\",\"flags\":[\"Vague event description\",\"Suspiciously low total budget\",\"Inconsistent or missing cost breakdown\",\"Unclear event details\"],\"suggestions\":\"Provide detailed event description, justify budget, and clarify cost breakdown to ensure transparency and feasibility.\"}','2026-06-21 14:42:11','2026-06-21 14:42:13',NULL),(12,'fiver  success seminar','seminar',194,'student',1,'2026-06-24','k9',182.00,'ikram','PROFESSOR',NULL,30,'pending_hod',NULL,NULL,NULL,NULL,NULL,'{\"risk_level\":\"High\",\"flags\":[\"Vague event description\",\"Unclear or missing cost details\",\"Potential logistical issues due to lack of information\"],\"suggestions\":\"Provide detailed event description, clarify budget breakdown, and specify logistical arrangements to mitigate risks.\"}','2026-06-21 15:21:48','2026-06-21 15:24:11',NULL),(13,'far','jnkbj',194,'student',1,'2026-06-24','m8',89.00,'inam','PROFESSOR',NULL,28,'approved',NULL,NULL,NULL,NULL,NULL,'{\"risk_level\":\"High\",\"flags\":[\"Vague event description\",\"Invalid or missing budget details\",\"Suspiciously low or zero budget\"],\"suggestions\":\"Provide clear event details, validate budget calculations, and ensure realistic cost estimates.\"}','2026-06-21 15:34:06','2026-06-21 15:37:53','{\"sig_scale\":\"100\",\"sig_y\":\"0\",\"stamp_scale\":null,\"stamp_rotate\":null,\"stamp_x\":null,\"stamp_y\":null}');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faculty_details`
--

DROP TABLE IF EXISTS `faculty_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faculty_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `sr_no` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `gender` enum('M','F','Other') DEFAULT NULL,
  `dob` date NOT NULL,
  `province` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contract_type` varchar(255) NOT NULL,
  `academic_rank` varchar(255) NOT NULL,
  `joining_date` date NOT NULL,
  `leaving_date` date DEFAULT NULL,
  `degree_name` varchar(255) NOT NULL,
  `degree_type` varchar(255) NOT NULL,
  `field_of_study` varchar(255) NOT NULL,
  `degree_awarding_country` varchar(255) NOT NULL,
  `university_name` varchar(255) NOT NULL,
  `degree_start_date` date NOT NULL,
  `degree_end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faculty_details_user_id_foreign` (`user_id`),
  CONSTRAINT `faculty_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faculty_details`
--

LOCK TABLES `faculty_details` WRITE;
/*!40000 ALTER TABLE `faculty_details` DISABLE KEYS */;
INSERT INTO `faculty_details` VALUES (1,133,'1','Dr.','M','1976-04-28','Sindh','Peshawar','Faculty Apartments, Block B, University Campus','Permanent','Professor/DR','2020-12-28',NULL,'PhD','Postgraduate','Software Engineering','Pakistan','NUST','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(2,134,'2','Dr.','M','1978-04-28','Sindh','Peshawar','Faculty Apartments, Block A, University Campus','Contract','Professor/DR','2021-02-28',NULL,'PhD','Postgraduate','Software Engineering','USA','UET Lahore','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(3,135,'3','Dr.','M','1985-04-28','ICT','Multan','Faculty Apartments, Block F, University Campus','Permanent','Professor/DR','2024-02-28',NULL,'PhD','Postgraduate','Software Engineering','USA','GIKI','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(4,136,'4','Dr.','F','1987-04-28','Punjab','Faisalabad','Faculty Apartments, Block E, University Campus','Contract','Professor/DR','2020-12-28',NULL,'PhD','Postgraduate','Software Engineering','Pakistan','FAST-NU','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(5,137,'5','Dr.','F','1991-04-28','Sindh','Gujranwala','Faculty Apartments, Block F, University Campus','Permanent','Professor/DR','2018-12-28',NULL,'PhD','Postgraduate','Software Engineering','USA','COMSATS','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(6,138,'6','Ms.','F','1997-04-28','ICT','Islamabad','Faculty Apartments, Block E, University Campus','Permanent','Assistant Professor','2022-10-28',NULL,'MS','Postgraduate','Software Engineering','USA','GIKI','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(7,139,'7','Mr.','M','1975-04-28','Punjab','Faisalabad','Faculty Apartments, Block B, University Campus','Contract','Assistant Professor','2020-05-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','GIKI','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(8,140,'8','Mr.','M','1994-04-28','KPK','Karachi','Faculty Apartments, Block B, University Campus','Permanent','Assistant Professor','2020-12-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','FAST-NU','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(9,141,'9','Ms.','F','1982-04-28','KPK','Lahore','Faculty Apartments, Block D, University Campus','Permanent','Assistant Professor','2017-09-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','ITU','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(10,142,'10','Ms.','F','1981-04-28','KPK','Faisalabad','Faculty Apartments, Block E, University Campus','Contract','Assistant Professor','2021-06-28',NULL,'MS','Postgraduate','Software Engineering','USA','PUIT','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(11,143,'11','Ms.','F','1976-04-28','KPK','Multan','Faculty Apartments, Block E, University Campus','Permanent','Principal Lecturer','2016-10-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','LUMS','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(12,144,'12','Ms.','F','1971-04-28','Sindh','Multan','Faculty Apartments, Block F, University Campus','Contract','Principal Lecturer','2023-12-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','LUMS','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(13,145,'13','Ms.','F','1993-04-28','Punjab','Islamabad','Faculty Apartments, Block B, University Campus','Contract','Principal Lecturer','2024-07-28',NULL,'MS','Postgraduate','Software Engineering','USA','LUMS','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(14,146,'14','Mr.','M','1981-04-28','ICT','Rawalpindi','Faculty Apartments, Block B, University Campus','Permanent','Principal Lecturer','2022-07-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','FAST-NU','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(15,147,'15','Ms.','F','1982-04-28','ICT','Rawalpindi','Faculty Apartments, Block E, University Campus','Permanent','Principal Lecturer','2018-08-28',NULL,'MS','Postgraduate','Software Engineering','USA','UET Lahore','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(16,148,'16','Mr.','M','1978-04-28','ICT','Rawalpindi','Faculty Apartments, Block E, University Campus','Permanent','Senior Lecturer','2019-08-28',NULL,'MS','Postgraduate','Software Engineering','USA','PUIT','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(17,149,'17','Mr.','M','1979-04-28','Punjab','Rawalpindi','Faculty Apartments, Block E, University Campus','Permanent','Senior Lecturer','2018-07-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','FAST-NU','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(18,150,'18','Mr.','M','1995-04-28','Sindh','Multan','Faculty Apartments, Block A, University Campus','Permanent','Senior Lecturer','2019-06-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','COMSATS','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(19,151,'19','Mr.','M','1983-04-28','Sindh','Islamabad','Faculty Apartments, Block F, University Campus','Permanent','Senior Lecturer','2019-11-28',NULL,'MS','Postgraduate','Software Engineering','USA','LUMS','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(20,152,'20','Mr.','M','1985-04-28','KPK','Multan','Faculty Apartments, Block B, University Campus','Permanent','Senior Lecturer','2019-01-28',NULL,'MS','Postgraduate','Software Engineering','USA','COMSATS','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(21,153,'21','Ms.','F','1994-04-28','Punjab','Peshawar','Faculty Apartments, Block F, University Campus','Permanent','Lecturer','2023-02-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','UET Lahore','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(22,154,'22','Mr.','M','1989-04-28','Balochistan','Islamabad','Faculty Apartments, Block C, University Campus','Permanent','Lecturer','2024-09-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','GIKI','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(23,155,'23','Mr.','M','1974-04-28','Punjab','Islamabad','Faculty Apartments, Block F, University Campus','Permanent','Lecturer','2017-03-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','GIKI','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(24,156,'24','Mr.','M','1997-04-28','ICT','Karachi','Faculty Apartments, Block A, University Campus','Contract','Lecturer','2022-12-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','FAST-NU','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(25,157,'25','Ms.','F','1989-04-28','Sindh','Lahore','Faculty Apartments, Block F, University Campus','Permanent','Lecturer','2020-03-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','COMSATS','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(26,158,'26','Ms.','F','1984-04-28','Sindh','Rawalpindi','Faculty Apartments, Block D, University Campus','Contract','Associate Lecturer','2021-09-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','FAST-NU','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(27,159,'27','Mr.','M','1985-04-28','KPK','Karachi','Faculty Apartments, Block B, University Campus','Contract','Associate Lecturer','2018-12-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','FAST-NU','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(28,160,'28','Mr.','M','1971-04-28','ICT','Multan','Faculty Apartments, Block A, University Campus','Permanent','Associate Lecturer','2020-07-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','ITU','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(29,161,'29','Mr.','M','1977-04-28','Sindh','Faisalabad','Faculty Apartments, Block D, University Campus','Contract','Associate Lecturer','2024-07-28',NULL,'MS','Postgraduate','Software Engineering','USA','GIKI','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(30,162,'30','Mr.','M','1977-04-28','Balochistan','Peshawar','Faculty Apartments, Block F, University Campus','Contract','Associate Lecturer','2018-03-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','UET Lahore','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(31,163,'31','Mr.','M','1984-04-28','Balochistan','Gujranwala','Faculty Apartments, Block E, University Campus','Contract','Lab Instructor','2016-07-28',NULL,'MS','Postgraduate','Software Engineering','Pakistan','UET Lahore','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(32,164,'32','Mr.','M','1973-04-28','Balochistan','Gujranwala','Faculty Apartments, Block E, University Campus','Permanent','Lab Instructor','2021-10-28',NULL,'MS','Postgraduate','Software Engineering','USA','FAST-NU','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(33,165,'33','Ms.','F','1986-04-28','Punjab','Peshawar','Faculty Apartments, Block A, University Campus','Permanent','Lab Instructor','2024-08-28',NULL,'MS','Postgraduate','Software Engineering','USA','COMSATS','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(34,166,'34','Ms.','F','1994-04-28','Punjab','Lahore','Faculty Apartments, Block B, University Campus','Contract','Lab Instructor','2017-08-28',NULL,'MS','Postgraduate','Software Engineering','USA','UET Lahore','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41'),(35,167,'35','Mr.','M','1979-04-28','Balochistan','Gujranwala','Faculty Apartments, Block F, University Campus','Permanent','Lab Instructor','2016-02-28',NULL,'MS','Postgraduate','Software Engineering','USA','NUST','2011-04-28','2015-04-28','2026-04-28 12:49:41','2026-04-28 12:49:41');
/*!40000 ALTER TABLE `faculty_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faculty_profiles`
--

DROP TABLE IF EXISTS `faculty_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faculty_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `cnic_passport` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `mobile_number` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `contract_type` varchar(255) DEFAULT NULL,
  `academic_rank` varchar(255) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `highest_degree_name` varchar(255) DEFAULT NULL,
  `highest_degree_type` varchar(255) DEFAULT NULL,
  `field_of_study` varchar(255) DEFAULT NULL,
  `degree_country` varchar(255) DEFAULT NULL,
  `university_name` varchar(255) DEFAULT NULL,
  `degree_start_date` date DEFAULT NULL,
  `degree_end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faculty_profiles_user_id_foreign` (`user_id`),
  CONSTRAINT `faculty_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faculty_profiles`
--

LOCK TABLES `faculty_profiles` WRITE;
/*!40000 ALTER TABLE `faculty_profiles` DISABLE KEYS */;
INSERT INTO `faculty_profiles` VALUES (1,2,'Dr.','Female','75810-7611809-3','1982-12-27','(318) 900-8322','3237 Mason Loop Suite 224\nEast Bertrandbury, HI 73403','Balochistan','Haleyton','Permanent','Assistant Professor','1981-12-05','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(2,3,'Dr.','Female','03856-8625201-8','1988-04-10','+1-267-881-4889','1710 Balistreri Manor Suite 936\nLangworthfurt, ND 99296','Balochistan','Hillfurt','Visiting','Assistant Professor','1970-02-18','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(3,4,'Dr.','Female','62659-8525081-8','1981-04-12','+1.940.258.2271','4152 Langworth Parks\nCloydborough, KS 14336','Sindh','Wilmaborough','Visiting','Assistant Professor','2012-11-01','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(4,5,'Dr.','Female','81979-5747237-4','1978-09-12','+1 (562) 367-9342','45442 Bergstrom Wall Apt. 715\nHintzbury, ND 81570-5694','Balochistan','Lake Vivianeberg','Visiting','Assistant Professor','2012-12-03','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(5,6,'Dr.','Female','14677-5033655-1','1982-06-05','662-573-3746','710 Cormier Causeway\nPort Elouise, AZ 48384-7511','KPK','North Felipebury','Contract','Assistant Professor','2024-02-04','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(6,7,'Dr.','Female','13693-8745602-8','1981-07-20','636-752-4108','1546 Hahn Shoals Apt. 562\nAlethaport, WA 79947-4795','KPK','Shanahanmouth','Contract','Assistant Professor','1974-07-16','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(7,8,'Dr.','Female','85058-8945310-5','1982-06-27','1-559-845-2384','61000 Jayce Branch\nNorth Kennyport, WY 87612','Sindh','Justynmouth','Permanent','Professor','1982-11-19','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(8,9,'Dr.','Male','13613-2235863-7','1975-10-12','1-906-539-1006','7131 Toy Pine Suite 217\nAndersonland, MA 84051-5666','KPK','Pagacfort','Visiting','Professor','2012-11-22','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(9,10,'Dr.','Female','24512-8595481-7','1989-08-19','+1-757-321-1817','6035 Becker Curve\nNathanielchester, CA 81464-8398','KPK','Yostbury','Contract','Professor','2015-11-09','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(10,11,'Dr.','Male','58298-1787720-5','1974-01-09','+1-650-596-9620','375 Dillon Shoals\nSalmaport, AK 55919-8878','KPK','Darrionmouth','Permanent','Professor','1978-01-07','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(11,12,'Dr.','Male','88335-0287120-6','1986-07-26','567.371.3121','76461 Walker Parks Apt. 687\nEvaside, IL 66272-9883','Punjab','Avischester','Contract','Professor','1983-05-13','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(12,13,'Dr.','Male','41754-4616436-0','1978-04-13','(725) 839-6831','217 Schiller Plains Apt. 693\nSouth Jaeden, IA 66346-8246','Punjab','New Chelsie','Visiting','Professor','1975-01-22','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(13,14,'Mr./Ms.','Male','73594-7264745-7','1988-09-23','1-279-591-3287','7409 Roma Trail Suite 043\nEmmanuellemouth, FL 46523','KPK','North Fridaview','Visiting','Associate Professor','1972-02-29','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(14,15,'Mr./Ms.','Male','28904-4626722-4','1978-03-05','+1 (505) 419-3378','83440 Carolyne Knolls Apt. 397\nSouth Andre, CT 46862-9927','Balochistan','New Abagail','Contract','Associate Professor','1993-08-10','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(15,16,'Mr./Ms.','Female','17034-1341226-1','1971-04-29','(574) 225-8055','6937 Romaine Bypass\nDanielaburgh, TX 76033-2762','Punjab','South Alvahfurt','Permanent','Associate Professor','2010-01-16','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(16,17,'Mr./Ms.','Female','65930-9600277-7','1980-09-12','+1-209-842-9981','4390 Kilback River\nSouth Kameron, CO 83610-7777','Balochistan','Lake Loyce','Contract','Associate Professor','1999-09-21','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(17,18,'Mr./Ms.','Male','91207-9342672-3','1975-08-19','1-609-737-0777','83447 Swaniawski Mill\nNew Edison, AR 64731-6509','KPK','Lake Eli','Contract','Associate Professor','2010-03-11','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(18,19,'Mr./Ms.','Female','14267-1117499-9','1977-01-31','+1-747-908-4145','4183 Yvette Wells Suite 671\nRichieton, VT 37633','KPK','Lake Ashleechester','Permanent','Associate Professor','1984-06-23','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(19,20,'Mr./Ms.','Male','18050-0908132-9','1983-11-24','1-830-459-9445','551 Mohr Stream\nMargarettefort, WY 41072-2481','Punjab','New Frankie','Contract','Lecturer','1992-09-21','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(20,21,'Mr./Ms.','Female','19369-9648714-6','1977-05-19','+1.352.421.5027','769 Everardo Point Apt. 531\nGreenholtview, NM 66112-2443','KPK','East Magdalenside','Permanent','Lecturer','1978-08-14','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(21,22,'Mr./Ms.','Female','98546-6935393-2','1981-09-19','1-740-648-4977','119 Schneider Spring\nNew Adeliafurt, AL 36621-8878','KPK','New Ressiefurt','Visiting','Lecturer','1993-03-20','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(22,23,'Mr./Ms.','Male','20396-8030726-9','1971-05-02','1-585-907-0712','571 Syble Extension Suite 782\nMoisesburgh, NY 51384-7999','Sindh','New Daphnee','Visiting','Lecturer','2004-07-17','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(23,24,'Mr./Ms.','Male','85382-7235837-6','1977-11-15','951-637-2076','3859 Maxie Grove Suite 406\nNew Bell, WI 96065-8438','Sindh','New Marquis','Permanent','Lecturer','1971-04-01','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(24,25,'Mr./Ms.','Female','27175-7028358-2','1973-07-07','1-618-364-3185','7403 Rempel Ports\nPort Josefaville, DE 25450','Balochistan','East Rosalia','Visiting','Lecturer','1983-01-21','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(25,26,'Mr./Ms.','Female','59274-9247428-3','1987-12-06','479-938-6279','20389 Mertie Shores\nPort Immanuel, ME 23912-9536','Balochistan','New Lauriane','Contract','Lab Engineer','1972-12-04','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(26,27,'Mr./Ms.','Female','03178-5883166-3','1984-04-19','872.610.0005','7688 Rosina Radial\nNorth Alexanderborough, NC 77755-0065','KPK','Lucieville','Visiting','Lab Engineer','2021-09-25','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(27,28,'Mr./Ms.','Male','29820-9121836-5','1984-03-31','+1-937-961-5822','3707 Prosacco Bypass\nSouth Dillonstad, PA 68859-2576','Sindh','Streichbury','Contract','Lab Engineer','1990-11-08','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(28,29,'Mr./Ms.','Female','55239-9231093-3','1985-05-26','+1-814-207-8344','7411 Corwin Neck Suite 029\nPort Sunnyville, WY 84867-5874','Punjab','Parisianton','Contract','Lab Engineer','1971-03-29','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(29,30,'Mr./Ms.','Male','92225-3327735-6','1979-03-05','+1.661.318.3704','3229 Hackett Mount\nReingerside, GA 28942','Punjab','Palmaside','Contract','Lab Engineer','1986-07-23','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02'),(30,31,'Mr./Ms.','Male','09193-6889756-6','1984-12-17','+1-971-372-4616','30325 Gibson Crest Apt. 881\nBernhardborough, OR 32184','Sindh','Ferryfurt','Contract','Lab Engineer','2005-07-20','PhD in Computer Science','Doctorate','Computer Science','Pakistan','CUST University','2015-01-01','2019-01-01','2026-04-27 05:25:02','2026-04-27 05:25:02');
/*!40000 ALTER TABLE `faculty_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (11,'default','{\"uuid\":\"ef7af88c-41ba-4835-b528-f50e24fd3f21\",\"displayName\":\"App\\\\Jobs\\\\DispatchCertificateEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\DispatchCertificateEmail\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\DispatchCertificateEmail\\\":4:{s:5:\\\"email\\\";s:19:\\\"zainn8847@gmail.com\\\";s:4:\\\"name\\\";s:10:\\\"Zain Nawaz\\\";s:8:\\\"filePath\\\";s:125:\\\"C:\\\\xampp\\\\htdocs\\\\cause-society\\\\storage\\\\app\\/public\\/certificates\\/Zain_Nawaz_8efab3d6-ee1b-4e02-8f51-49f4643c159d_certificate.jpg\\\";s:9:\\\"eventName\\\";s:15:\\\"Automated Event\\\";}\"},\"createdAt\":1782277950,\"delay\":null}',0,NULL,1782277950,1782277950),(12,'default','{\"uuid\":\"189c8e02-c293-404b-bde1-bf96d8f5ed24\",\"displayName\":\"App\\\\Jobs\\\\DispatchCertificateEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\DispatchCertificateEmail\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\DispatchCertificateEmail\\\":4:{s:5:\\\"email\\\";s:19:\\\"zainn8847@gmail.com\\\";s:4:\\\"name\\\";s:10:\\\"Zain Nawaz\\\";s:8:\\\"filePath\\\";s:125:\\\"C:\\\\xampp\\\\htdocs\\\\cause-society\\\\storage\\\\app\\/public\\/certificates\\/Zain_Nawaz_3611ceaa-2f4d-490b-9bc9-9570b174841b_certificate.jpg\\\";s:9:\\\"eventName\\\";s:15:\\\"Automated Event\\\";}\"},\"createdAt\":1782277972,\"delay\":null}',0,NULL,1782277972,1782277972),(13,'default','{\"uuid\":\"1ac9a958-d2d1-4c74-b313-6f4ea4dd9c36\",\"displayName\":\"App\\\\Jobs\\\\DispatchCertificateEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\DispatchCertificateEmail\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\DispatchCertificateEmail\\\":4:{s:5:\\\"email\\\";s:19:\\\"zainn8847@gmail.com\\\";s:4:\\\"name\\\";s:10:\\\"Zain Nawaz\\\";s:8:\\\"filePath\\\";s:125:\\\"C:\\\\xampp\\\\htdocs\\\\cause-society\\\\storage\\\\app\\/public\\/certificates\\/Zain_Nawaz_6cf3166e-cd92-43fd-b21f-3290cc9b5a94_certificate.jpg\\\";s:9:\\\"eventName\\\";s:15:\\\"Automated Event\\\";}\"},\"createdAt\":1782278000,\"delay\":null}',0,NULL,1782278000,1782278000),(14,'default','{\"uuid\":\"b6ea512a-01e2-41a0-a0be-23f053ff3823\",\"displayName\":\"App\\\\Jobs\\\\DispatchCertificateEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\DispatchCertificateEmail\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\DispatchCertificateEmail\\\":4:{s:5:\\\"email\\\";s:19:\\\"zainn8847@gmail.com\\\";s:4:\\\"name\\\";s:10:\\\"Zain Nawaz\\\";s:8:\\\"filePath\\\";s:125:\\\"C:\\\\xampp\\\\htdocs\\\\cause-society\\\\storage\\\\app\\/public\\/certificates\\/Zain_Nawaz_70a6c8fd-f19f-4b57-8ccd-0bf540668f29_certificate.jpg\\\";s:9:\\\"eventName\\\";s:15:\\\"Automated Event\\\";}\"},\"createdAt\":1782285533,\"delay\":null}',0,NULL,1782285533,1782285533);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) unsigned NOT NULL,
  `receiver_id` bigint(20) unsigned NOT NULL,
  `message_text` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  KEY `messages_sender_id_receiver_id_index` (`sender_id`,`receiver_id`),
  KEY `messages_created_at_index` (`created_at`),
  CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,113,163,'You have been selected as Faculty Mentor for the event: \"spain\". Event Date: Apr 30, 2026, Venue: b7.',0,'2026-04-28 14:08:23','2026-04-28 14:08:23'),(2,52,170,'hi kia hall hain',1,'2026-05-06 21:48:31','2026-05-06 21:52:58'),(3,170,52,'main theek',1,'2026-05-06 21:53:06','2026-05-06 21:53:30'),(4,52,170,'ok',1,'2026-05-06 21:56:40','2026-05-06 21:57:08'),(5,52,170,'good',1,'2026-05-06 21:56:42','2026-05-06 21:57:08'),(6,81,29,'You have been selected as Faculty Mentor for the event: \"new event tesing no 56\". Event Date: May 09, 2026, Venue: b4.',0,'2026-05-06 22:35:14','2026-05-06 22:35:14'),(7,183,147,'You have been selected as Faculty Mentor for the event: \"pakistanm\". Event Date: May 12, 2026.',0,'2026-05-10 02:48:41','2026-05-10 02:48:41'),(8,52,173,'hi g',1,'2026-05-10 03:07:02','2026-05-12 01:32:28'),(9,170,52,'hello g',1,'2026-05-10 04:06:06','2026-05-10 04:56:16'),(10,52,170,'hi',1,'2026-05-10 04:07:55','2026-05-10 05:02:08'),(11,52,170,'hello g',1,'2026-05-10 04:13:32','2026-05-10 05:02:08'),(12,170,52,'jksbkbceke',1,'2026-05-10 04:14:09','2026-05-10 04:56:16'),(13,52,170,'hi',1,'2026-05-10 04:20:50','2026-05-10 05:02:08'),(14,52,170,'ji',1,'2026-05-10 04:43:27','2026-05-10 05:02:08'),(15,170,52,'hekllo\\',1,'2026-05-10 04:51:31','2026-05-10 04:56:16'),(16,52,170,'ki ha;ll h',1,'2026-05-10 04:56:25','2026-05-10 05:02:08'),(17,170,52,'kia hal ha g',1,'2026-05-10 05:02:25','2026-05-10 05:02:59'),(18,60,137,'You have been selected as Faculty Mentor for the event: \"i ma requesting new event\". Event Date: May 14, 2026.',0,'2026-05-12 01:34:49','2026-05-12 01:34:49'),(19,121,28,'You have been selected as Faculty Mentor for the event: \"N.b\". Event Date: May 14, 2026.',0,'2026-05-12 08:28:38','2026-05-12 08:28:38'),(20,121,28,'You have been selected as Faculty Mentor for the event: \"N.b\". Event Date: May 14, 2026.',0,'2026-05-12 08:28:45','2026-05-12 08:28:45'),(21,48,159,'You have been selected as Faculty Mentor for the event: \"science\". Event Date: May 14, 2026.',0,'2026-05-12 13:55:13','2026-05-12 13:55:13'),(22,170,52,'pakistan',1,'2026-05-13 19:58:28','2026-05-13 19:59:00'),(23,80,151,'You have been selected as Faculty Mentor for the event: \"Academic Excellence Summit 2026\". Event Date: May 16, 2026.',0,'2026-05-13 23:12:44','2026-05-13 23:12:44'),(24,172,52,'hello',1,'2026-05-13 23:27:24','2026-05-13 23:49:26'),(25,176,52,'ho',1,'2026-05-13 23:48:58','2026-05-13 23:49:34'),(26,118,15,'You have been selected as Faculty Mentor for the event: \"Campus Innovation & Impact Expo\". Event Date: May 16, 2026.',0,'2026-05-14 03:42:08','2026-05-14 03:42:08'),(27,133,118,'Your event \"Campus Innovation & Impact Expo\" has been reviewed by the Patron with budget adjustments:\n\n- biscuts: Amount: PKR 7 (Qty: 7, Rate: 1)\n\nComments: No additional comments.',0,'2026-05-14 03:44:19','2026-05-14 03:44:19'),(28,52,170,'hi',1,'2026-05-14 03:50:22','2026-05-14 04:29:51'),(29,118,155,'You have been selected as Faculty Mentor for the event: \"FYP SEMINAR\". Event Date: May 17, 2026.',0,'2026-05-14 04:23:36','2026-05-14 04:23:36'),(30,52,170,'hello , lol',1,'2026-05-14 04:29:20','2026-05-14 04:29:51'),(31,118,28,'You have been selected as Faculty Mentor for the event: \"Future Minds Tech & Creativity Fair 2026\". Event Date: May 18, 2026.',0,'2026-05-14 05:44:19','2026-05-14 05:44:19'),(32,133,118,'Your event \"Future Minds Tech & Creativity Fair 2026\" has been reviewed by the Patron with budget adjustments:\n\n- tea: Amount: PKR 4 (Qty: 1, Rate: 4)\n\nComments: No additional comments.',0,'2026-05-14 05:46:28','2026-05-14 05:46:28'),(33,60,137,'You have been selected as Faculty Mentor for the event: \"Hackathon\". Event Date: May 20, 2026.',0,'2026-05-14 08:14:27','2026-05-14 08:14:27'),(34,170,52,'done ho gya ah',0,'2026-05-14 08:29:32','2026-05-14 08:29:32'),(35,122,52,'hello, i need help',1,'2026-05-19 21:52:00','2026-05-19 21:52:39'),(36,52,122,'how can ihelp you',0,'2026-05-19 21:52:52','2026-05-19 21:52:52'),(37,52,137,'You have been selected as Faculty Mentor for the event: \"Code for Change: Society Hackathon\". Event Date: May 22, 2026.',0,'2026-05-19 22:25:42','2026-05-19 22:25:42'),(38,52,137,'You have been selected as Faculty Mentor for the event: \"Code for Change: Society Hackathon\". Event Date: May 22, 2026.',0,'2026-05-19 22:30:46','2026-05-19 22:30:46'),(39,133,29,'You have been selected as Faculty Mentor for the event: \"interni\". Event Date: Jun 26, 2026.',0,'2026-06-17 14:09:17','2026-06-17 14:09:17'),(40,194,52,'hi',1,'2026-06-21 14:19:01','2026-06-21 14:19:45'),(41,133,27,'You have been selected as Faculty Mentor for the event: \"Upwork Success Sminar\". Event Date: Jun 25, 2026.',0,'2026-06-21 14:36:59','2026-06-21 14:36:59'),(42,133,15,'You have been selected as Faculty Mentor for the event: \"Upwork Workshop\". Event Date: Jun 23, 2026.',0,'2026-06-21 14:42:13','2026-06-21 14:42:13'),(43,194,30,'You have been selected as Faculty Mentor for the event: \"fiver  success seminar\". Event Date: Jun 24, 2026.',0,'2026-06-21 15:21:51','2026-06-21 15:21:51'),(44,194,28,'You have been selected as Faculty Mentor for the event: \"far\". Event Date: Jun 24, 2026.',0,'2026-06-21 15:34:09','2026-06-21 15:34:09'),(45,133,52,'Your event \"Code for Change: Society Hackathon\" has been reviewed by the Patron with budget adjustments:\n\n- samosay: Amount: PKR 267 (Qty: 3, Rate: 89)\n\nComments: No additional comments.',0,'2026-06-24 00:17:43','2026-06-24 00:17:43');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_11_000001_create_budgets_table',1),(5,'2024_01_11_000002_create_events_table',1),(6,'2024_01_11_000003_create_event_items_table',1),(7,'2024_01_11_000004_create_event_graphics_table',1),(8,'2024_01_11_000005_create_event_volunteers_table',1),(9,'2024_01_11_000006_create_candidate_profiles_table',1),(10,'2024_01_11_000007_create_votes_table',1),(11,'2024_01_11_000008_create_election_settings_table',1),(12,'2024_01_11_000009_create_activity_logs_table',1),(13,'2024_01_11_000010_create_announcements_table',1),(14,'2024_01_12_000001_add_approval_columns_to_events_table',1),(15,'2024_01_12_000002_add_annotations_to_event_graphics_table',1),(16,'2024_01_13_000001_create_role_assignments_table',1),(17,'2024_01_13_000002_add_faculty_role_to_users_table',1),(18,'2026_01_14_160322_remove_hod_patron_from_user_role_enum',1),(19,'2026_01_19_115613_create_messages_table',1),(20,'2026_02_12_142433_add_guest_speaker_and_mentor_to_events_table',1),(21,'2026_02_12_154056_add_hod_fields_to_event_items',1),(22,'2026_02_12_161526_change_default_approval_on_event_items',1),(23,'2026_02_12_162126_recalculate_budget_totals',1),(24,'2026_02_14_200712_add_student_details_to_users_table',1),(25,'2026_02_14_203204_create_notifications_table',1),(26,'2026_04_14_162808_create_volunteers_table',1),(27,'2026_04_14_163602_recreate_announcements_table',1),(28,'2026_04_14_171611_update_election_settings_table',1),(29,'2026_04_15_000001_create_chat_histories_table',1),(30,'2026_04_15_213239_create_semantic_embeddings_table',1),(31,'2026_04_15_214302_add_skills_and_experience_to_users_table',1),(32,'2026_04_15_214953_add_risk_assessment_to_events_table',1),(33,'2026_04_27_101616_create_student_profiles_table',1),(34,'2026_04_27_101630_create_faculty_profiles_table',1),(35,'2026_04_28_000001_create_event_media_table',2),(36,'2026_04_28_000002_create_event_documents_table',2),(37,'2026_04_28_000003_create_event_social_links_table',2),(38,'2026_04_28_000004_create_event_decoration_plans_table',2),(39,'2026_04_28_000005_add_new_roles_to_users_table',2),(40,'2026_04_28_000006_create_tasks_table',3),(41,'2026_04_28_000007_create_student_details_table',4),(42,'2026_04_28_000008_create_faculty_details_table',5),(43,'2026_04_28_000009_create_teams_and_messages_tables',6),(44,'2026_04_28_170520_add_term_code_to_academic_terms_table',7),(45,'2026_04_28_185749_update_tasks_table_for_reviews',8),(46,'2026_04_29_175404_create_chat_groups_table',9),(47,'2026_04_29_175406_create_chat_group_members_table',9),(48,'2026_04_29_175408_create_chat_messages_table',9),(49,'2026_04_29_182657_add_submission_file_to_tasks_table',10),(50,'2026_04_29_185951_add_annotated_file_to_tasks_table',11),(51,'2026_04_29_192323_add_is_volunteer_pool_to_users_table',12),(52,'2026_04_29_193648_alter_role_enum_in_role_assignments_table',13),(53,'2026_04_29_202552_add_image_and_annotations_to_chat_messages_table',14),(54,'2026_04_29_212534_fix_role_assignments_unique_constraint',15),(55,'2026_05_07_021003_update_event_and_items_table_for_guest_speaker_and_unit_rate',16),(56,'2026_05_07_021022_add_guest_link_to_events',16),(57,'2026_05_07_022731_add_link_to_announcements_table',17),(58,'2026_05_07_031328_add_signature_to_users_table',18),(59,'2026_05_07_033724_make_venue_nullable_in_events_table',19),(60,'2026_05_07_034134_add_unit_rate_to_event_items_table',20),(61,'2026_05_10_034211_add_profile_fields_to_users_table',21),(62,'2026_05_10_034917_remove_extra_fields_from_users_table',22),(63,'2026_05_10_042948_rename_announcement_columns',23),(64,'2026_05_10_043812_add_category_to_announcements',24),(65,'2026_05_10_044439_drop_category_from_announcements',25),(66,'2026_05_10_051600_create_president_selection_tables',26),(67,'2026_05_10_071428_add_signature_settings_to_events_table',27),(68,'2026_05_10_074418_make_venue_nullable_in_events_table',28),(69,'2026_05_11_025255_update_event_documents_doc_type_enum',29),(70,'2026_05_12_124227_add_decoration_images_to_tasks_table',30),(71,'2026_05_14_003523_add_details_to_volunteers_table',31),(72,'2026_05_12_144206_modify_event_documents_make_event_id_nullable',32),(73,'2026_05_14_043618_add_task_id_to_event_media_table',32),(74,'2026_05_14_044540_make_event_id_nullable_in_event_media_table',33),(75,'2026_05_20_000001_add_created_by_role_to_events_table',34),(76,'2026_06_17_190801_add_faculty_to_created_by_role_enum_in_events_table',35),(77,'2026_06_21_172035_create_certificates_table',36),(78,'2026_06_21_184527_create_password_otps_table',37),(79,'2026_06_21_190601_make_profile_fields_nullable',38);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES ('01918c31-5417-4459-bed3-cb0af634e8aa','App\\Notifications\\NewDirectMessage','App\\Models\\User',170,'{\"type\":\"direct_message\",\"message\":\"New message from Sarah Ahmed\",\"sender_id\":52,\"message_text\":\"hi\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/direct-chat?user_id=52\"}',NULL,'2026-05-14 03:50:22','2026-05-14 03:50:22'),('02c29cd0-11fb-4620-aa27-6d6321ce55d0','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":25,\"event_title\":\"science\",\"message\":\"Event \'science\' is fully approved! You can now assign tasks to teams.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/25\"}',NULL,'2026-05-12 13:59:07','2026-05-12 13:59:07'),('02c85309-5919-4a98-801d-64bbb89eedbb','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":7,\"event_title\":\"Code for Change: Society Hackathon\",\"message\":\"Your event \'Code for Change: Society Hackathon\' has been approved by the Patron. Budget adjustments were made.\",\"type\":\"warning\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/7\"}',NULL,'2026-06-24 00:17:44','2026-06-24 00:17:44'),('061dc32e-c87b-418e-b39d-ad93e5dbbdb7','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":2,\"event_title\":\"new event tesing no 56\",\"message\":\"Event \'new event tesing no 56\' is fully approved! You can now assign tasks to teams.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/2\"}',NULL,'2026-05-06 22:57:16','2026-05-06 22:57:16'),('0baf1809-6542-4a87-84ea-d6b67c45a66a','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":22,\"event_title\":\"i ma requesting new event\",\"message\":\"Event \'i ma requesting new event\' is fully approved! You can now assign tasks to teams.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/22\"}','2026-05-12 01:47:26','2026-05-12 01:43:54','2026-05-12 01:47:26'),('0d09658d-faed-4c03-b4f2-c54da07a4361','App\\Notifications\\EventStatusUpdated','App\\Models\\User',60,'{\"event_id\":5,\"event_title\":\"Hackathon\",\"message\":\"Your event \'Hackathon\' has been approved by the Patron.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/5\"}',NULL,'2026-05-14 08:19:30','2026-05-14 08:19:30'),('15ce9e19-be1b-424f-b468-7bf56917f256','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":3,\"event_title\":\"FYP SEMINAR\",\"message\":\"Event \'FYP SEMINAR\' is fully approved! You can now assign tasks to teams.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/3\"}',NULL,'2026-05-14 04:26:37','2026-05-14 04:26:37'),('1a163c7e-dd39-4620-b9dd-561503974217','App\\Notifications\\EventStatusUpdated','App\\Models\\User',121,'{\"event_id\":23,\"event_title\":\"N.b\",\"message\":\"Congratulations! Your event \'N.b\' has been fully approved by the HOD.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/23\"}',NULL,'2026-05-12 08:42:10','2026-05-12 08:42:10'),('1e363a75-6ddd-4298-88df-ae5d98ae0027','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":5,\"event_title\":\"Hackathon\",\"message\":\"Event \'Hackathon\' is fully approved! You can now assign tasks to teams.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/5\"}',NULL,'2026-05-14 08:23:21','2026-05-14 08:23:21'),('1faf9c3b-e38d-4aae-9f65-36e8cb3ec475','App\\Notifications\\EventStatusUpdated','App\\Models\\User',118,'{\"event_id\":4,\"event_title\":\"Future Minds Tech & Creativity Fair 2026\",\"message\":\"Your event \'Future Minds Tech & Creativity Fair 2026\' has been approved by the President and assigned to venue: h7.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/4\"}',NULL,'2026-05-14 05:45:01','2026-05-14 05:45:01'),('21561e64-6704-441e-86bd-5a75dcd43a90','App\\Notifications\\EventStatusUpdated','App\\Models\\User',194,'{\"event_id\":12,\"event_title\":\"fiver  success seminar\",\"message\":\"Your event \'fiver  success seminar\' has been approved by the Patron.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/12\"}',NULL,'2026-06-21 15:24:11','2026-06-21 15:24:11'),('238af3ee-0d5c-4f2a-9df6-f4964099de2c','App\\Notifications\\NewDirectMessage','App\\Models\\User',52,'{\"type\":\"direct_message\",\"message\":\"New message from John Graphic\",\"sender_id\":170,\"message_text\":\"kia hal ha g\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/direct-chat?user_id=170\"}',NULL,'2026-05-10 05:02:25','2026-05-10 05:02:25'),('28275da8-21af-4e6c-9c49-81f85b1c3363','App\\Notifications\\EventStatusUpdated','App\\Models\\User',171,'{\"event_id\":4,\"event_title\":\"Future Minds Tech & Creativity Fair 2026\",\"message\":\"Event \'Future Minds Tech & Creativity Fair 2026\' is approved! Please select volunteers for this event and send to President.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/4\"}',NULL,'2026-05-14 05:53:18','2026-05-14 05:53:18'),('2b27bca5-272e-40d6-b055-51c49f356ca2','App\\Notifications\\UserImportResult','App\\Models\\User',1,'{\"type\":\"user_import\",\"import_type\":\"student\",\"method\":\"bulk\",\"message\":\"\\u2705 Bulk Upload: 1 Student(s) added\",\"success_count\":1,\"failed_count\":0,\"notification_type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/users\"}',NULL,'2026-05-19 23:15:24','2026-05-19 23:15:24'),('2e3f2623-da93-4e89-a7e6-56f9ec561b29','App\\Notifications\\VolunteerPoolNotification','App\\Models\\User',118,'{\"student_id\":118,\"student_name\":\"Sana Virk\",\"message\":\"You have successfully joined the Volunteer Pool! Your profile is now visible to the Volunteer Coordinator.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/vc\\/search-students\"}',NULL,'2026-05-14 04:27:39','2026-05-14 04:27:39'),('2e70e983-c74c-49eb-9c26-11330c7dc4a2','App\\Notifications\\EventStatusUpdated','App\\Models\\User',171,'{\"event_id\":5,\"event_title\":\"Hackathon\",\"message\":\"Event \'Hackathon\' is approved! Please select volunteers for this event and send to President.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/5\"}',NULL,'2026-05-14 08:23:21','2026-05-14 08:23:21'),('2edbba9c-b3b1-4685-b912-166ec84b31db','App\\Notifications\\NewDirectMessage','App\\Models\\User',170,'{\"type\":\"direct_message\",\"message\":\"New message from Sarah Ahmed\",\"sender_id\":52,\"message_text\":\"hello g\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/direct-chat?user_id=52\"}',NULL,'2026-05-10 04:13:32','2026-05-10 04:13:32'),('30d18199-978f-4499-8e8a-b45387796071','App\\Notifications\\VolunteerPoolNotification','App\\Models\\User',171,'{\"student_id\":118,\"student_name\":\"Sana Virk\",\"message\":\"Student Sana Virk (BSE253737) has joined the Volunteer Pool and is ready for selection.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/vc\\/search-students\"}',NULL,'2026-05-14 04:27:39','2026-05-14 04:27:39'),('3319ffad-2973-4fe4-8977-fd3358b4d0ca','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":4,\"event_title\":\"Future Minds Tech & Creativity Fair 2026\",\"message\":\"Event \'Future Minds Tech & Creativity Fair 2026\' is fully approved! You can now assign tasks to teams.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/4\"}',NULL,'2026-05-14 05:53:18','2026-05-14 05:53:18'),('3710e342-5bd1-4b1a-ab71-1eb09aa38c15','App\\Notifications\\EventStatusUpdated','App\\Models\\User',121,'{\"event_id\":23,\"event_title\":\"N.b\",\"message\":\"Your event \'N.b\' has been approved by the Patron.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/23\"}',NULL,'2026-05-12 08:40:44','2026-05-12 08:40:44'),('37796d3e-cf49-4fa8-97f7-caca30ecbff6','App\\Notifications\\EventStatusUpdated','App\\Models\\User',183,'{\"event_id\":3,\"event_title\":\"pakistanm\",\"message\":\"Congratulations! Your event \'pakistanm\' has been fully approved by the HOD.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/3\"}',NULL,'2026-05-10 03:38:26','2026-05-10 03:38:26'),('387da1f9-d40a-44f4-9e12-fc28ff728fe3','App\\Notifications\\EventStatusUpdated','App\\Models\\User',118,'{\"event_id\":2,\"event_title\":\"Campus Innovation & Impact Expo\",\"message\":\"Your event \'Campus Innovation & Impact Expo\' has been approved by the President and assigned to venue: h4.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/2\"}',NULL,'2026-05-14 03:43:16','2026-05-14 03:43:16'),('3a23aa69-6ee9-4a90-885d-a4345a9fdb9a','App\\Notifications\\UserImportResult','App\\Models\\User',1,'{\"type\":\"user_import\",\"import_type\":\"bulk\",\"method\":\"bulk\",\"message\":\"\\u26a0\\ufe0f Bulk Upload: 10 User(s) added, 2 failed\",\"success_count\":10,\"failed_count\":2,\"notification_type\":\"warning\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/users\"}',NULL,'2026-05-19 23:11:04','2026-05-19 23:11:04'),('449f6d1f-5233-4b7f-a4e2-45910fbc0c44','App\\Notifications\\EventStatusUpdated','App\\Models\\User',60,'{\"event_id\":22,\"event_title\":\"i ma requesting new event\",\"message\":\"Your event \'i ma requesting new event\' has been approved by the Patron.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/22\"}',NULL,'2026-05-12 01:42:34','2026-05-12 01:42:34'),('44b2476d-b7db-4c60-b0ab-1ee4e0006c05','App\\Notifications\\EventStatusUpdated','App\\Models\\User',60,'{\"event_id\":5,\"event_title\":\"Hackathon\",\"message\":\"Congratulations! Your event \'Hackathon\' has been fully approved by the HOD.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/5\"}',NULL,'2026-05-14 08:23:21','2026-05-14 08:23:21'),('4739dd3e-6fe1-48e5-b9da-d449522e2156','App\\Notifications\\NewDirectMessage','App\\Models\\User',52,'{\"type\":\"direct_message\",\"message\":\"New message from Fatima Deco\",\"sender_id\":176,\"message_text\":\"ho\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/direct-chat?user_id=176\"}',NULL,'2026-05-13 23:48:58','2026-05-13 23:48:58'),('48fa9662-87d0-40d3-b673-f5c1539b7099','App\\Notifications\\UserProfileUpdatedNotification','App\\Models\\User',1,'{\"user_id\":133,\"message\":\"Hassan Farooqi (FACPR001) has updated their profile details.\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/users\",\"type\":\"profile_update\"}','2026-05-19 23:02:24','2026-05-09 22:47:58','2026-05-19 23:02:24'),('4a3d3fa7-0db1-4a47-b5de-f92189c1a4e8','App\\Notifications\\EventStatusUpdated','App\\Models\\User',118,'{\"event_id\":3,\"event_title\":\"FYP SEMINAR\",\"message\":\"Your event \'FYP SEMINAR\' has been approved by the President and assigned to venue: b4.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/3\"}',NULL,'2026-05-14 04:24:41','2026-05-14 04:24:41'),('4bd6e9bc-0132-44c1-92c9-687272d318f9','App\\Notifications\\EventStatusUpdated','App\\Models\\User',81,'{\"event_id\":2,\"event_title\":\"new event tesing no 56\",\"message\":\"Your event \'new event tesing no 56\' has been approved by the President and assigned to venue: b4.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/2\"}',NULL,'2026-05-06 22:45:33','2026-05-06 22:45:33'),('4d6f1fbd-21da-4789-bef7-93d792e0513f','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":1,\"event_title\":\"Academic Excellence Summit 2026\",\"message\":\"Event \'Academic Excellence Summit 2026\' is fully approved! You can now assign tasks to teams.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/1\"}',NULL,'2026-05-13 23:16:32','2026-05-13 23:16:32'),('4ebeb775-6337-40dd-8338-75f43e921242','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":3,\"event_title\":\"pakistanm\",\"message\":\"Event \'pakistanm\' is fully approved! You can now assign tasks to teams.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/3\"}',NULL,'2026-05-10 03:38:26','2026-05-10 03:38:26'),('55308518-6d3f-4d2a-83bd-dcc3fe2802d7','App\\Notifications\\NewDirectMessage','App\\Models\\User',52,'{\"type\":\"direct_message\",\"message\":\"New message from John Graphic\",\"sender_id\":170,\"message_text\":\"hekllo\\\\\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/direct-chat?user_id=170\"}',NULL,'2026-05-10 04:51:31','2026-05-10 04:51:31'),('593af283-8f9c-4e1f-85d2-cb93b8c13766','App\\Notifications\\EventStatusUpdated','App\\Models\\User',48,'{\"event_id\":25,\"event_title\":\"science\",\"message\":\"Congratulations! Your event \'science\' has been fully approved by the HOD.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/25\"}',NULL,'2026-05-12 13:59:07','2026-05-12 13:59:07'),('5ef4a0ec-991e-4bb1-9108-33d7e0a02dc8','App\\Notifications\\EventStatusUpdated','App\\Models\\User',80,'{\"event_id\":1,\"event_title\":\"Academic Excellence Summit 2026\",\"message\":\"Your event \'Academic Excellence Summit 2026\' has been approved by the Patron.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/1\"}',NULL,'2026-05-13 23:14:23','2026-05-13 23:14:23'),('601053d8-1628-4f0a-9a2a-e3c8838b5aef','App\\Notifications\\EventStatusUpdated','App\\Models\\User',118,'{\"event_id\":4,\"event_title\":\"Future Minds Tech & Creativity Fair 2026\",\"message\":\"Congratulations! Your event \'Future Minds Tech & Creativity Fair 2026\' has been fully approved by the HOD.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/4\"}',NULL,'2026-05-14 05:53:18','2026-05-14 05:53:18'),('60f93063-8501-4bf1-af4e-68fbaa0cfb26','App\\Notifications\\EventStatusUpdated','App\\Models\\User',194,'{\"event_id\":13,\"event_title\":\"far\",\"message\":\"Your event \'far\' has been approved by the President and assigned to venue: m8.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/13\"}',NULL,'2026-06-21 15:34:57','2026-06-21 15:34:57'),('65d9d932-bfd9-4ea8-8ec2-47c762b9c160','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":23,\"event_title\":\"N.b\",\"message\":\"Event \'N.b\' is fully approved! You can now assign tasks to teams.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/23\"}',NULL,'2026-05-12 08:42:10','2026-05-12 08:42:10'),('66809e88-9ca6-4293-b1d0-514575fe81e4','App\\Notifications\\EventStatusUpdated','App\\Models\\User',194,'{\"event_id\":13,\"event_title\":\"far\",\"message\":\"Congratulations! Your event \'far\' has been fully approved by the HOD.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/13\"}',NULL,'2026-06-21 15:37:53','2026-06-21 15:37:53'),('66d6f4e4-4bb2-4dd7-a986-db57bba3f789','App\\Notifications\\EventStatusUpdated','App\\Models\\User',113,'{\"event_id\":1,\"event_title\":\"spain\",\"message\":\"Your event \'spain\' has been approved by the Patron.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/1\"}',NULL,'2026-04-28 14:10:24','2026-04-28 14:10:24'),('6cbf737a-706d-4e38-9d05-6949c571f469','App\\Notifications\\NewDirectMessage','App\\Models\\User',170,'{\"type\":\"direct_message\",\"message\":\"New message from Sarah Ahmed\",\"sender_id\":52,\"message_text\":\"hi\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/direct-chat?user_id=52\"}',NULL,'2026-05-10 04:20:50','2026-05-10 04:20:50'),('6f31cb64-5e54-49a4-8741-9d572d0833aa','App\\Notifications\\EventStatusUpdated','App\\Models\\User',171,'{\"event_id\":1,\"event_title\":\"Academic Excellence Summit 2026\",\"message\":\"Event \'Academic Excellence Summit 2026\' is approved! Please select volunteers for this event and send to President.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/1\"}',NULL,'2026-05-13 23:16:32','2026-05-13 23:16:32'),('71ae6afa-26fe-4ad2-b81a-1eec3052d5c5','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":13,\"event_title\":\"far\",\"message\":\"Event \'far\' is fully approved! You can now assign tasks to teams.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/13\"}',NULL,'2026-06-21 15:37:53','2026-06-21 15:37:53'),('7b452fc8-a673-4b78-8144-ce97c03f7a98','App\\Notifications\\UserProfileUpdatedNotification','App\\Models\\User',1,'{\"user_id\":133,\"message\":\"Hassan Farooqi (FACPR001) has updated their profile details.\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/users\",\"type\":\"profile_update\"}','2026-05-19 23:02:24','2026-05-09 22:47:59','2026-05-19 23:02:24'),('7e3ed01f-9bba-4a00-8b71-83c875096a6d','App\\Notifications\\EventStatusUpdated','App\\Models\\User',118,'{\"event_id\":3,\"event_title\":\"FYP SEMINAR\",\"message\":\"Congratulations! Your event \'FYP SEMINAR\' has been fully approved by the HOD.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/3\"}',NULL,'2026-05-14 04:26:37','2026-05-14 04:26:37'),('81aba335-f529-4224-bd0a-bc18a78db6a2','App\\Notifications\\NewDirectMessage','App\\Models\\User',52,'{\"type\":\"direct_message\",\"message\":\"New message from John Graphic\",\"sender_id\":170,\"message_text\":\"jksbkbceke\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/direct-chat?user_id=170\"}',NULL,'2026-05-10 04:14:09','2026-05-10 04:14:09'),('834c382d-6b92-49c3-aa47-0249e73d9377','App\\Notifications\\EventStatusUpdated','App\\Models\\User',133,'{\"event_id\":9,\"event_title\":\"interni\",\"message\":\"Your event \'interni\' has been approved by the President and assigned to venue: b7.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/9\"}',NULL,'2026-06-17 14:10:44','2026-06-17 14:10:44'),('83c00305-109c-437f-9c48-f56a01110ee1','App\\Notifications\\NewDirectMessage','App\\Models\\User',52,'{\"type\":\"direct_message\",\"message\":\"New message from John Graphic\",\"sender_id\":170,\"message_text\":\"pakistan\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/direct-chat?user_id=170\"}',NULL,'2026-05-13 19:58:31','2026-05-13 19:58:31'),('87923ce1-44fc-4c3e-9292-0a7ef6c568ec','App\\Notifications\\UserProfileUpdatedNotification','App\\Models\\User',1,'{\"user_id\":52,\"message\":\"Sarah Ahmed (PRES-001) has updated their profile details.\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/users\",\"type\":\"profile_update\"}','2026-05-10 05:08:39','2026-05-10 03:08:26','2026-05-10 05:08:39'),('895fb88e-4601-44cf-9e60-4cf2dde9834d','App\\Notifications\\EventStatusUpdated','App\\Models\\User',194,'{\"event_id\":12,\"event_title\":\"fiver  success seminar\",\"message\":\"Your event \'fiver  success seminar\' has been approved by the President and assigned to venue: k9.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/12\"}',NULL,'2026-06-21 15:23:02','2026-06-21 15:23:02'),('8bb20795-1b7f-4099-b32e-89f5952b1ef2','App\\Notifications\\EventStatusUpdated','App\\Models\\User',113,'{\"event_id\":1,\"event_title\":\"spain\",\"message\":\"Your event \'spain\' has been approved by the President.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/1\"}',NULL,'2026-04-28 14:08:56','2026-04-28 14:08:56'),('8f889b7d-12ad-4f58-b9fd-ef5916cccb8e','App\\Notifications\\EventStatusUpdated','App\\Models\\User',121,'{\"event_id\":23,\"event_title\":\"N.b\",\"message\":\"Your event \'N.b\' has been approved by the President and assigned to venue: b7.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/23\"}',NULL,'2026-05-12 08:39:57','2026-05-12 08:39:57'),('8fd893aa-46f6-4f1e-9e65-7c2361fe5f16','App\\Notifications\\EventStatusUpdated','App\\Models\\User',133,'{\"event_id\":9,\"event_title\":\"interni\",\"message\":\"Your event \'interni\' has been approved by the Patron.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/9\"}',NULL,'2026-06-17 14:13:06','2026-06-17 14:13:06'),('90144f5b-a2db-4ede-bb12-8e6f6d02948a','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":6,\"event_title\":\"Code for Change: Society Hackathon\",\"message\":\"Your event \'Code for Change: Society Hackathon\' has been rejected by the Patron.\",\"type\":\"error\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/6\"}',NULL,'2026-05-19 22:33:04','2026-05-19 22:33:04'),('9666904f-d48d-48cd-82e2-7429c141b2ce','App\\Notifications\\EventStatusUpdated','App\\Models\\User',183,'{\"event_id\":3,\"event_title\":\"pakistanm\",\"message\":\"Your event \'pakistanm\' has been approved by the Patron.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/3\"}',NULL,'2026-05-10 03:19:01','2026-05-10 03:19:01'),('99ac78ea-1816-4458-957c-ec7a1faab376','App\\Notifications\\EventStatusUpdated','App\\Models\\User',171,'{\"event_id\":3,\"event_title\":\"FYP SEMINAR\",\"message\":\"Event \'FYP SEMINAR\' is approved! Please select volunteers for this event and send to President.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/3\"}',NULL,'2026-05-14 04:26:37','2026-05-14 04:26:37'),('9d59f81d-bc3f-4f31-8d3b-47efaaacfa90','App\\Notifications\\VolunteerPoolNotification','App\\Models\\User',183,'{\"student_id\":183,\"student_name\":\"Normal Student 1\",\"message\":\"You have successfully joined the Volunteer Pool! Your profile is now visible to the Volunteer Coordinator.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/vc\\/search-students\"}',NULL,'2026-05-10 02:42:09','2026-05-10 02:42:09'),('9f9fbf94-31d0-429b-a4bc-ee8ed9ea2dea','App\\Notifications\\EventStatusUpdated','App\\Models\\User',118,'{\"event_id\":4,\"event_title\":\"Future Minds Tech & Creativity Fair 2026\",\"message\":\"Your event \'Future Minds Tech & Creativity Fair 2026\' has been approved by the Patron. Budget adjustments were made.\",\"type\":\"warning\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/4\"}',NULL,'2026-05-14 05:46:28','2026-05-14 05:46:28'),('a084cf55-d587-42f5-8040-502476aa9693','App\\Notifications\\EventStatusUpdated','App\\Models\\User',118,'{\"event_id\":3,\"event_title\":\"FYP SEMINAR\",\"message\":\"Your event \'FYP SEMINAR\' has been approved by the Patron.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/3\"}',NULL,'2026-05-14 04:25:30','2026-05-14 04:25:30'),('a0c9b951-6599-4ff7-b0e0-093af53201d5','App\\Notifications\\EventStatusUpdated','App\\Models\\User',113,'{\"event_id\":1,\"event_title\":\"spain\",\"message\":\"Your event \'spain\' has been approved by the HOD.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/1\"}',NULL,'2026-04-28 14:11:37','2026-04-28 14:11:37'),('a67f0ab4-9fe0-4f26-9780-e6e76800bcac','App\\Notifications\\EventStatusUpdated','App\\Models\\User',171,'{\"event_id\":2,\"event_title\":\"Campus Innovation & Impact Expo\",\"message\":\"Event \'Campus Innovation & Impact Expo\' is approved! Please select volunteers for this event and send to President.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/2\"}',NULL,'2026-05-14 03:45:31','2026-05-14 03:45:31'),('aa4bd6ca-a422-40fb-bca2-2dabb378cc5e','App\\Notifications\\EventStatusUpdated','App\\Models\\User',60,'{\"event_id\":22,\"event_title\":\"i ma requesting new event\",\"message\":\"Congratulations! Your event \'i ma requesting new event\' has been fully approved by the HOD.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/22\"}',NULL,'2026-05-12 01:43:54','2026-05-12 01:43:54'),('ae3d08de-af82-4b0a-82ed-e93deb85857d','App\\Notifications\\NewDirectMessage','App\\Models\\User',170,'{\"type\":\"direct_message\",\"message\":\"New message from Sarah Ahmed\",\"sender_id\":52,\"message_text\":\"hello , lol\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/direct-chat?user_id=52\"}',NULL,'2026-05-14 04:29:20','2026-05-14 04:29:20'),('af272b8b-6403-4f95-8bc1-5fa097e5e8a7','App\\Notifications\\NewDirectMessage','App\\Models\\User',170,'{\"type\":\"direct_message\",\"message\":\"New message from Sarah Ahmed\",\"sender_id\":52,\"message_text\":\"ki ha;ll h\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/direct-chat?user_id=52\"}','2026-05-10 10:07:19','2026-05-10 04:56:25','2026-05-10 10:07:19'),('b1189fb9-3ab9-496b-9db7-34fb1cb37a89','App\\Notifications\\EventStatusUpdated','App\\Models\\User',118,'{\"event_id\":2,\"event_title\":\"Campus Innovation & Impact Expo\",\"message\":\"Congratulations! Your event \'Campus Innovation & Impact Expo\' has been fully approved by the HOD.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/2\"}',NULL,'2026-05-14 03:45:31','2026-05-14 03:45:31'),('b64fec3e-df27-4e39-9681-64c0641e14b6','App\\Notifications\\EventStatusUpdated','App\\Models\\User',113,'{\"event_id\":1,\"event_title\":\"spain\",\"message\":\"Congratulations! Your event \'spain\' has been fully approved.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/1\"}',NULL,'2026-04-28 14:17:41','2026-04-28 14:17:41'),('ba019b2f-4c83-4f51-9fb4-53734acf70ae','App\\Notifications\\EventStatusUpdated','App\\Models\\User',194,'{\"event_id\":13,\"event_title\":\"far\",\"message\":\"Your event \'far\' has been approved by the Patron.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/13\"}',NULL,'2026-06-21 15:36:29','2026-06-21 15:36:29'),('bb6aa41d-b2b8-4d8f-adf3-38a5617ba83c','App\\Notifications\\EventStatusUpdated','App\\Models\\User',81,'{\"event_id\":2,\"event_title\":\"new event tesing no 56\",\"message\":\"Your event \'new event tesing no 56\' has been approved by the Patron.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/2\"}',NULL,'2026-05-06 22:54:46','2026-05-06 22:54:46'),('bf1cb849-816f-4219-9587-bddbc46e87de','App\\Notifications\\EventStatusUpdated','App\\Models\\User',183,'{\"event_id\":3,\"event_title\":\"pakistanm\",\"message\":\"Your event \'pakistanm\' has been approved by the President and assigned to venue: b7.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/3\"}',NULL,'2026-05-10 03:09:29','2026-05-10 03:09:29'),('bf8b9961-da5c-4714-8c8c-966f9d506b27','App\\Notifications\\UserProfileUpdatedNotification','App\\Models\\User',1,'{\"user_id\":133,\"message\":\"Hassan Farooqi (FACPR001) has updated their profile details.\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/users\",\"type\":\"profile_update\"}','2026-05-19 23:02:24','2026-05-14 04:55:27','2026-05-19 23:02:24'),('c62532de-6641-4da0-8b80-8989231e9ee7','App\\Notifications\\NewDirectMessage','App\\Models\\User',52,'{\"type\":\"direct_message\",\"message\":\"New message from John Graphic\",\"sender_id\":170,\"message_text\":\"done ho gya ah\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/direct-chat?user_id=170\"}',NULL,'2026-05-14 08:29:32','2026-05-14 08:29:32'),('c8387f7b-480c-4f8b-bf3a-dc3b65ca00d4','App\\Notifications\\NewDirectMessage','App\\Models\\User',170,'{\"type\":\"direct_message\",\"message\":\"New message from Sarah Ahmed\",\"sender_id\":52,\"message_text\":\"ji\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/direct-chat?user_id=52\"}',NULL,'2026-05-10 04:43:27','2026-05-10 04:43:27'),('c935ecdb-91d1-46c3-8426-54fd7904e325','App\\Notifications\\EventStatusUpdated','App\\Models\\User',60,'{\"event_id\":22,\"event_title\":\"i ma requesting new event\",\"message\":\"Your event \'i ma requesting new event\' has been approved by the President and assigned to venue: m5.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/22\"}',NULL,'2026-05-12 01:36:15','2026-05-12 01:36:15'),('c96edf7b-5444-4b23-8f1d-3754f529bb4b','App\\Notifications\\EventStatusUpdated','App\\Models\\User',80,'{\"event_id\":1,\"event_title\":\"Academic Excellence Summit 2026\",\"message\":\"Your event \'Academic Excellence Summit 2026\' has been approved by the President and assigned to venue: b7.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/1\"}',NULL,'2026-05-13 23:13:41','2026-05-13 23:13:41'),('cbee7ab5-de04-4aa3-8740-3a1bb9c31491','App\\Notifications\\EventStatusUpdated','App\\Models\\User',171,'{\"event_id\":23,\"event_title\":\"N.b\",\"message\":\"Event \'N.b\' is approved! Please select volunteers for this event and send to President.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/23\"}',NULL,'2026-05-12 08:42:10','2026-05-12 08:42:10'),('ce6e9804-85f3-4cd6-be88-c3c9be5eded3','App\\Notifications\\EventStatusUpdated','App\\Models\\User',60,'{\"event_id\":5,\"event_title\":\"Hackathon\",\"message\":\"Your event \'Hackathon\' has been approved by the President and assigned to venue: b7.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/5\"}',NULL,'2026-05-14 08:17:25','2026-05-14 08:17:25'),('cefb8b98-eca0-451c-9b8c-8a019c4e26fb','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":9,\"event_title\":\"interni\",\"message\":\"Event \'interni\' is fully approved! You can now assign tasks to teams.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/9\"}',NULL,'2026-06-17 14:14:15','2026-06-17 14:14:15'),('cfd80793-dcdf-45e8-afb9-f68186a4eae4','App\\Notifications\\UserImportResult','App\\Models\\User',1,'{\"type\":\"user_import\",\"import_type\":\"student\",\"method\":\"bulk\",\"message\":\"\\u2705 Bulk Upload: 1 Student(s) added\",\"success_count\":1,\"failed_count\":0,\"notification_type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/users\"}',NULL,'2026-06-18 00:49:10','2026-06-18 00:49:10'),('d0c5f123-b32f-4559-a5a6-688c09ae3541','App\\Notifications\\EventStatusUpdated','App\\Models\\User',48,'{\"event_id\":25,\"event_title\":\"science\",\"message\":\"Your event \'science\' has been approved by the President and assigned to venue: mj.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/25\"}',NULL,'2026-05-12 13:57:31','2026-05-12 13:57:31'),('d1325f0b-c3f3-40eb-b68c-acd43e128410','App\\Notifications\\UserImportResult','App\\Models\\User',1,'{\"type\":\"user_import\",\"import_type\":\"student\",\"method\":\"single\",\"message\":\"\\u274c Failed to add Student (Test Student Failure): Email duplicate error\",\"success_count\":0,\"failed_count\":1,\"notification_type\":\"error\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/users\"}',NULL,'2026-05-19 23:11:04','2026-05-19 23:11:04'),('d7015d80-3177-4bf1-b7be-e4deeaefef10','App\\Notifications\\EventStatusUpdated','App\\Models\\User',118,'{\"event_id\":2,\"event_title\":\"Campus Innovation & Impact Expo\",\"message\":\"Your event \'Campus Innovation & Impact Expo\' has been approved by the Patron. Budget adjustments were made.\",\"type\":\"warning\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/2\"}',NULL,'2026-05-14 03:44:19','2026-05-14 03:44:19'),('e064f4de-252b-4c7c-b3c9-f65186ca6e28','App\\Notifications\\EventStatusUpdated','App\\Models\\User',52,'{\"event_id\":2,\"event_title\":\"Campus Innovation & Impact Expo\",\"message\":\"Event \'Campus Innovation & Impact Expo\' is fully approved! You can now assign tasks to teams.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/2\"}',NULL,'2026-05-14 03:45:31','2026-05-14 03:45:31'),('e27ffc0b-8512-4d4c-86f5-149378585021','App\\Notifications\\EventStatusUpdated','App\\Models\\User',171,'{\"event_id\":9,\"event_title\":\"interni\",\"message\":\"Event \'interni\' is approved! Please select volunteers for this event and send to President.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/9\"}',NULL,'2026-06-17 14:14:15','2026-06-17 14:14:15'),('e2be9237-7e20-441c-9c32-b41640c0d02a','App\\Notifications\\UserImportResult','App\\Models\\User',1,'{\"type\":\"user_import\",\"import_type\":\"student\",\"method\":\"single\",\"message\":\"\\u2705 New Student added successfully: Test Student Success\",\"success_count\":1,\"failed_count\":0,\"notification_type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/users\"}',NULL,'2026-05-19 23:11:04','2026-05-19 23:11:04'),('e881310c-fddf-4f2c-a50e-a9180b959dbe','App\\Notifications\\UserProfileUpdatedNotification','App\\Models\\User',1,'{\"user_id\":52,\"message\":\"Sarah Ahmed (PRES-001) has updated their profile details.\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/users\",\"type\":\"profile_update\"}','2026-05-19 23:02:24','2026-05-14 04:32:43','2026-05-19 23:02:24'),('eb7f63dc-d128-4599-a91f-fd011906a591','App\\Notifications\\EventStatusUpdated','App\\Models\\User',171,'{\"event_id\":25,\"event_title\":\"science\",\"message\":\"Event \'science\' is approved! Please select volunteers for this event and send to President.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/25\"}',NULL,'2026-05-12 13:59:07','2026-05-12 13:59:07'),('edb5ef3e-e72a-49bc-b77f-7214efb031a2','App\\Notifications\\EventStatusUpdated','App\\Models\\User',80,'{\"event_id\":1,\"event_title\":\"Academic Excellence Summit 2026\",\"message\":\"Congratulations! Your event \'Academic Excellence Summit 2026\' has been fully approved by the HOD.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/1\"}',NULL,'2026-05-13 23:16:32','2026-05-13 23:16:32'),('ee3977c6-07e5-435a-ac6b-b5df8bfdcd56','App\\Notifications\\EventStatusUpdated','App\\Models\\User',48,'{\"event_id\":25,\"event_title\":\"science\",\"message\":\"Your event \'science\' has been approved by the Patron.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/25\"}',NULL,'2026-05-12 13:58:15','2026-05-12 13:58:15'),('eecb7c7d-9285-454b-b053-a2cb77098585','App\\Notifications\\UserImportResult','App\\Models\\User',1,'{\"type\":\"user_import\",\"import_type\":\"student\",\"method\":\"bulk\",\"message\":\"\\u274c Bulk Upload Failed: No Student(s) were added. 1 row(s) had errors.\",\"success_count\":0,\"failed_count\":1,\"notification_type\":\"error\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/users\"}',NULL,'2026-05-19 23:13:34','2026-05-19 23:13:34'),('f102b1d7-65b2-403f-8c10-2e211281cf36','App\\Notifications\\NewDirectMessage','App\\Models\\User',52,'{\"type\":\"direct_message\",\"message\":\"New message from Ali Social\",\"sender_id\":172,\"message_text\":\"hello\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/direct-chat?user_id=172\"}',NULL,'2026-05-13 23:27:24','2026-05-13 23:27:24'),('f8cb3341-39fb-442b-855e-6d057e50525e','App\\Notifications\\EventStatusUpdated','App\\Models\\User',133,'{\"event_id\":9,\"event_title\":\"interni\",\"message\":\"Congratulations! Your event \'interni\' has been fully approved by the HOD.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/9\"}',NULL,'2026-06-17 14:14:15','2026-06-17 14:14:15'),('fb2688b2-75ba-4090-adfb-3b441a4fde8b','App\\Notifications\\EventStatusUpdated','App\\Models\\User',171,'{\"event_id\":13,\"event_title\":\"far\",\"message\":\"Event \'far\' is approved! Please select volunteers for this event and send to President.\",\"type\":\"info\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/13\"}',NULL,'2026-06-21 15:37:53','2026-06-21 15:37:53'),('fed293fa-50ce-40ca-b15b-9633c8b57a94','App\\Notifications\\EventStatusUpdated','App\\Models\\User',81,'{\"event_id\":2,\"event_title\":\"new event tesing no 56\",\"message\":\"Congratulations! Your event \'new event tesing no 56\' has been fully approved by the HOD.\",\"type\":\"success\",\"action_url\":\"http:\\/\\/127.0.0.1:8000\\/student\\/events\\/2\"}',NULL,'2026-05-06 22:57:16','2026-05-06 22:57:16');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_otps`
--

DROP TABLE IF EXISTS `password_otps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_otps` (
  `email` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `password_otps_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_otps`
--

LOCK TABLES `password_otps` WRITE;
/*!40000 ALTER TABLE `password_otps` DISABLE KEYS */;
INSERT INTO `password_otps` VALUES ('zainn8847@gmail.com','503540','2026-06-21 14:11:14','2026-06-21 14:01:14','2026-06-21 14:01:14');
/*!40000 ALTER TABLE `password_otps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
INSERT INTO `password_reset_tokens` VALUES ('zainn8847@gmail.com','$2y$12$Ccs3yNbHXrbkjtCgu0T15.RPYkPef3/2Z0Eiw/5QIIxWwjDoLKfkm','2026-06-18 00:51:43');
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_assignments`
--

DROP TABLE IF EXISTS `role_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `term_id` bigint(20) unsigned NOT NULL,
  `role` enum('hod','patron','president','sa','vc','gd','smt','doc','photo','video','deco') NOT NULL,
  `assigned_by` bigint(20) unsigned DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `active_only` tinyint(1) GENERATED ALWAYS AS (if(`is_active` = 1,1,NULL)) VIRTUAL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_active_role_per_term` (`term_id`,`role`,`active_only`),
  KEY `role_assignments_user_id_foreign` (`user_id`),
  KEY `role_assignments_assigned_by_foreign` (`assigned_by`),
  KEY `role_assignments_term_id_index` (`term_id`),
  CONSTRAINT `role_assignments_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `role_assignments_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `academic_terms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_assignments`
--

LOCK TABLES `role_assignments` WRITE;
/*!40000 ALTER TABLE `role_assignments` DISABLE KEYS */;
INSERT INTO `role_assignments` VALUES (1,134,1,'hod',1,'2026-04-28 14:02:39',1,'2026-04-28 14:02:39','2026-04-28 14:02:39',1),(2,133,1,'patron',134,'2026-04-28 14:03:18',1,'2026-04-28 14:03:18','2026-04-28 14:03:18',1),(11,170,1,'gd',52,'2026-04-29 16:23:50',0,'2026-04-29 16:23:50','2026-04-29 16:32:32',NULL),(12,173,1,'photo',52,'2026-04-29 16:23:52',1,'2026-04-29 16:23:52','2026-04-29 16:23:52',1),(13,170,1,'gd',52,'2026-04-29 16:23:54',0,'2026-04-29 16:23:54','2026-04-29 16:32:32',NULL),(14,170,1,'gd',52,'2026-04-29 16:32:32',1,'2026-04-29 16:32:32','2026-04-29 16:32:32',1),(15,174,1,'video',52,'2026-04-29 16:32:42',0,'2026-04-29 16:32:42','2026-04-29 16:32:58',NULL),(16,175,1,'doc',52,'2026-04-29 16:32:51',1,'2026-04-29 16:32:51','2026-04-29 16:32:51',1),(17,174,1,'video',52,'2026-04-29 16:32:58',1,'2026-04-29 16:32:58','2026-04-29 16:32:58',1),(18,176,1,'deco',52,'2026-04-29 16:33:08',1,'2026-04-29 16:33:08','2026-04-29 16:33:08',1),(19,172,1,'smt',52,'2026-04-29 16:33:14',1,'2026-04-29 16:33:14','2026-04-29 16:33:14',1),(20,177,1,'sa',52,'2026-04-29 16:33:22',1,'2026-04-29 16:33:22','2026-04-29 16:33:22',1),(22,52,1,'president',NULL,'2026-05-10 09:54:07',1,'2026-05-10 04:54:07','2026-05-10 04:54:07',1);
/*!40000 ALTER TABLE `role_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `selection_committees`
--

DROP TABLE IF EXISTS `selection_committees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `selection_committees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hod_id` bigint(20) unsigned NOT NULL,
  `patron_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `selection_committees_hod_id_foreign` (`hod_id`),
  KEY `selection_committees_patron_id_foreign` (`patron_id`),
  CONSTRAINT `selection_committees_hod_id_foreign` FOREIGN KEY (`hod_id`) REFERENCES `users` (`id`),
  CONSTRAINT `selection_committees_patron_id_foreign` FOREIGN KEY (`patron_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `selection_committees`
--

LOCK TABLES `selection_committees` WRITE;
/*!40000 ALTER TABLE `selection_committees` DISABLE KEYS */;
INSERT INTO `selection_committees` VALUES (1,134,133,0,'2026-05-10 01:02:00','2026-05-10 01:04:02');
/*!40000 ALTER TABLE `selection_committees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `semantic_embeddings`
--

DROP TABLE IF EXISTS `semantic_embeddings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `semantic_embeddings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(255) NOT NULL,
  `entity_id` bigint(20) unsigned NOT NULL,
  `content_text` text NOT NULL,
  `embedding` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`embedding`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `semantic_embeddings_entity_type_entity_id_index` (`entity_type`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `semantic_embeddings`
--

LOCK TABLES `semantic_embeddings` WRITE;
/*!40000 ALTER TABLE `semantic_embeddings` DISABLE KEYS */;
/*!40000 ALTER TABLE `semantic_embeddings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('4sZ4ro6D4B5sZoUMGxa2piHwdQqr2BtqpnuTrtNJ',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','YToyOntzOjY6Il90b2tlbiI7czo0MDoiSkszaW9GVGdrWGJTZUY3eTFpNmN2b21YVFhRTmVJdGhUYnpwQzdkTCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1782073997),('cg9zYDahJMes2vKNpIbKzglrWHQxEGjo3JSMKiuR',133,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRU5yemZXOWQ0ZDBiV1pNaFVYVk5ibzdERlNiUzBvR1dKWkJDRjBwRSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjE6e2k6MDtzOjc6InN1Y2Nlc3MiO319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEzMztzOjc6InN1Y2Nlc3MiO3M6Mjk6IldlbGNvbWUgYmFjaywgSGFzc2FuIEZhcm9vcWkhIjt9',1782074136),('F0Hd9WHCeqdGdkM5iWwI1jfF7JbxU8HngIlHkLpl',134,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOWlCT3g0azd1U2dzVElNTGtXSGdZSktPbjBUbDBmTVBvZjRXNVBDayI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcmNoaXZlL2V2ZW50LzEzIjtzOjU6InJvdXRlIjtzOjEzOiJhcmNoaXZlLmV2ZW50Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTM0O30=',1782074294),('Ke4ZiHrYxrmIdfRtLa42EWkINsAhd4JzhEeLzjYN',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','YToyOntzOjY6Il90b2tlbiI7czo0MDoiQ0pXNkF0NW5INUlmYnZJbGtoQkVwRHJLYkdjNEp3d1poRWlDSGN4MyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1782074055),('TYbEWZGrcPxbwXpmxI1fFMpKDHpzk4ssC19uGOe8',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','YToyOntzOjY6Il90b2tlbiI7czo0MDoiMTFpM2pRaWFMRmFkd1lzYVVpa2trTVhlV2VEdzhvekw5Mmh6aXVhcSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1782069559),('vhSoDOHQQhJ6QSFaY8zkrSIIjKsqAAsvoCF0gEs5',133,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMVV6NEZFTUl4TkJEZ1BFN0lJeFZWT3diVkFWUGk1M0JQN2N5bFZmRyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wYXRyb24vbXktZXZlbnRzIjtzOjU6InJvdXRlIjtzOjIyOiJwYXRyb24ubXktZXZlbnRzLmluZGV4Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTMzO30=',1782288479);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_details`
--

DROP TABLE IF EXISTS `student_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `gender` enum('M','F') NOT NULL,
  `admission_date` date NOT NULL,
  `nationality` varchar(255) NOT NULL,
  `passport_number` varchar(255) DEFAULT NULL,
  `dob` date NOT NULL,
  `domicile_district` varchar(255) NOT NULL,
  `domicile_province` varchar(255) NOT NULL,
  `mailing_address` text NOT NULL,
  `city` varchar(255) NOT NULL,
  `ssc_degree_name` varchar(255) NOT NULL,
  `ssc_board_name` varchar(255) NOT NULL,
  `ssc_total_marks` int(11) NOT NULL,
  `ssc_obtained_marks` int(11) NOT NULL,
  `hssc_degree_name` varchar(255) NOT NULL,
  `hssc_nomenclature` enum('1','2','3') NOT NULL,
  `hssc_board_name` varchar(255) NOT NULL,
  `hssc_total_marks` int(11) NOT NULL,
  `hssc_obtained_marks` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_details_user_id_foreign` (`user_id`),
  CONSTRAINT `student_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_details`
--

LOCK TABLES `student_details` WRITE;
/*!40000 ALTER TABLE `student_details` DISABLE KEYS */;
INSERT INTO `student_details` VALUES (1,53,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(2,54,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(3,55,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(4,56,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(5,57,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(6,58,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(7,59,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(8,60,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(9,61,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(10,62,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(11,63,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(12,64,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(13,65,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(14,66,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(15,67,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(16,68,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(17,69,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(18,70,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(19,71,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(20,72,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(21,73,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(22,74,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(23,75,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(24,76,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(25,77,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(26,78,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(27,79,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(28,80,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(29,81,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(30,82,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(31,83,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(32,84,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(33,85,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(34,86,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(35,87,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(36,88,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(37,89,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(38,90,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(39,91,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(40,92,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(41,93,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(42,94,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(43,95,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(44,96,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(45,97,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(46,98,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(47,99,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(48,100,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(49,101,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(50,102,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(51,103,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(52,104,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(53,105,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(54,106,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(55,107,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(56,108,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(57,109,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(58,110,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(59,111,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(60,112,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(61,113,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(62,114,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(63,115,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(64,116,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(65,117,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(66,118,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(67,119,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(68,120,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(69,121,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(70,122,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(71,123,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(72,124,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(73,125,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(74,126,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(75,127,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(76,128,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(77,129,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(78,130,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(79,131,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(80,132,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(81,170,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(82,171,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(83,172,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(84,173,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(85,174,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(86,175,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(87,176,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(88,177,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(89,32,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:41','2026-04-29 14:42:41'),(90,33,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:41','2026-04-29 14:42:41'),(91,34,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:41','2026-04-29 14:42:41'),(92,35,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:41','2026-04-29 14:42:41'),(93,36,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(94,37,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(95,38,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(96,39,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(97,40,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(98,41,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(99,42,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(100,43,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(101,44,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(102,45,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(103,46,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(104,47,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(105,48,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(106,49,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(107,50,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(108,51,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(109,178,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(110,179,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(111,180,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(112,181,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(113,182,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(114,183,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(115,184,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(116,185,'M','2023-09-01','Pakistani',NULL,'2000-01-01','Lahore','Punjab','Sample Address','Lahore','Matric','BISE Lahore',1100,900,'FSC','1','BISE Lahore',1100,850,'2026-04-29 14:42:42','2026-04-29 14:42:42'),(119,193,'M','2025-09-05','Pakistani','','2005-05-15','Lahore','Punjab','Street 1, Model Town','Lahore','Matric','BISE Lahore',1100,950,'FSc Pre-Engineering','1','BISE Lahore',1100,980,'2026-05-19 23:15:23','2026-05-19 23:15:23'),(120,194,'M','2024-09-01','Pakistani','','2005-05-15','Lahore','Punjab','Street 1, Model Town','Kharian','Matric','BISE Lahore',1100,950,'FSc Pre-Engineering','1','BISE Lahore',1100,990,'2026-06-18 00:49:06','2026-06-18 00:49:06');
/*!40000 ALTER TABLE `student_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_profiles`
--

DROP TABLE IF EXISTS `student_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `roll_no` varchar(255) NOT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `cnic_number` varchar(255) DEFAULT NULL,
  `passport_number` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `domicile_district` varchar(255) DEFAULT NULL,
  `domicile_province` varchar(255) DEFAULT NULL,
  `mailing_address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `ssc_degree_name` varchar(255) DEFAULT NULL,
  `ssc_board_name` varchar(255) DEFAULT NULL,
  `ssc_total_marks` int(11) DEFAULT NULL,
  `ssc_obtained_marks` int(11) DEFAULT NULL,
  `hssc_degree_name` varchar(255) DEFAULT NULL,
  `hssc_degree_nomenclature` varchar(255) DEFAULT NULL,
  `hssc_board_name` varchar(255) DEFAULT NULL,
  `hssc_total_marks` int(11) DEFAULT NULL,
  `hssc_obtained_marks` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_profiles_roll_no_unique` (`roll_no`),
  KEY `student_profiles_user_id_foreign` (`user_id`),
  CONSTRAINT `student_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_profiles`
--

LOCK TABLES `student_profiles` WRITE;
/*!40000 ALTER TABLE `student_profiles` DISABLE KEYS */;
INSERT INTO `student_profiles` VALUES (1,32,'BSE223001','Clinton Dach III','Male','2022-09-01','Pakistani','42147-7900675-0','','1971-01-08','+1-857-894-7866','Islamabad','Punjab','6502 Dare Plain\nEast Geovannifort, KY 67731-2219','East Gia','Matric','FBISE',1100,823,'FSc','Pre-Engineering','FBISE',1100,932,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(2,33,'BSE223002','Sim Price','Female','2022-09-01','Pakistani','23435-5335060-1','','2003-11-16','681.250.7783','Islamabad','Punjab','280 Barton Street Apt. 687\nAnahifurt, ND 31985','North Brandy','Matric','FBISE',1100,991,'FSc','Pre-Engineering','FBISE',1100,1006,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(3,34,'BSE223003','Sebastian Robel','Female','2022-09-01','Pakistani','74609-7873063-3','','1988-03-30','+1.510.640.8532','Islamabad','Punjab','8252 Dasia Circle\nPort Thaliaberg, VT 27888-7951','West Lorinebury','Matric','FBISE',1100,847,'FSc','Pre-Engineering','FBISE',1100,957,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(4,35,'BSE223004','Buddy Robel DDS','Female','2022-09-01','Pakistani','65015-1907380-2','','2003-07-01','430-840-1318','Islamabad','Punjab','578 Morris Flats Suite 789\nVolkmanmouth, UT 44832-3353','Scottieborough','Matric','FBISE',1100,1005,'FSc','Pre-Engineering','FBISE',1100,855,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(5,36,'BSE223005','Dr. Vance Homenick III','Female','2022-09-01','Pakistani','20349-5671035-7','','1972-02-10','+1-903-508-5927','Islamabad','Punjab','71050 Troy Freeway\nNew Columbusville, MO 70037','North Sabina','Matric','FBISE',1100,940,'FSc','Pre-Engineering','FBISE',1100,949,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(6,37,'BSE223006','Mr. Tyrell Little I','Male','2022-09-01','Pakistani','82983-1680613-0','','1980-03-01','+19857376211','Islamabad','Punjab','544 Charley Valleys Suite 206\nAugustushaven, IL 53511','East Elvieside','Matric','FBISE',1100,834,'FSc','Pre-Engineering','FBISE',1100,862,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(7,38,'BSE223007','Russel Moen','Male','2022-09-01','Pakistani','04602-2839890-5','','1993-02-18','+1.206.889.7287','Islamabad','Punjab','63136 Elenora Harbor Suite 668\nToyfurt, AR 28426-1652','South Torreychester','Matric','FBISE',1100,1023,'FSc','Pre-Engineering','FBISE',1100,896,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(8,39,'BSE223008','Stephan Boyle','Male','2022-09-01','Pakistani','23718-6530498-5','','1984-02-25','+12105148009','Islamabad','Punjab','463 Wunsch Forest\nNorth Jaquelinshire, CO 83676','Willmsville','Matric','FBISE',1100,952,'FSc','Pre-Engineering','FBISE',1100,819,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(9,40,'BSE223009','Jillian Cummerata','Female','2022-09-01','Pakistani','98064-3189134-8','','1971-08-05','1-361-420-0928','Islamabad','Punjab','12212 Colt Crossing\nLake Gabriel, UT 20978','West Brianne','Matric','FBISE',1100,808,'FSc','Pre-Engineering','FBISE',1100,841,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(10,41,'BSE223010','Dan Hackett','Male','2022-09-01','Pakistani','15421-4781509-6','','1971-05-27','469.233.8624','Islamabad','Punjab','2756 Lela Cove\nWest Jakob, HI 05316','Runteville','Matric','FBISE',1100,974,'FSc','Pre-Engineering','FBISE',1100,992,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(11,42,'BSE223011','Gregg Dickinson','Female','2022-09-01','Pakistani','32929-9072897-3','','1994-10-29','(267) 468-7480','Islamabad','Punjab','1106 Bradly Plaza Suite 599\nNatashaview, DE 71294-2996','South Mayeville','Matric','FBISE',1100,1028,'FSc','Pre-Engineering','FBISE',1100,921,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(12,43,'BSE223012','Neal Leffler','Female','2022-09-01','Pakistani','42701-5216355-3','','2003-03-06','1-615-859-6703','Islamabad','Punjab','347 Nelda Cliff\nSouth Octaviafort, LA 28000','Prosaccoside','Matric','FBISE',1100,943,'FSc','Pre-Engineering','FBISE',1100,946,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(13,44,'BSE223013','Anthony Grimes','Male','2022-09-01','Pakistani','68633-0520399-1','','1973-01-25','+1 (609) 691-7734','Islamabad','Punjab','24089 Zemlak Cape\nEast Jakobville, FL 52035','New Stanton','Matric','FBISE',1100,916,'FSc','Pre-Engineering','FBISE',1100,1019,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(14,45,'BSE223014','Prof. Wilbert Kerluke DDS','Male','2022-09-01','Pakistani','44865-4629983-0','','1970-06-22','540-704-2060','Islamabad','Punjab','7632 Juston Plains\nHagenesbury, VT 82338-9985','East Karolannburgh','Matric','FBISE',1100,932,'FSc','Pre-Engineering','FBISE',1100,874,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(15,46,'BSE223015','Dr. Jillian Schaefer III','Female','2022-09-01','Pakistani','27203-9262272-0','','1979-10-24','+1-732-962-1083','Islamabad','Punjab','1014 Bradly Trace\nEast Revastad, KS 30583','West Brandtfort','Matric','FBISE',1100,918,'FSc','Pre-Engineering','FBISE',1100,1008,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(16,47,'BSE223016','Deron Swift','Male','2022-09-01','Pakistani','97439-0056935-7','','1990-01-10','385-947-4378','Islamabad','Punjab','3353 Kieran Shore\nO\'Keefefort, MI 79737-1161','East Bianka','Matric','FBISE',1100,1019,'FSc','Pre-Engineering','FBISE',1100,1014,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(17,48,'BSE223017','Mr. Jabari Sipes V','Male','2022-09-01','Pakistani','79953-3644684-1','','2000-04-27','+1 (669) 751-2774','Islamabad','Punjab','178 Kling Pines Apt. 205\nSouth Hoseaton, AK 11770','Alexport','Matric','FBISE',1100,912,'FSc','Pre-Engineering','FBISE',1100,905,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(18,49,'BSE223018','Otis Nitzsche','Female','2022-09-01','Pakistani','46860-1745859-2','','1975-07-31','984.242.0866','Islamabad','Punjab','77718 Kaela Islands Suite 614\nEast Kathryn, IL 00420','Pollichton','Matric','FBISE',1100,963,'FSc','Pre-Engineering','FBISE',1100,998,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(19,50,'BSE223019','Art Corkery','Male','2022-09-01','Pakistani','30132-1502107-7','','1984-12-02','+1 (609) 349-3116','Islamabad','Punjab','603 Boyer Expressway Apt. 402\nLake Brennon, SC 61246','New Cade','Matric','FBISE',1100,968,'FSc','Pre-Engineering','FBISE',1100,808,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(20,51,'BSE223020','Troy Kozey Sr.','Male','2022-09-01','Pakistani','27689-8051809-5','','1982-01-10','+1-239-535-4194','Islamabad','Punjab','5196 Mueller Track Apt. 876\nEast Liana, PA 43117-3522','Lake Normafort','Matric','FBISE',1100,953,'FSc','Pre-Engineering','FBISE',1100,1012,'2026-04-27 05:25:02','2026-04-27 05:25:02'),(21,194,'BSE223179','Nawaz','M',NULL,NULL,'35602-1235667-1',NULL,NULL,'3105995004',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-21 14:07:39','2026-06-21 14:07:39');
/*!40000 ALTER TABLE `student_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `assigned_to_role` varchar(255) NOT NULL,
  `assigned_to_user_id` bigint(20) unsigned DEFAULT NULL,
  `assigned_by_user_id` bigint(20) unsigned NOT NULL,
  `event_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('pending','in_progress','completed','approved','rejected') DEFAULT 'pending',
  `submission_notes` text DEFAULT NULL,
  `submission_file` varchar(255) DEFAULT NULL,
  `before_image` varchar(255) DEFAULT NULL,
  `after_image` varchar(255) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `annotated_file` varchar(255) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_event_role_task` (`event_id`,`assigned_to_role`,`title`),
  KEY `tasks_assigned_to_user_id_foreign` (`assigned_to_user_id`),
  KEY `tasks_assigned_by_user_id_foreign` (`assigned_by_user_id`),
  CONSTRAINT `tasks_assigned_by_user_id_foreign` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_assigned_to_user_id_foreign` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES (1,'Design & Branding','Overall event supervision\r\nFinal decision-making\r\nCoordination with all departments\r\nEnsuring smooth execution of the event','gd',170,52,1,'approved','a','task_submissions/EWavhplicMjRoUWq5HkvAWxJiDrlSYZKNyjLNaRD.jpg',NULL,NULL,'correct the speelings','task_submissions/annotated_1_1778732765.png','2026-05-15','2026-05-13 23:20:52','2026-05-13 23:26:05'),(2,'Venue Decoration & Setup','Venue decoration planning\r\nStage setup and theme management\r\nSeating arrangement supervision\r\nEnsuring professional event atmosphere','deco',176,52,1,'approved','o',NULL,'task_submissions/hK0uwz0pdgMOHnOCHzEj6YeQgXP9SVKuudZGjiKG.png','task_submissions/QNIjOLeTc4bKqih7o0bY2bDiz92QkxhDnUzlhKF2.png',NULL,NULL,'2026-05-15','2026-05-13 23:21:44','2026-05-14 03:49:21'),(3,'Event promotion on all platforms','Event promotion on all platforms\r\nPosting updates and announcements\r\nAudience engagement and responses\r\nLive event coverage and story uploads','smt',172,52,1,'approved','ok','task_submissions/lz8EJVYpRXZOq47lHH0DhgKAxsn87wAvhQMJjMMF.png',NULL,NULL,NULL,NULL,'2026-05-15','2026-05-13 23:22:21','2026-05-13 23:27:58'),(4,'Capturing professional event photos','Capturing professional event photos\r\nCoverage of all sessions and activities\r\nEditing and organizing final photos\r\nCreating highlight photo albums','photo',173,52,1,'approved','k',NULL,NULL,NULL,NULL,NULL,'2026-05-15','2026-05-13 23:22:52','2026-05-13 23:47:43'),(5,'Design a poster of event','2 posters','gd',170,52,2,'approved','done','task_submissions/5/yzIfQgIijRwxeyC6nqAJw5M4QeqLvRsSzjS0P4yB.png',NULL,NULL,'good',NULL,'2026-05-15','2026-05-14 03:47:03','2026-05-14 03:49:09'),(6,'make a graphic poster for event','ok','gd',170,52,3,'approved','done','task_submissions/6/Z1RSmhaRTiPLOuYTTATmQarhiYfjWgtQ9DDnFFUf.png',NULL,NULL,NULL,NULL,'2026-05-15','2026-05-14 04:28:42','2026-05-14 04:31:05'),(7,'photos','done t','video',NULL,52,4,'pending',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-15','2026-05-14 05:54:29','2026-05-14 05:54:29'),(8,'photos','di','photo',173,52,4,'approved','done',NULL,NULL,NULL,NULL,NULL,'2026-05-15','2026-05-14 06:16:14','2026-05-14 06:19:03'),(9,'doc','jo','doc',175,52,4,'approved','ji','task_submissions/9/uxjeaEDz9safH44jbkWq24fid1OgGGBQAsltCavP.pdf',NULL,NULL,NULL,NULL,'2026-05-15','2026-05-14 06:17:47','2026-05-14 06:18:57'),(10,'make a poster','done it','gd',170,52,5,'approved','done','task_submissions/10/G0T4uidgHoNnLQZ5VhZ993R8UYgAJ5vUL5g6my71.png',NULL,NULL,'done',NULL,'2026-05-20','2026-05-14 08:28:04','2026-05-14 08:30:43');
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_messages`
--

DROP TABLE IF EXISTS `team_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint(20) unsigned NOT NULL,
  `sender_id` bigint(20) unsigned NOT NULL,
  `message` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `team_messages_team_id_foreign` (`team_id`),
  KEY `team_messages_sender_id_foreign` (`sender_id`),
  CONSTRAINT `team_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `team_messages_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_messages`
--

LOCK TABLES `team_messages` WRITE;
/*!40000 ALTER TABLE `team_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `team_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_user`
--

DROP TABLE IF EXISTS `team_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `is_lead` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `team_user_team_id_foreign` (`team_id`),
  KEY `team_user_user_id_foreign` (`user_id`),
  CONSTRAINT `team_user_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `team_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_user`
--

LOCK TABLES `team_user` WRITE;
/*!40000 ALTER TABLE `team_user` DISABLE KEYS */;
INSERT INTO `team_user` VALUES (1,1,40,0,'2026-04-28 10:44:18','2026-04-28 10:44:18');
/*!40000 ALTER TABLE `team_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teams` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `academic_term_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teams_academic_term_id_foreign` (`academic_term_id`),
  CONSTRAINT `teams_academic_term_id_foreign` FOREIGN KEY (`academic_term_id`) REFERENCES `academic_terms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` VALUES (1,'alpha','graphics',1,'2026-04-28 10:44:03','2026-04-28 10:44:03');
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reg_id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student','president','sa','vc','gd','faculty','smt','doc','photo','video','deco') NOT NULL DEFAULT 'student',
  `is_volunteer_pool` tinyint(1) NOT NULL DEFAULT 0,
  `digital_signature` varchar(255) DEFAULT NULL,
  `digital_stamp` varchar(255) DEFAULT NULL,
  `password_changed` tinyint(1) NOT NULL DEFAULT 0,
  `current_term_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cnic` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `mailing_address` varchar(255) DEFAULT NULL,
  `academic_rank` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `current_semester` varchar(255) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_reg_id_unique` (`reg_id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_current_term_id_foreign` (`current_term_id`),
  CONSTRAINT `users_current_term_id_foreign` FOREIGN KEY (`current_term_id`) REFERENCES `academic_terms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'ADMIN-001','System Administrator','admin@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','admin',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'FAC-ASS-1435','Dr. Miss Zoey Herzog','miss.zoey.herzog@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'FAC-ASS-2252','Dr. Ebony Langworth','ebony.langworth@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'FAC-ASS-3625','Dr. Mustafa Lang IV','mustafa.lang.iv@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'FAC-ASS-4775','Dr. Bennett Pfeffer I','bennett.pfeffer.i@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,'FAC-ASS-5238','Dr. Prof. Art Tromp','prof..art.tromp@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'FAC-ASS-6606','Dr. Joey Predovic','joey.predovic@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'FAC-PRO-1468','Dr. Katelynn Hintz','katelynn.hintz@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'FAC-PRO-2298','Dr. Magdalena Ruecker','magdalena.ruecker@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,'FAC-PRO-3763','Dr. Violet Buckridge DVM','violet.buckridge.dvm@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,'FAC-PRO-4713','Dr. Tanner Rosenbaum','tanner.rosenbaum@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,'FAC-PRO-5583','Dr. Dr. Lane Fahey III','dr..lane.fahey.iii@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,'FAC-PRO-6223','Dr. Mrs. Viva Swaniawski I','mrs..viva.swaniawski.i@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,'FAC-ASS-1804','Jordan Herman','jordan.herman@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,'FAC-ASS-2776','Caroline Brown DVM','caroline.brown.dvm@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,'FAC-ASS-3178','Miss Raegan Runolfsdottir III','miss.raegan.runolfsdottir.iii@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,'FAC-ASS-4913','Josefa Wyman','josefa.wyman@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,'FAC-ASS-5112','Aron Howe','aron.howe@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,'FAC-ASS-6208','Ms. Anahi Gutkowski II','ms..anahi.gutkowski.ii@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,'FAC-LEC-1364','Merritt Orn II','merritt.orn.ii@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,'FAC-LEC-2270','Miss Antonetta White I','miss.antonetta.white.i@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,'FAC-LEC-3349','Yoshiko Collins Jr.','yoshiko.collins.jr.@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,'FAC-LEC-4444','Prof. Ruben Strosin MD','prof..ruben.strosin.md@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,'FAC-LEC-5267','Mauricio Goodwin MD','mauricio.goodwin.md@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,'FAC-LEC-6132','Lola Hagenes IV','lola.hagenes.iv@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,'FAC-LAB-1833','Marcelina Lebsack','marcelina.lebsack@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,'FAC-LAB-2849','Dr. Davin Torphy','dr..davin.torphy@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,'FAC-LAB-3471','Delaney Paucek','delaney.paucek@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,'FAC-LAB-4320','Dock Robel','dock.robel@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,'FAC-LAB-5937','Casper Block II','casper.block.ii@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,'FAC-LAB-6183','Prof. Ricardo Harris','prof..ricardo.harris@cause.edu.pk',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','faculty',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,'BSE223001','Muhammad Ahmed','ahmed@cause.com',NULL,'$2y$12$a70x.G49/pfQml6ggbFtvudPItq2S3D36.ROgoFYw.uRuYkkRZQCa','student',1,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-05-13 19:46:55',NULL,NULL,NULL,NULL,NULL,NULL,'Management, Leadership',NULL),(33,'BSE223002','Fatima Zahra','fatima@cause.com',NULL,'$2y$12$zqkkrXZ4BvV42Hfj1Vng6OgfZ7A9qLg7j9xp.IIZywv13bMHLrIte','student',1,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-05-13 19:46:56',NULL,NULL,NULL,NULL,NULL,NULL,'Documentation, Social Media',NULL),(34,'BSE223003','Ali Raza','ali@cause.com',NULL,'$2y$12$.hMmeYBIZUYOz0jk9cycrOhHj6HzOqJt08zupRMGjZs2X9hVq/EHm','student',1,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-05-13 19:46:56',NULL,NULL,NULL,NULL,NULL,NULL,'Technical Support, AV',NULL),(35,'BSE223004','Zainab Bibi','zainab@cause.com',NULL,'$2y$12$ZK2xfspu7ftaz4L7fjM3L.Z9v6ClAzgBkfE6.CQaVel0fVKru00zO','student',1,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-05-13 19:46:57',NULL,NULL,NULL,NULL,NULL,NULL,'Decoration, Creative',NULL),(36,'BSE223005','Umar Farooq','umar@cause.com',NULL,'$2y$12$dFoKHYF1FjTf5/YOKzePJ.l1LHOpJuaT3eW0PVTj2dFMNZ4Do5uu.','student',1,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-05-13 19:46:58',NULL,NULL,NULL,NULL,NULL,NULL,'Security, Logistics',NULL),(37,'BSE223006','Ayesha Siddiqua','ayesha@cause.com',NULL,'$2y$12$INZfqac42zjI7NRb3yYcfuPclhGYMMzJybRhczUxrufftdGNVaIZ2','student',1,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-05-13 19:46:58',NULL,NULL,NULL,NULL,NULL,NULL,'Photography, Content',NULL),(38,'BSE223007','Hassan Khan','hassan@cause.com',NULL,'$2y$12$NQb0J4/TWybDi4u7rD9Ho.TSxjxtPxMU/WXSc3CvnzMNAut3iGEPG','student',1,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-05-13 19:46:59',NULL,NULL,NULL,NULL,NULL,NULL,'Graphic Design, Editing',NULL),(39,'BSE223008','Bilal Ahmed','bilal@cause.com',NULL,'$2y$12$OLlApOeyO6XSqihMCsZfHeZSyVMnfYLr4Hf6WXlrQjoT85ZBD5mEy','student',1,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-05-13 19:46:59',NULL,NULL,NULL,NULL,NULL,NULL,'Finance, Planning',NULL),(40,'BSE223009','Mr. Alexis Kuvalis V','mr..alexis.kuvalis.v@gmail.com',NULL,'$2y$12$1ycqj5OCAGp7cjpOT97BsONDCqS1VmnIqmJTQIViQ/LJusi2PoqGW','student',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-05-13 19:46:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(41,'BSE223010','Arnulfo Bradtke Jr.','arnulfo.bradtke.jr.@gmail.com',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','student',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-05-13 19:46:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(42,'BSE223011','Miss Araceli Baumbach','miss.araceli.baumbach@gmail.com',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','student',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(43,'BSE223012','Eli Moore','eli.moore@gmail.com',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','student',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(44,'BSE223013','Mr. Manuel Lubowitz Sr.','mr..manuel.lubowitz.sr.@gmail.com',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','student',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(45,'BSE223014','Vickie Weissnat MD','vickie.weissnat.md@gmail.com',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','student',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(46,'BSE223015','Gaetano Haag','gaetano.haag@gmail.com',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','student',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(47,'BSE223016','Johnnie Simonis','johnnie.simonis@gmail.com',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','student',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(48,'BSE223017','Myrna Gerlach','myrna.gerlach@gmail.com',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','student',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(49,'BSE223018','Elisha Wiza','elisha.wiza@gmail.com',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','student',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(50,'BSE223019','Prof. Lilian Schmitt','prof..lilian.schmitt@gmail.com',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','student',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(51,'BSE223020','Prof. Einar Witting MD','prof..einar.witting.md@gmail.com',NULL,'$2y$12$q1HmufjmtmYo1603NYLV.u1NJmHgjWSGTSQHHX7xdaJzzX7yvRUI2','student',0,NULL,NULL,1,1,'2026-04-27 05:25:02','2026-04-27 05:25:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(52,'PRES-001','Sarah Ahmed','president@cause.edu.pk','profile_pictures/SfxyJidEz6SxQArOpB9s4A6DsjYOmreUPgyjWVMR.png','$2y$12$1HkLcat9mzwGX1dkfUJslOqVEkuxeBDP5D0kioAFCEybrQ21j.qpa','president',0,NULL,NULL,1,1,'2026-04-28 10:42:36','2026-06-17 13:35:19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(53,'BSE223365','Zaid Butt','zaid.butt.75@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','38771-3038515-2','0303-2695069',NULL,NULL,'Zain Ali',NULL,NULL,NULL),(54,'BSE223698','Muhammad Mirza','muhammad.mirza.13@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','38719-4775126-9','0324-2735370',NULL,NULL,'Zaid Bajwa',NULL,NULL,NULL),(55,'BSE223249','Sara Warraich','sara.warraich.58@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','34887-7455359-5','0308-6352980',NULL,NULL,'Ahmed Ghuman',NULL,NULL,NULL),(56,'BSE223733','Mustafa Mirza','mustafa.mirza.61@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','39154-3915523-2','0346-4790947',NULL,NULL,'Hassan Qureshi',NULL,NULL,NULL),(57,'BSE223761','Imran Ahmed','imran.ahmed.53@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','31628-1475758-4','0344-8813378',NULL,NULL,'Muhammad Virk',NULL,NULL,NULL),(58,'BSE223207','Zainab Malik','zainab.malik.25@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','36524-1946504-2','0343-5304678',NULL,NULL,'Zaid Malik',NULL,NULL,NULL),(59,'BSE223067','Hassan Bajwa','hassan.bajwa.52@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','38919-4962848-8','0332-4558998',NULL,NULL,'Imran Raja',NULL,NULL,NULL),(60,'BSE223891','Imran Syed','imran.syed.96@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','33305-5784431-3','0310-6034717',NULL,NULL,'Ahmed Jat',NULL,NULL,NULL),(61,'BSE223934','Khadija Ali','khadija.ali.91@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','39357-9152326-7','0326-5223955',NULL,NULL,'Muhammad Jat',NULL,NULL,NULL),(62,'BSE223940','Zoya Butt','zoya.butt.19@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','32396-6643932-1','0345-3085194',NULL,NULL,'Zain Butt',NULL,NULL,NULL),(63,'BSE231211','Sana Butt','sana.butt.57@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','35995-2765749-3','0342-9226355',NULL,NULL,'Abdullah Qureshi',NULL,NULL,NULL),(64,'BSE231420','Zainab Butt','zainab.butt.13@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','38392-1314334-3','0339-7944783',NULL,NULL,'Hamza Khan',NULL,NULL,NULL),(65,'BSE231223','Anum Warraich','anum.warraich.64@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','34756-4812091-7','0328-3825568',NULL,NULL,'Abdullah Jat',NULL,NULL,NULL),(66,'BSE231075','Sana Ali','sana.ali.59@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','32710-4272188-1','0348-1735311',NULL,NULL,'Arsalan Khan',NULL,NULL,NULL),(67,'BSE231993','Zoya Ahmed','zoya.ahmed.48@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','37852-2284654-2','0339-8263461',NULL,NULL,'Ahmed Bajwa',NULL,NULL,NULL),(68,'BSE231931','Imran Khan','imran.khan.29@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','34361-3634885-7','0303-8117879',NULL,NULL,'Ali Syed',NULL,NULL,NULL),(69,'BSE231401','Sara Mirza','sara.mirza.83@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','37673-4939329-9','0342-5310394',NULL,NULL,'Bilal Ahmed',NULL,NULL,NULL),(70,'BSE231768','Arsalan Bajwa','arsalan.bajwa.30@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','30131-4409842-5','0326-6076627',NULL,NULL,'Mustafa Malik',NULL,NULL,NULL),(71,'BSE231347','Zoya Qureshi','zoya.qureshi.92@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','31286-6844406-3','0328-4834944',NULL,NULL,'Hamza Syed',NULL,NULL,NULL),(72,'BSE231892','Khadija Warraich','khadija.warraich.94@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','33923-4716382-6','0328-8285295',NULL,NULL,'Hassan Mirza',NULL,NULL,NULL),(73,'BSE233858','Umar Mirza','umar.mirza.40@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','30192-3136722-4','0302-4138359',NULL,NULL,'Muhammad Ahmed',NULL,NULL,NULL),(74,'BSE233078','Imran Raja','imran.raja.16@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','39105-8755080-2','0313-4441478',NULL,NULL,'Umar Ahmed',NULL,NULL,NULL),(75,'BSE233742','Hira Ahmed','hira.ahmed.20@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','34382-8782044-7','0333-5476045',NULL,NULL,'Bilal Jat',NULL,NULL,NULL),(76,'BSE233888','Zaid Butt','zaid.butt.17@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','39320-3324601-8','0337-1071926',NULL,NULL,'Abdullah Raja',NULL,NULL,NULL),(77,'BSE233332','Anum Virk','anum.virk.67@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','38095-3195262-2','0321-1128774',NULL,NULL,'Faisal Virk',NULL,NULL,NULL),(78,'BSE233210','Maha Qureshi','maha.qureshi.23@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','37869-8718604-1','0304-7112904',NULL,NULL,'Ahmed Raja',NULL,NULL,NULL),(79,'BSE233010','Anum Qureshi','anum.qureshi.63@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','33859-2441878-2','0342-9371055',NULL,NULL,'Faisal Syed',NULL,NULL,NULL),(80,'BSE233310','Saba Sheikh','saba.sheikh.44@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','36600-6310750-7','0348-3293089',NULL,NULL,'Usman Butt',NULL,NULL,NULL),(81,'BSE233700','Iqra Ghuman','iqra.ghuman.76@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','37876-6127880-7','0343-7141139',NULL,NULL,'Mustafa Qureshi',NULL,NULL,NULL),(82,'BSE233615','Bilal Khan','bilal.khan.77@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','35709-9086831-2','0305-7147646',NULL,NULL,'Zaid Mirza',NULL,NULL,NULL),(83,'BSE241575','Maha Malik','maha.malik.67@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','39639-9249349-3','0319-3284293',NULL,NULL,'Mustafa Ahmed',NULL,NULL,NULL),(84,'BSE241607','Abdullah Raja','abdullah.raja.19@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','34489-3113906-6','0304-7875110',NULL,NULL,'Mustafa Sheikh',NULL,NULL,NULL),(85,'BSE241685','Mustafa Khan','mustafa.khan.34@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','38369-1400799-5','0333-2983867',NULL,NULL,'Hassan Jat',NULL,NULL,NULL),(86,'BSE241406','Fatima Syed','fatima.syed.78@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','39751-6936297-2','0311-1263929',NULL,NULL,'Umar Syed',NULL,NULL,NULL),(87,'BSE241732','Arsalan Raja','arsalan.raja.99@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','31394-1633598-1','0342-9694076',NULL,NULL,'Zain Mirza',NULL,NULL,NULL),(88,'BSE241942','Bilal Warraich','bilal.warraich.51@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','38566-9007165-4','0333-9360518',NULL,NULL,'Bilal Qureshi',NULL,NULL,NULL),(89,'BSE241256','Bilal Khan','bilal.khan.11@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','37578-3837264-2','0329-2878719',NULL,NULL,'Zaid Qureshi',NULL,NULL,NULL),(90,'BSE241054','Zaid Ghuman','zaid.ghuman.68@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','39807-3153995-9','0328-9291147',NULL,NULL,'Imran Sheikh',NULL,NULL,NULL),(91,'BSE241233','Muhammad Ghuman','muhammad.ghuman.99@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','39623-2172332-2','0325-5737160',NULL,NULL,'Zaid Jat',NULL,NULL,NULL),(92,'BSE241041','Saba Jat','saba.jat.96@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','30150-5169607-3','0300-5909315',NULL,NULL,'Umar Malik',NULL,NULL,NULL),(93,'BSE243942','Amna Syed','amna.syed.63@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','30020-3247405-9','0302-3715880',NULL,NULL,'Faisal Malik',NULL,NULL,NULL),(94,'BSE243845','Hamza Sheikh','hamza.sheikh.80@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','32366-4240104-7','0307-5260433',NULL,NULL,'Zaid Butt',NULL,NULL,NULL),(95,'BSE243988','Umar Ahmed','umar.ahmed.11@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','33061-3789736-6','0310-8812133',NULL,NULL,'Umar Virk',NULL,NULL,NULL),(96,'BSE243030','Anum Malik','anum.malik.28@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','31784-7965128-6','0314-3595810',NULL,NULL,'Mustafa Bajwa',NULL,NULL,NULL),(97,'BSE243747','Sara Syed','sara.syed.78@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','37198-6423792-3','0335-6818861',NULL,NULL,'Mustafa Raja',NULL,NULL,NULL),(98,'BSE243946','Usman Sheikh','usman.sheikh.39@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','32220-6314761-2','0327-9359716',NULL,NULL,'Mustafa Syed',NULL,NULL,NULL),(99,'BSE243160','Zainab Malik','zainab.malik.31@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','33176-6302530-3','0339-1881656',NULL,NULL,'Imran Jat',NULL,NULL,NULL),(100,'BSE243353','Ali Sheikh','ali.sheikh.24@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','38622-2600692-9','0310-6134696',NULL,NULL,'Imran Ali',NULL,NULL,NULL),(101,'BSE243404','Maryam Ali','maryam.ali.77@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','30313-8701642-4','0348-6850260',NULL,NULL,'Umar Raja',NULL,NULL,NULL),(102,'BSE243158','Ahmed Ahmed','ahmed.ahmed.87@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','36810-6919348-1','0304-1213591',NULL,NULL,'Faisal Warraich',NULL,NULL,NULL),(103,'BSE251667','Ali Raja','ali.raja.88@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','31366-3770377-3','0311-2065372',NULL,NULL,'Faisal Mirza',NULL,NULL,NULL),(104,'BSE251826','Bilal Ghuman','bilal.ghuman.22@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','31490-4487485-8','0329-4410003',NULL,NULL,'Usman Ghuman',NULL,NULL,NULL),(105,'BSE251143','Zain Bajwa','zain.bajwa.29@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','37065-4037610-2','0347-9284037',NULL,NULL,'Ahmed Ghuman',NULL,NULL,NULL),(106,'BSE251805','Arsalan Ahmed','arsalan.ahmed.18@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','38995-4500269-1','0345-3625524',NULL,NULL,'Umar Malik',NULL,NULL,NULL),(107,'BSE251369','Hira Warraich','hira.warraich.83@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','36614-7987593-6','0339-3854451',NULL,NULL,'Ahmed Butt',NULL,NULL,NULL),(108,'BSE251368','Sana Mirza','sana.mirza.96@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','37401-6006351-1','0310-7747378',NULL,NULL,'Usman Mirza',NULL,NULL,NULL),(109,'BSE251503','Saba Sheikh','saba.sheikh.32@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','34780-4709480-2','0338-6037524',NULL,NULL,'Mustafa Butt',NULL,NULL,NULL),(110,'BSE251148','Umar Bajwa','umar.bajwa.30@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','37115-6861992-5','0323-1244410',NULL,NULL,'Muhammad Jat',NULL,NULL,NULL),(111,'BSE251715','Sara Malik','sara.malik.22@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','31623-3607589-6','0300-1004314',NULL,NULL,'Usman Raja',NULL,NULL,NULL),(112,'BSE251408','Nida Malik','nida.malik.58@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:15','2026-05-14 05:19:21','34923-6570706-7','0342-5729847',NULL,NULL,'Arsalan Raja',NULL,NULL,NULL),(113,'BSE253380','Zoya Mirza','zoya.mirza.87@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-05-14 05:19:21','33664-8701341-2','0318-2513137',NULL,NULL,'Hassan Mirza',NULL,NULL,NULL),(114,'BSE253521','Aisha Ahmed','aisha.ahmed.32@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-05-14 05:19:21','31296-4070925-6','0338-8687812',NULL,NULL,'Bilal Butt',NULL,NULL,NULL),(115,'BSE253733','Sara Syed','sara.syed.18@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-05-14 05:19:21','30827-9133937-6','0335-8036282',NULL,NULL,'Ali Warraich',NULL,NULL,NULL),(116,'BSE253007','Hamza Sheikh','hamza.sheikh.43@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-05-14 05:19:21','32143-4664486-4','0319-7879912',NULL,NULL,'Usman Raja',NULL,NULL,NULL),(117,'BSE253398','Saba Qureshi','saba.qureshi.43@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-05-14 05:19:21','39330-9481821-3','0349-3575656',NULL,NULL,'Ali Ghuman',NULL,NULL,NULL),(118,'BSE253737','Sana Virk','sana.virk.78@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',1,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-05-14 05:19:21','32958-4003642-7','0322-9772510',NULL,NULL,'Bilal Ali',NULL,NULL,NULL),(119,'BSE253359','Umar Virk','umar.virk.47@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-05-14 05:19:21','34572-3879567-4','0310-1325651',NULL,NULL,'Ali Mirza',NULL,NULL,NULL),(120,'BSE253865','Usman Ghuman','usman.ghuman.84@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-05-14 05:19:21','36806-2428847-3','0339-1792350',NULL,NULL,'Hassan Qureshi',NULL,NULL,NULL),(121,'BSE253779','Aisha Syed','aisha.syed.91@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-05-14 05:19:21','34418-2445878-4','0319-6948529',NULL,NULL,'Hassan Khan',NULL,NULL,NULL),(122,'BSE253853','Hamza Sheikh','hamza.sheikh.58@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-05-14 05:19:21','36009-6215440-1','0332-5119493',NULL,NULL,'Ahmed Qureshi',NULL,NULL,NULL),(123,'BSE261389','Ahmed Raja','ahmed.raja.67@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-04-28 12:31:16','34552-4287964-1','0321-3746734',NULL,NULL,'Hassan Bajwa',NULL,NULL,NULL),(124,'BSE261403','Mustafa Malik','mustafa.malik.53@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-04-28 12:31:16','39159-9900579-7','0318-7297471',NULL,NULL,'Usman Virk',NULL,NULL,NULL),(125,'BSE261711','Sana Bajwa','sana.bajwa.16@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-04-28 12:31:16','38538-3421624-8','0316-2190223',NULL,NULL,'Hamza Qureshi',NULL,NULL,NULL),(126,'BSE261079','Maryam Virk','maryam.virk.29@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-04-28 12:31:16','35764-7859266-7','0317-5801851',NULL,NULL,'Umar Mirza',NULL,NULL,NULL),(127,'BSE261537','Aisha Sheikh','aisha.sheikh.56@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-04-28 12:31:16','30325-8921320-1','0341-7094233',NULL,NULL,'Imran Khan',NULL,NULL,NULL),(128,'BSE261960','Ahmed Jat','ahmed.jat.30@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-04-28 12:31:16','38372-4039427-2','0333-9158042',NULL,NULL,'Zaid Khan',NULL,NULL,NULL),(129,'BSE261402','Anum Syed','anum.syed.14@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-04-28 12:31:16','33803-6360694-9','0312-5025008',NULL,NULL,'Abdullah Sheikh',NULL,NULL,NULL),(130,'BSE261872','Maha Ali','maha.ali.89@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-04-28 12:31:16','35408-5499893-3','0307-7419119',NULL,NULL,'Zain Ahmed',NULL,NULL,NULL),(131,'BSE261477','Hira Khan','hira.khan.70@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-04-28 12:31:16','39925-3275167-4','0344-3371248',NULL,NULL,'Bilal Sheikh',NULL,NULL,NULL),(132,'BSE261193','Anum Bajwa','anum.bajwa.68@example.com',NULL,'$2y$12$nWJu3pybrr0YdByiH/w.vOoop.BVlgHGmN7MSYPpY.sePR47yKEgC','student',0,NULL,NULL,1,1,'2026-04-28 12:31:16','2026-04-28 12:31:16','34705-2029531-6','0315-6993269',NULL,NULL,'Hamza Sheikh',NULL,NULL,NULL),(133,'BFE22001','Hassan Farooqi','hassan.farooqi.927@cause.edu.pk','profile_pictures/iQa4d51FlVTLEBCLcgytyrT40ZqB0FEs7iZqbPwq.png','$2y$12$1HkLcat9mzwGX1dkfUJslOqVEkuxeBDP5D0kioAFCEybrQ21j.qpa','faculty',0,'signatures/PKX3a3ppArceTDt4bawRwrAMbUZtJDebZG9xTndD.png',NULL,1,1,'2026-04-28 12:49:41','2026-06-21 15:24:11','34104-3501652-3','0331-3813653',NULL,NULL,'Umar Sheikh',NULL,NULL,NULL),(134,'FACPR002','Hassan Qureshi','hassan.qureshi.873@cause.edu.pk',NULL,'$2y$12$1HkLcat9mzwGX1dkfUJslOqVEkuxeBDP5D0kioAFCEybrQ21j.qpa','faculty',0,'signatures/uEARR1B1y4MeAddeDDcmhQ96HFehkRw7KK9qMMW5.png','stamps/4XZva31t8Q67UMuEBJdbiszc6WQeE7xkmhSCFJmP.png',1,1,'2026-04-28 12:49:41','2026-06-17 13:35:19','38817-3106347-7','0339-1780933',NULL,NULL,'Mustafa Hashmi',NULL,NULL,NULL),(135,'FACPR003','Hamza Malik','hamza.malik.977@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','30082-7619675-3','0338-1205556',NULL,NULL,'Bilal Qureshi',NULL,NULL,NULL),(136,'FACPR004','Amna Ali','amna.ali.500@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','36650-5474680-6','0306-9299447',NULL,NULL,'Ahmed Qureshi',NULL,NULL,NULL),(137,'FACPR005','Amna Khan','amna.khan.681@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','36783-1075373-1','0335-8334606',NULL,NULL,'Abdullah Qureshi',NULL,NULL,NULL),(138,'FACAS006','Farhana Farooqi','farhana.farooqi.418@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','32084-7031237-2','0331-2420086',NULL,NULL,'Muhammad Mirza',NULL,NULL,NULL),(139,'FACAS007','Zaid Naqvi','zaid.naqvi.710@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','36689-9659277-8','0348-2808866',NULL,NULL,'Zaid Farooqi',NULL,NULL,NULL),(140,'FACAS008','Farooq Syed','farooq.syed.431@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','32167-2269876-3','0319-7069335',NULL,NULL,'Zaid Ansari',NULL,NULL,NULL),(141,'FACAS009','Sana Naqvi','sana.naqvi.249@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','33206-8560634-1','0309-9957464',NULL,NULL,'Mustafa Sheikh',NULL,NULL,NULL),(142,'FACAS010','Sana Farooqi','sana.farooqi.906@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','31742-1404106-6','0313-1726668',NULL,NULL,'Mustafa Syed',NULL,NULL,NULL),(143,'FACPR011','Farhana Syed','farhana.syed.146@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','37279-3282822-1','0312-3436810',NULL,NULL,'Usman Naqvi',NULL,NULL,NULL),(144,'FACPR012','Tahira Gazaali','tahira.gazaali.403@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','34483-1088773-7','0315-7747911',NULL,NULL,'Rashid Ansari',NULL,NULL,NULL),(145,'FACPR013','Tahira Khan','tahira.khan.583@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','36381-3736825-3','0302-9555561',NULL,NULL,'Ali Khan',NULL,NULL,NULL),(146,'FACPR014','Umar Khan','umar.khan.727@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','38564-7007098-5','0309-6768798',NULL,NULL,'Zain Khan',NULL,NULL,NULL),(147,'FACPR015','Aisha Qureshi','aisha.qureshi.250@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','33905-9848485-6','0331-2353947',NULL,NULL,'Zain Khan',NULL,NULL,NULL),(148,'FACSE016','Farooq Ansari','farooq.ansari.434@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','39226-9568028-4','0327-3271858',NULL,NULL,'Hamza Raja',NULL,NULL,NULL),(149,'FACSE017','Ali Ahmed','ali.ahmed.619@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','35039-3100987-8','0305-3490977',NULL,NULL,'Umar Raja',NULL,NULL,NULL),(150,'FACSE018','Farooq Siddiqui','farooq.siddiqui.114@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','30328-8836573-1','0349-5634457',NULL,NULL,'Bilal Farooqi',NULL,NULL,NULL),(151,'FACSE019','Abdullah Gazaali','abdullah.gazaali.853@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','38748-9926421-5','0305-5317248',NULL,NULL,'Zain Qureshi',NULL,NULL,NULL),(152,'FACSE020','Mustafa Siddiqui','mustafa.siddiqui.487@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','30286-6093127-2','0313-6498431',NULL,NULL,'Abdullah Khan',NULL,NULL,NULL),(153,'FACLE021','Khadija Sheikh','khadija.sheikh.513@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','37771-4730528-6','0302-5685448',NULL,NULL,'Hassan Sheikh',NULL,NULL,NULL),(154,'FACLE022','Mustafa Hashmi','mustafa.hashmi.752@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','36120-1334736-9','0345-9548200',NULL,NULL,'Hamza Khan',NULL,NULL,NULL),(155,'FACLE023','Ahmed Naqvi','ahmed.naqvi.502@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','30119-7045264-1','0310-6387599',NULL,NULL,'Hamza Ahmed',NULL,NULL,NULL),(156,'FACLE024','Hamza Siddiqui','hamza.siddiqui.962@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','32186-3768707-9','0300-6547012',NULL,NULL,'Bilal Malik',NULL,NULL,NULL),(157,'FACLE025','Uzma Gazaali','uzma.gazaali.604@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','37925-9737290-7','0305-2657558',NULL,NULL,'Abdullah Khan',NULL,NULL,NULL),(158,'FACAS026','Maryam Raja','maryam.raja.277@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','34228-3958352-9','0336-2145255',NULL,NULL,'Muhammad Malik',NULL,NULL,NULL),(159,'FACAS027','Bilal Khan','bilal.khan.630@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','33765-2840803-7','0306-5434937',NULL,NULL,'Imran Malik',NULL,NULL,NULL),(160,'FACAS028','Hamza Raja','hamza.raja.108@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','36411-9855727-4','0335-2812978',NULL,NULL,'Muhammad Mirza',NULL,NULL,NULL),(161,'FACAS029','Muhammad Hashmi','muhammad.hashmi.594@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','34979-7579212-1','0323-2031776',NULL,NULL,'Imran Malik',NULL,NULL,NULL),(162,'FACAS030','Muhammad Hashmi','muhammad.hashmi.857@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','36489-1541430-9','0337-3660311',NULL,NULL,'Muhammad Siddiqui',NULL,NULL,NULL),(163,'FACLA031','Ali Qureshi','ali.qureshi.450@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','37249-1420517-9','0308-8785819',NULL,NULL,'Hassan Khan',NULL,NULL,NULL),(164,'FACLA032','Hassan Sheikh','hassan.sheikh.763@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','33548-8116965-1','0342-2048087',NULL,NULL,'Hamza Mirza',NULL,NULL,NULL),(165,'FACLA033','Sana Gazaali','sana.gazaali.488@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','38250-3866728-8','0313-8908553',NULL,NULL,'Mustafa Syed',NULL,NULL,NULL),(166,'FACLA034','Sara Farooqi','sara.farooqi.166@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','37519-6317415-1','0320-4249236',NULL,NULL,'Muhammad Raja',NULL,NULL,NULL),(167,'FACLA035','Imran Ali','imran.ali.593@cause.edu.pk',NULL,'$2y$12$yhekYF8HCRm9R7EXV.ruKeFB7RUsBkx1S0j/jUU3IxjPB4AxtBqiy','faculty',0,NULL,NULL,1,1,'2026-04-28 12:49:41','2026-05-14 05:19:21','37676-6178180-4','0315-9090894',NULL,NULL,'Ali Naqvi',NULL,NULL,NULL),(168,'STU-001','Student Affairs','stu-001@cause.edu.pk',NULL,'$2y$12$1HkLcat9mzwGX1dkfUJslOqVEkuxeBDP5D0kioAFCEybrQ21j.qpa','sa',0,NULL,NULL,1,1,'2026-04-28 14:16:53','2026-06-17 13:35:19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(169,'SA-001','Student Affairs Admin','sa@cause.edu.pk',NULL,'$2y$12$1HkLcat9mzwGX1dkfUJslOqVEkuxeBDP5D0kioAFCEybrQ21j.qpa','sa',0,NULL,NULL,1,1,'2026-04-28 14:16:53','2026-06-17 13:35:19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(170,'BSE223101','John Graphic','gd@cause.com',NULL,'$2y$12$1HkLcat9mzwGX1dkfUJslOqVEkuxeBDP5D0kioAFCEybrQ21j.qpa','gd',0,NULL,NULL,1,1,'2026-04-29 14:35:31','2026-06-17 13:35:19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(171,'BSE223102','Sarah Volunteer','vc@cause.com',NULL,'$2y$12$1HkLcat9mzwGX1dkfUJslOqVEkuxeBDP5D0kioAFCEybrQ21j.qpa','vc',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-06-17 13:35:19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(172,'BSE223103','Ali Social','smt@cause.com',NULL,'$2y$12$1HkLcat9mzwGX1dkfUJslOqVEkuxeBDP5D0kioAFCEybrQ21j.qpa','smt',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-06-17 13:35:19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(173,'BSE223104','Zain Photo','photo@cause.com',NULL,'$2y$12$1HkLcat9mzwGX1dkfUJslOqVEkuxeBDP5D0kioAFCEybrQ21j.qpa','photo',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-06-17 13:35:19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(174,'BSE223105','Aisha Video','video@cause.com',NULL,'$2y$12$1HkLcat9mzwGX1dkfUJslOqVEkuxeBDP5D0kioAFCEybrQ21j.qpa','video',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-06-17 13:35:19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(175,'BSE223106','Hassan Doc','doc@cause.com',NULL,'$2y$12$1HkLcat9mzwGX1dkfUJslOqVEkuxeBDP5D0kioAFCEybrQ21j.qpa','doc',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-06-17 13:35:19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(176,'BSE223107','Fatima Deco','deco@cause.com',NULL,'$2y$12$1HkLcat9mzwGX1dkfUJslOqVEkuxeBDP5D0kioAFCEybrQ21j.qpa','deco',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-06-17 13:35:19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(177,'BSE223108','Bilal GenSec','sa@cause.com',NULL,'$2y$12$1HkLcat9mzwGX1dkfUJslOqVEkuxeBDP5D0kioAFCEybrQ21j.qpa','sa',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-06-17 13:35:19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(178,'BSE223151','Student Volunteer 1','volunteer1@cause.com',NULL,'$2y$12$pstwl/dr5fCCMxgTCgdaNeG/c1ASlKgteUgIfDHfAxW45MS0g3El.','student',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-05-13 19:46:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(179,'BSE223152','Student Volunteer 2','volunteer2@cause.com',NULL,'$2y$12$pstwl/dr5fCCMxgTCgdaNeG/c1ASlKgteUgIfDHfAxW45MS0g3El.','student',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-05-13 19:46:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(180,'BSE223153','Student Volunteer 3','volunteer3@cause.com',NULL,'$2y$12$pstwl/dr5fCCMxgTCgdaNeG/c1ASlKgteUgIfDHfAxW45MS0g3El.','student',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-05-13 19:46:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(181,'BSE223154','Student Volunteer 4','volunteer4@cause.com',NULL,'$2y$12$pstwl/dr5fCCMxgTCgdaNeG/c1ASlKgteUgIfDHfAxW45MS0g3El.','student',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-05-13 19:46:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(182,'BSE223155','Student Volunteer 5','volunteer5@cause.com',NULL,'$2y$12$pstwl/dr5fCCMxgTCgdaNeG/c1ASlKgteUgIfDHfAxW45MS0g3El.','student',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-05-13 19:46:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(183,'BSE223201','Normal Student 1','student1@cause.com',NULL,'$2y$12$pstwl/dr5fCCMxgTCgdaNeG/c1ASlKgteUgIfDHfAxW45MS0g3El.','student',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-05-13 19:46:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(184,'BSE223202','Normal Student 2','student2@cause.com',NULL,'$2y$12$pstwl/dr5fCCMxgTCgdaNeG/c1ASlKgteUgIfDHfAxW45MS0g3El.','student',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-05-13 19:46:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(185,'BSE223203','Normal Student 3','student3@cause.com',NULL,'$2y$12$pstwl/dr5fCCMxgTCgdaNeG/c1ASlKgteUgIfDHfAxW45MS0g3El.','student',0,NULL,NULL,1,1,'2026-04-29 14:37:31','2026-04-29 15:09:16',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(193,'BSE223401','Ali fd','alimansoormirza29@gmail.com',NULL,'$2y$12$YQGNwTuKs6.8rF8K8WArPOMLdrCEHbkWmVjY8vfGsYybMdoLuyVhG','student',0,NULL,NULL,1,NULL,'2026-05-19 23:15:23','2026-05-19 23:59:47','35402-3234567-1','3001234567',NULL,NULL,'Ahmad d fwlKhan',NULL,NULL,NULL),(194,'BSE223179','zain nawaz','vlogsandreactions786@gmail.com',NULL,'$2y$12$j5sBc.Me1MW4KLIszZLoLehmRSNfFw.gIHz8Vv4hY/miFMyeXXKj2','student',0,NULL,NULL,1,1,'2026-06-18 00:49:06','2026-06-24 02:13:05','35602-1235667-1','3105995004',NULL,NULL,'Nawaz',NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `volunteers`
--

DROP TABLE IF EXISTS `volunteers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `volunteers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'assigned',
  `role_description` text DEFAULT NULL,
  `assigned_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `volunteers_event_id_user_id_unique` (`event_id`,`user_id`),
  KEY `volunteers_user_id_foreign` (`user_id`),
  KEY `volunteers_assigned_by_foreign` (`assigned_by`),
  CONSTRAINT `volunteers_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `volunteers_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `volunteers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `volunteers`
--

LOCK TABLES `volunteers` WRITE;
/*!40000 ALTER TABLE `volunteers` DISABLE KEYS */;
/*!40000 ALTER TABLE `volunteers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `candidate_id` bigint(20) unsigned NOT NULL,
  `term_id` bigint(20) unsigned NOT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_vote` (`student_id`,`term_id`),
  KEY `votes_candidate_id_foreign` (`candidate_id`),
  KEY `votes_term_id_foreign` (`term_id`),
  CONSTRAINT `votes_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `candidate_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `votes_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `votes_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `academic_terms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `votes`
--

LOCK TABLES `votes` WRITE;
/*!40000 ALTER TABLE `votes` DISABLE KEYS */;
/*!40000 ALTER TABLE `votes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-28 10:39:31
