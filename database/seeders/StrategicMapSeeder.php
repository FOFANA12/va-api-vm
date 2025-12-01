<?php

namespace Database\Seeders;

use App\Models\Structure;
use App\Models\StrategicMap;
use Illuminate\Database\Seeder;

class StrategicMapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StrategicMap::whereNotNull('id')->delete();

        $stateStructure = Structure::where('type', 'STATE')->first();
        $departmentStructure = Structure::where('type', 'DEPARTMENT')->first();

        $strategicMaps = [
            [
                'name' => "Carte stratégique nationale 2025",
                'description' => "Feuille de route de développement à l'échelle de l'État.",
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'status' => true,
                'structure' => $stateStructure,
            ],
            [
                'name' => "Carte stratégique de développement 2025",
                'description' => "Feuille de route pour le développement global de la structure.",
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'status' => true,
                'structure' => $departmentStructure,
            ],
            [
                'name' => "Carte stratégique digitale",
                'description' => "Plan d'action pour la digitalisation et l'innovation technologique.",
                'start_date' => '2025-03-01',
                'end_date' => '2026-02-28',
                'structure' => $departmentStructure,
            ],
            [
                'name' => "Carte stratégique de performance",
                'description' => "Suivi et évaluation des objectifs de performance annuelle.",
                'start_date' => '2025-04-01',
                'end_date' => '2026-03-31',
                'structure' => $departmentStructure,
            ],
        ];

        foreach ($strategicMaps as $map) {
            StrategicMap::create([
                'name' => $map['name'],
                'description' => $map['description'],
                'start_date' => $map['start_date'],
                'end_date' => $map['end_date'],
                'status' => $map['status'] ?? false,
                'structure_uuid' => $map['structure']->uuid,
                'created_by' => null,
                'updated_by' => null,
            ]);
        }
    }
}
