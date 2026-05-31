-- ============================================================
-- PATCH SQL - Mise à jour base de données en ligne
-- Source : Dump20260518.sql (local)
-- Cible  : mdarem-api_2026-05-18.sql (en ligne)
-- Date   : 2026-05-18
-- ============================================================
-- Appliquer ce script sur la base de données en ligne (mdarem-api)
-- pour la synchroniser avec la version locale.
--
-- Résumé des changements :
--   1. actions          - Ajout de DEFAULT sur priority, risk_level,
--                         generate_document_type + fix chart_type NOT NULL
--   2. action_domains   - FK responsible_uuid : RESTRICT → SET NULL
--   3. action_plans     - FK responsible_uuid : RESTRICT → SET NULL
--   4. capability_domains - FK responsible_uuid : RESTRICT → SET NULL
--   5. delegated_project_owners - FK created_by/updated_by : RESTRICT → SET NULL
--   6. strategic_domains - FK responsible_uuid : RESTRICT → SET NULL
--   7. structures       - Renommage des index uniques
--   8. migrations       - Synchronisation de la table des migrations Laravel
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. TABLE `actions`
-- Ajout des valeurs par défaut manquantes sur les colonnes
-- ============================================================

ALTER TABLE `actions`
    MODIFY COLUMN `priority`
        varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
    MODIFY COLUMN `risk_level`
        varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'moderate',
    MODIFY COLUMN `generate_document_type`
        varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'autre',
    MODIFY COLUMN `chart_type`
        varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BAR';

-- ============================================================
-- 2. TABLE `action_domains`
-- FK responsible_uuid : ON DELETE RESTRICT → ON DELETE SET NULL
-- ============================================================

ALTER TABLE `action_domains`
    DROP FOREIGN KEY `action_domains_responsible_uuid_foreign`;

ALTER TABLE `action_domains`
    ADD CONSTRAINT `action_domains_responsible_uuid_foreign`
        FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE SET NULL;

-- ============================================================
-- 3. TABLE `action_plans`
-- FK responsible_uuid : ON DELETE RESTRICT → ON DELETE SET NULL
-- ============================================================

ALTER TABLE `action_plans`
    DROP FOREIGN KEY `action_plans_responsible_uuid_foreign`;

ALTER TABLE `action_plans`
    ADD CONSTRAINT `action_plans_responsible_uuid_foreign`
        FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE SET NULL;

-- ============================================================
-- 4. TABLE `capability_domains`
-- FK responsible_uuid : ON DELETE RESTRICT → ON DELETE SET NULL
-- ============================================================

ALTER TABLE `capability_domains`
    DROP FOREIGN KEY `capability_domains_responsible_uuid_foreign`;

ALTER TABLE `capability_domains`
    ADD CONSTRAINT `capability_domains_responsible_uuid_foreign`
        FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE SET NULL;

-- ============================================================
-- 5. TABLE `delegated_project_owners`
-- FK created_by et updated_by : ON DELETE RESTRICT → ON DELETE SET NULL
-- ============================================================

ALTER TABLE `delegated_project_owners`
    DROP FOREIGN KEY `delegated_project_owners_created_by_foreign`,
    DROP FOREIGN KEY `delegated_project_owners_updated_by_foreign`;

ALTER TABLE `delegated_project_owners`
    ADD CONSTRAINT `delegated_project_owners_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
    ADD CONSTRAINT `delegated_project_owners_updated_by_foreign`
        FOREIGN KEY (`updated_by`) REFERENCES `users` (`uuid`) ON DELETE SET NULL;

-- ============================================================
-- 6. TABLE `strategic_domains`
-- FK responsible_uuid : ON DELETE RESTRICT → ON DELETE SET NULL
-- ============================================================

ALTER TABLE `strategic_domains`
    DROP FOREIGN KEY `strategic_domains_responsible_uuid_foreign`;

ALTER TABLE `strategic_domains`
    ADD CONSTRAINT `strategic_domains_responsible_uuid_foreign`
        FOREIGN KEY (`responsible_uuid`) REFERENCES `users` (`uuid`) ON DELETE SET NULL;

-- ============================================================
-- 7. TABLE `structures`
-- Renommage des index uniques pour correspondre aux conventions Laravel
-- ============================================================

ALTER TABLE `structures`
    DROP INDEX `unique_parent_abbreviation`,
    DROP INDEX `unique_parent_name`;

ALTER TABLE `structures`
    ADD UNIQUE KEY `structures_parent_uuid_abbreviation_unique` (`parent_uuid`, `abbreviation`),
    ADD UNIQUE KEY `structures_parent_uuid_name_unique` (`parent_uuid`, `name`);

-- ============================================================
-- 8. TABLE `migrations`
-- Insertion de l'historique complet des migrations Laravel
-- (la table est vide en ligne)
-- ============================================================

TRUNCATE TABLE `migrations`;

INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2025_01_03_131026_add_columns_to_users_table',1),
(5,'2025_04_23_003906_create_structures_table',1),
(6,'2025_04_23_003915_create_employees_table',1),
(7,'2025_04_26_234502_create_personal_access_tokens_table',1),
(8,'2025_05_12_023038_create_currencies_table',1),
(9,'2025_05_12_024133_create_funding_sources_table',1),
(10,'2025_05_13_004636_create_action_plans_table',1),
(11,'2025_07_03_175659_create_regions_table',1),
(12,'2025_07_04_001823_create_procurement_modes_table',1),
(13,'2025_07_04_102213_create_project_owners_table',1),
(14,'2025_07_04_122509_create_departments_table',1),
(15,'2025_07_05_020521_create_municipalities_table',1),
(16,'2025_07_05_020523_create_action_domains_table',1),
(17,'2025_07_05_020526_create_strategic_domains_table',1),
(18,'2025_07_05_231017_create_delegated_project_owners_table',1),
(19,'2025_07_07_001519_create_capability_domains_table',1),
(20,'2025_07_07_001520_create_elementary_levels_table',1),
(21,'2025_07_21_132103_create_actions_table',1),
(22,'2025_07_21_133709_create_beneficiaries_table',1),
(23,'2025_07_21_161138_create_stakeholders_table',1),
(24,'2025_07_24_085807_create_action_beneficiaries_table',1),
(25,'2025_07_24_085839_create_action_stakeholders_table',1),
(26,'2025_07_24_085856_create_action_funding_sources_table',1),
(27,'2025_08_05_130114_create_payment_modes_table',1),
(28,'2025_08_05_202620_create_action_periods_table',1),
(29,'2025_08_06_091614_create_expense_types_table',1),
(30,'2025_08_06_101728_create_budget_types_table',1),
(31,'2025_08_06_101834_create_action_phases_table',1),
(32,'2025_08_06_101835_create_suppliers_table',1),
(33,'2025_08_06_101836_create_contracts_table',1),
(34,'2025_08_06_101837_create_supplier_evaluations_table',1),
(35,'2025_08_06_102249_create_tasks_table',1),
(36,'2025_08_06_102250_create_action_fund_disbursements_table',1),
(37,'2025_08_06_124532_create_action_fund_receipts_table',1),
(38,'2025_08_19_123433_create_strategic_maps_table',1),
(39,'2025_08_20_102705_create_strategic_elements_table',1),
(40,'2025_08_20_121632_matrix_periods_table',1),
(41,'2025_08_20_121633_create_strategic_objectives_table',1),
(42,'2025_08_20_121634_create_file_types_table',1),
(43,'2025_08_20_163220_create_attachments_table',1),
(44,'2025_08_21_200634_create_action_controls_table',1),
(45,'2025_08_21_200643_create_action_control_phases_table',1),
(46,'2025_08_26_135316_create_decisions_table',1),
(47,'2025_08_26_135317_create_decision_statuses_table',1),
(48,'2025_08_26_215701_create_strategic_stakeholders_table',1),
(49,'2025_08_28_134440_create_indicator_categories_table',1),
(50,'2025_08_28_134445_create_indicators_table',1),
(51,'2025_08_28_191224_create_indicator_periods_table',1),
(52,'2025_08_31_134914_create_indicator_controls_table',1),
(53,'2025_09_01_061858_action_fund_disbursement_expense_types',1),
(54,'2025_09_07_080019_create_action_objective_alignments_table',1),
(55,'2025_09_09_064346_create_activity_log_table',1),
(56,'2025_09_09_064347_add_event_column_to_activity_log_table',1),
(57,'2025_09_09_064348_add_batch_uuid_column_to_activity_log_table',1),
(58,'2025_09_23_112150_create_action_metrics_table',1),
(59,'2025_09_23_121623_create_structure_metrics_table',1),
(60,'2025_10_26_162028_program_funding_sources',1),
(61,'2025_10_26_162051_program_beneficiaries',1),
(62,'2025_10_27_113007_create_action_statuses_table',1),
(63,'2025_10_27_113007_create_strategic_objective_statuses_table',1),
(64,'2025_10_27_130733_create_action_domain_statuses_table',1),
(65,'2025_10_27_130743_create_action_domain_states_table',1),
(66,'2025_10_28_062749_create_strategic_domain_statuses_table',1),
(67,'2025_10_28_062754_create_strategic_domain_states_table',1),
(68,'2025_10_28_062764_project_funding_sources',1),
(69,'2025_10_28_062774_project_beneficiaries',1),
(70,'2025_10_28_062784_create_capability_domain_statuses_table',1),
(71,'2025_10_28_062794_create_capability_domain_states_table',1),
(72,'2025_10_28_062796_activity_funding_sources',1),
(73,'2025_10_28_062798_activity_beneficiaries',1),
(74,'2025_11_04_131643_create_default_phases_table',1),
(75,'2025_11_09_065604_create_roles_table',1),
(76,'2025_11_09_065701_create_permissions_table',1),
(77,'2025_11_09_065735_role_permissions_table',1),
(78,'2025_11_09_070110_add_role_to_users_table',1),
(79,'2025_11_11_235945_add_parent_fk_to_strategic_elements_table',1),
(80,'2025_11_25_132554_create_indicator_statuses_table',1),
(81,'2025_12_04_194057_elementary_level_funding_sources',1),
(82,'2025_12_04_194459_elementary_level_beneficiaries',1),
(83,'2025_12_04_201130_create_elementary_level_states_table',1),
(84,'2025_12_04_201208_create_elementary_level_statuses_table',1),
(85,'2026_03_13_173219_add_reference_to_strategic_elements_table',1),
(86,'2026_04_22_120000_add_dates_to_indicators_table',1);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- FIN DU PATCH
-- ============================================================
