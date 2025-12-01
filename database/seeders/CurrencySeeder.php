<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::whereNotNull('id')->delete();
        $currencies = [
            [
                'name' => 'Ouguiya',
                'code' => 'MRU',
                'is_default' => false,
            ],
            [
                'name' => 'Franc CFA',
                'code' => 'XOF',
                'is_default' => true,
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'is_default' => false,
            ],
            [
                'name' => 'Dollar américain',
                'code' => 'USD',
                'is_default' => false,
            ],
        ];

        foreach ($currencies as $data) {
            Currency::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'is_default' => $data['is_default'],
                'status' => true,
                'created_by' => null,
                'updated_by' => null,
            ]);
        }
    }
}
