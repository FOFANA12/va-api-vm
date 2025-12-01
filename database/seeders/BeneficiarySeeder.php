<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use Illuminate\Database\Seeder;

class BeneficiarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Beneficiary::factory()->count(5)->create();
    }
}
