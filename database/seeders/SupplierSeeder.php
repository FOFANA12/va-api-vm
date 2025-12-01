<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\ContractType;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contractType = ContractType::first();

        if (!$contractType) {
            $this->command->warn('⚠️ Aucun type de contrat trouvé. Le seeder SupplierSeeder n’a rien créé.');
            return;
        }

        Supplier::factory()
            ->count(5)
            ->create([
                'contract_type_uuid' => $contractType->uuid,
            ]);
    }
}
