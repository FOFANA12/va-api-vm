<?php

return [
    'controller' => [
        'message_success_created' => 'Le contrôle a été enregistré avec succès.',
    ],
    'request' => [
        'root_cause' => 'cause racine',
        'control_date' => 'date du contrôle',
        'items' => [
            'title' => 'items de contrôle',
            'action_phase' => 'phase concernée',
            'progress_percent' => 'progession (%)',
        ],
    ],
    'controls_error' => [
        'required' => 'Vous devez fournir au moins un élément de contrôle.',
        'period_not_found' => 'La période de contrôle sélectionnée est introuvable.',
        'action_not_in_progress' => 'L\'action doit être en cours de réalisation pour pouvoir être contrôlée.',
        'period_already_controlled' => 'Cette période a déjà fait l\'objet d\'un contrôle.',
        'not_eligible' => 'Cette action n\'est pas éligible au contrôle pour le moment.',
        'previous_not_controlled' => 'Vous devez d\'abord effectuer le contrôle des périodes précédentes (progression plus faible).',
    ],
];
