<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Decision;
use App\Models\StrategicObjective;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DecisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $objective = StrategicObjective::first();
        $action = Action::first();

        $decisions = [
            [
                'reference' => 'MEFP_AXER _OBJ215_DECIS19',
                'title' => 'Validation de l’objectif stratégique',
                'description' => 'Décision concernant l’avancement du projet stratégique.',
                'decidable_type' => StrategicObjective::class,
                'decidable_id' => $objective?->id,
                'status' => 'announced',
                'priority' => 'high',
            ],
            [
                'reference' => 'MEFP_Procapec_ACT920_DECIS12',
                'title' => 'Lancement du projet BTP Sahel',
                'description' => 'Décision pour le démarrage du projet BTP Sahel.',
                'decidable_type' => Action::class,
                'decidable_id' => $action?->id,
                'status' => 'announced',
                'priority' => 'high',
            ],
        ];

        foreach ($decisions as $decision) {
            Decision::create([
                'reference' => $decision['reference'],
                'title' => $decision['title'],
                'description' => $decision['description'],
                'decidable_type' => $decision['decidable_type'],
                'decidable_id' => $decision['decidable_id'],
                'decision_date' => now(),
                'status' => $decision['status'],
                'priority' => $decision['priority'],
            ]);
        }
    }
}
