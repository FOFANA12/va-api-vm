<?php

namespace Database\Seeders;

use App\Models\ActionPlan;
use App\Models\Structure;
use Illuminate\Database\Seeder;

class ActionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ActionPlan::whereNotNull('id')->delete();
        $department = Structure::where('type', 'DEPARTMENT')->first();
        $childStructure = $department->children()->first();

        $plans = [
            [
                'name' => 'Plan d\'action pour la santé communautaire',
                'reference' => 'PA-SANTE-2025',
                'description' => 'Améliorer l\'accès aux soins de base dans les zones rurales',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'status' => true
            ],
            [
                'name' => 'Plan d\'action éducation numérique',
                'reference' => 'PA-EDUNUM-2025',
                'description' => 'Déployer des outils numériques dans 100 écoles publiques',
                'start_date' => '2025-03-01',
                'end_date' => '2025-11-30',
                'status' => false
            ],
            [
                'name' => 'Plan national d\'insertion des jeunes',
                'reference' => 'PA-JEUNES-2025',
                'description' => 'Favoriser l\'emploi des jeunes par la formation et le financement',
                'start_date' => '2025-02-15',
                'end_date' => '2025-10-15',
                'status' => false
            ],
        ];

        if ($childStructure) {
            foreach ($plans as $plan) {
                ActionPlan::create([
                    'structure_uuid' => $childStructure->uuid,
                    'responsible_uuid' => null,
                    'name' => $plan['name'],
                    'reference' => $plan['reference'],
                    'description' => $plan['description'],
                    'start_date' => $plan['start_date'],
                    'end_date' => $plan['end_date'],
                    'status' => $plan['status'],
                    'created_by' => null,
                    'updated_by' => null,
                ]);
            }
        }
    }
}
