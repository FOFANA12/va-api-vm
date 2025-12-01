<?php

return [
    'controller' => [
        'message_success_created' => 'La période a été créée avec succès.',
        'message_success_updated' => 'La période a été mise à jour avec succès.',
        'message_success_objectives_attached' => 'Les objectifs ont été associés à la période avec succès.',
        'message_success_objectives_detached' => "L'objectif a été détaché de la période avec succès.",
    ],
    'repository' => [
        'objective_not_found_or_not_attached' => "L'objectif spécifié est introuvable ou n'est pas rattaché à cette matrice.",
    ],
    'request' => [
        'start_date' => 'date de début',
        'end_date' => 'date de fin',
        'invalid_date_range' => 'La période doit être comprise entre :start et :end, correspondant à la période de la carte stratégique.',
    ],
];
