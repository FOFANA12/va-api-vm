<?php

declare(strict_types=1);

return [

    'controller' => [
        'message_success_created' => 'Le projet a été créé avec succès.',
        'message_success_updated' => 'Le projet a été modifié avec succès.',
        'message_success_status_updated' => 'Le statut du projet a été mis à jour avec succès.',
        'message_success_state_updated' => 'L\'état du projet a été mis à jour avec succès.',
    ],

    'request' => [
        'program' => 'programme',
        'name' => 'nom',
        'description' => 'description et objectifs',
        'prerequisites' => 'conditions préalables',
        'impacts' => 'impact attendu',
        'risks' => 'risques identifiés',
        'start_date' => 'date de début',
        'end_date' => 'date de fin',
        'currency' => 'devise',
        'status' => 'statut du projet',
        'state' => 'état du projet',
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
