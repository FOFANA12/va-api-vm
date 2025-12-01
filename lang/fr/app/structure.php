<?php

return [
    'controller' => [
        'message_success_created' => 'La structure a été créée avec succès.',
        'message_success_updated' => 'La structure a été mise à jour avec succès.',
    ],
    'request' => [
        'name' => 'nom de la structure',
        'type' => 'type de structure',
        'abbreviation' => 'abréviation',
        'parent' => 'structure parente',

        'parent_required' => 'Une structure de ce type doit avoir une structure parente.',
        'state_no_parent' => 'Une structure de type STATE ne peut pas avoir une structure parente.',
        'parent_not_found' => 'La structure parente sélectionnée est introuvable.',
        'invalid_parent_type' => 'Type de parent invalide : une structure de type :child doit être rattachée à une structure de type :expected (actuellement :given).',
    ],
];
