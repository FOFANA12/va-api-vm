<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $firstAction = Action::first();

        if (!$firstAction) {
            $this->command->warn('⚠️ Aucun enregistrement trouvé dans la table actions. Le seeder SupplierSeeder n\'a rien créé.');
            return;
        }

        Supplier::factory()
            ->count(5)
            ->create([
                'contract_type_uuid' => $firstAction->contract_type_uuid,
            ]);
    }
}
