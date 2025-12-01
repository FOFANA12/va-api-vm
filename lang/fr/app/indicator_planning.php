<?php

return [
    'controller' => [
        'message_success_updated' => 'La planification a été mise à jour avec succès.',
    ],
    'request' => [
        'frequency_unit' => 'unité de fréquence',
        'frequency_value' => 'valeur de fréquence',
        'periods' => [
            'title' => 'périodes de contrôle',
            'start_date' => 'date de début de la période',
            'end_date' => 'date de fin de la période',
            'target_value' => 'valeur cible',
        ],
    ],
    'plannings_error' => [
        'required' => 'vous devez avoir des périodes dans votre planification',
        'first_target_gt_initial' => 'Désolé, cette planification est invalide. La première valeur cible doit être supérieure à la valeur initiale (:value).',
        'start_date_outside' => 'La date de début doit être comprise entre :min et :max.',
        'end_date_outside' => 'La date de fin doit être comprise entre :min et :max.',
        'targets_increasing' => 'Les valeurs cibles doivent être strictement croissantes et sans doublons.',
        'last_target_must_equal' => 'La dernière valeur cible doit être égale à :value.',
    ],

];
