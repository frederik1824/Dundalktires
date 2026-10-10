-- MySQL dump 10.13  Distrib 26.7.0, for macos26.6 (arm64)
--
-- Host: 127.0.0.1    Database: dundalk_tires
-- ------------------------------------------------------
-- Server version	26.7.0

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
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `appointments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `vehicle_id` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `work_order_id` bigint unsigned DEFAULT NULL,
  `number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('scheduled','confirmed','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `service_type` enum('tire_change','rotation','alignment','inspection','repair','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `scheduled_at` datetime NOT NULL,
  `duration_minutes` smallint unsigned NOT NULL DEFAULT '60',
  `notes` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `appointments_number_unique` (`number`),
  KEY `appointments_branch_id_foreign` (`branch_id`),
  KEY `appointments_customer_id_foreign` (`customer_id`),
  KEY `appointments_vehicle_id_foreign` (`vehicle_id`),
  KEY `appointments_assigned_to_foreign` (`assigned_to`),
  KEY `appointments_created_by_foreign` (`created_by`),
  KEY `appointments_work_order_id_foreign` (`work_order_id`),
  CONSTRAINT `appointments_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'America/New_York',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,'Dundalk Used & New Tires','Dundalk, MD','','','America/New_York','USD',1,'2026-09-25 18:10:57','2026-09-25 18:10:57');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
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
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('dundalk-tires-cache-setting..tire_disposal_cost','N;',1791049163),('dundalk-tires-cache-spatie.permission.cache','a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:21:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"view_dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:12:\"view_reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:16:\"manage_inventory\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:14:\"view_inventory\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:16:\"manage_purchases\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:14:\"view_purchases\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:12:\"manage_sales\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:10:\"view_sales\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:16:\"manage_customers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:14:\"view_customers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:15:\"manage_services\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:13:\"view_services\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:18:\"manage_work_orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:16:\"view_work_orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:15:\"manage_expenses\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:13:\"view_expenses\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:16:\"manage_employees\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:14:\"view_employees\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:15:\"manage_settings\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:12:\"manage_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:14:\"view_audit_log\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}}s:5:\"roles\";a:4:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:5:\"owner\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:9:\"secretary\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:8:\"mechanic\";s:1:\"c\";s:3:\"web\";}}}',1791130328);
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
  `expiration` bigint NOT NULL,
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
-- Table structure for table `cash_closes`
--

DROP TABLE IF EXISTS `cash_closes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_closes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `opened_by` bigint unsigned DEFAULT NULL,
  `opened_at` timestamp NULL DEFAULT NULL,
  `opening_fund` decimal(12,2) NOT NULL DEFAULT '0.00',
  `date` date NOT NULL,
  `closed_by` bigint unsigned DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `status` enum('open','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `system_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `system_amounts` json DEFAULT NULL,
  `physical_total` decimal(12,2) DEFAULT NULL,
  `physical_amounts` json DEFAULT NULL,
  `difference` decimal(12,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_closes_branch_id_date_unique` (`branch_id`,`date`),
  KEY `cash_closes_closed_by_foreign` (`closed_by`),
  KEY `cash_closes_opened_by_foreign` (`opened_by`),
  CONSTRAINT `cash_closes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_closes_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_closes_opened_by_foreign` FOREIGN KEY (`opened_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_closes`
--

LOCK TABLES `cash_closes` WRITE;
/*!40000 ALTER TABLE `cash_closes` DISABLE KEYS */;
INSERT INTO `cash_closes` VALUES (1,1,NULL,NULL,0.00,'2026-09-27',NULL,NULL,'open',1559.95,'{\"cash\": 1559.95}',1559.95,'{\"cash\": 1559.95}',0.00,NULL,'2026-09-27 21:01:36','2026-09-27 21:13:34'),(2,1,NULL,NULL,0.00,'2026-09-28',1,'2026-09-28 19:42:07','closed',2587.90,'{\"card\": 169.98, \"cash\": 2247.92, \"transfer\": 170}',2587.90,'{\"card\": 169.98, \"cash\": 2247.92, \"transfer\": 170}',0.00,NULL,'2026-09-28 19:11:18','2026-09-28 19:42:07'),(3,1,1,'2026-10-03 02:39:22',6000.00,'2026-10-02',NULL,NULL,'open',0.00,NULL,NULL,NULL,NULL,NULL,'2026-10-03 02:39:22','2026-10-03 02:39:22');
/*!40000 ALTER TABLE `cash_closes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_notes`
--

DROP TABLE IF EXISTS `credit_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` enum('return','error','discount','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `refund_method` enum('cash','card','zelle','cashapp','transfer','store_credit','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `credit_notes_number_unique` (`number`),
  KEY `credit_notes_invoice_id_foreign` (`invoice_id`),
  KEY `credit_notes_branch_id_foreign` (`branch_id`),
  KEY `credit_notes_created_by_foreign` (`created_by`),
  CONSTRAINT `credit_notes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `credit_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `credit_notes_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_notes`
--

LOCK TABLES `credit_notes` WRITE;
/*!40000 ALTER TABLE `credit_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customers_branch_id_foreign` (`branch_id`),
  KEY `customers_name_index` (`name`),
  KEY `customers_phone_index` (`phone`),
  CONSTRAINT `customers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (22,1,'James Carter','(410) 555-1023','jcarter@gmail.com','112 Merritt Blvd, Dundalk, MD 21222',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(23,1,'Maria Gonzalez','(410) 555-3847','mariag@yahoo.com','320 Holabird Ave, Baltimore, MD 21224',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(24,1,'Robert Kim','(410) 555-7291','rkim@hotmail.com','5 Inverness Dr, Dundalk, MD 21222',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(25,1,'Tiffany Brooks','(410) 555-4612','tbrooks@gmail.com','800 Wise Ave, Baltimore, MD 21222',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(26,1,'Anthony Nguyen','(410) 555-0837','anguyen@gmail.com','44 Moravia Rd, Baltimore, MD 21206',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(27,1,'Patricia Davis','(410) 555-6530','pdavis@outlook.com','1903 Eastern Ave, Baltimore, MD 21231',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(28,1,'Carlos Reyes','(410) 555-2194','creyes@gmail.com','7723 Belair Rd, Baltimore, MD 21236',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(29,1,'Shawna Mitchell','(410) 555-8842','smitch@gmail.com','3301 Sinclair Ln, Baltimore, MD 21213',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(30,1,'William Turner','(410) 555-3377','wturner@yahoo.com','900 S Clinton St, Baltimore, MD 21224',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(31,1,'Linda Pham','(410) 555-7704','lpham@gmail.com','200 Old Eastern Ave, Dundalk, MD 21222',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(32,NULL,'Luis Rodriguez','89009090997','lrodriguez@gmail.com',NULL,NULL,1,'2026-09-27 22:10:45','2026-09-27 22:10:45',NULL),(33,NULL,'Frederik Lopez','8295540303','flopez@devsdesign.org','Santo Domingo',NULL,1,'2026-09-28 19:46:04','2026-09-28 19:46:04',NULL);
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_categories`
--

DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('fixed','variable') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'variable',
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#64748b',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_categories`
--

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;
INSERT INTO `expense_categories` VALUES (1,'Rent / Lease','fixed','#2563eb',1,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(2,'Utilities','fixed','#7c3aed',1,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(3,'Insurance','fixed','#059669',1,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(4,'Payroll','fixed','#d97706',1,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(5,'Inventory / Parts','variable','#dc2626',1,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(6,'Equipment','variable','#0891b2',1,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(7,'Marketing','variable','#7c3aed',1,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(8,'Repairs & Maintenance','variable','#ea580c',1,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(9,'Office Supplies','variable','#64748b',1,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(10,'Other','variable','#94a3b8',1,'2026-09-25 20:25:18','2026-09-25 20:25:18');
/*!40000 ALTER TABLE `expense_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','zelle','cashapp','transfer','check','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evidence` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `recurrence` enum('weekly','biweekly','monthly','yearly') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` date NOT NULL,
  `next_due_at` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_branch_id_foreign` (`branch_id`),
  KEY `expenses_created_by_foreign` (`created_by`),
  KEY `expenses_category_id_foreign` (`category_id`),
  KEY `expenses_paid_at_index` (`paid_at`),
  CONSTRAINT `expenses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
INSERT INTO `expenses` VALUES (1,1,1,2,'BGE Electric & Gas – August 2026',485.50,'transfer','BGE',NULL,NULL,1,'monthly','2026-08-29',NULL,NULL,'2026-08-30 00:20:44','2026-08-30 00:20:44',NULL),(2,1,1,3,'Commercial liability insurance – Sep',620.00,'transfer','Nationwide Business',NULL,NULL,1,'monthly','2026-09-24',NULL,NULL,'2026-09-25 00:20:44','2026-09-27 19:38:29','2026-09-27 19:38:29'),(3,1,1,4,'Payroll – Week 38',4800.00,'transfer','ADP Payroll Services',NULL,NULL,1,'weekly','2026-09-20',NULL,NULL,'2026-09-21 00:20:44','2026-09-27 19:38:26','2026-09-27 19:38:26'),(4,1,1,4,'Payroll – Week 37',4800.00,'transfer','ADP Payroll Services',NULL,NULL,1,'weekly','2026-09-13',NULL,NULL,'2026-09-14 00:20:44','2026-09-27 19:38:33','2026-09-27 19:38:33'),(5,1,1,6,'Tire changer maintenance service',280.00,'card','Coats Company',NULL,NULL,0,NULL,'2026-09-07',NULL,NULL,'2026-09-08 00:20:44','2026-09-08 00:20:44',NULL),(6,1,1,8,'Shop compressor repair',145.00,'cash','Baltimore Air Tools',NULL,NULL,0,NULL,'2026-09-11',NULL,NULL,'2026-09-12 00:20:44','2026-09-12 00:20:44',NULL),(7,1,1,9,'Printer paper & labels',62.40,'card','Staples',NULL,NULL,0,NULL,'2026-09-16',NULL,NULL,'2026-09-17 00:20:44','2026-09-17 00:20:44',NULL),(8,1,1,7,'Google Ads – September campaign',350.00,'card','Google LLC',NULL,NULL,1,'monthly','2026-09-22',NULL,NULL,'2026-09-23 00:20:44','2026-09-26 03:37:53','2026-09-26 03:37:53'),(9,1,1,5,'Emergency brake fluid stock purchase',95.60,'cash','O\'Reilly Auto Parts',NULL,NULL,0,NULL,'2026-09-18',NULL,NULL,'2026-09-19 00:20:44','2026-09-19 00:20:44',NULL),(10,1,1,10,'Waste tire disposal – 40 units',120.00,'cash','Maryland Tire Recycling',NULL,NULL,0,NULL,'2026-09-19',NULL,NULL,'2026-09-20 00:20:44','2026-09-20 00:20:44',NULL),(11,1,1,2,'Water & sewer bill – August',88.20,'transfer','Baltimore City DPW',NULL,NULL,1,'monthly','2026-09-03',NULL,NULL,'2026-09-04 00:20:44','2026-09-04 00:20:44',NULL);
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
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
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
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
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `type` enum('service','product','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'service',
  `reference_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `qty` smallint unsigned NOT NULL DEFAULT '1',
  `unit_price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
INSERT INTO `invoice_items` VALUES (6,3,'service',25,'Mount & Dismount (each)','Mount & Dismount (each)',4,12.00,0.00,48.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(7,3,'service',33,'Wheel Balance (set of 4)','Wheel Balance (set of 4)',1,50.00,0.00,50.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(8,3,'service',NULL,'Tire Disposal Fee (each)','Tire Disposal Fee (each)',4,4.00,0.00,16.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(9,3,'product',42,'GT Radial Champiro VP1 215/55R17','GT Radial Champiro VP1 215/55R17',4,89.99,0.00,359.96,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(10,3,'product',52,'Standard Valve Stem','Standard Valve Stem',4,3.50,0.00,14.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(11,4,'service',25,'Mount & Dismount (each)','Mount & Dismount (each)',4,15.00,0.00,60.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(12,4,'service',36,'4-Wheel Alignment','4-Wheel Alignment',1,95.00,0.00,95.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(13,4,'service',33,'Wheel Balance (set of 4)','Wheel Balance (set of 4)',1,60.00,0.00,60.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(14,4,'product',45,'Firestone Destination LE3 245/65R17','Firestone Destination LE3 245/65R17',4,119.99,0.00,479.96,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(15,4,'product',52,'Standard Valve Stem','Standard Valve Stem',4,3.50,0.00,14.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(16,5,'service',26,'Tire Repair – Plug','Tire Repair – Plug',2,15.00,0.00,30.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(17,5,'product',58,'Tire Plug Repair Kit','Tire Plug Repair Kit',2,4.99,0.00,9.98,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(18,6,'service',25,'Mount & Dismount (each)','Mount & Dismount (each)',4,12.00,0.00,48.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(19,6,'service',33,'Wheel Balance (set of 4)','Wheel Balance (set of 4)',1,50.00,0.00,50.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(20,6,'service',NULL,'Tire Disposal Fee (each)','Tire Disposal Fee (each)',4,4.00,0.00,16.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(21,6,'product',47,'Hankook Kinergy GT 195/60R15','Hankook Kinergy GT 195/60R15',4,79.99,0.00,319.96,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(22,6,'product',52,'Standard Valve Stem','Standard Valve Stem',4,3.50,0.00,14.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(23,7,'service',NULL,'TPMS Sensor Replace (each)','TPMS Sensor Replace (each)',4,55.00,0.00,220.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(24,7,'service',NULL,'TPMS Sensor Reset','TPMS Sensor Reset',1,20.00,0.00,20.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(25,7,'product',53,'TPMS Valve Stem Replacement','TPMS Valve Stem Replacement',4,24.99,0.00,99.96,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(26,8,'service',28,'Tire Rotation (set of 4)','Tire Rotation (set of 4)',1,26.00,0.00,26.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(27,8,'service',35,'Front Alignment','Front Alignment',1,80.00,0.00,80.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(28,9,'service',25,'Mount & Dismount (each)','Mount & Dismount (each)',4,15.00,0.00,60.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(29,9,'service',33,'Wheel Balance (set of 4)','Wheel Balance (set of 4)',1,60.00,0.00,60.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(30,9,'service',NULL,'Nitrogen Fill (set of 4)','Nitrogen Fill (set of 4)',1,20.00,0.00,20.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(31,9,'service',NULL,'Tire Disposal Fee (each)','Tire Disposal Fee (each)',4,4.00,0.00,16.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(32,9,'product',46,'Cooper Discoverer AT3 225/60R17','Cooper Discoverer AT3 225/60R17',4,149.99,0.00,599.96,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(33,9,'product',52,'Standard Valve Stem','Standard Valve Stem',4,3.50,0.00,14.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(34,10,'service',28,'Tire Rotation (set of 4)',NULL,1,20.00,0.00,20.00,'2026-09-26 02:41:30','2026-09-26 02:41:30'),(35,10,'service',27,'Tire Repair – Patch',NULL,1,25.00,0.00,25.00,'2026-09-26 02:41:30','2026-09-26 02:41:30'),(36,10,'service',26,'Tire Repair – Plug',NULL,1,15.00,0.00,15.00,'2026-09-26 02:41:30','2026-09-26 02:41:30'),(39,13,'product',43,'Michelin Defender 205/55R16',NULL,1,165.00,0.00,165.00,'2026-09-26 03:47:30','2026-09-26 03:47:30'),(40,14,'product',49,'Bridgestone Dueler H/L 225/65R17',NULL,10,145.00,0.00,1450.00,'2026-09-26 03:48:25','2026-09-26 03:48:25'),(41,15,'product',43,'Michelin Defender 205/55R16',NULL,1,165.00,0.00,165.00,'2026-09-27 19:44:21','2026-09-27 19:44:21'),(42,15,'product',45,'Firestone Destination LE3 245/65R17',NULL,1,119.99,0.00,119.99,'2026-09-27 19:44:21','2026-09-27 19:44:21'),(43,15,'product',46,'Cooper Discoverer AT3 225/60R17',NULL,1,149.99,0.00,149.99,'2026-09-27 19:44:21','2026-09-27 19:44:21'),(44,15,'product',50,'Continental TrueContact 185/65R15',NULL,1,99.99,0.00,99.99,'2026-09-27 19:44:21','2026-09-27 19:44:21'),(45,15,'product',49,'Bridgestone Dueler H/L 225/65R17',NULL,1,145.00,0.00,145.00,'2026-09-27 19:44:21','2026-09-27 19:44:21'),(46,15,'product',47,'Hankook Kinergy GT 195/60R15',NULL,1,79.99,0.00,79.99,'2026-09-27 19:44:21','2026-09-27 19:44:21'),(47,15,'product',50,'Continental TrueContact 185/65R15',NULL,1,99.99,0.00,99.99,'2026-09-27 19:44:21','2026-09-27 19:44:21'),(48,16,'product',44,'Goodyear Assurance WS 235/60R17',NULL,1,139.99,0.00,139.99,'2026-09-27 20:14:46','2026-09-27 20:14:46'),(49,16,'product',44,'Goodyear Assurance WS 235/60R17',NULL,1,139.99,0.00,139.99,'2026-09-27 20:14:46','2026-09-27 20:14:46'),(50,16,'product',43,'Michelin Defender 205/55R16',NULL,1,165.00,0.00,165.00,'2026-09-27 20:14:46','2026-09-27 20:14:46'),(51,16,'product',45,'Firestone Destination LE3 245/65R17',NULL,1,119.99,0.00,119.99,'2026-09-27 20:14:46','2026-09-27 20:14:46'),(52,16,'product',47,'Hankook Kinergy GT 195/60R15',NULL,1,79.99,0.00,79.99,'2026-09-27 20:14:46','2026-09-27 20:14:46'),(53,16,'product',48,'Toyo Open Country AT3 265/70R17',NULL,1,179.99,0.00,179.99,'2026-09-27 20:14:46','2026-09-27 20:14:46'),(54,16,'product',49,'Bridgestone Dueler H/L 225/65R17',NULL,1,145.00,0.00,145.00,'2026-09-27 20:14:46','2026-09-27 20:14:46'),(55,17,'product',45,'Firestone Destination LE3 245/65R17',NULL,1,119.99,0.00,119.99,'2026-09-27 22:13:12','2026-09-27 22:13:12'),(56,17,'product',45,'Firestone Destination LE3 245/65R17',NULL,1,119.99,0.00,119.99,'2026-09-27 22:13:12','2026-09-27 22:13:12'),(57,17,'product',45,'Firestone Destination LE3 245/65R17',NULL,1,119.99,0.00,119.99,'2026-09-27 22:13:12','2026-09-27 22:13:12'),(58,17,'product',44,'Goodyear Assurance WS 235/60R17',NULL,1,139.99,0.00,139.99,'2026-09-27 22:13:12','2026-09-27 22:13:12'),(59,17,'product',45,'Firestone Destination LE3 245/65R17',NULL,1,119.99,0.00,119.99,'2026-09-27 22:13:12','2026-09-27 22:13:12'),(60,17,'product',45,'Firestone Destination LE3 245/65R17',NULL,1,119.99,0.00,119.99,'2026-09-27 22:13:12','2026-09-27 22:13:12'),(61,17,'product',49,'Bridgestone Dueler H/L 225/65R17',NULL,1,145.00,0.00,145.00,'2026-09-27 22:13:12','2026-09-27 22:13:12'),(62,18,'service',28,'Tire Rotation (set of 4)',NULL,19,20.00,0.00,380.00,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(63,18,'service',34,'Road Force Balance (each)',NULL,10,22.00,0.00,220.00,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(64,18,'service',26,'Tire Repair – Plug',NULL,1,15.00,0.00,15.00,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(65,18,'service',25,'Mount & Dismount (each)',NULL,1,12.00,0.00,12.00,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(66,18,'service',25,'Mount & Dismount (each)',NULL,1,12.00,0.00,12.00,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(67,18,'service',25,'Mount & Dismount (each)',NULL,1,12.00,0.00,12.00,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(68,18,'service',35,'Front Alignment',NULL,1,65.00,0.00,65.00,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(69,18,'service',25,'Mount & Dismount (each)',NULL,1,12.00,0.00,12.00,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(70,18,'service',35,'Front Alignment',NULL,1,65.00,0.00,65.00,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(71,18,'service',30,'Wheel Swap (set of 4)',NULL,1,40.00,0.00,40.00,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(72,18,'product',43,'Michelin Defender 205/55R16',NULL,1,165.00,0.00,165.00,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(73,18,'product',48,'Toyo Open Country AT3 265/70R17',NULL,1,179.99,0.00,179.99,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(74,18,'product',43,'Michelin Defender 205/55R16',NULL,1,165.00,0.00,165.00,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(75,18,'product',44,'Goodyear Assurance WS 235/60R17',NULL,1,139.99,0.00,139.99,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(76,18,'product',44,'Goodyear Assurance WS 235/60R17',NULL,1,139.99,0.00,139.99,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(77,18,'product',44,'Goodyear Assurance WS 235/60R17',NULL,1,139.99,0.00,139.99,'2026-09-28 18:44:31','2026-09-28 18:44:31'),(78,19,'product',42,'GT Radial Champiro VP1 215/55R17',NULL,1,89.99,0.00,89.99,'2026-09-28 19:31:57','2026-09-28 19:31:57'),(79,20,'service',28,'Tire Rotation (set of 4)',NULL,1,20.00,0.00,20.00,'2026-09-28 19:38:56','2026-09-28 19:38:56'),(80,20,'service',26,'Tire Repair – Plug',NULL,1,15.00,0.00,15.00,'2026-09-28 19:38:56','2026-09-28 19:38:56'),(81,20,'product',45,'Firestone Destination LE3 245/65R17',NULL,1,119.99,0.00,119.99,'2026-09-28 19:38:56','2026-09-28 19:38:56'),(82,20,'product',45,'Firestone Destination LE3 245/65R17',NULL,1,119.99,0.00,119.99,'2026-09-28 19:38:56','2026-09-28 19:38:56'),(83,20,'product',45,'Firestone Destination LE3 245/65R17',NULL,1,119.99,0.00,119.99,'2026-09-28 19:38:56','2026-09-28 19:38:56'),(84,21,'product',42,'GT Radial Champiro VP1 215/55R17',NULL,1,89.99,0.00,89.99,'2026-09-28 19:40:43','2026-09-28 19:40:43'),(85,21,'product',47,'Hankook Kinergy GT 195/60R15',NULL,1,79.99,0.00,79.99,'2026-09-28 19:40:43','2026-09-28 19:40:43'),(86,22,'service',25,'Mount & Dismount (each)',NULL,5,12.00,0.00,60.00,'2026-09-28 19:41:33','2026-09-28 19:41:33'),(87,22,'service',28,'Tire Rotation (set of 4)',NULL,6,20.00,0.00,120.00,'2026-09-28 19:41:33','2026-09-28 19:41:33'),(88,23,'product',45,'Firestone Destination LE3 245/65R17',NULL,1,119.99,0.00,119.99,'2026-10-03 02:40:34','2026-10-03 02:40:34'),(89,23,'product',47,'Hankook Kinergy GT 195/60R15',NULL,1,79.99,0.00,79.99,'2026-10-03 02:40:34','2026-10-03 02:40:34'),(90,23,'product',47,'Hankook Kinergy GT 195/60R15',NULL,1,79.99,0.00,79.99,'2026-10-03 02:40:34','2026-10-03 02:40:34'),(91,23,'product',50,'Continental TrueContact 185/65R15',NULL,1,99.99,0.00,99.99,'2026-10-03 02:40:34','2026-10-03 02:40:34'),(92,23,'product',51,'Pirelli Cinturato P7 245/45R18',NULL,1,199.99,0.00,199.99,'2026-10-03 02:40:34','2026-10-03 02:40:34'),(93,23,'product',46,'Cooper Discoverer AT3 225/60R17',NULL,1,149.99,0.00,149.99,'2026-10-03 02:40:34','2026-10-03 02:40:34'),(94,23,'product',42,'GT Radial Champiro VP1 215/55R17',NULL,1,89.99,0.00,89.99,'2026-10-03 02:40:34','2026-10-03 02:40:34'),(95,24,'product',48,'Toyo Open Country AT3 265/70R17',NULL,1,179.99,0.00,179.99,'2026-10-03 02:42:46','2026-10-03 02:42:46'),(96,24,'product',45,'Firestone Destination LE3 245/65R17',NULL,1,119.99,0.00,119.99,'2026-10-03 02:42:46','2026-10-03 02:42:46'),(97,24,'product',47,'Hankook Kinergy GT 195/60R15',NULL,1,79.99,0.00,79.99,'2026-10-03 02:42:46','2026-10-03 02:42:46'),(98,24,'product',49,'Bridgestone Dueler H/L 225/65R17',NULL,1,145.00,0.00,145.00,'2026-10-03 02:42:46','2026-10-03 02:42:46'),(99,24,'product',42,'GT Radial Champiro VP1 215/55R17',NULL,1,89.99,0.00,89.99,'2026-10-03 02:42:46','2026-10-03 02:42:46'),(100,24,'product',50,'Continental TrueContact 185/65R15',NULL,1,99.99,0.00,99.99,'2026-10-03 02:42:46','2026-10-03 02:42:46'),(101,24,'product',51,'Pirelli Cinturato P7 245/45R18',NULL,1,199.99,0.00,199.99,'2026-10-03 02:42:46','2026-10-03 02:42:46'),(102,24,'product',43,'Michelin Defender 205/55R16',NULL,1,165.00,0.00,165.00,'2026-10-03 02:42:46','2026-10-03 02:42:46'),(103,24,'product',46,'Cooper Discoverer AT3 225/60R17',NULL,1,149.99,0.00,149.99,'2026-10-03 02:42:46','2026-10-03 02:42:46'),(104,24,'product',42,'GT Radial Champiro VP1 215/55R17',NULL,1,89.99,0.00,89.99,'2026-10-03 02:42:46','2026-10-03 02:42:46');
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `work_order_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','paid','partial','void') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_number_unique` (`number`),
  KEY `invoices_branch_id_foreign` (`branch_id`),
  KEY `invoices_customer_id_foreign` (`customer_id`),
  KEY `invoices_work_order_id_foreign` (`work_order_id`),
  KEY `invoices_created_by_foreign` (`created_by`),
  KEY `invoices_status_index` (`status`),
  KEY `invoices_number_index` (`number`),
  CONSTRAINT `invoices_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (3,1,22,3,1,'INV-0001','paid',NULL,487.96,0.00,0.00,487.96,487.96,'2026-09-26 00:20:44','2026-08-31 22:20:44','2026-09-26 00:20:44',NULL),(4,1,23,4,1,'INV-0002','paid',NULL,708.96,0.00,0.00,708.96,708.96,'2026-09-26 00:20:44','2026-09-05 23:20:44','2026-09-26 00:20:44',NULL),(5,1,24,5,1,'INV-0003','paid',NULL,39.98,0.00,0.00,39.98,39.98,'2026-09-26 00:20:44','2026-09-10 21:20:44','2026-09-26 00:20:44',NULL),(6,1,25,6,1,'INV-0004','paid',NULL,447.96,0.00,0.00,447.96,447.96,'2026-09-26 00:20:44','2026-09-13 23:20:44','2026-09-26 00:20:44',NULL),(7,1,26,7,1,'INV-0005','paid',NULL,339.96,0.00,0.00,339.96,339.96,'2026-09-26 00:20:44','2026-09-16 02:20:44','2026-09-26 00:20:44',NULL),(8,1,27,8,1,'INV-0006','paid',NULL,106.00,0.00,0.00,106.00,106.00,'2026-09-26 00:20:44','2026-09-17 23:20:44','2026-09-26 00:20:44',NULL),(9,1,28,9,1,'INV-0007','paid',NULL,769.96,0.00,0.00,769.96,769.96,'2026-09-26 00:20:44','2026-09-20 23:20:44','2026-09-26 00:20:44',NULL),(10,1,NULL,NULL,1,'INV-0008','void',NULL,60.00,0.00,10.00,50.00,50.00,'2026-09-26 02:41:30','2026-09-26 02:41:30','2026-09-26 02:41:38',NULL),(13,1,NULL,NULL,1,'INV-0009','draft',NULL,165.00,0.00,0.00,165.00,0.00,NULL,'2026-09-26 03:47:30','2026-09-26 03:47:30',NULL),(14,1,NULL,NULL,1,'INV-0010','draft',NULL,1450.00,0.00,50.00,1400.00,0.00,NULL,'2026-09-26 03:48:25','2026-09-26 03:48:25',NULL),(15,1,25,NULL,1,'INV-0011','paid','Nota de prueba',859.95,0.00,0.00,859.95,859.95,'2026-09-27 19:44:21','2026-09-27 19:44:21','2026-09-27 19:44:21',NULL),(16,1,24,NULL,1,'INV-0012','partial',NULL,969.95,0.00,0.00,969.95,700.00,NULL,'2026-09-27 20:14:46','2026-09-27 20:14:46',NULL),(17,1,32,NULL,1,'INV-0013','partial',NULL,884.94,0.00,0.00,884.94,700.00,NULL,'2026-09-27 22:13:12','2026-09-27 22:13:12',NULL),(18,1,32,14,1,'INV-0014','paid',NULL,1762.96,0.00,0.00,1762.96,1762.96,'2026-09-28 18:44:31','2026-09-28 18:44:31','2026-09-28 18:44:31',NULL),(19,1,NULL,NULL,1,'INV-0015','paid',NULL,89.99,0.00,0.00,89.99,89.99,'2026-09-28 19:31:57','2026-09-28 19:31:57','2026-09-28 19:31:57',NULL),(20,1,32,15,1,'INV-0016','paid',NULL,394.97,0.00,0.00,394.97,394.97,'2026-09-28 19:38:56','2026-09-28 19:38:56','2026-09-28 19:38:56',NULL),(21,1,27,NULL,1,'INV-0017','paid',NULL,169.98,0.00,0.00,169.98,169.98,'2026-09-28 19:40:43','2026-09-28 19:40:43','2026-09-28 19:40:43',NULL),(22,1,NULL,NULL,1,'INV-0018','paid',NULL,180.00,0.00,10.00,170.00,170.00,'2026-09-28 19:41:33','2026-09-28 19:41:33','2026-09-28 19:41:33',NULL),(23,1,33,NULL,1,'INV-0019','paid',NULL,819.93,0.00,0.00,819.93,1000.00,'2026-10-03 02:40:34','2026-10-03 02:40:34','2026-10-03 02:40:34',NULL),(24,1,23,NULL,1,'INV-0020','paid',NULL,1319.92,0.00,0.00,1319.92,1400.00,'2026-10-03 02:42:46','2026-10-03 02:42:46','2026-10-03 02:42:46',NULL);
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
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
  `attempts` smallint unsigned NOT NULL,
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
-- Table structure for table `landing_promotions`
--

DROP TABLE IF EXISTS `landing_promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `landing_promotions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `badge_text` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta_text` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Book Now',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `starts_at` date DEFAULT NULL,
  `ends_at` date DEFAULT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_promotions`
--

LOCK TABLES `landing_promotions` WRITE;
/*!40000 ALTER TABLE `landing_promotions` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_promotions` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_25_000001_create_branches_table',1),(5,'2026_09_25_000002_create_settings_table',1),(6,'2026_09_25_000003_add_branch_foreign_to_users',1),(7,'2026_09_25_140756_create_permission_tables',1),(8,'2026_09_25_100000_create_customers_table',2),(9,'2026_09_25_100001_create_vehicles_table',2),(10,'2026_09_25_200000_create_suppliers_table',3),(11,'2026_09_25_200001_create_product_categories_table',3),(12,'2026_09_25_200002_create_products_table',3),(13,'2026_09_25_200003_create_product_movements_table',3),(14,'2026_09_25_143131_create_purchase_orders_table',4),(15,'2026_09_25_143132_create_purchase_order_items_table',4),(16,'2026_09_25_300000_create_service_categories_table',5),(17,'2026_09_25_300001_create_services_table',5),(18,'2026_09_25_300002_create_service_price_rules_table',5),(19,'2026_09_25_300003_create_work_orders_table',5),(20,'2026_09_25_300004_create_work_order_services_table',5),(21,'2026_09_25_300005_create_work_order_products_table',5),(22,'2026_09_25_400000_create_invoices_table',5),(23,'2026_09_25_400001_create_invoice_items_table',5),(24,'2026_09_25_400002_create_payments_table',5),(25,'2026_09_25_500000_create_expense_categories_table',5),(26,'2026_09_25_500001_create_expenses_table',5),(27,'2026_09_25_232551_create_credit_notes_table',6),(28,'2026_09_25_233930_create_appointments_table',7),(29,'2026_09_27_161647_add_cost_price_to_work_order_pivots',8),(30,'2026_09_27_161650_add_cost_fields_to_work_orders',8),(31,'2026_09_27_161651_create_tire_disposals_table',8),(32,'2026_09_27_161652_create_tire_disposal_pickups_table',8),(33,'2026_09_27_161653_create_service_consumables_table',8),(34,'2026_09_27_161654_create_work_order_consumables_table',8),(35,'2026_09_27_162700_create_cash_closes_table',9),(36,'2026_09_27_171501_add_cost_price_to_services_table',10),(37,'2026_09_28_152256_add_opening_fields_to_cash_closes_table',11),(38,'2026_09_29_000001_create_web_leads_table',12),(39,'2026_09_29_000002_add_staff_fields_to_web_leads_table',13),(40,'2026_09_29_000003_create_landing_promotions_table',13),(41,'2026_09_29_000004_add_is_featured_to_products_table',13),(42,'2026_09_29_000005_create_testimonials_table',14);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(4,'App\\Models\\User',2),(4,'App\\Models\\User',3),(3,'App\\Models\\User',4),(2,'App\\Models\\User',5),(4,'App\\Models\\User',6),(4,'App\\Models\\User',7),(3,'App\\Models\\User',8),(2,'App\\Models\\User',9),(4,'App\\Models\\User',10),(4,'App\\Models\\User',11),(3,'App\\Models\\User',12),(2,'App\\Models\\User',13);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
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
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `received_by` bigint unsigned DEFAULT NULL,
  `method` enum('cash','card','zelle','cashapp','transfer','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `paid_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_invoice_id_foreign` (`invoice_id`),
  KEY `payments_received_by_foreign` (`received_by`),
  CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,3,NULL,'card',487.96,NULL,NULL,'2026-08-31 22:20:44','2026-08-31 22:20:44','2026-08-31 22:20:44'),(2,4,NULL,'cash',708.96,NULL,NULL,'2026-09-05 23:20:44','2026-09-05 23:20:44','2026-09-05 23:20:44'),(3,5,NULL,'cash',39.98,NULL,NULL,'2026-09-10 21:20:44','2026-09-10 21:20:44','2026-09-10 21:20:44'),(4,6,NULL,'zelle',447.96,NULL,NULL,'2026-09-13 23:20:44','2026-09-13 23:20:44','2026-09-13 23:20:44'),(5,7,NULL,'card',339.96,NULL,NULL,'2026-09-16 02:20:44','2026-09-16 02:20:44','2026-09-16 02:20:44'),(6,8,NULL,'cashapp',106.00,NULL,NULL,'2026-09-17 23:20:44','2026-09-17 23:20:44','2026-09-17 23:20:44'),(7,9,NULL,'card',769.96,NULL,NULL,'2026-09-20 23:20:44','2026-09-20 23:20:44','2026-09-20 23:20:44'),(8,10,1,'cash',50.00,NULL,NULL,'2026-09-26 02:41:30','2026-09-26 02:41:30','2026-09-26 02:41:30'),(9,15,1,'cash',859.95,NULL,NULL,'2026-09-27 19:44:21','2026-09-27 19:44:21','2026-09-27 19:44:21'),(10,16,1,'cash',700.00,NULL,NULL,'2026-09-27 20:14:46','2026-09-27 20:14:46','2026-09-27 20:14:46'),(11,17,1,'cash',700.00,NULL,NULL,'2026-09-27 22:13:12','2026-09-27 22:13:12','2026-09-27 22:13:12'),(12,18,1,'cash',1762.96,NULL,NULL,'2026-09-28 18:44:31','2026-09-28 18:44:31','2026-09-28 18:44:31'),(13,19,1,'cash',89.99,NULL,NULL,'2026-09-28 19:31:57','2026-09-28 19:31:57','2026-09-28 19:31:57'),(14,20,1,'cash',394.97,NULL,NULL,'2026-09-28 19:38:56','2026-09-28 19:38:56','2026-09-28 19:38:56'),(15,21,1,'card',169.98,NULL,NULL,'2026-09-28 19:40:43','2026-09-28 19:40:43','2026-09-28 19:40:43'),(16,22,1,'transfer',170.00,NULL,NULL,'2026-09-28 19:41:33','2026-09-28 19:41:33','2026-09-28 19:41:33'),(17,23,1,'cash',1000.00,NULL,NULL,'2026-10-03 02:40:34','2026-10-03 02:40:34','2026-10-03 02:40:34'),(18,24,1,'cash',1400.00,NULL,NULL,'2026-10-03 02:42:46','2026-10-03 02:42:46','2026-10-03 02:42:46');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'view_dashboard','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(2,'view_reports','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(3,'manage_inventory','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(4,'view_inventory','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(5,'manage_purchases','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(6,'view_purchases','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(7,'manage_sales','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(8,'view_sales','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(9,'manage_customers','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(10,'view_customers','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(11,'manage_services','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(12,'view_services','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(13,'manage_work_orders','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(14,'view_work_orders','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(15,'manage_expenses','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(16,'view_expenses','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(17,'manage_employees','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(18,'view_employees','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(19,'manage_settings','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(20,'manage_users','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(21,'view_audit_log','web','2026-09-25 18:10:57','2026-09-25 18:10:57');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'material',
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6B7280',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
INSERT INTO `product_categories` VALUES (1,'All-Season','tire','#2563eb','2026-09-25 18:23:59','2026-09-25 18:23:59'),(2,'Summer','tire','#f59e0b','2026-09-25 18:23:59','2026-09-25 18:23:59'),(3,'Winter','tire','#06b6d4','2026-09-25 18:23:59','2026-09-25 18:23:59'),(4,'All-Terrain','tire','#16a34a','2026-09-25 18:23:59','2026-09-25 18:23:59'),(5,'Performance','tire','#dc2626','2026-09-25 18:23:59','2026-09-25 18:23:59'),(6,'Used Tires','tire','#6b7280','2026-09-25 18:23:59','2026-09-25 18:23:59'),(7,'Valves & TPMS','material','#8b5cf6','2026-09-25 18:23:59','2026-09-25 18:23:59'),(8,'Wheel Weights','material','#0ea5e9','2026-09-25 18:23:59','2026-09-25 18:23:59'),(9,'Lubricants','material','#f97316','2026-09-25 18:23:59','2026-09-25 18:23:59'),(10,'Cleaning Products','material','#14b8a6','2026-09-25 18:23:59','2026-09-25 18:23:59'),(11,'Mounting Supplies','material','#a78bfa','2026-09-25 18:23:59','2026-09-25 18:23:59'),(12,'Other','material','#94a3b8','2026-09-25 18:23:59','2026-09-25 18:23:59');
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_movements`
--

DROP TABLE IF EXISTS `product_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty_before` int NOT NULL,
  `qty_change` int NOT NULL,
  `qty_after` int NOT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_movements_user_id_foreign` (`user_id`),
  KEY `product_movements_branch_id_foreign` (`branch_id`),
  KEY `product_movements_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  KEY `product_movements_product_id_created_at_index` (`product_id`,`created_at`),
  CONSTRAINT `product_movements_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_movements`
--

LOCK TABLES `product_movements` WRITE;
/*!40000 ALTER TABLE `product_movements` DISABLE KEYS */;
INSERT INTO `product_movements` VALUES (3,43,1,1,'App\\Models\\Invoice',13,'sale',12,-1,11,NULL,NULL,'2026-09-26 03:47:30',NULL),(4,49,1,1,'App\\Models\\Invoice',14,'sale',14,-10,4,NULL,NULL,'2026-09-26 03:48:25',NULL),(5,43,1,1,'App\\Models\\Invoice',15,'sale',11,-1,10,NULL,NULL,'2026-09-27 19:44:21',NULL),(6,45,1,1,'App\\Models\\Invoice',15,'sale',16,-1,15,NULL,NULL,'2026-09-27 19:44:21',NULL),(7,46,1,1,'App\\Models\\Invoice',15,'sale',10,-1,9,NULL,NULL,'2026-09-27 19:44:21',NULL),(8,50,1,1,'App\\Models\\Invoice',15,'sale',18,-1,17,NULL,NULL,'2026-09-27 19:44:21',NULL),(9,49,1,1,'App\\Models\\Invoice',15,'sale',10,-1,9,NULL,NULL,'2026-09-27 19:44:21',NULL),(10,47,1,1,'App\\Models\\Invoice',15,'sale',20,-1,19,NULL,NULL,'2026-09-27 19:44:21',NULL),(11,50,1,1,'App\\Models\\Invoice',15,'sale',17,-1,16,NULL,NULL,'2026-09-27 19:44:21',NULL),(12,44,1,1,'App\\Models\\Invoice',16,'sale',8,-1,7,NULL,NULL,'2026-09-27 20:14:46',NULL),(13,44,1,1,'App\\Models\\Invoice',16,'sale',7,-1,6,NULL,NULL,'2026-09-27 20:14:46',NULL),(14,43,1,1,'App\\Models\\Invoice',16,'sale',10,-1,9,NULL,NULL,'2026-09-27 20:14:46',NULL),(15,45,1,1,'App\\Models\\Invoice',16,'sale',15,-1,14,NULL,NULL,'2026-09-27 20:14:46',NULL),(16,47,1,1,'App\\Models\\Invoice',16,'sale',19,-1,18,NULL,NULL,'2026-09-27 20:14:46',NULL),(17,48,1,1,'App\\Models\\Invoice',16,'sale',6,-1,5,NULL,NULL,'2026-09-27 20:14:46',NULL),(18,49,1,1,'App\\Models\\Invoice',16,'sale',9,-1,8,NULL,NULL,'2026-09-27 20:14:46',NULL),(19,45,1,1,'App\\Models\\Invoice',17,'sale',14,-1,13,NULL,NULL,'2026-09-27 22:13:12',NULL),(20,45,1,1,'App\\Models\\Invoice',17,'sale',13,-1,12,NULL,NULL,'2026-09-27 22:13:12',NULL),(21,45,1,1,'App\\Models\\Invoice',17,'sale',12,-1,11,NULL,NULL,'2026-09-27 22:13:12',NULL),(22,44,1,1,'App\\Models\\Invoice',17,'sale',6,-1,5,NULL,NULL,'2026-09-27 22:13:12',NULL),(23,45,1,1,'App\\Models\\Invoice',17,'sale',11,-1,10,NULL,NULL,'2026-09-27 22:13:12',NULL),(24,45,1,1,'App\\Models\\Invoice',17,'sale',10,-1,9,NULL,NULL,'2026-09-27 22:13:12',NULL),(25,49,1,1,'App\\Models\\Invoice',17,'sale',8,-1,7,NULL,NULL,'2026-09-27 22:13:12',NULL),(26,43,1,1,'App\\Models\\WorkOrder',13,'Work order WO-011',9,-1,8,NULL,NULL,'2026-09-28 17:38:11',NULL),(27,43,1,1,'App\\Models\\WorkOrder',13,'Work order WO-011',8,-1,7,NULL,NULL,'2026-09-28 17:38:11',NULL),(28,43,1,1,'App\\Models\\WorkOrder',13,'Work order WO-011',7,-1,6,NULL,NULL,'2026-09-28 17:38:11',NULL),(29,43,1,1,'App\\Models\\WorkOrder',13,'Work order WO-011',6,-1,5,NULL,NULL,'2026-09-28 17:38:11',NULL),(30,47,1,1,'App\\Models\\WorkOrder',13,'Work order WO-011',18,-1,17,NULL,NULL,'2026-09-28 17:38:11',NULL),(31,47,1,1,'App\\Models\\WorkOrder',6,'sale',17,-4,13,NULL,NULL,'2026-09-28 18:35:55',NULL),(32,52,1,1,'App\\Models\\WorkOrder',6,'sale',200,-4,196,NULL,NULL,'2026-09-28 18:35:55',NULL),(33,53,1,1,'App\\Models\\WorkOrder',7,'sale',40,-4,36,NULL,NULL,'2026-09-28 18:35:56',NULL),(34,58,1,1,'App\\Models\\WorkOrder',5,'sale',60,-2,58,NULL,NULL,'2026-09-28 18:35:57',NULL),(35,43,1,1,'App\\Models\\WorkOrder',14,'sale',5,-1,4,NULL,NULL,'2026-09-28 18:40:15',NULL),(36,48,1,1,'App\\Models\\WorkOrder',14,'sale',5,-1,4,NULL,NULL,'2026-09-28 18:40:15',NULL),(37,43,1,1,'App\\Models\\WorkOrder',14,'sale',4,-1,3,NULL,NULL,'2026-09-28 18:40:15',NULL),(38,44,1,1,'App\\Models\\WorkOrder',14,'sale',5,-1,4,NULL,NULL,'2026-09-28 18:40:15',NULL),(39,44,1,1,'App\\Models\\WorkOrder',14,'sale',4,-1,3,NULL,NULL,'2026-09-28 18:40:15',NULL),(40,44,1,1,'App\\Models\\WorkOrder',14,'sale',3,-1,2,NULL,NULL,'2026-09-28 18:40:15',NULL),(41,59,1,1,'App\\Models\\WorkOrder',11,'sale',80,-1,79,NULL,NULL,'2026-09-28 18:40:16',NULL),(42,43,1,1,'App\\Models\\WorkOrder',14,'sale',3,-1,2,NULL,NULL,'2026-09-28 18:44:04',NULL),(43,48,1,1,'App\\Models\\WorkOrder',14,'sale',4,-1,3,NULL,NULL,'2026-09-28 18:44:04',NULL),(44,43,1,1,'App\\Models\\WorkOrder',14,'sale',2,-1,1,NULL,NULL,'2026-09-28 18:44:04',NULL),(45,44,1,1,'App\\Models\\WorkOrder',14,'sale',2,-1,1,NULL,NULL,'2026-09-28 18:44:04',NULL),(46,44,1,1,'App\\Models\\WorkOrder',14,'sale',1,-1,0,NULL,NULL,'2026-09-28 18:44:04',NULL),(47,44,1,1,'App\\Models\\WorkOrder',14,'sale',0,-1,-1,NULL,NULL,'2026-09-28 18:44:04',NULL),(48,43,1,1,'App\\Models\\Invoice',18,'sale',1,-1,0,NULL,NULL,'2026-09-28 18:44:31',NULL),(49,48,1,1,'App\\Models\\Invoice',18,'sale',3,-1,2,NULL,NULL,'2026-09-28 18:44:31',NULL),(50,43,1,1,'App\\Models\\Invoice',18,'sale',0,-1,-1,NULL,NULL,'2026-09-28 18:44:31',NULL),(51,44,1,1,'App\\Models\\Invoice',18,'sale',-1,-1,-2,NULL,NULL,'2026-09-28 18:44:31',NULL),(52,44,1,1,'App\\Models\\Invoice',18,'sale',-2,-1,-3,NULL,NULL,'2026-09-28 18:44:31',NULL),(53,44,1,1,'App\\Models\\Invoice',18,'sale',-3,-1,-4,NULL,NULL,'2026-09-28 18:44:31',NULL),(54,42,1,1,'App\\Models\\Invoice',19,'sale',24,-1,23,NULL,NULL,'2026-09-28 19:31:57',NULL),(55,45,1,1,'App\\Models\\WorkOrder',15,'sale',9,-1,8,NULL,NULL,'2026-09-28 19:38:07',NULL),(56,45,1,1,'App\\Models\\WorkOrder',15,'sale',8,-1,7,NULL,NULL,'2026-09-28 19:38:07',NULL),(57,45,1,1,'App\\Models\\WorkOrder',15,'sale',7,-1,6,NULL,NULL,'2026-09-28 19:38:07',NULL),(58,45,1,1,'App\\Models\\Invoice',20,'sale',6,-1,5,NULL,NULL,'2026-09-28 19:38:56',NULL),(59,45,1,1,'App\\Models\\Invoice',20,'sale',5,-1,4,NULL,NULL,'2026-09-28 19:38:56',NULL),(60,45,1,1,'App\\Models\\Invoice',20,'sale',4,-1,3,NULL,NULL,'2026-09-28 19:38:56',NULL),(61,42,1,1,'App\\Models\\Invoice',21,'sale',23,-1,22,NULL,NULL,'2026-09-28 19:40:43',NULL),(62,47,1,1,'App\\Models\\Invoice',21,'sale',13,-1,12,NULL,NULL,'2026-09-28 19:40:43',NULL),(63,45,1,1,'App\\Models\\Invoice',23,'sale',3,-1,2,NULL,NULL,'2026-10-03 02:40:34',NULL),(64,47,1,1,'App\\Models\\Invoice',23,'sale',12,-1,11,NULL,NULL,'2026-10-03 02:40:34',NULL),(65,47,1,1,'App\\Models\\Invoice',23,'sale',11,-1,10,NULL,NULL,'2026-10-03 02:40:34',NULL),(66,50,1,1,'App\\Models\\Invoice',23,'sale',16,-1,15,NULL,NULL,'2026-10-03 02:40:34',NULL),(67,51,1,1,'App\\Models\\Invoice',23,'sale',4,-1,3,NULL,NULL,'2026-10-03 02:40:34',NULL),(68,46,1,1,'App\\Models\\Invoice',23,'sale',9,-1,8,NULL,NULL,'2026-10-03 02:40:34',NULL),(69,42,1,1,'App\\Models\\Invoice',23,'sale',22,-1,21,NULL,NULL,'2026-10-03 02:40:34',NULL),(70,48,1,1,'App\\Models\\Invoice',24,'sale',2,-1,1,NULL,NULL,'2026-10-03 02:42:46',NULL),(71,45,1,1,'App\\Models\\Invoice',24,'sale',2,-1,1,NULL,NULL,'2026-10-03 02:42:46',NULL),(72,47,1,1,'App\\Models\\Invoice',24,'sale',10,-1,9,NULL,NULL,'2026-10-03 02:42:46',NULL),(73,49,1,1,'App\\Models\\Invoice',24,'sale',7,-1,6,NULL,NULL,'2026-10-03 02:42:46',NULL),(74,42,1,1,'App\\Models\\Invoice',24,'sale',21,-1,20,NULL,NULL,'2026-10-03 02:42:46',NULL),(75,50,1,1,'App\\Models\\Invoice',24,'sale',15,-1,14,NULL,NULL,'2026-10-03 02:42:46',NULL),(76,51,1,1,'App\\Models\\Invoice',24,'sale',3,-1,2,NULL,NULL,'2026-10-03 02:42:46',NULL),(77,43,1,1,'App\\Models\\Invoice',24,'sale',-1,-1,-2,NULL,NULL,'2026-10-03 02:42:46',NULL),(78,46,1,1,'App\\Models\\Invoice',24,'sale',8,-1,7,NULL,NULL,'2026-10-03 02:42:46',NULL),(79,42,1,1,'App\\Models\\Invoice',24,'sale',20,-1,19,NULL,NULL,'2026-10-03 02:42:46',NULL),(80,45,1,1,NULL,NULL,'correction',1,4,5,NULL,'COMPRA','2026-10-03 20:44:42',NULL);
/*!40000 ALTER TABLE `product_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tire',
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unit',
  `tire_brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tire_line` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tire_width` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tire_ratio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tire_diameter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tire_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tire_size_normalized` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sale_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_qty` int NOT NULL DEFAULT '0',
  `stock_min` int NOT NULL DEFAULT '0',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_branch_id_foreign` (`branch_id`),
  KEY `products_supplier_id_foreign` (`supplier_id`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_type_index` (`type`),
  KEY `products_tire_brand_index` (`tire_brand`),
  KEY `products_tire_width_tire_ratio_tire_diameter_index` (`tire_width`,`tire_ratio`,`tire_diameter`),
  KEY `products_tire_size_normalized_index` (`tire_size_normalized`),
  CONSTRAINT `products_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (42,1,11,1,'tire','GT-21555R17',NULL,'GT Radial Champiro VP1 215/55R17',NULL,'unit','GT Radial','Champiro VP1','215','55','17','all_season','21555R17',48.00,89.99,19,6,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-10-03 02:42:46',NULL),(43,1,11,1,'tire','MI-20555R16',NULL,'Michelin Defender 205/55R16',NULL,'unit','Michelin','Defender','205','55','16','all_season','20555R16',92.00,165.00,-2,4,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-10-03 02:42:46',NULL),(44,1,12,1,'tire','GY-23560R17',NULL,'Goodyear Assurance WS 235/60R17',NULL,'unit','Goodyear','Assurance WS','235','60','17','winter','23560R17',74.00,139.99,-4,4,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-09-28 18:44:31',NULL),(45,1,12,1,'tire','FI-24565R17',NULL,'Firestone Destination LE3 245/65R17',NULL,'unit','Firestone','Destination LE3','245','65','17','all_season','24565R17',68.00,119.99,5,4,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-10-03 20:44:42',NULL),(46,1,13,1,'tire','CO-22560R17',NULL,'Cooper Discoverer AT3 225/60R17',NULL,'unit','Cooper','Discoverer AT3','225','60','17','all_terrain','22560R17',82.00,149.99,7,4,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-10-03 02:42:46',NULL),(47,1,13,1,'tire','HK-19560R15',NULL,'Hankook Kinergy GT 195/60R15',NULL,'unit','Hankook','Kinergy GT','195','60','15','all_season','19560R15',42.00,79.99,9,6,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-10-03 02:42:46',NULL),(48,1,11,1,'tire','TO-26570R17',NULL,'Toyo Open Country AT3 265/70R17',NULL,'unit','Toyo','Open Country AT3','265','70','17','all_terrain','26570R17',95.00,179.99,1,4,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-10-03 02:42:46',NULL),(49,1,12,1,'tire','BR-22565R17',NULL,'Bridgestone Dueler H/L 225/65R17',NULL,'unit','Bridgestone','Dueler H/L','225','65','17','all_season','22565R17',78.00,145.00,6,4,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-10-03 02:42:46',NULL),(50,1,13,1,'tire','CO-18565R15',NULL,'Continental TrueContact 185/65R15',NULL,'unit','Continental','TrueContact','185','65','15','all_season','18565R15',55.00,99.99,14,6,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-10-03 02:42:46',NULL),(51,1,11,1,'tire','PI-24545R18',NULL,'Pirelli Cinturato P7 245/45R18',NULL,'unit','Pirelli','Cinturato P7','245','45','18','summer','24545R18',105.00,199.99,2,2,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-10-03 02:42:46',NULL),(52,1,15,1,'material','VAL-STD',NULL,'Standard Valve Stem',NULL,'unit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.75,3.50,196,50,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-09-28 18:35:55',NULL),(53,1,15,1,'material','VAL-TPMS',NULL,'TPMS Valve Stem Replacement',NULL,'unit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,8.50,24.99,36,10,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-09-28 18:35:56',NULL),(54,1,14,1,'material','WGT-FE-1oz',NULL,'Fe Clip-on Wheel Weight 1oz',NULL,'unit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.10,0.75,500,100,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(55,1,14,1,'material','WGT-ST-1oz',NULL,'Stick-on Wheel Weight 1oz',NULL,'unit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.12,0.80,400,100,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(56,1,14,1,'material','TLUB-16oz',NULL,'Tire Mounting Lube 16oz',NULL,'unit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4.20,12.99,30,5,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(57,1,15,1,'material','BEAD-SEAL',NULL,'Bead Sealer 8oz',NULL,'unit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,3.50,9.99,25,5,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(58,1,15,1,'material','PLUG-KIT',NULL,'Tire Plug Repair Kit',NULL,'unit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1.20,4.99,58,15,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-09-28 18:35:57',NULL),(59,1,15,1,'material','PATCH-4IN',NULL,'Radial Tire Patch 4\"',NULL,'unit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.90,3.99,79,20,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-09-28 18:40:16',NULL),(60,1,14,1,'material','NITRO-FIL',NULL,'Nitrogen Inflation (per tire)',NULL,'unit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.50,5.00,999,10,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(61,1,15,1,'material','LUG-NUT-5',NULL,'Lug Nut Set (5-pack)',NULL,'unit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,4.50,14.99,35,10,NULL,NULL,1,0,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `qty_ordered` int NOT NULL,
  `qty_received` int NOT NULL DEFAULT '0',
  `unit_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_product_id_foreign` (`product_id`),
  KEY `purchase_order_items_purchase_order_id_product_id_index` (`purchase_order_id`,`product_id`),
  CONSTRAINT `purchase_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_items`
--

LOCK TABLES `purchase_order_items` WRITE;
/*!40000 ALTER TABLE `purchase_order_items` DISABLE KEYS */;
INSERT INTO `purchase_order_items` VALUES (25,9,42,12,12,48.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(26,9,43,8,8,92.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(27,9,48,6,6,95.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(28,10,44,8,8,74.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(29,10,45,10,10,68.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(30,10,49,8,8,78.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(31,11,52,100,100,0.75,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(32,11,53,20,20,8.50,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(33,11,58,30,30,1.20,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(34,11,59,40,40,0.90,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(35,12,46,8,0,82.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(36,12,47,12,0,42.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(37,12,51,4,0,105.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44');
/*!40000 ALTER TABLE `purchase_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `received_by` bigint unsigned DEFAULT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'regular',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordered_at` timestamp NULL DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_number_unique` (`number`),
  KEY `purchase_orders_branch_id_foreign` (`branch_id`),
  KEY `purchase_orders_created_by_foreign` (`created_by`),
  KEY `purchase_orders_received_by_foreign` (`received_by`),
  KEY `purchase_orders_status_index` (`status`),
  KEY `purchase_orders_supplier_id_index` (`supplier_id`),
  CONSTRAINT `purchase_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_orders_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
INSERT INTO `purchase_orders` VALUES (9,1,11,1,NULL,'PO-001','received','regular',NULL,NULL,'2026-08-27 00:20:44','2026-09-01 00:20:44','2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(10,1,12,1,NULL,'PO-002','received','regular',NULL,NULL,'2026-09-06 00:20:44','2026-09-10 00:20:44','2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(11,1,15,1,NULL,'PO-003','received','regular',NULL,NULL,'2026-09-16 00:20:44','2026-09-19 00:20:44','2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(12,1,11,1,NULL,'PO-004','sent','regular',NULL,NULL,'2026-09-24 00:20:44',NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL);
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(1,2),(2,2),(4,2),(6,2),(8,2),(10,2),(12,2),(14,2),(16,2),(18,2),(21,2),(1,3),(2,3),(3,3),(4,3),(5,3),(7,3),(9,3),(12,3),(13,3),(14,3),(15,3),(16,3),(1,4),(12,4),(13,4),(14,4);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(2,'owner','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(3,'secretary','web','2026-09-25 18:10:57','2026-09-25 18:10:57'),(4,'mechanic','web','2026-09-25 18:10:57','2026-09-25 18:10:57');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_categories`
--

DROP TABLE IF EXISTS `service_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#2563eb',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_categories`
--

LOCK TABLES `service_categories` WRITE;
/*!40000 ALTER TABLE `service_categories` DISABLE KEYS */;
INSERT INTO `service_categories` VALUES (1,'Tire Services','#2563eb',1,1,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(2,'Wheel Services','#7c3aed',1,2,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(3,'Balancing','#059669',1,3,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(4,'Alignment','#d97706',1,4,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(5,'TPMS Services','#dc2626',1,5,'2026-09-25 20:25:18','2026-09-25 20:25:18'),(6,'Other Services','#64748b',1,6,'2026-09-25 20:25:18','2026-09-25 20:25:18');
/*!40000 ALTER TABLE `service_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_consumables`
--

DROP TABLE IF EXISTS `service_consumables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_consumables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `qty_default` decimal(8,3) NOT NULL DEFAULT '1.000',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_consumables_service_id_product_id_unique` (`service_id`,`product_id`),
  KEY `service_consumables_product_id_foreign` (`product_id`),
  CONSTRAINT `service_consumables_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_consumables_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_consumables`
--

LOCK TABLES `service_consumables` WRITE;
/*!40000 ALTER TABLE `service_consumables` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_consumables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_price_rules`
--

DROP TABLE IF EXISTS `service_price_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_price_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint unsigned NOT NULL,
  `vehicle_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_price_rules_service_id_vehicle_type_unique` (`service_id`,`vehicle_type`),
  CONSTRAINT `service_price_rules_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_price_rules`
--

LOCK TABLES `service_price_rules` WRITE;
/*!40000 ALTER TABLE `service_price_rules` DISABLE KEYS */;
INSERT INTO `service_price_rules` VALUES (81,25,'sedan',12.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(82,25,'suv',14.40,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(83,25,'pickup',15.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(84,25,'van',15.60,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(85,25,'truck',19.20,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(86,28,'sedan',20.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(87,28,'suv',24.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(88,28,'pickup',25.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(89,28,'van',26.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(90,28,'truck',32.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(91,30,'sedan',40.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(92,30,'suv',48.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(93,30,'pickup',50.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(94,30,'van',52.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(95,30,'truck',64.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(96,31,'sedan',25.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(97,31,'suv',30.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(98,31,'pickup',31.25,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(99,31,'van',32.50,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(100,31,'truck',40.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(101,32,'sedan',14.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(102,32,'suv',16.80,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(103,32,'pickup',17.50,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(104,32,'van',18.20,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(105,32,'truck',22.40,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(106,33,'sedan',50.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(107,33,'suv',60.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(108,33,'pickup',62.50,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(109,33,'van',65.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(110,33,'truck',80.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(111,35,'sedan',65.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(112,35,'suv',78.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(113,35,'pickup',81.25,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(114,35,'van',84.50,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(115,35,'truck',104.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(116,36,'sedan',95.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(117,36,'suv',114.00,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(118,36,'pickup',118.75,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(119,36,'van',123.50,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(120,36,'truck',152.00,'2026-09-26 00:20:44','2026-09-26 00:20:44');
/*!40000 ALTER TABLE `service_price_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `pricing_type` enum('fixed','by_vehicle_type','variable') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `base_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estimated_minutes` smallint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `services_branch_id_foreign` (`branch_id`),
  KEY `services_category_id_foreign` (`category_id`),
  KEY `services_name_index` (`name`),
  CONSTRAINT `services_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `services_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (25,1,1,'Mount & Dismount (each)',NULL,'by_vehicle_type',12.00,0.00,20,1,0,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(26,1,1,'Tire Repair – Plug',NULL,'fixed',15.00,0.00,20,1,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(27,1,1,'Tire Repair – Patch',NULL,'fixed',25.00,0.00,35,1,2,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(28,1,1,'Tire Rotation (set of 4)',NULL,'by_vehicle_type',20.00,0.00,30,1,3,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(29,1,1,'Flat Fix (on vehicle)',NULL,'fixed',30.00,0.00,25,1,4,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(30,1,2,'Wheel Swap (set of 4)',NULL,'by_vehicle_type',40.00,0.00,45,1,5,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(31,1,2,'Run-flat Tire Mount (each)',NULL,'by_vehicle_type',25.00,0.00,30,1,6,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(32,1,3,'Wheel Balance (each)',NULL,'by_vehicle_type',14.00,0.00,15,1,7,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(33,1,3,'Wheel Balance (set of 4)',NULL,'by_vehicle_type',50.00,0.00,45,1,8,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(34,1,3,'Road Force Balance (each)',NULL,'fixed',22.00,0.00,20,1,9,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(35,1,4,'Front Alignment',NULL,'by_vehicle_type',65.00,0.00,60,1,10,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(36,1,4,'4-Wheel Alignment',NULL,'by_vehicle_type',95.00,0.00,90,1,11,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(37,NULL,6,'mecanica',NULL,'fixed',0.00,0.00,NULL,1,0,'2026-09-26 03:12:38','2026-09-26 03:12:38',NULL);
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('aXFtr6zZug2oKW4Qk3L6BClOYqgc66jxpdLrJGMy',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJnUkFGc1U3MHpZcDBTem1uZ1B0YkF1VDR0UjJuSG5jV0NVY0NDVlg0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9wcm9kdWN0c1wvNDUiLCJyb3V0ZSI6InByb2R1Y3RzLnNob3cifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwicGFzc3dvcmRfaGFzaF93ZWIiOiIwNDllZjMwM2Y2MzljMDIwNjgwOGY1MDgyODY2YWQwNWYyMzVhMzQ3NzgxYzQwMTliOWJkYjAzZDVkNzIzMmY2In0=',1791047934),('NJrp4qRVcA0qirVmraCOKOL3t4n1LmCsTlAohsuZ',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJPdVpQZDJwdTJXSG1rbXZkMFBoZmRQMHBFTkNBc2FrdnNya1ROV2FiIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL3dvcmstb3JkZXJzXC8xNiJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1790999877),('ohMOhrmu7nYQWsXwosxvQAv9frMB1ihlUnYguBzo',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiIwZGp4djRVcG9EZW80aGd1bnhPb1gzcHNLeUFqVlduUFBPN0EyOUxzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC93b3JrLW9yZGVyc1wvMTYiLCJyb3V0ZSI6Indvcmstb3JkZXJzLnNob3cifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwicGFzc3dvcmRfaGFzaF93ZWIiOiIwNDllZjMwM2Y2MzljMDIwNjgwOGY1MDgyODY2YWQwNWYyMzVhMzQ3NzgxYzQwMTliOWJkYjAzZDVkNzIzMmY2In0=',1790987055),('sLPV5flFlqSE8DSdHszrCuxLlfSiATm0VMSJ0m7B',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/27.0 Safari/605.1.15','eyJfdG9rZW4iOiJZZlFDUjBFeEU3Z253bzRqd3lrMHJ1RnBaTFEwcjVCUFBnRmIwYmdMIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2Rhc2hib2FyZCJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1790980494);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_branch_id_key_unique` (`branch_id`,`key`),
  CONSTRAINT `settings_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (24,NULL,'business_name','Dundalk Used & New Tires','string','2026-09-26 00:20:43','2026-09-26 00:20:43'),(25,NULL,'business_address','7888 Dundalk Ave, Dundalk, MD 21222','string','2026-09-26 00:20:43','2026-09-28 19:49:38'),(26,NULL,'business_phone','667-727-3115','string','2026-09-26 00:20:43','2026-09-28 19:49:38'),(27,NULL,'business_email','info@dundalktiresmd.com','string','2026-09-26 00:20:43','2026-09-26 00:20:43'),(28,NULL,'invoice_prefix','INV','string','2026-09-26 00:20:43','2026-09-26 00:20:43'),(29,NULL,'purchase_prefix','PO','string','2026-09-26 00:20:43','2026-09-26 00:20:43'),(30,NULL,'work_order_prefix','WO','string','2026-09-26 00:20:43','2026-09-26 00:20:43'),(31,NULL,'tax_rate','0','decimal','2026-09-26 00:20:43','2026-09-26 02:52:41'),(32,NULL,'currency_symbol','$','string','2026-09-26 00:20:43','2026-09-26 00:20:43'),(33,NULL,'low_stock_alert','5','integer','2026-09-26 00:20:43','2026-09-26 00:20:43'),(34,NULL,'tax_enabled','false','boolean','2026-09-26 02:52:41','2026-09-26 02:52:41'),(35,NULL,'tax_label','Sales Tax','string','2026-09-26 02:52:41','2026-09-26 02:52:41'),(36,NULL,'allow_manual_discounts','true','boolean','2026-09-26 02:52:41','2026-09-26 02:52:41');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suppliers_branch_id_foreign` (`branch_id`),
  CONSTRAINT `suppliers_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (11,1,'American Tire Distributors','Tom Bradley','(704) 992-2000','tbradley@atd.com','12200 Herbert Wayne Ct, Huntersville, NC',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(12,1,'Priority Tire Wholesale','Lisa Chen','(855) 977-7473','lchen@prioritytire.com','800 Commerce Blvd, Carlstadt, NJ',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(13,1,'NTW National Tire Wholesale','Bob Garrett','(866) 689-6089','bgarrett@ntw.com','2605 Enterprise Rd E, Clearwater, FL',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(14,1,'Harbor Freight Tools','Mike Saenz','(800) 444-3353','mike@harborfreight.com','26541 Agoura Rd, Calabasas, CA',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(15,1,'O\'Reilly Auto Parts','Pam Richards','(417) 862-6708','prichards@oreilly.com','233 S Patterson Ave, Springfield, MO',NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL);
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimonials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `author_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint NOT NULL DEFAULT '5',
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reviewed_at` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonials`
--

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
INSERT INTO `testimonials` VALUES (1,'Maria G.',5,'Best tire shop in Dundalk! Got all 4 tires replaced in under an hour. The prices are unbeatable and the staff is super friendly and professional. They even noticed my brakes were wearing and let me know without trying to upsell me. Will definitely be back!','2026-09-15',1,0,'2026-09-29 20:16:05','2026-09-29 20:16:05');
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tire_disposal_pickup_items`
--

DROP TABLE IF EXISTS `tire_disposal_pickup_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tire_disposal_pickup_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pickup_id` bigint unsigned NOT NULL,
  `tire_disposal_id` bigint unsigned NOT NULL,
  `qty_collected` smallint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tire_disposal_pickup_items_pickup_id_tire_disposal_id_unique` (`pickup_id`,`tire_disposal_id`),
  KEY `tire_disposal_pickup_items_tire_disposal_id_foreign` (`tire_disposal_id`),
  CONSTRAINT `tire_disposal_pickup_items_pickup_id_foreign` FOREIGN KEY (`pickup_id`) REFERENCES `tire_disposal_pickups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tire_disposal_pickup_items_tire_disposal_id_foreign` FOREIGN KEY (`tire_disposal_id`) REFERENCES `tire_disposals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tire_disposal_pickup_items`
--

LOCK TABLES `tire_disposal_pickup_items` WRITE;
/*!40000 ALTER TABLE `tire_disposal_pickup_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `tire_disposal_pickup_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tire_disposal_pickups`
--

DROP TABLE IF EXISTS `tire_disposal_pickups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tire_disposal_pickups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `expense_id` bigint unsigned DEFAULT NULL,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_date` date NOT NULL,
  `qty_collected` smallint unsigned NOT NULL,
  `cost_per_unit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('cash','card','zelle','cashapp','transfer','check','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `evidence` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tire_disposal_pickups_created_by_foreign` (`created_by`),
  KEY `tire_disposal_pickups_expense_id_foreign` (`expense_id`),
  KEY `tire_disposal_pickups_branch_id_pickup_date_index` (`branch_id`,`pickup_date`),
  CONSTRAINT `tire_disposal_pickups_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tire_disposal_pickups_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tire_disposal_pickups_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tire_disposal_pickups`
--

LOCK TABLES `tire_disposal_pickups` WRITE;
/*!40000 ALTER TABLE `tire_disposal_pickups` DISABLE KEYS */;
/*!40000 ALTER TABLE `tire_disposal_pickups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tire_disposals`
--

DROP TABLE IF EXISTS `tire_disposals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tire_disposals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT NULL,
  `work_order_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `qty_total` smallint unsigned NOT NULL,
  `qty_client_kept` smallint unsigned NOT NULL DEFAULT '0',
  `qty_for_disposal` smallint unsigned NOT NULL,
  `cost_per_unit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','partial','collected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tire_disposals_created_by_foreign` (`created_by`),
  KEY `tire_disposals_branch_id_status_index` (`branch_id`,`status`),
  KEY `tire_disposals_work_order_id_index` (`work_order_id`),
  CONSTRAINT `tire_disposals_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tire_disposals_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tire_disposals_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tire_disposals`
--

LOCK TABLES `tire_disposals` WRITE;
/*!40000 ALTER TABLE `tire_disposals` DISABLE KEYS */;
INSERT INTO `tire_disposals` VALUES (1,1,13,1,4,0,4,1.00,'pending',NULL,'2026-09-28 17:35:35','2026-09-28 17:35:35'),(2,1,16,1,4,0,4,1.00,'pending',NULL,'2026-10-03 02:45:17','2026-10-03 02:45:17'),(4,1,3,1,4,0,4,2.50,'pending','Test','2026-10-03 02:52:05','2026-10-03 02:52:05'),(7,1,16,1,2,0,2,1.00,'pending',NULL,'2026-10-03 20:13:01','2026-10-03 20:13:01');
/*!40000 ALTER TABLE `tire_disposals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_branch_id_foreign` (`branch_id`),
  CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'Administrador','admin@dundalk.com',NULL,'$2y$12$ombGqjP6e/YgQllFxlgXI.ecV8HmQE/u4T6sTW23up0rWdGIJaK3G',1,NULL,'2026-09-25 18:10:58','2026-10-03 02:35:57'),(10,1,'Marcus Johnson','marcus@dundalk.com','2026-09-26 00:20:43','$2y$12$XlUKTf11cbC2ioqKyqP7XOdXgimDcETu6L73Kpuzd22re4jHuQVuy',1,NULL,'2026-09-26 00:20:43','2026-09-26 00:20:43'),(11,1,'Derek Williams','derek@dundalk.com','2026-09-26 00:20:43','$2y$12$T2ok4.ecrGxCsp/8N2mgZOjROuJCV2a0ERY93Jx84gh2.aKDjRVzK',1,NULL,'2026-09-26 00:20:43','2026-09-26 00:20:43'),(12,1,'Sandra Torres','sandra@dundalk.com','2026-09-26 00:20:44','$2y$12$1WbJP1OTzOsMLcpF8u1Qq.MneXzKnQnijPsz/lE380IAfoJEgZpde',1,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(13,1,'Kevin Park','kevin@dundalk.com','2026-09-26 00:20:44','$2y$12$eksHogAQ5IiR4wJ5M5SajOfWWROOmmJHTA6bLmOnfEgrRB5SOMGWO',1,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `make` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` year NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vin` varchar(17) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sedan',
  `tire_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mileage` int unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicles_customer_id_foreign` (`customer_id`),
  KEY `vehicles_branch_id_foreign` (`branch_id`),
  KEY `vehicles_plate_index` (`plate`),
  KEY `vehicles_make_model_index` (`make`,`model`),
  CONSTRAINT `vehicles_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vehicles_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES (24,22,1,'Toyota','Camry',2019,'Silver','8AB1234',NULL,'sedan','215/55R17',NULL,NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(25,22,1,'Honda','Pilot',2017,'Black','8CD5678',NULL,'suv','235/60R17',NULL,NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(26,23,1,'Chevrolet','Traverse',2020,'White','9MG3412',NULL,'suv','245/65R17',NULL,NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(27,24,1,'Ford','F-150',2021,'Red','5RK7721',NULL,'pickup','265/70R17',NULL,NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(28,25,1,'Nissan','Altima',2018,'Blue','3TB9901',NULL,'sedan','215/55R17',NULL,NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(29,26,1,'Hyundai','Tucson',2022,'Gray','7AN4455',NULL,'suv','225/60R17',NULL,NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(30,27,1,'Chrysler','Pacifica',2019,'White','2PD6683',NULL,'van','235/65R17',NULL,NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(31,28,1,'Ford','Explorer',2020,'Gray','6CR2211',NULL,'suv','255/55R18',NULL,NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(32,29,1,'Kia','Sorento',2021,'Black','4SM8890',NULL,'suv','235/65R17',NULL,NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(33,30,1,'Dodge','Ram 1500',2018,'Black','8WT3360',NULL,'pickup','265/70R17',NULL,NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(34,31,1,'Honda','CR-V',2020,'Pearl','1LP5522',NULL,'suv','225/65R17',NULL,NULL,1,'2026-09-26 00:20:44','2026-09-26 00:20:44',NULL),(35,32,NULL,'Toyota Corolla','G19',2019,NULL,'35435',NULL,'suv','215/22R18',46000,NULL,1,'2026-09-27 22:12:03','2026-09-27 22:12:03',NULL),(36,33,NULL,'Honda','CR-V',2020,'Gris',NULL,NULL,'suv','225/65R17',NULL,NULL,1,'2026-09-28 20:05:42','2026-09-28 20:05:42',NULL);
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_leads`
--

DROP TABLE IF EXISTS `web_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `web_leads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tire_size` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `contacted_at` timestamp NULL DEFAULT NULL,
  `staff_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `web_leads`
--

LOCK TABLES `web_leads` WRITE;
/*!40000 ALTER TABLE `web_leads` DISABLE KEYS */;
INSERT INTO `web_leads` VALUES (1,'Test Customer','667-555-0100','Wheel Alignment','2022 Toyota Camry','215/55R17','Test from landing page','new',NULL,NULL,'2026-09-29 18:22:08','2026-09-29 18:22:08');
/*!40000 ALTER TABLE `web_leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_consumables`
--

DROP TABLE IF EXISTS `work_order_consumables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_consumables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `work_order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unit',
  `qty_used` decimal(8,3) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_order_consumables_product_id_foreign` (`product_id`),
  KEY `work_order_consumables_recorded_by_foreign` (`recorded_by`),
  KEY `work_order_consumables_work_order_id_index` (`work_order_id`),
  CONSTRAINT `work_order_consumables_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_order_consumables_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_order_consumables_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_consumables`
--

LOCK TABLES `work_order_consumables` WRITE;
/*!40000 ALTER TABLE `work_order_consumables` DISABLE KEYS */;
INSERT INTO `work_order_consumables` VALUES (1,11,NULL,'Aceite lubricante','oz',2.000,1.50,3.00,1,NULL,'2026-09-27 21:36:52','2026-09-27 21:36:52'),(2,13,NULL,'plug','it',1.000,2.00,2.00,1,NULL,'2026-09-28 17:35:12','2026-09-28 17:35:12'),(3,16,NULL,'Pincho','1',2.000,20.00,40.00,1,NULL,'2026-10-03 02:44:53','2026-10-03 02:44:53');
/*!40000 ALTER TABLE `work_order_consumables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_products`
--

DROP TABLE IF EXISTS `work_order_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `work_order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` smallint unsigned NOT NULL DEFAULT '1',
  `unit_price` decimal(10,2) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_order_products_work_order_id_foreign` (`work_order_id`),
  KEY `work_order_products_product_id_foreign` (`product_id`),
  CONSTRAINT `work_order_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_order_products_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_products`
--

LOCK TABLES `work_order_products` WRITE;
/*!40000 ALTER TABLE `work_order_products` DISABLE KEYS */;
INSERT INTO `work_order_products` VALUES (5,3,42,'GT Radial Champiro VP1 215/55R17',4,89.99,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(6,3,52,'Standard Valve Stem',4,3.50,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(7,4,45,'Firestone Destination LE3 245/65R17',4,119.99,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(8,4,52,'Standard Valve Stem',4,3.50,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(9,5,58,'Tire Plug Repair Kit',2,4.99,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(10,6,47,'Hankook Kinergy GT 195/60R15',4,79.99,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(11,6,52,'Standard Valve Stem',4,3.50,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(12,7,53,'TPMS Valve Stem Replacement',4,24.99,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(13,9,46,'Cooper Discoverer AT3 225/60R17',4,149.99,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(14,9,52,'Standard Valve Stem',4,3.50,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(15,10,44,'Goodyear Assurance WS 235/60R17',2,139.99,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(16,11,59,'Radial Tire Patch 4\"',1,3.99,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(17,13,43,'Michelin Defender 205/55R16',1,165.00,92.00,NULL,'2026-09-28 17:34:10','2026-09-28 17:34:10'),(18,13,43,'Michelin Defender 205/55R16',1,165.00,92.00,NULL,'2026-09-28 17:34:10','2026-09-28 17:34:10'),(19,13,43,'Michelin Defender 205/55R16',1,165.00,92.00,NULL,'2026-09-28 17:34:10','2026-09-28 17:34:10'),(20,13,43,'Michelin Defender 205/55R16',1,165.00,92.00,NULL,'2026-09-28 17:34:10','2026-09-28 17:34:10'),(21,13,47,'Hankook Kinergy GT 195/60R15',1,79.99,42.00,NULL,'2026-09-28 17:34:10','2026-09-28 17:34:10'),(22,14,43,'Michelin Defender 205/55R16',1,165.00,92.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(23,14,48,'Toyo Open Country AT3 265/70R17',1,179.99,95.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(24,14,43,'Michelin Defender 205/55R16',1,165.00,92.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(25,14,44,'Goodyear Assurance WS 235/60R17',1,139.99,74.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(26,14,44,'Goodyear Assurance WS 235/60R17',1,139.99,74.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(27,14,44,'Goodyear Assurance WS 235/60R17',1,139.99,74.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(28,15,45,'Firestone Destination LE3 245/65R17',1,119.99,68.00,NULL,'2026-09-28 19:37:42','2026-09-28 19:37:42'),(29,15,45,'Firestone Destination LE3 245/65R17',1,119.99,68.00,NULL,'2026-09-28 19:37:42','2026-09-28 19:37:42'),(30,15,45,'Firestone Destination LE3 245/65R17',1,119.99,68.00,NULL,'2026-09-28 19:37:42','2026-09-28 19:37:42'),(31,16,45,'Firestone Destination LE3 245/65R17',1,119.99,68.00,NULL,'2026-10-03 02:44:18','2026-10-03 02:44:18'),(32,16,47,'Hankook Kinergy GT 195/60R15',1,79.99,42.00,NULL,'2026-10-03 02:44:18','2026-10-03 02:44:18'),(33,16,48,'Toyo Open Country AT3 265/70R17',1,179.99,95.00,NULL,'2026-10-03 02:44:18','2026-10-03 02:44:18'),(34,16,45,'Firestone Destination LE3 245/65R17',1,119.99,68.00,NULL,'2026-10-03 02:44:18','2026-10-03 02:44:18'),(35,16,46,'Cooper Discoverer AT3 225/60R17',1,149.99,82.00,NULL,'2026-10-03 02:44:18','2026-10-03 02:44:18'),(36,16,48,'Toyo Open Country AT3 265/70R17',1,179.99,95.00,NULL,'2026-10-03 02:44:18','2026-10-03 02:44:18'),(37,16,43,'Michelin Defender 205/55R16',1,165.00,92.00,NULL,'2026-10-03 02:44:18','2026-10-03 02:44:18');
/*!40000 ALTER TABLE `work_order_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_services`
--

DROP TABLE IF EXISTS `work_order_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `work_order_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` smallint unsigned NOT NULL DEFAULT '1',
  `unit_price` decimal(10,2) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_order_services_work_order_id_foreign` (`work_order_id`),
  KEY `work_order_services_service_id_foreign` (`service_id`),
  CONSTRAINT `work_order_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_order_services_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_services`
--

LOCK TABLES `work_order_services` WRITE;
/*!40000 ALTER TABLE `work_order_services` DISABLE KEYS */;
INSERT INTO `work_order_services` VALUES (7,3,25,'Mount & Dismount (each)',4,12.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(8,3,33,'Wheel Balance (set of 4)',1,50.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(9,3,NULL,'Tire Disposal Fee (each)',4,4.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(10,4,25,'Mount & Dismount (each)',4,15.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(11,4,36,'4-Wheel Alignment',1,95.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(12,4,33,'Wheel Balance (set of 4)',1,60.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(13,5,26,'Tire Repair – Plug',2,15.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(14,6,25,'Mount & Dismount (each)',4,12.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(15,6,33,'Wheel Balance (set of 4)',1,50.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(16,6,NULL,'Tire Disposal Fee (each)',4,4.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(17,7,NULL,'TPMS Sensor Replace (each)',4,55.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(18,7,NULL,'TPMS Sensor Reset',1,20.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(19,8,28,'Tire Rotation (set of 4)',1,26.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(20,8,35,'Front Alignment',1,80.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(21,9,25,'Mount & Dismount (each)',4,15.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(22,9,33,'Wheel Balance (set of 4)',1,60.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(23,9,NULL,'Nitrogen Fill (set of 4)',1,20.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(24,9,NULL,'Tire Disposal Fee (each)',4,4.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(25,10,25,'Mount & Dismount (each)',4,15.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(26,10,36,'4-Wheel Alignment',1,115.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(27,11,27,'Tire Repair – Patch',1,25.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(28,12,NULL,'TPMS Sensor Reset',1,20.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(29,12,NULL,'Lug Nut Torque Check',1,10.00,0.00,NULL,'2026-09-26 00:20:44','2026-09-26 00:20:44'),(30,13,27,'Tire Repair – Patch',1,25.00,0.00,NULL,'2026-09-28 17:34:10','2026-09-28 17:34:10'),(31,13,26,'Tire Repair – Plug',1,15.00,0.00,NULL,'2026-09-28 17:34:10','2026-09-28 17:34:10'),(32,13,31,'Run-flat Tire Mount (each)',1,25.00,0.00,NULL,'2026-09-28 17:34:10','2026-09-28 17:34:10'),(33,14,28,'Tire Rotation (set of 4)',19,20.00,0.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(34,14,34,'Road Force Balance (each)',10,22.00,0.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(35,14,26,'Tire Repair – Plug',1,15.00,0.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(36,14,25,'Mount & Dismount (each)',1,12.00,0.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(37,14,25,'Mount & Dismount (each)',1,12.00,0.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(38,14,25,'Mount & Dismount (each)',1,12.00,0.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(39,14,35,'Front Alignment',1,65.00,0.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(40,14,25,'Mount & Dismount (each)',1,12.00,0.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(41,14,35,'Front Alignment',1,65.00,0.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(42,14,30,'Wheel Swap (set of 4)',1,40.00,0.00,NULL,'2026-09-28 18:35:11','2026-09-28 18:35:11'),(43,15,28,'Tire Rotation (set of 4)',1,20.00,0.00,NULL,'2026-09-28 19:37:42','2026-09-28 19:37:42'),(44,15,26,'Tire Repair – Plug',1,15.00,0.00,NULL,'2026-09-28 19:37:42','2026-09-28 19:37:42'),(45,16,25,'Mount & Dismount (each)',1,12.00,0.00,NULL,'2026-10-03 02:44:18','2026-10-03 02:44:18'),(46,16,30,'Wheel Swap (set of 4)',1,40.00,0.00,NULL,'2026-10-03 02:44:18','2026-10-03 02:44:18'),(47,16,28,'Tire Rotation (set of 4)',1,20.00,0.00,NULL,'2026-10-03 02:44:18','2026-10-03 02:44:18'),(48,16,33,'Wheel Balance (set of 4)',1,50.00,0.00,NULL,'2026-10-03 02:44:18','2026-10-03 02:44:18');
/*!40000 ALTER TABLE `work_order_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_orders`
--

DROP TABLE IF EXISTS `work_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `vehicle_id` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `internal_notes` text COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cost_products` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cost_services` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cost_consumables` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cost_disposal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `margin_amount` decimal(10,2) DEFAULT NULL,
  `margin_pct` decimal(5,2) DEFAULT NULL,
  `invoiced` tinyint(1) NOT NULL DEFAULT '0',
  `promised_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `work_orders_number_unique` (`number`),
  KEY `work_orders_branch_id_foreign` (`branch_id`),
  KEY `work_orders_customer_id_foreign` (`customer_id`),
  KEY `work_orders_vehicle_id_foreign` (`vehicle_id`),
  KEY `work_orders_assigned_to_foreign` (`assigned_to`),
  KEY `work_orders_created_by_foreign` (`created_by`),
  KEY `work_orders_status_index` (`status`),
  KEY `work_orders_number_index` (`number`),
  CONSTRAINT `work_orders_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_orders_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_orders`
--

LOCK TABLES `work_orders` WRITE;
/*!40000 ALTER TABLE `work_orders` DISABLE KEYS */;
INSERT INTO `work_orders` VALUES (3,1,22,24,10,1,'WO-001','completed',NULL,NULL,487.96,0.00,487.96,0.00,0.00,0.00,10.00,477.96,97.95,1,'2026-08-31 22:20:44','2026-08-31 19:20:44','2026-08-31 22:20:44','2026-08-31 18:20:44','2026-10-03 02:52:05',NULL),(4,1,23,26,11,1,'WO-002','cancelled',NULL,NULL,708.96,0.00,708.96,0.00,0.00,0.00,0.00,NULL,NULL,1,'2026-09-05 22:20:44','2026-09-05 19:20:44','2026-09-05 23:20:44','2026-09-05 18:20:44','2026-09-28 18:36:03',NULL),(5,1,24,27,10,1,'WO-003','completed',NULL,NULL,39.98,0.00,39.98,0.00,0.00,0.00,0.00,NULL,NULL,1,'2026-09-10 22:20:44','2026-09-10 19:20:44','2026-09-10 21:20:44','2026-09-10 18:20:44','2026-09-28 18:35:57',NULL),(6,1,25,28,11,1,'WO-004','cancelled',NULL,NULL,447.96,0.00,447.96,0.00,0.00,0.00,0.00,NULL,NULL,1,'2026-09-13 22:20:44','2026-09-13 19:20:44','2026-09-13 23:20:44','2026-09-13 18:20:44','2026-09-28 18:36:04',NULL),(7,1,26,29,10,1,'WO-005','in_progress',NULL,NULL,339.96,0.00,339.96,0.00,0.00,0.00,0.00,NULL,NULL,1,'2026-09-16 01:20:44','2026-09-15 22:20:44','2026-09-16 02:20:44','2026-09-15 21:20:44','2026-09-28 18:52:22',NULL),(8,1,27,30,11,1,'WO-006','in_progress',NULL,NULL,106.00,0.00,106.00,0.00,0.00,0.00,0.00,NULL,NULL,1,'2026-09-17 23:20:44','2026-09-17 20:20:44','2026-09-17 23:20:44','2026-09-17 19:20:44','2026-09-28 18:10:44',NULL),(9,1,28,31,10,1,'WO-007','completed',NULL,NULL,769.96,0.00,769.96,0.00,0.00,0.00,0.00,NULL,NULL,1,'2026-09-20 23:20:44','2026-09-20 20:20:44','2026-09-20 23:20:44','2026-09-20 19:20:44','2026-09-26 00:20:44',NULL),(10,1,29,32,11,1,'WO-008','pending',NULL,NULL,454.98,0.00,454.98,0.00,0.00,0.00,0.00,NULL,NULL,0,'2026-09-25 23:20:44','2026-09-25 20:20:44',NULL,'2026-09-25 19:20:44','2026-09-28 18:10:36',NULL),(11,1,30,33,10,1,'WO-009','completed',NULL,NULL,28.99,0.00,28.99,0.00,0.00,3.00,0.00,25.99,89.65,0,'2026-09-26 02:20:44','2026-09-28 18:10:41','2026-09-28 18:40:16','2026-09-25 22:20:44','2026-09-28 18:40:16',NULL),(12,1,31,34,11,1,'WO-010','pending',NULL,NULL,30.00,0.00,30.00,0.00,0.00,0.00,0.00,NULL,NULL,0,'2026-09-26 00:20:44',NULL,NULL,'2026-09-25 20:20:44','2026-09-28 18:10:37',NULL),(13,1,23,NULL,11,1,'WO-011','in_progress',NULL,NULL,804.99,0.00,804.99,410.00,0.00,2.00,4.00,388.99,48.32,0,'2026-09-28 18:00:00','2026-09-28 17:34:43','2026-09-28 17:38:11','2026-09-28 17:34:10','2026-09-28 18:40:27',NULL),(14,1,32,35,13,1,'WO-012','completed',NULL,NULL,1762.96,0.00,1762.96,0.00,0.00,0.00,0.00,NULL,NULL,1,'2026-09-28 05:00:00','2026-09-28 18:35:44','2026-09-28 18:40:15','2026-09-28 18:35:11','2026-09-28 18:44:31',NULL),(15,1,32,35,10,1,'WO-013','completed',NULL,NULL,394.97,0.00,394.97,0.00,0.00,0.00,0.00,NULL,NULL,1,'2026-09-28 19:37:00','2026-09-28 19:37:57','2026-09-28 19:38:07','2026-09-28 19:37:42','2026-09-28 19:38:56',NULL),(16,1,33,36,11,1,'WO-014','pending','El cliente quiere resposabilidad con su vehiculo',NULL,1116.94,0.00,1116.94,542.00,0.00,40.00,6.00,528.94,47.36,0,'2026-10-03 16:00:00','2026-10-03 02:44:22',NULL,'2026-10-03 02:44:18','2026-10-03 20:39:23',NULL);
/*!40000 ALTER TABLE `work_orders` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-04 11:00:12
