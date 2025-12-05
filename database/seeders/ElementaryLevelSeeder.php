<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Beneficiary;
use App\Models\FundingSource;
use App\Models\ElementaryLevel;
use Illuminate\Database\Seeder;
use App\Models\CapabilityDomain;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ElementaryLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $elementaryLevels = [
            [
                'reference' => 'EML-001',
                'name' => 'Niveau élémentaire A ',
                'start_date' => '2025-01-15',
                'end_date' => '2025-01-30',
                'status' => 'preparation',
            ],
            [
                'reference' => 'EML-002',
                'name' => 'Niveau élémentaire B',
                'start_date' => '2025-03-01',
                'end_date' => '2025-03-15',
                'status' => 'preparation',
            ],
            [
                'reference' => 'EML-003',
                'name' => 'Niveau élémentaire C',
                'start_date' => '2025-05-01',
                'end_date' => '2025-06-01',
                'status' => 'preparation',
            ],
        ];

        foreach ($elementaryLevels as $data) {
            $beneficiaries = Beneficiary::query()->inRandomOrder()->limit(rand(2, 3))->get();

            $fundingSources = FundingSource::query()->inRandomOrder()->limit(rand(1, 3))->get();

            $fundingData = $fundingSources->map(function ($source) {
                return [
                    'funding_source_uuid' => $source->uuid,
                    'planned_budget' => fake()->numberBetween(50000, 250000),
                ];
            });

            $totalBudget = $fundingData->sum('planned_budget');



            $elementaryLevel = ElementaryLevel::create([
                'capability_domain_uuid' => CapabilityDomain::query()->inRandomOrder()->value('uuid'),
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
                $elementaryLevel->beneficiaries()->attach($beneficiaries->pluck('uuid'));
            }

            foreach ($fundingData as $funding) {
                $elementaryLevel->fundingSources()->attach($funding['funding_source_uuid'], [
                    'planned_budget' => $funding['planned_budget'],
                ]);
            }
        }
    }
}
