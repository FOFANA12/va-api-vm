<?php

return [
    'controller' => [
        'message_success_created' => 'La tâche a été créée avec succès.',
        'message_success_updated' => 'La tâche a été mise à jour avec succès.',
        'message_task_marked_as_completed' => 'La tâche a été marquée comme réalisée.',
        'message_task_marked_as_in_progress' => 'La tâche a été marquée comme en cours.',
    ],
    'request' => [
        'title' => 'titre',
        'description' => 'déscription',
        'priority' => 'priorité',
        'start_date' => 'date début',
        'end_date' => 'date fin',
        'assigned_to' => 'utilisateur assigné',
        'deliverable' => 'livrable attendu',
        'invalid_date_range' => 'Les dates de la tâche doivent être comprises entre :start et :end.',
    ],
];
