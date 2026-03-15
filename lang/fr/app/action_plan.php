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
    'import' => [
        'success' => 'L\'importation des plans d\'action a été effectuée avec succès.',
        'failed'  => 'L\'importation des plans d\'action a échoué.',

        'file_empty' => 'Le fichier est vide ou invalide.',
        'unexpected_error' => 'Une erreur inattendue est survenue lors de l\'importation des plans d\'action.',

        'structure_missing' => 'Votre compte n\'est rattaché à aucune structure.',
        'structure_not_operational' => 'Votre structure n\'est pas une structure opérationnelle autorisée pour cet import.',

        'responsible_not_found' => 'Responsable introuvable ou non rattaché à la structure (:email).',
        'already_exists' => 'Le plan d\'action ":name" existe déjà pour la structure ":structure".',
    ],
    'export' => [
        'sheet_name' => 'Plans d\'action',
        'filename_all' => 'export-plans-action.xlsx',

        'structure_abbreviation' => 'Abbréviation structure',
        'structure_label' => 'Nom structure',
        'reference' => 'Référence',
        'name' => 'Nom du plan d\'action',
        'description' => 'Description',
        'responsible' => 'Responsable',
        'start_date' => 'Date de début',
        'end_date' => 'Date de fin',
        'status' => 'Statut',
        'created_at' => 'Créé le',
        'updated_at' => 'Modifié le',
    ],

];
