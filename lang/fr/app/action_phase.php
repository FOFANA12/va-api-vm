<?php

return [
    'controller' => [
        'message_success_created' => 'La phase a été créée avec succès.',
        'message_success_updated' => 'La phase a été mise à jour avec succès.',
        'message_success_initialized' => 'Les phases par défaut ont été ajoutées avec succès à cette action.',
    ],
    'request' => [
        'name' => 'nom de la phase',
        'start_date' => 'date de début',
        'end_date' => 'date de fin',
        'weight' => 'poids',
        'number' => 'numéro',
        'description' => 'déscription',
        'deliverable' => 'livrable attendu',
    ],
    'errors' => [
        'action_not_planned' => 'L\'action doit être planifiée avant d\'ajouter des phases.',
        'out_of_bounds_start' => 'La date de début doit être dans la période de l\'action.',
        'out_of_bounds_end' => 'La date de fin doit être dans la période de l\'action.',
        'weight_overflow' => 'Le poids total ne doit pas dépasser 1. Restant : :remaining.',
    ],
];
