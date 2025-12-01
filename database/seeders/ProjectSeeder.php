<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use App\Models\FundingSource;
use App\Models\Program;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $projects = [
            [
                'reference' => 'PROJ-00A',
                'name' => 'Project A',
                'status' => 'preparation',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
            ],
            [
                'reference' => 'PROJ-00B',
                'name' => 'Project B',
                'status' => 'preparation',
                'start_date' => '2025-02-01',
                'end_date' => '2025-11-30',
            ],
            [
                'reference' => 'PROJ-00C',
                'name' => 'Project C',
                'status' => 'preparation',
                'start_date' => '2025-03-01',
                'end_date' => '2025-09-30',
            ],
        ];

        foreach ($projects as $data) {
            $beneficiaries = Beneficiary::query()->inRandomOrder()->limit(rand(2, 3))->get();

            $fundingSources = FundingSource::query()->inRandomOrder()->limit(rand(1, 3))->get();

            $fundingData = $fundingSources->map(function ($source) {
                return [
                    'funding_source_uuid' => $source->uuid,
                    'planned_budget' => fake()->numberBetween(50000, 250000),
                ];
            });

            $totalBudget = $fundingData->sum('planned_budget');


            $project = Project::create([
                'reference' => $data['reference'],
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'budget' => $totalBudget,
                'currency' => 'MRU',
                'responsible_uuid' => User::query()->inRandomOrder()->value('uuid'),
                'program_uuid' => Program::query()->inRandomOrder()->value('uuid'),
                'status' => $data['status'],
            ]);

            if ($beneficiaries->isNotEmpty()) {
                $project->beneficiaries()->attach($beneficiaries->pluck('uuid'));
            }

            foreach ($fundingData as $funding) {
                $project->fundingSources()->attach($funding['funding_source_uuid'], [
                    'planned_budget' => $funding['planned_budget'],
                ]);
            }
        }
    }
}
