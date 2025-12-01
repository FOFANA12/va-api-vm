<?php

namespace Database\Seeders;

use App\Models\ContractType;
use App\Models\ProcurementMode;
use App\Models\Structure;
use Illuminate\Database\Seeder;

class ProcurementModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $modes = [
            [
                'name' => 'Appel d\'offres ouvert',
                'duration'=> 5,
                'status' => true,
            ],
            [
                'name' => 'Appel d\'offres restreint',
                'duration'=> 15,
                'status' => true,
            ],
            [
                'name' => 'Entente directe',
                'duration'=> 20,
                'status' => false,
            ],
        ];

        foreach ($modes as $data) {
            ProcurementMode::create([
                'contract_type_uuid' => ContractType::query()->inRandomOrder()->value('uuid'),
                'name' => $data['name'],
                'duration' => $data['duration'],
                'status' => $data['status'],
            ]);
        }
    }
}
