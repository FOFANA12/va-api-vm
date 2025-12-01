<?php

namespace Database\Seeders;

use App\Helpers\ReferenceGenerator;
use App\Models\Indicator;
use App\Models\IndicatorCategory;
use App\Models\StrategicObjective;
use Illuminate\Database\Seeder;

class IndicatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $strategicObjective = StrategicObjective::first();
        $category = IndicatorCategory::first();

        $indicators = [
            [
                'name' => 'Taux de scolarisation primaire',
                'description' => 'Pourcentage d\'enfants inscrits à l\'école primaire.',
                'chart_type' => 'BAR',
                'frequency_unit' => 'months',
                'frequency_value' => 1,
                'initial_value' => 65.75,
                'final_target_value' => 90.50,
                'achieved_value' => 0,
                'unit' => '%',
                'status' => 'in_progress',
            ],
            [
                'name' => 'Nombre de centres de santé fonctionnels',
                'description' => 'Indique le nombre total de centres opérationnels.',
                'chart_type' => 'LINE',
                'frequency_unit' => 'months',
                'frequency_value' => 3,
                'initial_value' => 50.25,
                'final_target_value' => 120.60,
                'achieved_value' => 0,
                'unit' => 'kilomètre',
                'status' => 'in_progress',
            ],
        ];

        foreach ($indicators as $data) {
            $indicator = Indicator::create([
                'structure_uuid' => $strategicObjective->structure_uuid,
                'strategic_map_uuid' => $strategicObjective->strategic_map_uuid,
                'strategic_element_uuid' => $strategicObjective->strategic_element_uuid,
                'strategic_objective_uuid' => $strategicObjective->uuid,
                'lead_structure_uuid' => $strategicObjective->lead_structure_uuid,
                'category_uuid' => $category?->uuid,
                'name' => $data['name'],
                'description' => $data['description'],
                'chart_type' => $data['chart_type'],
                'frequency_unit' => $data['frequency_unit'],
                'frequency_value' => $data['frequency_value'],
                'initial_value' => $data['initial_value'],
                'final_target_value' => $data['final_target_value'],
                'achieved_value' => $data['achieved_value'],
                'unit' => $data['unit'],
                'status' => $data['status'],
            ]);

            $indicator->update([
                'reference' => ReferenceGenerator::generateIndicatorReference($indicator->id, $strategicObjective->reference),
            ]);

            
        }
    }
}
