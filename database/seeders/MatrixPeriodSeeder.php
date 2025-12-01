<?php

namespace Database\Seeders;

use App\Models\MatrixPeriod;
use App\Models\StrategicMap;
use Illuminate\Database\Seeder;

class MatrixPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
     {
        $periods = [
            [
                'start_date' => '2025-01-01',
                'end_date' => '2025-06-30',
            ],
            [
                'start_date' => '2025-07-01',
                'end_date' => '2025-12-31',
            ],
            [
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
            ],
        ];

        foreach ($periods as $data) {
            MatrixPeriod::create([
                'strategic_map_uuid' => StrategicMap::inRandomOrder()->value('uuid'),
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
            ]);
        }
    }
}
