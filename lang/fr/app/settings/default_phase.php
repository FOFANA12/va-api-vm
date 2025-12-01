<?php

return [
    'controller' => [
        'message_success_created' => 'La phase a été créée avec succès.',
        'message_success_updated' => 'La phase a été mise à jour avec succès.',
    ],
    'request' => [
        'name' => 'nom de la phase',
        'duration' => 'durée de la phase',
        'weight' => 'poids',
        'number' => 'numéro',
    ],
    'errors' => [
        'weight_overflow' => 'Le poids total ne doit pas dépasser 1. Restant : :remaining.',
    ],
];
