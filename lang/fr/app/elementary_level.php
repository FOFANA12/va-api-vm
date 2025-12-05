<?php

declare(strict_types=1);

return [

    'controller' => [
        'message_success_created' => 'Le niveau élémentaire a été créé avec succès.',
        'message_success_updated' => 'Le niveau élémentaire a été modifié avec succès.',
        'message_success_status_updated' => 'Le statut du niveau élémentaire a été mis à jour avec succès.',
        'message_success_state_updated' => 'L\'état du niveau élémentaire a été mis à jour avec succès.',
    ],

    'request' => [
        'capability_domain' => 'domaine capacitaire',
        'name' => 'nom',
        'description' => 'description et objectifs',
        'prerequisites' => 'conditions préalables',
        'impacts' => 'impact attendu',
        'risks' => 'risques identifiés',
        'start_date' => 'date de début',
        'end_date' => 'date de fin',
        'currency' => 'devise',
        'status' => 'statut de l\'activité',
        'state' => 'état de l\'activité',
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
