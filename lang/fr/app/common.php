<?php

declare(strict_types=1);


return [
    'controller' => [
        'message_success_deleted' => 'L\'opération s\'est terminée avec succès.',
    ],
    'destroy' => [
        'invalid_ids' => 'Les identifiants fournis sont invalides.',
        'no_items_deleted' => 'Aucune donnée n\'a pu être supprimée.',
        'not_found' => 'Élément introuvable.',
    ],
    'request' => [
        'line_items_required' => 'Aucune ligne d\'article n\'a été trouvée. Ajoutez des articles pour continuer.',
        'line_number' => 'ligne :line',
    ],
    'repository' => [
        'foreignKey' => 'L\'action ne peut pas être effectuée car cet enregistrement est lié à d\'autres ressources.',
        'error' => 'Une erreur inattendue s’est produite lors du traitement de votre demande. Veuillez réessayer plus tard.',
    ],
];
