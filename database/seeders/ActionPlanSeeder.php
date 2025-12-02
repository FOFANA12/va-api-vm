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
        $operational = Structure::where('type', 'OPERATIONAL')->first();

        ActionPlan::create([
            'structure_uuid' => $operational->uuid,
            'responsible_uuid' => null,
            'name' => 'Plan d\'action stratégique 2025',
            'reference' => 'PA-STRAT-2025',
            'description' => 'Plan d\'action opérationnel prioritaire pour l\'année 2025.',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'status' => true,
            'created_by' => null,
            'updated_by' => null,
        ]);
    }
}
