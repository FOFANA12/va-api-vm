<?php

namespace Database\Seeders;

use App\Models\DefaultPhase;
use Illuminate\Database\Seeder;

class DefaultPhaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPhases = [
            [
                'name' => 'Phase 1',
                'number' => 1,
                'duration' => 30,
                'weight' => 0.25,
            ],
            [
                'name' => 'Phase 2',
                'number' => 2,
                'duration' => 20,
                'weight' => 0.01,
            ],
        ];

        foreach ($defaultPhases as $index => $phase) {
            DefaultPhase::create(array_merge($phase, [
                'description' => "Description de la phase " . ($index + 1) . " — activités principales, livrables et jalons associés.",
                'deliverable' => "Livrable attendu : rapport de suivi de la phase " . ($index + 1) . ".",
            ]));
        }
    }
}
