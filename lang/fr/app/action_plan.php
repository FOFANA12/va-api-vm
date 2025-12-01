<?php

declare(strict_types=1);

return [

    'controller' => [
        'message_success_created' => 'Le plan d\'action a été créé avec succès.',
        'message_success_duplicated' => 'Le plan d\'action a été dupliqué avec succès.',
        'message_success_updated' => 'Le plan d\'action a été mis à jour avec succès.',
    ],
    'request' => [
        'name' => 'nom du plan d\'action',
        'description' => 'description',
        'start_date' => 'date de début',
        'end_date' => 'date de fin',
        'structure' => 'structure concernée',
        'responsible' => 'responsable',
        'already_active_action_plan' => 'Un plan d\'actiion actif existe déjà pour cette structure.',
    ],
];
