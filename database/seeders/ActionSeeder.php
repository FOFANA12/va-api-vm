<?php

namespace Database\Seeders;

use App\Helpers\ReferenceGenerator;
use App\Models\Action;
use App\Models\ActionPhase;
use App\Models\Structure;
use App\Models\ActionPlan;
use App\Models\ContractType;
use App\Models\ProcurementMode;
use App\Models\ProjectOwner;
use App\Models\DelegatedProjectOwner;
use App\Models\Region;
use App\Models\Department;
use App\Models\Municipality;
use App\Models\Program;
use App\Models\Project;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ActionSeeder extends Seeder
{
    /**     * Run the database seeds.     */
    public function run(): void
    {

        $department = Structure::where('type', 'DEPARTMENT')->first();
        $structure = $department->children()->first();

        $actionPlan = ActionPlan::first();
        $projectOwner = ProjectOwner::first();
        $delegatedOwner = DelegatedProjectOwner::first();
        $region = Region::first();
        $department = Department::first();
        $municipality = Municipality::first();
        $program = Program::first();
        $project = Project::first();
        $activity = Activity::first();
        $user = User::first();
        $actions = [
            [
                'name' => 'Réhabilitation d\'écoles primaires',
                'priority' => 'high',
                'risk_level' => 'medium',
                'description' => 'Réhabilitation de 10 écoles en milieu rural.',
                'prerequisites' => 'Étude technique préalable',
                'impacts' => 'Amélioration des conditions d\'apprentissage',
                'risks' => 'Manque de financement, retard de livraison',
                'generate_document_type' => 'ppm',
                'status' => 'in_progress',
                'start_date' => '2025-01-10',
                'end_date' => '2025-06-30',
                'total_budget' => 150000.00,
                'frequency_unit' => 'monthly',
                'frequency_value' => 1,
            ],
            [
                'name' => 'Construction d\'un centre de santé',
                'priority' => 'medium',
                'risk_level' => 'high',
                'description' => 'Construction d\'un nouveau centre de santé dans la commune A.',
                'prerequisites' => 'Validation du site par les autorités locales',
                'impacts' => 'Renforcement du système de santé local',
                'risks' => 'Opposition communautaire, intempéries',
                'generate_document_type' => 'paa',
                'status' => 'in_progress',
                'start_date' => '2025-03-01',
                'end_date' => '2025-09-01',
                'total_budget' => 300000.00,
                'frequency_unit' => 'quarterly',
                'frequency_value' => 3,
            ],
        ];

        $coefficientValues = [
            0.05,
            0.10,
            0.15,
            0.20,
            0.25,
            0.30,
            0.35,
            0.40,
            0.45,
            0.50,
            0.55,
            0.60,
            0.65,
            0.70,
            0.75,
            0.80,
            0.85,
            0.90,
            0.95,
            1.00,
        ];


        foreach ($actions as $data) {
            $action =  Action::create([
                'structure_uuid' => $structure?->uuid,
                'action_plan_uuid' => $actionPlan?->uuid,
                'project_owner_uuid' => $projectOwner?->uuid,
                'delegated_project_owner_uuid' => $delegatedOwner?->uuid,
                'region_uuid' => $region?->uuid,
                'department_uuid' => $department?->uuid,
                'municipality_uuid' => $municipality?->uuid,
                'program_uuid' => $program?->uuid,
                'project_uuid' => $project?->uuid,
                'activity_uuid' => $activity?->uuid,
                'name' => $data['name'],
                'priority' => $data['priority'],
                'risk_level' => $data['risk_level'],
                'description' => $data['description'],
                'prerequisites' => $data['prerequisites'],
                'impacts' => $data['impacts'],
                'risks' => $data['risks'],
                'generate_document_type' => $data['generate_document_type'],
                'status' => $data['status'],
                'status_changed_at' => now(),
                'status_changed_by' => $user?->uuid,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'total_budget' => $data['total_budget'],
                'frequency_unit' => $data['frequency_unit'],
                'frequency_value' => $data['frequency_value'],
            ]);

            $action->update([
                'reference' => ReferenceGenerator::generateActionReference($action->id, $structure->abbreviation),
            ]);

            $start = Carbon::parse($data['start_date']);
            $end = Carbon::parse($data['end_date']);
            $totalDays = $start->diffInDays($end);
            $chunk = floor($totalDays / 5);

            $weights = [];
            $remaining = 1.0;

            for ($i = 1; $i <= 5; $i++) {
                if ($i < 5) {
                    $weight = $coefficientValues[array_rand($coefficientValues)];
                    if ($weight > $remaining) {
                        $weight = $remaining;
                    }
                    $weights[] = $weight;
                    $remaining -= $weight;
                } else {
                    $weights[] = $remaining;
                }
            }

            for ($i = 1; $i <= 5; $i++) {
                $phaseStart = (clone $start)->addDays(($i - 1) * $chunk);
                $phaseEnd = $i < 5
                    ? (clone $start)->addDays($i * $chunk - 1)
                    : $end;

                ActionPhase::create([
                    'action_uuid' => $action->uuid,
                    'name' => "Phase $i",
                    'number' => $i,
                    'start_date' => $phaseStart->toDateString(),
                    'end_date' => $phaseEnd->toDateString(),
                    'weight' => round($weights[$i - 1], 2),
                    'description' => "Description de la phase $i — activités principales, livrables et jalons associés.",
                    'deliverable' => "Livrable attendu : rapport de suivi de la phase $i.",
                    'created_by' => $user?->uuid,
                ]);
            }
        }
    }
}
