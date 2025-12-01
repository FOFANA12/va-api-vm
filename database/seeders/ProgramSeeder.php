<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use App\Models\FundingSource;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $programs = [
            [
                'reference' => 'PRG-00A',
                'name' => 'Programme A',
                'status' => 'preparation',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
            ],
            [
                'reference' => 'PRG-00B',
                'name' => 'Programme B',
                'status' => 'preparation',
                'start_date' => '2025-02-01',
                'end_date' => '2025-11-30',
            ],
            [
                'reference' => 'PROG-00C',
                'name' => 'Programme C',
                'status' => 'preparation',
                'start_date' => '2025-03-01',
                'end_date' => '2025-09-30',
            ],
        ];

        foreach ($programs as $data) {
            $beneficiaries = Beneficiary::query()->inRandomOrder()->limit(rand(2, 3))->get();

            $fundingSources = FundingSource::query()->inRandomOrder()->limit(rand(1, 3))->get();

            $fundingData = $fundingSources->map(function ($source) {
                return [
                    'funding_source_uuid' => $source->uuid,
                    'planned_budget' => fake()->numberBetween(50000, 250000),
                ];
            });

            $totalBudget = $fundingData->sum('planned_budget');


            $program = Program::create([
                'reference' => $data['reference'],
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'budget' => $totalBudget,
                'currency' => 'MRU',
                'responsible_uuid' => User::query()->inRandomOrder()->value('uuid'),
                'status' => $data['status'],
            ]);

            if ($beneficiaries->isNotEmpty()) {
                $program->beneficiaries()->attach($beneficiaries->pluck('uuid'));
            }

            foreach ($fundingData as $funding) {
                $program->fundingSources()->attach($funding['funding_source_uuid'], [
                    'planned_budget' => $funding['planned_budget'],
                ]);
            }
        }
    }
}
