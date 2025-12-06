<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::whereNotNull('id')->delete();

        $permissions = [

            // Strategic maps
            ['name' => 'ACCESS_STRATEGIC_MAPS', 'category' => 'Carte stratégique', 'description' => 'Accès au module'],
            ['name' => 'CREATE_STRATEGIC_MAP', 'category' => 'Carte stratégique', 'description' => 'Créer une carte'],
            ['name' => 'READ_ALL_STRATEGIC_MAPS', 'category' => 'Carte stratégique', 'description' => 'Voir toutes les cartes'],
            ['name' => 'READ_SINGLE_STRATEGIC_MAP', 'category' => 'Carte stratégique', 'description' => 'Voir une carte'],
            ['name' => 'UPDATE_STRATEGIC_MAP', 'category' => 'Carte stratégique', 'description' => 'Modifier une carte'],
            ['name' => 'DELETE_STRATEGIC_MAP', 'category' => 'Carte stratégique', 'description' => 'Supprimer une carte'],

            // Priority matrix
            ['name' => 'ACCESS_PRIORITY_MATRIX', 'category' => 'Matrice de priorité', 'description' => 'Accès au module'],
            ['name' => 'MANAGE_PRIORITY_MATRIX', 'category' => 'Matrice de priorité', 'description' => 'Gérer une matrice'],

            // Stakeholders
            ['name' => 'ACCESS_STAKEHOLDERS', 'category' => 'Partie prenante', 'description' => 'Accès au module'],
            ['name' => 'MANAGE_STAKEHOLDERS', 'category' => 'Partie prenante', 'description' => 'Gérer les parties prenantes'],

            // Strategic levers
            ['name' => 'ACCESS_STRATEGIC_LEVERS', 'category' => 'Levier stratégique', 'description' => 'Accès au module'],
            ['name' => 'CREATE_STRATEGIC_LEVER',  'category' => 'Levier stratégique', 'description' => 'Créer un levier'],
            ['name' => 'READ_STRATEGIC_LEVERS',   'category' => 'Levier stratégique', 'description' => 'Voir les leviers'],
            ['name' => 'UPDATE_STRATEGIC_LEVER',  'category' => 'Levier stratégique', 'description' => 'Modifier un levier'],
            ['name' => 'DELETE_STRATEGIC_LEVER',  'category' => 'Levier stratégique', 'description' => 'Supprimer un levier'],

            //Strategic axes
            ['name' => 'ACCESS_STRATEGIC_AXES', 'category' => 'Axe stratégique', 'description' => 'Accès au module'],
            ['name' => 'CREATE_STRATEGIC_AXIS', 'category' => 'Axe stratégique', 'description' => 'Créer un axe'],
            ['name' => 'READ_STRATEGIC_AXES', 'category' => 'Axe stratégique', 'description' => 'Voir les axes'],
            ['name' => 'UPDATE_STRATEGIC_AXIS', 'category' => 'Axe stratégique', 'description' => 'Modifier un axe'],
            ['name' => 'DELETE_STRATEGIC_AXIS', 'category' => 'Axe stratégique', 'description' => 'Supprimer un axe'],

            // Strategic objective
            ['name' => 'ACCESS_STRATEGIC_OBJECTIVES', 'category' => 'Objectif stratégique', 'description' => 'Accès au module'],
            ['name' => 'CREATE_STRATEGIC_OBJECTIVE', 'category' => 'Objectif stratégique', 'description' => 'Créer un objectif'],
            ['name' => 'READ_STRATEGIC_OBJECTIVES', 'category' => 'Objectif stratégique', 'description' => 'Voir les objectifs'],
            ['name' => 'UPDATE_STRATEGIC_OBJECTIVE', 'category' => 'Objectif stratégique', 'description' => 'Modifier un objectif'],
            ['name' => 'DELETE_STRATEGIC_OBJECTIVE', 'category' => 'Objectif stratégique', 'description' => 'Supprimer un objectif'],

            // Strategic objective — Alignment
            ['name' => 'OBJ_ACCESS_ALIGNMENT', 'category' => 'Objectif — Alignement', 'description' => 'Accès à l\'onglet'],
            ['name' => 'OBJ_MANAGE_ALIGNMENT', 'category' => 'Objectif — Alignement', 'description' => 'Gérer l\'alignement'],

            // Strategic objective — Decision
            ['name' => 'OBJ_ACCESS_DECISIONS', 'category' => 'Objectif — Décision', 'description' => 'Accès au module'],
            ['name' => 'OBJ_MANAGE_DECISIONS', 'category' => 'Objectif — Décision', 'description' => 'Gérer les décisions'],

            // Strategic objective — File
            ['name' => 'OBJ_ACCESS_FILES', 'category' => 'Objectif — Fichier', 'description' => 'Accès aux fichiers'],
            ['name' => 'OBJ_MANAGE_FILES', 'category' => 'Objectif — Fichier', 'description' => 'Gérer les fichiers'],

            // Indicator
            ['name' => 'ACCESS_INDICATORS', 'category' => 'Indicateur', 'description' => 'Accès au module'],
            ['name' => 'CREATE_INDICATOR', 'category' => 'Indicateur', 'description' => 'Créer un indicateur'],
            ['name' => 'READ_INDICATORS', 'category' => 'Indicateur', 'description' => 'Voir les indicateurs'],
            ['name' => 'UPDATE_INDICATOR', 'category' => 'Indicateur', 'description' => 'Modifier un indicateur'],
            ['name' => 'DELETE_INDICATOR', 'category' => 'Indicateur', 'description' => 'Supprimer un indicateur'],

            // Indicator — Status
            ['name' => 'IND_ACCESS_STATUS', 'category' => 'Indicateur statut', 'description' => 'Accès au statut'],
            ['name' => 'IND_MANAGE_STATUS', 'category' => 'Indicateur — Statut', 'description' => 'Gérer le statut'],

            // Indicator — Planning
            ['name' => 'IND_ACCESS_PLANNING', 'category' => 'Indicateur planification', 'description' => 'Accès à la planification'],
            ['name' => 'IND_MANAGE_PLANNING', 'category' => 'Indicateur — Planification', 'description' => 'Gérer la planification'],

            // Indicator — Control
            ['name' => 'IND_ACCESS_CONTROL', 'category' => 'Indicateur contrôle', 'description' => 'Accès au contrôle'],
            ['name' => 'IND_MANAGE_CONTROL', 'category' => 'Indicateur — Contrôle', 'description' => 'Gérer les contrôles'],

            // Indicator — File
            ['name' => 'IND_ACCESS_FILES', 'category' => 'Indicateur fichier', 'description' => 'Accès aux fichiers'],
            ['name' => 'IND_MANAGE_FILES', 'category' => 'Indicateur — Fichier', 'description' => 'Gérer les fichiers'],

            // Indicator — Decision
            ['name' => 'IND_ACCESS_DECISIONS', 'category' => 'Indicateur décision', 'description' => 'Accès aux décisions'],
            ['name' => 'IND_MANAGE_DECISIONS', 'category' => 'Indicateur — Décision', 'description' => 'Gérer les décisions'],

            // Indicator — Reporting : performance
            ['name' => 'IND_ACCESS_REPORTING', 'category' => 'Indicateur reporting', 'description' => 'Accès au reporting'],

            // Action plans
            ['name' => 'ACCESS_ACTION_PLANS', 'category' => 'Plan d\'action', 'description' => 'Accès au module'],
            ['name' => 'CREATE_ACTION_PLAN', 'category' => 'Plan d\'action', 'description' => 'Créer un plan'],
            ['name' => 'READ_ACTION_PLANS', 'category' => 'Plan d\'action', 'description' => 'Voir les plans'],
            ['name' => 'UPDATE_ACTION_PLAN', 'category' => 'Plan d\'action', 'description' => 'Modifier un plan'],
            ['name' => 'DELETE_ACTION_PLAN', 'category' => 'Plan d\'action', 'description' => 'Supprimer un plan'],

            // Actions
            ['name' => 'ACCESS_ACTIONS', 'category' => 'Action', 'description' => 'Accès au module'],
            ['name' => 'CREATE_ACTION', 'category' => 'Action', 'description' => 'Créer une action'],
            ['name' => 'READ_ACTIONS', 'category' => 'Action', 'description' => 'Voir les actions'],
            ['name' => 'UPDATE_ACTION', 'category' => 'Action', 'description' => 'Modifier une action'],
            ['name' => 'DELETE_ACTION', 'category' => 'Action', 'description' => 'Supprimer une action'],

            // Action - Status
            ['name' => 'ACT_ACCESS_STATUS', 'category' => 'Action statut', 'description' => 'Accès au statut'],
            ['name' => 'ACT_MANAGE_STATUS', 'category' => 'Action — Statut', 'description' => 'Gérer le statut'],

            // Action - Planning
            ['name' => 'ACT_ACCESS_PLANNING', 'category' => 'Action planification', 'description' => 'Accès à la planification'],
            ['name' => 'ACT_MANAGE_PLANNING', 'category' => 'Action — Planification', 'description' => 'Gérer la planification'],

            // Action - Control
            ['name' => 'ACT_ACCESS_CONTROL', 'category' => 'Action contrôle', 'description' => 'Accès au contrôle'],
            ['name' => 'ACT_MANAGE_CONTROL', 'category' => 'Action — Contrôle', 'description' => 'Gérer les contrôles'],

            // Action - Alignment
            ['name' => 'ACT_ACCESS_ALIGNMENT', 'category' => 'Action alignement', 'description' => 'Accès à l\'alignement'],
            ['name' => 'ACT_MANAGE_ALIGNMENT', 'category' => 'Action — Alignement', 'description' => 'Gérer l\'alignement'],

            // Action Domain - Files
            ['name' => 'ACD_ACCESS_FILES', 'category' => 'Action Domain fichier', 'description' => 'Accès aux fichiers'],
            ['name' => 'ACD_MANAGE_FILES', 'category' => 'Action — Domain — Fichier', 'description' => 'Gérer les fichiers'],

            // Strategic Domain - Files
            ['name' => 'SD_ACCESS_FILES', 'category' => 'Stratégic Domain fichier', 'description' => 'Accès aux fichiers'],
            ['name' => 'SD_MANAGE_FILES', 'category' => 'Stratégic — Domain — Fichier', 'description' => 'Gérer les fichiers'],

            // Capability Domain - Files
            ['name' => 'CD_ACCESS_FILES', 'category' => 'Capacitaire Domain fichier', 'description' => 'Accès aux fichiers'],
            ['name' => 'CD_MANAGE_FILES', 'category' => 'Capacitaire — Domain — Fichier', 'description' => 'Gérer les fichiers'],

            // Elementary Level - Files
            ['name' => 'EML_ACCESS_FILES', 'category' => 'Niveau Elémentaire fichier', 'description' => 'Accès aux fichiers'],
            ['name' => 'EML_MANAGE_FILES', 'category' => 'Niveau — Elémentaire — Fichier', 'description' => 'Gérer les fichiers'],

            // Action - Files
            ['name' => 'ACT_ACCESS_FILES', 'category' => 'Action fichier', 'description' => 'Accès aux fichiers'],
            ['name' => 'ACT_MANAGE_FILES', 'category' => 'Action — Fichier', 'description' => 'Gérer les fichiers'],

            // Action - Decisions
            ['name' => 'ACT_ACCESS_DECISIONS', 'category' => 'Action décision', 'description' => 'Accès aux décisions'],
            ['name' => 'ACT_MANAGE_DECISIONS', 'category' => 'Action — Décision', 'description' => 'Gérer les décisions'],

            // Action - Phases
            ['name' => 'ACT_ACCESS_PHASES', 'category' => 'Action — Phase', 'description' => 'Accès aux phases'],
            ['name' => 'ACT_MANAGE_PHASES', 'category' => 'Action — Phase', 'description' => 'Gérer les phases'],

            // Action - Reporting
            ['name' => 'ACT_ACCESS_REPORTING', 'category' => 'Action reporting', 'description' => 'Accès au reporting'],

            // Suppliers
            ['name' => 'ACCESS_SUPPLIERS', 'category' => 'Fournisseur', 'description' => 'Accès au module'],
            ['name' => 'CREATE_SUPPLIER', 'category' => 'Fournisseur', 'description' => 'Créer un fournisseur'],
            ['name' => 'READ_SUPPLIERS', 'category' => 'Fournisseur', 'description' => 'Voir les fournisseurs'],
            ['name' => 'UPDATE_SUPPLIER', 'category' => 'Fournisseur', 'description' => 'Modifier un fournisseur'],
            ['name' => 'DELETE_SUPPLIER', 'category' => 'Fournisseur', 'description' => 'Supprimer un fournisseur'],

            // Supplier - Contracts
            ['name' => 'SUP_ACCESS_CONTRACTS', 'category' => 'Fournisseur contrat', 'description' => 'Accès aux contrats'],
            ['name' => 'SUP_MANAGE_CONTRACTS', 'category' => 'Fournisseur — Contrats', 'description' => 'Gérer les contrats'],

            // Supplier - Evaluation
            ['name' => 'SUP_ACCESS_EVALUATIONS', 'category' => 'Fournisseur évaluation', 'description' => 'Accès aux évaluations'],
            ['name' => 'SUP_MANAGE_EVALUATIONS', 'category' => 'Fournisseur — Évaluations', 'description' => 'Gérer les évaluations'],

            // Supplier - Files
            ['name' => 'SUP_ACCESS_FILES', 'category' => 'Fournisseur fichier', 'description' => 'Accès aux fichiers'],
            ['name' => 'SUP_MANAGE_FILES', 'category' => 'Fournisseur — Fichiers', 'description' => 'Gérer les fichiers'],

            // Fund receipts
            ['name' => 'ACCESS_FUND_RECEIPTS', 'category' => 'Encaissement', 'description' => 'Accès au module'],
            ['name' => 'CREATE_FUND_RECEIPT', 'category' => 'Encaissement', 'description' => 'Créer un encaissement'],
            ['name' => 'READ_FUND_RECEIPTS', 'category' => 'Encaissement', 'description' => 'Voir les encaissements'],
            ['name' => 'UPDATE_FUND_RECEIPT', 'category' => 'Encaissement', 'description' => 'Modifier un encaissement'],
            ['name' => 'DELETE_FUND_RECEIPT', 'category' => 'Encaissement', 'description' => 'Supprimer un encaissement'],

            // Fund disbursements
            ['name' => 'ACCESS_FUND_DISBURSEMENTS', 'category' => 'Décaissement', 'description' => 'Accès au module'],
            ['name' => 'CREATE_FUND_DISBURSEMENT', 'category' => 'Décaissement', 'description' => 'Créer un décaissement'],
            ['name' => 'READ_FUND_DISBURSEMENTS', 'category' => 'Décaissement', 'description' => 'Voir les décaissements'],
            ['name' => 'UPDATE_FUND_DISBURSEMENT', 'category' => 'Décaissement', 'description' => 'Modifier un décaissement'],
            ['name' => 'DELETE_FUND_DISBURSEMENT', 'category' => 'Décaissement', 'description' => 'Supprimer un décaissement'],

            // Structures
            ['name' => 'ACCESS_STRUCTURES', 'category' => 'Structure', 'description' => 'Accès au module'],
            ['name' => 'CREATE_STRUCTURE', 'category' => 'Structure', 'description' => 'Créer une structure'],
            ['name' => 'READ_STRUCTURES', 'category' => 'Structure', 'description' => 'Voir les structures'],
            ['name' => 'UPDATE_STRUCTURE', 'category' => 'Structure', 'description' => 'Modifier une structure'],
            ['name' => 'DELETE_STRUCTURE', 'category' => 'Structure', 'description' => 'Supprimer une structure'],

            // Employees (Personnel)
            ['name' => 'ACCESS_EMPLOYEES', 'category' => 'Personnel', 'description' => 'Accès au module'],
            ['name' => 'CREATE_EMPLOYEE', 'category' => 'Personnel', 'description' => 'Créer un personnel'],
            ['name' => 'READ_EMPLOYEES', 'category' => 'Personnel', 'description' => 'Voir le personnel'],
            ['name' => 'UPDATE_EMPLOYEE', 'category' => 'Personnel', 'description' => 'Modifier le personnel'],
            ['name' => 'DELETE_EMPLOYEE', 'category' => 'Personnel', 'description' => 'Supprimer le personnel'],

            // Action domains
            ['name' => 'ACCESS_ACTION_DOMAINS', 'category' => 'Domaine d\'action', 'description' => 'Accès au module'],
            ['name' => 'CREATE_ACTION_DOMAIN', 'category' => 'Domaine d\'action', 'description' => 'Créer un domaine d\'action'],
            ['name' => 'READ_ACTION_DOMAINS', 'category' => 'Domaine d\'action', 'description' => 'Voir les domaines d\'action'],
            ['name' => 'UPDATE_ACTION_DOMAIN', 'category' => 'Domaine d\'action', 'description' => 'Modifier un domaine d\'action'],
            ['name' => 'DELETE_ACTION_DOMAIN', 'category' => 'Domaine d\'action', 'description' => 'Supprimer un domaine d\'action'],

            // Strategic domains
            ['name' => 'ACCESS_STRATEGIC_DOMAINS', 'category' => 'Domaine stratégique', 'description' => 'Accès au module'],
            ['name' => 'CREATE_STRATEGIC_DOMAIN', 'category' => 'Domaine stratégique', 'description' => 'Créer un domaine stratégique'],
            ['name' => 'READ_STRATEGIC_DOMAINS', 'category' => 'Domaine stratégique', 'description' => 'Voir les domaines stratégiques'],
            ['name' => 'UPDATE_STRATEGIC_DOMAIN', 'category' => 'Domaine stratégique', 'description' => 'Modifier un domaine stratégique'],
            ['name' => 'DELETE_STRATEGIC_DOMAIN', 'category' => 'Domaine stratégique', 'description' => 'Supprimer un domaine stratégique'],

            // Capability domains
            ['name' => 'ACCESS_CAPABILITY_DOMAINS', 'category' => 'Domaine capacitaire', 'description' => 'Accès au module'],
            ['name' => 'CREATE_CAPABILITY_DOMAIN', 'category' => 'Domaine capacitaire', 'description' => 'Créer un domaine capacitaire'],
            ['name' => 'READ_CAPABILITY_DOMAINS', 'category' => 'Domaine capacitaire', 'description' => 'Voir les domaines capacitaires'],
            ['name' => 'UPDATE_CAPABILITY_DOMAIN', 'category' => 'Domaine capacitaire', 'description' => 'Modifier un domaine capacitaire'],
            ['name' => 'DELETE_CAPABILITY_DOMAIN', 'category' => 'Domaine capacitaire', 'description' => 'Supprimer un domaine capacitaire'],

            // Elementary levels
            ['name' => 'ACCESS_ELEMENTARY_LEVELS', 'category' => 'Niveau élémentaire', 'description' => 'Accès au module'],
            ['name' => 'CREATE_ELEMENTARY_LEVEL', 'category' => 'Niveau élémentaire', 'description' => 'Créer un niveau élémentaire'],
            ['name' => 'READ_ELEMENTARY_LEVELS', 'category' => 'Niveau élémentaire', 'description' => 'Voir les niveaux élémentaires'],
            ['name' => 'UPDATE_ELEMENTARY_LEVEL', 'category' => 'Niveau élémentaire', 'description' => 'Modifier un niveau élémentaire'],
            ['name' => 'DELETE_ELEMENTARY_LEVEL', 'category' => 'Niveau élémentaire', 'description' => 'Supprimer un niveau élémentaire'],

            // Reporting
            ['name' => 'ACCESS_REPORTING', 'category' => 'Reporting', 'description' => 'Accès au module'],

            // System Logs
            ['name' => 'ACCESS_LOGS', 'category' => 'Logs système', 'description' => 'Accès au module'],

            // Currencies
            ['name' => 'ACCESS_CURRENCIES', 'category' => 'Devise', 'description' => 'Accès au module'],
            ['name' => 'CREATE_CURRENCY', 'category' => 'Devise', 'description' => 'Créer une devise'],
            ['name' => 'READ_CURRENCIES', 'category' => 'Devise', 'description' => 'Voir les devises'],
            ['name' => 'UPDATE_CURRENCY', 'category' => 'Devise', 'description' => 'Modifier une devise'],
            ['name' => 'DELETE_CURRENCY', 'category' => 'Devise', 'description' => 'Supprimer une devise'],

            // Default phases
            ['name' => 'ACCESS_DEFAULT_PHASES', 'category' => 'Phase par défaut', 'description' => 'Accès au module'],
            ['name' => 'CREATE_DEFAULT_PHASE', 'category' => 'Phase par défaut', 'description' => 'Créer une phase'],
            ['name' => 'UPDATE_DEFAULT_PHASE', 'category' => 'Phase par défaut', 'description' => 'Modifier une phase'],
            ['name' => 'DELETE_DEFAULT_PHASE', 'category' => 'Phase par défaut', 'description' => 'Supprimer une phase'],

            // File types
            ['name' => 'ACCESS_FILE_TYPES', 'category' => 'Type de fichier', 'description' => 'Accès au module'],
            ['name' => 'CREATE_FILE_TYPE', 'category' => 'Type de fichier', 'description' => 'Créer un type'],
            ['name' => 'READ_FILE_TYPES', 'category' => 'Type de fichier', 'description' => 'Voir les types'],
            ['name' => 'UPDATE_FILE_TYPE', 'category' => 'Type de fichier', 'description' => 'Modifier un type'],
            ['name' => 'DELETE_FILE_TYPE', 'category' => 'Type de fichier', 'description' => 'Supprimer un type'],

            // Contract types
            ['name' => 'ACCESS_CONTRACT_TYPES', 'category' => 'Type de marché', 'description' => 'Accès au module'],
            ['name' => 'CREATE_CONTRACT_TYPE', 'category' => 'Type de marché', 'description' => 'Créer un type'],
            ['name' => 'READ_CONTRACT_TYPES', 'category' => 'Type de marché', 'description' => 'Voir les types'],
            ['name' => 'UPDATE_CONTRACT_TYPE', 'category' => 'Type de marché', 'description' => 'Modifier un type'],
            ['name' => 'DELETE_CONTRACT_TYPE', 'category' => 'Type de marché', 'description' => 'Supprimer un type'],

            // Procurement modes
            ['name' => 'ACCESS_PROCUREMENT_MODES', 'category' => 'Mode de sélection', 'description' => 'Accès au module'],
            ['name' => 'CREATE_PROCUREMENT_MODE', 'category' => 'Mode de sélection', 'description' => 'Créer un mode de sélection'],
            ['name' => 'READ_PROCUREMENT_MODES', 'category' => 'Mode de sélection', 'description' => 'Voir les modes de sélection'],
            ['name' => 'UPDATE_PROCUREMENT_MODE', 'category' => 'Mode de sélection', 'description' => 'Modifier un mode de sélection'],
            ['name' => 'DELETE_PROCUREMENT_MODE', 'category' => 'Mode de sélection', 'description' => 'Supprimer un mode de sélection'],

            // Project owners
            ['name' => 'ACCESS_PROJECT_OWNERS', 'category' => 'Maître d\'ouvrage', 'description' => 'Accès au module'],
            ['name' => 'CREATE_PROJECT_OWNER', 'category' => 'Maître d\'ouvrage', 'description' => 'Créer un maître d\'ouvrage'],
            ['name' => 'READ_PROJECT_OWNERS', 'category' => 'Maître d\'ouvrage', 'description' => 'Voir les maîtres d\'ouvrage'],
            ['name' => 'UPDATE_PROJECT_OWNER', 'category' => 'Maître d\'ouvrage', 'description' => 'Modifier un maître d\'ouvrage'],
            ['name' => 'DELETE_PROJECT_OWNER', 'category' => 'Maître d\'ouvrage', 'description' => 'Supprimer un maître d\'ouvrage'],

            // Delegated project owners
            ['name' => 'ACCESS_DELEGATED_PROJECT_OWNERS', 'category' => 'Maître d\'ouvrage délégué', 'description' => 'Accès au module'],
            ['name' => 'CREATE_DELEGATED_PROJECT_OWNER', 'category' => 'Maître d\'ouvrage délégué', 'description' => 'Créer un maître d\'ouvrage délégué'],
            ['name' => 'READ_DELEGATED_PROJECT_OWNERS', 'category' => 'Maître d\'ouvrage délégué', 'description' => 'Voir les maîtres d\'ouvrage délégués'],
            ['name' => 'UPDATE_DELEGATED_PROJECT_OWNER', 'category' => 'Maître d\'ouvrage délégué', 'description' => 'Modifier un maître d\'ouvrage délégué'],
            ['name' => 'DELETE_DELEGATED_PROJECT_OWNER', 'category' => 'Maître d\'ouvrage délégué', 'description' => 'Supprimer un maître d\'ouvrage délégué'],

            // Funding sources
            ['name' => 'ACCESS_FUNDING_SOURCES', 'category' => 'Source de financement', 'description' => 'Accès au module'],
            ['name' => 'CREATE_FUNDING_SOURCE', 'category' => 'Source de financement', 'description' => 'Créer une source'],
            ['name' => 'READ_FUNDING_SOURCES', 'category' => 'Source de financement', 'description' => 'Voir les sources'],
            ['name' => 'UPDATE_FUNDING_SOURCE', 'category' => 'Source de financement', 'description' => 'Modifier une source'],
            ['name' => 'DELETE_FUNDING_SOURCE', 'category' => 'Source de financement', 'description' => 'Supprimer une source'],

            // Regions
            ['name' => 'ACCESS_REGIONS', 'category' => 'Région', 'description' => 'Accès au module'],
            ['name' => 'CREATE_REGION', 'category' => 'Région', 'description' => 'Créer une région'],
            ['name' => 'READ_REGIONS', 'category' => 'Région', 'description' => 'Voir les régions'],
            ['name' => 'UPDATE_REGION', 'category' => 'Région', 'description' => 'Modifier une région'],
            ['name' => 'DELETE_REGION', 'category' => 'Région', 'description' => 'Supprimer une région'],

            // Departments
            ['name' => 'ACCESS_DEPARTMENTS', 'category' => 'Département', 'description' => 'Accès au module'],
            ['name' => 'CREATE_DEPARTMENT', 'category' => 'Département', 'description' => 'Créer un département'],
            ['name' => 'READ_DEPARTMENTS', 'category' => 'Département', 'description' => 'Voir les départements'],
            ['name' => 'UPDATE_DEPARTMENT', 'category' => 'Département', 'description' => 'Modifier un département'],
            ['name' => 'DELETE_DEPARTMENT', 'category' => 'Département', 'description' => 'Supprimer un département'],

            // Municipalities
            ['name' => 'ACCESS_MUNICIPALITIES', 'category' => 'Commune', 'description' => 'Accès au module'],
            ['name' => 'CREATE_MUNICIPALITY', 'category' => 'Commune', 'description' => 'Créer une commune'],
            ['name' => 'READ_MUNICIPALITIES', 'category' => 'Commune', 'description' => 'Voir les communes'],
            ['name' => 'UPDATE_MUNICIPALITY', 'category' => 'Commune', 'description' => 'Modifier une commune'],
            ['name' => 'DELETE_MUNICIPALITY', 'category' => 'Commune', 'description' => 'Supprimer une commune'],

            // Beneficiaries
            ['name' => 'ACCESS_BENEFICIARIES', 'category' => 'Bénéficiaire', 'description' => 'Accès au module'],
            ['name' => 'CREATE_BENEFICIARY', 'category' => 'Bénéficiaire', 'description' => 'Créer un bénéficiaire'],
            ['name' => 'READ_BENEFICIARIES', 'category' => 'Bénéficiaire', 'description' => 'Voir les bénéficiaires'],
            ['name' => 'UPDATE_BENEFICIARY', 'category' => 'Bénéficiaire', 'description' => 'Modifier un bénéficiaire'],
            ['name' => 'DELETE_BENEFICIARY', 'category' => 'Bénéficiaire', 'description' => 'Supprimer un bénéficiaire'],

            // Payment modes
            ['name' => 'ACCESS_PAYMENT_MODES', 'category' => 'Mode de paiement', 'description' => 'Accès au module'],
            ['name' => 'CREATE_PAYMENT_MODE', 'category' => 'Mode de paiement', 'description' => 'Créer un mode de paiement'],
            ['name' => 'READ_PAYMENT_MODES', 'category' => 'Mode de paiement', 'description' => 'Voir les modes de paiement'],
            ['name' => 'UPDATE_PAYMENT_MODE', 'category' => 'Mode de paiement', 'description' => 'Modifier un mode de paiement'],
            ['name' => 'DELETE_PAYMENT_MODE', 'category' => 'Mode de paiement', 'description' => 'Supprimer un mode de paiement'],

            // Budget types
            ['name' => 'ACCESS_BUDGET_TYPES', 'category' => 'Type de budgets', 'description' => 'Accès au module'],
            ['name' => 'CREATE_BUDGET_TYPE', 'category' => 'Type de budgets', 'description' => 'Créer un type de budget'],
            ['name' => 'READ_BUDGET_TYPES', 'category' => 'Type de budgets', 'description' => 'Voir les types de budgets'],
            ['name' => 'UPDATE_BUDGET_TYPE', 'category' => 'Type de budgets', 'description' => 'Modifier un type de budget'],
            ['name' => 'DELETE_BUDGET_TYPE', 'category' => 'Type de budgets', 'description' => 'Supprimer un type de budget'],

            // Expense types
            ['name' => 'ACCESS_EXPENSE_TYPES', 'category' => 'Type de dépenses', 'description' => 'Accès au module'],
            ['name' => 'CREATE_EXPENSE_TYPE', 'category' => 'Type de dépenses', 'description' => 'Créer un type de dépense'],
            ['name' => 'READ_EXPENSE_TYPES', 'category' => 'Type de dépenses', 'description' => 'Voir les types de dépenses'],
            ['name' => 'UPDATE_EXPENSE_TYPE', 'category' => 'Type de dépenses', 'description' => 'Modifier un type de dépense'],
            ['name' => 'DELETE_EXPENSE_TYPE', 'category' => 'Type de dépenses', 'description' => 'Supprimer un type de dépense'],

            // Indicator categories
            ['name' => 'ACCESS_INDICATOR_CATEGORIES', 'category' => 'Catégorie d\'indicateur', 'description' => 'Accès au module'],
            ['name' => 'CREATE_INDICATOR_CATEGORY', 'category' => 'Catégorie d\'indicateur', 'description' => 'Créer une catégorie'],
            ['name' => 'READ_INDICATOR_CATEGORIES', 'category' => 'Catégorie d\'indicateur', 'description' => 'Voir les catégories'],
            ['name' => 'UPDATE_INDICATOR_CATEGORY', 'category' => 'Catégorie d\'indicateur', 'description' => 'Modifier une catégorie'],
            ['name' => 'DELETE_INDICATOR_CATEGORY', 'category' => 'Catégorie d\'indicateur', 'description' => 'Supprimer une catégorie'],

            // Users
            ['name' => 'ACCESS_USERS', 'category' => 'Utilisateur', 'description' => 'Accès au module'],
            ['name' => 'CREATE_USER', 'category' => 'Utilisateur', 'description' => 'Créer un utilisateur'],
            ['name' => 'READ_USERS', 'category' => 'Utilisateur', 'description' => 'Voir les utilisateurs'],
            ['name' => 'UPDATE_USER', 'category' => 'Utilisateur', 'description' => 'Modifier un utilisateur'],
            ['name' => 'DELETE_USER', 'category' => 'Utilisateur', 'description' => 'Supprimer un utilisateur'],

            // Roles and permissions
            ['name' => 'ACCESS_ROLES', 'category' => 'Rôle et permission', 'description' => 'Accès au module'],
            ['name' => 'CREATE_ROLE', 'category' => 'Rôle et permission', 'description' => 'Créer un rôle'],
            ['name' => 'READ_ROLES', 'category' => 'Rôle et permission', 'description' => 'Voir les rôles'],
            ['name' => 'UPDATE_ROLE', 'category' => 'Rôle et permission', 'description' => 'Modifier un rôle'],
            ['name' => 'DELETE_ROLE', 'category' => 'Rôle et permission', 'description' => 'Supprimer un rôle'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm['name']],
                [
                    'category' => $perm['category'],
                    'description' => $perm['description']
                ]
            );
        }

        $admin = Role::create([
            'name' => 'Administrateur',
        ]);

        $admin->permissions()->sync(Permission::pluck('id')->toArray());

        $simpleUser = Role::create([
            'name' => 'Utilisateur simple',
        ]);

        $allowedPermissions = Permission::whereNotIn('category', [
            'Devises',
            'Phases par défaut',
            'Types de fichiers',
            'Types de marchés',
            'Modes de sélection',
            'Maîtres d\'ouvrage',
            'Maîtres d\'ouvrage délégués',
            'Sources de financement',
            'Régions',
            'Départements',
            'Communes',
            'Bénéficiaires',
            'Modes de paiement',
            'Types de budgets',
            'Types de dépenses',
            'Catégories d\'indicateurs',
            'Utilisateurs',
            'Rôles et permissions',
        ])->pluck('id')->toArray();

        $simpleUser->permissions()->sync($allowedPermissions);
    }
}
