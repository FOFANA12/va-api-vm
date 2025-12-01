<?php

declare(strict_types=1);

return [

    'controller' => [
        'message_success_created' => 'Le programme a été créé avec succès.',
        'message_success_updated' => 'Le programme a été modifié avec succès.',
        'message_success_status_updated' => 'Le statut du programme a été mis à jour avec succès.',
        'message_success_state_updated' => 'L\'état du programme a été mis à jour avec succès.',
    ],

    'request' => [
        'name' => 'nom',
        'description' => 'description et objectifs',
        'prerequisites' => 'conditions préalables',
        'impacts' => 'impact attendu',
        'risks' => 'risques identifiés',
        'start_date' => 'date de démarrage',
        'end_date' => 'date de clôture',
        'currency' => 'devise',
        'status' => 'statut du programme',
        'state' => 'état du programme',
        'responsible' => 'responsable',

        "funding_sources" => [
            'title' => 'sources de financement',
            'uuid' => 'identifiant',
            'planned_amount' => 'montant',
        ],

        "invalid_status" => "Le statut fourni est invalide.",
        "invalid_state" => "L\'état fourni est invalide.",
    ],

];
