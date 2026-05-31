# ************************************************************
# Sequel Ace SQL dump
# Version 20100
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# Host: mdarem-api.mysql.eu2.frbit.com (MySQL 8.0.44)
# Database: mdarem-api
# Generation Time: 2026-05-18 11:41:18 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table action_beneficiaries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_beneficiaries`;

CREATE TABLE `action_beneficiaries` (
  `action_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`action_uuid`,`beneficiary_uuid`),
  KEY `action_beneficiaries_beneficiary_uuid_foreign` (`beneficiary_uuid`),
  CONSTRAINT `action_beneficiaries_action_uuid_foreign` FOREIGN KEY (`action_uuid`) REFERENCES `actions` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_beneficiaries_beneficiary_uuid_foreign` FOREIGN KEY (`beneficiary_uuid`) REFERENCES `beneficiaries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table action_control_phases
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_control_phases`;

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



# Dump of table action_controls
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_controls`;

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



# Dump of table action_domain_states
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_domain_states`;

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



# Dump of table action_domain_statuses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_domain_statuses`;

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



# Dump of table action_domains
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_domains`;

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
  CONSTRAINT `action_domains_responsible_uuid_foreign` FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_domains_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table action_fund_disbursement_expense_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_fund_disbursement_expense_types`;

CREATE TABLE `action_fund_disbursement_expense_types` (
  `action_fund_disbursement_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expense_type_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(24,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`action_fund_disbursement_uuid`,`expense_type_uuid`),
  KEY `afdet_expense_type_fk` (`expense_type_uuid`),
  CONSTRAINT `afdet_disbursement_fk` FOREIGN KEY (`action_fund_disbursement_uuid`) REFERENCES `action_fund_disbursements` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `afdet_expense_type_fk` FOREIGN KEY (`expense_type_uuid`) REFERENCES `expense_types` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table action_fund_disbursements
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_fund_disbursements`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table action_fund_receipts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_fund_receipts`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table action_funding_sources
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_funding_sources`;

CREATE TABLE `action_funding_sources` (
  `action_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funding_source_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planned_budget` decimal(14,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`action_uuid`,`funding_source_uuid`),
  KEY `action_funding_sources_funding_source_uuid_foreign` (`funding_source_uuid`),
  CONSTRAINT `action_funding_sources_action_uuid_foreign` FOREIGN KEY (`action_uuid`) REFERENCES `actions` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_funding_sources_funding_source_uuid_foreign` FOREIGN KEY (`funding_source_uuid`) REFERENCES `funding_sources` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table action_metrics
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_metrics`;

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



# Dump of table action_objective_alignments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_objective_alignments`;

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



# Dump of table action_periods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_periods`;

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



# Dump of table action_phases
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_phases`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table action_plans
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_plans`;

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
  CONSTRAINT `action_plans_responsible_uuid_foreign` FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_plans_structure_uuid_foreign` FOREIGN KEY (`structure_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `action_plans_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table action_stakeholders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_stakeholders`;

CREATE TABLE `action_stakeholders` (
  `action_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stakeholder_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`action_uuid`,`stakeholder_uuid`),
  KEY `action_stakeholders_stakeholder_uuid_foreign` (`stakeholder_uuid`),
  CONSTRAINT `action_stakeholders_action_uuid_foreign` FOREIGN KEY (`action_uuid`) REFERENCES `actions` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `action_stakeholders_stakeholder_uuid_foreign` FOREIGN KEY (`stakeholder_uuid`) REFERENCES `stakeholders` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table action_statuses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `action_statuses`;

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



# Dump of table actions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `actions`;

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
  `priority` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `risk_level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `prerequisites` text COLLATE utf8mb4_unicode_ci,
  `impacts` text COLLATE utf8mb4_unicode_ci,
  `risks` text COLLATE utf8mb4_unicode_ci,
  `generate_document_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'created',
  `status_changed_at` timestamp NULL DEFAULT NULL,
  `status_changed_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chart_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'BAR',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table activity_beneficiaries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `activity_beneficiaries`;

CREATE TABLE `activity_beneficiaries` (
  `capability_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`capability_domain_uuid`,`beneficiary_uuid`),
  KEY `activity_beneficiaries_beneficiary_uuid_foreign` (`beneficiary_uuid`),
  CONSTRAINT `activity_beneficiaries_beneficiary_uuid_foreign` FOREIGN KEY (`beneficiary_uuid`) REFERENCES `beneficiaries` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `activity_beneficiaries_capability_domain_uuid_foreign` FOREIGN KEY (`capability_domain_uuid`) REFERENCES `capability_domains` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table activity_funding_sources
# ------------------------------------------------------------

DROP TABLE IF EXISTS `activity_funding_sources`;

CREATE TABLE `activity_funding_sources` (
  `capability_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funding_source_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planned_budget` decimal(14,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`capability_domain_uuid`,`funding_source_uuid`),
  KEY `activity_funding_sources_funding_source_uuid_foreign` (`funding_source_uuid`),
  CONSTRAINT `activity_funding_sources_capability_domain_uuid_foreign` FOREIGN KEY (`capability_domain_uuid`) REFERENCES `capability_domains` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `activity_funding_sources_funding_source_uuid_foreign` FOREIGN KEY (`funding_source_uuid`) REFERENCES `funding_sources` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table activity_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `activity_log`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table attachments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `attachments`;

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



# Dump of table beneficiaries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `beneficiaries`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table budget_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `budget_types`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table cache
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table cache_locks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table capability_domain_states
# ------------------------------------------------------------

DROP TABLE IF EXISTS `capability_domain_states`;

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



# Dump of table capability_domain_statuses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `capability_domain_statuses`;

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



# Dump of table capability_domains
# ------------------------------------------------------------

DROP TABLE IF EXISTS `capability_domains`;

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
  CONSTRAINT `capability_domains_responsible_uuid_foreign` FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `capability_domains_strategic_domain_uuid_foreign` FOREIGN KEY (`strategic_domain_uuid`) REFERENCES `strategic_domains` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `capability_domains_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table contracts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `contracts`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table currencies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `currencies`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table decision_statuses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `decision_statuses`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table decisions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `decisions`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table default_phases
# ------------------------------------------------------------

DROP TABLE IF EXISTS `default_phases`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table delegated_project_owners
# ------------------------------------------------------------

DROP TABLE IF EXISTS `delegated_project_owners`;

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
  CONSTRAINT `delegated_project_owners_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `delegated_project_owners_project_owner_uuid_foreign` FOREIGN KEY (`project_owner_uuid`) REFERENCES `project_owners` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `delegated_project_owners_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table departments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `departments`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table elementary_level_beneficiaries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `elementary_level_beneficiaries`;

CREATE TABLE `elementary_level_beneficiaries` (
  `elementary_level_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`elementary_level_uuid`,`beneficiary_uuid`),
  KEY `elementary_level_beneficiaries_beneficiary_uuid_foreign` (`beneficiary_uuid`),
  CONSTRAINT `elementary_level_beneficiaries_beneficiary_uuid_foreign` FOREIGN KEY (`beneficiary_uuid`) REFERENCES `beneficiaries` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `elementary_level_beneficiaries_elementary_level_uuid_foreign` FOREIGN KEY (`elementary_level_uuid`) REFERENCES `elementary_levels` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table elementary_level_funding_sources
# ------------------------------------------------------------

DROP TABLE IF EXISTS `elementary_level_funding_sources`;

CREATE TABLE `elementary_level_funding_sources` (
  `elementary_level_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funding_source_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planned_budget` decimal(14,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`elementary_level_uuid`,`funding_source_uuid`),
  KEY `elementary_level_funding_sources_funding_source_uuid_foreign` (`funding_source_uuid`),
  CONSTRAINT `elementary_level_funding_sources_elementary_level_uuid_foreign` FOREIGN KEY (`elementary_level_uuid`) REFERENCES `elementary_levels` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `elementary_level_funding_sources_funding_source_uuid_foreign` FOREIGN KEY (`funding_source_uuid`) REFERENCES `funding_sources` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table elementary_level_states
# ------------------------------------------------------------

DROP TABLE IF EXISTS `elementary_level_states`;

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



# Dump of table elementary_level_statuses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `elementary_level_statuses`;

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



# Dump of table elementary_levels
# ------------------------------------------------------------

DROP TABLE IF EXISTS `elementary_levels`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table employees
# ------------------------------------------------------------

DROP TABLE IF EXISTS `employees`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table expense_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `expense_types`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table failed_jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `failed_jobs`;

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



# Dump of table file_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `file_types`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table funding_sources
# ------------------------------------------------------------

DROP TABLE IF EXISTS `funding_sources`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table import_logs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `import_logs`;

CREATE TABLE `import_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `import_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `message` text COLLATE utf8mb4_unicode_ci,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `import_logs_uuid_unique` (`uuid`),
  KEY `import_logs_created_by_foreign` (`created_by`),
  CONSTRAINT `import_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table indicator_categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `indicator_categories`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table indicator_controls
# ------------------------------------------------------------

DROP TABLE IF EXISTS `indicator_controls`;

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



# Dump of table indicator_periods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `indicator_periods`;

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



# Dump of table indicator_statuses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `indicator_statuses`;

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



# Dump of table indicators
# ------------------------------------------------------------

DROP TABLE IF EXISTS `indicators`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table job_batches
# ------------------------------------------------------------

DROP TABLE IF EXISTS `job_batches`;

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



# Dump of table jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jobs`;

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



# Dump of table matrix_periods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `matrix_periods`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table municipalities
# ------------------------------------------------------------

DROP TABLE IF EXISTS `municipalities`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table password_reset_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table payment_modes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payment_modes`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_uuid_unique` (`uuid`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table personal_access_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `personal_access_tokens`;

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



# Dump of table procurement_modes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `procurement_modes`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table program_beneficiaries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `program_beneficiaries`;

CREATE TABLE `program_beneficiaries` (
  `action_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`action_domain_uuid`,`beneficiary_uuid`),
  KEY `program_beneficiaries_beneficiary_uuid_foreign` (`beneficiary_uuid`),
  CONSTRAINT `program_beneficiaries_action_domain_uuid_foreign` FOREIGN KEY (`action_domain_uuid`) REFERENCES `action_domains` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `program_beneficiaries_beneficiary_uuid_foreign` FOREIGN KEY (`beneficiary_uuid`) REFERENCES `beneficiaries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table program_funding_sources
# ------------------------------------------------------------

DROP TABLE IF EXISTS `program_funding_sources`;

CREATE TABLE `program_funding_sources` (
  `action_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funding_source_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planned_budget` decimal(14,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`action_domain_uuid`,`funding_source_uuid`),
  KEY `program_funding_sources_funding_source_uuid_foreign` (`funding_source_uuid`),
  CONSTRAINT `program_funding_sources_action_domain_uuid_foreign` FOREIGN KEY (`action_domain_uuid`) REFERENCES `action_domains` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `program_funding_sources_funding_source_uuid_foreign` FOREIGN KEY (`funding_source_uuid`) REFERENCES `funding_sources` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table project_beneficiaries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `project_beneficiaries`;

CREATE TABLE `project_beneficiaries` (
  `strategic_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`strategic_domain_uuid`,`beneficiary_uuid`),
  KEY `project_beneficiaries_beneficiary_uuid_foreign` (`beneficiary_uuid`),
  CONSTRAINT `project_beneficiaries_beneficiary_uuid_foreign` FOREIGN KEY (`beneficiary_uuid`) REFERENCES `beneficiaries` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `project_beneficiaries_strategic_domain_uuid_foreign` FOREIGN KEY (`strategic_domain_uuid`) REFERENCES `strategic_domains` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table project_funding_sources
# ------------------------------------------------------------

DROP TABLE IF EXISTS `project_funding_sources`;

CREATE TABLE `project_funding_sources` (
  `strategic_domain_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funding_source_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planned_budget` decimal(14,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`strategic_domain_uuid`,`funding_source_uuid`),
  KEY `project_funding_sources_funding_source_uuid_foreign` (`funding_source_uuid`),
  CONSTRAINT `project_funding_sources_funding_source_uuid_foreign` FOREIGN KEY (`funding_source_uuid`) REFERENCES `funding_sources` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `project_funding_sources_strategic_domain_uuid_foreign` FOREIGN KEY (`strategic_domain_uuid`) REFERENCES `strategic_domains` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table project_owners
# ------------------------------------------------------------

DROP TABLE IF EXISTS `project_owners`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table regions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `regions`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table role_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `role_permissions`;

CREATE TABLE `role_permissions` (
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `roles`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sessions`;

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



# Dump of table stakeholders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `stakeholders`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table strategic_domain_states
# ------------------------------------------------------------

DROP TABLE IF EXISTS `strategic_domain_states`;

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



# Dump of table strategic_domain_statuses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `strategic_domain_statuses`;

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



# Dump of table strategic_domains
# ------------------------------------------------------------

DROP TABLE IF EXISTS `strategic_domains`;

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
  CONSTRAINT `strategic_domains_responsible_uuid_foreign` FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE RESTRICT,
  CONSTRAINT `strategic_domains_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table strategic_elements
# ------------------------------------------------------------

DROP TABLE IF EXISTS `strategic_elements`;

CREATE TABLE `strategic_elements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `structure_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategic_map_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_structure_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_map_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_element_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('AXIS','LEVER') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AXIS',
  `order` int unsigned NOT NULL DEFAULT '0',
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table strategic_maps
# ------------------------------------------------------------

DROP TABLE IF EXISTS `strategic_maps`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table strategic_objective_statuses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `strategic_objective_statuses`;

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



# Dump of table strategic_objectives
# ------------------------------------------------------------

DROP TABLE IF EXISTS `strategic_objectives`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table strategic_stakeholders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `strategic_stakeholders`;

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



# Dump of table structure_metrics
# ------------------------------------------------------------

DROP TABLE IF EXISTS `structure_metrics`;

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



# Dump of table structures
# ------------------------------------------------------------

DROP TABLE IF EXISTS `structures`;

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
  UNIQUE KEY `unique_parent_abbreviation` (`parent_uuid`,`abbreviation`),
  UNIQUE KEY `unique_parent_name` (`parent_uuid`,`name`),
  CONSTRAINT `structures_parent_uuid_foreign` FOREIGN KEY (`parent_uuid`) REFERENCES `structures` (`uuid`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table supplier_evaluations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `supplier_evaluations`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table suppliers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `suppliers`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table tasks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tasks`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
