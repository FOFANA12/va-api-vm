-- MySQL dump 10.13  Distrib 8.0.45, for macos15 (arm64)
--
-- Host: localhost    Database: va-apivm
-- ------------------------------------------------------
-- Server version	8.0.26

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `action_beneficiaries`
--

DROP TABLE IF EXISTS `action_beneficiaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_beneficiaries` (
  `action_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`action_uuid`,`beneficiary_uuid`),
  KEY `action_beneficiaries_beneficiary_uuid_foreign` (`beneficiary_uuid`),
  CONSTRAINT `action_beneficiaries_action_uuid_foreign` FOREIGN KEY (`action_uuid`) REFERENCES `actions` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_beneficiaries_beneficiary_uuid_foreign` FOREIGN KEY (`beneficiary_uuid`) REFERENCES `beneficiaries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_beneficiaries`
--

LOCK TABLES `action_beneficiaries` WRITE;
/*!40000 ALTER TABLE `action_beneficiaries` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_beneficiaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_control_phases`
--

DROP TABLE IF EXISTS `action_control_phases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_control_phases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_control_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phase_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `progress_percent` decimal(5,2) NOT NULL,
  `weight` decimal(8,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `action_control_phases_uuid_unique` (`uuid`),
  KEY `action_control_phases_action_control_uuid_foreign` (`action_control_uuid`),
  KEY `action_control_phases_phase_uuid_foreign` (`phase_uuid`),
  CONSTRAINT `action_control_phases_action_control_uuid_foreign` FOREIGN KEY (`action_control_uuid`) REFERENCES `action_controls` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_control_phases_phase_uuid_foreign` FOREIGN KEY (`phase_uuid`) REFERENCES `action_phases` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_control_phases`
--

LOCK TABLES `action_control_phases` WRITE;
/*!40000 ALTER TABLE `action_control_phases` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_control_phases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_controls`
--

DROP TABLE IF EXISTS `action_controls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_controls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_period_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `control_date` date NOT NULL,
  `forecast_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `actual_progress_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `root_cause` text COLLATE utf8mb4_unicode_ci,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `action_controls_uuid_unique` (`uuid`),
  KEY `action_controls_action_period_uuid_foreign` (`action_period_uuid`),
  KEY `action_controls_created_by_foreign` (`created_by`),
  KEY `action_controls_updated_by_foreign` (`updated_by`),
  CONSTRAINT `action_controls_action_period_uuid_foreign` FOREIGN KEY (`action_period_uuid`) REFERENCES `action_periods` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_controls_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `action_controls_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_controls`
--

LOCK TABLES `action_controls` WRITE;
/*!40000 ALTER TABLE `action_controls` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_controls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_domain_states`
--

DROP TABLE IF EXISTS `action_domain_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_domain_states` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_domain_id` int NOT NULL,
  `state_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_date` timestamp NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `action_domain_states_uuid_unique` (`uuid`),
  KEY `action_domain_states_created_by_foreign` (`created_by`),
  KEY `action_domain_states_updated_by_foreign` (`updated_by`),
  KEY `action_domain_states_action_domain_uuid_index` (`action_domain_uuid`),
  KEY `action_domain_states_action_domain_id_index` (`action_domain_id`),
  CONSTRAINT `action_domain_states_action_domain_uuid_foreign` FOREIGN KEY (`action_domain_uuid`) REFERENCES `action_domains` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_domain_states_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `action_domain_states_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_domain_states`
--

LOCK TABLES `action_domain_states` WRITE;
/*!40000 ALTER TABLE `action_domain_states` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_domain_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_domain_statuses`
--

DROP TABLE IF EXISTS `action_domain_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_domain_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_domain_id` int NOT NULL,
  `status_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_date` timestamp NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `action_domain_statuses_uuid_unique` (`uuid`),
  KEY `action_domain_statuses_created_by_foreign` (`created_by`),
  KEY `action_domain_statuses_updated_by_foreign` (`updated_by`),
  KEY `action_domain_statuses_action_domain_uuid_index` (`action_domain_uuid`),
  KEY `action_domain_statuses_action_domain_id_index` (`action_domain_id`),
  CONSTRAINT `action_domain_statuses_action_domain_uuid_foreign` FOREIGN KEY (`action_domain_uuid`) REFERENCES `action_domains` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_domain_statuses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `action_domain_statuses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_domain_statuses`
--

LOCK TABLES `action_domain_statuses` WRITE;
/*!40000 ALTER TABLE `action_domain_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_domain_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_domains`
--

DROP TABLE IF EXISTS `action_domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_domains` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(14,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MRU',
  `responsible_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'preparation',
  `status_changed_at` timestamp NULL DEFAULT NULL,
  `status_changed_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `state_changed_at` timestamp NULL DEFAULT NULL,
  `state_changed_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `prerequisites` text COLLATE utf8mb4_unicode_ci,
  `impacts` text COLLATE utf8mb4_unicode_ci,
  `risks` text COLLATE utf8mb4_unicode_ci,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `action_domains_uuid_unique` (`uuid`),
  UNIQUE KEY `action_domains_name_unique` (`name`),
  UNIQUE KEY `action_domains_reference_unique` (`reference`),
  KEY `action_domains_responsible_uuid_foreign` (`responsible_uuid`),
  KEY `action_domains_created_by_foreign` (`created_by`),
  KEY `action_domains_updated_by_foreign` (`updated_by`),
  CONSTRAINT `action_domains_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `action_domains_responsible_uuid_foreign` FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `action_domains_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_domains`
--

LOCK TABLES `action_domains` WRITE;
/*!40000 ALTER TABLE `action_domains` DISABLE KEYS */;
INSERT INTO `action_domains` VALUES (1,'efec6c04-d518-4d8f-a6b7-4ed7c2dc2db9','PRG-00A','Programme A','2025-01-01','2025-12-31',178000.00,'MRU','47e95852-f4f4-4e64-940f-9adbd5b0edfb','preparation',NULL,NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(2,'62e7e327-083a-49e8-b40e-2fccb49d6a6c','PRG-00B','Programme B','2025-02-01','2025-11-30',407366.00,'MRU','8cb07c77-5be7-4824-95d4-3b4ec75c146b','preparation',NULL,NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(3,'62e1ae2b-f482-4788-9f2a-a4ba99bef1df','PROG-00C','Programme C','2025-03-01','2025-09-30',87485.00,'MRU','e4dea000-72cf-40ae-929a-20e33a36fe50','preparation',NULL,NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48');
/*!40000 ALTER TABLE `action_domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_fund_disbursement_expense_types`
--

DROP TABLE IF EXISTS `action_fund_disbursement_expense_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_fund_disbursement_expense_types` (
  `action_fund_disbursement_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expense_type_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(24,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`action_fund_disbursement_uuid`,`expense_type_uuid`),
  KEY `afdet_expense_type_fk` (`expense_type_uuid`),
  CONSTRAINT `afdet_disbursement_fk` FOREIGN KEY (`action_fund_disbursement_uuid`) REFERENCES `action_fund_disbursements` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `afdet_expense_type_fk` FOREIGN KEY (`expense_type_uuid`) REFERENCES `expense_types` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_fund_disbursement_expense_types`
--

LOCK TABLES `action_fund_disbursement_expense_types` WRITE;
/*!40000 ALTER TABLE `action_fund_disbursement_expense_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_fund_disbursement_expense_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_fund_disbursements`
--

DROP TABLE IF EXISTS `action_fund_disbursements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_fund_disbursements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signature_date` date NOT NULL,
  `execution_date` date NOT NULL,
  `payment_date` date NOT NULL,
  `payment_amount` decimal(14,2) NOT NULL,
  `payment_mode_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cheque_reference` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `budget_type_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phase_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `task_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contract_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `action_fund_disbursements_uuid_unique` (`uuid`),
  UNIQUE KEY `action_fund_disbursements_reference_unique` (`reference`),
  KEY `action_fund_disbursements_action_uuid_foreign` (`action_uuid`),
  KEY `action_fund_disbursements_supplier_uuid_foreign` (`supplier_uuid`),
  KEY `action_fund_disbursements_contract_uuid_foreign` (`contract_uuid`),
  KEY `action_fund_disbursements_payment_mode_uuid_foreign` (`payment_mode_uuid`),
  KEY `action_fund_disbursements_budget_type_uuid_foreign` (`budget_type_uuid`),
  KEY `action_fund_disbursements_phase_uuid_foreign` (`phase_uuid`),
  KEY `action_fund_disbursements_task_uuid_foreign` (`task_uuid`),
  KEY `action_fund_disbursements_created_by_foreign` (`created_by`),
  KEY `action_fund_disbursements_updated_by_foreign` (`updated_by`),
  CONSTRAINT `action_fund_disbursements_action_uuid_foreign` FOREIGN KEY (`action_uuid`) REFERENCES `actions` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_fund_disbursements_budget_type_uuid_foreign` FOREIGN KEY (`budget_type_uuid`) REFERENCES `budget_types` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_fund_disbursements_contract_uuid_foreign` FOREIGN KEY (`contract_uuid`) REFERENCES `contracts` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_fund_disbursements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `action_fund_disbursements_payment_mode_uuid_foreign` FOREIGN KEY (`payment_mode_uuid`) REFERENCES `payment_modes` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_fund_disbursements_phase_uuid_foreign` FOREIGN KEY (`phase_uuid`) REFERENCES `action_phases` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_fund_disbursements_supplier_uuid_foreign` FOREIGN KEY (`supplier_uuid`) REFERENCES `suppliers` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_fund_disbursements_task_uuid_foreign` FOREIGN KEY (`task_uuid`) REFERENCES `tasks` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_fund_disbursements_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_fund_disbursements`
--

LOCK TABLES `action_fund_disbursements` WRITE;
/*!40000 ALTER TABLE `action_fund_disbursements` DISABLE KEYS */;
INSERT INTO `action_fund_disbursements` VALUES (1,'b75bd00b-a82d-4e5e-88e0-7fe3860a0031','4d20ea52-f876-43d0-9ddc-e97b285030ed','DEC-UAS-OPS1-UAS-OPS1_ACT001-1-001','OP-001','2025-01-10','2025-01-20','2025-01-25',5000.00,'5f3ac087-77c8-4b26-8bd5-3feec7623567','CHQ-001','72f166f0-c083-4ab7-8a06-74106ff96355','97806701-866d-45fd-99a7-80b17186d056',NULL,'f7af3e96-be5f-4d00-87ef-2a9e5e926ea2','a03f4c8a-a6e6-4134-b2be-3c5c1a75b356','Premier décaissement pour formation.','2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL),(2,'80b3624a-a4ca-4fac-871b-14771969a3b7','4d20ea52-f876-43d0-9ddc-e97b285030ed','DEC-UAS-OPS1-UAS-OPS1_ACT001-1-002','OP-002','2025-02-15','2025-02-20','2025-02-28',7500.00,'5f3ac087-77c8-4b26-8bd5-3feec7623567','CHQ-002','72f166f0-c083-4ab7-8a06-74106ff96355','97806701-866d-45fd-99a7-80b17186d056',NULL,'f7af3e96-be5f-4d00-87ef-2a9e5e926ea2','a03f4c8a-a6e6-4134-b2be-3c5c1a75b356','Décaissement pour campagne de sensibilisation.','2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL),(3,'5e1da840-e3d9-44ed-88bf-070bf29f0c88','4d20ea52-f876-43d0-9ddc-e97b285030ed','DEC-UAS-OPS1-UAS-OPS1_ACT001-1-003','OP-003','2025-04-01','2025-04-10','2025-04-15',11000.00,'5f3ac087-77c8-4b26-8bd5-3feec7623567','CHQ-003','72f166f0-c083-4ab7-8a06-74106ff96355','97806701-866d-45fd-99a7-80b17186d056',NULL,'f7af3e96-be5f-4d00-87ef-2a9e5e926ea2','a03f4c8a-a6e6-4134-b2be-3c5c1a75b356','Décaissement pour distribution de kits.','2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL);
/*!40000 ALTER TABLE `action_fund_disbursements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_fund_receipts`
--

DROP TABLE IF EXISTS `action_fund_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_fund_receipts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_date` date NOT NULL,
  `validity_date` date NOT NULL,
  `funding_source_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` decimal(10,4) NOT NULL DEFAULT '1.0000',
  `amount_original` decimal(14,2) NOT NULL,
  `converted_amount` decimal(14,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `action_fund_receipts_uuid_unique` (`uuid`),
  UNIQUE KEY `action_fund_receipts_reference_unique` (`reference`),
  KEY `action_fund_receipts_created_by_foreign` (`created_by`),
  KEY `action_fund_receipts_updated_by_foreign` (`updated_by`),
  KEY `action_fund_receipts_action_uuid_index` (`action_uuid`),
  KEY `action_fund_receipts_funding_source_uuid_index` (`funding_source_uuid`),
  KEY `action_fund_receipts_currency_uuid_index` (`currency_uuid`),
  KEY `action_fund_receipts_amount_original_index` (`amount_original`),
  KEY `action_fund_receipts_converted_amount_index` (`converted_amount`),
  CONSTRAINT `action_fund_receipts_action_uuid_foreign` FOREIGN KEY (`action_uuid`) REFERENCES `actions` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_fund_receipts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `action_fund_receipts_currency_uuid_foreign` FOREIGN KEY (`currency_uuid`) REFERENCES `currencies` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_fund_receipts_funding_source_uuid_foreign` FOREIGN KEY (`funding_source_uuid`) REFERENCES `funding_sources` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_fund_receipts_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_fund_receipts`
--

LOCK TABLES `action_fund_receipts` WRITE;
/*!40000 ALTER TABLE `action_fund_receipts` DISABLE KEYS */;
INSERT INTO `action_fund_receipts` VALUES (1,'d267edec-7556-46fc-a02e-30fe5570937f','4d20ea52-f876-43d0-9ddc-e97b285030ed','ENC-UAS-OPS1-UAS-OPS1_ACT001-001','2025-01-10','2025-12-31','9655a402-f483-4504-bc37-762c19349d94','b5829a14-e756-4328-9693-24519dd4baf4',1.0000,1500.00,1500.00,'2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL),(2,'415fe3fa-e4f4-46b2-b685-0141dafd0b08','4d20ea52-f876-43d0-9ddc-e97b285030ed','ENC-UAS-OPS1-UAS-OPS1_ACT001-002','2025-02-15','2025-11-30','9655a402-f483-4504-bc37-762c19349d94','b5829a14-e756-4328-9693-24519dd4baf4',1.0000,1200.00,1200.00,'2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL);
/*!40000 ALTER TABLE `action_fund_receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_funding_sources`
--

DROP TABLE IF EXISTS `action_funding_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_funding_sources` (
  `action_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funding_source_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planned_budget` decimal(14,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`action_uuid`,`funding_source_uuid`),
  KEY `action_funding_sources_funding_source_uuid_foreign` (`funding_source_uuid`),
  CONSTRAINT `action_funding_sources_action_uuid_foreign` FOREIGN KEY (`action_uuid`) REFERENCES `actions` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_funding_sources_funding_source_uuid_foreign` FOREIGN KEY (`funding_source_uuid`) REFERENCES `funding_sources` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_funding_sources`
--

LOCK TABLES `action_funding_sources` WRITE;
/*!40000 ALTER TABLE `action_funding_sources` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_funding_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_metrics`
--

DROP TABLE IF EXISTS `action_metrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_metrics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `action_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_id` int unsigned NOT NULL,
  `realization_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `realization_index` decimal(5,2) NOT NULL DEFAULT '0.00',
  `aligned_axes_count` int unsigned NOT NULL DEFAULT '0',
  `aligned_objectives_count` int unsigned NOT NULL DEFAULT '0',
  `aligned_maps_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `action_metrics_action_uuid_index` (`action_uuid`),
  KEY `action_metrics_action_id_index` (`action_id`),
  CONSTRAINT `action_metrics_action_uuid_foreign` FOREIGN KEY (`action_uuid`) REFERENCES `actions` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_metrics`
--

LOCK TABLES `action_metrics` WRITE;
/*!40000 ALTER TABLE `action_metrics` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_metrics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_objective_alignments`
--

DROP TABLE IF EXISTS `action_objective_alignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_objective_alignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_structure_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objective_structure_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_map_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_element_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objective_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aligned_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aligned_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_action_objective` (`action_uuid`,`objective_uuid`),
  UNIQUE KEY `action_objective_alignments_uuid_unique` (`uuid`),
  UNIQUE KEY `action_objective_alignments_identifier_unique` (`identifier`),
  KEY `idx_action_objective` (`action_uuid`,`objective_uuid`),
  KEY `idx_structures` (`action_structure_uuid`,`objective_structure_uuid`),
  KEY `action_objective_alignments_objective_structure_uuid_foreign` (`objective_structure_uuid`),
  KEY `action_objective_alignments_strategic_map_uuid_foreign` (`strategic_map_uuid`),
  KEY `action_objective_alignments_strategic_element_uuid_foreign` (`strategic_element_uuid`),
  KEY `action_objective_alignments_objective_uuid_foreign` (`objective_uuid`),
  KEY `action_objective_alignments_aligned_by_foreign` (`aligned_by`),
  CONSTRAINT `action_objective_alignments_action_structure_uuid_foreign` FOREIGN KEY (`action_structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_objective_alignments_action_uuid_foreign` FOREIGN KEY (`action_uuid`) REFERENCES `actions` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_objective_alignments_aligned_by_foreign` FOREIGN KEY (`aligned_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `action_objective_alignments_objective_structure_uuid_foreign` FOREIGN KEY (`objective_structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_objective_alignments_objective_uuid_foreign` FOREIGN KEY (`objective_uuid`) REFERENCES `strategic_objectives` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_objective_alignments_strategic_element_uuid_foreign` FOREIGN KEY (`strategic_element_uuid`) REFERENCES `strategic_elements` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_objective_alignments_strategic_map_uuid_foreign` FOREIGN KEY (`strategic_map_uuid`) REFERENCES `strategic_maps` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_objective_alignments`
--

LOCK TABLES `action_objective_alignments` WRITE;
/*!40000 ALTER TABLE `action_objective_alignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_objective_alignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_periods`
--

DROP TABLE IF EXISTS `action_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_periods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `progress_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `actual_progress_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `action_periods_uuid_unique` (`uuid`),
  KEY `action_periods_action_uuid_foreign` (`action_uuid`),
  CONSTRAINT `action_periods_action_uuid_foreign` FOREIGN KEY (`action_uuid`) REFERENCES `actions` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_periods`
--

LOCK TABLES `action_periods` WRITE;
/*!40000 ALTER TABLE `action_periods` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_phases`
--

DROP TABLE IF EXISTS `action_phases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_phases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` tinyint unsigned NOT NULL DEFAULT '0',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `weight` decimal(8,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `deliverable` text COLLATE utf8mb4_unicode_ci,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `action_phases_action_uuid_name_unique` (`action_uuid`,`name`),
  UNIQUE KEY `action_phases_uuid_unique` (`uuid`),
  KEY `action_phases_created_by_foreign` (`created_by`),
  KEY `action_phases_updated_by_foreign` (`updated_by`),
  CONSTRAINT `action_phases_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `action_phases_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_phases`
--

LOCK TABLES `action_phases` WRITE;
/*!40000 ALTER TABLE `action_phases` DISABLE KEYS */;
INSERT INTO `action_phases` VALUES (1,'97806701-866d-45fd-99a7-80b17186d056','4d20ea52-f876-43d0-9ddc-e97b285030ed','Phase 1',1,'2025-01-10','2025-02-12',0.05,'Description de la phase 1 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 1.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(2,'eee1e176-5088-45ca-8d05-58141907c4d0','4d20ea52-f876-43d0-9ddc-e97b285030ed','Phase 2',2,'2025-02-13','2025-03-18',0.95,'Description de la phase 2 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 2.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(3,'e3fb1e00-b343-49a7-9c42-bfa9de8ccaf4','4d20ea52-f876-43d0-9ddc-e97b285030ed','Phase 3',3,'2025-03-19','2025-04-21',0.00,'Description de la phase 3 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 3.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(4,'9b8937b0-3328-435b-80f4-abdcbabc832c','4d20ea52-f876-43d0-9ddc-e97b285030ed','Phase 4',4,'2025-04-22','2025-05-25',0.00,'Description de la phase 4 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 4.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(5,'53222dcf-fbe4-4067-8ea2-073aa3fbc7bc','4d20ea52-f876-43d0-9ddc-e97b285030ed','Phase 5',5,'2025-05-26','2025-06-30',0.00,'Description de la phase 5 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 5.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(6,'0bc3ff67-2687-4b98-abb9-7e47232674d5','456dcd9f-75bf-4751-819b-59bc6a770fe7','Phase 1',1,'2025-03-01','2025-04-05',1.00,'Description de la phase 1 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 1.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(7,'fd7f693e-d965-42e7-9192-e7ef46696f4b','456dcd9f-75bf-4751-819b-59bc6a770fe7','Phase 2',2,'2025-04-06','2025-05-11',0.00,'Description de la phase 2 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 2.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(8,'63a94c3a-ed43-489d-aa6e-a2a5e05849b0','456dcd9f-75bf-4751-819b-59bc6a770fe7','Phase 3',3,'2025-05-12','2025-06-16',0.00,'Description de la phase 3 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 3.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(9,'97e205b7-6588-4879-b8ee-14af99b6ca1d','456dcd9f-75bf-4751-819b-59bc6a770fe7','Phase 4',4,'2025-06-17','2025-07-22',0.00,'Description de la phase 4 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 4.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(10,'2b527de4-e8d0-496f-ab29-d3ce71767ab6','456dcd9f-75bf-4751-819b-59bc6a770fe7','Phase 5',5,'2025-07-23','2025-09-01',0.00,'Description de la phase 5 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 5.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(11,'1c7c7038-e5fe-4e5e-a64c-c9635cb75ab0','c53331a4-04ac-471f-8c58-b5e21cdadae3','Phase 1',1,'2025-01-10','2025-02-12',0.75,'Description de la phase 1 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 1.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(12,'c038b428-b8c5-428d-bedd-2e1eb0df729c','c53331a4-04ac-471f-8c58-b5e21cdadae3','Phase 2',2,'2025-02-13','2025-03-18',0.25,'Description de la phase 2 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 2.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(13,'9b1033d3-d4e9-446b-989c-b448032d3f4c','c53331a4-04ac-471f-8c58-b5e21cdadae3','Phase 3',3,'2025-03-19','2025-04-21',0.00,'Description de la phase 3 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 3.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(14,'2f50ca50-4986-461f-9b5b-7e3dfd899396','c53331a4-04ac-471f-8c58-b5e21cdadae3','Phase 4',4,'2025-04-22','2025-05-25',0.00,'Description de la phase 4 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 4.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(15,'d08d5c52-4891-4c79-ac7d-df251c5e2b25','c53331a4-04ac-471f-8c58-b5e21cdadae3','Phase 5',5,'2025-05-26','2025-06-30',0.00,'Description de la phase 5 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 5.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(16,'dc08d366-ae8a-4749-a606-67f17402fb3f','11392d4d-82e6-49c7-b728-b067fcc9e9f3','Phase 1',1,'2025-03-01','2025-04-05',0.65,'Description de la phase 1 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 1.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(17,'a367c44b-c575-4a2d-ae76-9ee270844c3c','11392d4d-82e6-49c7-b728-b067fcc9e9f3','Phase 2',2,'2025-04-06','2025-05-11',0.20,'Description de la phase 2 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 2.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(18,'e0aac968-7058-4006-87bc-ae51f15be67a','11392d4d-82e6-49c7-b728-b067fcc9e9f3','Phase 3',3,'2025-05-12','2025-06-16',0.15,'Description de la phase 3 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 3.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(19,'92d7246e-12c9-4b5f-9e50-c7fb10e44aa7','11392d4d-82e6-49c7-b728-b067fcc9e9f3','Phase 4',4,'2025-06-17','2025-07-22',0.00,'Description de la phase 4 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 4.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(20,'9f44dbe4-cb7d-45b0-bd0d-8b148afe59ac','11392d4d-82e6-49c7-b728-b067fcc9e9f3','Phase 5',5,'2025-07-23','2025-09-01',0.00,'Description de la phase 5 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 5.','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49');
/*!40000 ALTER TABLE `action_phases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_plans`
--

DROP TABLE IF EXISTS `action_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `structure_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `responsible_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `action_plans_structure_uuid_name_unique` (`structure_uuid`,`name`),
  UNIQUE KEY `action_plans_uuid_unique` (`uuid`),
  KEY `action_plans_responsible_uuid_foreign` (`responsible_uuid`),
  KEY `action_plans_created_by_foreign` (`created_by`),
  KEY `action_plans_updated_by_foreign` (`updated_by`),
  CONSTRAINT `action_plans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `action_plans_responsible_uuid_foreign` FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `action_plans_structure_uuid_foreign` FOREIGN KEY (`structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_plans_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_plans`
--

LOCK TABLES `action_plans` WRITE;
/*!40000 ALTER TABLE `action_plans` DISABLE KEYS */;
INSERT INTO `action_plans` VALUES (1,'fb389df6-3f85-4c92-9f94-6ce8a5e84c18','c4196136-dc09-48c8-8b83-baa6d45b7eff',NULL,'PA-STRAT-2025','Plan d\'action stratégique 2025','Plan d\'action opérationnel prioritaire pour l\'année 2025.','2025-01-01','2025-12-31',1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL);
/*!40000 ALTER TABLE `action_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_stakeholders`
--

DROP TABLE IF EXISTS `action_stakeholders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_stakeholders` (
  `action_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stakeholder_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`action_uuid`,`stakeholder_uuid`),
  KEY `action_stakeholders_stakeholder_uuid_foreign` (`stakeholder_uuid`),
  CONSTRAINT `action_stakeholders_action_uuid_foreign` FOREIGN KEY (`action_uuid`) REFERENCES `actions` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_stakeholders_stakeholder_uuid_foreign` FOREIGN KEY (`stakeholder_uuid`) REFERENCES `stakeholders` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_stakeholders`
--

LOCK TABLES `action_stakeholders` WRITE;
/*!40000 ALTER TABLE `action_stakeholders` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_stakeholders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_statuses`
--

DROP TABLE IF EXISTS `action_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_id` bigint NOT NULL,
  `status_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_date` timestamp NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `action_statuses_uuid_unique` (`uuid`),
  KEY `action_statuses_created_by_foreign` (`created_by`),
  KEY `action_statuses_updated_by_foreign` (`updated_by`),
  KEY `action_statuses_action_uuid_index` (`action_uuid`),
  KEY `action_statuses_action_id_index` (`action_id`),
  CONSTRAINT `action_statuses_action_uuid_foreign` FOREIGN KEY (`action_uuid`) REFERENCES `actions` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_statuses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `action_statuses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_statuses`
--

LOCK TABLES `action_statuses` WRITE;
/*!40000 ALTER TABLE `action_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `actions`
--

DROP TABLE IF EXISTS `actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `structure_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_plan_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_owner_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delegated_project_owner_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `municipality_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `strategic_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capability_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `elementary_level_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `risk_level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'moderate',
  `description` text COLLATE utf8mb4_unicode_ci,
  `prerequisites` text COLLATE utf8mb4_unicode_ci,
  `impacts` text COLLATE utf8mb4_unicode_ci,
  `risks` text COLLATE utf8mb4_unicode_ci,
  `generate_document_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'autre',
  `state` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'created',
  `status_changed_at` timestamp NULL DEFAULT NULL,
  `status_changed_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chart_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BAR',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `actual_start_date` date DEFAULT NULL,
  `actual_end_date` date DEFAULT NULL,
  `total_budget` decimal(24,2) NOT NULL DEFAULT '0.00',
  `total_receipt_fund` decimal(24,2) NOT NULL DEFAULT '0.00',
  `total_disbursement_fund` decimal(24,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MRU',
  `frequency_unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency_value` tinyint DEFAULT NULL,
  `is_planned` tinyint(1) NOT NULL DEFAULT '0',
  `actual_progress_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `failed` tinyint(1) NOT NULL DEFAULT '0',
  `alert` tinyint(1) NOT NULL DEFAULT '0',
  `responsible_structure_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsible_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `actions_uuid_unique` (`uuid`),
  UNIQUE KEY `actions_reference_unique` (`reference`),
  KEY `actions_structure_uuid_foreign` (`structure_uuid`),
  KEY `actions_action_plan_uuid_foreign` (`action_plan_uuid`),
  KEY `actions_project_owner_uuid_foreign` (`project_owner_uuid`),
  KEY `actions_delegated_project_owner_uuid_foreign` (`delegated_project_owner_uuid`),
  KEY `actions_action_domain_uuid_foreign` (`action_domain_uuid`),
  KEY `actions_strategic_domain_uuid_foreign` (`strategic_domain_uuid`),
  KEY `actions_capability_domain_uuid_foreign` (`capability_domain_uuid`),
  KEY `actions_elementary_level_uuid_foreign` (`elementary_level_uuid`),
  KEY `actions_region_uuid_foreign` (`region_uuid`),
  KEY `actions_department_uuid_foreign` (`department_uuid`),
  KEY `actions_municipality_uuid_foreign` (`municipality_uuid`),
  KEY `actions_created_by_foreign` (`created_by`),
  KEY `actions_updated_by_foreign` (`updated_by`),
  KEY `actions_status_changed_by_foreign` (`status_changed_by`),
  KEY `actions_responsible_structure_uuid_foreign` (`responsible_structure_uuid`),
  KEY `actions_responsible_uuid_foreign` (`responsible_uuid`),
  CONSTRAINT `actions_action_domain_uuid_foreign` FOREIGN KEY (`action_domain_uuid`) REFERENCES `action_domains` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `actions_action_plan_uuid_foreign` FOREIGN KEY (`action_plan_uuid`) REFERENCES `action_plans` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `actions_capability_domain_uuid_foreign` FOREIGN KEY (`capability_domain_uuid`) REFERENCES `capability_domains` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `actions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `actions_delegated_project_owner_uuid_foreign` FOREIGN KEY (`delegated_project_owner_uuid`) REFERENCES `delegated_project_owners` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `actions_department_uuid_foreign` FOREIGN KEY (`department_uuid`) REFERENCES `departments` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `actions_elementary_level_uuid_foreign` FOREIGN KEY (`elementary_level_uuid`) REFERENCES `elementary_levels` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `actions_municipality_uuid_foreign` FOREIGN KEY (`municipality_uuid`) REFERENCES `municipalities` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `actions_project_owner_uuid_foreign` FOREIGN KEY (`project_owner_uuid`) REFERENCES `project_owners` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `actions_region_uuid_foreign` FOREIGN KEY (`region_uuid`) REFERENCES `regions` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `actions_responsible_structure_uuid_foreign` FOREIGN KEY (`responsible_structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `actions_responsible_uuid_foreign` FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `actions_status_changed_by_foreign` FOREIGN KEY (`status_changed_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `actions_strategic_domain_uuid_foreign` FOREIGN KEY (`strategic_domain_uuid`) REFERENCES `strategic_domains` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `actions_structure_uuid_foreign` FOREIGN KEY (`structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `actions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actions`
--

LOCK TABLES `actions` WRITE;
/*!40000 ALTER TABLE `actions` DISABLE KEYS */;
INSERT INTO `actions` VALUES (1,'4d20ea52-f876-43d0-9ddc-e97b285030ed','UAS-OPS1_ACT001','c4196136-dc09-48c8-8b83-baa6d45b7eff','fb389df6-3f85-4c92-9f94-6ce8a5e84c18','cf577d7f-f6fc-440c-981e-87d9494e46dc','9a94ab88-338e-438a-8226-a20930528896','742b399a-5971-447e-86e4-0e648eb61d36','c3246e1b-6260-4279-b845-d60eb30fba2b','0648f81f-8a72-47c2-aa51-a0e82899894a','efec6c04-d518-4d8f-a6b7-4ed7c2dc2db9','48b76f89-38ad-43d6-b7e2-7fbdb6fa074e','7207f568-b7af-4662-b80f-9e3d34c9ff85',NULL,'Réhabilitation d\'écoles primaires','high','medium','Réhabilitation de 10 écoles en milieu rural.','Étude technique préalable','Amélioration des conditions d\'apprentissage','Manque de financement, retard de livraison','ppm','none','in_progress','2026-05-18 11:35:48','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8','BAR','2025-01-10','2025-06-30',NULL,NULL,150000.00,0.00,0.00,'MRU','monthly',1,0,0.00,0,0,NULL,NULL,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(2,'456dcd9f-75bf-4751-819b-59bc6a770fe7','UAS-OPS1_ACT002','c4196136-dc09-48c8-8b83-baa6d45b7eff','fb389df6-3f85-4c92-9f94-6ce8a5e84c18','cf577d7f-f6fc-440c-981e-87d9494e46dc','9a94ab88-338e-438a-8226-a20930528896','742b399a-5971-447e-86e4-0e648eb61d36','c3246e1b-6260-4279-b845-d60eb30fba2b','0648f81f-8a72-47c2-aa51-a0e82899894a','efec6c04-d518-4d8f-a6b7-4ed7c2dc2db9','48b76f89-38ad-43d6-b7e2-7fbdb6fa074e','7207f568-b7af-4662-b80f-9e3d34c9ff85',NULL,'Construction d\'un centre de santé','medium','high','Construction d\'un nouveau centre de santé dans la commune A.','Validation du site par les autorités locales','Renforcement du système de santé local','Opposition communautaire, intempéries','paa','none','in_progress','2026-05-18 11:35:48','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8','LINE','2025-03-01','2025-09-01',NULL,NULL,300000.00,0.00,0.00,'MRU','quarterly',3,0,0.00,0,0,NULL,NULL,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(3,'c53331a4-04ac-471f-8c58-b5e21cdadae3','UAS-OPS1_ACT003','c4196136-dc09-48c8-8b83-baa6d45b7eff','fb389df6-3f85-4c92-9f94-6ce8a5e84c18','cf577d7f-f6fc-440c-981e-87d9494e46dc','9a94ab88-338e-438a-8226-a20930528896','742b399a-5971-447e-86e4-0e648eb61d36','c3246e1b-6260-4279-b845-d60eb30fba2b','0648f81f-8a72-47c2-aa51-a0e82899894a','efec6c04-d518-4d8f-a6b7-4ed7c2dc2db9','48b76f89-38ad-43d6-b7e2-7fbdb6fa074e','7207f568-b7af-4662-b80f-9e3d34c9ff85',NULL,'Réhabilitation d\'écoles primaires','high','medium','Réhabilitation de 10 écoles en milieu rural.','Étude technique préalable','Amélioration des conditions d\'apprentissage','Manque de financement, retard de livraison','ppm','none','in_progress','2026-05-18 11:35:49','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8','BAR','2025-01-10','2025-06-30',NULL,NULL,150000.00,0.00,0.00,'MRU','monthly',1,0,0.00,0,0,NULL,NULL,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(4,'11392d4d-82e6-49c7-b728-b067fcc9e9f3','UAS-OPS1_ACT004','c4196136-dc09-48c8-8b83-baa6d45b7eff','fb389df6-3f85-4c92-9f94-6ce8a5e84c18','cf577d7f-f6fc-440c-981e-87d9494e46dc','9a94ab88-338e-438a-8226-a20930528896','742b399a-5971-447e-86e4-0e648eb61d36','c3246e1b-6260-4279-b845-d60eb30fba2b','0648f81f-8a72-47c2-aa51-a0e82899894a','efec6c04-d518-4d8f-a6b7-4ed7c2dc2db9','48b76f89-38ad-43d6-b7e2-7fbdb6fa074e','7207f568-b7af-4662-b80f-9e3d34c9ff85',NULL,'Construction d\'un centre de santé','medium','high','Construction d\'un nouveau centre de santé dans la commune A.','Validation du site par les autorités locales','Renforcement du système de santé local','Opposition communautaire, intempéries','paa','none','in_progress','2026-05-18 11:35:49','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8','LINE','2025-03-01','2025-09-01',NULL,NULL,300000.00,0.00,0.00,'MRU','quarterly',3,0,0.00,0,0,NULL,NULL,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49');
/*!40000 ALTER TABLE `actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_beneficiaries`
--

DROP TABLE IF EXISTS `activity_beneficiaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_beneficiaries` (
  `capability_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`capability_domain_uuid`,`beneficiary_uuid`),
  KEY `activity_beneficiaries_beneficiary_uuid_foreign` (`beneficiary_uuid`),
  CONSTRAINT `activity_beneficiaries_beneficiary_uuid_foreign` FOREIGN KEY (`beneficiary_uuid`) REFERENCES `beneficiaries` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `activity_beneficiaries_capability_domain_uuid_foreign` FOREIGN KEY (`capability_domain_uuid`) REFERENCES `capability_domains` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_beneficiaries`
--

LOCK TABLES `activity_beneficiaries` WRITE;
/*!40000 ALTER TABLE `activity_beneficiaries` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_beneficiaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_funding_sources`
--

DROP TABLE IF EXISTS `activity_funding_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_funding_sources` (
  `capability_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funding_source_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planned_budget` decimal(14,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`capability_domain_uuid`,`funding_source_uuid`),
  KEY `activity_funding_sources_funding_source_uuid_foreign` (`funding_source_uuid`),
  CONSTRAINT `activity_funding_sources_capability_domain_uuid_foreign` FOREIGN KEY (`capability_domain_uuid`) REFERENCES `capability_domains` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `activity_funding_sources_funding_source_uuid_foreign` FOREIGN KEY (`funding_source_uuid`) REFERENCES `funding_sources` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_funding_sources`
--

LOCK TABLES `activity_funding_sources` WRITE;
/*!40000 ALTER TABLE `activity_funding_sources` DISABLE KEYS */;
INSERT INTO `activity_funding_sources` VALUES ('6de78ac4-f97f-48a5-afe2-510cff4ab642','438af312-de77-4dab-bea3-74f76fb45c12',106056.00),('6de78ac4-f97f-48a5-afe2-510cff4ab642','9655a402-f483-4504-bc37-762c19349d94',227205.00),('6de78ac4-f97f-48a5-afe2-510cff4ab642','f097b72c-2932-4e8a-97de-e8002fe9a2b2',203008.00),('7207f568-b7af-4662-b80f-9e3d34c9ff85','cdec5c14-26e2-48bb-83d0-994205a4615c',82586.00),('7207f568-b7af-4662-b80f-9e3d34c9ff85','f097b72c-2932-4e8a-97de-e8002fe9a2b2',57431.00),('ffb3a636-5a9a-473e-acd1-56d84b8e59df','cdec5c14-26e2-48bb-83d0-994205a4615c',167290.00),('ffb3a636-5a9a-473e-acd1-56d84b8e59df','e38bb49c-124b-48fb-a30c-f4d7ec7e4c4f',70912.00);
/*!40000 ALTER TABLE `activity_funding_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint unsigned DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
INSERT INTO `activity_log` VALUES (1,'structure','Structure created: #1','App\\Models\\Structure','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Autorité Centrale\", \"status\": true, \"parent_uuid\": null, \"abbreviation\": \"STATE-ROOT\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(2,'structure','Structure created: #2','App\\Models\\Structure','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Administration Centrale\", \"status\": true, \"parent_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\", \"abbreviation\": \"ADM-CENT\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(3,'structure','Structure created: #3','App\\Models\\Structure','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Unité de Gestion des Projets\", \"status\": true, \"parent_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"abbreviation\": \"UGP\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(4,'structure','Structure created: #4','App\\Models\\Structure','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"Unité d\'Appui et de Support\", \"status\": true, \"parent_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"abbreviation\": \"UAS\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(5,'structure','Structure created: #5','App\\Models\\Structure','created',5,NULL,NULL,'{\"attributes\": {\"name\": \"Service d\'Assistance Technique\", \"status\": true, \"parent_uuid\": \"dcdb2590-11ac-4e04-bd16-79aed235d8ec\", \"abbreviation\": \"UAS-OPS1\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(6,'structure','Structure created: #6','App\\Models\\Structure','created',6,NULL,NULL,'{\"attributes\": {\"name\": \"Service de Maintenance Systèmes\", \"status\": true, \"parent_uuid\": \"dcdb2590-11ac-4e04-bd16-79aed235d8ec\", \"abbreviation\": \"UAS-OPS2\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(7,'structure','Structure created: #7','App\\Models\\Structure','created',7,NULL,NULL,'{\"attributes\": {\"name\": \"Service Logistique Interne\", \"status\": true, \"parent_uuid\": \"dcdb2590-11ac-4e04-bd16-79aed235d8ec\", \"abbreviation\": \"UAS-OPS3\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(8,'structure','Structure created: #8','App\\Models\\Structure','created',8,NULL,NULL,'{\"attributes\": {\"name\": \"Service Gestion des Projets\", \"status\": true, \"parent_uuid\": \"f1a52261-f519-4324-a668-3941c39d7cd3\", \"abbreviation\": \"GPRJ\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(9,'structure','Structure created: #9','App\\Models\\Structure','created',9,NULL,NULL,'{\"attributes\": {\"name\": \"Cellule Analyse\", \"status\": true, \"parent_uuid\": \"05ce8f09-4551-4332-bb32-86d4db6902b6\", \"abbreviation\": \"GPRJ-V1\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(10,'structure','Structure created: #10','App\\Models\\Structure','created',10,NULL,NULL,'{\"attributes\": {\"name\": \"Cellule Suivi-Évaluation\", \"status\": true, \"parent_uuid\": \"05ce8f09-4551-4332-bb32-86d4db6902b6\", \"abbreviation\": \"GPRJ-V2\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(11,'structure','Structure created: #11','App\\Models\\Structure','created',11,NULL,NULL,'{\"attributes\": {\"name\": \"Cellule Planification\", \"status\": true, \"parent_uuid\": \"05ce8f09-4551-4332-bb32-86d4db6902b6\", \"abbreviation\": \"GPRJ-V3\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(12,'structure','Structure created: #12','App\\Models\\Structure','created',12,NULL,NULL,'{\"attributes\": {\"name\": \"Service Ressources Humaines\", \"status\": true, \"parent_uuid\": \"f1a52261-f519-4324-a668-3941c39d7cd3\", \"abbreviation\": \"GPRH\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(13,'structure','Structure created: #13','App\\Models\\Structure','created',13,NULL,NULL,'{\"attributes\": {\"name\": \"Cellule Recrutement\", \"status\": true, \"parent_uuid\": \"b254bc3a-0e7a-4ca7-9939-6a2172f85c19\", \"abbreviation\": \"GPRH-V1\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(14,'structure','Structure created: #14','App\\Models\\Structure','created',14,NULL,NULL,'{\"attributes\": {\"name\": \"Cellule Développement des Compétences\", \"status\": true, \"parent_uuid\": \"b254bc3a-0e7a-4ca7-9939-6a2172f85c19\", \"abbreviation\": \"GPRH-V2\"}}',NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(15,'user','User created: #1','users','created',1,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Admin\", \"email\": \"admin@admin.com\", \"phone\": \"38086802\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:31','2026-05-18 11:35:31'),(16,'user','User created: #2','users','created',2,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Employé Test\", \"email\": \"employe@employe.com\", \"phone\": \"01234567\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:31','2026-05-18 11:35:31'),(17,'employee','Employee created: #1','App\\Models\\Employee','created',1,NULL,NULL,'{\"attributes\": {\"floor\": \"RDC\", \"office\": \"Bureau 90\", \"job_title\": \"Diamond Worker\", \"can_logged_in\": true, \"structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\"}}',NULL,'2026-05-18 11:35:31','2026-05-18 11:35:31'),(18,'user','User created: #3','users','created',3,NULL,NULL,'{\"attributes\": {\"lang\": \"en\", \"name\": \"Kaela O\'Reilly\", \"email\": \"maggio.charity@example.org\", \"phone\": \"351-413-3023\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(19,'user','User created: #4','users','created',4,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Nolan Berge\", \"email\": \"kim.harber@example.com\", \"phone\": \"+1 (458) 758-2199\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(20,'user','User created: #5','users','created',5,NULL,NULL,'{\"attributes\": {\"lang\": \"en\", \"name\": \"Emie Strosin\", \"email\": \"qveum@example.org\", \"phone\": \"1-336-357-5408\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(21,'user','User created: #6','users','created',6,NULL,NULL,'{\"attributes\": {\"lang\": \"ar\", \"name\": \"Prof. Emile Lubowitz DDS\", \"email\": null, \"phone\": \"+1-915-241-1041\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(22,'user','User created: #7','users','created',7,NULL,NULL,'{\"attributes\": {\"lang\": \"en\", \"name\": \"Niko Reynolds\", \"email\": \"vstamm@example.org\", \"phone\": \"+1-219-773-8646\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(23,'user','User created: #8','users','created',8,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Gerson Thiel\", \"email\": \"quitzon.erik@example.com\", \"phone\": \"573-970-8136\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(24,'user','User created: #9','users','created',9,NULL,NULL,'{\"attributes\": {\"lang\": \"ar\", \"name\": \"Rylan West\", \"email\": \"qhammes@example.net\", \"phone\": \"1-989-780-6080\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(25,'user','User created: #10','users','created',10,NULL,NULL,'{\"attributes\": {\"lang\": \"ar\", \"name\": \"Myrtis Gleichner\", \"email\": \"josefina08@example.net\", \"phone\": \"+1-332-540-9960\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(26,'user','User created: #11','users','created',11,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Ms. Birdie Considine\", \"email\": \"rodrigo03@example.com\", \"phone\": \"+1 (954) 730-6733\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(27,'user','User created: #12','users','created',12,NULL,NULL,'{\"attributes\": {\"lang\": \"en\", \"name\": \"Kay Rath\", \"email\": null, \"phone\": \"+1 (478) 442-1601\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(28,'user','User created: #13','users','created',13,NULL,NULL,'{\"attributes\": {\"lang\": \"ar\", \"name\": \"Boyd Boyer\", \"email\": \"fkoepp@example.com\", \"phone\": \"+1 (747) 545-4924\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(29,'user','User created: #14','users','created',14,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Arnoldo Hauck\", \"email\": \"rowland77@example.com\", \"phone\": \"+1-903-568-4268\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(30,'user','User created: #15','users','created',15,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Dr. Akeem McKenzie I\", \"email\": \"clay52@example.net\", \"phone\": \"+1.669.443.7399\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(31,'user','User created: #16','users','created',16,NULL,NULL,'{\"attributes\": {\"lang\": \"ar\", \"name\": \"Arnulfo Mayert Jr.\", \"email\": \"general.hilpert@example.org\", \"phone\": \"1-386-507-4167\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(32,'user','User created: #17','users','created',17,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Amalia Bins\", \"email\": \"herman.giovanna@example.net\", \"phone\": \"434-985-7248\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:46'),(33,'user','User created: #18','users','created',18,NULL,NULL,'{\"attributes\": {\"lang\": \"en\", \"name\": \"Cristal Bernier\", \"email\": \"gmarquardt@example.org\", \"phone\": \"425-495-1078\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(34,'user','User created: #19','users','created',19,NULL,NULL,'{\"attributes\": {\"lang\": \"ar\", \"name\": \"Dr. Cordelia Tromp PhD\", \"email\": \"alessia.buckridge@example.com\", \"phone\": \"+1-580-358-7081\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(35,'user','User created: #20','users','created',20,NULL,NULL,'{\"attributes\": {\"lang\": \"en\", \"name\": \"Karianne Hoeger\", \"email\": \"casper.odie@example.org\", \"phone\": \"+1 (347) 627-5118\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(36,'user','User created: #21','users','created',21,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Yazmin Gusikowski\", \"email\": \"brown.burley@example.com\", \"phone\": \"+1.302.270.2505\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(37,'user','User created: #22','users','created',22,NULL,NULL,'{\"attributes\": {\"lang\": \"ar\", \"name\": \"Grayson Doyle\", \"email\": \"tgutmann@example.net\", \"phone\": \"+1 (325) 448-1429\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(38,'user','User created: #23','users','created',23,NULL,NULL,'{\"attributes\": {\"lang\": \"ar\", \"name\": \"Stefan Farrell\", \"email\": \"meda.daugherty@example.org\", \"phone\": \"1-231-909-1633\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(39,'user','User created: #24','users','created',24,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Maudie Gleason Jr.\", \"email\": null, \"phone\": \"+1.860.647.8400\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(40,'user','User created: #25','users','created',25,NULL,NULL,'{\"attributes\": {\"lang\": \"ar\", \"name\": \"Liza Auer\", \"email\": \"luisa.rohan@example.org\", \"phone\": \"315.379.3508\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(41,'user','User created: #26','users','created',26,NULL,NULL,'{\"attributes\": {\"lang\": \"en\", \"name\": \"Mrs. Leann Schultz\", \"email\": \"oren71@example.net\", \"phone\": \"248.748.5077\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(42,'user','User created: #27','users','created',27,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Alexandrine Weber\", \"email\": \"wilber.feil@example.org\", \"phone\": \"+18053130059\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(43,'user','User created: #28','users','created',28,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Andre Willms\", \"email\": \"margarett43@example.com\", \"phone\": \"+1-847-730-6798\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(44,'user','User created: #29','users','created',29,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Prof. Gregory Goodwin\", \"email\": \"zpowlowski@example.net\", \"phone\": \"(520) 619-9793\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(45,'user','User created: #30','users','created',30,NULL,NULL,'{\"attributes\": {\"lang\": \"fr\", \"name\": \"Kattie Runolfsson\", \"email\": null, \"phone\": \"918.742.7175\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(46,'user','User created: #31','users','created',31,NULL,NULL,'{\"attributes\": {\"lang\": \"ar\", \"name\": \"Verlie Rippin\", \"email\": null, \"phone\": \"812-307-1827\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(47,'user','User created: #32','users','created',32,NULL,NULL,'{\"attributes\": {\"lang\": \"ar\", \"name\": \"Tyshawn Mraz V\", \"email\": null, \"phone\": \"+1 (909) 893-5671\", \"avatar\": null, \"status\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(48,'employee','Employee created: #2','App\\Models\\Employee','created',2,NULL,NULL,'{\"attributes\": {\"floor\": \"2\", \"office\": \"Bureau 66\", \"job_title\": \"Tile Setter OR Marbl\", \"can_logged_in\": true, \"structure_uuid\": \"dcdb2590-11ac-4e04-bd16-79aed235d8ec\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(49,'employee','Employee created: #3','App\\Models\\Employee','created',3,NULL,NULL,'{\"attributes\": {\"floor\": \"1\", \"office\": \"Bureau 21\", \"job_title\": \"Pressure Vessel Insp\", \"can_logged_in\": true, \"structure_uuid\": \"05ce8f09-4551-4332-bb32-86d4db6902b6\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(50,'employee','Employee created: #4','App\\Models\\Employee','created',4,NULL,NULL,'{\"attributes\": {\"floor\": \"B\", \"office\": \"Bureau 11\", \"job_title\": \"Woodworker\", \"can_logged_in\": true, \"structure_uuid\": \"05ce8f09-4551-4332-bb32-86d4db6902b6\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(51,'employee','Employee created: #5','App\\Models\\Employee','created',5,NULL,NULL,'{\"attributes\": {\"floor\": \"2\", \"office\": \"Bureau 64\", \"job_title\": \"Probation Officers a\", \"can_logged_in\": false, \"structure_uuid\": \"61c3f03b-a3a8-43d4-b917-40e1c6c5d089\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(52,'employee','Employee created: #6','App\\Models\\Employee','created',6,NULL,NULL,'{\"attributes\": {\"floor\": \"RDC\", \"office\": \"Bureau 72\", \"job_title\": \"Chemical Engineer\", \"can_logged_in\": true, \"structure_uuid\": \"f1a52261-f519-4324-a668-3941c39d7cd3\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(53,'employee','Employee created: #7','App\\Models\\Employee','created',7,NULL,NULL,'{\"attributes\": {\"floor\": \"B\", \"office\": \"Bureau 97\", \"job_title\": \"Offset Lithographic \", \"can_logged_in\": true, \"structure_uuid\": \"c4196136-dc09-48c8-8b83-baa6d45b7eff\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(54,'employee','Employee created: #8','App\\Models\\Employee','created',8,NULL,NULL,'{\"attributes\": {\"floor\": \"B\", \"office\": \"Bureau 90\", \"job_title\": \"Protective Service W\", \"can_logged_in\": true, \"structure_uuid\": \"05ce8f09-4551-4332-bb32-86d4db6902b6\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(55,'employee','Employee created: #9','App\\Models\\Employee','created',9,NULL,NULL,'{\"attributes\": {\"floor\": \"1\", \"office\": \"Bureau 33\", \"job_title\": \"Soil Conservationist\", \"can_logged_in\": true, \"structure_uuid\": \"150dc506-49c1-4b32-a5b4-92ad8326d634\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(56,'employee','Employee created: #10','App\\Models\\Employee','created',10,NULL,NULL,'{\"attributes\": {\"floor\": \"2\", \"office\": \"Bureau 81\", \"job_title\": \"Court Clerk\", \"can_logged_in\": true, \"structure_uuid\": \"4dc75397-084c-41bb-8fb1-e596f3dc2a65\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(57,'employee','Employee created: #11','App\\Models\\Employee','created',11,NULL,NULL,'{\"attributes\": {\"floor\": \"1\", \"office\": \"Bureau 42\", \"job_title\": \"Machinery Maintenanc\", \"can_logged_in\": false, \"structure_uuid\": \"3e339ff6-13ee-4efc-8eb0-872c6c3c1972\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(58,'employee','Employee created: #12','App\\Models\\Employee','created',12,NULL,NULL,'{\"attributes\": {\"floor\": \"2\", \"office\": \"Bureau 50\", \"job_title\": \"Freight and Material\", \"can_logged_in\": true, \"structure_uuid\": \"3e339ff6-13ee-4efc-8eb0-872c6c3c1972\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(59,'employee','Employee created: #13','App\\Models\\Employee','created',13,NULL,NULL,'{\"attributes\": {\"floor\": \"B\", \"office\": \"Bureau 60\", \"job_title\": \"Special Force\", \"can_logged_in\": true, \"structure_uuid\": \"9740bc28-b8ec-423e-9243-1005b4594dd8\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(60,'employee','Employee created: #14','App\\Models\\Employee','created',14,NULL,NULL,'{\"attributes\": {\"floor\": \"RDC\", \"office\": \"Bureau 20\", \"job_title\": \"Psychiatric Technici\", \"can_logged_in\": true, \"structure_uuid\": \"c4196136-dc09-48c8-8b83-baa6d45b7eff\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(61,'employee','Employee created: #15','App\\Models\\Employee','created',15,NULL,NULL,'{\"attributes\": {\"floor\": \"2\", \"office\": \"Bureau 88\", \"job_title\": \"Freight Inspector\", \"can_logged_in\": true, \"structure_uuid\": \"f1a52261-f519-4324-a668-3941c39d7cd3\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(62,'employee','Employee created: #16','App\\Models\\Employee','created',16,NULL,NULL,'{\"attributes\": {\"floor\": \"B\", \"office\": \"Bureau 74\", \"job_title\": \"Utility Meter Reader\", \"can_logged_in\": true, \"structure_uuid\": \"150dc506-49c1-4b32-a5b4-92ad8326d634\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(63,'employee','Employee created: #17','App\\Models\\Employee','created',17,NULL,NULL,'{\"attributes\": {\"floor\": \"1\", \"office\": \"Bureau 11\", \"job_title\": \"Transportation Worke\", \"can_logged_in\": true, \"structure_uuid\": \"61c3f03b-a3a8-43d4-b917-40e1c6c5d089\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(64,'employee','Employee created: #18','App\\Models\\Employee','created',18,NULL,NULL,'{\"attributes\": {\"floor\": \"1\", \"office\": \"Bureau 07\", \"job_title\": \"Instrument Sales Rep\", \"can_logged_in\": true, \"structure_uuid\": \"3e339ff6-13ee-4efc-8eb0-872c6c3c1972\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(65,'employee','Employee created: #19','App\\Models\\Employee','created',19,NULL,NULL,'{\"attributes\": {\"floor\": \"1\", \"office\": \"Bureau 45\", \"job_title\": \"Actuary\", \"can_logged_in\": true, \"structure_uuid\": \"f1a52261-f519-4324-a668-3941c39d7cd3\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(66,'employee','Employee created: #20','App\\Models\\Employee','created',20,NULL,NULL,'{\"attributes\": {\"floor\": \"RDC\", \"office\": \"Bureau 56\", \"job_title\": \"Driver-Sales Worker\", \"can_logged_in\": true, \"structure_uuid\": \"b254bc3a-0e7a-4ca7-9939-6a2172f85c19\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(67,'employee','Employee created: #21','App\\Models\\Employee','created',21,NULL,NULL,'{\"attributes\": {\"floor\": \"B\", \"office\": \"Bureau 53\", \"job_title\": \"Engraver\", \"can_logged_in\": true, \"structure_uuid\": \"c4196136-dc09-48c8-8b83-baa6d45b7eff\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(68,'employee','Employee created: #22','App\\Models\\Employee','created',22,NULL,NULL,'{\"attributes\": {\"floor\": \"RDC\", \"office\": \"Bureau 95\", \"job_title\": \"Real Estate Broker\", \"can_logged_in\": true, \"structure_uuid\": \"b254bc3a-0e7a-4ca7-9939-6a2172f85c19\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(69,'employee','Employee created: #23','App\\Models\\Employee','created',23,NULL,NULL,'{\"attributes\": {\"floor\": \"2\", \"office\": \"Bureau 76\", \"job_title\": \"Copy Machine Operato\", \"can_logged_in\": false, \"structure_uuid\": \"4dc75397-084c-41bb-8fb1-e596f3dc2a65\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(70,'employee','Employee created: #24','App\\Models\\Employee','created',24,NULL,NULL,'{\"attributes\": {\"floor\": \"2\", \"office\": \"Bureau 56\", \"job_title\": \"Cartoonist\", \"can_logged_in\": true, \"structure_uuid\": \"3e339ff6-13ee-4efc-8eb0-872c6c3c1972\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(71,'employee','Employee created: #25','App\\Models\\Employee','created',25,NULL,NULL,'{\"attributes\": {\"floor\": \"2\", \"office\": \"Bureau 79\", \"job_title\": \"Fabric Pressers\", \"can_logged_in\": true, \"structure_uuid\": \"a961c2e7-c64c-49cc-a8a6-85ce2525aa92\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(72,'employee','Employee created: #26','App\\Models\\Employee','created',26,NULL,NULL,'{\"attributes\": {\"floor\": \"1\", \"office\": \"Bureau 09\", \"job_title\": \"Manager of Weapons S\", \"can_logged_in\": true, \"structure_uuid\": \"ea156fac-eba9-4541-8032-4bbcd61eab54\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(73,'employee','Employee created: #27','App\\Models\\Employee','created',27,NULL,NULL,'{\"attributes\": {\"floor\": \"2\", \"office\": \"Bureau 20\", \"job_title\": \"Religious Worker\", \"can_logged_in\": true, \"structure_uuid\": \"61c3f03b-a3a8-43d4-b917-40e1c6c5d089\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(74,'employee','Employee created: #28','App\\Models\\Employee','created',28,NULL,NULL,'{\"attributes\": {\"floor\": \"B\", \"office\": \"Bureau 47\", \"job_title\": \"Food Preparation Wor\", \"can_logged_in\": true, \"structure_uuid\": \"a961c2e7-c64c-49cc-a8a6-85ce2525aa92\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(75,'employee','Employee created: #29','App\\Models\\Employee','created',29,NULL,NULL,'{\"attributes\": {\"floor\": \"RDC\", \"office\": \"Bureau 73\", \"job_title\": \"Physician Assistant\", \"can_logged_in\": false, \"structure_uuid\": \"f1a52261-f519-4324-a668-3941c39d7cd3\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(76,'employee','Employee created: #30','App\\Models\\Employee','created',30,NULL,NULL,'{\"attributes\": {\"floor\": \"1\", \"office\": \"Bureau 71\", \"job_title\": \"Septic Tank Servicer\", \"can_logged_in\": false, \"structure_uuid\": \"b254bc3a-0e7a-4ca7-9939-6a2172f85c19\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(77,'employee','Employee created: #31','App\\Models\\Employee','created',31,NULL,NULL,'{\"attributes\": {\"floor\": \"RDC\", \"office\": \"Bureau 37\", \"job_title\": \"Gas Pumping Station \", \"can_logged_in\": false, \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(78,'currency','Currency created: #1','App\\Models\\Currency','created',1,NULL,NULL,'{\"attributes\": {\"code\": \"MRU\", \"name\": \"Ouguiya\", \"status\": true, \"is_default\": false}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(79,'currency','Currency created: #2','App\\Models\\Currency','created',2,NULL,NULL,'{\"attributes\": {\"code\": \"XOF\", \"name\": \"Franc CFA\", \"status\": true, \"is_default\": true}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(80,'currency','Currency created: #3','App\\Models\\Currency','created',3,NULL,NULL,'{\"attributes\": {\"code\": \"EUR\", \"name\": \"Euro\", \"status\": true, \"is_default\": false}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(81,'currency','Currency created: #4','App\\Models\\Currency','created',4,NULL,NULL,'{\"attributes\": {\"code\": \"USD\", \"name\": \"Dollar américain\", \"status\": true, \"is_default\": false}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(82,'funding_source','Funding source created: #1','App\\Models\\FundingSource','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Banque mondiale\", \"status\": true, \"description\": \"Partenaire de financement institutionnel global\", \"structure_uuid\": null}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(83,'funding_source','Funding source created: #2','App\\Models\\FundingSource','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Union Européenne\", \"status\": true, \"description\": \"Soutien au développement durable\", \"structure_uuid\": null}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(84,'funding_source','Funding source created: #3','App\\Models\\FundingSource','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Fonds propre de l\'État\", \"status\": true, \"description\": \"Financement public national\", \"structure_uuid\": null}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(85,'funding_source','Funding source created: #4','App\\Models\\FundingSource','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"Partenariat public-privé\", \"status\": true, \"description\": \"Collaboration entre secteurs public et privé\", \"structure_uuid\": null}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(86,'funding_source','Funding source created: #5','App\\Models\\FundingSource','created',5,NULL,NULL,'{\"attributes\": {\"name\": \"Programme des Nations Unies pour le Développement (PNUD)\", \"status\": true, \"description\": \"Agence de l\'ONU dédiée au développement\", \"structure_uuid\": null}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(87,'funding_source','Funding source created: #6','App\\Models\\FundingSource','created',6,NULL,NULL,'{\"attributes\": {\"name\": \"Budget National\", \"status\": true, \"description\": \"Financement interne par l\'État\", \"structure_uuid\": null}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(88,'action_plan','Action plan created: #1','App\\Models\\ActionPlan','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Plan d\'action stratégique 2025\", \"status\": true, \"end_date\": \"2025-12-31\", \"reference\": \"PA-STRAT-2025\", \"start_date\": \"2025-01-01\", \"description\": \"Plan d\'action opérationnel prioritaire pour l\'année 2025.\", \"structure_uuid\": \"c4196136-dc09-48c8-8b83-baa6d45b7eff\", \"responsible_uuid\": null}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(89,'region','Region created: #1','App\\Models\\Region','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Williamsonmouth\", \"status\": true, \"latitude\": -1.536991, \"longitude\": -143.734406}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(90,'region','Region created: #2','App\\Models\\Region','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Schillerfurt\", \"status\": true, \"latitude\": -68.519523, \"longitude\": 47.308025}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(91,'region','Region created: #3','App\\Models\\Region','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"East Madelynn\", \"status\": true, \"latitude\": 41.629353, \"longitude\": -65.746896}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(92,'region','Region created: #4','App\\Models\\Region','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"Lake Loraberg\", \"status\": true, \"latitude\": 37.881108, \"longitude\": -10.920095}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(93,'region','Region created: #5','App\\Models\\Region','created',5,NULL,NULL,'{\"attributes\": {\"name\": \"Amariburgh\", \"status\": true, \"latitude\": -56.271733, \"longitude\": -153.256731}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(94,'department','Department created: #1','App\\Models\\Department','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Priceshire\", \"status\": true, \"latitude\": -55.296821, \"longitude\": -70.157211, \"region_uuid\": \"febe6726-ecfa-4c53-b338-400e639559aa\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(95,'department','Department created: #2','App\\Models\\Department','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"West Jaidamouth\", \"status\": true, \"latitude\": -43.958653, \"longitude\": -154.51186, \"region_uuid\": \"6e30fd97-9b0c-425b-be85-6b8d2ba7b9e0\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(96,'department','Department created: #3','App\\Models\\Department','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"New Cassiemouth\", \"status\": true, \"latitude\": -51.084054, \"longitude\": 101.316337, \"region_uuid\": \"2606b718-6f19-4c82-a9cd-43c453f1c095\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(97,'department','Department created: #4','App\\Models\\Department','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"Port Lupeberg\", \"status\": true, \"latitude\": 14.288961, \"longitude\": 128.10701, \"region_uuid\": \"6e30fd97-9b0c-425b-be85-6b8d2ba7b9e0\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(98,'department','Department created: #5','App\\Models\\Department','created',5,NULL,NULL,'{\"attributes\": {\"name\": \"Hettingerstad\", \"status\": true, \"latitude\": -68.669418, \"longitude\": 12.977692, \"region_uuid\": \"742b399a-5971-447e-86e4-0e648eb61d36\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(99,'procurement_mode','Procurement mode created: #1','App\\Models\\ProcurementMode','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Appel d\'offres ouvert\", \"status\": true, \"duration\": 5, \"contract_type_uuid\": null}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(100,'procurement_mode','Procurement mode created: #2','App\\Models\\ProcurementMode','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Appel d\'offres restreint\", \"status\": true, \"duration\": 15, \"contract_type_uuid\": null}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(101,'procurement_mode','Procurement mode created: #3','App\\Models\\ProcurementMode','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Entente directe\", \"status\": false, \"duration\": 20, \"contract_type_uuid\": null}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(102,'municipality','Municipality created: #1','App\\Models\\Municipality','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Port Camylle\", \"status\": true, \"latitude\": -4.840597, \"longitude\": 92.797775, \"department_uuid\": \"c3246e1b-6260-4279-b845-d60eb30fba2b\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(103,'municipality','Municipality created: #2','App\\Models\\Municipality','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Webertown\", \"status\": true, \"latitude\": -63.473368, \"longitude\": -175.734466, \"department_uuid\": \"de14f568-ff4e-4e87-a448-f307a8c3c9c5\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(104,'municipality','Municipality created: #3','App\\Models\\Municipality','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Lake Ila\", \"status\": true, \"latitude\": 66.79401, \"longitude\": 22.161725, \"department_uuid\": \"fe039939-2914-459c-a9eb-9f9833fd994f\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(105,'municipality','Municipality created: #4','App\\Models\\Municipality','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"North Lillaville\", \"status\": true, \"latitude\": 9.19405, \"longitude\": 41.021771, \"department_uuid\": \"ec9ddd80-db02-433e-8843-98a3ce883b56\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(106,'municipality','Municipality created: #5','App\\Models\\Municipality','created',5,NULL,NULL,'{\"attributes\": {\"name\": \"Port Kaley\", \"status\": true, \"latitude\": 42.307056, \"longitude\": -29.441497, \"department_uuid\": \"fe039939-2914-459c-a9eb-9f9833fd994f\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(107,'program','Program created: #1','action_domains','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Programme A\", \"budget\": 178000, \"status\": \"preparation\", \"end_date\": \"2025-12-31\", \"reference\": \"PRG-00A\", \"start_date\": \"2025-01-01\", \"description\": null, \"currency_uuid\": null, \"responsible_uuid\": \"47e95852-f4f4-4e64-940f-9adbd5b0edfb\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(108,'program','Program created: #2','action_domains','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Programme B\", \"budget\": 407366, \"status\": \"preparation\", \"end_date\": \"2025-11-30\", \"reference\": \"PRG-00B\", \"start_date\": \"2025-02-01\", \"description\": null, \"currency_uuid\": null, \"responsible_uuid\": \"8cb07c77-5be7-4824-95d4-3b4ec75c146b\"}}',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(109,'program','Program created: #3','action_domains','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Programme C\", \"budget\": 87485, \"status\": \"preparation\", \"end_date\": \"2025-09-30\", \"reference\": \"PROG-00C\", \"start_date\": \"2025-03-01\", \"description\": null, \"currency_uuid\": null, \"responsible_uuid\": \"e4dea000-72cf-40ae-929a-20e33a36fe50\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(110,'project','Project created: #1','strategic_domains','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Project A\", \"budget\": 94201, \"status\": \"preparation\", \"end_date\": \"2025-12-31\", \"objective\": null, \"reference\": \"PROJ-00A\", \"start_date\": \"2025-01-01\", \"currency_uuid\": null, \"expected_results\": null, \"responsible_uuid\": \"a3011198-148b-4119-b64c-c415b7cd925e\", \"action_domain_uuid\": \"efec6c04-d518-4d8f-a6b7-4ed7c2dc2db9\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(111,'project','Project created: #2','strategic_domains','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Project B\", \"budget\": 227260, \"status\": \"preparation\", \"end_date\": \"2025-11-30\", \"objective\": null, \"reference\": \"PROJ-00B\", \"start_date\": \"2025-02-01\", \"currency_uuid\": null, \"expected_results\": null, \"responsible_uuid\": \"b9d5a85d-cd9d-47e6-ba1e-f5be6f11bc7f\", \"action_domain_uuid\": \"62e1ae2b-f482-4788-9f2a-a4ba99bef1df\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(112,'project','Project created: #3','strategic_domains','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Project C\", \"budget\": 391946, \"status\": \"preparation\", \"end_date\": \"2025-09-30\", \"objective\": null, \"reference\": \"PROJ-00C\", \"start_date\": \"2025-03-01\", \"currency_uuid\": null, \"expected_results\": null, \"responsible_uuid\": \"32233eba-1f16-42c5-b1f4-138ed81e30c7\", \"action_domain_uuid\": \"62e7e327-083a-49e8-b40e-2fccb49d6a6c\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(113,'project_owner','Project owner created: #1','App\\Models\\ProjectOwner','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Ministère des Travaux Publics\", \"type\": \"Public\", \"email\": \"mtp@example.gov\", \"phone\": \"+22212345678\", \"status\": true, \"structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(114,'project_owner','Project owner created: #2','App\\Models\\ProjectOwner','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Entreprise BTP Sahel\", \"type\": \"Privé\", \"email\": \"contact@btpsahel.com\", \"phone\": \"+22298765432\", \"status\": true, \"structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(115,'project_owner','Project owner created: #3','App\\Models\\ProjectOwner','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"ONG Développement Rural\", \"type\": \"ONG\", \"email\": \"info@ongdr.org\", \"phone\": \"+22233445566\", \"status\": false, \"structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(116,'delegated_project_owner','Delegate project owner created: #1','App\\Models\\DelegatedProjectOwner','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Maître d\'ouvrage délégué A\", \"email\": \"delegue.a@example.com\", \"phone\": \"22330011\", \"status\": true, \"project_owner_uuid\": \"cf577d7f-f6fc-440c-981e-87d9494e46dc\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(117,'delegated_project_owner','Delegate project owner created: #2','App\\Models\\DelegatedProjectOwner','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Maître d\'ouvrage délégué B\", \"email\": \"delegue.b@example.com\", \"phone\": \"22330022\", \"status\": false, \"project_owner_uuid\": \"cf577d7f-f6fc-440c-981e-87d9494e46dc\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(118,'activity','Activity created: #1','capability_domains','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Formation des agents\", \"budget\": 140017, \"status\": \"preparation\", \"end_date\": \"2025-01-30\", \"reference\": \"ACT-001\", \"start_date\": \"2025-01-15\", \"description\": null, \"currency_uuid\": null, \"responsible_uuid\": \"58f41856-9d3f-4ca5-ae52-44282b0799a9\", \"strategic_domain_uuid\": \"48b76f89-38ad-43d6-b7e2-7fbdb6fa074e\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(119,'activity','Activity created: #2','capability_domains','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Campagne de sensibilisation\", \"budget\": 238202, \"status\": \"preparation\", \"end_date\": \"2025-03-15\", \"reference\": \"ACT-002\", \"start_date\": \"2025-03-01\", \"description\": null, \"currency_uuid\": null, \"responsible_uuid\": \"32233eba-1f16-42c5-b1f4-138ed81e30c7\", \"strategic_domain_uuid\": \"48b76f89-38ad-43d6-b7e2-7fbdb6fa074e\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(120,'activity','Activity created: #3','capability_domains','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Distribution de kits\", \"budget\": 536269, \"status\": \"preparation\", \"end_date\": \"2025-06-01\", \"reference\": \"ACT-003\", \"start_date\": \"2025-05-01\", \"description\": null, \"currency_uuid\": null, \"responsible_uuid\": \"10fc0325-3da5-4318-9df4-af1cd01351a2\", \"strategic_domain_uuid\": \"dc5dbf4b-9a50-4752-b4f1-dd9407133087\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(121,'beneficiary','Beneficiary created: #1','App\\Models\\Beneficiary','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Albertha Turner\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(122,'beneficiary','Beneficiary created: #2','App\\Models\\Beneficiary','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Vicente Feil\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(123,'beneficiary','Beneficiary created: #3','App\\Models\\Beneficiary','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Dr. Jazmyn Bergnaum PhD\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(124,'beneficiary','Beneficiary created: #4','App\\Models\\Beneficiary','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"Maxwell Bartoletti\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(125,'beneficiary','Beneficiary created: #5','App\\Models\\Beneficiary','created',5,NULL,NULL,'{\"attributes\": {\"name\": \"Loren Cummerata\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(126,'stakeholder','Stakeholder created: #1','App\\Models\\Stakeholder','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Kendra O\'Conner\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(127,'stakeholder','Stakeholder created: #2','App\\Models\\Stakeholder','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Daphney Smitham\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(128,'stakeholder','Stakeholder created: #3','App\\Models\\Stakeholder','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Jerald Goyette\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(129,'stakeholder','Stakeholder created: #4','App\\Models\\Stakeholder','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"Leanna Eichmann\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(130,'stakeholder','Stakeholder created: #5','App\\Models\\Stakeholder','created',5,NULL,NULL,'{\"attributes\": {\"name\": \"Dr. Ebba Huel\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(131,'action','Action created: #1','actions','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Réhabilitation d\'écoles primaires\", \"risks\": \"Manque de financement, retard de livraison\", \"budget\": null, \"status\": \"in_progress\", \"impacts\": \"Amélioration des conditions d\'apprentissage\", \"end_date\": \"2025-06-30T00:00:00.000000Z\", \"priority\": \"high\", \"reference\": null, \"risk_level\": \"medium\", \"start_date\": \"2025-01-10T00:00:00.000000Z\", \"description\": \"Réhabilitation de 10 écoles en milieu rural.\", \"currency_uuid\": null, \"prerequisites\": \"Étude technique préalable\", \"structure_uuid\": \"c4196136-dc09-48c8-8b83-baa6d45b7eff\", \"action_plan_uuid\": \"fb389df6-3f85-4c92-9f94-6ce8a5e84c18\", \"generate_document_type\": \"ppm\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(132,'action','Action updated: #1','actions','updated',1,NULL,NULL,'{\"old\": {\"reference\": null}, \"attributes\": {\"reference\": \"UAS-OPS1_ACT001\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(133,'action','Action created: #2','actions','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Construction d\'un centre de santé\", \"risks\": \"Opposition communautaire, intempéries\", \"budget\": null, \"status\": \"in_progress\", \"impacts\": \"Renforcement du système de santé local\", \"end_date\": \"2025-09-01T00:00:00.000000Z\", \"priority\": \"medium\", \"reference\": null, \"risk_level\": \"high\", \"start_date\": \"2025-03-01T00:00:00.000000Z\", \"description\": \"Construction d\'un nouveau centre de santé dans la commune A.\", \"currency_uuid\": null, \"prerequisites\": \"Validation du site par les autorités locales\", \"structure_uuid\": \"c4196136-dc09-48c8-8b83-baa6d45b7eff\", \"action_plan_uuid\": \"fb389df6-3f85-4c92-9f94-6ce8a5e84c18\", \"generate_document_type\": \"paa\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(134,'action','Action updated: #2','actions','updated',2,NULL,NULL,'{\"old\": {\"reference\": null}, \"attributes\": {\"reference\": \"UAS-OPS1_ACT002\"}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(135,'payment_mode','Payment mode created: #1','App\\Models\\PaymentMode','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Espèces\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(136,'payment_mode','Payment mode created: #2','App\\Models\\PaymentMode','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Carte de crédit\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(137,'payment_mode','Payment mode created: #3','App\\Models\\PaymentMode','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Virement bancaire\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(138,'payment_mode','Payment mode created: #4','App\\Models\\PaymentMode','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"Mobile Money\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(139,'payment_mode','Payment mode created: #5','App\\Models\\PaymentMode','created',5,NULL,NULL,'{\"attributes\": {\"name\": \"PayPal\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(140,'expense_type','Expense type created: #1','App\\Models\\ExpenseType','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Depense 1\", \"status\": false}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(141,'expense_type','Expense type created: #2','App\\Models\\ExpenseType','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Depense 2\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(142,'expense_type','Expense type created: #3','App\\Models\\ExpenseType','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Depense 3\", \"status\": false}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(143,'expense_type','Expense type created: #4','App\\Models\\ExpenseType','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"Depense 4\", \"status\": false}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(144,'budget_type','Budget type created: #1','App\\Models\\BudgetType','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Marketing\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(145,'budget_type','Budget type created: #2','App\\Models\\BudgetType','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Transport\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(146,'budget_type','Budget type created: #3','App\\Models\\BudgetType','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Épargne\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(147,'budget_type','Budget type created: #4','App\\Models\\BudgetType','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"Loyer\", \"status\": true}}',NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(148,'action_fund_disbursement','Action fund disbursement created: #1','action_fund_disbursements','created',1,NULL,NULL,'{\"attributes\": {\"reference\": null, \"phase_uuid\": \"97806701-866d-45fd-99a7-80b17186d056\", \"tax_number\": null, \"action_uuid\": \"4d20ea52-f876-43d0-9ddc-e97b285030ed\", \"description\": \"Premier décaissement pour formation.\", \"payment_date\": \"2025-01-25\", \"supplier_name\": null, \"execution_date\": \"2025-01-20\", \"payment_amount\": 5000, \"signature_date\": \"2025-01-10\", \"contract_number\": null, \"budget_type_uuid\": \"72f166f0-c083-4ab7-8a06-74106ff96355\", \"cheque_reference\": \"CHQ-001\", \"operation_number\": \"OP-001\", \"payment_mode_uuid\": \"5f3ac087-77c8-4b26-8bd5-3feec7623567\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(149,'action_fund_disbursement','Action fund disbursement updated: #1','action_fund_disbursements','updated',1,NULL,NULL,'{\"old\": {\"reference\": null}, \"attributes\": {\"reference\": \"DEC-UAS-OPS1-UAS-OPS1_ACT001-1-001\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(150,'action_fund_disbursement','Action fund disbursement created: #2','action_fund_disbursements','created',2,NULL,NULL,'{\"attributes\": {\"reference\": null, \"phase_uuid\": \"97806701-866d-45fd-99a7-80b17186d056\", \"tax_number\": null, \"action_uuid\": \"4d20ea52-f876-43d0-9ddc-e97b285030ed\", \"description\": \"Décaissement pour campagne de sensibilisation.\", \"payment_date\": \"2025-02-28\", \"supplier_name\": null, \"execution_date\": \"2025-02-20\", \"payment_amount\": 7500, \"signature_date\": \"2025-02-15\", \"contract_number\": null, \"budget_type_uuid\": \"72f166f0-c083-4ab7-8a06-74106ff96355\", \"cheque_reference\": \"CHQ-002\", \"operation_number\": \"OP-002\", \"payment_mode_uuid\": \"5f3ac087-77c8-4b26-8bd5-3feec7623567\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(151,'action_fund_disbursement','Action fund disbursement updated: #2','action_fund_disbursements','updated',2,NULL,NULL,'{\"old\": {\"reference\": null}, \"attributes\": {\"reference\": \"DEC-UAS-OPS1-UAS-OPS1_ACT001-1-002\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(152,'action_fund_disbursement','Action fund disbursement created: #3','action_fund_disbursements','created',3,NULL,NULL,'{\"attributes\": {\"reference\": null, \"phase_uuid\": \"97806701-866d-45fd-99a7-80b17186d056\", \"tax_number\": null, \"action_uuid\": \"4d20ea52-f876-43d0-9ddc-e97b285030ed\", \"description\": \"Décaissement pour distribution de kits.\", \"payment_date\": \"2025-04-15\", \"supplier_name\": null, \"execution_date\": \"2025-04-10\", \"payment_amount\": 11000, \"signature_date\": \"2025-04-01\", \"contract_number\": null, \"budget_type_uuid\": \"72f166f0-c083-4ab7-8a06-74106ff96355\", \"cheque_reference\": \"CHQ-003\", \"operation_number\": \"OP-003\", \"payment_mode_uuid\": \"5f3ac087-77c8-4b26-8bd5-3feec7623567\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(153,'action_fund_disbursement','Action fund disbursement updated: #3','action_fund_disbursements','updated',3,NULL,NULL,'{\"old\": {\"reference\": null}, \"attributes\": {\"reference\": \"DEC-UAS-OPS1-UAS-OPS1_ACT001-1-003\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(154,'action_fund_receipt','Action fund receipt created: #1','App\\Models\\ActionFundReceipt','created',1,NULL,NULL,'{\"attributes\": {\"reference\": \"AFR-2025-001\", \"action_uuid\": \"4d20ea52-f876-43d0-9ddc-e97b285030ed\", \"receipt_date\": \"2025-01-10\", \"currency_uuid\": \"b5829a14-e756-4328-9693-24519dd4baf4\", \"exchange_rate\": \"1.0000\", \"validity_date\": \"2025-12-31\", \"amount_original\": \"1500.00\", \"converted_amount\": \"1500.00\", \"funding_source_uuid\": \"9655a402-f483-4504-bc37-762c19349d94\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(155,'action_fund_receipt','Action fund receipt updated: #1','App\\Models\\ActionFundReceipt','updated',1,NULL,NULL,'{\"old\": {\"reference\": \"AFR-2025-001\"}, \"attributes\": {\"reference\": \"ENC-UAS-OPS1-UAS-OPS1_ACT001-001\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(156,'action_fund_receipt','Action fund receipt created: #2','App\\Models\\ActionFundReceipt','created',2,NULL,NULL,'{\"attributes\": {\"reference\": \"AFR-2025-002\", \"action_uuid\": \"4d20ea52-f876-43d0-9ddc-e97b285030ed\", \"receipt_date\": \"2025-02-15\", \"currency_uuid\": \"b5829a14-e756-4328-9693-24519dd4baf4\", \"exchange_rate\": \"1.0000\", \"validity_date\": \"2025-11-30\", \"amount_original\": \"1200.00\", \"converted_amount\": \"1200.00\", \"funding_source_uuid\": \"9655a402-f483-4504-bc37-762c19349d94\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(157,'action_fund_receipt','Action fund receipt updated: #2','App\\Models\\ActionFundReceipt','updated',2,NULL,NULL,'{\"old\": {\"reference\": \"AFR-2025-002\"}, \"attributes\": {\"reference\": \"ENC-UAS-OPS1-UAS-OPS1_ACT001-002\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(158,'action','Action created: #3','actions','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Réhabilitation d\'écoles primaires\", \"risks\": \"Manque de financement, retard de livraison\", \"budget\": null, \"status\": \"in_progress\", \"impacts\": \"Amélioration des conditions d\'apprentissage\", \"end_date\": \"2025-06-30T00:00:00.000000Z\", \"priority\": \"high\", \"reference\": null, \"risk_level\": \"medium\", \"start_date\": \"2025-01-10T00:00:00.000000Z\", \"description\": \"Réhabilitation de 10 écoles en milieu rural.\", \"currency_uuid\": null, \"prerequisites\": \"Étude technique préalable\", \"structure_uuid\": \"c4196136-dc09-48c8-8b83-baa6d45b7eff\", \"action_plan_uuid\": \"fb389df6-3f85-4c92-9f94-6ce8a5e84c18\", \"generate_document_type\": \"ppm\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(159,'action','Action updated: #3','actions','updated',3,NULL,NULL,'{\"old\": {\"reference\": null}, \"attributes\": {\"reference\": \"UAS-OPS1_ACT003\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(160,'action','Action created: #4','actions','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"Construction d\'un centre de santé\", \"risks\": \"Opposition communautaire, intempéries\", \"budget\": null, \"status\": \"in_progress\", \"impacts\": \"Renforcement du système de santé local\", \"end_date\": \"2025-09-01T00:00:00.000000Z\", \"priority\": \"medium\", \"reference\": null, \"risk_level\": \"high\", \"start_date\": \"2025-03-01T00:00:00.000000Z\", \"description\": \"Construction d\'un nouveau centre de santé dans la commune A.\", \"currency_uuid\": null, \"prerequisites\": \"Validation du site par les autorités locales\", \"structure_uuid\": \"c4196136-dc09-48c8-8b83-baa6d45b7eff\", \"action_plan_uuid\": \"fb389df6-3f85-4c92-9f94-6ce8a5e84c18\", \"generate_document_type\": \"paa\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(161,'action','Action updated: #4','actions','updated',4,NULL,NULL,'{\"old\": {\"reference\": null}, \"attributes\": {\"reference\": \"UAS-OPS1_ACT004\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(162,'strategic_map','Strategic Map created: #1','App\\Models\\StrategicMap','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Carte stratégique nationale 2025\", \"status\": true, \"end_date\": \"2025-12-31\", \"start_date\": \"2025-01-01\", \"description\": \"Feuille de route de développement à l\'échelle de l\'État.\", \"structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(163,'strategic_map','Strategic Map created: #2','App\\Models\\StrategicMap','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Carte stratégique de développement 2025\", \"status\": true, \"end_date\": \"2025-12-31\", \"start_date\": \"2025-01-01\", \"description\": \"Feuille de route pour le développement global de la structure.\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(164,'strategic_map','Strategic Map created: #3','App\\Models\\StrategicMap','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Carte stratégique digitale\", \"status\": false, \"end_date\": \"2026-02-28\", \"start_date\": \"2025-03-01\", \"description\": \"Plan d\'action pour la digitalisation et l\'innovation technologique.\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(165,'strategic_map','Strategic Map created: #4','App\\Models\\StrategicMap','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"Carte stratégique de performance\", \"status\": false, \"end_date\": \"2026-03-31\", \"start_date\": \"2025-04-01\", \"description\": \"Suivi et évaluation des objectifs de performance annuelle.\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(166,'strategic_lever','Lever created: #1','App\\Models\\StrategicElement','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Levier de gouvernance et innovation publique\", \"type\": \"LEVER\", \"order\": 1, \"status\": true, \"description\": \"Piloter la transformation institutionnelle et la performance publique.\", \"abbreviation\": \"LEV-GOV\", \"structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\", \"parent_map_uuid\": null, \"strategic_map_uuid\": \"1d05e790-8857-4781-88e7-6ef9b22aeb1a\", \"parent_element_uuid\": null, \"parent_structure_uuid\": null}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(167,'strategic_lever','Lever created: #2','App\\Models\\StrategicElement','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Levier de durabilité et environnement\", \"type\": \"LEVER\", \"order\": 2, \"status\": true, \"description\": \"Promouvoir la durabilité écologique et la responsabilité sociétale.\", \"abbreviation\": \"LEV-DUR\", \"structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\", \"parent_map_uuid\": null, \"strategic_map_uuid\": \"1d05e790-8857-4781-88e7-6ef9b22aeb1a\", \"parent_element_uuid\": null, \"parent_structure_uuid\": null}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(168,'strategic_lever','Lever created: #3','App\\Models\\StrategicElement','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Levier de développement économique\", \"type\": \"LEVER\", \"order\": 3, \"status\": true, \"description\": \"Stimuler la croissance, la compétitivité et l\'innovation économique.\", \"abbreviation\": \"LEV-ECO\", \"structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\", \"parent_map_uuid\": null, \"strategic_map_uuid\": \"1d05e790-8857-4781-88e7-6ef9b22aeb1a\", \"parent_element_uuid\": null, \"parent_structure_uuid\": null}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(169,'strategic_lever','Lever created: #4','App\\Models\\StrategicElement','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"Levier d\'inclusion et cohésion sociale\", \"type\": \"LEVER\", \"order\": 4, \"status\": true, \"description\": \"Renforcer l\'équité, l\'inclusion et la solidarité au sein de la société.\", \"abbreviation\": \"LEV-SOC\", \"structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\", \"parent_map_uuid\": null, \"strategic_map_uuid\": \"1d05e790-8857-4781-88e7-6ef9b22aeb1a\", \"parent_element_uuid\": null, \"parent_structure_uuid\": null}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(170,'strategic_axis','Axis created: #5','App\\Models\\StrategicElement','created',5,NULL,NULL,'{\"attributes\": {\"name\": \"Axe Innovation & Digitalisation\", \"type\": \"AXIS\", \"order\": 1, \"status\": true, \"description\": \"Encourager la digitalisation et l\'innovation publique.\", \"abbreviation\": \"INNO\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"parent_map_uuid\": \"1d05e790-8857-4781-88e7-6ef9b22aeb1a\", \"strategic_map_uuid\": \"fae3b1c4-578c-474d-a03f-3953f2f091d6\", \"parent_element_uuid\": \"6795c5f5-0a82-401c-8371-2e58c85745ad\", \"parent_structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(171,'strategic_axis','Axis created: #6','App\\Models\\StrategicElement','created',6,NULL,NULL,'{\"attributes\": {\"name\": \"Axe Gouvernance Transparente\", \"type\": \"AXIS\", \"order\": 2, \"status\": true, \"description\": \"Renforcer la transparence et la redevabilité.\", \"abbreviation\": \"GOV\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"parent_map_uuid\": \"1d05e790-8857-4781-88e7-6ef9b22aeb1a\", \"strategic_map_uuid\": \"fae3b1c4-578c-474d-a03f-3953f2f091d6\", \"parent_element_uuid\": \"6795c5f5-0a82-401c-8371-2e58c85745ad\", \"parent_structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(172,'strategic_axis','Axis created: #7','App\\Models\\StrategicElement','created',7,NULL,NULL,'{\"attributes\": {\"name\": \"Axe Énergie Verte\", \"type\": \"AXIS\", \"order\": 3, \"status\": true, \"description\": \"Promouvoir les énergies renouvelables et la sobriété énergétique.\", \"abbreviation\": \"GREEN\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"parent_map_uuid\": \"1d05e790-8857-4781-88e7-6ef9b22aeb1a\", \"strategic_map_uuid\": \"fae3b1c4-578c-474d-a03f-3953f2f091d6\", \"parent_element_uuid\": \"9bfdad71-9202-467f-8b9b-52d01d3946a6\", \"parent_structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(173,'strategic_axis','Axis created: #8','App\\Models\\StrategicElement','created',8,NULL,NULL,'{\"attributes\": {\"name\": \"Axe Gestion des Ressources\", \"type\": \"AXIS\", \"order\": 4, \"status\": true, \"description\": \"Optimiser l\'utilisation des ressources naturelles.\", \"abbreviation\": \"RES\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"parent_map_uuid\": \"1d05e790-8857-4781-88e7-6ef9b22aeb1a\", \"strategic_map_uuid\": \"fae3b1c4-578c-474d-a03f-3953f2f091d6\", \"parent_element_uuid\": \"9bfdad71-9202-467f-8b9b-52d01d3946a6\", \"parent_structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(174,'strategic_axis','Axis created: #9','App\\Models\\StrategicElement','created',9,NULL,NULL,'{\"attributes\": {\"name\": \"Axe Innovation Économique\", \"type\": \"AXIS\", \"order\": 5, \"status\": true, \"description\": \"Soutenir les startups et les initiatives entrepreneuriales.\", \"abbreviation\": \"ECO-INN\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"parent_map_uuid\": \"1d05e790-8857-4781-88e7-6ef9b22aeb1a\", \"strategic_map_uuid\": \"fae3b1c4-578c-474d-a03f-3953f2f091d6\", \"parent_element_uuid\": \"b6051c57-1bc1-414b-86e1-43a74c683634\", \"parent_structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(175,'strategic_axis','Axis created: #10','App\\Models\\StrategicElement','created',10,NULL,NULL,'{\"attributes\": {\"name\": \"Axe Industrie & Compétitivité\", \"type\": \"AXIS\", \"order\": 6, \"status\": true, \"description\": \"Renforcer le tissu industriel et l\'emploi local.\", \"abbreviation\": \"IND\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"parent_map_uuid\": \"1d05e790-8857-4781-88e7-6ef9b22aeb1a\", \"strategic_map_uuid\": \"fae3b1c4-578c-474d-a03f-3953f2f091d6\", \"parent_element_uuid\": \"b6051c57-1bc1-414b-86e1-43a74c683634\", \"parent_structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(176,'strategic_axis','Axis created: #11','App\\Models\\StrategicElement','created',11,NULL,NULL,'{\"attributes\": {\"name\": \"Axe Éducation et Compétences\", \"type\": \"AXIS\", \"order\": 7, \"status\": true, \"description\": \"Améliorer l\'accès et la qualité de l\'éducation.\", \"abbreviation\": \"EDU\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"parent_map_uuid\": \"1d05e790-8857-4781-88e7-6ef9b22aeb1a\", \"strategic_map_uuid\": \"fae3b1c4-578c-474d-a03f-3953f2f091d6\", \"parent_element_uuid\": \"b7508403-8736-4371-b733-fa3c5d245b48\", \"parent_structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(177,'strategic_axis','Axis created: #12','App\\Models\\StrategicElement','created',12,NULL,NULL,'{\"attributes\": {\"name\": \"Axe Santé & Bien-être\", \"type\": \"AXIS\", \"order\": 8, \"status\": true, \"description\": \"Renforcer les infrastructures sanitaires et sociales.\", \"abbreviation\": \"SAN\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"parent_map_uuid\": \"1d05e790-8857-4781-88e7-6ef9b22aeb1a\", \"strategic_map_uuid\": \"fae3b1c4-578c-474d-a03f-3953f2f091d6\", \"parent_element_uuid\": \"b7508403-8736-4371-b733-fa3c5d245b48\", \"parent_structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(178,'strategic_objective','Strategic Objective created: #1','strategic_objectives','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Renforcer les capacités institutionnelles\", \"status\": \"declared\", \"end_date\": \"2025-06-30T00:00:00.000000Z\", \"priority\": \"medium\", \"reference\": \"OBJ-001\", \"risk_level\": \"low\", \"start_date\": \"2025-01-10T00:00:00.000000Z\", \"description\": \"Améliorer la formation et le suivi des agents clés.\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"strategic_map_uuid\": \"fae3b1c4-578c-474d-a03f-3953f2f091d6\", \"lead_structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\", \"strategic_axis_uuid\": null}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(179,'strategic_objective','Strategic Objective created: #2','strategic_objectives','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Promouvoir la digitalisation\", \"status\": \"declared\", \"end_date\": \"2025-12-31T00:00:00.000000Z\", \"priority\": \"medium\", \"reference\": \"OBJ-002\", \"risk_level\": \"low\", \"start_date\": \"2025-02-01T00:00:00.000000Z\", \"description\": \"Mise en place d\'outils numériques pour optimiser les processus.\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"strategic_map_uuid\": \"fae3b1c4-578c-474d-a03f-3953f2f091d6\", \"lead_structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\", \"strategic_axis_uuid\": null}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(180,'strategic_objective','Strategic Objective created: #3','strategic_objectives','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Accroître la participation communautaire\", \"status\": \"engaged\", \"end_date\": \"2025-09-15T00:00:00.000000Z\", \"priority\": \"medium\", \"reference\": \"OBJ-003\", \"risk_level\": \"high\", \"start_date\": \"2025-03-15T00:00:00.000000Z\", \"description\": \"Encourager la collaboration avec les associations locales.\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"strategic_map_uuid\": \"fae3b1c4-578c-474d-a03f-3953f2f091d6\", \"lead_structure_uuid\": \"f1f07bb0-8b5c-4170-9afc-02f307f5f0d6\", \"strategic_axis_uuid\": null}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(181,'indicator_categories','Indicator category created: #1','App\\Models\\IndicatorCategory','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Performance Financière\", \"status\": true}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(182,'indicator_categories','Indicator category created: #2','App\\Models\\IndicatorCategory','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Satisfaction Client\", \"status\": true}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(183,'indicator_categories','Indicator category created: #3','App\\Models\\IndicatorCategory','created',3,NULL,NULL,'{\"attributes\": {\"name\": \"Processus Internes\", \"status\": true}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(184,'indicator_categories','Indicator category created: #4','App\\Models\\IndicatorCategory','created',4,NULL,NULL,'{\"attributes\": {\"name\": \"Apprentissage et Croissance\", \"status\": false}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(185,'indicator','Indicator created: #1','indicators','created',1,NULL,NULL,'{\"attributes\": {\"name\": \"Taux de scolarisation primaire\", \"unit\": \"%\", \"state\": \"none\", \"status\": \"in_progress\", \"end_date\": \"2025-06-30T00:00:00.000000Z\", \"reference\": null, \"chart_type\": \"BAR\", \"is_planned\": false, \"start_date\": \"2025-01-10T00:00:00.000000Z\", \"description\": \"Pourcentage d\'enfants inscrits à l\'école primaire.\", \"initial_value\": 65.75, \"achieved_value\": 0, \"frequency_unit\": \"months\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"frequency_value\": 1, \"final_target_value\": 90.5, \"strategic_map_uuid\": \"fae3b1c4-578c-474d-a03f-3953f2f091d6\", \"strategic_element_uuid\": \"e679e743-67ea-431a-8493-665cb27cb7c9\", \"strategic_objective_uuid\": \"a7aa7d10-ddae-473c-b5af-e1697c96c6aa\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(186,'indicator','Indicator updated: #1','indicators','updated',1,NULL,NULL,'{\"old\": {\"state\": null, \"reference\": null, \"is_planned\": null}, \"attributes\": {\"state\": \"none\", \"reference\": \"OBJ-001_IND001\", \"is_planned\": false}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(187,'indicator','Indicator created: #2','indicators','created',2,NULL,NULL,'{\"attributes\": {\"name\": \"Nombre de centres de santé fonctionnels\", \"unit\": \"kilomètre\", \"state\": \"none\", \"status\": \"in_progress\", \"end_date\": \"2025-06-30T00:00:00.000000Z\", \"reference\": null, \"chart_type\": \"LINE\", \"is_planned\": false, \"start_date\": \"2025-01-10T00:00:00.000000Z\", \"description\": \"Indique le nombre total de centres opérationnels.\", \"initial_value\": 50.25, \"achieved_value\": 0, \"frequency_unit\": \"months\", \"structure_uuid\": \"56a0ec48-5f29-4778-85cb-f347275fcf95\", \"frequency_value\": 3, \"final_target_value\": 120.6, \"strategic_map_uuid\": \"fae3b1c4-578c-474d-a03f-3953f2f091d6\", \"strategic_element_uuid\": \"e679e743-67ea-431a-8493-665cb27cb7c9\", \"strategic_objective_uuid\": \"a7aa7d10-ddae-473c-b5af-e1697c96c6aa\"}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(188,'indicator','Indicator updated: #2','indicators','updated',2,NULL,NULL,'{\"old\": {\"state\": null, \"reference\": null, \"is_planned\": null}, \"attributes\": {\"state\": \"none\", \"reference\": \"OBJ-001_IND002\", \"is_planned\": false}}',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49');
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachable_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachable_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identifier` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint DEFAULT NULL,
  `uploaded_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `file_type_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attachments_uuid_unique` (`uuid`),
  KEY `attachments_attachable_index` (`attachable_id`,`attachable_type`),
  KEY `attachments_file_type_uuid_foreign` (`file_type_uuid`),
  KEY `attachments_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `attachments_file_type_uuid_foreign` FOREIGN KEY (`file_type_uuid`) REFERENCES `file_types` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachments`
--

LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `beneficiaries`
--

DROP TABLE IF EXISTS `beneficiaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `beneficiaries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `beneficiaries_uuid_unique` (`uuid`),
  UNIQUE KEY `beneficiaries_name_unique` (`name`),
  KEY `beneficiaries_created_by_foreign` (`created_by`),
  KEY `beneficiaries_updated_by_foreign` (`updated_by`),
  CONSTRAINT `beneficiaries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `beneficiaries_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beneficiaries`
--

LOCK TABLES `beneficiaries` WRITE;
/*!40000 ALTER TABLE `beneficiaries` DISABLE KEYS */;
INSERT INTO `beneficiaries` VALUES (1,'8bdc521f-0e85-4262-8760-f0193c66c36c','Albertha Turner','mitchel.bailey@powlowski.info','540.347.8463',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(2,'4d3f9694-d82c-42fd-9054-3fc6fdb277d5','Vicente Feil','jayde09@von.com','1-551-964-1872',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(3,'acf3bc7c-fb1b-46b1-af1f-2511ad7ad947','Dr. Jazmyn Bergnaum PhD','lschowalter@gmail.com','(917) 693-8029',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(4,'c235bc06-d4c5-44f0-a9ef-ebee8d042e93','Maxwell Bartoletti','roel.collier@gmail.com','+1-628-473-7486',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(5,'e690e38a-ac1f-44d3-9f5a-1e7f18064e9c','Loren Cummerata','ova44@ortiz.org','1-434-671-7774',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL);
/*!40000 ALTER TABLE `beneficiaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `budget_types`
--

DROP TABLE IF EXISTS `budget_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `budget_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `budget_types_uuid_unique` (`uuid`),
  UNIQUE KEY `budget_types_name_unique` (`name`),
  KEY `budget_types_created_by_foreign` (`created_by`),
  KEY `budget_types_updated_by_foreign` (`updated_by`),
  CONSTRAINT `budget_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `budget_types_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `budget_types`
--

LOCK TABLES `budget_types` WRITE;
/*!40000 ALTER TABLE `budget_types` DISABLE KEYS */;
INSERT INTO `budget_types` VALUES (1,'72f166f0-c083-4ab7-8a06-74106ff96355','Marketing',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(2,'7aefc3b5-4cf7-411c-bd2e-054a9c57a9a8','Transport',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(3,'a506d260-c807-4ea3-b29a-5a7916c96a40','Épargne',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(4,'65df61d6-62f8-49e1-bc25-923b5bf28bd3','Loyer',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL);
/*!40000 ALTER TABLE `budget_types` ENABLE KEYS */;
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
  PRIMARY KEY (`key`)
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
-- Table structure for table `capability_domain_states`
--

DROP TABLE IF EXISTS `capability_domain_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `capability_domain_states` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capability_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capability_domain_id` int NOT NULL,
  `state_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_date` timestamp NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `capability_domain_states_uuid_unique` (`uuid`),
  KEY `capability_domain_states_created_by_foreign` (`created_by`),
  KEY `capability_domain_states_updated_by_foreign` (`updated_by`),
  KEY `capability_domain_states_capability_domain_uuid_index` (`capability_domain_uuid`),
  KEY `capability_domain_states_capability_domain_id_index` (`capability_domain_id`),
  CONSTRAINT `capability_domain_states_capability_domain_uuid_foreign` FOREIGN KEY (`capability_domain_uuid`) REFERENCES `capability_domains` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `capability_domain_states_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `capability_domain_states_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `capability_domain_states`
--

LOCK TABLES `capability_domain_states` WRITE;
/*!40000 ALTER TABLE `capability_domain_states` DISABLE KEYS */;
/*!40000 ALTER TABLE `capability_domain_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `capability_domain_statuses`
--

DROP TABLE IF EXISTS `capability_domain_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `capability_domain_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capability_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capability_domain_id` int NOT NULL,
  `status_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_date` timestamp NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `capability_domain_statuses_uuid_unique` (`uuid`),
  KEY `capability_domain_statuses_created_by_foreign` (`created_by`),
  KEY `capability_domain_statuses_updated_by_foreign` (`updated_by`),
  KEY `capability_domain_statuses_capability_domain_uuid_index` (`capability_domain_uuid`),
  KEY `capability_domain_statuses_capability_domain_id_index` (`capability_domain_id`),
  CONSTRAINT `capability_domain_statuses_capability_domain_uuid_foreign` FOREIGN KEY (`capability_domain_uuid`) REFERENCES `capability_domains` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `capability_domain_statuses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `capability_domain_statuses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `capability_domain_statuses`
--

LOCK TABLES `capability_domain_statuses` WRITE;
/*!40000 ALTER TABLE `capability_domain_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `capability_domain_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `capability_domains`
--

DROP TABLE IF EXISTS `capability_domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `capability_domains` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(14,2) DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MRU',
  `responsible_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'preparation',
  `status_changed_at` timestamp NULL DEFAULT NULL,
  `status_changed_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `state_changed_at` timestamp NULL DEFAULT NULL,
  `state_changed_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `prerequisites` text COLLATE utf8mb4_unicode_ci,
  `impacts` text COLLATE utf8mb4_unicode_ci,
  `risks` text COLLATE utf8mb4_unicode_ci,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `capability_domains_uuid_unique` (`uuid`),
  UNIQUE KEY `capability_domains_name_unique` (`name`),
  UNIQUE KEY `capability_domains_reference_unique` (`reference`),
  KEY `capability_domains_strategic_domain_uuid_foreign` (`strategic_domain_uuid`),
  KEY `capability_domains_responsible_uuid_foreign` (`responsible_uuid`),
  KEY `capability_domains_created_by_foreign` (`created_by`),
  KEY `capability_domains_updated_by_foreign` (`updated_by`),
  CONSTRAINT `capability_domains_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `capability_domains_responsible_uuid_foreign` FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `capability_domains_strategic_domain_uuid_foreign` FOREIGN KEY (`strategic_domain_uuid`) REFERENCES `strategic_domains` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `capability_domains_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `capability_domains`
--

LOCK TABLES `capability_domains` WRITE;
/*!40000 ALTER TABLE `capability_domains` DISABLE KEYS */;
INSERT INTO `capability_domains` VALUES (1,'7207f568-b7af-4662-b80f-9e3d34c9ff85','48b76f89-38ad-43d6-b7e2-7fbdb6fa074e','ACT-001','Formation des agents','2025-01-15','2025-01-30',140017.00,'MRU','58f41856-9d3f-4ca5-ae52-44282b0799a9','preparation',NULL,NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(2,'ffb3a636-5a9a-473e-acd1-56d84b8e59df','48b76f89-38ad-43d6-b7e2-7fbdb6fa074e','ACT-002','Campagne de sensibilisation','2025-03-01','2025-03-15',238202.00,'MRU','32233eba-1f16-42c5-b1f4-138ed81e30c7','preparation',NULL,NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(3,'6de78ac4-f97f-48a5-afe2-510cff4ab642','dc5dbf4b-9a50-4752-b4f1-dd9407133087','ACT-003','Distribution de kits','2025-05-01','2025-06-01',536269.00,'MRU','10fc0325-3da5-4318-9df4-af1cd01351a2','preparation',NULL,NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48');
/*!40000 ALTER TABLE `capability_domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contracts`
--

DROP TABLE IF EXISTS `contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contracts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contract_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `signed_at` date DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contracts_uuid_unique` (`uuid`),
  UNIQUE KEY `contracts_contract_number_unique` (`contract_number`),
  KEY `contracts_supplier_uuid_foreign` (`supplier_uuid`),
  KEY `contracts_created_by_foreign` (`created_by`),
  KEY `contracts_updated_by_foreign` (`updated_by`),
  CONSTRAINT `contracts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `contracts_supplier_uuid_foreign` FOREIGN KEY (`supplier_uuid`) REFERENCES `suppliers` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `contracts_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contracts`
--

LOCK TABLES `contracts` WRITE;
/*!40000 ALTER TABLE `contracts` DISABLE KEYS */;
INSERT INTO `contracts` VALUES (1,'a03f4c8a-a6e6-4134-b2be-3c5c1a75b356','f7af3e96-be5f-4d00-87ef-2a9e5e926ea2','CT-454/7430','Excepturi cupiditate soluta consequatur quasi.','2025-07-24','2028-02-13',4578316.46,'Et dolor est quae velit reiciendis voluptate. Aspernatur consequatur eos in labore atque incidunt sed. Nemo qui sed amet molestiae aut est. Et iure quia eveniet quas alias.',1,'2024-07-14',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(2,'dc7a02a5-5e46-4fd5-b8da-6336d140b78d','2b253357-9f52-4f56-90ad-56366d70ddd4','CT-783/6262','Quasi occaecati quibusdam repellat aut ab.','2025-01-28','2025-11-24',4389857.39,NULL,1,'2024-07-05',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(3,'4eaf8c21-b1b6-4574-977c-182d9649a6e5','2b253357-9f52-4f56-90ad-56366d70ddd4','CT-221/1407','Ad enim quam voluptatem quo.','2024-10-30','2024-11-24',3287534.51,NULL,1,'2024-05-28',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(4,'5277cbd2-5fd2-4b8c-bd55-381e98a23949','573f1054-5322-46ba-a073-9abdb9585844','CT-585/2742','Libero voluptas maiores voluptatem molestias voluptatem dolores dolorum.','2025-10-05','2026-04-06',8407775.29,NULL,0,'2024-08-29',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(5,'86496f3b-2771-46b0-8e05-9fd2a431ecc0','5868fdb4-c752-4597-9015-e0acdf85f2cb','CT-144/5816','Id quam nobis voluptatum quis mollitia magni.','2025-08-19','2028-07-12',3442345.42,NULL,1,'2024-12-15',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(6,'d2431135-9363-4f98-900e-af6a883fb904','2b253357-9f52-4f56-90ad-56366d70ddd4','CT-171/6244','Quibusdam libero explicabo velit voluptatibus blanditiis sunt nam.','2024-08-19','2026-08-15',4263867.19,NULL,1,'2024-08-05',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(7,'f3da4b85-629f-414c-8b96-f6eeafcaaf27','f7af3e96-be5f-4d00-87ef-2a9e5e926ea2','CT-655/1619','Ad praesentium ratione aliquid molestiae sequi quod repellendus.','2024-06-04','2027-01-26',4839667.31,'Corrupti dolorem totam aut tempore quasi non. Atque iste ut eveniet. Aut possimus aliquid libero. Animi ut qui necessitatibus.',1,'2024-05-26',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(8,'ddc17a16-4bf9-4666-b3a6-f064aa9a20c6','f7af3e96-be5f-4d00-87ef-2a9e5e926ea2','CT-418/0125','Mollitia eos magni quia in consequatur eos porro.','2025-04-16','2025-05-24',6646806.67,NULL,1,'2024-12-23',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(9,'a21ab5b1-eebc-4235-baaa-f609db5679c1','f7af3e96-be5f-4d00-87ef-2a9e5e926ea2','CT-344/5308','Exercitationem in iure quod deserunt eum molestiae iste.','2025-11-23','2029-03-16',1086162.14,NULL,1,'2024-09-05',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(10,'83aa560b-05b8-4b1f-8cdb-02d5deefe895','573f1054-5322-46ba-a073-9abdb9585844','CT-722/4100','Ut et placeat officiis atque aut itaque ab.','2025-12-15','2028-05-30',518361.16,'Reprehenderit explicabo quos ex. Doloribus laudantium autem rerum iste magnam. Quia eaque rerum iste sed deserunt at et.',1,'2025-03-22',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48');
/*!40000 ALTER TABLE `contracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currencies_uuid_unique` (`uuid`),
  UNIQUE KEY `currencies_code_unique` (`code`),
  KEY `currencies_created_by_foreign` (`created_by`),
  KEY `currencies_updated_by_foreign` (`updated_by`),
  CONSTRAINT `currencies_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `currencies_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'b5829a14-e756-4328-9693-24519dd4baf4','Ouguiya','MRU',0,1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(2,'fb89294b-4dde-4b1f-ba40-8824ceb8dc62','Franc CFA','XOF',1,1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(3,'f0a82221-3889-4c08-94f4-ad00e34ed8cc','Euro','EUR',0,1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(4,'2cde81a6-c973-4d29-9838-1a375d385c20','Dollar américain','USD',0,1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL);
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `decision_statuses`
--

DROP TABLE IF EXISTS `decision_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `decision_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decision_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_date` timestamp NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'announced',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `decision_statuses_uuid_unique` (`uuid`),
  KEY `decision_statuses_decision_uuid_foreign` (`decision_uuid`),
  KEY `decision_statuses_created_by_foreign` (`created_by`),
  KEY `decision_statuses_updated_by_foreign` (`updated_by`),
  CONSTRAINT `decision_statuses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `decision_statuses_decision_uuid_foreign` FOREIGN KEY (`decision_uuid`) REFERENCES `decisions` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `decision_statuses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `decision_statuses`
--

LOCK TABLES `decision_statuses` WRITE;
/*!40000 ALTER TABLE `decision_statuses` DISABLE KEYS */;
INSERT INTO `decision_statuses` VALUES (1,'96d5e463-9d6a-47e0-a962-931f4f7bc99c','4e3ad018-4d67-400f-bdb1-a428cea4fded','2026-05-18 11:35:49','Décision créée mais non traitée.','none','2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL),(2,'47de123d-8ab1-4545-ba71-e19a2a55c5d1','4e3ad018-4d67-400f-bdb1-a428cea4fded','2026-05-18 11:35:49','Décision en cours de traitement.','in_progress','2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL),(3,'aab43dfd-54e0-4d56-bb29-2c5ad38175cc','4e3ad018-4d67-400f-bdb1-a428cea4fded','2026-05-18 11:35:49','Décision traitée.','processed','2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL);
/*!40000 ALTER TABLE `decision_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `decisions`
--

DROP TABLE IF EXISTS `decisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `decisions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `decision_date` date NOT NULL,
  `decidable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decidable_id` bigint unsigned NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'announced',
  `status_changed_at` timestamp NULL DEFAULT NULL,
  `status_changed_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `decisions_uuid_unique` (`uuid`),
  UNIQUE KEY `decisions_reference_unique` (`reference`),
  KEY `decisions_decidable_type_decidable_id_index` (`decidable_type`,`decidable_id`),
  KEY `decisions_created_by_foreign` (`created_by`),
  KEY `decisions_updated_by_foreign` (`updated_by`),
  KEY `decisions_status_changed_by_foreign` (`status_changed_by`),
  CONSTRAINT `decisions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `decisions_status_changed_by_foreign` FOREIGN KEY (`status_changed_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `decisions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `decisions`
--

LOCK TABLES `decisions` WRITE;
/*!40000 ALTER TABLE `decisions` DISABLE KEYS */;
INSERT INTO `decisions` VALUES (1,'4e3ad018-4d67-400f-bdb1-a428cea4fded','MEFP_AXER _OBJ215_DECIS19','2026-05-18','App\\Models\\StrategicObjective',1,'Validation de l’objectif stratégique','high','Décision concernant l\'avancement du projet stratégique.','announced',NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL),(2,'5a4bdde5-a2e8-4c02-b2a2-58940213f627','MEFP_Procapec_ACT920_DECIS12','2026-05-18','App\\Models\\Action',1,'Lancement du projet BTP Sahel','high','Décision pour le démarrage du projet BTP Sahel.','announced',NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL);
/*!40000 ALTER TABLE `decisions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `default_phases`
--

DROP TABLE IF EXISTS `default_phases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `default_phases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` tinyint unsigned NOT NULL DEFAULT '0',
  `duration` int unsigned NOT NULL,
  `weight` decimal(8,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `deliverable` text COLLATE utf8mb4_unicode_ci,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `default_phases_uuid_unique` (`uuid`),
  UNIQUE KEY `default_phases_name_unique` (`name`),
  KEY `default_phases_created_by_foreign` (`created_by`),
  KEY `default_phases_updated_by_foreign` (`updated_by`),
  CONSTRAINT `default_phases_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `default_phases_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `default_phases`
--

LOCK TABLES `default_phases` WRITE;
/*!40000 ALTER TABLE `default_phases` DISABLE KEYS */;
INSERT INTO `default_phases` VALUES (1,'a2ee9645-dbcf-40c9-b3db-be99937a1aa8','Phase 1',1,30,0.25,'Description de la phase 1 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 1.',NULL,NULL,'2026-05-18 11:35:50','2026-05-18 11:35:50'),(2,'1663d07c-98bc-470c-9d99-e4c28722b54b','Phase 2',2,20,0.01,'Description de la phase 2 — activités principales, livrables et jalons associés.','Livrable attendu : rapport de suivi de la phase 2.',NULL,NULL,'2026-05-18 11:35:50','2026-05-18 11:35:50');
/*!40000 ALTER TABLE `default_phases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delegated_project_owners`
--

DROP TABLE IF EXISTS `delegated_project_owners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delegated_project_owners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_owner_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delegated_project_owners_uuid_unique` (`uuid`),
  KEY `delegated_project_owners_project_owner_uuid_foreign` (`project_owner_uuid`),
  KEY `delegated_project_owners_created_by_foreign` (`created_by`),
  KEY `delegated_project_owners_updated_by_foreign` (`updated_by`),
  CONSTRAINT `delegated_project_owners_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `delegated_project_owners_project_owner_uuid_foreign` FOREIGN KEY (`project_owner_uuid`) REFERENCES `project_owners` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `delegated_project_owners_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delegated_project_owners`
--

LOCK TABLES `delegated_project_owners` WRITE;
/*!40000 ALTER TABLE `delegated_project_owners` DISABLE KEYS */;
INSERT INTO `delegated_project_owners` VALUES (1,'9a94ab88-338e-438a-8226-a20930528896','cf577d7f-f6fc-440c-981e-87d9494e46dc','Maître d\'ouvrage délégué A','delegue.a@example.com','22330011',1,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(2,'4f91897a-75c1-4a28-ad06-d670d7ffa130','cf577d7f-f6fc-440c-981e-87d9494e46dc','Maître d\'ouvrage délégué B','delegue.b@example.com','22330022',0,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48');
/*!40000 ALTER TABLE `delegated_project_owners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_uuid_unique` (`uuid`),
  UNIQUE KEY `departments_name_unique` (`name`),
  KEY `departments_region_uuid_foreign` (`region_uuid`),
  KEY `departments_created_by_foreign` (`created_by`),
  KEY `departments_updated_by_foreign` (`updated_by`),
  CONSTRAINT `departments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `departments_region_uuid_foreign` FOREIGN KEY (`region_uuid`) REFERENCES `regions` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `departments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'c3246e1b-6260-4279-b845-d60eb30fba2b','Priceshire','febe6726-ecfa-4c53-b338-400e639559aa',1,-55.296821,-70.157211,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(2,'ec9ddd80-db02-433e-8843-98a3ce883b56','West Jaidamouth','6e30fd97-9b0c-425b-be85-6b8d2ba7b9e0',1,-43.958653,-154.511860,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(3,'fe039939-2914-459c-a9eb-9f9833fd994f','New Cassiemouth','2606b718-6f19-4c82-a9cd-43c453f1c095',1,-51.084054,101.316337,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(4,'de14f568-ff4e-4e87-a448-f307a8c3c9c5','Port Lupeberg','6e30fd97-9b0c-425b-be85-6b8d2ba7b9e0',1,14.288961,128.107010,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(5,'6e71c415-5439-4422-a30a-d996b975fa15','Hettingerstad','742b399a-5971-447e-86e4-0e648eb61d36',1,-68.669418,12.977692,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL);
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elementary_level_beneficiaries`
--

DROP TABLE IF EXISTS `elementary_level_beneficiaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `elementary_level_beneficiaries` (
  `elementary_level_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`elementary_level_uuid`,`beneficiary_uuid`),
  KEY `elementary_level_beneficiaries_beneficiary_uuid_foreign` (`beneficiary_uuid`),
  CONSTRAINT `elementary_level_beneficiaries_beneficiary_uuid_foreign` FOREIGN KEY (`beneficiary_uuid`) REFERENCES `beneficiaries` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `elementary_level_beneficiaries_elementary_level_uuid_foreign` FOREIGN KEY (`elementary_level_uuid`) REFERENCES `elementary_levels` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elementary_level_beneficiaries`
--

LOCK TABLES `elementary_level_beneficiaries` WRITE;
/*!40000 ALTER TABLE `elementary_level_beneficiaries` DISABLE KEYS */;
INSERT INTO `elementary_level_beneficiaries` VALUES ('6b0a8995-ddae-460d-a31c-6aff9f40617c','4d3f9694-d82c-42fd-9054-3fc6fdb277d5'),('9c13fb7b-d903-48e8-81ce-7671df08e274','4d3f9694-d82c-42fd-9054-3fc6fdb277d5'),('f8007ca2-2abf-4a17-9097-7982e636bf9d','8bdc521f-0e85-4262-8760-f0193c66c36c'),('6b0a8995-ddae-460d-a31c-6aff9f40617c','acf3bc7c-fb1b-46b1-af1f-2511ad7ad947'),('9c13fb7b-d903-48e8-81ce-7671df08e274','acf3bc7c-fb1b-46b1-af1f-2511ad7ad947'),('6b0a8995-ddae-460d-a31c-6aff9f40617c','e690e38a-ac1f-44d3-9f5a-1e7f18064e9c'),('9c13fb7b-d903-48e8-81ce-7671df08e274','e690e38a-ac1f-44d3-9f5a-1e7f18064e9c'),('f8007ca2-2abf-4a17-9097-7982e636bf9d','e690e38a-ac1f-44d3-9f5a-1e7f18064e9c');
/*!40000 ALTER TABLE `elementary_level_beneficiaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elementary_level_funding_sources`
--

DROP TABLE IF EXISTS `elementary_level_funding_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `elementary_level_funding_sources` (
  `elementary_level_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funding_source_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planned_budget` decimal(14,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`elementary_level_uuid`,`funding_source_uuid`),
  KEY `elementary_level_funding_sources_funding_source_uuid_foreign` (`funding_source_uuid`),
  CONSTRAINT `elementary_level_funding_sources_elementary_level_uuid_foreign` FOREIGN KEY (`elementary_level_uuid`) REFERENCES `elementary_levels` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `elementary_level_funding_sources_funding_source_uuid_foreign` FOREIGN KEY (`funding_source_uuid`) REFERENCES `funding_sources` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elementary_level_funding_sources`
--

LOCK TABLES `elementary_level_funding_sources` WRITE;
/*!40000 ALTER TABLE `elementary_level_funding_sources` DISABLE KEYS */;
INSERT INTO `elementary_level_funding_sources` VALUES ('6b0a8995-ddae-460d-a31c-6aff9f40617c','5f97d913-4aeb-4c13-8807-f35f5e0b547c',146523.00),('6b0a8995-ddae-460d-a31c-6aff9f40617c','e38bb49c-124b-48fb-a30c-f4d7ec7e4c4f',179568.00),('9c13fb7b-d903-48e8-81ce-7671df08e274','9655a402-f483-4504-bc37-762c19349d94',146588.00),('f8007ca2-2abf-4a17-9097-7982e636bf9d','438af312-de77-4dab-bea3-74f76fb45c12',136463.00),('f8007ca2-2abf-4a17-9097-7982e636bf9d','f097b72c-2932-4e8a-97de-e8002fe9a2b2',238640.00);
/*!40000 ALTER TABLE `elementary_level_funding_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elementary_level_states`
--

DROP TABLE IF EXISTS `elementary_level_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `elementary_level_states` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `elementary_level_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `elementary_level_id` int NOT NULL,
  `state_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_date` timestamp NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `elementary_level_states_uuid_unique` (`uuid`),
  KEY `elementary_level_states_created_by_foreign` (`created_by`),
  KEY `elementary_level_states_updated_by_foreign` (`updated_by`),
  KEY `elementary_level_states_elementary_level_uuid_index` (`elementary_level_uuid`),
  KEY `elementary_level_states_elementary_level_id_index` (`elementary_level_id`),
  CONSTRAINT `elementary_level_states_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `elementary_level_states_elementary_level_uuid_foreign` FOREIGN KEY (`elementary_level_uuid`) REFERENCES `elementary_levels` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `elementary_level_states_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elementary_level_states`
--

LOCK TABLES `elementary_level_states` WRITE;
/*!40000 ALTER TABLE `elementary_level_states` DISABLE KEYS */;
/*!40000 ALTER TABLE `elementary_level_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elementary_level_statuses`
--

DROP TABLE IF EXISTS `elementary_level_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `elementary_level_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `elementary_level_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `elementary_level_id` int NOT NULL,
  `status_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_date` timestamp NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `elementary_level_statuses_uuid_unique` (`uuid`),
  KEY `elementary_level_statuses_created_by_foreign` (`created_by`),
  KEY `elementary_level_statuses_updated_by_foreign` (`updated_by`),
  KEY `elementary_level_statuses_elementary_level_uuid_index` (`elementary_level_uuid`),
  KEY `elementary_level_statuses_elementary_level_id_index` (`elementary_level_id`),
  CONSTRAINT `elementary_level_statuses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `elementary_level_statuses_elementary_level_uuid_foreign` FOREIGN KEY (`elementary_level_uuid`) REFERENCES `elementary_levels` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `elementary_level_statuses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elementary_level_statuses`
--

LOCK TABLES `elementary_level_statuses` WRITE;
/*!40000 ALTER TABLE `elementary_level_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `elementary_level_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elementary_levels`
--

DROP TABLE IF EXISTS `elementary_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `elementary_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capability_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(14,2) DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MRU',
  `responsible_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'preparation',
  `status_changed_at` timestamp NULL DEFAULT NULL,
  `status_changed_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `state_changed_at` timestamp NULL DEFAULT NULL,
  `state_changed_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `prerequisites` text COLLATE utf8mb4_unicode_ci,
  `impacts` text COLLATE utf8mb4_unicode_ci,
  `risks` text COLLATE utf8mb4_unicode_ci,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `elementary_levels_uuid_unique` (`uuid`),
  UNIQUE KEY `elementary_levels_name_unique` (`name`),
  UNIQUE KEY `elementary_levels_reference_unique` (`reference`),
  KEY `elementary_levels_capability_domain_uuid_foreign` (`capability_domain_uuid`),
  KEY `elementary_levels_responsible_uuid_foreign` (`responsible_uuid`),
  KEY `elementary_levels_created_by_foreign` (`created_by`),
  KEY `elementary_levels_updated_by_foreign` (`updated_by`),
  CONSTRAINT `elementary_levels_capability_domain_uuid_foreign` FOREIGN KEY (`capability_domain_uuid`) REFERENCES `capability_domains` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `elementary_levels_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `elementary_levels_responsible_uuid_foreign` FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `elementary_levels_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elementary_levels`
--

LOCK TABLES `elementary_levels` WRITE;
/*!40000 ALTER TABLE `elementary_levels` DISABLE KEYS */;
INSERT INTO `elementary_levels` VALUES (1,'6b0a8995-ddae-460d-a31c-6aff9f40617c','7207f568-b7af-4662-b80f-9e3d34c9ff85','EML-001','Niveau élémentaire A ','2025-01-15','2025-01-30',326091.00,'MRU','4299daf7-e238-4f1d-9692-71df1ac7cc2e','preparation',NULL,NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-18 11:35:50','2026-05-18 11:35:50'),(2,'f8007ca2-2abf-4a17-9097-7982e636bf9d','7207f568-b7af-4662-b80f-9e3d34c9ff85','EML-002','Niveau élémentaire B','2025-03-01','2025-03-15',375103.00,'MRU','d1fa387a-cd07-47fb-8889-ae83ba83538a','preparation',NULL,NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-18 11:35:50','2026-05-18 11:35:50'),(3,'9c13fb7b-d903-48e8-81ce-7671df08e274','ffb3a636-5a9a-473e-acd1-56d84b8e59df','EML-003','Niveau élémentaire C','2025-05-01','2025-06-01',146588.00,'MRU','11ea98e3-2c42-453e-aa58-49f1afff24b0','preparation',NULL,NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-18 11:35:50','2026-05-18 11:35:50');
/*!40000 ALTER TABLE `elementary_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `structure_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `floor` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `can_logged_in` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_uuid_unique` (`uuid`),
  UNIQUE KEY `employees_user_uuid_unique` (`user_uuid`),
  KEY `employees_structure_uuid_foreign` (`structure_uuid`),
  CONSTRAINT `employees_structure_uuid_foreign` FOREIGN KEY (`structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `employees_user_uuid_foreign` FOREIGN KEY (`user_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'348ab8da-c36c-45f4-93d6-f2047413280e','Diamond Worker','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','ef55495f-b400-44ae-b800-b82e8634009a','RDC','Bureau 90',1),(2,'1752eac4-2d22-418f-99a5-0cabc36288c5','Tile Setter OR Marbl','dcdb2590-11ac-4e04-bd16-79aed235d8ec','09128243-6f6a-4b33-a7ae-7b43b94277cd','2','Bureau 66',1),(3,'ad3efff3-52e2-4e47-bf62-2d17b4324e82','Pressure Vessel Insp','05ce8f09-4551-4332-bb32-86d4db6902b6','4299daf7-e238-4f1d-9692-71df1ac7cc2e','1','Bureau 21',1),(4,'b291558f-514c-4111-b197-a0e34351aa16','Woodworker','05ce8f09-4551-4332-bb32-86d4db6902b6','11ea98e3-2c42-453e-aa58-49f1afff24b0','B','Bureau 11',1),(5,'bd0eb6eb-8018-40b3-9830-e267406f4844','Probation Officers a','61c3f03b-a3a8-43d4-b917-40e1c6c5d089','7f2ae275-bede-457b-89b7-66536a09a6a7','2','Bureau 64',0),(6,'082ce931-7c0b-45d7-b5a4-127fd7a08395','Chemical Engineer','f1a52261-f519-4324-a668-3941c39d7cd3','8cb07c77-5be7-4824-95d4-3b4ec75c146b','RDC','Bureau 72',1),(7,'729df0ae-8980-49bb-affa-7d8e5f4842c5','Offset Lithographic ','c4196136-dc09-48c8-8b83-baa6d45b7eff','b984782e-2576-49a6-aee8-4b5bdff02e8d','B','Bureau 97',1),(8,'f7340df9-def2-428e-9b13-8816cb7b4b50','Protective Service W','05ce8f09-4551-4332-bb32-86d4db6902b6','e4dea000-72cf-40ae-929a-20e33a36fe50','B','Bureau 90',1),(9,'ba433888-1d6f-41b3-8182-b0e1017c8fb2','Soil Conservationist','150dc506-49c1-4b32-a5b4-92ad8326d634','5ffa7b8b-31fe-49df-98c4-38c6de2d9603','1','Bureau 33',1),(10,'c3764420-d906-4799-bef0-315324c50c8e','Court Clerk','4dc75397-084c-41bb-8fb1-e596f3dc2a65','47e95852-f4f4-4e64-940f-9adbd5b0edfb','2','Bureau 81',1),(11,'5834b25b-efbc-452a-9c53-562d55671a51','Machinery Maintenanc','3e339ff6-13ee-4efc-8eb0-872c6c3c1972','062b1644-ff70-47d9-89b0-a8adac5ea0a6','1','Bureau 42',0),(12,'d68e52e8-8a9c-4818-ab8f-a746ebd2e42c','Freight and Material','3e339ff6-13ee-4efc-8eb0-872c6c3c1972','9d61c4dd-f867-40a4-b36c-26b77fb2d335','2','Bureau 50',1),(13,'cb598d97-4d79-474a-8fc6-c096ad9f6129','Special Force','9740bc28-b8ec-423e-9243-1005b4594dd8','39638505-8d4c-4b81-b3f4-24f196837e16','B','Bureau 60',1),(14,'df4e4ffb-c31f-45b4-8876-80b6445fad5c','Psychiatric Technici','c4196136-dc09-48c8-8b83-baa6d45b7eff','c0941baf-ade5-494b-ab64-9cc454c9d1a8','RDC','Bureau 20',1),(15,'09c63c2a-2164-4e64-86f2-48ee30acf32c','Freight Inspector','f1a52261-f519-4324-a668-3941c39d7cd3','9bb56309-f9fe-4be1-bd56-2e9b56b5bd59','2','Bureau 88',1),(16,'9e8f7df6-4f00-43e8-a4a8-37dba79fe0db','Utility Meter Reader','150dc506-49c1-4b32-a5b4-92ad8326d634','b9d5a85d-cd9d-47e6-ba1e-f5be6f11bc7f','B','Bureau 74',1),(17,'bc7c57df-4e5c-4d1c-ae9b-f987b19d4dd2','Transportation Worke','61c3f03b-a3a8-43d4-b917-40e1c6c5d089','8c9dc4cc-d4c8-42ef-a375-c1511cc562b8','1','Bureau 11',1),(18,'d1fef6eb-05f4-45f3-84a6-031a6dc0a4db','Instrument Sales Rep','3e339ff6-13ee-4efc-8eb0-872c6c3c1972','161beb6d-9eee-43d8-b701-ea0dae69e855','1','Bureau 07',1),(19,'f52437c5-32d2-4ef1-93e8-75c94c0ec9ca','Actuary','f1a52261-f519-4324-a668-3941c39d7cd3','32233eba-1f16-42c5-b1f4-138ed81e30c7','1','Bureau 45',1),(20,'511a3b34-d20d-43e4-83ec-e854814c631b','Driver-Sales Worker','b254bc3a-0e7a-4ca7-9939-6a2172f85c19','465542fb-6afb-4bbc-868e-dbdf3337f187','RDC','Bureau 56',1),(21,'3d72f133-a9f1-4a62-813e-d2d1e30f0125','Engraver','c4196136-dc09-48c8-8b83-baa6d45b7eff','5edfc6f5-628c-4b28-a261-74bbd626f089','B','Bureau 53',1),(22,'7e440133-9193-4b8b-9f77-3d2e1ce63f72','Real Estate Broker','b254bc3a-0e7a-4ca7-9939-6a2172f85c19','39405667-35cd-44d8-ba97-404f639ee4bc','RDC','Bureau 95',1),(23,'c93df5a7-64bb-4561-bd0d-01c5baaa205e','Copy Machine Operato','4dc75397-084c-41bb-8fb1-e596f3dc2a65','56f21624-2acd-4535-83e7-4782f913f54f','2','Bureau 76',0),(24,'21bc7ef1-a774-4755-b31b-20d3e09bd6d0','Cartoonist','3e339ff6-13ee-4efc-8eb0-872c6c3c1972','a3011198-148b-4119-b64c-c415b7cd925e','2','Bureau 56',1),(25,'61af5f10-b63b-4010-a1c7-315bcec5a159','Fabric Pressers','a961c2e7-c64c-49cc-a8a6-85ce2525aa92','ae483742-38ff-4d81-ac93-3cf2a9663257','2','Bureau 79',1),(26,'b1876c59-b93e-4d30-b2e5-8b21404eef41','Manager of Weapons S','ea156fac-eba9-4541-8032-4bbcd61eab54','a76a3623-fccb-4694-a06b-eb0b70f68a0a','1','Bureau 09',1),(27,'9e09f5fc-6079-42d2-b2b4-04276ede30bc','Religious Worker','61c3f03b-a3a8-43d4-b917-40e1c6c5d089','d1fa387a-cd07-47fb-8889-ae83ba83538a','2','Bureau 20',1),(28,'c47a70cb-a955-4141-98de-15e386f1df05','Food Preparation Wor','a961c2e7-c64c-49cc-a8a6-85ce2525aa92','ec2d1c01-9adb-4b08-ab9c-3e0cdd5c6164','B','Bureau 47',1),(29,'4f5bef3a-fce0-4069-a103-98a749fcef09','Physician Assistant','f1a52261-f519-4324-a668-3941c39d7cd3','1303795e-aef4-4b94-8e57-2dc70c9fc501','RDC','Bureau 73',0),(30,'a7f9afb3-752b-47b8-8892-77c0d9a9a02b','Septic Tank Servicer','b254bc3a-0e7a-4ca7-9939-6a2172f85c19','10fc0325-3da5-4318-9df4-af1cd01351a2','1','Bureau 71',0),(31,'31029597-c157-4ffa-b669-807dd609d2c5','Gas Pumping Station ','56a0ec48-5f29-4778-85cb-f347275fcf95','58f41856-9d3f-4ca5-ae52-44282b0799a9','RDC','Bureau 37',0);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_types`
--

DROP TABLE IF EXISTS `expense_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expense_types_uuid_unique` (`uuid`),
  KEY `expense_types_created_by_foreign` (`created_by`),
  KEY `expense_types_updated_by_foreign` (`updated_by`),
  CONSTRAINT `expense_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `expense_types_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_types`
--

LOCK TABLES `expense_types` WRITE;
/*!40000 ALTER TABLE `expense_types` DISABLE KEYS */;
INSERT INTO `expense_types` VALUES (1,'a6c96f59-6b0b-4f7e-af07-e4b517da8117','Depense 1',0,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(2,'1a7fe5c5-7a5a-47a2-b17c-839c6e4abbe3','Depense 2',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(3,'8f35004b-c8a1-4706-9985-b7650d9aace8','Depense 3',0,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(4,'2f386608-631d-4e15-bac4-9743280388f6','Depense 4',0,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL);
/*!40000 ALTER TABLE `expense_types` ENABLE KEYS */;
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
-- Table structure for table `file_types`
--

DROP TABLE IF EXISTS `file_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `file_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identifier` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_types_uuid_unique` (`uuid`),
  UNIQUE KEY `file_types_name_unique` (`name`),
  KEY `file_types_created_by_foreign` (`created_by`),
  KEY `file_types_updated_by_foreign` (`updated_by`),
  CONSTRAINT `file_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `file_types_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_types`
--

LOCK TABLES `file_types` WRITE;
/*!40000 ALTER TABLE `file_types` DISABLE KEYS */;
INSERT INTO `file_types` VALUES (1,'65a343b6-7e1b-4c41-b780-e7ce304fe95b','AD-HOC',NULL,NULL,NULL,NULL,1,'2026-05-18 11:35:50','2026-05-18 11:35:50',NULL,NULL),(2,'39bde3b6-427f-4cd3-81b9-c2338dca841f','CPMP',NULL,NULL,NULL,NULL,1,'2026-05-18 11:35:50','2026-05-18 11:35:50',NULL,NULL),(3,'f0248208-5401-48cf-9356-af4aeb9a4635','CNCMP',NULL,NULL,NULL,NULL,1,'2026-05-18 11:35:50','2026-05-18 11:35:50',NULL,NULL);
/*!40000 ALTER TABLE `file_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funding_sources`
--

DROP TABLE IF EXISTS `funding_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funding_sources` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `structure_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `funding_sources_uuid_unique` (`uuid`),
  UNIQUE KEY `funding_sources_structure_uuid_name_unique` (`structure_uuid`,`name`),
  KEY `funding_sources_created_by_foreign` (`created_by`),
  KEY `funding_sources_updated_by_foreign` (`updated_by`),
  CONSTRAINT `funding_sources_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `funding_sources_structure_uuid_foreign` FOREIGN KEY (`structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `funding_sources_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funding_sources`
--

LOCK TABLES `funding_sources` WRITE;
/*!40000 ALTER TABLE `funding_sources` DISABLE KEYS */;
INSERT INTO `funding_sources` VALUES (1,'9655a402-f483-4504-bc37-762c19349d94',NULL,'Banque mondiale','Partenaire de financement institutionnel global',1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(2,'f097b72c-2932-4e8a-97de-e8002fe9a2b2',NULL,'Union Européenne','Soutien au développement durable',1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(3,'5f97d913-4aeb-4c13-8807-f35f5e0b547c',NULL,'Fonds propre de l\'État','Financement public national',1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(4,'e38bb49c-124b-48fb-a30c-f4d7ec7e4c4f',NULL,'Partenariat public-privé','Collaboration entre secteurs public et privé',1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(5,'438af312-de77-4dab-bea3-74f76fb45c12',NULL,'Programme des Nations Unies pour le Développement (PNUD)','Agence de l\'ONU dédiée au développement',1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(6,'cdec5c14-26e2-48bb-83d0-994205a4615c',NULL,'Budget National','Financement interne par l\'État',1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL);
/*!40000 ALTER TABLE `funding_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `indicator_categories`
--

DROP TABLE IF EXISTS `indicator_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `indicator_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `indicator_categories_uuid_unique` (`uuid`),
  UNIQUE KEY `indicator_categories_name_unique` (`name`),
  KEY `indicator_categories_created_by_foreign` (`created_by`),
  KEY `indicator_categories_updated_by_foreign` (`updated_by`),
  CONSTRAINT `indicator_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `indicator_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `indicator_categories`
--

LOCK TABLES `indicator_categories` WRITE;
/*!40000 ALTER TABLE `indicator_categories` DISABLE KEYS */;
INSERT INTO `indicator_categories` VALUES (1,'06295260-8734-4b19-b575-fc455b48088b','Performance Financière',1,'2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL),(2,'da1d0e85-47b4-42cc-b473-0cb79a0648ee','Satisfaction Client',1,'2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL),(3,'44eea676-a933-4efb-9a27-53f23154c453','Processus Internes',1,'2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL),(4,'f6243d29-4b46-4aab-bcb2-15925a229610','Apprentissage et Croissance',0,'2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL);
/*!40000 ALTER TABLE `indicator_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `indicator_controls`
--

DROP TABLE IF EXISTS `indicator_controls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `indicator_controls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `indicator_period_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `control_date` date NOT NULL,
  `target_value` decimal(12,2) NOT NULL,
  `achieved_value` decimal(12,2) NOT NULL,
  `root_cause` text COLLATE utf8mb4_unicode_ci,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `indicator_controls_uuid_unique` (`uuid`),
  KEY `indicator_controls_indicator_period_uuid_foreign` (`indicator_period_uuid`),
  KEY `indicator_controls_created_by_foreign` (`created_by`),
  KEY `indicator_controls_updated_by_foreign` (`updated_by`),
  CONSTRAINT `indicator_controls_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `indicator_controls_indicator_period_uuid_foreign` FOREIGN KEY (`indicator_period_uuid`) REFERENCES `indicator_periods` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `indicator_controls_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `indicator_controls`
--

LOCK TABLES `indicator_controls` WRITE;
/*!40000 ALTER TABLE `indicator_controls` DISABLE KEYS */;
/*!40000 ALTER TABLE `indicator_controls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `indicator_periods`
--

DROP TABLE IF EXISTS `indicator_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `indicator_periods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `indicator_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `target_value` decimal(12,2) NOT NULL,
  `achieved_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `indicator_periods_uuid_unique` (`uuid`),
  KEY `indicator_periods_indicator_uuid_foreign` (`indicator_uuid`),
  CONSTRAINT `indicator_periods_indicator_uuid_foreign` FOREIGN KEY (`indicator_uuid`) REFERENCES `indicators` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `indicator_periods`
--

LOCK TABLES `indicator_periods` WRITE;
/*!40000 ALTER TABLE `indicator_periods` DISABLE KEYS */;
/*!40000 ALTER TABLE `indicator_periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `indicator_statuses`
--

DROP TABLE IF EXISTS `indicator_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `indicator_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `indicator_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `indicator_id` bigint NOT NULL,
  `status_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_date` timestamp NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `indicator_statuses_uuid_unique` (`uuid`),
  KEY `indicator_statuses_created_by_foreign` (`created_by`),
  KEY `indicator_statuses_updated_by_foreign` (`updated_by`),
  KEY `indicator_statuses_indicator_uuid_index` (`indicator_uuid`),
  KEY `indicator_statuses_indicator_id_index` (`indicator_id`),
  CONSTRAINT `indicator_statuses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `indicator_statuses_indicator_uuid_foreign` FOREIGN KEY (`indicator_uuid`) REFERENCES `indicators` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `indicator_statuses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `indicator_statuses`
--

LOCK TABLES `indicator_statuses` WRITE;
/*!40000 ALTER TABLE `indicator_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `indicator_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `indicators`
--

DROP TABLE IF EXISTS `indicators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `indicators` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `structure_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_map_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_element_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_objective_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lead_structure_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chart_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frequency_unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency_value` int DEFAULT NULL,
  `initial_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `final_target_value` decimal(12,2) NOT NULL,
  `achieved_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'created',
  `state` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `status_changed_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_changed_at` timestamp NULL DEFAULT NULL,
  `is_planned` tinyint(1) NOT NULL DEFAULT '0',
  `actual_start_date` date DEFAULT NULL,
  `actual_end_date` date DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `indicators_uuid_unique` (`uuid`),
  UNIQUE KEY `indicators_reference_unique` (`reference`),
  KEY `indicators_structure_uuid_foreign` (`structure_uuid`),
  KEY `indicators_strategic_map_uuid_foreign` (`strategic_map_uuid`),
  KEY `indicators_strategic_element_uuid_foreign` (`strategic_element_uuid`),
  KEY `indicators_strategic_objective_uuid_foreign` (`strategic_objective_uuid`),
  KEY `indicators_lead_structure_uuid_foreign` (`lead_structure_uuid`),
  KEY `indicators_category_uuid_foreign` (`category_uuid`),
  KEY `indicators_status_changed_by_foreign` (`status_changed_by`),
  KEY `indicators_created_by_foreign` (`created_by`),
  KEY `indicators_updated_by_foreign` (`updated_by`),
  CONSTRAINT `indicators_category_uuid_foreign` FOREIGN KEY (`category_uuid`) REFERENCES `indicator_categories` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `indicators_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `indicators_lead_structure_uuid_foreign` FOREIGN KEY (`lead_structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `indicators_status_changed_by_foreign` FOREIGN KEY (`status_changed_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `indicators_strategic_element_uuid_foreign` FOREIGN KEY (`strategic_element_uuid`) REFERENCES `strategic_elements` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `indicators_strategic_map_uuid_foreign` FOREIGN KEY (`strategic_map_uuid`) REFERENCES `strategic_maps` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `indicators_strategic_objective_uuid_foreign` FOREIGN KEY (`strategic_objective_uuid`) REFERENCES `strategic_objectives` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `indicators_structure_uuid_foreign` FOREIGN KEY (`structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `indicators_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `indicators`
--

LOCK TABLES `indicators` WRITE;
/*!40000 ALTER TABLE `indicators` DISABLE KEYS */;
INSERT INTO `indicators` VALUES (1,'732e3a4f-5635-45a8-8ea5-7a1ea85c00bb','OBJ-001_IND001','Taux de scolarisation primaire','Pourcentage d\'enfants inscrits à l\'école primaire.','2025-01-10','2025-06-30','56a0ec48-5f29-4778-85cb-f347275fcf95','fae3b1c4-578c-474d-a03f-3953f2f091d6','e679e743-67ea-431a-8493-665cb27cb7c9','a7aa7d10-ddae-473c-b5af-e1697c96c6aa','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','06295260-8734-4b19-b575-fc455b48088b','BAR','months',1,65.75,90.50,0.00,'%','in_progress','none',NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(2,'880f598b-0dd4-461f-9038-cf8a835ab52f','OBJ-001_IND002','Nombre de centres de santé fonctionnels','Indique le nombre total de centres opérationnels.','2025-01-10','2025-06-30','56a0ec48-5f29-4778-85cb-f347275fcf95','fae3b1c4-578c-474d-a03f-3953f2f091d6','e679e743-67ea-431a-8493-665cb27cb7c9','a7aa7d10-ddae-473c-b5af-e1697c96c6aa','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','06295260-8734-4b19-b575-fc455b48088b','LINE','months',3,50.25,120.60,0.00,'kilomètre','in_progress','none',NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49');
/*!40000 ALTER TABLE `indicators` ENABLE KEYS */;
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
-- Table structure for table `matrix_periods`
--

DROP TABLE IF EXISTS `matrix_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matrix_periods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_map_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matrix_periods_uuid_unique` (`uuid`),
  KEY `matrix_periods_strategic_map_uuid_foreign` (`strategic_map_uuid`),
  KEY `matrix_periods_created_by_foreign` (`created_by`),
  KEY `matrix_periods_updated_by_foreign` (`updated_by`),
  CONSTRAINT `matrix_periods_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `matrix_periods_strategic_map_uuid_foreign` FOREIGN KEY (`strategic_map_uuid`) REFERENCES `strategic_maps` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `matrix_periods_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matrix_periods`
--

LOCK TABLES `matrix_periods` WRITE;
/*!40000 ALTER TABLE `matrix_periods` DISABLE KEYS */;
INSERT INTO `matrix_periods` VALUES (1,'0478ef88-84ca-4772-9e15-c22a4b00be82','055179dc-2035-46b7-82cc-71dc56edf8fb','2025-01-01','2025-06-30',NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(2,'4b1f5c82-44ee-43ac-ba10-5e405733b12e','9e6de6d6-b940-4e21-ab55-fa35c82eda8b','2025-07-01','2025-12-31',NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(3,'e8bc307b-5d72-4335-ac77-933d1afb41e9','9e6de6d6-b940-4e21-ab55-fa35c82eda8b','2026-01-01','2026-12-31',NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49');
/*!40000 ALTER TABLE `matrix_periods` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_01_03_131026_add_columns_to_users_table',1),(5,'2025_04_23_003906_create_structures_table',1),(6,'2025_04_23_003915_create_employees_table',1),(7,'2025_04_26_234502_create_personal_access_tokens_table',1),(8,'2025_05_12_023038_create_currencies_table',1),(9,'2025_05_12_024133_create_funding_sources_table',1),(10,'2025_05_13_004636_create_action_plans_table',1),(11,'2025_07_03_175659_create_regions_table',1),(12,'2025_07_04_001823_create_procurement_modes_table',1),(13,'2025_07_04_102213_create_project_owners_table',1),(14,'2025_07_04_122509_create_departments_table',1),(15,'2025_07_05_020521_create_municipalities_table',1),(16,'2025_07_05_020523_create_action_domains_table',1),(17,'2025_07_05_020526_create_strategic_domains_table',1),(18,'2025_07_05_231017_create_delegated_project_owners_table',1),(19,'2025_07_07_001519_create_capability_domains_table',1),(20,'2025_07_07_001520_create_elementary_levels_table',1),(21,'2025_07_21_132103_create_actions_table',1),(22,'2025_07_21_133709_create_beneficiaries_table',1),(23,'2025_07_21_161138_create_stakeholders_table',1),(24,'2025_07_24_085807_create_action_beneficiaries_table',1),(25,'2025_07_24_085839_create_action_stakeholders_table',1),(26,'2025_07_24_085856_create_action_funding_sources_table',1),(27,'2025_08_05_130114_create_payment_modes_table',1),(28,'2025_08_05_202620_create_action_periods_table',1),(29,'2025_08_06_091614_create_expense_types_table',1),(30,'2025_08_06_101728_create_budget_types_table',1),(31,'2025_08_06_101834_create_action_phases_table',1),(32,'2025_08_06_101835_create_suppliers_table',1),(33,'2025_08_06_101836_create_contracts_table',1),(34,'2025_08_06_101837_create_supplier_evaluations_table',1),(35,'2025_08_06_102249_create_tasks_table',1),(36,'2025_08_06_102250_create_action_fund_disbursements_table',1),(37,'2025_08_06_124532_create_action_fund_receipts_table',1),(38,'2025_08_19_123433_create_strategic_maps_table',1),(39,'2025_08_20_102705_create_strategic_elements_table',1),(40,'2025_08_20_121632_matrix_periods_table',1),(41,'2025_08_20_121633_create_strategic_objectives_table',1),(42,'2025_08_20_121634_create_file_types_table',1),(43,'2025_08_20_163220_create_attachments_table',1),(44,'2025_08_21_200634_create_action_controls_table',1),(45,'2025_08_21_200643_create_action_control_phases_table',1),(46,'2025_08_26_135316_create_decisions_table',1),(47,'2025_08_26_135317_create_decision_statuses_table',1),(48,'2025_08_26_215701_create_strategic_stakeholders_table',1),(49,'2025_08_28_134440_create_indicator_categories_table',1),(50,'2025_08_28_134445_create_indicators_table',1),(51,'2025_08_28_191224_create_indicator_periods_table',1),(52,'2025_08_31_134914_create_indicator_controls_table',1),(53,'2025_09_01_061858_action_fund_disbursement_expense_types',1),(54,'2025_09_07_080019_create_action_objective_alignments_table',1),(55,'2025_09_09_064346_create_activity_log_table',1),(56,'2025_09_09_064347_add_event_column_to_activity_log_table',1),(57,'2025_09_09_064348_add_batch_uuid_column_to_activity_log_table',1),(58,'2025_09_23_112150_create_action_metrics_table',1),(59,'2025_09_23_121623_create_structure_metrics_table',1),(60,'2025_10_26_162028_program_funding_sources',1),(61,'2025_10_26_162051_program_beneficiaries',1),(62,'2025_10_27_113007_create_action_statuses_table',1),(63,'2025_10_27_113007_create_strategic_objective_statuses_table',1),(64,'2025_10_27_130733_create_action_domain_statuses_table',1),(65,'2025_10_27_130743_create_action_domain_states_table',1),(66,'2025_10_28_062749_create_strategic_domain_statuses_table',1),(67,'2025_10_28_062754_create_strategic_domain_states_table',1),(68,'2025_10_28_062764_project_funding_sources',1),(69,'2025_10_28_062774_project_beneficiaries',1),(70,'2025_10_28_062784_create_capability_domain_statuses_table',1),(71,'2025_10_28_062794_create_capability_domain_states_table',1),(72,'2025_10_28_062796_activity_funding_sources',1),(73,'2025_10_28_062798_activity_beneficiaries',1),(74,'2025_11_04_131643_create_default_phases_table',1),(75,'2025_11_09_065604_create_roles_table',1),(76,'2025_11_09_065701_create_permissions_table',1),(77,'2025_11_09_065735_role_permissions_table',1),(78,'2025_11_09_070110_add_role_to_users_table',1),(79,'2025_11_11_235945_add_parent_fk_to_strategic_elements_table',1),(80,'2025_11_25_132554_create_indicator_statuses_table',1),(81,'2025_12_04_194057_elementary_level_funding_sources',1),(82,'2025_12_04_194459_elementary_level_beneficiaries',1),(83,'2025_12_04_201130_create_elementary_level_states_table',1),(84,'2025_12_04_201208_create_elementary_level_statuses_table',1),(85,'2026_03_13_173219_add_reference_to_strategic_elements_table',1),(86,'2026_04_22_120000_add_dates_to_indicators_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `municipalities`
--

DROP TABLE IF EXISTS `municipalities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `municipalities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `municipalities_uuid_unique` (`uuid`),
  UNIQUE KEY `municipalities_name_unique` (`name`),
  KEY `municipalities_department_uuid_foreign` (`department_uuid`),
  KEY `municipalities_created_by_foreign` (`created_by`),
  KEY `municipalities_updated_by_foreign` (`updated_by`),
  CONSTRAINT `municipalities_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `municipalities_department_uuid_foreign` FOREIGN KEY (`department_uuid`) REFERENCES `departments` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `municipalities_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `municipalities`
--

LOCK TABLES `municipalities` WRITE;
/*!40000 ALTER TABLE `municipalities` DISABLE KEYS */;
INSERT INTO `municipalities` VALUES (1,'0648f81f-8a72-47c2-aa51-a0e82899894a','Port Camylle','c3246e1b-6260-4279-b845-d60eb30fba2b',1,-4.840597,92.797775,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(2,'b018dad7-d788-4be9-86ea-55ac6451a31f','Webertown','de14f568-ff4e-4e87-a448-f307a8c3c9c5',1,-63.473368,-175.734466,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(3,'ff74466b-8fcb-451b-8735-c3a8ab2a325f','Lake Ila','fe039939-2914-459c-a9eb-9f9833fd994f',1,66.794010,22.161725,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(4,'53410f9c-432e-4406-94e8-50b1b3e4d47e','North Lillaville','ec9ddd80-db02-433e-8843-98a3ce883b56',1,9.194050,41.021771,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(5,'9be4926c-a570-48e5-8286-9a9f90f01ead','Port Kaley','fe039939-2914-459c-a9eb-9f9833fd994f',1,42.307056,-29.441497,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL);
/*!40000 ALTER TABLE `municipalities` ENABLE KEYS */;
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
-- Table structure for table `payment_modes`
--

DROP TABLE IF EXISTS `payment_modes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_modes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_modes_uuid_unique` (`uuid`),
  UNIQUE KEY `payment_modes_name_unique` (`name`),
  KEY `payment_modes_created_by_foreign` (`created_by`),
  KEY `payment_modes_updated_by_foreign` (`updated_by`),
  CONSTRAINT `payment_modes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `payment_modes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_modes`
--

LOCK TABLES `payment_modes` WRITE;
/*!40000 ALTER TABLE `payment_modes` DISABLE KEYS */;
INSERT INTO `payment_modes` VALUES (1,'5f3ac087-77c8-4b26-8bd5-3feec7623567','Espèces',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(2,'428fe39b-b625-4d6b-b7f2-9e428d53971d','Carte de crédit',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(3,'80435438-22b8-4a43-8c94-343a49ece0cc','Virement bancaire',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(4,'9890db9d-2424-4965-afe3-00d3a05ab6a2','Mobile Money',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(5,'5380c81e-3436-40e9-a318-c814e19dfac4','PayPal',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL);
/*!40000 ALTER TABLE `payment_modes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_uuid_unique` (`uuid`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=212 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dc084571-7ce5-49ec-8b19-bbf6203ceace','ACCESS_STRATEGIC_MAPS','Carte stratégique','Accès au module'),(2,'7b97bc2a-9f78-4d96-b7ae-b480ca15425d','CREATE_STRATEGIC_MAP','Carte stratégique','Créer une carte'),(3,'251cf2fa-efa1-4d95-b888-a045fa2b8cc8','READ_ALL_STRATEGIC_MAPS','Carte stratégique','Voir toutes les cartes'),(4,'5eb4be6d-021b-4506-a644-5f3b6d7bbc9c','READ_SINGLE_STRATEGIC_MAP','Carte stratégique','Voir une carte'),(5,'a3469f6d-c368-46f4-b46a-f67d459b8b85','UPDATE_STRATEGIC_MAP','Carte stratégique','Modifier une carte'),(6,'95e0c8ec-b27b-478f-82fc-32a15d6424b0','DELETE_STRATEGIC_MAP','Carte stratégique','Supprimer une carte'),(7,'1f690149-d2be-4be2-a8a5-3271e3036d1c','ACCESS_PRIORITY_MATRIX','Matrice de priorité','Accès au module'),(8,'eb5054a7-340d-4b16-8f89-0ba57ee298eb','MANAGE_PRIORITY_MATRIX','Matrice de priorité','Gérer une matrice'),(9,'85b05260-9522-4feb-bf98-a52bf8d7aeb2','ACCESS_MAP_STAKEHOLDERS','Partie prenante (Carte)','Accès au module'),(10,'cadbcac0-2b11-4f2a-b9fb-798b897c9f23','MANAGE_MAP_STAKEHOLDERS','Partie prenante (Carte)','Gérer les parties prenantes'),(11,'d26cf020-fe13-4a7f-aec5-06e3152134d6','ACCESS_STRATEGIC_LEVERS','Levier stratégique','Accès au module'),(12,'9dcc1a89-3042-4018-b272-86d22b7f8c05','CREATE_STRATEGIC_LEVER','Levier stratégique','Créer un levier'),(13,'5edc1dc1-8b0f-440d-82b1-40e8165aae4b','READ_STRATEGIC_LEVERS','Levier stratégique','Voir les leviers'),(14,'6b2c3560-6d62-467a-b8c2-070231d34360','UPDATE_STRATEGIC_LEVER','Levier stratégique','Modifier un levier'),(15,'3fd10ecd-c20a-4f33-b98b-62394a0cf14d','DELETE_STRATEGIC_LEVER','Levier stratégique','Supprimer un levier'),(16,'68d798b8-4b45-4299-bcc5-d6e2d2e37f59','ACCESS_STRATEGIC_AXES','Axe stratégique','Accès au module'),(17,'ba2c6ac9-66f3-41a1-948a-7c41ba86b4bc','CREATE_STRATEGIC_AXIS','Axe stratégique','Créer un axe'),(18,'af01c25c-8ec1-42df-ab49-0dbe4dbc4a8b','READ_STRATEGIC_AXES','Axe stratégique','Voir les axes'),(19,'41d56edc-754d-4ff6-a4c6-c20e2482a053','UPDATE_STRATEGIC_AXIS','Axe stratégique','Modifier un axe'),(20,'14b045eb-a689-45b7-b4a9-c616607561ef','DELETE_STRATEGIC_AXIS','Axe stratégique','Supprimer un axe'),(21,'49cee8b3-006a-4a29-b4df-fbafe5df49ad','ACCESS_STRATEGIC_OBJECTIVES','Objectif stratégique','Accès au module'),(22,'ad3a6d90-d20d-4797-9cb8-22c4bbd8d004','CREATE_STRATEGIC_OBJECTIVE','Objectif stratégique','Créer un objectif'),(23,'e5320b04-0c49-4e30-af8a-453e0f8d06dc','READ_STRATEGIC_OBJECTIVES','Objectif stratégique','Voir les objectifs'),(24,'a106efc9-9052-43b8-ab8e-1f8d895651cc','UPDATE_STRATEGIC_OBJECTIVE','Objectif stratégique','Modifier un objectif'),(25,'a3328b14-c711-4754-80cd-b584754fa906','DELETE_STRATEGIC_OBJECTIVE','Objectif stratégique','Supprimer un objectif'),(26,'715da3ad-f978-4b5b-9648-c9ff50df3a08','OBJ_ACCESS_STATUS','Objectif — Statut','Accès au statut'),(27,'772da9ae-d55c-46ec-b6ca-7503858f5130','OBJ_MANAGE_STATUS','Objectif — Statut','Gérer le statut'),(28,'b799101c-7e47-46c2-9188-8455698164b7','OBJ_ACCESS_ALIGNMENT','Objectif — Alignement','Accès à l\'onglet'),(29,'b5864e70-5a01-44aa-a5e8-a9f65ce13cbc','OBJ_MANAGE_ALIGNMENT','Objectif — Alignement','Gérer l\'alignement'),(30,'94a9cda4-a676-4f56-9df7-baca158ed591','OBJ_ACCESS_DECISIONS','Objectif — Décision','Accès au module'),(31,'57177561-cc2c-46cb-b30c-2ad81dcc0e45','OBJ_MANAGE_DECISIONS','Objectif — Décision','Gérer les décisions'),(32,'6dd335c7-76ea-4b5a-a926-300bb35f70d8','OBJ_ACCESS_FILES','Objectif — Fichier','Accès aux fichiers'),(33,'2986c62b-061f-4272-99f7-135559ebd088','OBJ_MANAGE_FILES','Objectif — Fichier','Gérer les fichiers'),(34,'cca38191-c969-44e8-b4e3-6d43be6fe618','ACCESS_INDICATORS','Indicateur','Accès au module'),(35,'ecb0043c-b63b-42d5-b915-eea6791fdaa8','CREATE_INDICATOR','Indicateur','Créer un indicateur'),(36,'275edae4-14ff-4314-8e1b-f6433256c033','READ_INDICATORS','Indicateur','Voir les indicateurs'),(37,'55e89f14-3c99-4b80-b620-1f98bbc834b6','UPDATE_INDICATOR','Indicateur','Modifier un indicateur'),(38,'d95aea33-bbe0-40ef-ad4c-484a56ccac6b','DELETE_INDICATOR','Indicateur','Supprimer un indicateur'),(39,'de588a9d-28d4-40d9-a221-b5acf5e4e760','IND_ACCESS_STATUS','Indicateur — Statut','Accès au statut'),(40,'25891c45-4496-4d37-b210-8a23b4c5f215','IND_MANAGE_STATUS','Indicateur — Statut','Gérer le statut'),(41,'055dae4f-d217-40db-a0d4-2e9c59e81eea','IND_ACCESS_PLANNING','Indicateur — Planification','Accès à la planification'),(42,'9ef10e7d-ac7d-4657-931a-7bee2356e448','IND_MANAGE_PLANNING','Indicateur — Planification','Gérer la planification'),(43,'797d749f-25aa-40b4-88e5-4ae449c10d3a','IND_ACCESS_CONTROL','Indicateur — Contrôle','Accès au contrôle'),(44,'4baf6e8e-1f55-40ee-baaf-95033567b3b0','IND_MANAGE_CONTROL','Indicateur — Contrôle','Gérer les contrôles'),(45,'b1ef0ccd-201d-4cdf-b53f-67a596226f8b','IND_ACCESS_FILES','Indicateur — Fichier','Accès aux fichiers'),(46,'e613dc6e-1a0b-4d00-a89d-914750d14d42','IND_MANAGE_FILES','Indicateur — Fichier','Gérer les fichiers'),(47,'4a462297-7c7f-45c5-896b-00bff9f7f2bb','IND_ACCESS_DECISIONS','Indicateur — Décision','Accès aux décisions'),(48,'63995e50-4cfa-4b65-9d3e-c493ed398b93','IND_MANAGE_DECISIONS','Indicateur — Décision','Gérer les décisions'),(49,'5e188055-2a4b-4c78-aa62-8847a86d34aa','IND_ACCESS_REPORTING','Indicateur reporting','Accès au reporting'),(50,'18df77aa-417c-41d7-8261-1011e3b403f3','ACCESS_ACTION_PLANS','Plan d\'action','Accès au module'),(51,'6c4d832a-d2ce-4de9-9190-65d14d33d6c3','CREATE_ACTION_PLAN','Plan d\'action','Créer un plan'),(52,'ae0cfbae-df99-4c8b-ab8a-157ee61d5f3e','READ_ACTION_PLANS','Plan d\'action','Voir les plans'),(53,'f41001a7-9bf3-4458-a4df-efda4bd27e31','UPDATE_ACTION_PLAN','Plan d\'action','Modifier un plan'),(54,'bd6a4a31-9c23-4231-86a2-dc5d33a133cc','DELETE_ACTION_PLAN','Plan d\'action','Supprimer un plan'),(55,'9bb5883f-be34-4433-bbff-c1790e2fd1fc','ACCESS_ACTIONS','Action','Accès au module'),(56,'37464ea7-1646-4136-8319-99614c779c51','CREATE_ACTION','Action','Créer une action'),(57,'1a8a402f-a006-4205-af7c-5b9e2224183b','READ_ACTIONS','Action','Voir les actions'),(58,'3332ffa7-4a58-4e65-8b95-5263318f96d7','UPDATE_ACTION','Action','Modifier une action'),(59,'df8336a4-5980-4cc6-9b73-927bb6150117','DELETE_ACTION','Action','Supprimer une action'),(60,'3ee09bb8-3460-42d8-9bb6-4df54c3571dc','ACT_ACCESS_STATUS','Action — Statut','Accès au statut'),(61,'43d47952-7f53-4f68-9c26-be43a6858587','ACT_MANAGE_STATUS','Action — Statut','Gérer le statut'),(62,'c62ff216-d1f4-4a6f-b256-fff9ceab9cc5','ACT_ACCESS_PLANNING','Action — Planification','Accès à la planification'),(63,'c65d7ef6-1744-4456-9aa2-d2c5799be336','ACT_MANAGE_PLANNING','Action — Planification','Gérer la planification'),(64,'55d04da5-6cb2-40f9-9b5d-3bce9117208c','ACT_ACCESS_CONTROL','Action — Contrôle','Accès au contrôle'),(65,'6cfa9553-2177-47f7-b146-a9874028f4a4','ACT_MANAGE_CONTROL','Action — Contrôle','Gérer les contrôles'),(66,'acea60a0-1b32-4539-be4b-92c316e03bcb','ACT_ACCESS_ALIGNMENT','Action — Alignement','Accès à l\'alignement'),(67,'b978d4d2-2340-4274-8162-06e63c6b1efc','ACT_MANAGE_ALIGNMENT','Action — Alignement','Gérer l\'alignement'),(68,'0b154b70-f925-41b2-97ba-b32c9139afae','ACT_ACCESS_FILES','Action — Fichier','Accès aux fichiers'),(69,'3dab6c95-23aa-4d81-aabe-6b88a7ca7b93','ACT_MANAGE_FILES','Action — Fichier','Gérer les fichiers'),(70,'9d168993-564a-491f-a108-c8bfaa89a4a9','ACT_ACCESS_DECISIONS','Action — Décision','Accès aux décisions'),(71,'ad160c77-48fd-4242-aaa3-1c852bedeb88','ACT_MANAGE_DECISIONS','Action — Décision','Gérer les décisions'),(72,'7d6179cd-6c8e-413f-a178-d7ab92248eea','ACT_ACCESS_PHASES','Action — Phase','Accès aux phases'),(73,'6648b963-5e28-423c-9003-764471d68bdb','ACT_MANAGE_PHASES','Action — Phase','Gérer les phases'),(74,'e9d6887e-0099-457f-b899-33242fd7d058','ACT_ACCESS_REPORTING','Action reporting','Accès au reporting'),(75,'b0a4815b-581e-4827-9754-a3251940667e','ACCESS_SUPPLIERS','Fournisseur','Accès au module'),(76,'38556ea7-a0bc-42f5-a8b5-b24747902f64','CREATE_SUPPLIER','Fournisseur','Créer un fournisseur'),(77,'9a59a546-6d42-4d85-8469-5230c4d9112f','READ_SUPPLIERS','Fournisseur','Voir les fournisseurs'),(78,'ec2f03f9-7439-40a7-b84d-e892dea93a13','UPDATE_SUPPLIER','Fournisseur','Modifier un fournisseur'),(79,'9a407e61-beb5-4200-bb7b-4a14d9d5da30','DELETE_SUPPLIER','Fournisseur','Supprimer un fournisseur'),(80,'51792dfc-f4b6-49ed-9192-f7c92847767f','SUP_ACCESS_CONTRACTS','Fournisseur — Contrats','Accès aux contrats'),(81,'424ac84f-6091-4ebe-b9e5-96d0c0231728','SUP_MANAGE_CONTRACTS','Fournisseur — Contrats','Gérer les contrats'),(82,'67c50a1f-27cc-4ef7-9acc-42ae23d84bfb','SUP_ACCESS_EVALUATIONS','Fournisseur — Évaluations','Accès aux évaluations'),(83,'83ef3447-f51a-44d4-812e-8f3fef9731ce','SUP_MANAGE_EVALUATIONS','Fournisseur — Évaluations','Gérer les évaluations'),(84,'888a13a4-f326-4362-8130-b3a226fb4214','SUP_ACCESS_FILES','Fournisseur — Fichiers','Accès aux fichiers'),(85,'9836771d-56b9-46a2-a00a-f5ead48bcf11','SUP_MANAGE_FILES','Fournisseur — Fichiers','Gérer les fichiers'),(86,'99efea3d-74dc-49e8-9caa-b85e473dc9d6','ACCESS_FUND_RECEIPTS','Encaissement','Accès au module'),(87,'755d0bd3-09da-40f0-a5ee-defef4ee5635','CREATE_FUND_RECEIPT','Encaissement','Créer un encaissement'),(88,'647bfee3-3efc-4b85-81eb-f7fcc964b820','READ_FUND_RECEIPTS','Encaissement','Voir les encaissements'),(89,'52166145-b830-469d-b1a7-64743e59b522','UPDATE_FUND_RECEIPT','Encaissement','Modifier un encaissement'),(90,'6df8fd52-ece0-4339-a46a-c3e1d1668346','DELETE_FUND_RECEIPT','Encaissement','Supprimer un encaissement'),(91,'50643346-c866-4c3c-b97d-c448cc73e54c','ACCESS_FUND_DISBURSEMENTS','Décaissement','Accès au module'),(92,'9b8b6e47-d59f-4e81-9829-57fec71ce817','CREATE_FUND_DISBURSEMENT','Décaissement','Créer un décaissement'),(93,'b0d2f5ee-9d76-4685-846d-3a810fbb7b2d','READ_FUND_DISBURSEMENTS','Décaissement','Voir les décaissements'),(94,'26e97abd-0eb1-426e-8929-741a05ad4e74','UPDATE_FUND_DISBURSEMENT','Décaissement','Modifier un décaissement'),(95,'e35d9b40-386c-4b2a-8a53-4fc4c3359f61','DELETE_FUND_DISBURSEMENT','Décaissement','Supprimer un décaissement'),(96,'c03e7f83-f8c5-4e33-a2d3-c70144327455','ACCESS_STRUCTURES','Structure','Accès au module'),(97,'4cbbac0b-7a05-486a-b6bc-6d4b049aff06','CREATE_STRUCTURE','Structure','Créer une structure'),(98,'652d4abc-8549-484e-8b88-b88a30c6d8e6','READ_STRUCTURES','Structure','Voir les structures'),(99,'afafc9f4-f0d6-435e-8c69-f7823b58fe4e','UPDATE_STRUCTURE','Structure','Modifier une structure'),(100,'29169f24-3e01-41b7-8639-6bf70e2b456f','DELETE_STRUCTURE','Structure','Supprimer une structure'),(101,'e2434da7-f513-42bb-89c5-373be07d10ac','ACCESS_EMPLOYEES','Personnel','Accès au module'),(102,'4d7a17fb-6c5e-43d3-8b60-193e6d4a1f58','CREATE_EMPLOYEE','Personnel','Créer un personnel'),(103,'5c8f5b84-fd82-42cd-9c76-8f8f89fefe43','READ_EMPLOYEES','Personnel','Voir le personnel'),(104,'e3f44ee8-a979-41fb-a88b-052814b681be','UPDATE_EMPLOYEE','Personnel','Modifier le personnel'),(105,'2ef24a77-e423-4a6e-b39f-8fcb0d5ce213','DELETE_EMPLOYEE','Personnel','Supprimer le personnel'),(106,'dc2426bf-c1c2-413d-8461-39f208297823','ACCESS_ACTION_DOMAINS','Domaine d\'action','Accès au module'),(107,'af4a180d-77ed-4819-b7c1-be650aca90f7','CREATE_ACTION_DOMAIN','Domaine d\'action','Créer un domaine d\'action'),(108,'c7ab40f8-b79d-4cf0-9ca0-8e404afc36e6','READ_ACTION_DOMAINS','Domaine d\'action','Voir les domaines d\'action'),(109,'7253c0b4-a34a-4ffd-8101-8d28d74a56fa','UPDATE_ACTION_DOMAIN','Domaine d\'action','Modifier un domaine d\'action'),(110,'9ee47c17-d504-4c73-8f81-98b87b8a92f1','DELETE_ACTION_DOMAIN','Domaine d\'action','Supprimer un domaine d\'action'),(111,'21d87a7d-2d06-430d-bad5-462e602474f7','ACCESS_STRATEGIC_DOMAINS','Domaine stratégique','Accès au module'),(112,'c555971e-4f3d-44fe-b3f2-ef52d343b999','CREATE_STRATEGIC_DOMAIN','Domaine stratégique','Créer un domaine stratégique'),(113,'0a9886ce-d79c-4731-8314-822a5447bab4','READ_STRATEGIC_DOMAINS','Domaine stratégique','Voir les domaines stratégiques'),(114,'2bf07884-07e7-4345-93fa-449d41eb49ff','UPDATE_STRATEGIC_DOMAIN','Domaine stratégique','Modifier un domaine stratégique'),(115,'d31b3c8e-c577-47d2-8d52-231af01bf58c','DELETE_STRATEGIC_DOMAIN','Domaine stratégique','Supprimer un domaine stratégique'),(116,'5b2763ec-66ab-4cfc-8a54-4183b6d07bcf','ACCESS_CAPABILITY_DOMAINS','Domaine capacitaire','Accès au module'),(117,'ca4c3280-993a-4e7d-a005-3e93d2b3a42d','CREATE_CAPABILITY_DOMAIN','Domaine capacitaire','Créer un domaine capacitaire'),(118,'1b5b2095-7109-4b16-855a-9efa28b0e756','READ_CAPABILITY_DOMAINS','Domaine capacitaire','Voir les domaines capacitaires'),(119,'ff60562b-fef2-419c-8ee5-5b379eda178f','UPDATE_CAPABILITY_DOMAIN','Domaine capacitaire','Modifier un domaine capacitaire'),(120,'ed0666be-22f1-4bd6-852b-41e2d7fb1248','DELETE_CAPABILITY_DOMAIN','Domaine capacitaire','Supprimer un domaine capacitaire'),(121,'b54c1984-943d-48ac-9775-1590dde7e2cd','ACCESS_ELEMENTARY_LEVELS','Niveau élémentaire','Accès au module'),(122,'bd79ae62-68f8-4693-b84c-8519efff90f6','CREATE_ELEMENTARY_LEVEL','Niveau élémentaire','Créer un niveau élémentaire'),(123,'672f9f21-f935-4f9d-b086-2948cc3ef8eb','READ_ELEMENTARY_LEVELS','Niveau élémentaire','Voir les niveaux élémentaires'),(124,'50bdd620-7088-4f38-97d3-a3e4c0efb4da','UPDATE_ELEMENTARY_LEVEL','Niveau élémentaire','Modifier un niveau élémentaire'),(125,'3b07c4bd-9700-40dd-9081-ff1e16b2bcdc','DELETE_ELEMENTARY_LEVEL','Niveau élémentaire','Supprimer un niveau élémentaire'),(126,'154c568c-b4ca-43c2-86be-d118d62132b4','ACCESS_REPORTING','Reporting','Accès au module'),(127,'a9bbb9bc-0d0f-4da3-8f7a-e8461e05a550','ACCESS_LOGS','Logs système','Accès au module'),(128,'ad33fb98-1070-4c6a-9814-640739c0fae3','ACCESS_CURRENCIES','Devise','Accès au module'),(129,'6a1a826a-4a67-485e-b884-b24728a43ba3','CREATE_CURRENCY','Devise','Créer une devise'),(130,'e8387376-fca9-4630-b96a-b14f735fe18f','READ_CURRENCIES','Devise','Voir les devises'),(131,'024efa8a-4f72-4fbb-b0b5-d928fb37e3e5','UPDATE_CURRENCY','Devise','Modifier une devise'),(132,'ff1d2cf0-ebe5-4bc1-945b-83e9c92ea27c','DELETE_CURRENCY','Devise','Supprimer une devise'),(133,'0f30fb9c-03c0-4487-bfcf-49867a642622','ACCESS_DEFAULT_PHASES','Phase par défaut','Accès au module'),(134,'d75e865e-b8a2-4746-a755-5b95008559df','CREATE_DEFAULT_PHASE','Phase par défaut','Créer une phase'),(135,'c2079c04-708e-42c1-8782-76b23a6411bc','UPDATE_DEFAULT_PHASE','Phase par défaut','Modifier une phase'),(136,'2ac80615-a6e1-47ca-a5ee-d731db7e4505','DELETE_DEFAULT_PHASE','Phase par défaut','Supprimer une phase'),(137,'9c8be356-ab23-4c0d-88d6-491f0ae38853','ACCESS_FILE_TYPES','Type de fichier','Accès au module'),(138,'2b4bff23-1068-4214-b16e-4805ba474b64','CREATE_FILE_TYPE','Type de fichier','Créer un type'),(139,'7173401e-f95d-471f-8862-e98e990238f1','READ_FILE_TYPES','Type de fichier','Voir les types'),(140,'c41c2ec1-ed42-40ed-a2bc-e17d009d719d','UPDATE_FILE_TYPE','Type de fichier','Modifier un type'),(141,'fcd8648c-afe4-4cac-8b08-912b9bf6dd25','DELETE_FILE_TYPE','Type de fichier','Supprimer un type'),(142,'d47a9bf4-30a7-4a44-9aad-3677371e7404','ACCESS_PROJECT_OWNERS','Maître d\'ouvrage','Accès au module'),(143,'241dec84-3817-4677-b4d1-460588628116','CREATE_PROJECT_OWNER','Maître d\'ouvrage','Créer un maître d\'ouvrage'),(144,'030f29d4-b561-4f86-b773-b0ad9ced5f56','READ_PROJECT_OWNERS','Maître d\'ouvrage','Voir les maîtres d\'ouvrage'),(145,'098f9612-a4f3-4e9b-bb96-74aa5aa207d3','UPDATE_PROJECT_OWNER','Maître d\'ouvrage','Modifier un maître d\'ouvrage'),(146,'1ea084d7-b3d0-4ac0-a9b1-45821da05911','DELETE_PROJECT_OWNER','Maître d\'ouvrage','Supprimer un maître d\'ouvrage'),(147,'32da10fc-766a-47f4-a661-4bada7898a69','ACCESS_DELEGATED_PROJECT_OWNERS','Maître d\'ouvrage délégué','Accès au module'),(148,'7387525a-21fe-4dfc-b636-5286c13f881d','CREATE_DELEGATED_PROJECT_OWNER','Maître d\'ouvrage délégué','Créer un maître d\'ouvrage délégué'),(149,'a3d734a0-637a-4470-80ea-f64e5f430008','READ_DELEGATED_PROJECT_OWNERS','Maître d\'ouvrage délégué','Voir les maîtres d\'ouvrage délégués'),(150,'943fa2c9-0e3b-4946-91a7-992bb15e9547','UPDATE_DELEGATED_PROJECT_OWNER','Maître d\'ouvrage délégué','Modifier un maître d\'ouvrage délégué'),(151,'c72248f9-d7de-48b5-9e9d-a54d8ec0fff9','DELETE_DELEGATED_PROJECT_OWNER','Maître d\'ouvrage délégué','Supprimer un maître d\'ouvrage délégué'),(152,'d4912439-2b44-469c-a2cb-a2cc88582bb2','ACCESS_FUNDING_SOURCES','Source de financement','Accès au module'),(153,'06ed4f7a-28e2-4d29-a5eb-715417ac83a6','CREATE_FUNDING_SOURCE','Source de financement','Créer une source'),(154,'202da6d8-51be-4a33-8a9f-ec6c6b622289','READ_FUNDING_SOURCES','Source de financement','Voir les sources'),(155,'c68bc1a7-74a3-4fa9-a15f-0066d0ebe131','UPDATE_FUNDING_SOURCE','Source de financement','Modifier une source'),(156,'d0a7faad-7a15-4d65-a4fe-95f5d63e4249','DELETE_FUNDING_SOURCE','Source de financement','Supprimer une source'),(157,'6947bb98-42ea-48d9-a5c4-8c1df189e6a4','ACCESS_REGIONS','Région','Accès au module'),(158,'72a9698a-e14b-4fdb-aba6-3e2c9d9ed61d','CREATE_REGION','Région','Créer une région'),(159,'6e3d6e37-724b-4924-8dd1-77df9b5e5fda','READ_REGIONS','Région','Voir les régions'),(160,'e714e418-d8db-47f3-846c-1e9668fdc67e','UPDATE_REGION','Région','Modifier une région'),(161,'bdc0ac94-ba31-41a0-b3a2-be99a261bca6','DELETE_REGION','Région','Supprimer une région'),(162,'9818c885-a128-4327-94ac-ac47310cc699','ACCESS_DEPARTMENTS','Département','Accès au module'),(163,'278c2631-bcbf-415f-8168-0d7f3d78c183','CREATE_DEPARTMENT','Département','Créer un département'),(164,'94540690-e7f3-4f13-96c7-7b4aa1064407','READ_DEPARTMENTS','Département','Voir les départements'),(165,'b335e1b1-5e07-4e34-bec8-f6c4f96593c6','UPDATE_DEPARTMENT','Département','Modifier un département'),(166,'e3c65cb7-99cc-42d7-b502-1ef2b9553b22','DELETE_DEPARTMENT','Département','Supprimer un département'),(167,'e7a26ed4-b70e-4a16-81de-e883324e161b','ACCESS_MUNICIPALITIES','Commune','Accès au module'),(168,'2fb5ae81-71b9-4514-b71b-62f193694fd2','CREATE_MUNICIPALITY','Commune','Créer une commune'),(169,'a872c3fd-a303-4a81-b083-6841d301f785','READ_MUNICIPALITIES','Commune','Voir les communes'),(170,'09a084df-3748-435f-9dcd-dbec773fecb1','UPDATE_MUNICIPALITY','Commune','Modifier une commune'),(171,'7a474c7b-d9ba-4c33-8919-23fbdb000273','DELETE_MUNICIPALITY','Commune','Supprimer une commune'),(172,'403ff7d8-44f8-424b-bfc2-5282e5d85195','ACCESS_BENEFICIARIES','Bénéficiaire','Accès au module'),(173,'88713a5c-0c7c-4951-b728-c6431afab733','CREATE_BENEFICIARY','Bénéficiaire','Créer un bénéficiaire'),(174,'dceb19f9-5627-4f20-929d-34ba299e948d','READ_BENEFICIARIES','Bénéficiaire','Voir les bénéficiaires'),(175,'1c3f35a1-7dd8-490a-aaa1-c28df9474d59','UPDATE_BENEFICIARY','Bénéficiaire','Modifier un bénéficiaire'),(176,'a98d7405-c2f3-4096-94e8-284df30ff663','DELETE_BENEFICIARY','Bénéficiaire','Supprimer un bénéficiaire'),(177,'9db9c64f-d985-43db-b829-0fc7cc3c0ec1','ACCESS_PAYMENT_MODES','Mode de paiement','Accès au module'),(178,'7a174458-90cf-47e2-9b41-888078de1a09','CREATE_PAYMENT_MODE','Mode de paiement','Créer un mode de paiement'),(179,'ab061c6a-1a66-495d-9209-b8434d32c41c','READ_PAYMENT_MODES','Mode de paiement','Voir les modes de paiement'),(180,'a26225b8-68b9-4f26-b5a5-e2602afa2352','UPDATE_PAYMENT_MODE','Mode de paiement','Modifier un mode de paiement'),(181,'74d1b64e-4cf1-4dd0-bae3-06da2d90cedc','DELETE_PAYMENT_MODE','Mode de paiement','Supprimer un mode de paiement'),(182,'8792cf93-1be8-4e03-82f7-3ec7f292fe88','ACCESS_BUDGET_TYPES','Type de budgets','Accès au module'),(183,'19950c22-67f7-47c8-952a-31f89bf454c3','CREATE_BUDGET_TYPE','Type de budgets','Créer un type de budget'),(184,'b79f0dbb-1d5b-49fd-a4d6-62eb455233ba','READ_BUDGET_TYPES','Type de budgets','Voir les types de budgets'),(185,'54e40fef-6e09-4360-9a9a-bda021155c0f','UPDATE_BUDGET_TYPE','Type de budgets','Modifier un type de budget'),(186,'307616f0-3e0a-4f21-9aa1-e6e4e959455e','DELETE_BUDGET_TYPE','Type de budgets','Supprimer un type de budget'),(187,'8c69034f-839a-42f4-a438-4d576c331c48','ACCESS_EXPENSE_TYPES','Type de dépenses','Accès au module'),(188,'0b8350e0-06be-4205-bd4f-673394fe3f2c','CREATE_EXPENSE_TYPE','Type de dépenses','Créer un type de dépense'),(189,'a77d82e3-07a7-4868-8165-b66d41bd4011','READ_EXPENSE_TYPES','Type de dépenses','Voir les types de dépenses'),(190,'b60e0c7a-0d91-46f4-8adf-c7548317459c','UPDATE_EXPENSE_TYPE','Type de dépenses','Modifier un type de dépense'),(191,'8499b1f7-10db-40d8-a064-85212f668990','DELETE_EXPENSE_TYPE','Type de dépenses','Supprimer un type de dépense'),(192,'dc0c8b99-89a2-4981-987d-6dcf82c66967','ACCESS_INDICATOR_CATEGORIES','Catégorie d\'indicateur','Accès au module'),(193,'34d46d63-84df-4108-b92c-e79db82dd3a1','CREATE_INDICATOR_CATEGORY','Catégorie d\'indicateur','Créer une catégorie'),(194,'e8f60ff6-57ae-482b-a9ca-0f5e295b6988','READ_INDICATOR_CATEGORIES','Catégorie d\'indicateur','Voir les catégories'),(195,'946be5c2-3ed7-48da-9d1b-cc828066dd6a','UPDATE_INDICATOR_CATEGORY','Catégorie d\'indicateur','Modifier une catégorie'),(196,'606d803f-09c7-4561-a9de-8e7c1138f066','DELETE_INDICATOR_CATEGORY','Catégorie d\'indicateur','Supprimer une catégorie'),(197,'3c8033fe-8d51-4e9c-8a4a-6558ecebcd84','ACCESS_STAKEHOLDERS','Parties prenantes','Accès au module'),(198,'004f6736-df54-4f4d-af7d-620cb0eb7f10','CREATE_STAKEHOLDER','Parties prenantes','Créer une partie prenante'),(199,'c87ebab8-3f2c-4d74-8741-117862fc73c7','READ_STAKEHOLDERS','Parties prenantes','Voir les parties prenantes'),(200,'b60db582-0c5a-4a42-bb4f-8390de188de5','UPDATE_STAKEHOLDER','Parties prenantes','Modifier une partie prenante'),(201,'246e44b9-bb48-4d5c-8655-f024c5a52fc2','DELETE_STAKEHOLDER','Parties prenantes','Supprimer une partie prenante'),(202,'5e67e3cd-1167-46ef-859c-612ddf7a86ab','ACCESS_USERS','Utilisateur','Accès au module'),(203,'b75398af-a36d-4182-a1da-a9901b9c6dfd','CREATE_USER','Utilisateur','Créer un utilisateur'),(204,'998e0c96-5ab6-4470-a2f4-d1e2b12ca159','READ_USERS','Utilisateur','Voir les utilisateurs'),(205,'d9df9537-9d76-43e7-b80a-a846fdbd401d','UPDATE_USER','Utilisateur','Modifier un utilisateur'),(206,'6a956aaa-1bc7-48f8-85ac-c6247b60d768','DELETE_USER','Utilisateur','Supprimer un utilisateur'),(207,'86e66bc0-73e3-49a3-9d6b-76178c1375ba','ACCESS_ROLES','Rôle et permission','Accès au module'),(208,'594cc15d-70ec-4e51-a22d-937facb107d2','CREATE_ROLE','Rôle et permission','Créer un rôle'),(209,'22af1725-6e94-4b93-bc06-e6f5a9bc1ae1','READ_ROLES','Rôle et permission','Voir les rôles'),(210,'ba598b3f-7ead-4f14-ab5d-9e41bdb0c6b8','UPDATE_ROLE','Rôle et permission','Modifier un rôle'),(211,'e239eefc-fc5a-41d5-8027-b8a1c38ae759','DELETE_ROLE','Rôle et permission','Supprimer un rôle');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
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
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `procurement_modes`
--

DROP TABLE IF EXISTS `procurement_modes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `procurement_modes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `procurement_modes_uuid_unique` (`uuid`),
  UNIQUE KEY `procurement_modes_name_unique` (`name`),
  KEY `procurement_modes_created_by_foreign` (`created_by`),
  KEY `procurement_modes_updated_by_foreign` (`updated_by`),
  CONSTRAINT `procurement_modes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `procurement_modes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `procurement_modes`
--

LOCK TABLES `procurement_modes` WRITE;
/*!40000 ALTER TABLE `procurement_modes` DISABLE KEYS */;
INSERT INTO `procurement_modes` VALUES (1,'3683f810-2288-4828-85d1-440fd994df33','Appel d\'offres ouvert',5,1,NULL,NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(2,'151338ba-ba2a-427f-b256-4ff3bb5456bc','Appel d\'offres restreint',15,1,NULL,NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47'),(3,'16073b56-3f42-448e-8346-2675b84a2c81','Entente directe',20,0,NULL,NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47');
/*!40000 ALTER TABLE `procurement_modes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_beneficiaries`
--

DROP TABLE IF EXISTS `program_beneficiaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `program_beneficiaries` (
  `action_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`action_domain_uuid`,`beneficiary_uuid`),
  KEY `program_beneficiaries_beneficiary_uuid_foreign` (`beneficiary_uuid`),
  CONSTRAINT `program_beneficiaries_action_domain_uuid_foreign` FOREIGN KEY (`action_domain_uuid`) REFERENCES `action_domains` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `program_beneficiaries_beneficiary_uuid_foreign` FOREIGN KEY (`beneficiary_uuid`) REFERENCES `beneficiaries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_beneficiaries`
--

LOCK TABLES `program_beneficiaries` WRITE;
/*!40000 ALTER TABLE `program_beneficiaries` DISABLE KEYS */;
/*!40000 ALTER TABLE `program_beneficiaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_funding_sources`
--

DROP TABLE IF EXISTS `program_funding_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `program_funding_sources` (
  `action_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funding_source_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planned_budget` decimal(14,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`action_domain_uuid`,`funding_source_uuid`),
  KEY `program_funding_sources_funding_source_uuid_foreign` (`funding_source_uuid`),
  CONSTRAINT `program_funding_sources_action_domain_uuid_foreign` FOREIGN KEY (`action_domain_uuid`) REFERENCES `action_domains` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `program_funding_sources_funding_source_uuid_foreign` FOREIGN KEY (`funding_source_uuid`) REFERENCES `funding_sources` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_funding_sources`
--

LOCK TABLES `program_funding_sources` WRITE;
/*!40000 ALTER TABLE `program_funding_sources` DISABLE KEYS */;
INSERT INTO `program_funding_sources` VALUES ('62e1ae2b-f482-4788-9f2a-a4ba99bef1df','f097b72c-2932-4e8a-97de-e8002fe9a2b2',87485.00),('62e7e327-083a-49e8-b40e-2fccb49d6a6c','5f97d913-4aeb-4c13-8807-f35f5e0b547c',219300.00),('62e7e327-083a-49e8-b40e-2fccb49d6a6c','9655a402-f483-4504-bc37-762c19349d94',96604.00),('62e7e327-083a-49e8-b40e-2fccb49d6a6c','e38bb49c-124b-48fb-a30c-f4d7ec7e4c4f',91462.00),('efec6c04-d518-4d8f-a6b7-4ed7c2dc2db9','438af312-de77-4dab-bea3-74f76fb45c12',110368.00),('efec6c04-d518-4d8f-a6b7-4ed7c2dc2db9','5f97d913-4aeb-4c13-8807-f35f5e0b547c',67632.00);
/*!40000 ALTER TABLE `program_funding_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_beneficiaries`
--

DROP TABLE IF EXISTS `project_beneficiaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_beneficiaries` (
  `strategic_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`strategic_domain_uuid`,`beneficiary_uuid`),
  KEY `project_beneficiaries_beneficiary_uuid_foreign` (`beneficiary_uuid`),
  CONSTRAINT `project_beneficiaries_beneficiary_uuid_foreign` FOREIGN KEY (`beneficiary_uuid`) REFERENCES `beneficiaries` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `project_beneficiaries_strategic_domain_uuid_foreign` FOREIGN KEY (`strategic_domain_uuid`) REFERENCES `strategic_domains` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_beneficiaries`
--

LOCK TABLES `project_beneficiaries` WRITE;
/*!40000 ALTER TABLE `project_beneficiaries` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_beneficiaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_funding_sources`
--

DROP TABLE IF EXISTS `project_funding_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_funding_sources` (
  `strategic_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funding_source_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planned_budget` decimal(14,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`strategic_domain_uuid`,`funding_source_uuid`),
  KEY `project_funding_sources_funding_source_uuid_foreign` (`funding_source_uuid`),
  CONSTRAINT `project_funding_sources_funding_source_uuid_foreign` FOREIGN KEY (`funding_source_uuid`) REFERENCES `funding_sources` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `project_funding_sources_strategic_domain_uuid_foreign` FOREIGN KEY (`strategic_domain_uuid`) REFERENCES `strategic_domains` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_funding_sources`
--

LOCK TABLES `project_funding_sources` WRITE;
/*!40000 ALTER TABLE `project_funding_sources` DISABLE KEYS */;
INSERT INTO `project_funding_sources` VALUES ('3a2012e5-0cc3-4907-9615-5151d36c8023','cdec5c14-26e2-48bb-83d0-994205a4615c',94042.00),('3a2012e5-0cc3-4907-9615-5151d36c8023','e38bb49c-124b-48fb-a30c-f4d7ec7e4c4f',78089.00),('3a2012e5-0cc3-4907-9615-5151d36c8023','f097b72c-2932-4e8a-97de-e8002fe9a2b2',219815.00),('48b76f89-38ad-43d6-b7e2-7fbdb6fa074e','f097b72c-2932-4e8a-97de-e8002fe9a2b2',94201.00),('dc5dbf4b-9a50-4752-b4f1-dd9407133087','9655a402-f483-4504-bc37-762c19349d94',67817.00),('dc5dbf4b-9a50-4752-b4f1-dd9407133087','cdec5c14-26e2-48bb-83d0-994205a4615c',159443.00);
/*!40000 ALTER TABLE `project_funding_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_owners`
--

DROP TABLE IF EXISTS `project_owners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_owners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `structure_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_owners_uuid_unique` (`uuid`),
  UNIQUE KEY `project_owners_name_unique` (`name`),
  UNIQUE KEY `project_owners_email_unique` (`email`),
  UNIQUE KEY `project_owners_phone_unique` (`phone`),
  KEY `project_owners_structure_uuid_foreign` (`structure_uuid`),
  KEY `project_owners_created_by_foreign` (`created_by`),
  KEY `project_owners_updated_by_foreign` (`updated_by`),
  CONSTRAINT `project_owners_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `project_owners_structure_uuid_foreign` FOREIGN KEY (`structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `project_owners_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_owners`
--

LOCK TABLES `project_owners` WRITE;
/*!40000 ALTER TABLE `project_owners` DISABLE KEYS */;
INSERT INTO `project_owners` VALUES (1,'cf577d7f-f6fc-440c-981e-87d9494e46dc','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','Ministère des Travaux Publics','Public','mtp@example.gov','+22212345678',1,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(2,'127ef65d-ea97-40e8-8c62-a5223f75e2ae','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','Entreprise BTP Sahel','Privé','contact@btpsahel.com','+22298765432',1,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48'),(3,'baab32d2-f8d4-47a0-8edb-26e676214335','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','ONG Développement Rural','ONG','info@ongdr.org','+22233445566',0,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48');
/*!40000 ALTER TABLE `project_owners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regions`
--

DROP TABLE IF EXISTS `regions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `regions_uuid_unique` (`uuid`),
  UNIQUE KEY `regions_name_unique` (`name`),
  KEY `regions_created_by_foreign` (`created_by`),
  KEY `regions_updated_by_foreign` (`updated_by`),
  CONSTRAINT `regions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `regions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regions`
--

LOCK TABLES `regions` WRITE;
/*!40000 ALTER TABLE `regions` DISABLE KEYS */;
INSERT INTO `regions` VALUES (1,'742b399a-5971-447e-86e4-0e648eb61d36','Williamsonmouth',-1.536991,-143.734406,1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(2,'2606b718-6f19-4c82-a9cd-43c453f1c095','Schillerfurt',-68.519523,47.308025,1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(3,'71c8fde7-26fd-4534-bda9-f5d66dc8f176','East Madelynn',41.629353,-65.746896,1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(4,'febe6726-ecfa-4c53-b338-400e639559aa','Lake Loraberg',37.881108,-10.920095,1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL),(5,'6e30fd97-9b0c-425b-be85-6b8d2ba7b9e0','Amariburgh',-56.271733,-153.256731,1,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL);
/*!40000 ALTER TABLE `regions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1),(2,1),(1,2),(2,2),(1,3),(2,3),(1,4),(2,4),(1,5),(2,5),(1,6),(2,6),(1,7),(2,7),(1,8),(2,8),(1,9),(2,9),(1,10),(2,10),(1,11),(2,11),(1,12),(2,12),(1,13),(2,13),(1,14),(2,14),(1,15),(2,15),(1,16),(2,16),(1,17),(2,17),(1,18),(2,18),(1,19),(2,19),(1,20),(2,20),(1,21),(2,21),(1,22),(2,22),(1,23),(2,23),(1,24),(2,24),(1,25),(2,25),(1,26),(2,26),(1,27),(2,27),(1,28),(2,28),(1,29),(2,29),(1,30),(2,30),(1,31),(2,31),(1,32),(2,32),(1,33),(2,33),(1,34),(2,34),(1,35),(2,35),(1,36),(2,36),(1,37),(2,37),(1,38),(2,38),(1,39),(2,39),(1,40),(2,40),(1,41),(2,41),(1,42),(2,42),(1,43),(2,43),(1,44),(2,44),(1,45),(2,45),(1,46),(2,46),(1,47),(2,47),(1,48),(2,48),(1,49),(2,49),(1,50),(2,50),(1,51),(2,51),(1,52),(2,52),(1,53),(2,53),(1,54),(2,54),(1,55),(2,55),(1,56),(2,56),(1,57),(2,57),(1,58),(2,58),(1,59),(2,59),(1,60),(2,60),(1,61),(2,61),(1,62),(2,62),(1,63),(2,63),(1,64),(2,64),(1,65),(2,65),(1,66),(2,66),(1,67),(2,67),(1,68),(2,68),(1,69),(2,69),(1,70),(2,70),(1,71),(2,71),(1,72),(2,72),(1,73),(2,73),(1,74),(2,74),(1,75),(2,75),(1,76),(2,76),(1,77),(2,77),(1,78),(2,78),(1,79),(2,79),(1,80),(2,80),(1,81),(2,81),(1,82),(2,82),(1,83),(2,83),(1,84),(2,84),(1,85),(2,85),(1,86),(2,86),(1,87),(2,87),(1,88),(2,88),(1,89),(2,89),(1,90),(2,90),(1,91),(2,91),(1,92),(2,92),(1,93),(2,93),(1,94),(2,94),(1,95),(2,95),(1,96),(2,96),(1,97),(2,97),(1,98),(2,98),(1,99),(2,99),(1,100),(2,100),(1,101),(2,101),(1,102),(2,102),(1,103),(2,103),(1,104),(2,104),(1,105),(2,105),(1,106),(2,106),(1,107),(2,107),(1,108),(2,108),(1,109),(2,109),(1,110),(2,110),(1,111),(2,111),(1,112),(2,112),(1,113),(2,113),(1,114),(2,114),(1,115),(2,115),(1,116),(2,116),(1,117),(2,117),(1,118),(2,118),(1,119),(2,119),(1,120),(2,120),(1,121),(2,121),(1,122),(2,122),(1,123),(2,123),(1,124),(2,124),(1,125),(2,125),(1,126),(2,126),(1,127),(2,127),(1,128),(2,128),(1,129),(2,129),(1,130),(2,130),(1,131),(2,131),(1,132),(2,132),(1,133),(2,133),(1,134),(2,134),(1,135),(2,135),(1,136),(2,136),(1,137),(2,137),(1,138),(2,138),(1,139),(2,139),(1,140),(2,140),(1,141),(2,141),(1,142),(2,142),(1,143),(2,143),(1,144),(2,144),(1,145),(2,145),(1,146),(2,146),(1,147),(2,147),(1,148),(2,148),(1,149),(2,149),(1,150),(2,150),(1,151),(2,151),(1,152),(2,152),(1,153),(2,153),(1,154),(2,154),(1,155),(2,155),(1,156),(2,156),(1,157),(2,157),(1,158),(2,158),(1,159),(2,159),(1,160),(2,160),(1,161),(2,161),(1,162),(2,162),(1,163),(2,163),(1,164),(2,164),(1,165),(2,165),(1,166),(2,166),(1,167),(2,167),(1,168),(2,168),(1,169),(2,169),(1,170),(2,170),(1,171),(2,171),(1,172),(2,172),(1,173),(2,173),(1,174),(2,174),(1,175),(2,175),(1,176),(2,176),(1,177),(2,177),(1,178),(2,178),(1,179),(2,179),(1,180),(2,180),(1,181),(2,181),(1,182),(2,182),(1,183),(2,183),(1,184),(2,184),(1,185),(2,185),(1,186),(2,186),(1,187),(2,187),(1,188),(2,188),(1,189),(2,189),(1,190),(2,190),(1,191),(2,191),(1,192),(2,192),(1,193),(2,193),(1,194),(2,194),(1,195),(2,195),(1,196),(2,196),(1,197),(2,197),(1,198),(2,198),(1,199),(2,199),(1,200),(2,200),(1,201),(2,201),(1,202),(2,202),(1,203),(2,203),(1,204),(2,204),(1,205),(2,205),(1,206),(2,206),(1,207),(2,207),(1,208),(2,208),(1,209),(2,209),(1,210),(2,210),(1,211),(2,211);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_uuid_unique` (`uuid`),
  UNIQUE KEY `roles_name_unique` (`name`),
  KEY `roles_created_by_foreign` (`created_by`),
  KEY `roles_updated_by_foreign` (`updated_by`),
  CONSTRAINT `roles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `roles_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'436fdc3a-f21f-461b-952e-e019b9efb9c4','Administrateur',NULL,NULL,'2026-05-18 11:35:30','2026-05-18 11:35:30'),(2,'01be557a-a1a6-4130-9c67-3137dd28ea31','Utilisateur simple',NULL,NULL,'2026-05-18 11:35:30','2026-05-18 11:35:30');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stakeholders`
--

DROP TABLE IF EXISTS `stakeholders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stakeholders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stakeholders_uuid_unique` (`uuid`),
  UNIQUE KEY `stakeholders_name_unique` (`name`),
  KEY `stakeholders_created_by_foreign` (`created_by`),
  KEY `stakeholders_updated_by_foreign` (`updated_by`),
  CONSTRAINT `stakeholders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `stakeholders_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stakeholders`
--

LOCK TABLES `stakeholders` WRITE;
/*!40000 ALTER TABLE `stakeholders` DISABLE KEYS */;
INSERT INTO `stakeholders` VALUES (1,'5c4cfa52-8197-4ce7-be2e-f131571f672b','Kendra O\'Conner','wilma55@lockman.org','(973) 608-8760',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(2,'b3b79390-12ac-4f90-adf4-0cd620ffe2bd','Daphney Smitham','mylene.mraz@hahn.com','910.785.0820',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(3,'6e548fc6-2367-404a-9ab1-7f959820d703','Jerald Goyette','nico51@pfeffer.info','865-555-9259',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(4,'ad0f024b-23a2-4b9a-a9cb-034c1a5b7049','Leanna Eichmann','beichmann@yahoo.com','1-539-573-9941',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL),(5,'5483ca6b-f40f-49d4-a8c7-1067f6f90c43','Dr. Ebba Huel','edwin.okon@gmail.com','224-989-5543',1,'2026-05-18 11:35:48','2026-05-18 11:35:48',NULL,NULL);
/*!40000 ALTER TABLE `stakeholders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `strategic_domain_states`
--

DROP TABLE IF EXISTS `strategic_domain_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `strategic_domain_states` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_domain_id` int NOT NULL,
  `state_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_date` timestamp NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `strategic_domain_states_uuid_unique` (`uuid`),
  KEY `strategic_domain_states_created_by_foreign` (`created_by`),
  KEY `strategic_domain_states_updated_by_foreign` (`updated_by`),
  KEY `strategic_domain_states_strategic_domain_uuid_index` (`strategic_domain_uuid`),
  KEY `strategic_domain_states_strategic_domain_id_index` (`strategic_domain_id`),
  CONSTRAINT `strategic_domain_states_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_domain_states_strategic_domain_uuid_foreign` FOREIGN KEY (`strategic_domain_uuid`) REFERENCES `strategic_domains` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `strategic_domain_states_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `strategic_domain_states`
--

LOCK TABLES `strategic_domain_states` WRITE;
/*!40000 ALTER TABLE `strategic_domain_states` DISABLE KEYS */;
/*!40000 ALTER TABLE `strategic_domain_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `strategic_domain_statuses`
--

DROP TABLE IF EXISTS `strategic_domain_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `strategic_domain_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_domain_id` int NOT NULL,
  `status_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_date` timestamp NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `strategic_domain_statuses_uuid_unique` (`uuid`),
  KEY `strategic_domain_statuses_created_by_foreign` (`created_by`),
  KEY `strategic_domain_statuses_updated_by_foreign` (`updated_by`),
  KEY `strategic_domain_statuses_strategic_domain_uuid_index` (`strategic_domain_uuid`),
  KEY `strategic_domain_statuses_strategic_domain_id_index` (`strategic_domain_id`),
  CONSTRAINT `strategic_domain_statuses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_domain_statuses_strategic_domain_uuid_foreign` FOREIGN KEY (`strategic_domain_uuid`) REFERENCES `strategic_domains` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `strategic_domain_statuses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `strategic_domain_statuses`
--

LOCK TABLES `strategic_domain_statuses` WRITE;
/*!40000 ALTER TABLE `strategic_domain_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `strategic_domain_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `strategic_domains`
--

DROP TABLE IF EXISTS `strategic_domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `strategic_domains` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(14,2) NOT NULL DEFAULT '0.00',
  `action_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MRU',
  `responsible_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'preparation',
  `status_changed_at` timestamp NULL DEFAULT NULL,
  `status_changed_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `state_changed_at` timestamp NULL DEFAULT NULL,
  `state_changed_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `prerequisites` text COLLATE utf8mb4_unicode_ci,
  `impacts` text COLLATE utf8mb4_unicode_ci,
  `risks` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `strategic_domains_uuid_unique` (`uuid`),
  UNIQUE KEY `strategic_domains_name_unique` (`name`),
  UNIQUE KEY `strategic_domains_reference_unique` (`reference`),
  KEY `strategic_domains_action_domain_uuid_foreign` (`action_domain_uuid`),
  KEY `strategic_domains_responsible_uuid_foreign` (`responsible_uuid`),
  KEY `strategic_domains_created_by_foreign` (`created_by`),
  KEY `strategic_domains_updated_by_foreign` (`updated_by`),
  CONSTRAINT `strategic_domains_action_domain_uuid_foreign` FOREIGN KEY (`action_domain_uuid`) REFERENCES `action_domains` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `strategic_domains_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_domains_responsible_uuid_foreign` FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_domains_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `strategic_domains`
--

LOCK TABLES `strategic_domains` WRITE;
/*!40000 ALTER TABLE `strategic_domains` DISABLE KEYS */;
INSERT INTO `strategic_domains` VALUES (1,'48b76f89-38ad-43d6-b7e2-7fbdb6fa074e','PROJ-00A','Project A','2025-01-01','2025-12-31',94201.00,'efec6c04-d518-4d8f-a6b7-4ed7c2dc2db9','MRU','a3011198-148b-4119-b64c-c415b7cd925e',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48','preparation',NULL,NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL),(2,'dc5dbf4b-9a50-4752-b4f1-dd9407133087','PROJ-00B','Project B','2025-02-01','2025-11-30',227260.00,'62e1ae2b-f482-4788-9f2a-a4ba99bef1df','MRU','b9d5a85d-cd9d-47e6-ba1e-f5be6f11bc7f',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48','preparation',NULL,NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL),(3,'3a2012e5-0cc3-4907-9615-5151d36c8023','PROJ-00C','Project C','2025-03-01','2025-09-30',391946.00,'62e7e327-083a-49e8-b40e-2fccb49d6a6c','MRU','32233eba-1f16-42c5-b1f4-138ed81e30c7',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:48','preparation',NULL,NULL,'none',NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `strategic_domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `strategic_elements`
--

DROP TABLE IF EXISTS `strategic_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `strategic_elements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `structure_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_map_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_structure_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_map_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_element_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('AXIS','LEVER') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AXIS',
  `order` int unsigned NOT NULL DEFAULT '0',
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `strategic_elements_strategic_map_uuid_name_type_unique` (`strategic_map_uuid`,`name`,`type`),
  UNIQUE KEY `strategic_elements_strategic_map_uuid_abbreviation_type_unique` (`strategic_map_uuid`,`abbreviation`,`type`),
  UNIQUE KEY `strategic_elements_uuid_unique` (`uuid`),
  UNIQUE KEY `strategic_elements_reference_unique` (`reference`),
  KEY `strategic_elements_parent_structure_uuid_foreign` (`parent_structure_uuid`),
  KEY `strategic_elements_parent_map_uuid_foreign` (`parent_map_uuid`),
  KEY `strategic_elements_structure_uuid_foreign` (`structure_uuid`),
  KEY `strategic_elements_created_by_foreign` (`created_by`),
  KEY `strategic_elements_updated_by_foreign` (`updated_by`),
  KEY `strategic_elements_parent_element_uuid_foreign` (`parent_element_uuid`),
  CONSTRAINT `strategic_elements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_elements_parent_element_uuid_foreign` FOREIGN KEY (`parent_element_uuid`) REFERENCES `strategic_elements` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_elements_parent_map_uuid_foreign` FOREIGN KEY (`parent_map_uuid`) REFERENCES `strategic_maps` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_elements_parent_structure_uuid_foreign` FOREIGN KEY (`parent_structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_elements_strategic_map_uuid_foreign` FOREIGN KEY (`strategic_map_uuid`) REFERENCES `strategic_maps` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `strategic_elements_structure_uuid_foreign` FOREIGN KEY (`structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `strategic_elements_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `strategic_elements`
--

LOCK TABLES `strategic_elements` WRITE;
/*!40000 ALTER TABLE `strategic_elements` DISABLE KEYS */;
INSERT INTO `strategic_elements` VALUES (1,'6795c5f5-0a82-401c-8371-2e58c85745ad',NULL,'f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','1d05e790-8857-4781-88e7-6ef9b22aeb1a',NULL,NULL,NULL,'LEVER',1,'Levier de gouvernance et innovation publique','LEV-GOV','Piloter la transformation institutionnelle et la performance publique.',1,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(2,'9bfdad71-9202-467f-8b9b-52d01d3946a6',NULL,'f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','1d05e790-8857-4781-88e7-6ef9b22aeb1a',NULL,NULL,NULL,'LEVER',2,'Levier de durabilité et environnement','LEV-DUR','Promouvoir la durabilité écologique et la responsabilité sociétale.',1,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(3,'b6051c57-1bc1-414b-86e1-43a74c683634',NULL,'f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','1d05e790-8857-4781-88e7-6ef9b22aeb1a',NULL,NULL,NULL,'LEVER',3,'Levier de développement économique','LEV-ECO','Stimuler la croissance, la compétitivité et l\'innovation économique.',1,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(4,'b7508403-8736-4371-b733-fa3c5d245b48',NULL,'f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','1d05e790-8857-4781-88e7-6ef9b22aeb1a',NULL,NULL,NULL,'LEVER',4,'Levier d\'inclusion et cohésion sociale','LEV-SOC','Renforcer l\'équité, l\'inclusion et la solidarité au sein de la société.',1,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(5,'e679e743-67ea-431a-8493-665cb27cb7c9',NULL,'56a0ec48-5f29-4778-85cb-f347275fcf95','fae3b1c4-578c-474d-a03f-3953f2f091d6','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','1d05e790-8857-4781-88e7-6ef9b22aeb1a','6795c5f5-0a82-401c-8371-2e58c85745ad','AXIS',1,'Axe Innovation & Digitalisation','INNO','Encourager la digitalisation et l\'innovation publique.',1,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(6,'9fe991de-d611-4277-9746-b269022c09cc',NULL,'56a0ec48-5f29-4778-85cb-f347275fcf95','fae3b1c4-578c-474d-a03f-3953f2f091d6','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','1d05e790-8857-4781-88e7-6ef9b22aeb1a','6795c5f5-0a82-401c-8371-2e58c85745ad','AXIS',2,'Axe Gouvernance Transparente','GOV','Renforcer la transparence et la redevabilité.',1,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(7,'e4c676ad-f549-491d-81bf-9a44b419209c',NULL,'56a0ec48-5f29-4778-85cb-f347275fcf95','fae3b1c4-578c-474d-a03f-3953f2f091d6','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','1d05e790-8857-4781-88e7-6ef9b22aeb1a','9bfdad71-9202-467f-8b9b-52d01d3946a6','AXIS',3,'Axe Énergie Verte','GREEN','Promouvoir les énergies renouvelables et la sobriété énergétique.',1,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(8,'a4caafe2-b47e-4953-8c3c-876d22180437',NULL,'56a0ec48-5f29-4778-85cb-f347275fcf95','fae3b1c4-578c-474d-a03f-3953f2f091d6','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','1d05e790-8857-4781-88e7-6ef9b22aeb1a','9bfdad71-9202-467f-8b9b-52d01d3946a6','AXIS',4,'Axe Gestion des Ressources','RES','Optimiser l\'utilisation des ressources naturelles.',1,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(9,'d246a3b7-5673-4f1a-af4d-9eacb0b452dd',NULL,'56a0ec48-5f29-4778-85cb-f347275fcf95','fae3b1c4-578c-474d-a03f-3953f2f091d6','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','1d05e790-8857-4781-88e7-6ef9b22aeb1a','b6051c57-1bc1-414b-86e1-43a74c683634','AXIS',5,'Axe Innovation Économique','ECO-INN','Soutenir les startups et les initiatives entrepreneuriales.',1,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(10,'716e17d9-a8d6-49b7-b9ec-b81244b900f9',NULL,'56a0ec48-5f29-4778-85cb-f347275fcf95','fae3b1c4-578c-474d-a03f-3953f2f091d6','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','1d05e790-8857-4781-88e7-6ef9b22aeb1a','b6051c57-1bc1-414b-86e1-43a74c683634','AXIS',6,'Axe Industrie & Compétitivité','IND','Renforcer le tissu industriel et l\'emploi local.',1,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(11,'3575ef82-7f5a-42e3-acc5-63cc6a9deb3c',NULL,'56a0ec48-5f29-4778-85cb-f347275fcf95','fae3b1c4-578c-474d-a03f-3953f2f091d6','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','1d05e790-8857-4781-88e7-6ef9b22aeb1a','b7508403-8736-4371-b733-fa3c5d245b48','AXIS',7,'Axe Éducation et Compétences','EDU','Améliorer l\'accès et la qualité de l\'éducation.',1,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(12,'840a6f58-16e2-4a31-89df-2532f405a8e6',NULL,'56a0ec48-5f29-4778-85cb-f347275fcf95','fae3b1c4-578c-474d-a03f-3953f2f091d6','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','1d05e790-8857-4781-88e7-6ef9b22aeb1a','b7508403-8736-4371-b733-fa3c5d245b48','AXIS',8,'Axe Santé & Bien-être','SAN','Renforcer les infrastructures sanitaires et sociales.',1,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49');
/*!40000 ALTER TABLE `strategic_elements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `strategic_maps`
--

DROP TABLE IF EXISTS `strategic_maps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `strategic_maps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `structure_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `strategic_maps_uuid_unique` (`uuid`),
  KEY `strategic_maps_structure_uuid_foreign` (`structure_uuid`),
  KEY `strategic_maps_created_by_foreign` (`created_by`),
  KEY `strategic_maps_updated_by_foreign` (`updated_by`),
  CONSTRAINT `strategic_maps_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_maps_structure_uuid_foreign` FOREIGN KEY (`structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `strategic_maps_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `strategic_maps`
--

LOCK TABLES `strategic_maps` WRITE;
/*!40000 ALTER TABLE `strategic_maps` DISABLE KEYS */;
INSERT INTO `strategic_maps` VALUES (1,'1d05e790-8857-4781-88e7-6ef9b22aeb1a','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','Carte stratégique nationale 2025','Feuille de route de développement à l\'échelle de l\'État.','2025-01-01','2025-12-31',1,'2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL),(2,'fae3b1c4-578c-474d-a03f-3953f2f091d6','56a0ec48-5f29-4778-85cb-f347275fcf95','Carte stratégique de développement 2025','Feuille de route pour le développement global de la structure.','2025-01-01','2025-12-31',1,'2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL),(3,'9e6de6d6-b940-4e21-ab55-fa35c82eda8b','56a0ec48-5f29-4778-85cb-f347275fcf95','Carte stratégique digitale','Plan d\'action pour la digitalisation et l\'innovation technologique.','2025-03-01','2026-02-28',0,'2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL),(4,'055179dc-2035-46b7-82cc-71dc56edf8fb','56a0ec48-5f29-4778-85cb-f347275fcf95','Carte stratégique de performance','Suivi et évaluation des objectifs de performance annuelle.','2025-04-01','2026-03-31',0,'2026-05-18 11:35:49','2026-05-18 11:35:49',NULL,NULL);
/*!40000 ALTER TABLE `strategic_maps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `strategic_objective_statuses`
--

DROP TABLE IF EXISTS `strategic_objective_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `strategic_objective_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_objective_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_objective_id` bigint NOT NULL,
  `status_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_date` timestamp NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `strategic_objective_statuses_uuid_unique` (`uuid`),
  KEY `strategic_objective_statuses_created_by_foreign` (`created_by`),
  KEY `strategic_objective_statuses_updated_by_foreign` (`updated_by`),
  KEY `strategic_objective_statuses_strategic_objective_uuid_index` (`strategic_objective_uuid`),
  KEY `strategic_objective_statuses_strategic_objective_id_index` (`strategic_objective_id`),
  CONSTRAINT `strategic_objective_statuses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_objective_statuses_strategic_objective_uuid_foreign` FOREIGN KEY (`strategic_objective_uuid`) REFERENCES `strategic_objectives` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `strategic_objective_statuses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `strategic_objective_statuses`
--

LOCK TABLES `strategic_objective_statuses` WRITE;
/*!40000 ALTER TABLE `strategic_objective_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `strategic_objective_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `strategic_objectives`
--

DROP TABLE IF EXISTS `strategic_objectives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `strategic_objectives` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `structure_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_map_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_element_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lead_structure_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matrix_period_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `priority` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `risk_level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'declared',
  `state` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `status_changed_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_changed_at` datetime DEFAULT NULL,
  `failed` tinyint(1) NOT NULL DEFAULT '1',
  `alert` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `strategic_objectives_strategic_element_uuid_name_unique` (`strategic_element_uuid`,`name`),
  UNIQUE KEY `strategic_objectives_uuid_unique` (`uuid`),
  UNIQUE KEY `strategic_objectives_reference_unique` (`reference`),
  KEY `strategic_objectives_structure_uuid_foreign` (`structure_uuid`),
  KEY `strategic_objectives_strategic_map_uuid_foreign` (`strategic_map_uuid`),
  KEY `strategic_objectives_lead_structure_uuid_foreign` (`lead_structure_uuid`),
  KEY `strategic_objectives_matrix_period_uuid_foreign` (`matrix_period_uuid`),
  KEY `strategic_objectives_status_changed_by_foreign` (`status_changed_by`),
  KEY `strategic_objectives_created_by_foreign` (`created_by`),
  KEY `strategic_objectives_updated_by_foreign` (`updated_by`),
  CONSTRAINT `strategic_objectives_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_objectives_lead_structure_uuid_foreign` FOREIGN KEY (`lead_structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `strategic_objectives_matrix_period_uuid_foreign` FOREIGN KEY (`matrix_period_uuid`) REFERENCES `matrix_periods` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_objectives_status_changed_by_foreign` FOREIGN KEY (`status_changed_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_objectives_strategic_element_uuid_foreign` FOREIGN KEY (`strategic_element_uuid`) REFERENCES `strategic_elements` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `strategic_objectives_strategic_map_uuid_foreign` FOREIGN KEY (`strategic_map_uuid`) REFERENCES `strategic_maps` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `strategic_objectives_structure_uuid_foreign` FOREIGN KEY (`structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `strategic_objectives_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `strategic_objectives`
--

LOCK TABLES `strategic_objectives` WRITE;
/*!40000 ALTER TABLE `strategic_objectives` DISABLE KEYS */;
INSERT INTO `strategic_objectives` VALUES (1,'a7aa7d10-ddae-473c-b5af-e1697c96c6aa','OBJ-001','Renforcer les capacités institutionnelles','56a0ec48-5f29-4778-85cb-f347275fcf95','fae3b1c4-578c-474d-a03f-3953f2f091d6','e679e743-67ea-431a-8493-665cb27cb7c9','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6',NULL,'2025-01-10','2025-06-30','Améliorer la formation et le suivi des agents clés.','medium','low','declared','none',NULL,NULL,1,0,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(2,'490db246-6937-4802-8726-93e032099dc2','OBJ-002','Promouvoir la digitalisation','56a0ec48-5f29-4778-85cb-f347275fcf95','fae3b1c4-578c-474d-a03f-3953f2f091d6','e679e743-67ea-431a-8493-665cb27cb7c9','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6',NULL,'2025-02-01','2025-12-31','Mise en place d\'outils numériques pour optimiser les processus.','medium','low','declared','none',NULL,NULL,1,0,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(3,'43212fc6-31b7-41ee-9336-fb915e65d236','OBJ-003','Accroître la participation communautaire','56a0ec48-5f29-4778-85cb-f347275fcf95','fae3b1c4-578c-474d-a03f-3953f2f091d6','e679e743-67ea-431a-8493-665cb27cb7c9','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6',NULL,'2025-03-15','2025-09-15','Encourager la collaboration avec les associations locales.','medium','high','engaged','none',NULL,NULL,1,0,NULL,NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49');
/*!40000 ALTER TABLE `strategic_objectives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `strategic_stakeholders`
--

DROP TABLE IF EXISTS `strategic_stakeholders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `strategic_stakeholders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_map_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organization` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `responsible` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `strategic_stakeholders_uuid_unique` (`uuid`),
  KEY `strategic_stakeholders_strategic_map_uuid_foreign` (`strategic_map_uuid`),
  KEY `strategic_stakeholders_created_by_foreign` (`created_by`),
  KEY `strategic_stakeholders_updated_by_foreign` (`updated_by`),
  CONSTRAINT `strategic_stakeholders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `strategic_stakeholders_strategic_map_uuid_foreign` FOREIGN KEY (`strategic_map_uuid`) REFERENCES `strategic_maps` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `strategic_stakeholders_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `strategic_stakeholders`
--

LOCK TABLES `strategic_stakeholders` WRITE;
/*!40000 ALTER TABLE `strategic_stakeholders` DISABLE KEYS */;
/*!40000 ALTER TABLE `strategic_stakeholders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `structure_metrics`
--

DROP TABLE IF EXISTS `structure_metrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `structure_metrics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `structure_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `structure_id` int unsigned NOT NULL,
  `aligned_axes_count` int unsigned NOT NULL DEFAULT '0',
  `aligned_objectives_count` int unsigned NOT NULL DEFAULT '0',
  `aligned_maps_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `structure_metrics_structure_uuid_index` (`structure_uuid`),
  KEY `structure_metrics_structure_id_index` (`structure_id`),
  CONSTRAINT `structure_metrics_structure_uuid_foreign` FOREIGN KEY (`structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `structure_metrics`
--

LOCK TABLES `structure_metrics` WRITE;
/*!40000 ALTER TABLE `structure_metrics` DISABLE KEYS */;
/*!40000 ALTER TABLE `structure_metrics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `structures`
--

DROP TABLE IF EXISTS `structures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `structures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `structures_uuid_unique` (`uuid`),
  UNIQUE KEY `structures_parent_uuid_abbreviation_unique` (`parent_uuid`,`abbreviation`),
  UNIQUE KEY `structures_parent_uuid_name_unique` (`parent_uuid`,`name`),
  CONSTRAINT `structures_parent_uuid_foreign` FOREIGN KEY (`parent_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `structures`
--

LOCK TABLES `structures` WRITE;
/*!40000 ALTER TABLE `structures` DISABLE KEYS */;
INSERT INTO `structures` VALUES (1,'f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','STATE-ROOT','Autorité Centrale',NULL,'STATE',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(2,'56a0ec48-5f29-4778-85cb-f347275fcf95','ADM-CENT','Administration Centrale','f1f07bb0-8b5c-4170-9afc-02f307f5f0d6','STRATEGIC',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(3,'f1a52261-f519-4324-a668-3941c39d7cd3','UGP','Unité de Gestion des Projets','56a0ec48-5f29-4778-85cb-f347275fcf95','VIRTUAL',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(4,'dcdb2590-11ac-4e04-bd16-79aed235d8ec','UAS','Unité d\'Appui et de Support','56a0ec48-5f29-4778-85cb-f347275fcf95','VIRTUAL',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(5,'c4196136-dc09-48c8-8b83-baa6d45b7eff','UAS-OPS1','Service d\'Assistance Technique','dcdb2590-11ac-4e04-bd16-79aed235d8ec','OPERATIONAL',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(6,'4dc75397-084c-41bb-8fb1-e596f3dc2a65','UAS-OPS2','Service de Maintenance Systèmes','dcdb2590-11ac-4e04-bd16-79aed235d8ec','OPERATIONAL',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(7,'3e339ff6-13ee-4efc-8eb0-872c6c3c1972','UAS-OPS3','Service Logistique Interne','dcdb2590-11ac-4e04-bd16-79aed235d8ec','OPERATIONAL',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(8,'05ce8f09-4551-4332-bb32-86d4db6902b6','GPRJ','Service Gestion des Projets','f1a52261-f519-4324-a668-3941c39d7cd3','OPERATIONAL',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(9,'150dc506-49c1-4b32-a5b4-92ad8326d634','GPRJ-V1','Cellule Analyse','05ce8f09-4551-4332-bb32-86d4db6902b6','VIRTUAL',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(10,'9740bc28-b8ec-423e-9243-1005b4594dd8','GPRJ-V2','Cellule Suivi-Évaluation','05ce8f09-4551-4332-bb32-86d4db6902b6','VIRTUAL',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(11,'61c3f03b-a3a8-43d4-b917-40e1c6c5d089','GPRJ-V3','Cellule Planification','05ce8f09-4551-4332-bb32-86d4db6902b6','VIRTUAL',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(12,'b254bc3a-0e7a-4ca7-9939-6a2172f85c19','GPRH','Service Ressources Humaines','f1a52261-f519-4324-a668-3941c39d7cd3','OPERATIONAL',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(13,'ea156fac-eba9-4541-8032-4bbcd61eab54','GPRH-V1','Cellule Recrutement','b254bc3a-0e7a-4ca7-9939-6a2172f85c19','VIRTUAL',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28'),(14,'a961c2e7-c64c-49cc-a8a6-85ce2525aa92','GPRH-V2','Cellule Développement des Compétences','b254bc3a-0e7a-4ca7-9939-6a2172f85c19','VIRTUAL',1,NULL,NULL,'2026-05-18 11:35:28','2026-05-18 11:35:28');
/*!40000 ALTER TABLE `structures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_evaluations`
--

DROP TABLE IF EXISTS `supplier_evaluations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_evaluations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `score_delay` tinyint unsigned NOT NULL,
  `score_price` tinyint unsigned NOT NULL,
  `score_quality` tinyint unsigned NOT NULL,
  `total_score` decimal(3,2) DEFAULT '0.00',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `evaluated_at` date DEFAULT NULL,
  `evaluated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_evaluations_uuid_unique` (`uuid`),
  KEY `supplier_evaluations_supplier_uuid_foreign` (`supplier_uuid`),
  KEY `supplier_evaluations_evaluated_by_foreign` (`evaluated_by`),
  CONSTRAINT `supplier_evaluations_evaluated_by_foreign` FOREIGN KEY (`evaluated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `supplier_evaluations_supplier_uuid_foreign` FOREIGN KEY (`supplier_uuid`) REFERENCES `suppliers` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_evaluations`
--

LOCK TABLES `supplier_evaluations` WRITE;
/*!40000 ALTER TABLE `supplier_evaluations` DISABLE KEYS */;
INSERT INTO `supplier_evaluations` VALUES (1,'c1d07407-4fbb-46ff-9f49-78fc6d39c253','573f1054-5322-46ba-a073-9abdb9585844',2,4,1,2.12,'Facere quo fugiat rerum exercitationem. Quasi delectus et quidem unde hic blanditiis.','2026-03-23',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(2,'6e098b89-e921-4353-ad0f-91124fb1e90a','2b253357-9f52-4f56-90ad-56366d70ddd4',2,3,3,2.59,'Nihil ut inventore fuga rem. Et iste delectus quis omnis occaecati.','2026-05-02',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(3,'e126ea3d-4533-4809-a998-0e1ea9242a0a','573f1054-5322-46ba-a073-9abdb9585844',0,3,2,1.68,'Maxime earum nihil illum sed sit cupiditate. Eos fuga expedita labore necessitatibus ipsum officia.','2026-05-12',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(4,'96a64560-87a7-4215-92dd-b68fc839f3d6','5868fdb4-c752-4597-9015-e0acdf85f2cb',0,3,1,1.13,'Eaque qui molestias ducimus ad. Non doloribus est dolore delectus. Rerum eius esse ipsum quisquam.','2026-03-27',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(5,'26638aa2-5d04-4de7-aaed-c0d2e6f23602','a679ee16-70aa-4501-b6ff-9523ca73a24b',3,1,0,1.47,'Commodi est alias voluptatibus. Repudiandae suscipit voluptatum consequatur. Iure in vitae et.','2026-05-15',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(6,'73a21cce-7670-44e7-a4f4-99ef21b22655','f7af3e96-be5f-4d00-87ef-2a9e5e926ea2',0,2,0,0.66,'Nulla quam omnis culpa. Sit id repellendus animi odit. Non accusantium fugiat quae autem eos aut.','2026-05-07',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(7,'83f05dae-d45f-43b1-89d6-67d16532ce1d','573f1054-5322-46ba-a073-9abdb9585844',2,3,3,2.62,'Atque consectetur ea quia qui. Unde amet quidem autem aut adipisci voluptates.','2026-04-27',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(8,'fd36bc6f-ab89-4f63-af5a-9edab292d140','5868fdb4-c752-4597-9015-e0acdf85f2cb',4,1,1,1.91,'Dicta voluptatem aliquam repellat deserunt et doloremque. Est eum sint exercitationem est.','2026-05-16',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(9,'21d7b9b0-dff7-41a7-981c-5945b7030cf9','573f1054-5322-46ba-a073-9abdb9585844',1,2,1,1.47,'Vel quia quis modi nesciunt ut a. Commodi eum asperiores vero commodi.','2026-05-01',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(10,'c3eabb44-a211-456e-97ed-c7df640fd9f4','a679ee16-70aa-4501-b6ff-9523ca73a24b',2,1,0,0.92,'Doloribus architecto error quo ducimus asperiores neque. Et mollitia ratione sapiente harum qui.','2026-05-07',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(11,'64b9897a-ff47-4b4a-af7b-4a6f672743b0','2b253357-9f52-4f56-90ad-56366d70ddd4',3,2,2,2.16,'Dolorum dicta mollitia quis laborum culpa ipsam. Dolores magni ut eum.','2026-03-22',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(12,'2f315bf3-ef31-4dcb-816c-c8c08e648aff','5868fdb4-c752-4597-9015-e0acdf85f2cb',4,0,1,1.96,'In dicta voluptatibus eos est qui. Velit aperiam est id cupiditate.','2026-04-15',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(13,'250e9f5b-1238-4b18-8df8-867d6bf03427','a679ee16-70aa-4501-b6ff-9523ca73a24b',4,4,1,2.76,'Fugiat veniam voluptatibus autem sint libero. Laboriosam tempora aliquam debitis hic dolor laborum.','2026-03-25',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(14,'5cf925aa-0399-4a3e-96ba-9e0a74c002e7','a679ee16-70aa-4501-b6ff-9523ca73a24b',2,3,3,2.33,'Id quibusdam et quaerat aut. Itaque explicabo sit est fuga temporibus voluptatem.','2026-03-26',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49'),(15,'6d546633-ef3d-4797-a9b2-13d2b07b354b','f7af3e96-be5f-4d00-87ef-2a9e5e926ea2',4,2,2,2.86,'Tempora accusantium ut illo ut eligendi. Vero quisquam provident mollitia.','2026-05-07',NULL,'2026-05-18 11:35:49','2026-05-18 11:35:49');
/*!40000 ALTER TABLE `supplier_evaluations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `register_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `establishment_year` year DEFAULT NULL,
  `capital` decimal(14,2) NOT NULL DEFAULT '0.00',
  `annual_turnover` decimal(14,2) NOT NULL DEFAULT '0.00',
  `employees_count` int NOT NULL DEFAULT '0',
  `note` decimal(3,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `whatsapp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_uuid_unique` (`uuid`),
  UNIQUE KEY `suppliers_tax_number_unique` (`tax_number`),
  KEY `suppliers_created_by_foreign` (`created_by`),
  KEY `suppliers_updated_by_foreign` (`updated_by`),
  CONSTRAINT `suppliers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `suppliers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'f7af3e96-be5f-4d00-87ef-2a9e5e926ea2','Kuhic, Schinner and Smith','33908899',NULL,1991,94667591.20,124890842.07,196,1.76,1,'Allan Goyette','+1-270-213-5725',NULL,NULL,NULL,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:49'),(2,'5868fdb4-c752-4597-9015-e0acdf85f2cb','Reilly Inc','91092789','RC-179/niiq',2013,74231806.85,286672101.02,169,1.67,1,'Ida Brakus','1-614-545-2535','1-318-594-7519','hauer@example.com','244 Dell Vista\nEast Kameron, WA 78850',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:49'),(3,'a679ee16-70aa-4501-b6ff-9523ca73a24b','Predovic-Schuppe','91831907','RC-698/inrh',NULL,98500763.16,29556461.86,350,1.87,1,'Dr. Kamryn Koepp','+1.240.508.2373',NULL,NULL,NULL,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:49'),(4,'573f1054-5322-46ba-a073-9abdb9585844','Heidenreich, Kunde and Kilback','91990072',NULL,NULL,33575926.04,259699166.87,206,1.97,1,'Robyn Block IV','(678) 409-8056','+18157747514','justine.hyatt@example.com',NULL,NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:49'),(5,'2b253357-9f52-4f56-90ad-56366d70ddd4','Schmitt-Carter','42268310','RC-222/ttpk',1972,18640696.82,108754032.11,411,2.38,1,'Carmelo Witting','952-486-3876','718.444.8032','tjohns@example.net','231 Gianni Forge Apt. 986\nSengerview, NM 48026',NULL,NULL,'2026-05-18 11:35:48','2026-05-18 11:35:49');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phase_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `priority` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `assigned_to` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deliverable` text COLLATE utf8mb4_unicode_ci,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tasks_uuid_unique` (`uuid`),
  KEY `tasks_phase_uuid_foreign` (`phase_uuid`),
  KEY `tasks_assigned_to_foreign` (`assigned_to`),
  KEY `tasks_created_by_foreign` (`created_by`),
  KEY `tasks_updated_by_foreign` (`updated_by`),
  CONSTRAINT `tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `tasks_phase_uuid_foreign` FOREIGN KEY (`phase_uuid`) REFERENCES `action_phases` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `tasks_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES (1,'f06f03af-9687-48df-a756-1ed30264280b','97806701-866d-45fd-99a7-80b17186d056','Préparer le cahier des charges','Rédaction des besoins et objectifs du projet.','high','2026-05-19','2026-05-25',0,'f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8','2026-05-18 11:35:49','2026-05-18 11:35:49'),(2,'aba62c83-6d08-41f9-9054-aa1052f3c858','97806701-866d-45fd-99a7-80b17186d056','Valider le budget','Réunion de validation avec l\'équipe financière.','medium','2026-05-18','2026-05-23',0,'f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8','2026-05-18 11:35:49','2026-05-18 11:35:49'),(3,'25614b0d-0539-47c0-bdad-e476edf32755','97806701-866d-45fd-99a7-80b17186d056','Lancer le développement','Début du sprint 1 de l\'équipe technique.','high','2026-05-28','2026-06-17',0,'f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8',NULL,'f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8','f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8','2026-05-18 11:35:49','2026-05-18 11:35:49');
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lang` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_uuid_unique` (`uuid`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`),
  KEY `users_created_by_foreign` (`created_by`),
  KEY `users_updated_by_foreign` (`updated_by`),
  KEY `users_role_uuid_foreign` (`role_uuid`),
  CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  CONSTRAINT `users_role_uuid_foreign` FOREIGN KEY (`role_uuid`) REFERENCES `roles` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `users_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'f1ff64d9-8fe4-4267-a1ee-9213dc89f2d8','Admin','admin@admin.com','fr','38086802',1,'$2y$12$hM6LO3NloP0iWUsaNCld4u7ndp.JurreIRsxM9VskQanGjvTEJ3C2',NULL,'2026-05-18 11:35:31','2026-05-18 11:35:31',NULL,NULL,'436fdc3a-f21f-461b-952e-e019b9efb9c4'),(2,'ef55495f-b400-44ae-b800-b82e8634009a','Employé Test','employe@employe.com','fr','01234567',1,'$2y$12$xMxTij.rkUj3tItBSL.sNO2A/Q78OQTrV.Rxj3yWmYb5YL7uHAP0W',NULL,'2026-05-18 11:35:31','2026-05-18 11:35:31',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(3,'09128243-6f6a-4b33-a7ae-7b43b94277cd','Kaela O\'Reilly','maggio.charity@example.org','en','351-413-3023',1,'$2y$12$5qqUkEfdWsaxSunm0tibV.rZRgiSpedluRxeWjFBEqVtOoGLqJT36',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(4,'4299daf7-e238-4f1d-9692-71df1ac7cc2e','Nolan Berge','kim.harber@example.com','fr','+1 (458) 758-2199',1,'$2y$12$NOaXVyW3.ad1iZ.6VDmAYu.cWBDmgm5iFtthJzqypYhDMC1UFnIDK',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(5,'11ea98e3-2c42-453e-aa58-49f1afff24b0','Emie Strosin','qveum@example.org','en','1-336-357-5408',1,'$2y$12$qbee553jtT53V3SRYjZvN.UOAHOd5EXNiAhy.LmVQQYUfTQIYztM2',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(6,'7f2ae275-bede-457b-89b7-66536a09a6a7','Prof. Emile Lubowitz DDS',NULL,'ar','+1-915-241-1041',1,NULL,NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(7,'8cb07c77-5be7-4824-95d4-3b4ec75c146b','Niko Reynolds','vstamm@example.org','en','+1-219-773-8646',1,'$2y$12$iNZg1DWelpv8DmtbScvujOjL6hYctNavsDtcmtnV.v5OT4RajMt5m',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(8,'b984782e-2576-49a6-aee8-4b5bdff02e8d','Gerson Thiel','quitzon.erik@example.com','fr','573-970-8136',1,'$2y$12$0KRlREmEDKf7Dfju5b11Nea9x0ArjM0qDDSvnCQX9RzpFsz61gxQ2',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(9,'e4dea000-72cf-40ae-929a-20e33a36fe50','Rylan West','qhammes@example.net','ar','1-989-780-6080',1,'$2y$12$hWLK1K33nvgjRjU51rIo3ORT2ILSlk2PC2kApA9ubdIGG/L2bW3xu',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(10,'5ffa7b8b-31fe-49df-98c4-38c6de2d9603','Myrtis Gleichner','josefina08@example.net','ar','+1-332-540-9960',1,'$2y$12$fhlglmhDQhrKpKfCWbX5xeffe9iWK5tpaXDANRjUL5IvA4yagyrE6',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(11,'47e95852-f4f4-4e64-940f-9adbd5b0edfb','Ms. Birdie Considine','rodrigo03@example.com','fr','+1 (954) 730-6733',1,'$2y$12$ll3KjTMcSXyu3H2XtJOVgOfteJ9y3dWJ6Y/JOQkCrF6F.H6sJ.8Hy',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(12,'062b1644-ff70-47d9-89b0-a8adac5ea0a6','Kay Rath',NULL,'en','+1 (478) 442-1601',1,NULL,NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(13,'9d61c4dd-f867-40a4-b36c-26b77fb2d335','Boyd Boyer','fkoepp@example.com','ar','+1 (747) 545-4924',1,'$2y$12$hjr8/ceT5KmZvUF3ZIe7AOuPUBmbtBjCpfe6OgsU0380l2tOgkZ22',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(14,'39638505-8d4c-4b81-b3f4-24f196837e16','Arnoldo Hauck','rowland77@example.com','fr','+1-903-568-4268',1,'$2y$12$OBmPG0UQwH98vKv3UMxKp.2jvfdiK5wT9ASKWyw1BYHMIbySBY2lm',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(15,'c0941baf-ade5-494b-ab64-9cc454c9d1a8','Dr. Akeem McKenzie I','clay52@example.net','fr','+1.669.443.7399',1,'$2y$12$g6h6mPXJcgq55qOQDGhnBeMHqq89B0BiLIQ8PffpqkzZ4v15N8nJK',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(16,'9bb56309-f9fe-4be1-bd56-2e9b56b5bd59','Arnulfo Mayert Jr.','general.hilpert@example.org','ar','1-386-507-4167',1,'$2y$12$stRayg.sJQ6zKS8OmfqFJOmofQRjMLzsdVG5OFqhRUpuSWyS4DERu',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(17,'b9d5a85d-cd9d-47e6-ba1e-f5be6f11bc7f','Amalia Bins','herman.giovanna@example.net','fr','434-985-7248',1,'$2y$12$GwCUnu54GJAcNs0.WKvqNedStB5Zfl4bObcsiDuYqtR9CWBOwYzCa',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(18,'8c9dc4cc-d4c8-42ef-a375-c1511cc562b8','Cristal Bernier','gmarquardt@example.org','en','425-495-1078',1,'$2y$12$85WgXVW/nt.meoDXdYVjQ.MHOfN4R031chrsvziw.rD9XQWLKVhdi',NULL,'2026-05-18 11:35:46','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(19,'161beb6d-9eee-43d8-b701-ea0dae69e855','Dr. Cordelia Tromp PhD','alessia.buckridge@example.com','ar','+1-580-358-7081',1,'$2y$12$TyeW57Vb2fg6Y9WM9PN7RO4HWzm6R4ucqyKE54qUlE46GT/gwENKi',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(20,'32233eba-1f16-42c5-b1f4-138ed81e30c7','Karianne Hoeger','casper.odie@example.org','en','+1 (347) 627-5118',1,'$2y$12$HCIUBTnGgikmfN1BSj/Tm.t/oqqWlLllr2ACfvZlluhh/rTUPKSrO',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(21,'465542fb-6afb-4bbc-868e-dbdf3337f187','Yazmin Gusikowski','brown.burley@example.com','fr','+1.302.270.2505',1,'$2y$12$0oWspPUA1MnRz868.f6hDOhLTSLGWjTPuAJhGUDxSx/UyW5DCO/4a',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(22,'5edfc6f5-628c-4b28-a261-74bbd626f089','Grayson Doyle','tgutmann@example.net','ar','+1 (325) 448-1429',1,'$2y$12$3rfsdjT5yRfGNbYZm481MeHiuDfzKG8yRWB0LZHRyWsAF37hA.gKq',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(23,'39405667-35cd-44d8-ba97-404f639ee4bc','Stefan Farrell','meda.daugherty@example.org','ar','1-231-909-1633',1,'$2y$12$PytDoWXnfRjABwPu0CeDaeKChmhRioJNhP5aHmXCQzO79ouPbLzCS',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(24,'56f21624-2acd-4535-83e7-4782f913f54f','Maudie Gleason Jr.',NULL,'fr','+1.860.647.8400',1,NULL,NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(25,'a3011198-148b-4119-b64c-c415b7cd925e','Liza Auer','luisa.rohan@example.org','ar','315.379.3508',1,'$2y$12$Ghuf4usPr4/L1BHy81Zliex/U6emx.3skmstYKVgTHJfkdcvexCOW',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(26,'ae483742-38ff-4d81-ac93-3cf2a9663257','Mrs. Leann Schultz','oren71@example.net','en','248.748.5077',1,'$2y$12$qS3TNfyDTrtRBBwl2OyWyu4f2Zzy.dAlXOE.Yk3TfTfzvQo4.81fu',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(27,'a76a3623-fccb-4694-a06b-eb0b70f68a0a','Alexandrine Weber','wilber.feil@example.org','fr','+18053130059',1,'$2y$12$0umUDbK4etKJXQKGksk.mei3yvzV9kGarOMr2CiF0jeSkQk4p7jr2',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(28,'d1fa387a-cd07-47fb-8889-ae83ba83538a','Andre Willms','margarett43@example.com','fr','+1-847-730-6798',1,'$2y$12$elXP6bkk.cbzEwH8srPuEeIgG5hbzK9qVBIH6wif0cNRDjLUAcAEy',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(29,'ec2d1c01-9adb-4b08-ab9c-3e0cdd5c6164','Prof. Gregory Goodwin','zpowlowski@example.net','fr','(520) 619-9793',1,'$2y$12$.bY9XBDCyorw0EhH.r5VYuiUecOsqSv23GlQYU8LmQ17fcMXzuig.',NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(30,'1303795e-aef4-4b94-8e57-2dc70c9fc501','Kattie Runolfsson',NULL,'fr','918.742.7175',1,NULL,NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(31,'10fc0325-3da5-4318-9df4-af1cd01351a2','Verlie Rippin',NULL,'ar','812-307-1827',1,NULL,NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31'),(32,'58f41856-9d3f-4ca5-ae52-44282b0799a9','Tyshawn Mraz V',NULL,'ar','+1 (909) 893-5671',1,NULL,NULL,'2026-05-18 11:35:47','2026-05-18 11:35:47',NULL,NULL,'01be557a-a1a6-4130-9c67-3137dd28ea31');
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

-- Dump completed on 2026-05-18 11:37:28
