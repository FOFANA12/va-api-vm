<?php

namespace Database\Seeders;

use App\Models\IndicatorCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndicatorCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IndicatorCategory::whereNotNull('id')->delete();
        $categories = [
            [
                'name' => 'Performance Financière',
                'status' => true,
            ],
            [
                'name' => 'Satisfaction Client',
                'status' => true,
            ],
            [
                'name' => 'Processus Internes',
                'status' => true,
            ],
            [
                'name' => 'Apprentissage et Croissance',
                'status' => false,
            ],
        ];

        foreach ($categories as $category) {
            IndicatorCategory::create([
                'name' => $category['name'],
                'status' => $category['status'],
                'created_by' => null,
                'updated_by' => null,
            ]);
        }
    }
}
