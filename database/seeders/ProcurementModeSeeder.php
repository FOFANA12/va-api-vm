<?php

namespace Database\Seeders;

use App\Models\ProcurementMode;
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
                'name' => $data['name'],
                'duration' => $data['duration'],
                'status' => $data['status'],
            ]);
        }
    }
}
