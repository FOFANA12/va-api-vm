<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Beneficiary;
use App\Models\FundingSource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            [
                'reference' => 'ACT-001',
                'name' => 'Formation des agents',
                'start_date' => '2025-01-15',
                'end_date' => '2025-01-30',
                'status' => 'preparation',
            ],
            [
                'reference' => 'ACT-002',
                'name' => 'Campagne de sensibilisation',
                'start_date' => '2025-03-01',
                'end_date' => '2025-03-15',
                'status' => 'preparation',
            ],
            [
                'reference' => 'ACT-003',
                'name' => 'Distribution de kits',
                'start_date' => '2025-05-01',
                'end_date' => '2025-06-01',
                'status' => 'preparation',
            ],
        ];

        foreach ($activities as $data) {
            $beneficiaries = Beneficiary::query()->inRandomOrder()->limit(rand(2, 3))->get();

            $fundingSources = FundingSource::query()->inRandomOrder()->limit(rand(1, 3))->get();

            $fundingData = $fundingSources->map(function ($source) {
                return [
                    'funding_source_uuid' => $source->uuid,
                    'planned_budget' => fake()->numberBetween(50000, 250000),
                ];
            });

            $totalBudget = $fundingData->sum('planned_budget');



            $activity = Activity::create([
                'project_uuid' => Project::query()->inRandomOrder()->value('uuid'),
                'reference' => $data['reference'],
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'budget' => $totalBudget,
                'currency' => 'MRU',
                'status' => $data['status'],
                'responsible_uuid' => User::query()->inRandomOrder()->value('uuid'),
            ]);

            if ($beneficiaries->isNotEmpty()) {
                $activity->beneficiaries()->attach($beneficiaries->pluck('uuid'));
            }

            foreach ($fundingData as $funding) {
                $activity->fundingSources()->attach($funding['funding_source_uuid'], [
                    'planned_budget' => $funding['planned_budget'],
                ]);
            }
        }
    }
}
