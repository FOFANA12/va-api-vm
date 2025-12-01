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
        'contract_type' => 'type de marché',
        'procurement_mode' => 'mode de passation',
        'project_owner' => 'maître d\'ouvrage',
        'delegated_project_owner' => 'maître d\'ouvrage délégué',
        'currency' => 'devise',

        'program' => 'programme',
        'project' => 'projet',
        'activity' => 'activité',
        'region' => 'région',
        'department' => 'département',
        'municipality' => 'commune',

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
];
