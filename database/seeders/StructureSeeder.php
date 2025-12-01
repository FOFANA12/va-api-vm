<?php

namespace Database\Seeders;

use App\Models\Structure;
use Illuminate\Database\Seeder;

class StructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Structure::whereNotNull('id')->delete();

        $ministry = Structure::create([
            'abbreviation' => 'MDN',
            'name' => 'Ministère de la Défense Nationale',
            'type' => 'STATE'
        ]);

        $departments = [
            [
                'abbr' => 'DLOG',
                'name' => 'Département de la Logistique',
                'directions' => [
                    'Direction des Approvisionnements',
                    'Direction de Maintenance',
                    'Direction des Transports',
                ]
            ],
            [
                'abbr' => 'DRH',
                'name' => 'Département des Ressources Humaines',
                'directions' => [
                    'Direction du Recrutement',
                    'Direction de la Formation',
                ]
            ],
            [
                'abbr' => 'DOPS',
                'name' => 'Département des Opérations',
                'directions' => [
                    'Direction des Opérations Aériennes',
                    'Direction des Opérations Terrestres',
                    'Direction de Coordination Stratégique',
                ]
            ],
        ];

        foreach ($departments as $dep) {

            $department = Structure::create([
                'abbreviation' => $dep['abbr'],
                'name' => $dep['name'],
                'type' => 'DEPARTMENT',
                'parent_uuid' => $ministry->uuid,
            ]);

            foreach ($dep['directions'] as $index => $directionName) {
                Structure::create([
                    'abbreviation' => $dep['abbr'] . '-DIR' . ($index + 1),
                    'name' => $directionName,
                    'type' => 'DIRECTION',
                    'parent_uuid' => $department->uuid,
                ]);
            }
        }
    }
}
