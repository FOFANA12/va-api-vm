<?php

return [
    'controller' => [
        'message_success_created' => 'L\'action a été créée avec succès.',
        'message_success_updated' => 'L\'action a été mise à jour avec succès.',
        'message_success_status_updated' => 'Le statut a été mis à jour avec succès.'
    ],
    'request' => [
        'name' => 'nom de l\'action',
        'priority' => 'niveau de priorité',
        'risk_level' => 'niveau de risque',
        'generate_document_type' => 'type de document généré',
        'status' => 'statut de l\'action',

        'structure' => 'structure concernée',
        'action_plan' => 'plan d\'action',
        'project_owner' => 'maître d\'ouvrage',
        'delegated_project_owner' => 'maître d\'ouvrage délégué',
        'currency' => 'devise',

        'program' => 'programme',
        'project' => 'projet',
        'activity' => 'activité',
        'region' => 'région',
        'department' => 'département',
        'municipality' => 'commune',
        'chart_type' => 'type de graphique',

        'responsible_structure' => 'structure responsable',
        'responsible' => 'responsable',

        'description' => 'description',
        'prerequisites' => 'conditions préalables',
        'impacts' => 'impacts attendus',
        'risks' => 'risques identifiés',
        "funding_sources" => [
            'title' => 'sources de financement',
            'uuid' => 'identifiant',
            'planned_amount' => 'montant',
        ],
        "invalid_status" => "Le statut fourni est invalide.",
    ],
    'import' => [
        'success' => 'L’importation des actions a été effectuée avec succès.',
        'failed'  => 'L’importation des actions a échoué.',

        'file_empty' => 'Le fichier est vide ou invalide.',
        'unexpected_error' => 'Une erreur inattendue est survenue lors de l\'importation des actions.',

        'structure_not_found' => 'Structure introuvable (:abbreviation).',
        'responsible_structure_invalid' => 'La structure responsable est invalide (:abbreviation) ou n\'est pas un enfant de la structure.',
        'responsible_structure_required' => 'La structure responsable est obligatoire lorsque le responsable est renseigné.',
        'responsible_not_found' => 'Responsable introuvable ou non rattaché à la structure responsable (:abbreviation).',

        'action_plan_not_found' => 'Le plan d\'action ":name" est introuvable pour la structure ":structure".',

        'priority_invalid' => 'La priorité ":value" est invalide.',
        'risk_level_invalid' => 'Le niveau de risque ":value" est invalide.',
        'plan_type_invalid' => 'Le type de plan ":value" est invalide.',
        'chart_type_invalid' => 'Le type de graphique ":value" est invalide.',

        'project_owner_not_found' => 'Maître d’ouvrage ":name" introuvable.',
        'delegated_project_owner_not_found' => 'Maître d’ouvrage délégué ":name" introuvable pour le maître d’ouvrage.',

        'action_domain_not_found' => 'Domaine d’action introuvable.',
        'action_domain_required' => 'Le domaine d’action est requis pour définir un domaine stratégique.',

        'strategic_domain_not_found' => 'Domaine stratégique introuvable.',
        'strategic_domain_required' => 'Le domaine stratégique est requis pour définir un domaine capacitaire.',

        'capability_domain_not_found' => 'Domaine capacitaire introuvable.',
        'capability_domain_required' => 'Le domaine capacitaire est requis pour définir un niveau élémentaire.',

        'elementary_level_not_found' => 'Niveau élémentaire introuvable.',

        'region_not_found' => 'Région introuvable.',
        'region_required' => 'La région est obligatoire pour définir un département.',

        'department_not_found' => 'Département introuvable.',
        'department_required' => 'Le département est obligatoire pour définir une commune.',

        'municipality_not_found' => 'Commune introuvable.',
    ],
];
