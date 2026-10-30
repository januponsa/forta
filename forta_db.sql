-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: forta_db
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
-- Table structure for table `academic_calendar_events`
--

DROP TABLE IF EXISTS `academic_calendar_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_calendar_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `academic_calendar_id` bigint(20) unsigned NOT NULL,
  `form_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `group_key` varchar(255) DEFAULT NULL,
  `is_tentative` tinyint(1) NOT NULL DEFAULT 0,
  `source_page` int(11) DEFAULT NULL,
  `source_label` varchar(255) DEFAULT NULL,
  `source_type` varchar(255) NOT NULL DEFAULT 'department',
  `source_reference` varchar(255) DEFAULT NULL,
  `is_source_locked` tinyint(1) NOT NULL DEFAULT 0,
  `category_code` varchar(255) NOT NULL DEFAULT '''other''',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `publication_status` varchar(255) NOT NULL DEFAULT 'draft',
  `external_url` varchar(255) DEFAULT NULL,
  `internal_notes` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academic_calendar_events_created_by_foreign` (`created_by`),
  KEY `academic_calendar_events_academic_calendar_id_index` (`academic_calendar_id`),
  KEY `academic_calendar_events_form_id_index` (`form_id`),
  KEY `academic_calendar_events_event_type_index` (`category_code`),
  KEY `academic_calendar_events_start_date_index` (`start_date`),
  KEY `academic_calendar_events_end_date_index` (`end_date`),
  KEY `academic_calendar_events_is_public_index` (`is_public`),
  KEY `academic_calendar_events_group_key_index` (`group_key`),
  KEY `academic_calendar_events_category_code_index` (`category_code`),
  CONSTRAINT `academic_calendar_events_academic_calendar_id_foreign` FOREIGN KEY (`academic_calendar_id`) REFERENCES `academic_calendars` (`id`) ON DELETE CASCADE,
  CONSTRAINT `academic_calendar_events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `academic_calendar_events_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_calendar_events`
--

LOCK TABLES `academic_calendar_events` WRITE;
/*!40000 ALTER TABLE `academic_calendar_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_calendar_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_calendar_meeting_weeks`
--

DROP TABLE IF EXISTS `academic_calendar_meeting_weeks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_calendar_meeting_weeks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `academic_calendar_id` bigint(20) unsigned NOT NULL,
  `meeting_number` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `note` text DEFAULT NULL,
  `is_instructional` tinyint(1) NOT NULL DEFAULT 1,
  `source_page` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_calendar_meeting` (`academic_calendar_id`,`meeting_number`),
  CONSTRAINT `academic_calendar_meeting_weeks_academic_calendar_id_foreign` FOREIGN KEY (`academic_calendar_id`) REFERENCES `academic_calendars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_calendar_meeting_weeks`
--

LOCK TABLES `academic_calendar_meeting_weeks` WRITE;
/*!40000 ALTER TABLE `academic_calendar_meeting_weeks` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_calendar_meeting_weeks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_calendars`
--

DROP TABLE IF EXISTS `academic_calendars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_calendars` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `semester_code` varchar(255) DEFAULT NULL,
  `semester_name` varchar(255) NOT NULL,
  `semester_type` varchar(255) DEFAULT NULL,
  `academic_year` varchar(255) DEFAULT NULL,
  `display_start_date` date DEFAULT NULL,
  `display_end_date` date DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `publication_status` varchar(255) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `source_document_title` varchar(255) DEFAULT NULL,
  `source_letter_number` varchar(255) DEFAULT NULL,
  `source_letter_date` date DEFAULT NULL,
  `source_document_code` varchar(255) DEFAULT NULL,
  `source_page` int(11) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_calendars_semester_code_unique` (`semester_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_calendars`
--

LOCK TABLES `academic_calendars` WRITE;
/*!40000 ALTER TABLE `academic_calendars` DISABLE KEYS */;
INSERT INTO `academic_calendars` VALUES (1,NULL,'Semester Ganjil 2026/2027','gasal',NULL,NULL,NULL,'2026-09-01','2026-12-31',1,'published','',NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-01 01:09:32','2026-10-01 01:09:32');
/*!40000 ALTER TABLE `academic_calendars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_types`
--

DROP TABLE IF EXISTS `activity_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_types_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_types`
--

LOCK TABLES `activity_types` WRITE;
/*!40000 ALTER TABLE `activity_types` DISABLE KEYS */;
INSERT INTO `activity_types` VALUES (1,'Tugas Akhir','tugas-akhir','2026-09-04 01:01:57','2026-09-04 01:01:57'),(2,'Konversi Mata Kuliah','konversi-mata-kuliah','2026-09-04 01:01:57','2026-09-04 01:01:57'),(3,'KKN','kkn','2026-09-04 01:01:57','2026-09-04 01:01:57'),(4,'Magang / Kerja Praktik','magang-kerja-praktik','2026-09-04 01:01:57','2026-09-04 01:01:57');
/*!40000 ALTER TABLE `activity_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_scores`
--

DROP TABLE IF EXISTS `assessment_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `rubric_item_id` bigint(20) unsigned NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_scores_assessment_id_foreign` (`assessment_id`),
  KEY `assessment_scores_rubric_item_id_foreign` (`rubric_item_id`),
  CONSTRAINT `assessment_scores_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_scores_rubric_item_id_foreign` FOREIGN KEY (`rubric_item_id`) REFERENCES `rubric_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_scores`
--

LOCK TABLES `assessment_scores` WRITE;
/*!40000 ALTER TABLE `assessment_scores` DISABLE KEYS */;
INSERT INTO `assessment_scores` VALUES (1,1,14,92.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(2,1,15,92.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(3,1,16,92.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(4,1,17,92.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(5,1,18,92.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(6,1,19,92.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(7,1,20,92.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(8,1,21,92.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(9,1,22,92.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(10,2,14,95.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(11,2,15,95.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(12,2,16,95.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(13,2,17,95.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(14,2,18,95.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(15,2,19,95.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(16,2,20,95.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(17,2,21,95.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(18,2,22,95.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(19,3,1,8.80,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(20,3,2,8.80,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(21,3,3,17.60,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(22,3,4,26.40,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(23,3,5,26.40,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(24,4,6,13.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(25,4,7,13.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(26,4,8,9.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(27,4,9,13.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(28,4,10,13.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(29,4,11,9.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(30,4,12,9.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(31,4,13,9.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(32,5,14,96.67,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(33,5,15,96.67,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(34,5,16,96.67,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(35,5,17,96.67,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(36,5,18,96.67,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(37,5,19,96.67,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(38,5,20,96.67,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(39,5,21,96.67,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(40,5,22,96.67,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(41,6,1,9.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(42,6,2,9.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(43,6,3,19.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(44,6,4,28.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(45,6,5,28.50,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(46,7,6,14.40,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(47,7,7,14.40,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(48,7,8,9.60,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(49,7,9,14.40,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(50,7,10,14.40,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(51,7,11,9.60,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(52,7,12,9.60,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(53,7,13,9.60,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(54,8,14,84.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(55,8,15,84.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(56,8,16,84.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(57,8,17,84.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(58,8,18,84.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(59,8,19,84.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(60,8,20,84.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(61,8,21,84.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(62,8,22,84.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(63,9,1,8.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(64,9,2,8.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(65,9,3,16.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(66,9,4,24.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(67,9,5,24.00,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(68,10,6,12.30,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(69,10,7,12.30,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(70,10,8,8.20,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(71,10,9,12.30,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(72,10,10,12.30,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(73,10,11,8.20,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(74,10,12,8.20,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(75,10,13,8.20,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(76,11,6,10.00,'2026-10-30 05:27:08','2026-10-30 05:27:08'),(77,11,7,10.00,'2026-10-30 05:27:08','2026-10-30 05:27:08'),(78,11,8,10.00,'2026-10-30 05:27:08','2026-10-30 05:27:08'),(79,11,9,15.00,'2026-10-30 05:27:08','2026-10-30 05:27:08'),(80,11,10,15.00,'2026-10-30 05:27:08','2026-10-30 05:27:08'),(81,11,11,10.00,'2026-10-30 05:27:08','2026-10-30 05:27:08'),(82,11,12,10.00,'2026-10-30 05:27:08','2026-10-30 05:27:08'),(83,11,13,10.00,'2026-10-30 05:27:08','2026-10-30 05:27:08');
/*!40000 ALTER TABLE `assessment_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessments`
--

DROP TABLE IF EXISTS `assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `defense_case_id` bigint(20) unsigned NOT NULL,
  `rubric_version_id` bigint(20) unsigned NOT NULL,
  `assessor_id` bigint(20) unsigned DEFAULT NULL,
  `assessor_type` varchar(255) NOT NULL,
  `assessor_role` varchar(255) NOT NULL,
  `total_score` decimal(5,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `finalized_at` timestamp NULL DEFAULT NULL,
  `originality_status` varchar(255) DEFAULT NULL,
  `originality_notes` text DEFAULT NULL,
  `general_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessments_defense_case_id_foreign` (`defense_case_id`),
  KEY `assessments_rubric_version_id_foreign` (`rubric_version_id`),
  CONSTRAINT `assessments_defense_case_id_foreign` FOREIGN KEY (`defense_case_id`) REFERENCES `defense_cases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessments_rubric_version_id_foreign` FOREIGN KEY (`rubric_version_id`) REFERENCES `rubric_versions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessments`
--

LOCK TABLES `assessments` WRITE;
/*!40000 ALTER TABLE `assessments` DISABLE KEYS */;
INSERT INTO `assessments` VALUES (1,5,3,1,'admin','mentor',92.50,'final','2026-10-29 02:07:59',NULL,NULL,'Mahasiswa menunjukkan kedisiplinan dan inisiatif tinggi selama magang.','2026-10-30 02:07:59','2026-10-30 02:07:59'),(2,6,3,1,'admin','mentor',95.00,'final','2026-10-29 02:07:59',NULL,NULL,'Mahasiswa menunjukkan kedisiplinan dan inisiatif tinggi selama magang.','2026-10-30 02:07:59','2026-10-30 02:07:59'),(3,6,1,3,'lecturer','supervisor',88.00,'final','2026-10-29 02:07:59',NULL,NULL,'Laporan kerja praktik terstruktur baik.','2026-10-30 02:07:59','2026-10-30 02:07:59'),(4,6,2,4,'lecturer','examiner',90.00,'final','2026-10-29 02:07:59','Tidak Ada Indikasi Pelanggaran','Hasil uji similarity Turnitin 12% (memenuhi standar).','Presentasi sangat jelas dan penguasaan materi sangat baik.','2026-10-30 02:07:59','2026-10-30 02:07:59'),(5,7,3,1,'admin','mentor',96.67,'final','2026-10-29 02:07:59',NULL,NULL,'Mahasiswa menunjukkan kedisiplinan dan inisiatif tinggi selama magang.','2026-10-30 02:07:59','2026-10-30 02:07:59'),(6,7,1,1,'lecturer','supervisor',95.00,'final','2026-10-29 02:07:59',NULL,NULL,'Laporan kerja praktik terstruktur baik.','2026-10-30 02:07:59','2026-10-30 02:07:59'),(7,7,2,5,'lecturer','examiner',96.00,'final','2026-10-29 02:07:59','Tidak Ada Indikasi Pelanggaran','Hasil uji similarity Turnitin 12% (memenuhi standar).','Presentasi sangat jelas dan penguasaan materi sangat baik.','2026-10-30 02:07:59','2026-10-30 02:07:59'),(8,8,3,1,'admin','mentor',84.00,'final','2026-10-29 02:08:07',NULL,NULL,'Mahasiswa menunjukkan kedisiplinan dan inisiatif tinggi selama magang.','2026-10-30 02:08:07','2026-10-30 02:08:07'),(9,8,1,6,'lecturer','supervisor',80.00,'final','2026-10-29 02:08:07',NULL,NULL,'Laporan kerja praktik terstruktur baik.','2026-10-30 02:08:07','2026-10-30 02:08:07'),(10,8,2,3,'lecturer','examiner',82.00,'final','2026-10-29 02:08:07','Tidak Ada Indikasi Pelanggaran','Hasil uji similarity Turnitin 12% (memenuhi standar).','Presentasi sangat jelas dan penguasaan materi sangat baik.','2026-10-30 02:08:07','2026-10-30 02:08:07'),(11,9,2,4,'lecturer','examiner',90.00,'final',NULL,NULL,NULL,NULL,'2026-10-30 05:27:08','2026-10-30 05:27:08');
/*!40000 ALTER TABLE `assessments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `actor_role` varchar(255) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `target_type` varchar(255) DEFAULT NULL,
  `target_id` varchar(255) DEFAULT NULL,
  `data_before` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_before`)),
  `data_after` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_after`)),
  `freed_bytes` bigint(20) unsigned NOT NULL DEFAULT 0,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_actor_id_foreign` (`actor_id`),
  CONSTRAINT `audit_logs_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'superadmin','assign_reviewer','submission','1',NULL,'{\"reviewer_id\":\"1\"}',0,'127.0.0.1','2026-10-01 00:53:54','2026-10-01 00:53:54'),(2,1,'superadmin','update_submission_status','submission','1','{\"status\":\"submitted\"}','{\"status\":\"approved\"}',0,'127.0.0.1','2026-10-01 00:53:56','2026-10-01 00:53:56'),(3,1,'superadmin','create_semester','academic_calendars','1',NULL,'{\"semester_code\":null,\"semester_name\":\"Semester Ganjil 2026\\/2027\",\"semester_type\":\"gasal\",\"academic_year\":null,\"start_date\":\"2026-09-01T00:00:00.000000Z\",\"end_date\":\"2026-12-31T00:00:00.000000Z\",\"display_start_date\":null,\"display_end_date\":null,\"is_active\":true,\"publication_status\":\"published\",\"notes\":\"\",\"updated_at\":\"2026-10-01T08:09:32.000000Z\",\"created_at\":\"2026-10-01T08:09:32.000000Z\",\"id\":1}',0,'127.0.0.1','2026-10-01 01:09:32','2026-10-01 01:09:32'),(4,3,'admin_forta','assign_reviewer','submission','103',NULL,'{\"reviewer_id\":\"1\"}',0,'127.0.0.1','2026-10-30 02:34:05','2026-10-30 02:34:05'),(5,3,'admin_forta','update_assignment_status','reviewer_assignment','2','{\"status\":\"Belum Diperiksa\"}','{\"status\":\"Selesai\"}',0,'127.0.0.1','2026-10-30 02:34:10','2026-10-30 02:34:10'),(6,3,'admin_forta','update_submission_status','submission','103','{\"status\":\"submitted\"}','{\"status\":\"approved\"}',0,'127.0.0.1','2026-10-30 02:34:12','2026-10-30 02:34:12');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `authentication_logs`
--

DROP TABLE IF EXISTS `authentication_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authentication_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `actor_type` varchar(255) NOT NULL,
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `event` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `reason_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authentication_logs`
--

LOCK TABLES `authentication_logs` WRITE;
/*!40000 ALTER TABLE `authentication_logs` DISABLE KEYS */;
INSERT INTO `authentication_logs` VALUES (1,'admin',NULL,'admin@pradita.ac.id','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Kredensial salah','2026-09-16 02:36:53','2026-09-16 02:36:53'),(2,'admin',NULL,'admin_forta','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Kredensial salah','2026-09-16 02:39:10','2026-09-16 02:39:10'),(3,'admin',NULL,'admin@pradita.ac.id','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Kredensial salah','2026-09-16 02:42:11','2026-09-16 02:42:11'),(4,'admin',NULL,'admin@pradita.ac.id','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Kredensial salah','2026-09-16 02:42:26','2026-09-16 02:42:26'),(5,'admin',1,'admin','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Via credentials','2026-09-16 02:45:19','2026-09-16 02:45:19'),(6,'student',NULL,'januponsa.dio@student.pradita.ac.id','registration_requested','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Menuju halaman pendaftaran mandiri','2026-09-16 03:09:37','2026-09-16 03:09:37'),(7,'student',NULL,'januponsa.dio@student.pradita.ac.id','registration_requested','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36',NULL,'2026-09-16 03:09:51','2026-09-16 03:09:51'),(8,'admin',1,'januponsa.dio@student.pradita.ac.id','registration_approved','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36',NULL,'2026-09-16 03:10:07','2026-09-16 03:10:07'),(9,'student',123,'januponsa.dio@student.pradita.ac.id','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36',NULL,'2026-09-16 03:10:20','2026-09-16 03:10:20'),(10,'admin',1,'admin','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Via credentials','2026-09-17 23:49:58','2026-09-17 23:49:58'),(11,'student',123,'januponsa.dio@student.pradita.ac.id','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36',NULL,'2026-09-17 23:53:58','2026-09-17 23:53:58'),(12,'admin',1,NULL,'logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36',NULL,'2026-09-18 00:08:12','2026-09-18 00:08:12'),(13,'admin',3,'adminprodi','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Role tidak valid via credential.','2026-09-18 01:03:49','2026-09-18 01:03:49'),(14,'admin',3,'adminprodi','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Role tidak valid via credential.','2026-09-18 01:03:57','2026-09-18 01:03:57'),(15,'admin',1,'admin','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Via credentials','2026-09-18 01:04:16','2026-09-18 01:04:16'),(16,'admin',1,NULL,'logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36',NULL,'2026-09-18 01:06:04','2026-09-18 01:06:04'),(17,'admin',3,'adminprodi','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Role tidak valid via credential.','2026-09-18 01:06:12','2026-09-18 01:06:12'),(18,'admin',3,'adminprodi','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Via credentials','2026-09-18 01:09:58','2026-09-18 01:09:58'),(19,'admin',3,'adminprodi','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Via credentials','2026-09-18 01:10:09','2026-09-18 01:10:09'),(20,'admin',3,'adminprodi','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','Via credentials','2026-09-18 06:09:14','2026-09-18 06:09:14'),(21,'student',123,'januponsa.dio@student.pradita.ac.id','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36',NULL,'2026-09-18 06:56:08','2026-09-18 06:56:08'),(22,'admin',3,'adminprodi','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','Via credentials','2026-09-18 19:56:14','2026-09-18 19:56:14'),(23,'student',123,'januponsa.dio@student.pradita.ac.id','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36',NULL,'2026-09-18 21:46:19','2026-09-18 21:46:19'),(24,'admin',3,'adminprodi','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','Via credentials','2026-09-19 05:38:50','2026-09-19 05:38:50'),(25,'student',123,'januponsa.dio@student.pradita.ac.id','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36',NULL,'2026-09-19 05:38:58','2026-09-19 05:38:58'),(26,'student',123,'januponsa.dio@student.pradita.ac.id','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36',NULL,'2026-09-19 06:40:05','2026-09-19 06:40:05'),(27,'student',123,'januponsa.dio@student.pradita.ac.id','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36',NULL,'2026-09-19 07:53:15','2026-09-19 07:53:15'),(28,'admin',1,'admin','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','Via credentials','2026-10-01 00:33:32','2026-10-01 00:33:32'),(29,'student',123,'januponsa.dio@student.pradita.ac.id','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36',NULL,'2026-10-01 00:51:56','2026-10-01 00:51:56'),(30,'admin',NULL,'admin_forta','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','Kredensial salah','2026-10-15 01:48:27','2026-10-15 01:48:27'),(31,'admin',1,'admin','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','Via credentials','2026-10-15 01:48:34','2026-10-15 01:48:34'),(32,'admin',1,'admin','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','Via credentials','2026-10-15 06:26:07','2026-10-15 06:26:07'),(33,'admin',1,'admin','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','Via credentials','2026-10-15 06:38:11','2026-10-15 06:38:11'),(34,'admin',1,'admin','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','Kredensial salah','2026-10-20 02:23:28','2026-10-20 02:23:28'),(35,'admin',NULL,'admin_forta','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','Kredensial salah','2026-10-20 02:23:38','2026-10-20 02:23:38'),(36,'admin',NULL,'admin.prodi','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','Kredensial salah','2026-10-20 02:23:51','2026-10-20 02:23:51'),(37,'admin',1,'admin','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','Via credentials','2026-10-20 02:24:24','2026-10-20 02:24:24'),(38,'student',123,'januponsa.dio@student.pradita.ac.id','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36',NULL,'2026-10-20 02:33:26','2026-10-20 02:33:26'),(39,'admin',1,'admin','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 01:54:04','2026-10-30 01:54:04'),(40,'admin',3,'adminprodi','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 01:54:13','2026-10-30 01:54:13'),(41,'admin',NULL,'admin_forta','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 01:54:24','2026-10-30 01:54:24'),(42,'admin',1,'admin','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Via credentials','2026-10-30 01:54:35','2026-10-30 01:54:35'),(43,'admin',1,'admin','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Via credentials','2026-10-30 01:54:47','2026-10-30 01:54:47'),(44,'admin',NULL,'januponsa.dio@pradita.ac.id','oauth_rejected','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','User tidak terdaftar.','2026-10-30 01:55:25','2026-10-30 01:55:25'),(45,'admin',NULL,'admin@pradita.ac.id','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 01:55:40','2026-10-30 01:55:40'),(46,'admin',NULL,'admin@pradita.ac.id','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 01:55:43','2026-10-30 01:55:43'),(47,'admin',NULL,'admin123','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 01:59:01','2026-10-30 01:59:01'),(48,'admin',1,'admin','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Via credentials','2026-10-30 01:59:10','2026-10-30 01:59:10'),(49,'admin',NULL,'superadmin','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 01:59:20','2026-10-30 01:59:20'),(50,'admin',NULL,'superadmin','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 01:59:24','2026-10-30 01:59:24'),(51,'admin',3,'adminprodi','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Via credentials','2026-10-30 01:59:45','2026-10-30 01:59:45'),(52,'student',123,'januponsa.dio@student.pradita.ac.id','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36',NULL,'2026-10-30 02:11:58','2026-10-30 02:11:58'),(53,'admin',3,NULL,'logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36',NULL,'2026-10-30 02:37:07','2026-10-30 02:37:07'),(54,'admin',NULL,'januponsa.dio@pradita.ac.id','oauth_rejected','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','User tidak terdaftar.','2026-10-30 02:37:14','2026-10-30 02:37:14'),(55,'admin',NULL,'januponsa.dio@pradita.ac.id','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 02:37:23','2026-10-30 02:37:23'),(56,'admin',4,'sekprodi.if','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Via credentials','2026-10-30 02:37:31','2026-10-30 02:37:31'),(57,'admin',1,'admin','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 02:45:01','2026-10-30 02:45:01'),(58,'admin',NULL,'admin@pradita.ac.id','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 02:45:05','2026-10-30 02:45:05'),(59,'admin',4,'sekprodi.if','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 02:45:16','2026-10-30 02:45:16'),(60,'admin',NULL,'januponsa','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 02:45:20','2026-10-30 02:45:20'),(61,'admin',1,'admin','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 02:45:31','2026-10-30 02:45:31'),(62,'admin',5,'alfa.ryano','login_failed','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Kredensial salah','2026-10-30 05:03:19','2026-10-30 05:03:19'),(63,'student',123,NULL,'logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36',NULL,'2026-10-30 05:24:39','2026-10-30 05:24:39'),(64,'student',123,'januponsa.dio@student.pradita.ac.id','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36',NULL,'2026-10-30 05:24:52','2026-10-30 05:24:52'),(65,'student',123,NULL,'logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36',NULL,'2026-10-30 05:24:56','2026-10-30 05:24:56'),(66,'admin',5,'alfa.ryano','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Via credentials','2026-10-30 05:25:14','2026-10-30 05:25:14'),(67,'lecturer',5,NULL,'logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36',NULL,'2026-10-30 05:27:30','2026-10-30 05:27:30'),(68,'lecturer',8,'master.edison','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Via credentials','2026-10-30 05:27:42','2026-10-30 05:27:42'),(69,'lecturer',8,NULL,'logout','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36',NULL,'2026-10-30 05:27:47','2026-10-30 05:27:47'),(70,'admin',7,'erick.dazki','login_success','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','Via credentials','2026-10-30 05:27:55','2026-10-30 05:27:55');
/*!40000 ALTER TABLE `authentication_logs` ENABLE KEYS */;
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
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('5c785c036466adea360111aa28563bfd556b5fba','i:2;',1793352420),('5c785c036466adea360111aa28563bfd556b5fba:timer','i:1793352420;',1793352420),('77de68daecd823babbb58edb1c8e14d7106e83bb','i:1;',1789737478),('77de68daecd823babbb58edb1c8e14d7106e83bb:timer','i:1789737478;',1789737478);
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
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `class_groups`
--

DROP TABLE IF EXISTS `class_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_groups_course_id_name_unique` (`course_id`,`name`),
  CONSTRAINT `class_groups_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_groups`
--

LOCK TABLES `class_groups` WRITE;
/*!40000 ALTER TABLE `class_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `credits` int(11) NOT NULL DEFAULT 3,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courses_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `defense_assignments`
--

DROP TABLE IF EXISTS `defense_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `defense_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `defense_case_id` bigint(20) unsigned NOT NULL,
  `lecturer_id` bigint(20) unsigned NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `defense_assignments_defense_case_id_foreign` (`defense_case_id`),
  KEY `defense_assignments_lecturer_id_foreign` (`lecturer_id`),
  CONSTRAINT `defense_assignments_defense_case_id_foreign` FOREIGN KEY (`defense_case_id`) REFERENCES `defense_cases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `defense_assignments_lecturer_id_foreign` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `defense_assignments`
--

LOCK TABLES `defense_assignments` WRITE;
/*!40000 ALTER TABLE `defense_assignments` DISABLE KEYS */;
INSERT INTO `defense_assignments` VALUES (1,3,1,'supervisor','2026-10-30 02:07:59','2026-10-30 02:07:59'),(2,3,4,'examiner','2026-10-30 02:07:59','2026-10-30 02:07:59'),(3,4,3,'supervisor','2026-10-30 02:07:59','2026-10-30 02:07:59'),(4,4,5,'examiner','2026-10-30 02:07:59','2026-10-30 02:07:59'),(5,5,1,'supervisor','2026-10-30 02:07:59','2026-10-30 02:07:59'),(6,5,6,'examiner','2026-10-30 02:07:59','2026-10-30 02:07:59'),(7,6,3,'supervisor','2026-10-30 02:07:59','2026-10-30 02:07:59'),(8,6,4,'examiner','2026-10-30 02:07:59','2026-10-30 02:07:59'),(9,7,1,'supervisor','2026-10-30 02:07:59','2026-10-30 02:07:59'),(10,7,5,'examiner','2026-10-30 02:07:59','2026-10-30 02:07:59'),(11,8,6,'supervisor','2026-10-30 02:08:07','2026-10-30 02:08:07'),(12,8,3,'examiner','2026-10-30 02:08:07','2026-10-30 02:08:07'),(13,2,1,'supervisor','2026-10-30 02:43:52','2026-10-30 02:43:52'),(14,2,2,'examiner','2026-10-30 02:43:52','2026-10-30 02:43:52'),(15,1,1,'supervisor','2026-10-30 02:56:58','2026-10-30 02:56:58'),(16,1,4,'examiner','2026-10-30 02:56:58','2026-10-30 02:56:58'),(17,9,1,'supervisor','2026-10-30 02:57:17','2026-10-30 02:57:17'),(18,9,4,'examiner','2026-10-30 02:57:17','2026-10-30 02:57:17');
/*!40000 ALTER TABLE `defense_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `defense_cases`
--

DROP TABLE IF EXISTS `defense_cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `defense_cases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `submission_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `defense_type` varchar(255) NOT NULL DEFAULT 'internship_defense',
  `semester` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'registered',
  `final_score` decimal(5,2) DEFAULT NULL,
  `final_grade` varchar(2) DEFAULT NULL,
  `final_decision` varchar(255) DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `finalized_by` bigint(20) unsigned DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `defense_cases_submission_id_foreign` (`submission_id`),
  KEY `defense_cases_student_id_foreign` (`student_id`),
  KEY `defense_cases_finalized_by_foreign` (`finalized_by`),
  CONSTRAINT `defense_cases_finalized_by_foreign` FOREIGN KEY (`finalized_by`) REFERENCES `users` (`id`),
  CONSTRAINT `defense_cases_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `defense_cases_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `defense_cases`
--

LOCK TABLES `defense_cases` WRITE;
/*!40000 ALTER TABLE `defense_cases` DISABLE KEYS */;
INSERT INTO `defense_cases` VALUES (1,94,1,'internship_defense','Genap 2025/2026','scheduled',NULL,NULL,NULL,NULL,NULL,'{\"company_name\":\"PT Telekomunikasi Indonesia Tbk\",\"company_address\":\"Gedung Telkom Landmark Tower Lt. 38, Jl. Jend. Gatot Subroto, Jakarta Selatan\",\"mentor_name\":\"Ahmad Fauzi, S.T.\",\"mentor_position\":\"Lead Backend Engineer\",\"mentor_nip\":\"TLK-90812\",\"report_title\":\"Pengembangan Modul Otentikasi dan RBAC Menggunakan Laravel Sanctum pada PT Telekomunikasi Indonesia\",\"internship_period\":\"1 Februari 2026 - 30 April 2026\"}','2026-10-30 02:07:59','2026-10-30 02:56:58',NULL),(2,95,2,'internship_defense','Genap 2025/2026','scheduled',NULL,NULL,NULL,NULL,NULL,'{\"company_name\":\"PT Bank Central Asia Tbk\",\"company_address\":\"Menara BCA Lt. 22, Grand Indonesia, Jl. M.H. Thamrin No. 1, Jakarta Pusat\",\"mentor_name\":\"Rendra Kusuma, M.Kom.\",\"mentor_position\":\"DevOps Specialist Lead\",\"mentor_nip\":\"BCA-88712\",\"report_title\":\"Implementasi Pipeline CI\\/CD Berbasis GitHub Actions pada Core Banking PT Bank Central Asia Tbk\",\"internship_period\":\"15 Januari 2026 - 15 April 2026\"}','2026-10-30 02:07:59','2026-10-30 02:43:52',NULL),(3,96,3,'internship_defense','Genap 2025/2026','scheduled',NULL,NULL,NULL,NULL,NULL,'{\"company_name\":\"PT Tokopedia\",\"company_address\":\"Tokopedia Tower Ciputra World 2, Jl. Prof. DR. Satrio, Jakarta Selatan\",\"mentor_name\":\"Kevin Wijaya, S.Kom.\",\"mentor_position\":\"Senior Data Scientist\",\"mentor_nip\":\"TKP-77210\",\"report_title\":\"Analisis Sentimen Ulasan Pengguna Aplikasi E-Commerce Menggunakan IndoBERT pada PT Tokopedia\",\"internship_period\":\"5 Januari 2026 - 5 April 2026\"}','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL),(4,97,4,'internship_defense','Genap 2025/2026','waiting_mentor_score',NULL,NULL,NULL,NULL,NULL,'{\"company_name\":\"PT Espay Debit Indonesia Koe (DANA)\",\"company_address\":\"Capital Place Office Tower Lt. 18, Jl. Jend. Gatot Subroto Kav. 18, Jakarta Selatan\",\"mentor_name\":\"Jessica Tan, S.Ds.\",\"mentor_position\":\"Product Design Lead\",\"mentor_nip\":\"DNA-54129\",\"report_title\":\"Perancangan UI\\/UX Dashboard Transaksi FinTech Menggunakan Design Thinking pada PT DANA Indonesia\",\"internship_period\":\"10 Januari 2026 - 10 April 2026\"}','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL),(5,98,5,'internship_defense','Genap 2025/2026','waiting_scores',NULL,NULL,NULL,NULL,NULL,'{\"company_name\":\"PT Traveloka Indonesia\",\"company_address\":\"Traveloka Campus BSD Green Office Park, BSD City, Tangerang\",\"mentor_name\":\"Dimas Aditya, S.T.\",\"mentor_position\":\"QA Automation Lead\",\"mentor_nip\":\"TVL-33910\",\"report_title\":\"Otomasi Pengujian End-to-End Menggunakan Playwright pada Platform PT Traveloka Indonesia\",\"internship_period\":\"12 Januari 2026 - 12 April 2026\"}','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL),(6,99,6,'internship_defense','Genap 2025/2026','ready_to_finalize',91.40,'A','Lulus','2026-10-29 02:07:59',1,'{\"company_name\":\"PT GoTo Gojek Tokopedia Tbk\",\"company_address\":\"Pasaraya Blok M Gedung B Lt. 6, Jl. Iskandarsyah II No. 2, Jakarta Selatan\",\"mentor_name\":\"Bayu Wicaksono, M.T.\",\"mentor_position\":\"Principal Systems Architect\",\"mentor_nip\":\"GTO-10293\",\"report_title\":\"Migrasi Arsitektur Monolith ke Microservices Berbasis Docker Kubernetes pada PT GoTo Tokopedia\",\"internship_period\":\"1 Januari 2026 - 1 April 2026\"}','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL),(7,100,7,'internship_defense','Genap 2025/2026','completed',95.97,'A','Lulus','2026-10-29 02:07:59',1,'{\"company_name\":\"PT Astra International Tbk\",\"company_address\":\"Menara Astra Lt. 15, Jl. Jend. Sudirman Kav. 5-6, Jakarta Pusat\",\"mentor_name\":\"Ir. Hendro Subroto\",\"mentor_position\":\"Head of IT Infrastructure\",\"mentor_nip\":\"AST-89012\",\"report_title\":\"Implementasi Sistem Deteksi Intrusi Jaringan Menggunakan Snort dan ELK Stack pada PT Astra International\",\"internship_period\":\"15 Januari 2026 - 15 April 2026\"}','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL),(8,101,8,'internship_defense','Genap 2025/2026','revision_required',82.20,'B+','Lulus','2026-10-29 02:08:07',1,'{\"company_name\":\"PT Siloam International Hospitals Tbk\",\"company_address\":\"Gedung Fakultas Kedokteran UPH, Jl. Boulevard Jend. Sudirman, Lippo Karawaci, Tangerang\",\"mentor_name\":\"Drg. Maria Stephanie, M.M.S.I.\",\"mentor_position\":\"Head of Hospital Information System\",\"mentor_nip\":\"SLM-66231\",\"report_title\":\"Rancang Bangun Sistem Antrean Digital Rumah Sakit Berbasis WebSocket pada Siloam Hospitals Group\",\"internship_period\":\"1 Februari 2026 - 30 April 2026\"}','2026-10-30 02:08:07','2026-10-30 02:08:07',NULL),(9,103,123,'internship_defense','Genap 2025/2026','scheduled',NULL,NULL,NULL,NULL,NULL,'{\"company_name\":\"Can Ngopi\",\"company_address\":\"Jl. Boulevard Raya No.88, Gading Serpong, Tangerang\",\"mentor_name\":\"Juan\",\"mentor_position\":\"Manajer\",\"report_title\":\"Pengembangan Sistem Informasi Manajamen Penyimpanan Can Ngopi\",\"internship_period\":\"N\\/A\"}','2026-10-30 02:26:09','2026-10-30 02:57:17',NULL);
/*!40000 ALTER TABLE `defense_cases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `defense_histories`
--

DROP TABLE IF EXISTS `defense_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `defense_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `defense_case_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `before_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`before_state`)),
  `after_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`after_state`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `defense_histories_defense_case_id_foreign` (`defense_case_id`),
  KEY `defense_histories_user_id_foreign` (`user_id`),
  CONSTRAINT `defense_histories_defense_case_id_foreign` FOREIGN KEY (`defense_case_id`) REFERENCES `defense_cases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `defense_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `defense_histories`
--

LOCK TABLES `defense_histories` WRITE;
/*!40000 ALTER TABLE `defense_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `defense_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `defense_schedules`
--

DROP TABLE IF EXISTS `defense_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `defense_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `defense_case_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room_or_link` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `defense_schedules_defense_case_id_foreign` (`defense_case_id`),
  CONSTRAINT `defense_schedules_defense_case_id_foreign` FOREIGN KEY (`defense_case_id`) REFERENCES `defense_cases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `defense_schedules`
--

LOCK TABLES `defense_schedules` WRITE;
/*!40000 ALTER TABLE `defense_schedules` DISABLE KEYS */;
INSERT INTO `defense_schedules` VALUES (1,3,'2026-11-02','09:00:00','10:00:00','Lab Komputer 302 (Kampus Serpong)','Harap hadir 15 menit sebelum sidang dimulai.','2026-10-30 02:07:59','2026-10-30 02:07:59'),(2,4,'2026-10-30','10:30:00','11:30:00','Ruang Sidang 401','Harap hadir 15 menit sebelum sidang dimulai.','2026-10-30 02:07:59','2026-10-30 02:07:59'),(3,5,'2026-10-29','13:00:00','14:00:00','Ruang Sidang 402','Harap hadir 15 menit sebelum sidang dimulai.','2026-10-30 02:07:59','2026-10-30 02:07:59'),(4,6,'2026-10-28','14:30:00','15:30:00','Online via Zoom Meeting Room A','Harap hadir 15 menit sebelum sidang dimulai.','2026-10-30 02:07:59','2026-10-30 02:07:59'),(5,7,'2026-10-25','09:00:00','10:00:00','Ruang Sidang 401','Harap hadir 15 menit sebelum sidang dimulai.','2026-10-30 02:07:59','2026-10-30 02:07:59'),(6,8,'2026-10-26','11:00:00','12:00:00','Ruang Sidang 402','Harap hadir 15 menit sebelum sidang dimulai.','2026-10-30 02:08:07','2026-10-30 02:08:07'),(7,2,'2026-11-10','09:00:00','10:00:00','Ruang Sidang 101',NULL,'2026-10-30 02:43:52','2026-10-30 02:43:52'),(8,1,'2026-11-03','09:00:00','10:00:00','Ruang Sidang 101',NULL,'2026-10-30 02:56:58','2026-10-30 02:56:58'),(9,9,'2026-11-03','02:00:00','03:00:00','Ruang Sidang 101',NULL,'2026-10-30 02:57:17','2026-10-30 05:29:04');
/*!40000 ALTER TABLE `defense_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `defense_suggestions`
--

DROP TABLE IF EXISTS `defense_suggestions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `defense_suggestions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `defense_case_id` bigint(20) unsigned NOT NULL,
  `lecturer_id` bigint(20) unsigned NOT NULL,
  `lecturer_role` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `suggestion` text NOT NULL,
  `priority` varchar(255) NOT NULL DEFAULT 'normal',
  `status` varchar(255) NOT NULL DEFAULT 'Belum Dikerjakan',
  `student_response` text DEFAULT NULL,
  `revision_file_path` varchar(255) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `defense_suggestions_defense_case_id_foreign` (`defense_case_id`),
  KEY `defense_suggestions_lecturer_id_foreign` (`lecturer_id`),
  CONSTRAINT `defense_suggestions_defense_case_id_foreign` FOREIGN KEY (`defense_case_id`) REFERENCES `defense_cases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `defense_suggestions_lecturer_id_foreign` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `defense_suggestions`
--

LOCK TABLES `defense_suggestions` WRITE;
/*!40000 ALTER TABLE `defense_suggestions` DISABLE KEYS */;
INSERT INTO `defense_suggestions` VALUES (1,8,3,'examiner','Laporan','Perbaiki sitasi pustaka pada Bab 2 dan tambahkan diagram sequence alur antrean pada Bab 3.','high','Belum Dikerjakan',NULL,NULL,NULL,'2026-10-30 02:08:07','2026-10-30 02:08:07'),(2,8,3,'examiner','Penyempurnaan Program','Tambahkan mekanisme fallback polling jika koneksi WebSocket terputus.','normal','Sedang Dikerjakan',NULL,NULL,NULL,'2026-10-30 02:08:07','2026-10-30 02:08:07');
/*!40000 ALTER TABLE `defense_suggestions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_asset_versions`
--

DROP TABLE IF EXISTS `document_asset_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_asset_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_asset_id` bigint(20) unsigned NOT NULL,
  `version_number` int(11) NOT NULL DEFAULT 1,
  `original_path` varchar(255) NOT NULL,
  `processed_path` varchar(255) DEFAULT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `original_width` int(11) NOT NULL,
  `original_height` int(11) NOT NULL,
  `aspect_ratio` decimal(10,6) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_format` varchar(255) NOT NULL,
  `has_transparency` tinyint(1) NOT NULL DEFAULT 0,
  `crop_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`crop_data`)),
  `rotation` int(11) NOT NULL DEFAULT 0,
  `flip_horizontal` tinyint(1) NOT NULL DEFAULT 0,
  `flip_vertical` tinyint(1) NOT NULL DEFAULT 0,
  `opacity` decimal(3,2) NOT NULL DEFAULT 1.00,
  `object_fit` varchar(255) NOT NULL DEFAULT 'contain',
  `processed_width` int(11) DEFAULT NULL,
  `processed_height` int(11) DEFAULT NULL,
  `change_notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_asset_versions_document_asset_id_foreign` (`document_asset_id`),
  KEY `document_asset_versions_created_by_foreign` (`created_by`),
  CONSTRAINT `document_asset_versions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_asset_versions_document_asset_id_foreign` FOREIGN KEY (`document_asset_id`) REFERENCES `document_assets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_asset_versions`
--

LOCK TABLES `document_asset_versions` WRITE;
/*!40000 ALTER TABLE `document_asset_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_asset_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_assets`
--

DROP TABLE IF EXISTS `document_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_assets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `asset_type` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `owner_type` varchar(255) DEFAULT NULL,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `default_width` int(11) DEFAULT NULL,
  `default_height` int(11) DEFAULT NULL,
  `active_version_id` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_assets_created_by_foreign` (`created_by`),
  KEY `document_assets_active_version_id_foreign` (`active_version_id`),
  CONSTRAINT `document_assets_active_version_id_foreign` FOREIGN KEY (`active_version_id`) REFERENCES `document_asset_versions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_assets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_assets`
--

LOCK TABLES `document_assets` WRITE;
/*!40000 ALTER TABLE `document_assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_elements`
--

DROP TABLE IF EXISTS `document_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_elements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `template_version_id` bigint(20) unsigned NOT NULL,
  `element_type` varchar(255) NOT NULL,
  `page_number` int(11) NOT NULL DEFAULT 1,
  `x` decimal(10,2) NOT NULL DEFAULT 0.00,
  `y` decimal(10,2) NOT NULL DEFAULT 0.00,
  `width` decimal(10,2) NOT NULL DEFAULT 100.00,
  `height` decimal(10,2) NOT NULL DEFAULT 30.00,
  `rotation` int(11) NOT NULL DEFAULT 0,
  `opacity` decimal(3,2) NOT NULL DEFAULT 1.00,
  `text_align` varchar(255) NOT NULL DEFAULT 'left',
  `padding` decimal(8,2) NOT NULL DEFAULT 0.00,
  `margin_el` decimal(8,2) NOT NULL DEFAULT 0.00,
  `border` varchar(255) DEFAULT NULL,
  `z_index` int(11) NOT NULL DEFAULT 0,
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `condition` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `placeholder_key` varchar(255) DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `asset_id` bigint(20) unsigned DEFAULT NULL,
  `asset_version_id` bigint(20) unsigned DEFAULT NULL,
  `data_source` varchar(255) DEFAULT NULL,
  `table_columns` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`table_columns`)),
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_elements_template_version_id_foreign` (`template_version_id`),
  KEY `document_elements_asset_id_foreign` (`asset_id`),
  KEY `document_elements_asset_version_id_foreign` (`asset_version_id`),
  CONSTRAINT `document_elements_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `document_assets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_elements_asset_version_id_foreign` FOREIGN KEY (`asset_version_id`) REFERENCES `document_asset_versions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_elements_template_version_id_foreign` FOREIGN KEY (`template_version_id`) REFERENCES `document_template_versions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_elements`
--

LOCK TABLES `document_elements` WRITE;
/*!40000 ALTER TABLE `document_elements` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_elements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_histories`
--

DROP TABLE IF EXISTS `document_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `target_type` varchar(255) NOT NULL,
  `target_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `before_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`before_state`)),
  `after_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`after_state`)),
  `template_version_id` bigint(20) unsigned DEFAULT NULL,
  `asset_version_id` bigint(20) unsigned DEFAULT NULL,
  `document_instance_id` bigint(20) unsigned DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_histories_user_id_foreign` (`user_id`),
  KEY `document_histories_target_type_target_id_index` (`target_type`,`target_id`),
  CONSTRAINT `document_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_histories`
--

LOCK TABLES `document_histories` WRITE;
/*!40000 ALTER TABLE `document_histories` DISABLE KEYS */;
INSERT INTO `document_histories` VALUES (1,'DocumentTemplate',1,'created','Template created',NULL,'{\"name\":\"Internal Memo\",\"type\":\"Internal_Memo_Konversi\",\"category\":\"surat_umum\",\"editor_type\":\"flow\",\"document_purpose\":null,\"letterhead_version_id\":null,\"status\":\"draft\",\"body_html\":\"<p>asdassg<span style=\\\"font-family: calibri, sans-serif;\\\">sfgdf<\\/span><\\/p>\\n<p><span style=\\\"font-family: calibri, sans-serif;\\\">sdfsgdjgg<\\/span><\\/p>\\n<p><span style=\\\"font-family: calibri, sans-serif;\\\">fxyujjkl<\\/span><\\/p>\\n<p><span style=\\\"font-family: calibri, sans-serif;\\\">NIM:&nbsp;<\\/span><\\/p>\\n<p><span style=\\\"font-family: calibri, sans-serif;\\\">Nama<\\/span><\\/p>\",\"header_html\":\"001\\/123\\/PRU\\/2026\",\"footer_html\":\"\",\"created_by\":1,\"updated_by\":1,\"updated_at\":\"2026-10-01T07:57:48.000000Z\",\"created_at\":\"2026-10-01T07:57:48.000000Z\",\"id\":1}',NULL,NULL,NULL,NULL,1,'127.0.0.1','2026-10-01 00:57:48','2026-10-01 00:57:48'),(2,'DocumentTemplate',1,'updated','Template updated',NULL,'{\"name\":\"Internal Memo\",\"type\":\"Internal_Memo_Konversi\",\"category\":\"surat_umum\",\"editor_type\":\"flow\",\"document_purpose\":null,\"letterhead_version_id\":null,\"status\":\"draft\",\"body_html\":\"<p>asdassg<span style=\\\"font-family: calibri, sans-serif;\\\">sfgdf<\\/span><\\/p>\\n<p><span style=\\\"font-family: calibri, sans-serif;\\\">sdfsgdjgg<\\/span><\\/p>\\n<p><span style=\\\"font-family: calibri, sans-serif;\\\">fxyujjkl<\\/span><\\/p>\\n<p><span style=\\\"font-family: calibri, sans-serif;\\\">NIM:&nbsp;<\\/span><\\/p>\\n<p><span style=\\\"font-family: calibri, sans-serif;\\\">Nama<\\/span><\\/p>\",\"header_html\":\"001\\/123\\/PRU\\/2026\",\"footer_html\":\"\",\"created_by\":1,\"updated_by\":1,\"updated_at\":\"2026-10-01T07:57:48.000000Z\",\"created_at\":\"2026-10-01T07:57:48.000000Z\",\"id\":1,\"active_version_id\":1}',NULL,NULL,NULL,NULL,1,'127.0.0.1','2026-10-01 00:57:48','2026-10-01 00:57:48'),(3,'DocumentTemplate',1,'created','Template baru dibuat',NULL,'{\"id\":1,\"name\":\"Internal Memo\",\"type\":\"Internal_Memo_Konversi\",\"document_purpose\":null,\"category\":\"surat_umum\",\"editor_type\":\"flow\",\"status\":\"draft\",\"active_version_id\":1,\"letterhead_version_id\":null,\"effective_date\":null,\"header_html\":\"001\\/123\\/PRU\\/2026\",\"body_html\":\"<p>asdassg<span style=\\\"font-family: calibri, sans-serif;\\\">sfgdf<\\/span><\\/p>\\n<p><span style=\\\"font-family: calibri, sans-serif;\\\">sdfsgdjgg<\\/span><\\/p>\\n<p><span style=\\\"font-family: calibri, sans-serif;\\\">fxyujjkl<\\/span><\\/p>\\n<p><span style=\\\"font-family: calibri, sans-serif;\\\">NIM:&nbsp;<\\/span><\\/p>\\n<p><span style=\\\"font-family: calibri, sans-serif;\\\">Nama<\\/span><\\/p>\",\"footer_html\":\"\",\"paper_size\":\"A4\",\"margin_top\":\"30.00\",\"margin_bottom\":\"30.00\",\"margin_left\":\"30.00\",\"margin_right\":\"30.00\",\"created_by\":1,\"updated_by\":1,\"created_at\":\"2026-10-01T07:57:48.000000Z\",\"updated_at\":\"2026-10-01T07:57:48.000000Z\",\"deleted_at\":null}',NULL,NULL,NULL,NULL,1,'127.0.0.1','2026-10-01 00:57:48','2026-10-01 00:57:48');
/*!40000 ALTER TABLE `document_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_instance_overrides`
--

DROP TABLE IF EXISTS `document_instance_overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_instance_overrides` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_instance_id` bigint(20) unsigned NOT NULL,
  `field_key` varchar(255) NOT NULL,
  `original_value` text DEFAULT NULL,
  `override_value` text DEFAULT NULL,
  `override_type` varchar(255) NOT NULL DEFAULT 'text',
  `reason` text DEFAULT NULL,
  `overridden_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_instance_overrides_document_instance_id_foreign` (`document_instance_id`),
  KEY `document_instance_overrides_overridden_by_foreign` (`overridden_by`),
  CONSTRAINT `document_instance_overrides_document_instance_id_foreign` FOREIGN KEY (`document_instance_id`) REFERENCES `document_instances` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_instance_overrides_overridden_by_foreign` FOREIGN KEY (`overridden_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_instance_overrides`
--

LOCK TABLES `document_instance_overrides` WRITE;
/*!40000 ALTER TABLE `document_instance_overrides` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_instance_overrides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_instances`
--

DROP TABLE IF EXISTS `document_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_instances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_template_id` bigint(20) unsigned NOT NULL,
  `template_version_id` bigint(20) unsigned DEFAULT NULL,
  `letterhead_version_id` bigint(20) unsigned DEFAULT NULL,
  `source_type` varchar(255) NOT NULL,
  `source_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `data_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_snapshot`)),
  `asset_snapshots` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`asset_snapshots`)),
  `override_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`override_data`)),
  `file_path` varchar(255) DEFAULT NULL,
  `file_checksum` varchar(255) DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `finalized_by` bigint(20) unsigned DEFAULT NULL,
  `previous_instance_id` bigint(20) unsigned DEFAULT NULL,
  `revision_number` int(11) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_instances_document_template_id_foreign` (`document_template_id`),
  KEY `document_instances_finalized_by_foreign` (`finalized_by`),
  KEY `document_instances_created_by_foreign` (`created_by`),
  KEY `document_instances_updated_by_foreign` (`updated_by`),
  KEY `document_instances_template_version_id_foreign` (`template_version_id`),
  KEY `document_instances_letterhead_version_id_foreign` (`letterhead_version_id`),
  KEY `document_instances_previous_instance_id_foreign` (`previous_instance_id`),
  KEY `document_instances_source_type_source_id_index` (`source_type`,`source_id`),
  CONSTRAINT `document_instances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_instances_document_template_id_foreign` FOREIGN KEY (`document_template_id`) REFERENCES `document_templates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_instances_finalized_by_foreign` FOREIGN KEY (`finalized_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_instances_letterhead_version_id_foreign` FOREIGN KEY (`letterhead_version_id`) REFERENCES `letterhead_versions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_instances_previous_instance_id_foreign` FOREIGN KEY (`previous_instance_id`) REFERENCES `document_instances` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_instances_template_version_id_foreign` FOREIGN KEY (`template_version_id`) REFERENCES `document_template_versions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_instances_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_instances`
--

LOCK TABLES `document_instances` WRITE;
/*!40000 ALTER TABLE `document_instances` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_instances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_template_versions`
--

DROP TABLE IF EXISTS `document_template_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_template_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_template_id` bigint(20) unsigned NOT NULL,
  `version_number` int(11) NOT NULL DEFAULT 1,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `header_html` text DEFAULT NULL,
  `body_html` longtext DEFAULT NULL,
  `footer_html` text DEFAULT NULL,
  `canvas_layout` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`canvas_layout`)),
  `paper_size` varchar(255) NOT NULL DEFAULT 'A4',
  `orientation` varchar(255) NOT NULL DEFAULT 'portrait',
  `margin_top` decimal(8,2) NOT NULL DEFAULT 25.00,
  `margin_bottom` decimal(8,2) NOT NULL DEFAULT 25.00,
  `margin_left` decimal(8,2) NOT NULL DEFAULT 25.00,
  `margin_right` decimal(8,2) NOT NULL DEFAULT 25.00,
  `letterhead_version_id` bigint(20) unsigned DEFAULT NULL,
  `signatory_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`signatory_config`)),
  `change_notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_template_versions_document_template_id_foreign` (`document_template_id`),
  KEY `document_template_versions_letterhead_version_id_foreign` (`letterhead_version_id`),
  KEY `document_template_versions_created_by_foreign` (`created_by`),
  CONSTRAINT `document_template_versions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_template_versions_document_template_id_foreign` FOREIGN KEY (`document_template_id`) REFERENCES `document_templates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_template_versions_letterhead_version_id_foreign` FOREIGN KEY (`letterhead_version_id`) REFERENCES `letterhead_versions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_template_versions`
--

LOCK TABLES `document_template_versions` WRITE;
/*!40000 ALTER TABLE `document_template_versions` DISABLE KEYS */;
INSERT INTO `document_template_versions` VALUES (1,1,1,'draft','001/123/PRU/2026','<p>asdassg<span style=\"font-family: calibri, sans-serif;\">sfgdf</span></p>\n<p><span style=\"font-family: calibri, sans-serif;\">sdfsgdjgg</span></p>\n<p><span style=\"font-family: calibri, sans-serif;\">fxyujjkl</span></p>\n<p><span style=\"font-family: calibri, sans-serif;\">NIM:&nbsp;</span></p>\n<p><span style=\"font-family: calibri, sans-serif;\">Nama</span></p>','',NULL,'A4','portrait',25.00,25.00,25.00,25.00,NULL,NULL,NULL,1,NULL,'2026-10-01 00:57:48','2026-10-01 00:57:48');
/*!40000 ALTER TABLE `document_template_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_templates`
--

DROP TABLE IF EXISTS `document_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `document_purpose` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `editor_type` varchar(255) NOT NULL DEFAULT 'flow',
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `active_version_id` bigint(20) unsigned DEFAULT NULL,
  `letterhead_version_id` bigint(20) unsigned DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `header_html` text DEFAULT NULL,
  `body_html` longtext NOT NULL,
  `footer_html` text DEFAULT NULL,
  `paper_size` varchar(255) NOT NULL DEFAULT 'A4',
  `margin_top` decimal(8,2) NOT NULL DEFAULT 30.00,
  `margin_bottom` decimal(8,2) NOT NULL DEFAULT 30.00,
  `margin_left` decimal(8,2) NOT NULL DEFAULT 30.00,
  `margin_right` decimal(8,2) NOT NULL DEFAULT 30.00,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_templates_type_unique` (`type`),
  UNIQUE KEY `document_templates_document_purpose_unique` (`document_purpose`),
  KEY `document_templates_letterhead_version_id_foreign` (`letterhead_version_id`),
  KEY `document_templates_created_by_foreign` (`created_by`),
  KEY `document_templates_updated_by_foreign` (`updated_by`),
  KEY `document_templates_active_version_id_foreign` (`active_version_id`),
  CONSTRAINT `document_templates_active_version_id_foreign` FOREIGN KEY (`active_version_id`) REFERENCES `document_template_versions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_templates_letterhead_version_id_foreign` FOREIGN KEY (`letterhead_version_id`) REFERENCES `letterhead_versions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_templates_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_templates`
--

LOCK TABLES `document_templates` WRITE;
/*!40000 ALTER TABLE `document_templates` DISABLE KEYS */;
INSERT INTO `document_templates` VALUES (1,'Internal Memo','Internal_Memo_Konversi',NULL,'surat_umum','flow','draft',1,NULL,NULL,'001/123/PRU/2026','<p>asdassg<span style=\"font-family: calibri, sans-serif;\">sfgdf</span></p>\n<p><span style=\"font-family: calibri, sans-serif;\">sdfsgdjgg</span></p>\n<p><span style=\"font-family: calibri, sans-serif;\">fxyujjkl</span></p>\n<p><span style=\"font-family: calibri, sans-serif;\">NIM:&nbsp;</span></p>\n<p><span style=\"font-family: calibri, sans-serif;\">Nama</span></p>','','A4',30.00,30.00,30.00,30.00,1,1,'2026-10-01 00:57:48','2026-10-01 00:57:48',NULL);
/*!40000 ALTER TABLE `document_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_blast_attachments`
--

DROP TABLE IF EXISTS `email_blast_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_blast_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email_blast_id` bigint(20) unsigned NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL DEFAULT 0,
  `mime_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_blast_attachments_email_blast_id_foreign` (`email_blast_id`),
  CONSTRAINT `email_blast_attachments_email_blast_id_foreign` FOREIGN KEY (`email_blast_id`) REFERENCES `email_blasts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_blast_attachments`
--

LOCK TABLES `email_blast_attachments` WRITE;
/*!40000 ALTER TABLE `email_blast_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_blast_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_blast_recipients`
--

DROP TABLE IF EXISTS `email_blast_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_blast_recipients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email_blast_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `nim` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `angkatan` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_blast_recipients_email_blast_id_foreign` (`email_blast_id`),
  KEY `email_blast_recipients_student_id_foreign` (`student_id`),
  CONSTRAINT `email_blast_recipients_email_blast_id_foreign` FOREIGN KEY (`email_blast_id`) REFERENCES `email_blasts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `email_blast_recipients_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_blast_recipients`
--

LOCK TABLES `email_blast_recipients` WRITE;
/*!40000 ALTER TABLE `email_blast_recipients` DISABLE KEYS */;
INSERT INTO `email_blast_recipients` VALUES (1,1,41,'MARK JUSTIN GUNAWAN','2410101017','mark.justin@student.pradita.ac.id','2024','sent',NULL,'2026-10-01 01:12:53','2026-10-01 01:12:47','2026-10-01 01:12:53'),(2,1,56,'JIMMY CHEN','2410101037','jimmy.chen@student.pradita.ac.id','2024','sent',NULL,'2026-10-01 01:12:53','2026-10-01 01:12:47','2026-10-01 01:12:53');
/*!40000 ALTER TABLE `email_blast_recipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_blasts`
--

DROP TABLE IF EXISTS `email_blasts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_blasts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `body_html` longtext NOT NULL,
  `delivery_mode` varchar(255) NOT NULL DEFAULT 'bcc',
  `target_description` varchar(255) DEFAULT NULL,
  `total_recipients` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) unsigned NOT NULL,
  `sent_by` bigint(20) unsigned DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_blasts_created_by_foreign` (`created_by`),
  KEY `email_blasts_sent_by_foreign` (`sent_by`),
  CONSTRAINT `email_blasts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `email_blasts_sent_by_foreign` FOREIGN KEY (`sent_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_blasts`
--

LOCK TABLES `email_blasts` WRITE;
/*!40000 ALTER TABLE `email_blasts` DISABLE KEYS */;
INSERT INTO `email_blasts` VALUES (1,'Percobaan Blasting','Info pembukaan magang','cc','Pilihan Manual (2 mahasiswa)',2,'completed',1,NULL,NULL,'2026-10-01 01:12:53','2026-10-01 01:12:47','2026-10-01 01:12:53');
/*!40000 ALTER TABLE `email_blasts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_logs`
--

DROP TABLE IF EXISTS `email_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `to_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `type` enum('confirmation','reminder_admin','reminder_student','blast') NOT NULL,
  `status` enum('sent','failed') NOT NULL,
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_logs`
--

LOCK TABLES `email_logs` WRITE;
/*!40000 ALTER TABLE `email_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body_html` longtext NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_templates`
--

LOCK TABLES `email_templates` WRITE;
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;
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
-- Table structure for table `form_fields`
--

DROP TABLE IF EXISTS `form_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_fields` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `label` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL,
  `max_files` int(11) DEFAULT NULL,
  `max_size_mb` int(11) DEFAULT NULL,
  `allowed_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_types`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `section_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `validation_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`validation_rules`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `default_value` text DEFAULT NULL,
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`conditions`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `form_fields_form_id_name_unique` (`form_id`,`name`),
  KEY `form_fields_section_id_foreign` (`section_id`),
  CONSTRAINT `form_fields_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `form_fields_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `form_sections` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_fields`
--

LOCK TABLES `form_fields` WRITE;
/*!40000 ALTER TABLE `form_fields` DISABLE KEYS */;
INSERT INTO `form_fields` VALUES (1,1,NULL,'Judul TA','textarea',NULL,NULL,1,1,NULL,NULL,NULL,'2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,NULL,1,NULL,NULL),(2,1,NULL,'Nama Dosen Pembimbing','text',NULL,NULL,1,2,NULL,NULL,NULL,'2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,NULL,1,NULL,NULL),(3,1,NULL,'Bidang Penelitian','select',NULL,'[\"Kecerdasan Buatan\",\"Rekayasa Perangkat Lunak\",\"Jaringan & Keamanan\",\"Sistem Informasi\",\"Data Science\",\"Lainnya\"]',1,3,NULL,NULL,NULL,'2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,NULL,1,NULL,NULL),(4,1,NULL,'Link Proposal Drive','url',NULL,NULL,1,4,NULL,NULL,NULL,'2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,NULL,1,NULL,NULL),(5,1,NULL,'Upload Lembar Persetujuan','file',NULL,NULL,1,5,1,2,'[\"PDF\"]','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,NULL,1,NULL,NULL),(6,6,NULL,'Judul TA','textarea',NULL,NULL,1,1,NULL,NULL,NULL,'2026-10-20 02:28:36','2026-10-20 02:28:36',NULL,NULL,NULL,1,NULL,NULL),(7,6,NULL,'Nama Dosen Pembimbing','text',NULL,NULL,1,2,NULL,NULL,NULL,'2026-10-20 02:28:36','2026-10-20 02:28:36',NULL,NULL,NULL,1,NULL,NULL),(8,6,NULL,'Bidang Penelitian','select',NULL,'[\"Kecerdasan Buatan\",\"Rekayasa Perangkat Lunak\",\"Jaringan & Keamanan\",\"Sistem Informasi\",\"Data Science\",\"Lainnya\"]',1,3,NULL,NULL,NULL,'2026-10-20 02:28:36','2026-10-20 02:28:36',NULL,NULL,NULL,1,NULL,NULL),(9,6,NULL,'Link Proposal Drive','url',NULL,NULL,1,4,NULL,NULL,NULL,'2026-10-20 02:28:36','2026-10-20 02:28:36',NULL,NULL,NULL,1,NULL,NULL),(10,6,NULL,'Upload Lembar Persetujuan','file',NULL,NULL,1,5,1,2,'[\"PDF\"]','2026-10-20 02:28:36','2026-10-20 02:28:36',NULL,NULL,NULL,1,NULL,NULL),(11,8,'INTDEF_report_title','Judul Laporan Kerja Praktik','textarea',NULL,NULL,1,1,NULL,NULL,NULL,'2026-10-30 02:07:18','2026-10-30 02:07:18',NULL,NULL,NULL,1,NULL,NULL),(12,8,'INTDEF_company_name','Nama Perusahaan / Instansi','text',NULL,NULL,1,2,NULL,NULL,NULL,'2026-10-30 02:07:18','2026-10-30 02:07:18',NULL,NULL,NULL,1,NULL,NULL),(13,8,'INTDEF_company_address','Alamat Perusahaan','textarea',NULL,NULL,0,3,NULL,NULL,NULL,'2026-10-30 02:07:18','2026-10-30 02:07:18',NULL,NULL,NULL,1,NULL,NULL),(14,8,'INTDEF_mentor_name','Nama Mentor Lapangan','text',NULL,NULL,1,4,NULL,NULL,NULL,'2026-10-30 02:07:18','2026-10-30 02:07:18',NULL,NULL,NULL,1,NULL,NULL),(15,8,'INTDEF_mentor_position','Jabatan Mentor Lapangan','text',NULL,NULL,0,5,NULL,NULL,NULL,'2026-10-30 02:07:18','2026-10-30 02:07:18',NULL,NULL,NULL,1,NULL,NULL),(16,8,'INTDEF_mentor_nip','NIP / NIK Mentor','text',NULL,NULL,0,6,NULL,NULL,NULL,'2026-10-30 02:07:18','2026-10-30 02:07:18',NULL,NULL,NULL,1,NULL,NULL),(17,8,'INTDEF_internship_period','Periode Magang','text',NULL,NULL,0,7,NULL,NULL,NULL,'2026-10-30 02:07:18','2026-10-30 02:07:18',NULL,NULL,NULL,1,NULL,NULL),(18,8,'INTDEF_mentor_evaluation_file','Dokumen Penilaian Mentor Lapangan (PDF)','file',NULL,NULL,1,8,NULL,5,'[\"PDF\"]','2026-10-30 02:07:18','2026-10-30 02:07:18',NULL,NULL,NULL,1,NULL,NULL),(19,8,'INTDEF_final_report_file','Draft Laporan Kerja Praktik (PDF)','file',NULL,NULL,1,9,NULL,10,'[\"PDF\"]','2026-10-30 02:07:18','2026-10-30 02:07:18',NULL,NULL,NULL,1,NULL,NULL),(20,7,NULL,'Nama Perusahaan','text',NULL,NULL,1,1,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(21,7,NULL,'Bidang Perusahaan','text',NULL,NULL,1,2,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(22,7,NULL,'Alamat Perusahaan','textarea',NULL,NULL,1,3,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(23,7,NULL,'Divisi Penempatan','text',NULL,NULL,1,4,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(24,7,NULL,'Posisi Magang','text',NULL,NULL,1,5,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(25,7,NULL,'Nama Pembimbing Lapangan','text',NULL,NULL,0,6,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(26,7,NULL,'Email Pembimbing Lapangan','email',NULL,NULL,0,7,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(27,7,NULL,'Nomor Kontak Pembimbing','tel',NULL,NULL,0,8,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(28,7,NULL,'Tanggal Mulai','date',NULL,NULL,1,9,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(29,7,NULL,'Tanggal Selesai','date',NULL,NULL,1,10,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(30,7,NULL,'Deskripsi Pekerjaan','textarea',NULL,NULL,1,11,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(31,7,NULL,'Teknologi yang Digunakan','text',NULL,NULL,0,12,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(32,7,NULL,'Link Website Perusahaan','url',NULL,NULL,0,13,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(33,7,NULL,'Upload Surat Penerimaan','file',NULL,NULL,1,14,1,3,'[\"PDF\"]','2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(34,7,NULL,'Upload Proposal','file',NULL,NULL,0,15,1,5,'[\"PDF\"]','2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL),(35,7,NULL,'Catatan Tambahan','textarea',NULL,NULL,0,16,NULL,NULL,NULL,'2026-10-30 02:14:10','2026-10-30 02:14:10',NULL,NULL,NULL,1,NULL,NULL);
/*!40000 ALTER TABLE `form_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_sections`
--

DROP TABLE IF EXISTS `form_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `form_sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint(20) unsigned NOT NULL,
  `section_code` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `form_sections_form_id_section_code_unique` (`form_id`,`section_code`),
  CONSTRAINT `form_sections_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_sections`
--

LOCK TABLES `form_sections` WRITE;
/*!40000 ALTER TABLE `form_sections` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forms`
--

DROP TABLE IF EXISTS `forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_code` varchar(255) DEFAULT NULL,
  `depends_on_form_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `activity_type_id` bigint(20) unsigned NOT NULL,
  `phase` enum('registration','reporting') NOT NULL,
  `semester` varchar(255) NOT NULL,
  `open_at` datetime DEFAULT NULL,
  `close_at` datetime DEFAULT NULL,
  `status` enum('draft','active','closed','archived') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `display_order` int(10) unsigned NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT 1,
  `parent_form_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `forms_slug_unique` (`slug`),
  UNIQUE KEY `forms_form_code_unique` (`form_code`),
  KEY `forms_activity_type_id_foreign` (`activity_type_id`),
  KEY `forms_depends_on_form_id_foreign` (`depends_on_form_id`),
  KEY `forms_display_order_index` (`display_order`),
  KEY `forms_parent_form_id_foreign` (`parent_form_id`),
  CONSTRAINT `forms_activity_type_id_foreign` FOREIGN KEY (`activity_type_id`) REFERENCES `activity_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forms_depends_on_form_id_foreign` FOREIGN KEY (`depends_on_form_id`) REFERENCES `forms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `forms_parent_form_id_foreign` FOREIGN KEY (`parent_form_id`) REFERENCES `forms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forms`
--

LOCK TABLES `forms` WRITE;
/*!40000 ALTER TABLE `forms` DISABLE KEYS */;
INSERT INTO `forms` VALUES (1,NULL,NULL,'Pendaftaran TA Ganjil 2025/2026','Form pendaftaran awal Tugas Akhir untuk mahasiswa semester ganjil TA 2025/2026.','pendaftaran-ta-ganjil-2025-2026',1,'registration','Ganjil 2025/2026','2026-09-04 08:01:57','2026-10-04 08:01:57','active','2026-09-04 01:01:57','2026-09-04 01:01:57',0,NULL,1,NULL),(2,NULL,NULL,'Magang 2026','','magang-2026-6ryMda',4,'registration','7','2026-09-01 15:47:00','2026-11-30 15:47:00','archived','2026-10-01 01:47:48','2026-10-15 06:35:39',1,NULL,1,NULL),(3,NULL,NULL,'Pengajuan Magang 2026','Pengajuan surat permohonan dan tempat magang.','magang-2026',4,'registration','Genap 2025/2026','2026-09-24 13:34:26','2026-11-15 13:34:26','active','2026-10-15 06:34:26','2026-10-15 06:34:26',0,NULL,1,NULL),(4,NULL,NULL,'Seminar Proposal Skripsi 2026','Pendaftaran jadwal dan berkas seminar proposal.','seminar-proposal-skripsi-2026',1,'registration','Genap 2025/2026','2026-10-01 13:34:43','2026-11-15 13:34:43','active','2026-10-15 06:34:43','2026-10-15 06:34:43',0,NULL,1,NULL),(5,NULL,NULL,'Sidang Akhir & Yudisium 2026','Pengajuan sidang meja hijau dan verifikasi kelulusan.','sidang-akhir-yudisium-2026',1,'reporting','Genap 2025/2026','2026-10-05 13:34:43','2026-11-15 13:34:43','active','2026-10-15 06:34:43','2026-10-15 06:34:43',0,NULL,1,NULL),(6,NULL,NULL,'Salinan - Pendaftaran TA Ganjil 2025/2026','Form pendaftaran awal Tugas Akhir untuk mahasiswa semester ganjil TA 2025/2026.','salinan-pendaftaran-ta-ganjil-20252026-ATFX12',1,'registration','Ganjil 2025/2026',NULL,NULL,'draft','2026-10-20 02:28:36','2026-10-20 02:28:36',1,NULL,1,NULL),(7,NULL,NULL,'Pendaftaran Sidang Magang Ganjil 2026/2027','','pendaftaran-sidang-magang-ganjil-20262027-Xrf6gm',4,'registration','7','2026-10-20 16:04:00','2026-12-17 16:04:00','active','2026-10-30 02:04:34','2026-10-30 02:14:23',2,NULL,1,NULL),(8,'INTERNSHIP_DEFENSE_REGISTRATION',NULL,'Pendaftaran Sidang Kerja Praktik 2026','Formulir resmi pendaftaran sidang kerja praktik/magang program studi Informatika.','pendaftaran-sidang-kerja-praktik-2026',4,'registration','Genap 2025/2026','2026-09-30 09:07:59','2026-12-30 09:07:59','active','2026-10-30 02:07:18','2026-10-30 02:07:59',0,NULL,1,NULL);
/*!40000 ALTER TABLE `forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `generated_documents`
--

DROP TABLE IF EXISTS `generated_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `generated_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `defense_case_id` bigint(20) unsigned NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `checksum` varchar(255) DEFAULT NULL,
  `generated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `generated_documents_defense_case_id_foreign` (`defense_case_id`),
  KEY `generated_documents_generated_by_foreign` (`generated_by`),
  CONSTRAINT `generated_documents_defense_case_id_foreign` FOREIGN KEY (`defense_case_id`) REFERENCES `defense_cases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `generated_documents_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `generated_documents`
--

LOCK TABLES `generated_documents` WRITE;
/*!40000 ALTER TABLE `generated_documents` DISABLE KEYS */;
INSERT INTO `generated_documents` VALUES (1,7,'biodata','defense_documents/7/Biodata_KP_2310101010_andrew-jatzen-lay.pdf','Biodata_KP_2310101010_andrew-jatzen-lay.pdf',1,NULL,NULL,'2026-10-30 02:08:01','2026-10-30 02:08:01'),(2,7,'f1_berita_acara','defense_documents/7/F1_Berita_Acara_KP_2310101010_andrew-jatzen-lay.pdf','F1_Berita_Acara_KP_2310101010_andrew-jatzen-lay.pdf',1,NULL,NULL,'2026-10-30 02:08:01','2026-10-30 02:08:01'),(3,7,'f2_rekap_nilai','defense_documents/7/F2_Nilai_Ujian_KP_2310101010_andrew-jatzen-lay.pdf','F2_Nilai_Ujian_KP_2310101010_andrew-jatzen-lay.pdf',1,NULL,NULL,'2026-10-30 02:08:02','2026-10-30 02:08:02'),(4,7,'f3_penilaian','defense_documents/7/F3_Pembimbing_KP_2310101010_andrew-jatzen-lay.pdf','F3_Pembimbing_KP_2310101010_andrew-jatzen-lay.pdf',1,NULL,NULL,'2026-10-30 02:08:04','2026-10-30 02:08:04'),(5,7,'f4_penilaian','defense_documents/7/F4_Penguji_KP_2310101010_andrew-jatzen-lay.pdf','F4_Penguji_KP_2310101010_andrew-jatzen-lay.pdf',1,NULL,NULL,'2026-10-30 02:08:04','2026-10-30 02:08:04'),(6,7,'f5_penilaian','defense_documents/7/F5_Mentor_KP_2310101010_andrew-jatzen-lay.pdf','F5_Mentor_KP_2310101010_andrew-jatzen-lay.pdf',1,NULL,NULL,'2026-10-30 02:08:04','2026-10-30 02:08:04'),(7,7,'f6_saran','defense_documents/7/F6_Saran_KP_2310101010_andrew-jatzen-lay.pdf','F6_Saran_KP_2310101010_andrew-jatzen-lay.pdf',1,NULL,NULL,'2026-10-30 02:08:07','2026-10-30 02:08:07');
/*!40000 ALTER TABLE `generated_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `internship_letter_requests`
--

DROP TABLE IF EXISTS `internship_letter_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `internship_letter_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) NOT NULL,
  `recipient_name` varchar(255) NOT NULL DEFAULT 'Bapak/Ibu HRD',
  `company_address` text NOT NULL,
  `company_city` varchar(255) NOT NULL,
  `placement_location` varchar(255) DEFAULT NULL,
  `internship_position` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `letter_date` date DEFAULT NULL,
  `duration_notes` varchar(255) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `final_pdf_path` varchar(255) DEFAULT NULL,
  `status` enum('draft','submitted','under_review','revision_required','approved','rejected','generated','completed') NOT NULL DEFAULT 'draft',
  `letter_number` varchar(255) DEFAULT NULL,
  `generated_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `revision_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `internship_letter_requests_letter_number_unique` (`letter_number`),
  KEY `internship_letter_requests_student_id_foreign` (`student_id`),
  KEY `internship_letter_requests_approved_by_foreign` (`approved_by`),
  CONSTRAINT `internship_letter_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `internship_letter_requests_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `internship_letter_requests`
--

LOCK TABLES `internship_letter_requests` WRITE;
/*!40000 ALTER TABLE `internship_letter_requests` DISABLE KEYS */;
INSERT INTO `internship_letter_requests` VALUES (1,123,NULL,'PT. Ini itu','Bapak/Ibu HRD sdf','Di tempat','Tangerang',NULL,NULL,'2026-09-01','2026-09-16',NULL,NULL,NULL,NULL,NULL,NULL,'rejected',NULL,NULL,NULL,NULL,'masih salah','Masih perlu di revisi kembali','2026-09-16 03:11:45','2026-09-17 23:51:48',NULL),(2,123,NULL,'PT. Sana Sini','Bapak/Ibu HRD PT. Sana Sini','Jl. Sana sini','Bekasi',NULL,NULL,'2026-08-01','2026-11-30',NULL,NULL,NULL,NULL,NULL,'internship_letters/Surat_Pengantar_KP_2023012345_1789714560.pdf','generated','001/P-IF/Pradita/IX/2026','2026-09-17 23:56:00',1,'2026-09-17 23:55:55',NULL,NULL,'2026-09-17 23:55:31','2026-09-17 23:56:00',NULL),(3,123,' 7','PT. Pete','Bapak/Ibu HRD Pete','disitu','Jakarta',NULL,NULL,'2026-06-01','2026-11-30','2026-09-07',NULL,NULL,NULL,NULL,'internship_letters/Surat_Pengantar_KP_2023012345_1789829484.pdf','generated','003/P-KP/Pradita/IX/2026','2026-09-19 07:51:24',3,'2026-09-19 07:51:24',NULL,NULL,'2026-09-19 06:13:54','2026-09-19 07:51:24',NULL),(4,123,'7','Summarecon','Bapak/Ibu HRD PT. Summarecon','Jl. Perintis Kemerdeukaan','Jakarta',NULL,NULL,'2026-10-01','2027-03-31','2026-08-06',NULL,NULL,NULL,NULL,'internship_letters/Surat_Pengantar_KP_2023012345_1790841769.pdf','generated','001-F/PRU','2026-10-01 01:02:50',1,'2026-10-01 01:02:50',NULL,NULL,'2026-10-01 00:59:45','2026-10-01 01:02:50',NULL),(5,123,'7','xyz','Bapak/Ibu HRD zyx','afgifh','tfujyi','fnjg',NULL,'2026-10-06','2026-12-03','2026-10-20',NULL,NULL,NULL,NULL,'internship_letters/Surat_Pengantar_KP_2023012345_1792488979.pdf','generated','002/P-IF/Pradita/X/2026','2026-10-20 02:36:19',1,'2026-10-20 02:36:19',NULL,NULL,'2026-10-20 02:34:15','2026-10-20 02:36:19',NULL);
/*!40000 ALTER TABLE `internship_letter_requests` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lecturers`
--

DROP TABLE IF EXISTS `lecturers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lecturers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `nip` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `signature_path` varchar(255) DEFAULT NULL,
  `stamp_path` varchar(255) DEFAULT NULL,
  `default_width` int(11) NOT NULL DEFAULT 150,
  `default_height` int(11) NOT NULL DEFAULT 75,
  `include_name` tinyint(1) NOT NULL DEFAULT 1,
  `include_position` tinyint(1) NOT NULL DEFAULT 0,
  `include_date` tinyint(1) NOT NULL DEFAULT 0,
  `allowed_roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_roles`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lecturers_nip_unique` (`nip`),
  UNIQUE KEY `lecturers_email_unique` (`email`),
  KEY `lecturers_user_id_foreign` (`user_id`),
  CONSTRAINT `lecturers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lecturers`
--

LOCK TABLES `lecturers` WRITE;
/*!40000 ALTER TABLE `lecturers` DISABLE KEYS */;
INSERT INTO `lecturers` VALUES (1,4,'031400010','Januponsa Dio Firizqi, S.Kom., M.Kom.','Koordinator Kerja Praktik','januponsa.dio@pradita.ac.id',1,'document-assets/signatures/lPYjJ7OSdp7mxKAv6Un22Ca5lPUacbXOtarNqP8n.png',NULL,150,75,0,0,0,'[\"superadmin\",\"admin_forta\"]','2026-09-18 06:13:13','2026-10-30 02:52:53'),(2,7,'302059002','Erick Dazki, S.Kom., M.Kom.','Ketua Program Studi Informatika','erick.dazki@pradita.ac.id',1,'document-assets/signatures/Ygzwm7TymRgh57WrXFXgt1aoA2Zk2F2H2avSQDXZ.png','document-assets/stamps/jQB8UuF1NXsjU3W1XybFfVZH8GULIthbNaXKgtzA.png',150,75,0,0,0,NULL,'2026-09-18 06:16:06','2026-10-30 02:52:52'),(3,9,'309059801','Refgiufi Patria Avrianto, S.Kom., M.Kom.','','refgiufi.patria@pradita.ac.id',1,NULL,NULL,150,75,0,0,0,'[\"superadmin\",\"admin_forta\"]','2026-10-30 02:07:01','2026-10-30 02:52:53'),(4,5,'316078403','Alfa Ryano Yohannis, S.T., M.T., Ph.D.','','alfa.ryano@pradita.ac.id',1,NULL,NULL,150,75,0,0,0,'[\"superadmin\",\"admin_forta\"]','2026-10-30 02:07:01','2026-10-30 02:52:52'),(5,6,'313046703','Arya Sanjaya, S.T., M.Kom.','','arya.sanjaya@pradita.ac.id',1,NULL,NULL,150,75,0,0,0,'[\"superadmin\",\"admin_forta\"]','2026-10-30 02:07:02','2026-10-30 02:52:52'),(6,8,'323077202','Master Edison Siregar, S.T., M.Kom.','Dosen Pembimbing & Penguji','master.edison@pradita.ac.id',1,NULL,NULL,150,75,1,0,0,'[\"superadmin\",\"admin_forta\"]','2026-10-30 02:07:02','2026-10-30 02:52:53');
/*!40000 ALTER TABLE `lecturers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `letter_logbooks`
--

DROP TABLE IF EXISTS `letter_logbooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `letter_logbooks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `letter_number` varchar(255) NOT NULL,
  `letter_title` varchar(255) NOT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `letter_date` date NOT NULL,
  `is_backdated` tinyint(1) NOT NULL DEFAULT 0,
  `source_type` varchar(255) NOT NULL,
  `source_id` bigint(20) unsigned NOT NULL,
  `final_pdf_path` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `letter_logbooks_created_by_foreign` (`created_by`),
  KEY `letter_logbooks_letter_number_index` (`letter_number`),
  KEY `letter_logbooks_letter_date_index` (`letter_date`),
  KEY `letter_logbooks_source_type_index` (`source_type`),
  KEY `letter_logbooks_source_id_index` (`source_id`),
  CONSTRAINT `letter_logbooks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `letter_logbooks`
--

LOCK TABLES `letter_logbooks` WRITE;
/*!40000 ALTER TABLE `letter_logbooks` DISABLE KEYS */;
INSERT INTO `letter_logbooks` VALUES (1,'002/P-IF/Pradita/X/2026','Surat Pengantar KP/Magang','Bapak/Ibu HRD zyx','2026-10-20',0,'App\\Models\\InternshipLetterRequest',5,'internship_letters/Surat_Pengantar_KP_2023012345_1792488979.pdf',1,'2026-10-20 02:36:19','2026-10-20 02:36:19');
/*!40000 ALTER TABLE `letter_logbooks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `letter_number_sequences`
--

DROP TABLE IF EXISTS `letter_number_sequences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `letter_number_sequences` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT 'internship',
  `year` int(11) NOT NULL,
  `month` int(11) DEFAULT NULL,
  `last_number` int(11) NOT NULL DEFAULT 0,
  `format` varchar(255) NOT NULL DEFAULT '{nomor_urut}/{kode_surat}/{bulan_romawi}/{tahun}',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `letter_number_sequences_type_year_month_unique` (`type`,`year`,`month`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `letter_number_sequences`
--

LOCK TABLES `letter_number_sequences` WRITE;
/*!40000 ALTER TABLE `letter_number_sequences` DISABLE KEYS */;
INSERT INTO `letter_number_sequences` VALUES (1,'internship',2026,9,2,'{nomor_urut}/{kode_surat}/{bulan_romawi}/{tahun}','2026-09-17 23:55:55','2026-10-20 02:35:29');
/*!40000 ALTER TABLE `letter_number_sequences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `letter_request_histories`
--

DROP TABLE IF EXISTS `letter_request_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `letter_request_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `internship_letter_request_id` bigint(20) unsigned NOT NULL,
  `actor_type` varchar(255) NOT NULL,
  `actor_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `previous_status` varchar(255) DEFAULT NULL,
  `new_status` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `letter_request_histories_internship_letter_request_id_foreign` (`internship_letter_request_id`),
  CONSTRAINT `letter_request_histories_internship_letter_request_id_foreign` FOREIGN KEY (`internship_letter_request_id`) REFERENCES `internship_letter_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `letter_request_histories`
--

LOCK TABLES `letter_request_histories` WRITE;
/*!40000 ALTER TABLE `letter_request_histories` DISABLE KEYS */;
INSERT INTO `letter_request_histories` VALUES (1,1,'student',123,'submitted',NULL,'submitted',NULL,'2026-09-16 03:11:45','2026-09-16 03:11:45'),(2,1,'user',1,'status_change','submitted','under_review',NULL,'2026-09-16 03:12:07','2026-09-16 03:12:07'),(3,1,'user',1,'status_change','revision_required','revision_required','Masih perlu di revisi kembali','2026-09-17 23:50:56','2026-09-17 23:50:56'),(4,1,'user',1,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-09-17 23:51:11','2026-09-17 23:51:11'),(5,1,'user',1,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-09-17 23:51:12','2026-09-17 23:51:12'),(6,1,'user',1,'status_change','rejected','rejected','masih salah','2026-09-17 23:51:48','2026-09-17 23:51:48'),(7,2,'student',123,'submitted',NULL,'submitted',NULL,'2026-09-17 23:55:31','2026-09-17 23:55:31'),(8,2,'user',1,'status_change','submitted','under_review',NULL,'2026-09-17 23:55:52','2026-09-17 23:55:52'),(9,2,'user',1,'status_change','approved','approved','Admin menyetujui permohonan.','2026-09-17 23:55:55','2026-09-17 23:55:55'),(10,2,'user',1,'status_change','generated','generated','Sistem mencetak PDF resmi.','2026-09-17 23:56:00','2026-09-17 23:56:00'),(11,3,'student',123,'submitted',NULL,'submitted',NULL,'2026-09-19 06:13:54','2026-09-19 06:13:54'),(12,3,'user',3,'status_change','submitted','under_review',NULL,'2026-09-19 06:14:55','2026-09-19 06:14:55'),(13,3,'user',3,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-09-19 06:38:07','2026-09-19 06:38:07'),(14,3,'user',3,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-09-19 06:38:11','2026-09-19 06:38:11'),(15,3,'user',3,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-09-19 06:42:48','2026-09-19 06:42:48'),(16,3,'user',3,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-09-19 07:01:00','2026-09-19 07:01:00'),(17,3,'user',3,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-09-19 07:01:44','2026-09-19 07:01:44'),(18,3,'user',3,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-09-19 07:01:48','2026-09-19 07:01:48'),(19,3,'user',3,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-09-19 07:01:59','2026-09-19 07:01:59'),(20,3,'user',3,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-09-19 07:02:02','2026-09-19 07:02:02'),(21,3,'user',3,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-09-19 07:04:21','2026-09-19 07:04:21'),(22,3,'user',3,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-09-19 07:30:09','2026-09-19 07:30:09'),(23,3,'user',3,'status_change','approved','approved','Admin menyetujui permohonan.','2026-09-19 07:30:37','2026-09-19 07:30:37'),(24,3,'user',3,'status_change','generated','generated','Sistem mencetak PDF resmi dan menempelkan tanda tangan.','2026-09-19 07:30:37','2026-09-19 07:30:37'),(25,3,'user',3,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-09-19 07:51:02','2026-09-19 07:51:02'),(26,3,'user',3,'status_change','approved','approved','Admin menyetujui permohonan.','2026-09-19 07:51:24','2026-09-19 07:51:24'),(27,3,'user',3,'status_change','generated','generated','Sistem mencetak PDF resmi dan menempelkan tanda tangan.','2026-09-19 07:51:24','2026-09-19 07:51:24'),(28,3,'user',3,'status_change','generated','email_sent','Admin mengirim ulang notifikasi email ke mahasiswa.','2026-09-19 07:51:52','2026-09-19 07:51:52'),(29,4,'student',123,'submitted',NULL,'submitted',NULL,'2026-10-01 00:59:45','2026-10-01 00:59:45'),(30,4,'user',1,'status_change','submitted','under_review',NULL,'2026-10-01 01:00:03','2026-10-01 01:00:03'),(31,4,'user',1,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-10-01 01:02:07','2026-10-01 01:02:07'),(32,4,'user',1,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-10-01 01:02:10','2026-10-01 01:02:10'),(33,4,'user',1,'status_change','approved','approved','Admin menyetujui permohonan.','2026-10-01 01:02:50','2026-10-01 01:02:50'),(34,4,'user',1,'status_change','generated','generated','Sistem mencetak PDF resmi dan menempelkan tanda tangan.','2026-10-01 01:02:50','2026-10-01 01:02:50'),(35,4,'user',1,'status_change','generated','email_sent','Admin mengirim ulang notifikasi email ke mahasiswa.','2026-10-01 01:03:28','2026-10-01 01:03:28'),(36,5,'student',123,'submitted',NULL,'submitted',NULL,'2026-10-20 02:34:15','2026-10-20 02:34:15'),(37,5,'user',1,'status_change','submitted','under_review',NULL,'2026-10-20 02:34:35','2026-10-20 02:34:35'),(38,5,'user',1,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-10-20 02:35:27','2026-10-20 02:35:27'),(39,5,'user',1,'updated_details',NULL,NULL,'Admin memperbaiki detail perusahaan tujuan.','2026-10-20 02:35:29','2026-10-20 02:35:29'),(40,5,'user',1,'status_change','approved','approved','Admin menyetujui permohonan.','2026-10-20 02:36:19','2026-10-20 02:36:19'),(41,5,'user',1,'status_change','generated','generated','Sistem mencetak PDF resmi dan menempelkan tanda tangan.','2026-10-20 02:36:19','2026-10-20 02:36:19'),(42,5,'user',1,'status_change','generated','email_sent','Admin mengirim ulang notifikasi email ke mahasiswa.','2026-10-20 02:36:56','2026-10-20 02:36:56');
/*!40000 ALTER TABLE `letter_request_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `letter_templates`
--

DROP TABLE IF EXISTS `letter_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `letter_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT 'internship',
  `logo_asset_id` bigint(20) unsigned DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `kop_text` text DEFAULT NULL,
  `university_name` varchar(255) DEFAULT NULL,
  `campus_address` varchar(255) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `city` varchar(255) NOT NULL DEFAULT 'Tangerang',
  `letter_code` varchar(255) NOT NULL DEFAULT 'P-IF/Pradita',
  `subject` varchar(255) NOT NULL DEFAULT 'Surat Pengantar Kerja Praktik',
  `opening_paragraph` text DEFAULT NULL,
  `purpose_paragraph` text DEFAULT NULL,
  `closing_paragraph` text DEFAULT NULL,
  `signatory_name` varchar(255) DEFAULT NULL,
  `signatory_position` varchar(255) DEFAULT NULL,
  `lecturer_id` bigint(20) unsigned DEFAULT NULL,
  `signature_path` varchar(255) DEFAULT NULL,
  `stamp_path` varchar(255) DEFAULT NULL,
  `number_format` varchar(255) NOT NULL DEFAULT '{nomor_urut}/{kode_surat}/{bulan_romawi}/{tahun}',
  `margin_top` int(11) NOT NULL DEFAULT 30,
  `margin_bottom` int(11) NOT NULL DEFAULT 30,
  `margin_left` int(11) NOT NULL DEFAULT 30,
  `margin_right` int(11) NOT NULL DEFAULT 30,
  `paper_size` varchar(255) NOT NULL DEFAULT 'a4',
  `footer_text` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sig_x` int(11) DEFAULT NULL,
  `sig_y` int(11) DEFAULT NULL,
  `sig_page` int(11) NOT NULL DEFAULT 1,
  `sig_width` int(11) NOT NULL DEFAULT 150,
  `sig_height` int(11) NOT NULL DEFAULT 75,
  PRIMARY KEY (`id`),
  UNIQUE KEY `letter_templates_type_unique` (`type`),
  KEY `letter_templates_logo_asset_id_foreign` (`logo_asset_id`),
  KEY `letter_templates_lecturer_id_foreign` (`lecturer_id`),
  CONSTRAINT `letter_templates_lecturer_id_foreign` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `letter_templates_logo_asset_id_foreign` FOREIGN KEY (`logo_asset_id`) REFERENCES `document_assets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `letter_templates`
--

LOCK TABLES `letter_templates` WRITE;
/*!40000 ALTER TABLE `letter_templates` DISABLE KEYS */;
INSERT INTO `letter_templates` VALUES (1,'internship',NULL,NULL,NULL,NULL,NULL,NULL,'Tangerang','P-IF/Pradita','Surat Pengantar Kerja Praktik','Dengan hormat,\r\n\r\nBerkaitan dengan Kurikulum Program Studi Informatika Universitas Pradita, setiap mahasiswa diwajibkan untuk melaksanakan Kerja Praktik di suatu instansi/perusahaan sebagai salah satu syarat kelulusan. Oleh karena itu, kami mohon bantuan Bapak/Ibu untuk dapat menerima mahasiswa kami:','Untuk melaksanakan Kerja Praktik di instansi/perusahaan yang Bapak/Ibu pimpin.','Demikian permohonan ini kami sampaikan. Atas perhatian dan kerjasamanya, kami mengucapkan terima kasih.',NULL,NULL,NULL,NULL,NULL,'{nomor_urut}/{kode_surat}/{bulan_romawi}/{tahun}',30,30,30,30,'a4',NULL,'2026-09-16 03:12:35','2026-09-19 07:24:08',NULL,NULL,1,150,75);
/*!40000 ALTER TABLE `letter_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `letterhead_masters`
--

DROP TABLE IF EXISTS `letterhead_masters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `letterhead_masters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `university_name` varchar(255) NOT NULL DEFAULT 'Universitas Pradita',
  `faculty` varchar(255) DEFAULT NULL,
  `study_program` varchar(255) DEFAULT NULL,
  `campus_address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `active_version_id` bigint(20) unsigned DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `letterhead_masters_code_unique` (`code`),
  KEY `letterhead_masters_created_by_foreign` (`created_by`),
  KEY `letterhead_masters_updated_by_foreign` (`updated_by`),
  KEY `letterhead_masters_active_version_id_foreign` (`active_version_id`),
  CONSTRAINT `letterhead_masters_active_version_id_foreign` FOREIGN KEY (`active_version_id`) REFERENCES `letterhead_versions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `letterhead_masters_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `letterhead_masters_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `letterhead_masters`
--

LOCK TABLES `letterhead_masters` WRITE;
/*!40000 ALTER TABLE `letterhead_masters` DISABLE KEYS */;
/*!40000 ALTER TABLE `letterhead_masters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `letterhead_versions`
--

DROP TABLE IF EXISTS `letterhead_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `letterhead_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `letterhead_master_id` bigint(20) unsigned NOT NULL,
  `version_number` int(11) NOT NULL DEFAULT 1,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `logo_asset_id` bigint(20) unsigned DEFAULT NULL,
  `header_html` text DEFAULT NULL,
  `header_layout` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`header_layout`)),
  `header_height` decimal(8,2) NOT NULL DEFAULT 100.00,
  `separator_style` varchar(255) NOT NULL DEFAULT 'solid',
  `separator_width` int(11) NOT NULL DEFAULT 2,
  `separator_color` varchar(255) NOT NULL DEFAULT '#000000',
  `footer_html` text DEFAULT NULL,
  `footer_layout` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`footer_layout`)),
  `footer_height` decimal(8,2) NOT NULL DEFAULT 30.00,
  `margin_top` decimal(8,2) NOT NULL DEFAULT 25.00,
  `margin_bottom` decimal(8,2) NOT NULL DEFAULT 25.00,
  `margin_left` decimal(8,2) NOT NULL DEFAULT 25.00,
  `margin_right` decimal(8,2) NOT NULL DEFAULT 25.00,
  `change_notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `letterhead_versions_letterhead_master_id_foreign` (`letterhead_master_id`),
  KEY `letterhead_versions_created_by_foreign` (`created_by`),
  KEY `letterhead_versions_logo_asset_id_foreign` (`logo_asset_id`),
  CONSTRAINT `letterhead_versions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `letterhead_versions_letterhead_master_id_foreign` FOREIGN KEY (`letterhead_master_id`) REFERENCES `letterhead_masters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `letterhead_versions_logo_asset_id_foreign` FOREIGN KEY (`logo_asset_id`) REFERENCES `document_assets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `letterhead_versions`
--

LOCK TABLES `letterhead_versions` WRITE;
/*!40000 ALTER TABLE `letterhead_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `letterhead_versions` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_07_10_071149_create_activity_types_table',1),(5,'2026_07_10_071149_create_forms_table',1),(6,'2026_07_10_071149_create_students_table',1),(7,'2026_07_10_071150_create_form_fields_table',1),(8,'2026_07_10_071150_create_submissions_table',1),(9,'2026_07_10_071151_create_academic_calendars_table',1),(10,'2026_07_10_071151_create_email_logs_table',1),(11,'2026_07_10_071151_create_reminder_recipients_table',1),(12,'2026_07_10_071152_create_submission_files_table',1),(13,'2026_07_10_083656_create_academic_calendar_events_table',1),(14,'2026_07_10_085557_enhance_academic_calendars_and_events',1),(15,'2026_07_10_093001_enhance_users_for_admin_auth',1),(16,'2026_07_10_093002_enhance_students_for_auth',1),(17,'2026_07_10_093003_create_student_registration_requests_table',1),(18,'2026_07_10_093004_create_authentication_logs_table',1),(19,'2026_07_10_095514_add_status_to_submissions_table',1),(20,'2026_07_10_100832_add_deleted_at_to_students_table',1),(21,'2026_07_10_210000_enhance_form_builder_tables',1),(22,'2026_07_11_051044_add_stable_keys_to_forms_tables',1),(23,'2026_07_11_051045_create_master_data_tables',1),(24,'2026_07_11_064210_add_display_order_to_forms_table',1),(25,'2026_07_11_070325_add_academic_management_fields_to_existing_tables',1),(26,'2026_07_11_070325_create_audit_logs_table',1),(27,'2026_07_11_070326_create_reviewer_assignments_table',1),(28,'2026_07_11_125228_create_letter_templates_table',1),(29,'2026_07_11_125229_create_internship_letter_requests_table',1),(30,'2026_07_11_125229_create_letter_number_sequences_table',1),(31,'2026_07_11_125230_create_letter_request_histories_table',1),(32,'2026_07_14_042856_create_signatories_table',1),(33,'2026_07_14_042902_create_signature_requests_table',1),(34,'2026_07_14_042908_create_signature_request_histories_table',1),(35,'2026_07_14_084857_make_signatory_id_nullable_in_signature_requests_table',1),(36,'2026_07_14_094132_add_management_columns_to_calendar_tables',1),(37,'2026_07_14_100119_create_email_blast_tables',1),(38,'2026_07_14_123123_create_defense_management_tables',1),(39,'2026_07_18_065144_create_document_templates_table',1),(40,'2026_07_18_073600_create_letterhead_tables',1),(41,'2026_07_18_073601_create_document_asset_tables',1),(42,'2026_07_18_073602_enhance_document_templates_and_create_versions',1),(43,'2026_07_18_073603_create_document_instance_tables',1),(44,'2026_07_28_091145_add_signatory_columns_to_lecturers_table',1),(45,'2026_07_28_091242_update_signature_requests_table_signatory_id_to_lecturer_id',1),(46,'2026_07_28_091810_drop_signatories_table',1),(47,'2026_07_28_105838_add_logo_and_lecturer_to_letter_templates_table',1),(48,'2026_09_18_134506_create_signature_placements_table',2),(49,'2026_09_19_134803_add_semester_and_letter_date_to_internship_letter_requests',3),(50,'2026_10_01_093908_create_letter_logbooks_table',4),(51,'2026_10_01_093957_add_signature_coordinates_to_letter_templates',5),(52,'2026_10_30_193000_sync_lecturer_user_accounts',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_banks`
--

DROP TABLE IF EXISTS `question_banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_banks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `validation_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`validation_rules`)),
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `max_files` int(11) DEFAULT NULL,
  `max_size_mb` int(11) DEFAULT NULL,
  `allowed_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_types`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_banks`
--

LOCK TABLES `question_banks` WRITE;
/*!40000 ALTER TABLE `question_banks` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_banks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reminder_recipients`
--

DROP TABLE IF EXISTS `reminder_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reminder_recipients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reminder_recipients_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reminder_recipients`
--

LOCK TABLES `reminder_recipients` WRITE;
/*!40000 ALTER TABLE `reminder_recipients` DISABLE KEYS */;
INSERT INTO `reminder_recipients` VALUES (1,'Kaprodi','kaprodi@pradita.ac.id','kaprodi','2026-09-04 01:01:57','2026-09-04 01:01:57'),(2,'Sekprodi','sekprodi@pradita.ac.id','sekprodi','2026-09-04 01:01:57','2026-09-04 01:01:57');
/*!40000 ALTER TABLE `reminder_recipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviewer_assignments`
--

DROP TABLE IF EXISTS `reviewer_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviewer_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `submission_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Belum Diperiksa',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviewer_assignments_submission_id_foreign` (`submission_id`),
  KEY `reviewer_assignments_user_id_foreign` (`user_id`),
  CONSTRAINT `reviewer_assignments_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviewer_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviewer_assignments`
--

LOCK TABLES `reviewer_assignments` WRITE;
/*!40000 ALTER TABLE `reviewer_assignments` DISABLE KEYS */;
INSERT INTO `reviewer_assignments` VALUES (1,1,1,'Belum Diperiksa','2026-10-01 00:53:54','2026-10-01 00:53:54'),(2,103,1,'Selesai','2026-10-30 02:34:05','2026-10-30 02:34:10');
/*!40000 ALTER TABLE `reviewer_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rubric_items`
--

DROP TABLE IF EXISTS `rubric_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rubric_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rubric_section_id` bigint(20) unsigned NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `max_score` int(11) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rubric_items_rubric_section_id_foreign` (`rubric_section_id`),
  CONSTRAINT `rubric_items_rubric_section_id_foreign` FOREIGN KEY (`rubric_section_id`) REFERENCES `rubric_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rubric_items`
--

LOCK TABLES `rubric_items` WRITE;
/*!40000 ALTER TABLE `rubric_items` DISABLE KEYS */;
INSERT INTO `rubric_items` VALUES (1,1,'A1','Referensi/Studi Pustaka: Keterbaruan bahan pustaka yang digunakan dalam penulisan laporan serta banyaknya referensi terkait.',10,1,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(2,1,'A2','Keaktifan mahasiswa berkonsultasi dengan dosen pembimbing untuk mendiskusikan laporan.',10,2,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(3,2,'B1','Kemampuan menulis laporan: Kepatuhan penulisan ilmiah sesuai buku petunjuk kerja praktik, termasuk penggunaan Bahasa Indonesia yang baik dan benar.',20,1,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(4,2,'B2','Hasil Pengamatan: Ketelitian memilih observasi lapangan yang diangkat menjadi topik.',30,2,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(5,2,'B3','Topik/Pembahasan: Ketajaman menganalisis permasalahan pada topik.',30,3,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(6,3,'A1','Materi Laporan: Penguasaan mahasiswa terhadap materi laporan kerja praktik.',15,1,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(7,3,'A2','Materi Presentasi: Penguasaan mahasiswa terhadap materi presentasi.',15,2,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(8,3,'A3','Penyajian: Strukturisasi penyajian presentasi yang disampaikan.',10,3,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(9,4,'B1','Topik/Pembahasan: Ketepatan memilih temuan observasi dan menganalisis temuan observasi.',15,1,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(10,4,'B2','Manfaat pembahasan: Pembahasan memberikan manfaat dan pembekalan pekerjaan lapangan bagi peserta kerja praktik.',15,2,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(11,5,'C1','Sikap: Kepatuhan mahasiswa terhadap tata tertib sidang.',10,1,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(12,5,'C2','Presentasi: Efektivitas presentasi selama persidangan.',10,2,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(13,5,'C3','Kemampuan penyajian dan menjawab pertanyaan: Kejelasan serta sistematika presentasi dan jawaban.',10,3,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(14,6,'A1','Mahasiswa mampu mengidentifikasi isu utama dalam masalah di lapangan dan berinisiatif selama kerja praktik.',100,1,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(15,7,'B1','Kemampuan berkomunikasi secara lisan.',100,1,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(16,7,'B2','Kemampuan berkomunikasi secara tertulis.',100,2,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(17,8,'C1','Sopan santun selama kerja praktik.',100,1,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(18,8,'C2','Menghormati sesama selama kerja praktik.',100,2,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(19,9,'D1','Kedisiplinan waktu dalam bekerja.',100,1,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(20,9,'D2','Ketaatan terhadap peraturan perusahaan.',100,2,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(21,10,'E1','Kemampuan bekerja secara mandiri.',100,1,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(22,10,'E2','Kemampuan bekerja dalam tim.',100,2,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(23,11,'A1','Referensi/Studi Pustaka: Keterbaruan bahan pustaka yang digunakan dalam penulisan laporan serta banyaknya referensi terkait.',10,1,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(24,11,'A2','Keaktifan mahasiswa berkonsultasi dengan dosen pembimbing untuk mendiskusikan laporan.',10,2,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(25,12,'B1','Kemampuan menulis laporan: Kepatuhan penulisan ilmiah sesuai buku petunjuk kerja praktik, termasuk penggunaan Bahasa Indonesia yang baik dan benar.',20,1,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(26,12,'B2','Hasil Pengamatan: Ketelitian memilih observasi lapangan yang diangkat menjadi topik.',30,2,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(27,12,'B3','Topik/Pembahasan: Ketajaman menganalisis permasalahan pada topik.',30,3,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(28,13,'A1','Materi Laporan: Penguasaan mahasiswa terhadap materi laporan kerja praktik.',15,1,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(29,13,'A2','Materi Presentasi: Penguasaan mahasiswa terhadap materi presentasi.',15,2,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(30,13,'A3','Penyajian: Strukturisasi penyajian presentasi yang disampaikan.',10,3,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(31,14,'B1','Topik/Pembahasan: Ketepatan memilih temuan observasi dan menganalisis temuan observasi.',15,1,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(32,14,'B2','Manfaat pembahasan: Pembahasan memberikan manfaat dan pembekalan pekerjaan lapangan bagi peserta kerja praktik.',15,2,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(33,15,'C1','Sikap: Kepatuhan mahasiswa terhadap tata tertib sidang.',10,1,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(34,15,'C2','Presentasi: Efektivitas presentasi selama persidangan.',10,2,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(35,15,'C3','Kemampuan penyajian dan menjawab pertanyaan: Kejelasan serta sistematika presentasi dan jawaban.',10,3,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(36,16,'A1','Mahasiswa mampu mengidentifikasi isu utama dalam masalah di lapangan dan berinisiatif selama kerja praktik.',100,1,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(37,17,'B1','Kemampuan berkomunikasi secara lisan.',100,1,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(38,17,'B2','Kemampuan berkomunikasi secara tertulis.',100,2,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(39,18,'C1','Sopan santun selama kerja praktik.',100,1,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(40,18,'C2','Menghormati sesama selama kerja praktik.',100,2,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(41,19,'D1','Kedisiplinan waktu dalam bekerja.',100,1,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(42,19,'D2','Ketaatan terhadap peraturan perusahaan.',100,2,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(43,20,'E1','Kemampuan bekerja secara mandiri.',100,1,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(44,20,'E2','Kemampuan bekerja dalam tim.',100,2,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(45,21,'A1','Referensi/Studi Pustaka: Keterbaruan bahan pustaka yang digunakan dalam penulisan laporan serta banyaknya referensi terkait.',10,1,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(46,21,'A2','Keaktifan mahasiswa berkonsultasi dengan dosen pembimbing untuk mendiskusikan laporan.',10,2,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(47,22,'B1','Kemampuan menulis laporan: Kepatuhan penulisan ilmiah sesuai buku petunjuk kerja praktik, termasuk penggunaan Bahasa Indonesia yang baik dan benar.',20,1,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(48,22,'B2','Hasil Pengamatan: Ketelitian memilih observasi lapangan yang diangkat menjadi topik.',30,2,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(49,22,'B3','Topik/Pembahasan: Ketajaman menganalisis permasalahan pada topik.',30,3,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(50,23,'A1','Materi Laporan: Penguasaan mahasiswa terhadap materi laporan kerja praktik.',15,1,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(51,23,'A2','Materi Presentasi: Penguasaan mahasiswa terhadap materi presentasi.',15,2,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(52,23,'A3','Penyajian: Strukturisasi penyajian presentasi yang disampaikan.',10,3,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(53,24,'B1','Topik/Pembahasan: Ketepatan memilih temuan observasi dan menganalisis temuan observasi.',15,1,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(54,24,'B2','Manfaat pembahasan: Pembahasan memberikan manfaat dan pembekalan pekerjaan lapangan bagi peserta kerja praktik.',15,2,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(55,25,'C1','Sikap: Kepatuhan mahasiswa terhadap tata tertib sidang.',10,1,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(56,25,'C2','Presentasi: Efektivitas presentasi selama persidangan.',10,2,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(57,25,'C3','Kemampuan penyajian dan menjawab pertanyaan: Kejelasan serta sistematika presentasi dan jawaban.',10,3,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(58,26,'A1','Mahasiswa mampu mengidentifikasi isu utama dalam masalah di lapangan dan berinisiatif selama kerja praktik.',100,1,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(59,27,'B1','Kemampuan berkomunikasi secara lisan.',100,1,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(60,27,'B2','Kemampuan berkomunikasi secara tertulis.',100,2,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(61,28,'C1','Sopan santun selama kerja praktik.',100,1,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(62,28,'C2','Menghormati sesama selama kerja praktik.',100,2,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(63,29,'D1','Kedisiplinan waktu dalam bekerja.',100,1,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(64,29,'D2','Ketaatan terhadap peraturan perusahaan.',100,2,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(65,30,'E1','Kemampuan bekerja secara mandiri.',100,1,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(66,30,'E2','Kemampuan bekerja dalam tim.',100,2,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(67,31,'A1','Referensi/Studi Pustaka: Keterbaruan bahan pustaka yang digunakan dalam penulisan laporan serta banyaknya referensi terkait.',10,1,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(68,31,'A2','Keaktifan mahasiswa berkonsultasi dengan dosen pembimbing untuk mendiskusikan laporan.',10,2,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(69,32,'B1','Kemampuan menulis laporan: Kepatuhan penulisan ilmiah sesuai buku petunjuk kerja praktik, termasuk penggunaan Bahasa Indonesia yang baik dan benar.',20,1,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(70,32,'B2','Hasil Pengamatan: Ketelitian memilih observasi lapangan yang diangkat menjadi topik.',30,2,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(71,32,'B3','Topik/Pembahasan: Ketajaman menganalisis permasalahan pada topik.',30,3,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(72,33,'A1','Materi Laporan: Penguasaan mahasiswa terhadap materi laporan kerja praktik.',15,1,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(73,33,'A2','Materi Presentasi: Penguasaan mahasiswa terhadap materi presentasi.',15,2,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(74,33,'A3','Penyajian: Strukturisasi penyajian presentasi yang disampaikan.',10,3,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(75,34,'B1','Topik/Pembahasan: Ketepatan memilih temuan observasi dan menganalisis temuan observasi.',15,1,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(76,34,'B2','Manfaat pembahasan: Pembahasan memberikan manfaat dan pembekalan pekerjaan lapangan bagi peserta kerja praktik.',15,2,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(77,35,'C1','Sikap: Kepatuhan mahasiswa terhadap tata tertib sidang.',10,1,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(78,35,'C2','Presentasi: Efektivitas presentasi selama persidangan.',10,2,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(79,35,'C3','Kemampuan penyajian dan menjawab pertanyaan: Kejelasan serta sistematika presentasi dan jawaban.',10,3,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(80,36,'A1','Mahasiswa mampu mengidentifikasi isu utama dalam masalah di lapangan dan berinisiatif selama kerja praktik.',100,1,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(81,37,'B1','Kemampuan berkomunikasi secara lisan.',100,1,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(82,37,'B2','Kemampuan berkomunikasi secara tertulis.',100,2,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(83,38,'C1','Sopan santun selama kerja praktik.',100,1,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(84,38,'C2','Menghormati sesama selama kerja praktik.',100,2,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(85,39,'D1','Kedisiplinan waktu dalam bekerja.',100,1,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(86,39,'D2','Ketaatan terhadap peraturan perusahaan.',100,2,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(87,40,'E1','Kemampuan bekerja secara mandiri.',100,1,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(88,40,'E2','Kemampuan bekerja dalam tim.',100,2,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(89,41,'A1','Referensi/Studi Pustaka: Keterbaruan bahan pustaka yang digunakan dalam penulisan laporan serta banyaknya referensi terkait.',10,1,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(90,41,'A2','Keaktifan mahasiswa berkonsultasi dengan dosen pembimbing untuk mendiskusikan laporan.',10,2,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(91,42,'B1','Kemampuan menulis laporan: Kepatuhan penulisan ilmiah sesuai buku petunjuk kerja praktik, termasuk penggunaan Bahasa Indonesia yang baik dan benar.',20,1,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(92,42,'B2','Hasil Pengamatan: Ketelitian memilih observasi lapangan yang diangkat menjadi topik.',30,2,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(93,42,'B3','Topik/Pembahasan: Ketajaman menganalisis permasalahan pada topik.',30,3,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(94,43,'A1','Materi Laporan: Penguasaan mahasiswa terhadap materi laporan kerja praktik.',15,1,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(95,43,'A2','Materi Presentasi: Penguasaan mahasiswa terhadap materi presentasi.',15,2,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(96,43,'A3','Penyajian: Strukturisasi penyajian presentasi yang disampaikan.',10,3,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(97,44,'B1','Topik/Pembahasan: Ketepatan memilih temuan observasi dan menganalisis temuan observasi.',15,1,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(98,44,'B2','Manfaat pembahasan: Pembahasan memberikan manfaat dan pembekalan pekerjaan lapangan bagi peserta kerja praktik.',15,2,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(99,45,'C1','Sikap: Kepatuhan mahasiswa terhadap tata tertib sidang.',10,1,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(100,45,'C2','Presentasi: Efektivitas presentasi selama persidangan.',10,2,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(101,45,'C3','Kemampuan penyajian dan menjawab pertanyaan: Kejelasan serta sistematika presentasi dan jawaban.',10,3,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(102,46,'A1','Mahasiswa mampu mengidentifikasi isu utama dalam masalah di lapangan dan berinisiatif selama kerja praktik.',100,1,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(103,47,'B1','Kemampuan berkomunikasi secara lisan.',100,1,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(104,47,'B2','Kemampuan berkomunikasi secara tertulis.',100,2,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(105,48,'C1','Sopan santun selama kerja praktik.',100,1,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(106,48,'C2','Menghormati sesama selama kerja praktik.',100,2,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(107,49,'D1','Kedisiplinan waktu dalam bekerja.',100,1,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(108,49,'D2','Ketaatan terhadap peraturan perusahaan.',100,2,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(109,50,'E1','Kemampuan bekerja secara mandiri.',100,1,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(110,50,'E2','Kemampuan bekerja dalam tim.',100,2,'2026-10-30 02:07:59','2026-10-30 02:07:59');
/*!40000 ALTER TABLE `rubric_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rubric_sections`
--

DROP TABLE IF EXISTS `rubric_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rubric_sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rubric_version_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `max_score` int(11) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rubric_sections_rubric_version_id_foreign` (`rubric_version_id`),
  CONSTRAINT `rubric_sections_rubric_version_id_foreign` FOREIGN KEY (`rubric_version_id`) REFERENCES `rubric_versions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rubric_sections`
--

LOCK TABLES `rubric_sections` WRITE;
/*!40000 ALTER TABLE `rubric_sections` DISABLE KEYS */;
INSERT INTO `rubric_sections` VALUES (1,1,'A. Ketekunan Mahasiswa',20,1,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(2,1,'B. Isi Laporan',80,2,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(3,2,'A. Pemahaman Isi Laporan',40,1,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(4,2,'B. Laporan Kerja Praktik',30,2,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(5,2,'C. Sidang/Seminar Kerja Praktik',30,3,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(6,3,'A. Berpikir kritis, kreatif/inisiatif, dan analitis',100,1,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(7,3,'B. Komunikasi',200,2,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(8,3,'C. Etika',200,3,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(9,3,'D. Kedisiplinan',200,4,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(10,3,'E. Kerja mandiri dan tim',200,5,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(11,4,'A. Ketekunan Mahasiswa',20,1,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(12,4,'B. Isi Laporan',80,2,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(13,5,'A. Pemahaman Isi Laporan',40,1,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(14,5,'B. Laporan Kerja Praktik',30,2,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(15,5,'C. Sidang/Seminar Kerja Praktik',30,3,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(16,6,'A. Berpikir kritis, kreatif/inisiatif, dan analitis',100,1,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(17,6,'B. Komunikasi',200,2,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(18,6,'C. Etika',200,3,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(19,6,'D. Kedisiplinan',200,4,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(20,6,'E. Kerja mandiri dan tim',200,5,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(21,7,'A. Ketekunan Mahasiswa',20,1,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(22,7,'B. Isi Laporan',80,2,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(23,8,'A. Pemahaman Isi Laporan',40,1,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(24,8,'B. Laporan Kerja Praktik',30,2,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(25,8,'C. Sidang/Seminar Kerja Praktik',30,3,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(26,9,'A. Berpikir kritis, kreatif/inisiatif, dan analitis',100,1,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(27,9,'B. Komunikasi',200,2,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(28,9,'C. Etika',200,3,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(29,9,'D. Kedisiplinan',200,4,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(30,9,'E. Kerja mandiri dan tim',200,5,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(31,10,'A. Ketekunan Mahasiswa',20,1,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(32,10,'B. Isi Laporan',80,2,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(33,11,'A. Pemahaman Isi Laporan',40,1,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(34,11,'B. Laporan Kerja Praktik',30,2,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(35,11,'C. Sidang/Seminar Kerja Praktik',30,3,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(36,12,'A. Berpikir kritis, kreatif/inisiatif, dan analitis',100,1,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(37,12,'B. Komunikasi',200,2,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(38,12,'C. Etika',200,3,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(39,12,'D. Kedisiplinan',200,4,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(40,12,'E. Kerja mandiri dan tim',200,5,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(41,13,'A. Ketekunan Mahasiswa',20,1,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(42,13,'B. Isi Laporan',80,2,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(43,14,'A. Pemahaman Isi Laporan',40,1,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(44,14,'B. Laporan Kerja Praktik',30,2,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(45,14,'C. Sidang/Seminar Kerja Praktik',30,3,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(46,15,'A. Berpikir kritis, kreatif/inisiatif, dan analitis',100,1,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(47,15,'B. Komunikasi',200,2,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(48,15,'C. Etika',200,3,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(49,15,'D. Kedisiplinan',200,4,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(50,15,'E. Kerja mandiri dan tim',200,5,'2026-10-30 02:07:59','2026-10-30 02:07:59');
/*!40000 ALTER TABLE `rubric_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rubric_versions`
--

DROP TABLE IF EXISTS `rubric_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rubric_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `defense_type` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `version_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `weight_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rubric_versions`
--

LOCK TABLES `rubric_versions` WRITE;
/*!40000 ALTER TABLE `rubric_versions` DISABLE KEYS */;
INSERT INTO `rubric_versions` VALUES (1,'internship_defense','supervisor','v1.0',1,30.00,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(2,'internship_defense','examiner','v1.0',1,30.00,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(3,'internship_defense','mentor','v1.0',1,40.00,'2026-10-30 02:03:42','2026-10-30 02:03:42'),(4,'internship_defense','supervisor','v1.0',1,30.00,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(5,'internship_defense','examiner','v1.0',1,30.00,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(6,'internship_defense','mentor','v1.0',1,40.00,'2026-10-30 02:07:01','2026-10-30 02:07:01'),(7,'internship_defense','supervisor','v1.0',1,30.00,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(8,'internship_defense','examiner','v1.0',1,30.00,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(9,'internship_defense','mentor','v1.0',1,40.00,'2026-10-30 02:07:18','2026-10-30 02:07:18'),(10,'internship_defense','supervisor','v1.0',1,30.00,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(11,'internship_defense','examiner','v1.0',1,30.00,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(12,'internship_defense','mentor','v1.0',1,40.00,'2026-10-30 02:07:35','2026-10-30 02:07:35'),(13,'internship_defense','supervisor','v1.0',1,30.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(14,'internship_defense','examiner','v1.0',1,30.00,'2026-10-30 02:07:59','2026-10-30 02:07:59'),(15,'internship_defense','mentor','v1.0',1,40.00,'2026-10-30 02:07:59','2026-10-30 02:07:59');
/*!40000 ALTER TABLE `rubric_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saved_audiences`
--

DROP TABLE IF EXISTS `saved_audiences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saved_audiences` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `filter_criteria` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`filter_criteria`)),
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `saved_audiences_created_by_foreign` (`created_by`),
  CONSTRAINT `saved_audiences_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saved_audiences`
--

LOCK TABLES `saved_audiences` WRITE;
/*!40000 ALTER TABLE `saved_audiences` DISABLE KEYS */;
/*!40000 ALTER TABLE `saved_audiences` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signature_placements`
--

DROP TABLE IF EXISTS `signature_placements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `signature_placements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `signature_request_id` bigint(20) unsigned NOT NULL,
  `lecturer_id` bigint(20) unsigned DEFAULT NULL,
  `page_number` int(11) NOT NULL DEFAULT 1,
  `x_pos` double DEFAULT NULL,
  `y_pos` double DEFAULT NULL,
  `width` double DEFAULT NULL,
  `height` double DEFAULT NULL,
  `include_stamp` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `signature_placements_signature_request_id_foreign` (`signature_request_id`),
  KEY `signature_placements_lecturer_id_foreign` (`lecturer_id`),
  CONSTRAINT `signature_placements_lecturer_id_foreign` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `signature_placements_signature_request_id_foreign` FOREIGN KEY (`signature_request_id`) REFERENCES `signature_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signature_placements`
--

LOCK TABLES `signature_placements` WRITE;
/*!40000 ALTER TABLE `signature_placements` DISABLE KEYS */;
INSERT INTO `signature_placements` VALUES (15,6,1,1,144.29,247.18,40,24.06,0,'2026-09-19 05:41:08','2026-09-19 05:41:08'),(16,6,1,2,122.98,182.64,62.49,43.67,0,'2026-09-19 05:41:08','2026-09-19 05:41:08'),(17,6,2,2,24.79,209.69,61.78,33.96,0,'2026-09-19 05:41:08','2026-09-19 05:41:08'),(18,6,2,3,77.53,29,61.07,43.91,1,'2026-09-19 05:41:08','2026-09-19 05:41:08'),(19,7,2,1,31,240.24,45.07,36.07,0,'2026-10-01 01:07:16','2026-10-01 01:07:16'),(20,7,2,3,26.46,205.69,81.95,42.88,0,'2026-10-01 01:07:16','2026-10-01 01:07:16'),(21,8,2,3,122.96,220.07,77.25,45.53,0,'2026-10-20 02:51:43','2026-10-20 02:51:43'),(22,8,2,4,57.04,88.64,93.78,47,1,'2026-10-20 02:51:43','2026-10-20 02:51:43'),(23,8,1,3,-0,216.05,97.55,62.07,0,'2026-10-20 02:51:43','2026-10-20 02:51:43');
/*!40000 ALTER TABLE `signature_placements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signature_request_histories`
--

DROP TABLE IF EXISTS `signature_request_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `signature_request_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `signature_request_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `signature_request_histories_signature_request_id_foreign` (`signature_request_id`),
  KEY `signature_request_histories_user_id_foreign` (`user_id`),
  CONSTRAINT `signature_request_histories_signature_request_id_foreign` FOREIGN KEY (`signature_request_id`) REFERENCES `signature_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `signature_request_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signature_request_histories`
--

LOCK TABLES `signature_request_histories` WRITE;
/*!40000 ALTER TABLE `signature_request_histories` DISABLE KEYS */;
INSERT INTO `signature_request_histories` VALUES (3,6,NULL,'email_sent','Email sent to student successfully.','2026-09-19 05:42:11','2026-09-19 05:42:11'),(4,7,NULL,'email_sent','Email sent to student successfully.','2026-10-01 01:07:41','2026-10-01 01:07:41'),(5,8,NULL,'email_sent','Email sent to student successfully.','2026-10-20 02:52:15','2026-10-20 02:52:15');
/*!40000 ALTER TABLE `signature_request_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signature_requests`
--

DROP TABLE IF EXISTS `signature_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `signature_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `letter_type` varchar(255) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `lecturer_id` bigint(20) unsigned DEFAULT NULL,
  `original_file_path` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `signed_file_path` varchar(255) DEFAULT NULL,
  `page_number` int(11) DEFAULT NULL,
  `x_pos` double DEFAULT NULL,
  `y_pos` double DEFAULT NULL,
  `width` double DEFAULT NULL,
  `height` double DEFAULT NULL,
  `page_width` double DEFAULT NULL,
  `page_height` double DEFAULT NULL,
  `rotation` double DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `reviewer_id` bigint(20) unsigned DEFAULT NULL,
  `approver_id` bigint(20) unsigned DEFAULT NULL,
  `revision_note` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `original_checksum` varchar(255) DEFAULT NULL,
  `signed_checksum` varchar(255) DEFAULT NULL,
  `email_error` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `signed_at` timestamp NULL DEFAULT NULL,
  `emailed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `signature_requests_student_id_foreign` (`student_id`),
  KEY `signature_requests_reviewer_id_foreign` (`reviewer_id`),
  KEY `signature_requests_approver_id_foreign` (`approver_id`),
  CONSTRAINT `signature_requests_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `signature_requests_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `signature_requests_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signature_requests`
--

LOCK TABLES `signature_requests` WRITE;
/*!40000 ALTER TABLE `signature_requests` DISABLE KEYS */;
INSERT INTO `signature_requests` VALUES (6,123,'d\\xc',NULL,NULL,NULL,1,'signature-requests/original/I0J9m61movpM7GG255NUBvTY9lhfPGuVqHF2b3XG.pdf','2eben-FR-BM-13 Formulir Persetujuan Kegiatan MBKM.docx.pdf','signed/b2f32f51-e6ed-40e0-aac7-a6ef6e85e60c_2eben-FR-BM-13 Formulir Persetujuan Kegiatan MBKM.docx.pdf',1,0,0,40,20,210,297,NULL,'completed',NULL,3,NULL,NULL,'5289ad5e235072323759d3619fa085c797fd5112fb68a4339ef9f88c7eb595be','1dc7788216f67b08f72290255763f6704a645055f094a7e684348e5c6b7279e2',NULL,'2026-09-19 05:39:33','2026-09-19 05:41:08','2026-09-19 05:42:11','2026-09-19 05:39:33','2026-09-19 05:42:11'),(7,123,'Minta ttd',NULL,NULL,NULL,2,'signature-requests/original/PIeaRR43mIMZQ0Ni5BxDqZmEsMNDURArwhgYRKp3.pdf','Bryan-FR-BM-12 Formulir Pendaftaran Kegiatan MBKM untuk mahasiswa pradita.pdf','signed/1b800695-94e8-4c82-bbb9-d67e4f47dd21_Bryan-FR-BM-12 Formulir Pendaftaran Kegiatan MBKM untuk mahasiswa pradita.pdf',1,0,0,40,20,210,297,NULL,'completed',NULL,1,NULL,NULL,'0dc73b5811354a933552e10a51dd9ad4602fd25e17f85ffd4c8433582756f454','05df30c7e72a89888682e1b04dd0fbd9dcf186989b8c00630ccd520970371632',NULL,'2026-10-01 01:05:59','2026-10-01 01:07:16','2026-10-01 01:07:41','2026-10-01 01:05:59','2026-10-01 01:07:41'),(8,123,'djujthr','drbty','drtyhrd','bb',2,'signature-requests/original/gYtFGh09wAcKGrguAdOlKuZXzBK7Nt17ajWn53Lh.pdf','Bryan-FR-BM-12 Formulir Pendaftaran Kegiatan MBKM untuk mahasiswa pradita.pdf','signed/485e490d-e728-4797-a324-77622beda831_Bryan-FR-BM-12 Formulir Pendaftaran Kegiatan MBKM untuk mahasiswa pradita.pdf',1,0,0,40,20,210,297,NULL,'completed',NULL,1,NULL,NULL,'0dc73b5811354a933552e10a51dd9ad4602fd25e17f85ffd4c8433582756f454','369356185c65375092c91fc1d7170bc75cf2a03cb597e212c9442172b586c773',NULL,'2026-10-20 02:48:55','2026-10-20 02:51:44','2026-10-20 02:52:15','2026-10-20 02:48:55','2026-10-20 02:52:15');
/*!40000 ALTER TABLE `signature_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_registration_requests`
--

DROP TABLE IF EXISTS `student_registration_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_registration_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `google_id` varchar(255) DEFAULT NULL,
  `google_email` varchar(255) NOT NULL,
  `normalized_email` varchar(255) NOT NULL,
  `nim` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `angkatan` varchar(255) NOT NULL,
  `google_avatar` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `conflict_type` varchar(255) DEFAULT NULL,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `request_note` text DEFAULT NULL,
  `review_note` text DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_registration_requests_normalized_email_unique` (`normalized_email`),
  UNIQUE KEY `student_registration_requests_nim_unique` (`nim`),
  KEY `student_registration_requests_student_id_foreign` (`student_id`),
  KEY `student_registration_requests_reviewed_by_foreign` (`reviewed_by`),
  CONSTRAINT `student_registration_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_registration_requests_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_registration_requests`
--

LOCK TABLES `student_registration_requests` WRITE;
/*!40000 ALTER TABLE `student_registration_requests` DISABLE KEYS */;
INSERT INTO `student_registration_requests` VALUES (1,'108717049444662636234','januponsa.dio@student.pradita.ac.id','januponsa.dio@student.pradita.ac.id','2023012345','Januponsa Dio Firizqi','2023','https://lh3.googleusercontent.com/a/ACg8ocIlXA0-OGnNQgmjLpaPo7mnDkwSU2Ur3qKXqa7bUxiUWctizA=s96-c','approved',NULL,123,NULL,NULL,'2026-09-16 03:09:51','2026-09-16 03:10:07',1,NULL,'2026-09-16 03:10:07'),(2,'dummy_google_id_2310101007','farell.alvaro@student.pradita.ac.id','farell.alvaro@student.pradita.ac.id','2310101007','FARELL ALVARO THERIONO','2023',NULL,'approved',NULL,5,NULL,NULL,'2026-10-01 06:40:07','2026-10-02 06:40:07',1,'2026-10-01 06:40:07','2026-10-02 06:40:07');
/*!40000 ALTER TABLE `student_registration_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nim` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `normalized_email` varchar(255) DEFAULT NULL,
  `angkatan` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `academic_status` varchar(255) NOT NULL DEFAULT 'inactive',
  `login_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `approval_status` varchar(255) NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `source_type` varchar(255) DEFAULT NULL,
  `source_batch` varchar(255) DEFAULT NULL,
  `source_row` varchar(255) DEFAULT NULL,
  `source_hash` varchar(255) DEFAULT NULL,
  `manual_override` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status_akademik` varchar(255) NOT NULL DEFAULT 'Aktif',
  `status_akun` varchar(255) NOT NULL DEFAULT 'Login Diizinkan',
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_nim_unique` (`nim`),
  UNIQUE KEY `students_email_unique` (`email`),
  UNIQUE KEY `students_normalized_email_unique` (`normalized_email`),
  KEY `students_approved_by_foreign` (`approved_by`),
  CONSTRAINT `students_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'2310101001','MUHAMMAD SULTHAN ARIF RIZKY PRASETYO','muhammad.sulthan@student.pradita.ac.id','muhammad.sulthan@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(2,'2310101002','DASON TIOVINO','dason.tiovino@student.pradita.ac.id','dason.tiovino@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(3,'2310101003','MICKHAEL NIKLAS','mickhael.niklas@student.pradita.ac.id','mickhael.niklas@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(4,'2310101005','BRIAN ABRAHAM JORDAN SUNTPIET','brian.abraham@student.pradita.ac.id','brian.abraham@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(5,'2310101007','FARELL ALVARO THERIONO','farell.alvaro@student.pradita.ac.id','farell.alvaro@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(6,'2310101008','KENNETH WILLIAM GUNAWAN','kenneth.william@student.pradita.ac.id','kenneth.william@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(7,'2310101010','ANDREW JATZEN LAY','andrew.jatzen@student.pradita.ac.id','andrew.jatzen@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(8,'2310101011','BRYAN ADIPUTRA ONGGO','bryan.adiputra@student.pradita.ac.id','bryan.adiputra@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(9,'2310101012','JONATHAN AXEL TEDY ASIHTO','jonathan.axel@student.pradita.ac.id','jonathan.axel@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(10,'2310101015','MUHAMMAD WAHYU HIDAYAT','muhammad.wahyu@student.pradita.ac.id','muhammad.wahyu@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(11,'2310101016','NAUFAL FADHILLA','naufal.fadhilla@student.pradita.ac.id','naufal.fadhilla@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(12,'2310101017','ALEJANDRO RAFAEL','alejandro.rafael@student.pradita.ac.id','alejandro.rafael@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(13,'2310101020','WILLSON AUSTIN','willson.austin@student.pradita.ac.id','willson.austin@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(14,'2310101021','AFFAN MAULANA','affan.maulana@student.pradita.ac.id','affan.maulana@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(15,'2310101023','ARIYA PANNA','ariya.panna@student.pradita.ac.id','ariya.panna@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(16,'2310101024','ASTRIA FEBRIAN ANGGRAINI','astria.febrian@student.pradita.ac.id','astria.febrian@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(17,'2310101025','NADYA RUDIE SUCIPTO','nadya.rudie@student.pradita.ac.id','nadya.rudie@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(18,'2310101026','DEVIN TAN','devin.tan@student.pradita.ac.id','devin.tan@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(19,'2310101027','FITRAH FEBRIAN LISTANTO','fitrah.febrian@student.pradita.ac.id','fitrah.febrian@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(20,'2310101028','BOY DEVIANO','boy.deviano@student.pradita.ac.id','boy.deviano@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(21,'2310101031','RIVANDI ZEIN SAPUTRA','rivandi.zein@student.pradita.ac.id','rivandi.zein@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(22,'2310101032','KENNETH LODEWIJK IBRAHIM','kenneth.lodewijk@student.pradita.ac.id','kenneth.lodewijk@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(23,'2310101035','ERDINE A. TIMOTHY','erdine.a.@student.pradita.ac.id','erdine.a.@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(24,'2310101036','MUHAMMAD ZIKRY ANDRIAN ARDIANSYAH','muhammad.zikry@student.pradita.ac.id','muhammad.zikry@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(25,'2310101033','HANS HANDRI TANUJAYA','hans.handri@student.pradita.ac.id','hans.handri@student.pradita.ac.id','2023',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(26,'2410101001','WESLEY MAXIMILLIAN LAY','wesley.maximillian@student.pradita.ac.id','wesley.maximillian@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(27,'2410101002','JOVAN VITO VIANI','jovan.vito@student.pradita.ac.id','jovan.vito@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(28,'2410101003','RHIFQI SYAHPUTRA PRIYAMBODO','rhifqi.syahputra@student.pradita.ac.id','rhifqi.syahputra@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(29,'2410101004','BRYAN CHANDRA','bryan.chandra@student.pradita.ac.id','bryan.chandra@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(30,'2410101005','EKAHADI LUTHFI TEDJA','ekahadi.luthfi@student.pradita.ac.id','ekahadi.luthfi@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(31,'2410101006','BENTLEY ARSHAVIN','bentley.arshavin@student.pradita.ac.id','bentley.arshavin@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(32,'2410101008','CEELEY RICHELA','ceeley.richela@student.pradita.ac.id','ceeley.richela@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(33,'2410101009','CYRIL NATASHA SETIAWAN','cyril.natasha@student.pradita.ac.id','cyril.natasha@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(34,'2410101010','MUHAMMAD ARIQ FAKHRIZAKI PRABOWO','muhammad.ariq@student.pradita.ac.id','muhammad.ariq@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(35,'2410101011','NICOLAS JULIAN KURNIA PURWANTORO','nicolas.julian@student.pradita.ac.id','nicolas.julian@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(36,'2410101012','PIUS PETRA LOVINNO','pius.petra@student.pradita.ac.id','pius.petra@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(37,'2410101013','DIERLYAWAN WIGUNA','dierlyawan.wiguna@student.pradita.ac.id','dierlyawan.wiguna@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(38,'2410101014','STEPHANIE CHANDRA','stephanie.chandra@student.pradita.ac.id','stephanie.chandra@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(39,'2410101015','EBEN HEZER WANGSA DJAJA','eben.hezer@student.pradita.ac.id','eben.hezer@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(40,'2410101016','KEVIN SEBASTIAN WIBOWO','kevin.sebastian@student.pradita.ac.id','kevin.sebastian@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(41,'2410101017','MARK JUSTIN GUNAWAN','mark.justin@student.pradita.ac.id','mark.justin@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(42,'2410101018','LUISA HANING TYAS','luisa.haning@student.pradita.ac.id','luisa.haning@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(43,'2410101019','TIMOTHY ALBERT PRATAMA','timothy.albert@student.pradita.ac.id','timothy.albert@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(44,'2410101021','MICHAEL CHIANG','michael.chiang@student.pradita.ac.id','michael.chiang@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(45,'2410101024','JAMES TAN','james.tan@student.pradita.ac.id','james.tan@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(46,'2410101025','MIKO AFRIAN PEREY','miko.afrian@student.pradita.ac.id','miko.afrian@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(47,'2410101027','WILLY','willy@student.pradita.ac.id','willy@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(48,'2410101028','FAHREL HAFIZ AMARAL HAQ','fahrel.hafiz@student.pradita.ac.id','fahrel.hafiz@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(49,'2410101030','DEWA RESTU SATRIA','dewa.restu@student.pradita.ac.id','dewa.restu@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(50,'2410101031','FAIZ EKA SAKTI','faiz.eka@student.pradita.ac.id','faiz.eka@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(51,'2410101032','IMANUEL JONATAN','imanuel.jonatan@student.pradita.ac.id','imanuel.jonatan@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(52,'2410101033','MICHAEL HANSEL','michael.hansel@student.pradita.ac.id','michael.hansel@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(53,'2410101034','HENGKY','hengky@student.pradita.ac.id','hengky@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(54,'2410101035','DAVID CUNGNIAGO','david.cungniago@student.pradita.ac.id','david.cungniago@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(55,'2410101036','GIVEN','given@student.pradita.ac.id','given@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(56,'2410101037','JIMMY CHEN','jimmy.chen@student.pradita.ac.id','jimmy.chen@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(57,'2410101038','KHIAR ZAKI MAULANA','khiar.zaki@student.pradita.ac.id','khiar.zaki@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(58,'2410101039','GABRIEL CUPIDO KURNIAWAN','gabriel.cupido@student.pradita.ac.id','gabriel.cupido@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(59,'2410101040','MARCELINO RIZALDI','marcelino.rizaldi@student.pradita.ac.id','marcelino.rizaldi@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(60,'2410101041','JAMES EDISON WIHARDJA','james.edison@student.pradita.ac.id','james.edison@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(61,'2410101043','CELINE MERCY TAASIRINGAN','celine.mercy@student.pradita.ac.id','celine.mercy@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(62,'2410101044','CYNTHIA KEISHA','cynthia.keisha@student.pradita.ac.id','cynthia.keisha@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(63,'2410101045','MATTHEW KEANE','matthew.keane@student.pradita.ac.id','matthew.keane@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(64,'2410101046','WILLIAM SHE PUTRA','william.she@student.pradita.ac.id','william.she@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(65,'2410101047','HUGO ALEXANDER TANOTO','hugo.alexander@student.pradita.ac.id','hugo.alexander@student.pradita.ac.id','2024',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(66,'2510101001','ALBERT KRISHER CHU','albert.krisher@student.pradita.ac.id','albert.krisher@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(67,'2510101002','FREDERIC EMMANUEL','frederic.emmanuel@student.pradita.ac.id','frederic.emmanuel@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(68,'2510101003','DAVIN CORNELIUS','davin.cornelius@student.pradita.ac.id','davin.cornelius@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(69,'2510101004','ALDRY CHIAYARDI','aldry.chiayardi@student.pradita.ac.id','aldry.chiayardi@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(70,'2510101005','Inas Tsabitah Dien','inas.tsabitah@student.pradita.ac.id','inas.tsabitah@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(71,'2510101006','FIRMAN FELIX NDURU','firman.felix@student.pradita.ac.id','firman.felix@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(72,'2510101007','NEILVENT VICTOR CHENARIAS','neilvent.victor@student.pradita.ac.id','neilvent.victor@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(73,'2510101008','SYAFIQ SADIDDUL AZMI','syafiq.sadiddul@student.pradita.ac.id','syafiq.sadiddul@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(74,'2510101009','ALVINE TANSRI','alvine.tansri@student.pradita.ac.id','alvine.tansri@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(75,'2510101010','FELIX THEODORE','felix.theodore@student.pradita.ac.id','felix.theodore@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(76,'2510101011','SANATA RIJALUL FALAH KADARISMAN','sanata.rijalul@student.pradita.ac.id','sanata.rijalul@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(77,'2510101012','DAFFA ABDUR RASYID','daffa.abdur@student.pradita.ac.id','daffa.abdur@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(78,'2510101013','FISKO ARITMATIKA YURI','fisko.aritmatika@student.pradita.ac.id','fisko.aritmatika@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(79,'2510101014','RAZAN RAFI AKMALUZZUHAIR','razan.rafi@student.pradita.ac.id','razan.rafi@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(80,'2510101015','MUHAMMAD RAFI AL FATIH','muhammad.rafi@student.pradita.ac.id','muhammad.rafi@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(81,'2510101017','ADITYA PRATAMA HUTOMO ALIAS KWA THIAN SIN','aditya.pratama@student.pradita.ac.id','aditya.pratama@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(82,'2510101018','SHAKILA RAFIFAH ISDA PUTRI','shakila.rafifah@student.pradita.ac.id','shakila.rafifah@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(83,'2510101019','MARSEKAL MARKHE RONDONUWU','marsekal.markhe@student.pradita.ac.id','marsekal.markhe@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(84,'2510101020','SHANTITA PROMETTA','shantita.prometta@student.pradita.ac.id','shantita.prometta@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(85,'2510101021','VIREN CHANDRU KUMAR','viren.chandru@student.pradita.ac.id','viren.chandru@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(86,'2510101022','MUHAMMAD RAFIF ABRAR','muhammad.rafif@student.pradita.ac.id','muhammad.rafif@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(87,'2510101023','HYLMI RASHID AYDIN','hylmi.rashid@student.pradita.ac.id','hylmi.rashid@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(88,'2510101024','GHASSAN RIZKI RUSMANA','ghassan.rizki@student.pradita.ac.id','ghassan.rizki@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(89,'2510101025','CORLINE SHEREN HARLI','corline.sheren@student.pradita.ac.id','corline.sheren@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(90,'2510101026','ALFIO ACNEILSEN','alfio.acneilsen@student.pradita.ac.id','alfio.acneilsen@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(91,'2510101027','DIMAS AKMAL MUZAKKI','dimas.akmal@student.pradita.ac.id','dimas.akmal@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(92,'2510101028','BIJAN LUNGGA BHASMARA','bijan.lungga@student.pradita.ac.id','bijan.lungga@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(93,'2510101029','FARIDZ ARYA PASCAL','faridz.arya@student.pradita.ac.id','faridz.arya@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(94,'2510101030','MUHAMMAD RIDHO HIBATULLOH','muhammad.ridho@student.pradita.ac.id','muhammad.ridho@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(95,'2510101031','SINDAR FELIX SIHOLE','sindar.felix@student.pradita.ac.id','sindar.felix@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(96,'2510101032','MIFTAHUL MIDZAN NUR BAHRI','miftahul.midzan@student.pradita.ac.id','miftahul.midzan@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(97,'2510101033','SAZKIA HERFI SAKINAH','sazkia.herfi@student.pradita.ac.id','sazkia.herfi@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(98,'2510101034','WINSTAR SANTIAGO','winstar.santiago@student.pradita.ac.id','winstar.santiago@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(99,'2510101035','DEVIN DARMAWAN','devin.darmawan@student.pradita.ac.id','devin.darmawan@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(100,'2510101036','MAXMILLIANO GHAZYLLAH BUDI JUNIOR','maxmilliano.ghazyllah@student.pradita.ac.id','maxmilliano.ghazyllah@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(101,'2510101037','NICHOLAS WIJAYA','nicholas.wijaya@student.pradita.ac.id','nicholas.wijaya@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(102,'2510101038','SYDNEY CALISTA','sydney.calista@student.pradita.ac.id','sydney.calista@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(103,'2510101046','VYRGI RAPABEL FEVALEND','vyrgi.rapabel@student.pradita.ac.id','vyrgi.rapabel@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(104,'2510101048','DANIEL ABNER','daniel.abner@student.pradita.ac.id','daniel.abner@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(105,'2510101049','PUTRA MALIKI RAMADHAN','putra.maliki@student.pradita.ac.id','putra.maliki@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(106,'2510101050','MARCHEL YOSIA PARLINDUNGAN SIAHAAN','marchel.yosia@student.pradita.ac.id','marchel.yosia@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(107,'2510101051','SANTOS','santos@student.pradita.ac.id','santos@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(108,'2510101053','PASCAL EYOTA LUKMAN','pascal.eyota@student.pradita.ac.id','pascal.eyota@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(109,'2510101055','MISBAHUL HABIBI','misbahul.habibi@student.pradita.ac.id','misbahul.habibi@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(110,'2510101056','VIVIANG WENNIE','viviang.wennie@student.pradita.ac.id','viviang.wennie@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(111,'2510101057','LEONARD YEOH','leonard.yeoh@student.pradita.ac.id','leonard.yeoh@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(112,'2510101058','YOHAN S JALU PRAKASA','yohan.s@student.pradita.ac.id','yohan.s@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(113,'2510101059','FARHAN NIZAM SYA\'BAN AL BANTA','farhan.nizam@student.pradita.ac.id','farhan.nizam@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(114,'2510101060','MUHAMMAD RIZQI HANIF','muhammad.rizqi@student.pradita.ac.id','muhammad.rizqi@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(115,'2510101061','WIJDAN YAZID ANANDA','wijdan.yazid@student.pradita.ac.id','wijdan.yazid@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(116,'2510101062','RICHKY RIFADI','richky.rifadi@student.pradita.ac.id','richky.rifadi@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(117,'2510101064','ATHIF KHAIRULLAH','athif.khairullah@student.pradita.ac.id','athif.khairullah@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(118,'2510101069','ANTONIUS ARRYA AL YOHRI','antonius.arrya@student.pradita.ac.id','antonius.arrya@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(119,'2510101070','MUHAMMAD FIKA AULIA RIZQI','muhammad.fika@student.pradita.ac.id','muhammad.fika@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(120,'2510101071','RANU IKRAM MILAN LONDI','ranu.ikram@student.pradita.ac.id','ranu.ikram@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(121,'2510101074','RIDHO PASHA DWI KURNIAWAN','ridho.pasha@student.pradita.ac.id','ridho.pasha@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(122,'2510101075','GABRIEL BENNY THEODORE SIMANJUNTAK','gabriel.benny@student.pradita.ac.id','gabriel.benny@student.pradita.ac.id','2025',NULL,NULL,'active',1,'approved','2026-09-04 01:01:57','2026-09-04 01:01:57','2026-09-04 01:01:57',NULL,NULL,'official_roster','Data Mahasiswa IF 2023-2025',NULL,NULL,0,NULL,'Aktif','Login Diizinkan'),(123,'2023012345','Januponsa Dio Firizqi','januponsa.dio@student.pradita.ac.id','januponsa.dio@student.pradita.ac.id','2023','108717049444662636234','https://lh3.googleusercontent.com/a/ACg8ocIlXA0-OGnNQgmjLpaPo7mnDkwSU2Ur3qKXqa7bUxiUWctizA=s96-c','active',1,'approved','2026-09-16 03:10:07','2026-09-16 03:10:07','2026-10-30 05:24:52',1,'2026-10-30 05:24:52','self_registration',NULL,NULL,NULL,0,NULL,'Aktif','Login Diizinkan');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `study_focuses`
--

DROP TABLE IF EXISTS `study_focuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `study_focuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `study_focuses_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `study_focuses`
--

LOCK TABLES `study_focuses` WRITE;
/*!40000 ALTER TABLE `study_focuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `study_focuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submission_files`
--

DROP TABLE IF EXISTS `submission_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submission_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `submission_id` bigint(20) unsigned NOT NULL,
  `field_id` bigint(20) unsigned NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_path` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `size_bytes` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `review_status` varchar(255) DEFAULT NULL,
  `review_note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `submission_files_submission_id_foreign` (`submission_id`),
  KEY `submission_files_field_id_foreign` (`field_id`),
  CONSTRAINT `submission_files_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `form_fields` (`id`) ON DELETE CASCADE,
  CONSTRAINT `submission_files_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submission_files`
--

LOCK TABLES `submission_files` WRITE;
/*!40000 ALTER TABLE `submission_files` DISABLE KEYS */;
INSERT INTO `submission_files` VALUES (1,1,5,'Ceeley-Formulir Pendaftaran (FR-BM-12).docx.pdf','submissions/1/4UFNzIMm9kqdy2SWR2K9ieCs7D1dyFixqzdI722s.pdf','application/pdf',424599,'2026-10-01 07:53:41','2026-10-01 00:52:39','2026-10-01 00:53:41','Disetujui',''),(2,94,18,'Form_Nilai_Mentor_2310101001.pdf','submissions/mentor_eval_2310101001.pdf','application/pdf',125000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(3,94,19,'Laporan_KP_2310101001.pdf','submissions/report_2310101001.pdf','application/pdf',840000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(4,95,18,'Form_Nilai_Mentor_2310101002.pdf','submissions/mentor_eval_2310101002.pdf','application/pdf',125000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(5,95,19,'Laporan_KP_2310101002.pdf','submissions/report_2310101002.pdf','application/pdf',840000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(6,96,18,'Form_Nilai_Mentor_2310101003.pdf','submissions/mentor_eval_2310101003.pdf','application/pdf',125000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(7,96,19,'Laporan_KP_2310101003.pdf','submissions/report_2310101003.pdf','application/pdf',840000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(8,97,18,'Form_Nilai_Mentor_2310101005.pdf','submissions/mentor_eval_2310101005.pdf','application/pdf',125000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(9,97,19,'Laporan_KP_2310101005.pdf','submissions/report_2310101005.pdf','application/pdf',840000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(10,98,18,'Form_Nilai_Mentor_2310101007.pdf','submissions/mentor_eval_2310101007.pdf','application/pdf',125000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(11,98,19,'Laporan_KP_2310101007.pdf','submissions/report_2310101007.pdf','application/pdf',840000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(12,99,18,'Form_Nilai_Mentor_2310101008.pdf','submissions/mentor_eval_2310101008.pdf','application/pdf',125000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(13,99,19,'Laporan_KP_2310101008.pdf','submissions/report_2310101008.pdf','application/pdf',840000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(14,100,18,'Form_Nilai_Mentor_2310101010.pdf','submissions/mentor_eval_2310101010.pdf','application/pdf',125000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(15,100,19,'Laporan_KP_2310101010.pdf','submissions/report_2310101010.pdf','application/pdf',840000,'2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59',NULL,NULL),(16,101,18,'Form_Nilai_Mentor_2310101011.pdf','submissions/mentor_eval_2310101011.pdf','application/pdf',125000,'2026-10-20 02:08:07','2026-10-30 02:08:07','2026-10-30 02:08:07',NULL,NULL),(17,101,19,'Laporan_KP_2310101011.pdf','submissions/report_2310101011.pdf','application/pdf',840000,'2026-10-20 02:08:07','2026-10-30 02:08:07','2026-10-30 02:08:07',NULL,NULL),(18,103,18,'Eben2-FR-BM-12 Formulir Pendaftaran Kegiatan MBKM untuk mahasiswa pradita.docx.pdf','submissions/8/HIpbraEXO7joN2mfE15lXAPd2HNLh2ROIvwO6AFI.pdf','application/pdf',386754,'2026-10-30 02:26:09','2026-10-30 02:26:09','2026-10-30 02:26:09',NULL,NULL),(19,103,19,'Bryan-FR-BM-12 Formulir Pendaftaran Kegiatan MBKM untuk mahasiswa pradita.pdf','submissions/8/mn8GsIvihDYTD8LvxreFHYm0jmUEOt5zRfAzohe0.pdf','application/pdf',558633,'2026-10-30 02:26:09','2026-10-30 02:26:09','2026-10-30 02:26:09',NULL,NULL);
/*!40000 ALTER TABLE `submission_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submissions`
--

DROP TABLE IF EXISTS `submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint(20) unsigned NOT NULL,
  `nim` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`answers`)),
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'submitted',
  `review_note` text DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `field_review_statuses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`field_review_statuses`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `submissions_form_id_nim_unique` (`form_id`,`nim`),
  KEY `submissions_reviewed_by_foreign` (`reviewed_by`),
  KEY `submissions_status_index` (`status`),
  CONSTRAINT `submissions_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `submissions_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submissions`
--

LOCK TABLES `submissions` WRITE;
/*!40000 ALTER TABLE `submissions` DISABLE KEYS */;
INSERT INTO `submissions` VALUES (1,1,'2023012345','Januponsa Dio Firizqi','januponsa.dio@student.pradita.ac.id','{\"1\":\"dn5gwa45\",\"2\":\"drtyr7\",\"3\":\"Sistem Informasi\",\"4\":\"https:\\/\\/drive.google.com\\/file\\/d\\/1bvllAqQCViPH19ls-OrPS4w2ukf8vFdk\\/view\"}','2026-10-01 07:53:56','2026-10-01 00:52:39','2026-10-01 00:53:56','approved',NULL,1,'2026-10-01 00:53:56',NULL),(14,1,'2310101008','KENNETH WILLIAM GUNAWAN','kenneth.william@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101008\"}','2026-10-03 06:40:07','2026-10-04 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(15,1,'2310101010','ANDREW JATZEN LAY','andrew.jatzen@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101010\"}','2026-10-03 06:40:07','2026-09-28 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(16,1,'2310101011','BRYAN ADIPUTRA ONGGO','bryan.adiputra@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101011\"}','2026-10-07 06:40:07','2026-09-29 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(17,1,'2310101012','JONATHAN AXEL TEDY ASIHTO','jonathan.axel@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101012\"}','2026-09-26 06:40:07','2026-09-24 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(18,1,'2310101015','MUHAMMAD WAHYU HIDAYAT','muhammad.wahyu@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101015\"}','2026-10-10 06:40:07','2026-10-04 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(19,1,'2310101016','NAUFAL FADHILLA','naufal.fadhilla@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101016\"}','2026-10-02 06:40:07','2026-09-29 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(20,1,'2310101017','ALEJANDRO RAFAEL','alejandro.rafael@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101017\"}','2026-09-27 06:40:07','2026-09-27 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(21,1,'2310101020','WILLSON AUSTIN','willson.austin@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101020\"}','2026-10-12 06:40:07','2026-09-27 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(22,1,'2310101021','AFFAN MAULANA','affan.maulana@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101021\"}','2026-10-07 06:40:07','2026-10-08 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(23,1,'2310101023','ARIYA PANNA','ariya.panna@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101023\"}','2026-10-03 06:40:07','2026-10-04 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(24,1,'2310101024','ASTRIA FEBRIAN ANGGRAINI','astria.febrian@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101024\"}','2026-10-14 06:40:07','2026-10-11 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(25,1,'2310101025','NADYA RUDIE SUCIPTO','nadya.rudie@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101025\"}','2026-10-08 06:40:07','2026-10-04 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(26,1,'2310101026','DEVIN TAN','devin.tan@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101026\"}','2026-10-03 06:40:07','2026-10-05 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(27,1,'2310101027','FITRAH FEBRIAN LISTANTO','fitrah.febrian@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101027\"}','2026-09-28 06:40:07','2026-09-23 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(28,1,'2310101028','BOY DEVIANO','boy.deviano@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101028\"}','2026-10-09 06:40:07','2026-10-03 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(29,1,'2310101031','RIVANDI ZEIN SAPUTRA','rivandi.zein@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101031\"}','2026-10-01 06:40:07','2026-09-28 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(30,1,'2310101032','KENNETH LODEWIJK IBRAHIM','kenneth.lodewijk@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101032\"}','2026-10-07 06:40:07','2026-10-12 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(31,1,'2310101035','ERDINE A. TIMOTHY','erdine.a.@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101035\"}','2026-10-01 06:40:07','2026-10-05 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(32,1,'2310101036','MUHAMMAD ZIKRY ANDRIAN ARDIANSYAH','muhammad.zikry@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101036\"}','2026-10-06 06:40:07','2026-10-12 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(33,1,'2310101033','HANS HANDRI TANUJAYA','hans.handri@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101033\"}','2026-10-09 06:40:07','2026-09-26 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(34,1,'2410101001','WESLEY MAXIMILLIAN LAY','wesley.maximillian@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-05 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(35,1,'2410101002','JOVAN VITO VIANI','jovan.vito@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-09 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(36,1,'2410101003','RHIFQI SYAHPUTRA PRIYAMBODO','rhifqi.syahputra@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-13 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(37,1,'2410101004','BRYAN CHANDRA','bryan.chandra@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-10 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(38,1,'2410101005','EKAHADI LUTHFI TEDJA','ekahadi.luthfi@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-08 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(39,3,'2310101008','KENNETH WILLIAM GUNAWAN','kenneth.william@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101008\"}','2026-10-07 06:40:07','2026-09-25 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(40,3,'2310101010','ANDREW JATZEN LAY','andrew.jatzen@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101010\"}','2026-10-08 06:40:07','2026-09-27 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(41,3,'2310101011','BRYAN ADIPUTRA ONGGO','bryan.adiputra@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101011\"}','2026-10-10 06:40:07','2026-10-13 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(42,3,'2310101012','JONATHAN AXEL TEDY ASIHTO','jonathan.axel@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101012\"}','2026-10-01 06:40:07','2026-09-24 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(43,3,'2310101015','MUHAMMAD WAHYU HIDAYAT','muhammad.wahyu@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101015\"}','2026-10-13 06:40:07','2026-09-25 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(44,3,'2310101016','NAUFAL FADHILLA','naufal.fadhilla@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101016\"}','2026-09-25 06:40:07','2026-10-13 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(45,3,'2310101017','ALEJANDRO RAFAEL','alejandro.rafael@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101017\"}','2026-10-12 06:40:07','2026-10-07 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(46,3,'2310101020','WILLSON AUSTIN','willson.austin@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101020\"}','2026-10-09 06:40:07','2026-09-25 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(47,3,'2310101021','AFFAN MAULANA','affan.maulana@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101021\"}','2026-10-13 06:40:07','2026-09-24 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(48,3,'2310101023','ARIYA PANNA','ariya.panna@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101023\"}','2026-10-13 06:40:07','2026-09-25 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(49,3,'2310101024','ASTRIA FEBRIAN ANGGRAINI','astria.febrian@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101024\"}','2026-10-06 06:40:07','2026-10-01 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(50,3,'2310101025','NADYA RUDIE SUCIPTO','nadya.rudie@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101025\"}','2026-10-14 06:40:07','2026-10-05 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(51,3,'2310101026','DEVIN TAN','devin.tan@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101026\"}','2026-10-02 06:40:07','2026-10-05 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(52,3,'2310101027','FITRAH FEBRIAN LISTANTO','fitrah.febrian@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101027\"}','2026-10-02 06:40:07','2026-10-12 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(53,3,'2310101028','BOY DEVIANO','boy.deviano@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101028\"}','2026-10-08 06:40:07','2026-10-08 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(54,3,'2310101031','RIVANDI ZEIN SAPUTRA','rivandi.zein@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-14 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(55,3,'2310101032','KENNETH LODEWIJK IBRAHIM','kenneth.lodewijk@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-13 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(56,3,'2310101035','ERDINE A. TIMOTHY','erdine.a.@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-12 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(57,3,'2310101036','MUHAMMAD ZIKRY ANDRIAN ARDIANSYAH','muhammad.zikry@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-11 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(58,4,'2310101008','KENNETH WILLIAM GUNAWAN','kenneth.william@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101008\"}','2026-10-10 06:40:07','2026-10-13 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(59,4,'2310101010','ANDREW JATZEN LAY','andrew.jatzen@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101010\"}','2026-10-04 06:40:07','2026-10-10 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(60,4,'2310101011','BRYAN ADIPUTRA ONGGO','bryan.adiputra@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101011\"}','2026-10-14 06:40:07','2026-09-24 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(61,4,'2310101012','JONATHAN AXEL TEDY ASIHTO','jonathan.axel@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101012\"}','2026-10-04 06:40:07','2026-09-29 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(62,4,'2310101015','MUHAMMAD WAHYU HIDAYAT','muhammad.wahyu@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101015\"}','2026-09-28 06:40:07','2026-10-11 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(63,4,'2310101016','NAUFAL FADHILLA','naufal.fadhilla@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101016\"}','2026-10-14 06:40:07','2026-10-02 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(64,4,'2310101017','ALEJANDRO RAFAEL','alejandro.rafael@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101017\"}','2026-10-08 06:40:07','2026-09-23 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(65,4,'2310101020','WILLSON AUSTIN','willson.austin@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101020\"}','2026-10-13 06:40:07','2026-10-05 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(66,4,'2310101021','AFFAN MAULANA','affan.maulana@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101021\"}','2026-10-14 06:40:07','2026-10-09 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(67,4,'2310101023','ARIYA PANNA','ariya.panna@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101023\"}','2026-10-05 06:40:07','2026-10-06 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(68,4,'2310101024','ASTRIA FEBRIAN ANGGRAINI','astria.febrian@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101024\"}','2026-09-30 06:40:07','2026-09-24 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(69,4,'2310101025','NADYA RUDIE SUCIPTO','nadya.rudie@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-05 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(70,4,'2310101026','DEVIN TAN','devin.tan@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-11 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(71,4,'2310101027','FITRAH FEBRIAN LISTANTO','fitrah.febrian@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-14 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(72,5,'2310101008','KENNETH WILLIAM GUNAWAN','kenneth.william@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101008\"}','2026-10-13 06:40:07','2026-10-08 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(73,5,'2310101010','ANDREW JATZEN LAY','andrew.jatzen@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101010\"}','2026-09-25 06:40:07','2026-10-11 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(74,5,'2310101011','BRYAN ADIPUTRA ONGGO','bryan.adiputra@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101011\"}','2026-09-30 06:40:07','2026-10-04 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(75,5,'2310101012','JONATHAN AXEL TEDY ASIHTO','jonathan.axel@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101012\"}','2026-10-11 06:40:07','2026-10-06 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(76,5,'2310101015','MUHAMMAD WAHYU HIDAYAT','muhammad.wahyu@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101015\"}','2026-10-04 06:40:07','2026-09-29 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(77,5,'2310101016','NAUFAL FADHILLA','naufal.fadhilla@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101016\"}','2026-09-30 06:40:07','2026-09-29 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(78,5,'2310101017','ALEJANDRO RAFAEL','alejandro.rafael@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Sistem Informasi\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101017\"}','2026-10-10 06:40:07','2026-09-27 06:40:07','2026-10-15 06:40:07','approved',NULL,NULL,NULL,NULL),(79,5,'2310101020','WILLSON AUSTIN','willson.austin@student.pradita.ac.id','{\"judul\":\"Riset Akademik Mandiri Bidang Informatika\",\"link\":\"https:\\/\\/drive.google.com\\/sample-2310101020\"}','2026-10-12 06:40:07','2026-10-02 06:40:07','2026-10-15 06:40:07','submitted',NULL,NULL,NULL,NULL),(80,5,'2310101021','AFFAN MAULANA','affan.maulana@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-10 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(81,5,'2310101023','ARIYA PANNA','ariya.panna@student.pradita.ac.id','{\"catatan\":\"Draft pengisian awal\"}','2026-10-15 13:40:07','2026-10-14 06:40:07','2026-10-15 06:40:07','draft',NULL,NULL,NULL,NULL),(82,1,'2310101001','MUHAMMAD SULTHAN ARIF RIZKY PRASETYO','muhammad.sulthan@student.pradita.ac.id','{\"judul\":\"Sistem Rekomendasi Karir Menggunakan AI\",\"catatan\":\"Draft belum selesai\"}','2026-10-15 13:40:06','2026-10-03 06:40:06','2026-10-10 06:40:06','draft',NULL,NULL,NULL,NULL),(83,3,'2310101001','MUHAMMAD SULTHAN ARIF RIZKY PRASETYO','muhammad.sulthan@student.pradita.ac.id','{\"tempat_magang\":\"PT Gojek Indonesia\",\"posisi\":\"Software Engineer Intern\"}','2026-10-07 06:40:06','2026-10-06 06:40:06','2026-10-15 06:40:06','rejected','Surat penerimaan magang belum ada stempel basah perusahaan.',1,'2026-10-09 06:40:06',NULL),(84,4,'2310101001','MUHAMMAD SULTHAN ARIF RIZKY PRASETYO','muhammad.sulthan@student.pradita.ac.id','{\"judul_proposal\":\"Pengembangan Sistem Rekomendasi Karir AI\"}','2026-10-11 06:40:06','2026-10-10 06:40:06','2026-10-15 06:40:06','rejected','Bab 1 dan Bab 2 belum disetujui oleh Dosen Pembimbing Utama.',1,'2026-10-13 06:40:06',NULL),(85,1,'2310101002','DASON TIOVINO','dason.tiovino@student.pradita.ac.id','{\"judul\":\"Analisis Sentimen Twitter dengan BERT\"}','2026-10-15 13:40:06','2026-09-30 06:40:06','2026-10-15 06:40:06','draft',NULL,NULL,NULL,NULL),(86,3,'2310101002','DASON TIOVINO','dason.tiovino@student.pradita.ac.id','{\"instansi\":\"PT Teknologi Digital Nusantara\"}','2026-10-15 13:40:06','2026-10-05 06:40:06','2026-10-15 06:40:06','draft',NULL,NULL,NULL,NULL),(87,4,'2310101002','DASON TIOVINO','dason.tiovino@student.pradita.ac.id','{\"judul_proposal\":\"Implementasi NLP untuk Analisis Sentimen\"}','2026-10-10 06:40:06','2026-10-09 06:40:06','2026-10-15 06:40:06','rejected','Transkrip nilai tidak memenuhi syarat minimum SKS lulus.',1,'2026-10-12 06:40:06',NULL),(88,1,'2310101003','MICKHAEL NIKLAS','mickhael.niklas@student.pradita.ac.id','{\"catatan\":\"Form masih dalam proses pengisian mahasiswa\"}','2026-10-15 13:40:06','2026-10-07 06:40:06','2026-10-15 06:40:06','draft',NULL,NULL,NULL,NULL),(89,3,'2310101003','MICKHAEL NIKLAS','mickhael.niklas@student.pradita.ac.id','{\"catatan\":\"Form masih dalam proses pengisian mahasiswa\"}','2026-10-15 13:40:06','2026-10-07 06:40:06','2026-10-15 06:40:06','draft',NULL,NULL,NULL,NULL),(90,4,'2310101003','MICKHAEL NIKLAS','mickhael.niklas@student.pradita.ac.id','{\"catatan\":\"Form masih dalam proses pengisian mahasiswa\"}','2026-10-15 13:40:06','2026-10-07 06:40:06','2026-10-15 06:40:06','draft',NULL,NULL,NULL,NULL),(91,1,'2310101005','BRIAN ABRAHAM JORDAN SUNTPIET','brian.abraham@student.pradita.ac.id','{\"judul\":\"Aplikasi Monitoring Kehadiran IoT\"}','2026-10-06 06:40:07','2026-10-05 06:40:07','2026-10-15 06:40:07','rejected','Topik penelitian overlap dengan judul angkatan sebelumnya.',1,'2026-10-08 06:40:07',NULL),(92,5,'2310101005','BRIAN ABRAHAM JORDAN SUNTPIET','brian.abraham@student.pradita.ac.id','{\"judul_skripsi\":\"Aplikasi Monitoring Kehadiran IoT\"}','2026-10-12 06:40:07','2026-10-11 06:40:07','2026-10-15 06:40:07','rejected','Bukti sertifikasi keahlian belum diunggah.',1,'2026-10-14 06:40:07',NULL),(93,3,'2023012345','Januponsa Dio Firizqi','januponsa.dio@student.pradita.ac.id','[]','2026-10-30 09:03:09','2026-10-20 02:42:01','2026-10-30 02:03:09','approved',NULL,NULL,NULL,NULL),(94,8,'2310101001','MUHAMMAD SULTHAN ARIF RIZKY PRASETYO','muhammad.sulthan@student.pradita.ac.id','{\"INTDEF_report_title\":\"Pengembangan Modul Otentikasi dan RBAC Menggunakan Laravel Sanctum pada PT Telekomunikasi Indonesia\",\"INTDEF_company_name\":\"PT Telekomunikasi Indonesia Tbk\",\"INTDEF_company_address\":\"Gedung Telkom Landmark Tower Lt. 38, Jl. Jend. Gatot Subroto, Jakarta Selatan\",\"INTDEF_mentor_name\":\"Ahmad Fauzi, S.T.\",\"INTDEF_mentor_position\":\"Lead Backend Engineer\",\"INTDEF_mentor_nip\":\"TLK-90812\",\"INTDEF_internship_period\":\"1 Februari 2026 - 30 April 2026\"}','2026-10-20 02:07:59','2026-10-30 02:07:35','2026-10-30 02:07:59','submitted',NULL,NULL,NULL,NULL),(95,8,'2310101002','DASON TIOVINO','dason.tiovino@student.pradita.ac.id','{\"INTDEF_report_title\":\"Implementasi Pipeline CI\\/CD Berbasis GitHub Actions pada Core Banking PT Bank Central Asia Tbk\",\"INTDEF_company_name\":\"PT Bank Central Asia Tbk\",\"INTDEF_company_address\":\"Menara BCA Lt. 22, Grand Indonesia, Jl. M.H. Thamrin No. 1, Jakarta Pusat\",\"INTDEF_mentor_name\":\"Rendra Kusuma, M.Kom.\",\"INTDEF_mentor_position\":\"DevOps Specialist Lead\",\"INTDEF_mentor_nip\":\"BCA-88712\",\"INTDEF_internship_period\":\"15 Januari 2026 - 15 April 2026\"}','2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59','approved',NULL,NULL,NULL,NULL),(96,8,'2310101003','MICKHAEL NIKLAS','mickhael.niklas@student.pradita.ac.id','{\"INTDEF_report_title\":\"Analisis Sentimen Ulasan Pengguna Aplikasi E-Commerce Menggunakan IndoBERT pada PT Tokopedia\",\"INTDEF_company_name\":\"PT Tokopedia\",\"INTDEF_company_address\":\"Tokopedia Tower Ciputra World 2, Jl. Prof. DR. Satrio, Jakarta Selatan\",\"INTDEF_mentor_name\":\"Kevin Wijaya, S.Kom.\",\"INTDEF_mentor_position\":\"Senior Data Scientist\",\"INTDEF_mentor_nip\":\"TKP-77210\",\"INTDEF_internship_period\":\"5 Januari 2026 - 5 April 2026\"}','2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59','approved',NULL,NULL,NULL,NULL),(97,8,'2310101005','BRIAN ABRAHAM JORDAN SUNTPIET','brian.abraham@student.pradita.ac.id','{\"INTDEF_report_title\":\"Perancangan UI\\/UX Dashboard Transaksi FinTech Menggunakan Design Thinking pada PT DANA Indonesia\",\"INTDEF_company_name\":\"PT Espay Debit Indonesia Koe (DANA)\",\"INTDEF_company_address\":\"Capital Place Office Tower Lt. 18, Jl. Jend. Gatot Subroto Kav. 18, Jakarta Selatan\",\"INTDEF_mentor_name\":\"Jessica Tan, S.Ds.\",\"INTDEF_mentor_position\":\"Product Design Lead\",\"INTDEF_mentor_nip\":\"DNA-54129\",\"INTDEF_internship_period\":\"10 Januari 2026 - 10 April 2026\"}','2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59','approved',NULL,NULL,NULL,NULL),(98,8,'2310101007','FARELL ALVARO THERIONO','farell.alvaro@student.pradita.ac.id','{\"INTDEF_report_title\":\"Otomasi Pengujian End-to-End Menggunakan Playwright pada Platform PT Traveloka Indonesia\",\"INTDEF_company_name\":\"PT Traveloka Indonesia\",\"INTDEF_company_address\":\"Traveloka Campus BSD Green Office Park, BSD City, Tangerang\",\"INTDEF_mentor_name\":\"Dimas Aditya, S.T.\",\"INTDEF_mentor_position\":\"QA Automation Lead\",\"INTDEF_mentor_nip\":\"TVL-33910\",\"INTDEF_internship_period\":\"12 Januari 2026 - 12 April 2026\"}','2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59','approved',NULL,NULL,NULL,NULL),(99,8,'2310101008','KENNETH WILLIAM GUNAWAN','kenneth.william@student.pradita.ac.id','{\"INTDEF_report_title\":\"Migrasi Arsitektur Monolith ke Microservices Berbasis Docker Kubernetes pada PT GoTo Tokopedia\",\"INTDEF_company_name\":\"PT GoTo Gojek Tokopedia Tbk\",\"INTDEF_company_address\":\"Pasaraya Blok M Gedung B Lt. 6, Jl. Iskandarsyah II No. 2, Jakarta Selatan\",\"INTDEF_mentor_name\":\"Bayu Wicaksono, M.T.\",\"INTDEF_mentor_position\":\"Principal Systems Architect\",\"INTDEF_mentor_nip\":\"GTO-10293\",\"INTDEF_internship_period\":\"1 Januari 2026 - 1 April 2026\"}','2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59','approved',NULL,NULL,NULL,NULL),(100,8,'2310101010','ANDREW JATZEN LAY','andrew.jatzen@student.pradita.ac.id','{\"INTDEF_report_title\":\"Implementasi Sistem Deteksi Intrusi Jaringan Menggunakan Snort dan ELK Stack pada PT Astra International\",\"INTDEF_company_name\":\"PT Astra International Tbk\",\"INTDEF_company_address\":\"Menara Astra Lt. 15, Jl. Jend. Sudirman Kav. 5-6, Jakarta Pusat\",\"INTDEF_mentor_name\":\"Ir. Hendro Subroto\",\"INTDEF_mentor_position\":\"Head of IT Infrastructure\",\"INTDEF_mentor_nip\":\"AST-89012\",\"INTDEF_internship_period\":\"15 Januari 2026 - 15 April 2026\"}','2026-10-20 02:07:59','2026-10-30 02:07:59','2026-10-30 02:07:59','approved',NULL,NULL,NULL,NULL),(101,8,'2310101011','BRYAN ADIPUTRA ONGGO','bryan.adiputra@student.pradita.ac.id','{\"INTDEF_report_title\":\"Rancang Bangun Sistem Antrean Digital Rumah Sakit Berbasis WebSocket pada Siloam Hospitals Group\",\"INTDEF_company_name\":\"PT Siloam International Hospitals Tbk\",\"INTDEF_company_address\":\"Gedung Fakultas Kedokteran UPH, Jl. Boulevard Jend. Sudirman, Lippo Karawaci, Tangerang\",\"INTDEF_mentor_name\":\"Drg. Maria Stephanie, M.M.S.I.\",\"INTDEF_mentor_position\":\"Head of Hospital Information System\",\"INTDEF_mentor_nip\":\"SLM-66231\",\"INTDEF_internship_period\":\"1 Februari 2026 - 30 April 2026\"}','2026-10-20 02:08:07','2026-10-30 02:08:07','2026-10-30 02:08:07','approved',NULL,NULL,NULL,NULL),(102,7,'2023012345','Januponsa Dio Firizqi','januponsa.dio@student.pradita.ac.id','[]','2026-10-30 02:12:16','2026-10-30 02:12:16','2026-10-30 02:12:16','submitted',NULL,NULL,NULL,NULL),(103,8,'2023012345','Januponsa Dio Firizqi','januponsa.dio@student.pradita.ac.id','{\"11\":\"Pengembangan Sistem Informasi Manajamen Penyimpanan Can Ngopi\",\"12\":\"Can Ngopi\",\"13\":\"Jl. Boulevard Raya No.88, Gading Serpong, Tangerang\",\"14\":\"Juan\",\"15\":\"Manajer\",\"16\":\"-\",\"17\":\"Mei 2026-Desember 2026\"}','2026-10-30 09:34:12','2026-10-30 02:26:09','2026-10-30 02:34:12','approved',NULL,3,'2026-10-30 02:34:12',NULL);
/*!40000 ALTER TABLE `submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `normalized_email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'admin',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status_akun` varchar(255) NOT NULL DEFAULT 'Login Diizinkan',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_normalized_email_unique` (`normalized_email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin','admin@pradita.ac.id',NULL,NULL,'$2y$12$uCofDOzzF.2LgRIRSUTj5eNLLq81c8.0DQl4WYOnr6rsKg0B0vpOC',NULL,NULL,'superadmin',1,0,'2026-10-30 01:59:10','127.0.0.1',NULL,'2026-09-04 01:01:57','2026-10-30 01:59:10',NULL,'Login Diizinkan'),(3,'admin prodi','adminprodi','admin@fortapradita.ac.id',NULL,NULL,'$2y$12$2rOjqa8CK8qnTQRMV7tZGOsYOI2P2E/bb8UcazcbQafnl7g83gAKy',NULL,NULL,'admin_forta',1,0,'2026-10-30 01:59:45','127.0.0.1',NULL,'2026-09-18 00:08:01','2026-10-30 01:59:45',NULL,'Login Diizinkan'),(4,'Januponsa Dio Firizqi, S.Kom., M.Kom.','sekprodi.if','januponsa.dio@pradita.ac.id','januponsa.dio@pradita.ac.id',NULL,'$2y$12$RKbtJuFYM.LU4z/vepYufOqvo4D6GiSui3x5TRsQqXtEM8Yu3yUp6',NULL,NULL,'admin_forta',1,0,'2026-10-30 02:37:31','127.0.0.1',NULL,'2026-10-15 06:27:15','2026-10-30 05:33:23',NULL,'Login Diizinkan'),(5,'Alfa Ryano Yohannis, S.T., M.T., Ph.D.','alfa.ryano','alfa.ryano@pradita.ac.id','alfa.ryano@pradita.ac.id',NULL,'$2y$12$l5deI6/xnOJvUELcll/0MeoidTOZW9bm/Ug/FdfBcH6hafJB5C.O.',NULL,NULL,'dosen',1,0,'2026-10-30 05:25:14','127.0.0.1',NULL,'2026-10-30 02:52:52','2026-10-30 05:33:23',NULL,'Login Diizinkan'),(6,'Arya Sanjaya, S.T., M.Kom.','arya.sanjaya','arya.sanjaya@pradita.ac.id','arya.sanjaya@pradita.ac.id',NULL,'$2y$12$HwPqOvEr4yrqkL28RDmClucwudjRCDUHrFt/vGPjjtJoi72XqImjy',NULL,NULL,'dosen',1,0,NULL,NULL,NULL,'2026-10-30 02:52:52','2026-10-30 05:33:23',NULL,'Login Diizinkan'),(7,'Erick Dazki, S.Kom., M.Kom.','erick.dazki','erick.dazki@pradita.ac.id','erick.dazki@pradita.ac.id',NULL,'$2y$12$fdCQSfklnexv50wp9VZTdOKv5OOjg/fZc7eopE49EUqRoUqfm9lP6',NULL,NULL,'dosen',1,0,'2026-10-30 05:27:55','127.0.0.1',NULL,'2026-10-30 02:52:52','2026-10-30 05:33:23',NULL,'Login Diizinkan'),(8,'Master Edison Siregar, S.T., M.Kom.','master.edison','master.edison@pradita.ac.id','master.edison@pradita.ac.id',NULL,'$2y$12$CLNjjTBKnmQ6fBbcP74pB.TEj.untL2qajjddWfq8pHo2yoFgQzLa',NULL,NULL,'dosen',1,0,'2026-10-30 05:27:42','127.0.0.1',NULL,'2026-10-30 02:52:53','2026-10-30 05:33:24',NULL,'Login Diizinkan'),(9,'Refgiufi Patria Avrianto, S.Kom., M.Kom.','refgiufi.patria','refgiufi.patria@pradita.ac.id','refgiufi.patria@pradita.ac.id',NULL,'$2y$12$3Eqhduevd/tvFg08AUnnqubCDKOxtHaHTxjqkPj.tNV1VkVUl3E5e',NULL,NULL,'dosen',1,0,NULL,NULL,NULL,'2026-10-30 02:52:53','2026-10-30 05:33:24',NULL,'Login Diizinkan');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-30 19:33:40
