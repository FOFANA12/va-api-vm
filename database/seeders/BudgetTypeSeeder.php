<?php

namespace Database\Seeders;

use App\Models\BudgetType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BudgetTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BudgetType::whereNotNull('id')->delete();

        $budgetTypes = [
            ['name' => "Marketing"],
            ['name' => "Transport"],
            ['name' => "Épargne"],
            ['name' => "Loyer"],
        ];

        foreach ($budgetTypes as $budget) {
            BudgetType::create([
                'name' => $budget['name'],
                'status' => true,
                'created_by' => null,
                'updated_by' => null,
            ]);
        }
    }
}
