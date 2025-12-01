<?php

namespace Database\Seeders;

use App\Models\ActionPhase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Task::whereNotNull('id')->delete();

        $phase = ActionPhase::first();
        $user = User::first();

        $tasks = [
            [
                'title' => 'Préparer le cahier des charges',
                'description' => 'Rédaction des besoins et objectifs du projet.',
                'priority' => 'high',
                'start_date' => now()->addDays(1),
                'end_date' => now()->addDays(7),
            ],
            [
                'title' => 'Valider le budget',
                'description' => 'Réunion de validation avec l\'équipe financière.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'start_date' => now(),
                'end_date' => now()->addDays(5),
            ],
            [
                'title' => 'Lancer le développement',
                'description' => 'Début du sprint 1 de l\'équipe technique.',
                'status' => 'not_started',
                'priority' => 'high',
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(30),
            ],
        ];

        foreach ($tasks as $task) {
            Task::create([
                'phase_uuid' => $phase?->uuid,
                'title' => $task['title'],
                'description' => $task['description'],
                'priority' => $task['priority'],
                'start_date' => $task['start_date'],
                'end_date' => $task['end_date'],
                'assigned_to' => $user?->uuid,
                'deliverable' => null,
                'created_by' => $user?->uuid,
                'updated_by' => $user?->uuid,
            ]);
        }
    }
}
