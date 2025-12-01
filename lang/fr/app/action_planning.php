<?php

return [
    'controller' => [
        'message_success_updated' => 'La planification a été mise à jour avec succès.',
    ],
    'request' => [
        'start_date' => 'date de début',
        'end_date' => 'date de fin',
        'frequency_unit' => 'unité de fréquence',
        'frequency_value' => 'valeur de fréquence',
        'periods' => [
            'title' => 'périodes de contrôle',
            'start_date' => 'date de début de la période',
            'end_date' => 'date de fin de la période',
            'progress_percent' => 'taux de progression prévu',
        ],
    ],
    'plannings_error' => [
        'required' => 'vous devez avoir des périodes dans votre planification',
        'not_strictly_increasing' => 'Désolé, cette planification est invalide. Les prévisions doivent être strictement croissantes et sans doublon.',
        'last_not_100' => 'Désolé, cette planification est invalide. La dernière prévision doit être à 100 %.',
    ],
];
