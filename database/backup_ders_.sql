-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: ders_
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ai_providers`
--

DROP TABLE IF EXISTS `ai_providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_providers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_providers_identifier_unique` (`identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_providers`
--

LOCK TABLES `ai_providers` WRITE;
/*!40000 ALTER TABLE `ai_providers` DISABLE KEYS */;
INSERT INTO `ai_providers` VALUES (1,'OpenAI GPT-4o','openai','YOUR_OPENAI_API_KEY_HERE',1,NULL,'2026-03-01 11:08:06'),(2,'Google Gemini 1.5 Pro','gemini','YOUR_GEMINI_API_KEY_HERE',0,NULL,'2026-03-01 11:08:06');
/*!40000 ALTER TABLE `ai_providers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
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
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `generated_tests`
--

DROP TABLE IF EXISTS `generated_tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `generated_tests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `questions_data` json NOT NULL,
  `outcome_ids` json DEFAULT NULL,
  `user_answers` json DEFAULT NULL,
  `score` int DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `generated_tests_user_id_foreign` (`user_id`),
  CONSTRAINT `generated_tests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `generated_tests`
--

LOCK TABLES `generated_tests` WRITE;
/*!40000 ALTER TABLE `generated_tests` DISABLE KEYS */;
/*!40000 ALTER TABLE `generated_tests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_03_01_000001_add_fields_to_users_table',1),(5,'2026_03_01_000002_create_subjects_table',1),(6,'2026_03_01_000003_create_outcomes_table',1),(7,'2026_03_01_000004_create_questions_table',1),(8,'2026_03_01_000005_create_generated_tests_table',1),(9,'2026_03_01_095016_create_personal_access_tokens_table',2),(10,'2026_03_01_130000_add_correct_answer_to_questions_table',3),(11,'2026_03_01_163200_create_ai_providers_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outcomes`
--

DROP TABLE IF EXISTS `outcomes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outcomes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `grade_level` tinyint NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outcomes_subject_id_foreign` (`subject_id`),
  CONSTRAINT `outcomes_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outcomes`
--

LOCK TABLES `outcomes` WRITE;
/*!40000 ALTER TABLE `outcomes` DISABLE KEYS */;
INSERT INTO `outcomes` VALUES (1,1,'Doğal Sayılar',NULL,5,'MAT.5.af2d','2026-03-01 06:34:49','2026-03-01 06:34:49'),(2,1,'Doğal Sayılar',NULL,6,'MAT.6.9c32','2026-03-01 06:34:49','2026-03-01 06:34:49'),(3,1,'Tam Sayılar',NULL,6,'MAT.6.91da','2026-03-01 06:34:49','2026-03-01 06:34:49'),(4,1,'Tam Sayılar',NULL,7,'MAT.7.5673','2026-03-01 06:34:49','2026-03-01 06:34:49'),(5,1,'Rasyonel Sayılar',NULL,7,'MAT.7.da8e','2026-03-01 06:34:49','2026-03-01 06:34:49'),(6,1,'Rasyonel Sayılar',NULL,8,'MAT.8.bdea','2026-03-01 06:34:49','2026-03-01 06:34:49'),(7,1,'Çarpanlar ve Katlar',NULL,5,'MAT.5.5e13','2026-03-01 06:34:49','2026-03-01 06:34:49'),(8,1,'Çarpanlar ve Katlar',NULL,6,'MAT.6.6f9d','2026-03-01 06:34:49','2026-03-01 06:34:49'),(9,1,'Kesirler',NULL,5,'MAT.5.17fe','2026-03-01 06:34:49','2026-03-01 06:34:49'),(10,1,'Kesirler',NULL,6,'MAT.6.f46c','2026-03-01 06:34:49','2026-03-01 06:34:49'),(11,1,'Kesirler',NULL,7,'MAT.7.5451','2026-03-01 06:34:49','2026-03-01 06:34:49'),(12,1,'Ondalık Gösterim',NULL,5,'MAT.5.850d','2026-03-01 06:34:49','2026-03-01 06:34:49'),(13,1,'Ondalık Gösterim',NULL,6,'MAT.6.5048','2026-03-01 06:34:49','2026-03-01 06:34:49'),(14,1,'Yüzde Hesaplamaları',NULL,6,'MAT.6.e44d','2026-03-01 06:34:49','2026-03-01 06:34:49'),(15,1,'Yüzde Hesaplamaları',NULL,7,'MAT.7.e119','2026-03-01 06:34:49','2026-03-01 06:34:49'),(16,1,'Oran ve Orantı',NULL,6,'MAT.6.9ec3','2026-03-01 06:34:49','2026-03-01 06:34:49'),(17,1,'Oran ve Orantı',NULL,7,'MAT.7.202d','2026-03-01 06:34:49','2026-03-01 06:34:49'),(18,1,'Cebirsel İfadeler',NULL,6,'MAT.6.b21b','2026-03-01 06:34:49','2026-03-01 06:34:49'),(19,1,'Cebirsel İfadeler',NULL,7,'MAT.7.ab9b','2026-03-01 06:34:49','2026-03-01 06:34:49'),(20,1,'Cebirsel İfadeler',NULL,8,'MAT.8.fe82','2026-03-01 06:34:49','2026-03-01 06:34:49'),(21,1,'Denklem Çözme',NULL,7,'MAT.7.4a60','2026-03-01 06:34:49','2026-03-01 06:34:49'),(22,1,'Denklem Çözme',NULL,8,'MAT.8.a239','2026-03-01 06:34:49','2026-03-01 06:34:49'),(23,1,'Eşitsizlikler',NULL,8,'MAT.8.b981','2026-03-01 06:34:49','2026-03-01 06:34:49'),(24,1,'Üslü İfadeler',NULL,8,'MAT.8.2d27','2026-03-01 06:34:49','2026-03-01 06:34:49'),(25,1,'Kareköklü İfadeler',NULL,8,'MAT.8.2775','2026-03-01 06:34:49','2026-03-01 06:34:49'),(26,1,'Üçgenler',NULL,5,'MAT.5.825e','2026-03-01 06:34:49','2026-03-01 06:34:49'),(27,1,'Üçgenler',NULL,6,'MAT.6.3726','2026-03-01 06:34:49','2026-03-01 06:34:49'),(28,1,'Üçgenler',NULL,7,'MAT.7.b722','2026-03-01 06:34:49','2026-03-01 06:34:49'),(29,1,'Üçgenler',NULL,8,'MAT.8.87a6','2026-03-01 06:34:49','2026-03-01 06:34:49'),(30,1,'Dörtgenler',NULL,5,'MAT.5.090d','2026-03-01 06:34:49','2026-03-01 06:34:49'),(31,1,'Dörtgenler',NULL,6,'MAT.6.a56a','2026-03-01 06:34:49','2026-03-01 06:34:49'),(32,1,'Dörtgenler',NULL,7,'MAT.7.85b6','2026-03-01 06:34:49','2026-03-01 06:34:49'),(33,1,'Çember ve Daire',NULL,6,'MAT.6.24a4','2026-03-01 06:34:49','2026-03-01 06:34:49'),(34,1,'Çember ve Daire',NULL,7,'MAT.7.9def','2026-03-01 06:34:49','2026-03-01 06:34:49'),(35,1,'Alan ve Çevre',NULL,5,'MAT.5.dfdf','2026-03-01 06:34:49','2026-03-01 06:34:49'),(36,1,'Alan ve Çevre',NULL,6,'MAT.6.25d1','2026-03-01 06:34:49','2026-03-01 06:34:49'),(37,1,'Alan ve Çevre',NULL,7,'MAT.7.dc66','2026-03-01 06:34:49','2026-03-01 06:34:49'),(38,1,'Hacim Hesaplamaları',NULL,6,'MAT.6.6d75','2026-03-01 06:34:49','2026-03-01 06:34:49'),(39,1,'Hacim Hesaplamaları',NULL,7,'MAT.7.9ed3','2026-03-01 06:34:49','2026-03-01 06:34:49'),(40,1,'Hacim Hesaplamaları',NULL,8,'MAT.8.3e42','2026-03-01 06:34:49','2026-03-01 06:34:49'),(41,1,'Koordinat Sistemi',NULL,7,'MAT.7.5143','2026-03-01 06:34:49','2026-03-01 06:34:49'),(42,1,'Koordinat Sistemi',NULL,8,'MAT.8.bbf4','2026-03-01 06:34:49','2026-03-01 06:34:49'),(43,1,'Olasılık',NULL,8,'MAT.8.4fef','2026-03-01 06:34:49','2026-03-01 06:34:49'),(44,1,'İstatistik',NULL,7,'MAT.7.0cf6','2026-03-01 06:34:49','2026-03-01 06:34:49'),(45,1,'İstatistik',NULL,8,'MAT.8.869e','2026-03-01 06:34:49','2026-03-01 06:34:49'),(46,1,'Örüntü ve İlişki',NULL,5,'MAT.5.87d2','2026-03-01 06:34:49','2026-03-01 06:34:49'),(47,1,'Örüntü ve İlişki',NULL,6,'MAT.6.ac22','2026-03-01 06:34:49','2026-03-01 06:34:49'),(48,2,'Okuduğunu Anlama',NULL,5,'Tü.5.b3d0','2026-03-01 06:34:49','2026-03-01 06:34:49'),(49,2,'Okuduğunu Anlama',NULL,6,'Tü.6.1b23','2026-03-01 06:34:49','2026-03-01 06:34:49'),(50,2,'Okuduğunu Anlama',NULL,7,'Tü.7.b672','2026-03-01 06:34:49','2026-03-01 06:34:49'),(51,2,'Okuduğunu Anlama',NULL,8,'Tü.8.6c52','2026-03-01 06:34:49','2026-03-01 06:34:49'),(52,2,'Sözcükte Anlam',NULL,5,'Tü.5.faa3','2026-03-01 06:34:49','2026-03-01 06:34:49'),(53,2,'Sözcükte Anlam',NULL,6,'Tü.6.fc05','2026-03-01 06:34:49','2026-03-01 06:34:49'),(54,2,'Sözcükte Anlam',NULL,7,'Tü.7.161c','2026-03-01 06:34:49','2026-03-01 06:34:49'),(55,2,'Sözcükte Anlam',NULL,8,'Tü.8.1152','2026-03-01 06:34:49','2026-03-01 06:34:49'),(56,2,'Cümlede Anlam',NULL,5,'Tü.5.9f79','2026-03-01 06:34:49','2026-03-01 06:34:49'),(57,2,'Cümlede Anlam',NULL,6,'Tü.6.904c','2026-03-01 06:34:49','2026-03-01 06:34:49'),(58,2,'Cümlede Anlam',NULL,7,'Tü.7.fa2a','2026-03-01 06:34:49','2026-03-01 06:34:49'),(59,2,'Cümlede Anlam',NULL,8,'Tü.8.c50c','2026-03-01 06:34:49','2026-03-01 06:34:49'),(60,2,'Paragrafta Anlam',NULL,6,'Tü.6.cac9','2026-03-01 06:34:49','2026-03-01 06:34:49'),(61,2,'Paragrafta Anlam',NULL,7,'Tü.7.e8c5','2026-03-01 06:34:49','2026-03-01 06:34:49'),(62,2,'Paragrafta Anlam',NULL,8,'Tü.8.0a84','2026-03-01 06:34:49','2026-03-01 06:34:49'),(63,2,'Yazım Kuralları',NULL,5,'Tü.5.3aa7','2026-03-01 06:34:49','2026-03-01 06:34:49'),(64,2,'Yazım Kuralları',NULL,6,'Tü.6.a333','2026-03-01 06:34:49','2026-03-01 06:34:49'),(65,2,'Yazım Kuralları',NULL,7,'Tü.7.c4d4','2026-03-01 06:34:49','2026-03-01 06:34:49'),(66,2,'Yazım Kuralları',NULL,8,'Tü.8.28bc','2026-03-01 06:34:49','2026-03-01 06:34:49'),(67,2,'Noktalama İşaretleri',NULL,5,'Tü.5.d404','2026-03-01 06:34:49','2026-03-01 06:34:49'),(68,2,'Noktalama İşaretleri',NULL,6,'Tü.6.68c9','2026-03-01 06:34:49','2026-03-01 06:34:49'),(69,2,'Noktalama İşaretleri',NULL,7,'Tü.7.5493','2026-03-01 06:34:49','2026-03-01 06:34:49'),(70,2,'Noktalama İşaretleri',NULL,8,'Tü.8.c7d1','2026-03-01 06:34:49','2026-03-01 06:34:49'),(71,2,'Fiiller',NULL,6,'Tü.6.bcaf','2026-03-01 06:34:49','2026-03-01 06:34:49'),(72,2,'Fiiller',NULL,7,'Tü.7.2bed','2026-03-01 06:34:49','2026-03-01 06:34:49'),(73,2,'Fiiller',NULL,8,'Tü.8.b0d4','2026-03-01 06:34:49','2026-03-01 06:34:49'),(74,2,'İsimler ve Sıfatlar',NULL,5,'Tü.5.5eeb','2026-03-01 06:34:49','2026-03-01 06:34:49'),(75,2,'İsimler ve Sıfatlar',NULL,6,'Tü.6.35b7','2026-03-01 06:34:49','2026-03-01 06:34:49'),(76,2,'Zarflar',NULL,6,'Tü.6.640e','2026-03-01 06:34:49','2026-03-01 06:34:49'),(77,2,'Zarflar',NULL,7,'Tü.7.e943','2026-03-01 06:34:49','2026-03-01 06:34:49'),(78,2,'Cümle Bilgisi',NULL,7,'Tü.7.845d','2026-03-01 06:34:49','2026-03-01 06:34:49'),(79,2,'Cümle Bilgisi',NULL,8,'Tü.8.9d9c','2026-03-01 06:34:49','2026-03-01 06:34:49'),(80,2,'Söz Sanatları',NULL,7,'Tü.7.a792','2026-03-01 06:34:49','2026-03-01 06:34:49'),(81,2,'Söz Sanatları',NULL,8,'Tü.8.8cb1','2026-03-01 06:34:49','2026-03-01 06:34:49'),(82,2,'Metin Türleri',NULL,6,'Tü.6.9972','2026-03-01 06:34:49','2026-03-01 06:34:49'),(83,2,'Metin Türleri',NULL,7,'Tü.7.356b','2026-03-01 06:34:49','2026-03-01 06:34:49'),(84,2,'Metin Türleri',NULL,8,'Tü.8.971d','2026-03-01 06:34:49','2026-03-01 06:34:49'),(85,3,'Simple Present Tense',NULL,5,'İN.5.9a9e','2026-03-01 06:34:49','2026-03-01 06:34:49'),(86,3,'Simple Present Tense',NULL,6,'İN.6.6d28','2026-03-01 06:34:49','2026-03-01 06:34:49'),(87,3,'Present Continuous Tense',NULL,5,'İN.5.9a6e','2026-03-01 06:34:49','2026-03-01 06:34:49'),(88,3,'Present Continuous Tense',NULL,6,'İN.6.8a65','2026-03-01 06:34:49','2026-03-01 06:34:49'),(89,3,'Simple Past Tense',NULL,6,'İN.6.e11d','2026-03-01 06:34:49','2026-03-01 06:34:49'),(90,3,'Simple Past Tense',NULL,7,'İN.7.8850','2026-03-01 06:34:49','2026-03-01 06:34:49'),(91,3,'Future Tense',NULL,7,'İN.7.9d0a','2026-03-01 06:34:49','2026-03-01 06:34:49'),(92,3,'Future Tense',NULL,8,'İN.8.2f58','2026-03-01 06:34:49','2026-03-01 06:34:49'),(93,3,'Comparatives & Superlatives',NULL,7,'İN.7.7b7f','2026-03-01 06:34:49','2026-03-01 06:34:49'),(94,3,'Comparatives & Superlatives',NULL,8,'İN.8.241b','2026-03-01 06:34:49','2026-03-01 06:34:49'),(95,3,'Reading Comprehension',NULL,5,'İN.5.e7d9','2026-03-01 06:34:49','2026-03-01 06:34:49'),(96,3,'Reading Comprehension',NULL,6,'İN.6.4739','2026-03-01 06:34:49','2026-03-01 06:34:49'),(97,3,'Reading Comprehension',NULL,7,'İN.7.737f','2026-03-01 06:34:49','2026-03-01 06:34:49'),(98,3,'Reading Comprehension',NULL,8,'İN.8.770b','2026-03-01 06:34:49','2026-03-01 06:34:49'),(99,3,'Vocabulary',NULL,5,'İN.5.6d0a','2026-03-01 06:34:49','2026-03-01 06:34:49'),(100,3,'Vocabulary',NULL,6,'İN.6.c441','2026-03-01 06:34:49','2026-03-01 06:34:49'),(101,3,'Vocabulary',NULL,7,'İN.7.111c','2026-03-01 06:34:49','2026-03-01 06:34:49'),(102,3,'Vocabulary',NULL,8,'İN.8.572a','2026-03-01 06:34:49','2026-03-01 06:34:49'),(103,3,'Modals',NULL,7,'İN.7.1685','2026-03-01 06:34:49','2026-03-01 06:34:49'),(104,3,'Modals',NULL,8,'İN.8.cfff','2026-03-01 06:34:49','2026-03-01 06:34:49'),(105,3,'Prepositions',NULL,5,'İN.5.0a47','2026-03-01 06:34:49','2026-03-01 06:34:49'),(106,3,'Prepositions',NULL,6,'İN.6.1a5e','2026-03-01 06:34:49','2026-03-01 06:34:49'),(107,3,'Conditionals',NULL,8,'İN.8.a662','2026-03-01 06:34:49','2026-03-01 06:34:49'),(108,4,'Madde ve Değişim',NULL,5,'FEN.5.03f8','2026-03-01 06:34:49','2026-03-01 06:34:49'),(109,4,'Madde ve Değişim',NULL,6,'FEN.6.e598','2026-03-01 06:34:49','2026-03-01 06:34:49'),(110,4,'Madde ve Değişim',NULL,7,'FEN.7.731b','2026-03-01 06:34:49','2026-03-01 06:34:49'),(111,4,'Madde ve Değişim',NULL,8,'FEN.8.2f43','2026-03-01 06:34:49','2026-03-01 06:34:49'),(112,4,'Kuvvet ve Hareket',NULL,5,'FEN.5.072b','2026-03-01 06:34:49','2026-03-01 06:34:49'),(113,4,'Kuvvet ve Hareket',NULL,6,'FEN.6.e3c9','2026-03-01 06:34:49','2026-03-01 06:34:49'),(114,4,'Kuvvet ve Hareket',NULL,7,'FEN.7.ad94','2026-03-01 06:34:49','2026-03-01 06:34:49'),(115,4,'Kuvvet ve Hareket',NULL,8,'FEN.8.9651','2026-03-01 06:34:49','2026-03-01 06:34:49'),(116,4,'Enerji',NULL,6,'FEN.6.cd6d','2026-03-01 06:34:49','2026-03-01 06:34:49'),(117,4,'Enerji',NULL,7,'FEN.7.308d','2026-03-01 06:34:49','2026-03-01 06:34:49'),(118,4,'Enerji',NULL,8,'FEN.8.fbfe','2026-03-01 06:34:49','2026-03-01 06:34:49'),(119,4,'Işık ve Ses',NULL,5,'FEN.5.7fbf','2026-03-01 06:34:49','2026-03-01 06:34:49'),(120,4,'Işık ve Ses',NULL,6,'FEN.6.067d','2026-03-01 06:34:49','2026-03-01 06:34:49'),(121,4,'Işık ve Ses',NULL,7,'FEN.7.8807','2026-03-01 06:34:49','2026-03-01 06:34:49'),(122,4,'Elektrik',NULL,5,'FEN.5.b059','2026-03-01 06:34:49','2026-03-01 06:34:49'),(123,4,'Elektrik',NULL,6,'FEN.6.be3c','2026-03-01 06:34:49','2026-03-01 06:34:49'),(124,4,'Elektrik',NULL,7,'FEN.7.76d2','2026-03-01 06:34:49','2026-03-01 06:34:49'),(125,4,'Elektrik',NULL,8,'FEN.8.4b36','2026-03-01 06:34:49','2026-03-01 06:34:49'),(126,4,'Canlılar Dünyası',NULL,5,'FEN.5.0148','2026-03-01 06:34:49','2026-03-01 06:34:49'),(127,4,'Canlılar Dünyası',NULL,6,'FEN.6.47da','2026-03-01 06:34:49','2026-03-01 06:34:49'),(128,4,'Vücudumuzdaki Sistemler',NULL,6,'FEN.6.450f','2026-03-01 06:34:49','2026-03-01 06:34:49'),(129,4,'Vücudumuzdaki Sistemler',NULL,7,'FEN.7.19a9','2026-03-01 06:34:49','2026-03-01 06:34:49'),(130,4,'Hücre',NULL,7,'FEN.7.03b8','2026-03-01 06:34:49','2026-03-01 06:34:49'),(131,4,'Hücre',NULL,8,'FEN.8.11c7','2026-03-01 06:34:49','2026-03-01 06:34:49'),(132,4,'DNA ve Genetik',NULL,8,'FEN.8.6127','2026-03-01 06:34:49','2026-03-01 06:34:49'),(133,4,'Dünya ve Evren',NULL,5,'FEN.5.7f93','2026-03-01 06:34:49','2026-03-01 06:34:49'),(134,4,'Dünya ve Evren',NULL,6,'FEN.6.2c13','2026-03-01 06:34:49','2026-03-01 06:34:49'),(135,4,'Dünya ve Evren',NULL,7,'FEN.7.cf6a','2026-03-01 06:34:49','2026-03-01 06:34:49'),(136,4,'Basınç',NULL,8,'FEN.8.7d54','2026-03-01 06:34:49','2026-03-01 06:34:49'),(137,4,'Asit ve Bazlar',NULL,8,'FEN.8.f859','2026-03-01 06:34:49','2026-03-01 06:34:49'),(138,5,'Türk Tarihi',NULL,5,'SOS.5.6ff5','2026-03-01 06:34:49','2026-03-01 06:34:49'),(139,5,'Türk Tarihi',NULL,6,'SOS.6.2953','2026-03-01 06:34:49','2026-03-01 06:34:49'),(140,5,'Türk Tarihi',NULL,7,'SOS.7.4f4c','2026-03-01 06:34:49','2026-03-01 06:34:49'),(141,5,'İnkılap Tarihi',NULL,8,'SOS.8.a97c','2026-03-01 06:34:49','2026-03-01 06:34:49'),(142,5,'Coğrafya',NULL,5,'SOS.5.6c37','2026-03-01 06:34:49','2026-03-01 06:34:49'),(143,5,'Coğrafya',NULL,6,'SOS.6.0645','2026-03-01 06:34:49','2026-03-01 06:34:49'),(144,5,'Coğrafya',NULL,7,'SOS.7.1bd8','2026-03-01 06:34:49','2026-03-01 06:34:49'),(145,5,'Vatandaşlık',NULL,5,'SOS.5.053c','2026-03-01 06:34:49','2026-03-01 06:34:49'),(146,5,'Vatandaşlık',NULL,6,'SOS.6.3a61','2026-03-01 06:34:49','2026-03-01 06:34:49'),(147,5,'Vatandaşlık',NULL,7,'SOS.7.8a57','2026-03-01 06:34:49','2026-03-01 06:34:49'),(148,5,'Vatandaşlık',NULL,8,'SOS.8.7f43','2026-03-01 06:34:49','2026-03-01 06:34:49'),(149,5,'Ekonomi',NULL,6,'SOS.6.8d04','2026-03-01 06:34:49','2026-03-01 06:34:49'),(150,5,'Ekonomi',NULL,7,'SOS.7.768c','2026-03-01 06:34:49','2026-03-01 06:34:49'),(151,5,'Kültür ve Miras',NULL,5,'SOS.5.94e1','2026-03-01 06:34:49','2026-03-01 06:34:49'),(152,5,'Kültür ve Miras',NULL,6,'SOS.6.dea3','2026-03-01 06:34:49','2026-03-01 06:34:49'),(153,6,'İnanç',NULL,8,NULL,'2026-03-01 09:30:46','2026-03-01 09:30:46'),(154,6,'İbadet',NULL,8,NULL,'2026-03-01 09:30:46','2026-03-01 09:30:46'),(155,6,'Ahlak ve Değerler',NULL,8,NULL,'2026-03-01 09:30:46','2026-03-01 09:30:46'),(156,6,'Din ve Ahlak',NULL,8,NULL,'2026-03-01 09:30:46','2026-03-01 09:30:46'),(157,6,'Hz. Muhammed\'in (s.a.v.) Hayatı',NULL,8,NULL,'2026-03-01 09:30:46','2026-03-01 09:30:46'),(158,6,'Kur\'an-ı Kerim ve Özellikleri',NULL,8,NULL,'2026-03-01 09:30:46','2026-03-01 09:30:46'),(159,6,'Vahiy ve Akıl',NULL,8,NULL,'2026-03-01 09:30:46','2026-03-01 09:30:46'),(160,7,'Bilinmeyen Kazanım',NULL,7,NULL,'2026-03-01 10:45:36','2026-03-01 10:45:36');
/*!40000 ALTER TABLE `outcomes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',3,'auth-token','f803e8aa7db77fff92134cf6d4e2bf0ba35670d034270ebe94e6331923f0a406','[\"*\"]',NULL,NULL,'2026-03-01 06:51:56','2026-03-01 06:51:56'),(2,'App\\Models\\User',3,'auth-token','0913280d57d0284b0200b5d143f1005b573cc8ab41ca31afb9b090f8ed7e396e','[\"*\"]',NULL,NULL,'2026-03-01 07:46:35','2026-03-01 07:46:35'),(3,'App\\Models\\User',4,'auth-token','49490d273ae4115266619bdf6fa3ef3881a196666539268e4242cc03f2cd5261','[\"*\"]',NULL,NULL,'2026-03-01 08:05:56','2026-03-01 08:05:56'),(4,'App\\Models\\User',4,'auth-token','671e502b79b61c09fe1d2894a33e924dfeb680ba2b8207bea52a62e8b4902ee1','[\"*\"]',NULL,NULL,'2026-03-01 08:10:10','2026-03-01 08:10:10'),(5,'App\\Models\\User',4,'auth-token','4770aa870b938d162a58a2a378e8d1b0b627dc4ce0291a6e39becc5106942876','[\"*\"]',NULL,NULL,'2026-03-01 12:24:41','2026-03-01 12:24:41');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `outcome_id` bigint unsigned DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ocr_text` text COLLATE utf8mb4_unicode_ci,
  `ai_analysis` json DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `correct_answer` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questions_user_id_foreign` (`user_id`),
  KEY `questions_subject_id_foreign` (`subject_id`),
  KEY `questions_outcome_id_foreign` (`outcome_id`),
  CONSTRAINT `questions_outcome_id_foreign` FOREIGN KEY (`outcome_id`) REFERENCES `outcomes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `questions_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `questions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` VALUES (3,4,1,18,'questions/iQzE9sb1hNpYaILmyW5Ih8IHXP1aQ3MoQAjQNAtJ.jpg','Elif, yeni aldığı bir kitabın 18 sayfasını ilk gün okuyor. Kitabın kalan sayfalarını ise bazı günler 15, bazı günler 20 sayfa okuyarak kitabı toplam 25 günde bitiriyor.\n\nElif kitaptan x gün 20 sayfa okuduğuna göre kitabın toplam sayfa sayısını x cinsinden veren cebirsel ifade aşağıdakilerden hangisidir?\n\nA) 35x + 360\nB) 5x + 378\nC) 5x + 342\nD) 5x + 393\n\nSİNAN KUZUCU\nYAYINLARI','{\"outcome\": \"Cebirsel İfadeler\", \"subject\": \"Matematik\", \"ocr_text\": \"Elif, yeni aldığı bir kitabın 18 sayfasını ilk gün okuyor. Kitabın kalan sayfalarını ise bazı günler 15, bazı günler 20 sayfa okuyarak kitabı toplam 25 günde bitiriyor.\\n\\nElif kitaptan x gün 20 sayfa okuduğuna göre kitabın toplam sayfa sayısını x cinsinden veren cebirsel ifade aşağıdakilerden hangisidir?\\n\\nA) 35x + 360\\nB) 5x + 378\\nC) 5x + 342\\nD) 5x + 393\\n\\nSİNAN KUZUCU\\nYAYINLARI\", \"explanation\": \"Bu soru, Elif\'in okuma alışkanlıkları üzerinden kitabın toplam sayfa sayısını cebirsel bir ifade ile temsil etmeyi gerektirir. Elif ilk gün 18 sayfa okumuş, geriye kalan günlerde ise x gün 20 sayfa okuyarak kitabı 25 günde bitirmiştir. Toplam gün sayısı üzerinden denklem oluşturularak doğru cebirsel ifade bulunur.\"}','analyzed','2026-03-01 08:54:07','2026-03-01 08:54:16',NULL),(5,4,1,21,'questions/5l4PXnARTUBkucZYfVQc9vgG8OkeValENrDXUz4M.jpg','Soru: Aşağıda verilen ABCD dikdörtgeninin çevre uzunluğu 54 cm olduğuna göre, KLMN karesinin alanı kaç cm² olur?\n\nYukarıda verilen bu sorunun çözümünde, aşağıdaki adımlar takip edilmiştir.\n\n1. adım : 2[(x + 3) + x] = 54\n2. adım : 2x + 3 = 27\n3. adım : 2x = 24\n4. adım : x = 12\n5. adım : 4 . 12 = 48\n\nBuna göre sorunun çözümü yapılırken kaçıncı adımda hata yapılmıştır?\n\nA) 2\nB) 3\nC) 4','{\"outcome\": \"Denklem Çözme\", \"subject\": \"Matematik\", \"ocr_text\": \"Soru: Aşağıda verilen ABCD dikdörtgeninin çevre uzunluğu 54 cm olduğuna göre, KLMN karesinin alanı kaç cm² olur?\\n\\nYukarıda verilen bu sorunun çözümünde, aşağıdaki adımlar takip edilmiştir.\\n\\n1. adım : 2[(x + 3) + x] = 54\\n2. adım : 2x + 3 = 27\\n3. adım : 2x = 24\\n4. adım : x = 12\\n5. adım : 4 . 12 = 48\\n\\nBuna göre sorunun çözümü yapılırken kaçıncı adımda hata yapılmıştır?\\n\\nA) 2\\nB) 3\\nC) 4\", \"explanation\": \"Soru, bir dikdörtgenin çevresine dayalı bir problem çözüyor. İlk adımda çevre denklem doğru kurulmuş ancak ikinci adımda yanlış bir şekilde dağılmış. Doğru işlem yapılırsa 2[(x+3) + x] = 54 ifadesi 2x + 6 = 54 şeklinde olmalı. Hata 2. adımda yapılmış.\", \"correct_answer\": null}','analyzed','2026-03-01 10:14:26','2026-03-01 10:14:26',NULL),(6,4,1,5,'questions/PTospVl4vvCCJdio2JzjlJWngeFPnsr1AglNvGY2.jpg','Her birinin üzerinde birer rasyonel sayı yazan dörder adet top aşağıdaki gibi A ve B kutularına atılmıştır. Bu kutulardan birer adet top alındığında kutulardaki doğal sayı belirten top sayıları birbirine eşit olmaktadır. Buna göre kutulardan alınan toplar aşağıdakilerden hangisi olabilir?','{\"outcome\": \"Rasyonel Sayılar\", \"subject\": \"Matematik\", \"ocr_text\": \"Her birinin üzerinde birer rasyonel sayı yazan dörder adet top aşağıdaki gibi A ve B kutularına atılmıştır. Bu kutulardan birer adet top alındığında kutulardaki doğal sayı belirten top sayıları birbirine eşit olmaktadır. Buna göre kutulardan alınan toplar aşağıdakilerden hangisi olabilir?\", \"explanation\": \"Soruda her iki kutudan birer top seçilerek üzerlerindeki rasyonel sayıların doğal sayılara eşit olacak şekilde eşleştirilmesi gerekiyor. Verilen seçenekler içerisinde toplamları doğal sayı olan seçenek bulunmalıdır. Bu, rasyonel sayılarla işlem yapma becerisini ölçer.\", \"correct_answer\": null}','analyzed','2026-03-01 10:15:04','2026-03-01 10:15:04',NULL),(7,4,1,35,'questions/Kcddj2ONrzwSgv0PZwAL80l4Y2J6RuDyE6MV2X9h.jpg','2. Gökmen Bey 2000 m²\'lik bir iş yeri yaptırmak istiyor. Alacağı parselleri birleştirerek iş yerini yaptıracaktır.\nGökmen Bey yaptıracağı iş yeri için en az kaç TL öder?\nA) 42000\nB) 39000\nC) 38000\nD) 32000','{\"outcome\": \"Alan ve Çevre\", \"subject\": \"Matematik\", \"ocr_text\": \"2. Gökmen Bey 2000 m²\'lik bir iş yeri yaptırmak istiyor. Alacağı parselleri birleştirerek iş yerini yaptıracaktır.\\nGökmen Bey yaptıracağı iş yeri için en az kaç TL öder?\\nA) 42000\\nB) 39000\\nC) 38000\\nD) 32000\", \"explanation\": \"Bu soru, bir iş yerinin alanı için en düşük maliyeti belirlemeye yönelik bir problem içermektedir. Öğrencilerden, çeşitli seçenekleri değerlendirerek en düşük maliyeti hesaplamaları beklenir.\", \"correct_answer\": null}','analyzed','2026-03-01 10:15:29','2026-03-01 10:15:29',NULL);
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
INSERT INTO `sessions` VALUES ('hAwBg89moK0r6xtJR7oWKqploLkz4k7EnzIqmI57',4,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiN0I4dW9hcUZMV1FwR0VTemNJS0RBR3h0Yk9QRVF6ZDNaMDZxekdYYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly9sb2NhbGhvc3QvZGVyc18vcHVibGljL2FwaS9zdWJqZWN0cy8xL291dGNvbWVzIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiI3ZThjZjEzZWRiMzFkNzIyNDZhZWNjYzBlZmEzMGY0MzE4NjkxNGQ0YWM0ZTliODUzYzFkYjY5NGIyYzkxNjQ0Ijt9',1772378957);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'Matematik','📐','Sayılar, geometri, cebir ve problem çözme',1,'2026-03-01 06:34:49','2026-03-01 06:34:49'),(2,'Türkçe','📖','Okuma anlama, dil bilgisi ve yazılı anlatım',1,'2026-03-01 06:34:49','2026-03-01 06:34:49'),(3,'İngilizce','🌍','İngilizce dil becerileri ve gramer',1,'2026-03-01 06:34:49','2026-03-01 06:34:49'),(4,'Fen Bilimleri','🔬','Fizik, kimya, biyoloji ve yer bilimleri',1,'2026-03-01 06:34:49','2026-03-01 06:34:49'),(5,'Sosyal Bilgiler','🌎','Tarih, coğrafya ve vatandaşlık',1,'2026-03-01 06:34:49','2026-03-01 06:34:49'),(6,'Din',NULL,NULL,1,'2026-03-01 09:25:03','2026-03-01 09:25:03'),(7,'Bilinmeyen Ders',NULL,NULL,1,'2026-03-01 10:43:45','2026-03-01 10:43:45');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade_level` tinyint NOT NULL DEFAULT '5',
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'Test Öğrenci','test@smarttest.com',NULL,'$2y$12$Z6f4OtHIe6/vCyUek47Ecem7JuQ1DXudegL0z9K.14J6dgNsqE3Ku',5,'student',NULL,NULL,'2026-03-01 06:43:23','2026-03-01 06:43:23'),(4,'Nihat Balkan','nihatbalkanai@gmail.com',NULL,'$2y$12$qBhAPl.BA3DmGBsSd6cbQOqVtzPcD1TcnS9Dv2pUstCxw4RYDevgq',7,'student',NULL,NULL,'2026-03-01 07:59:52','2026-03-01 08:21:26');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'ders_'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-01 18:34:59
