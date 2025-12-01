<?php

namespace Database\Seeders;

use App\Models\FileType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FileType::whereNotNull('id')->delete();

        $fileTypes = [
            [
                'name' => 'AD-HOC',
                'status' => true,
            ],
            [
                'name' => 'CPMP',
                'status' => true,
            ],
            [
                'name' => 'CNCMP',
                'status' => true,
            ],
        ];

        foreach ($fileTypes as $type) {
            FileType::create([
                'name' => $type['name'],
                'status' => $type['status'],
                'created_by' => null,
                'updated_by' => null,
            ]);
        }
    }
}
