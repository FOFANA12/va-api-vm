<?php

namespace Database\Seeders;

use App\Models\Structure;
use App\Models\StrategicAxis;
use App\Models\StrategicElement;
use App\Models\StrategicMap;
use App\Models\StrategicObjective;
use Illuminate\Database\Seeder;

class StrategicObjectiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $strategicElement = StrategicElement::where('type', 'AXIS')->first();
        $leadStructure = Structure::where('uuid', '!=', $strategicElement->strature_uuid)->first();

        $objectives = [
            [
                'reference' => 'OBJ-001',
                'name' => 'Renforcer les capacités institutionnelles',
                'description' => 'Améliorer la formation et le suivi des agents clés.',
                'start_date' => '2025-01-10',
                'end_date' => '2025-06-30',
                'priority' => 'medium',
                'risk_level' => 'low',
                'status' => 'declared',
            ],
            [
                'reference' => 'OBJ-002',
                'name' => 'Promouvoir la digitalisation',
                'description' => 'Mise en place d\'outils numériques pour optimiser les processus.',
                'start_date' => '2025-02-01',
                'end_date' => '2025-12-31',
                'priority' => 'medium',
                'risk_level' => 'low',
                'status' => 'declared',
            ],
            [
                'reference' => 'OBJ-003',
                'name' => 'Accroître la participation communautaire',
                'description' => 'Encourager la collaboration avec les associations locales.',
                'start_date' => '2025-03-15',
                'end_date' => '2025-09-15',
                'priority' => 'medium',
                'risk_level' => 'high',
                'status' => 'engaged',
            ],
        ];

        foreach ($objectives as $data) {
            StrategicObjective::create([
                'structure_uuid' => $strategicElement->structure_uuid,
                'strategic_map_uuid' => $strategicElement->strategic_map_uuid,
                'strategic_element_uuid' => $strategicElement->uuid,
                'lead_structure_uuid' => $leadStructure->uuid,
                'reference' => $data['reference'],
                'name' => $data['name'],
                'description' => $data['description'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'priority' => $data['priority'],
                'risk_level' => $data['risk_level'],
                'status' => $data['status'],
            ]);
        }
    }
}
