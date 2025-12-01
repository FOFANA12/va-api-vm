<?php

namespace Database\Seeders;

use App\Models\ExpenseType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExpenseType::whereNotNull('id')->delete();
        $expenseTypes = [
            [
                'name' => 'Depense 1',
                'status' => false,
            ],
            [
                'name' => 'Depense 2',
                'status' => true,
            ],
            [
                'name' => 'Depense 3',
                'status' => false,
            ],
            [
                'name' => 'Depense 4',
                'status' => false,
            ],
        ];

        foreach ($expenseTypes as $data) {
            ExpenseType::create([
                'name' => $data['name'],
                'status' => $data['status'],
                'created_by' => null,
                'updated_by' => null,
            ]);
        }
    }
}
