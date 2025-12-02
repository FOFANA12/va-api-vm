<?php

namespace Database\Seeders;

use App\Models\Structure;
use Illuminate\Database\Seeder;

class StructureSeeder extends Seeder
{
    public function run(): void
    {
        Structure::whereNotNull('id')->delete();

        $state = Structure::create([
            'abbreviation' => 'STATE-ROOT',
            'name' => 'Autorité Centrale',
            'type' => 'STATE',
            'parent_uuid' => null,
        ]);

        $strategic = Structure::create([
            'abbreviation' => 'ADM-CENT',
            'name' => 'Administration Centrale',
            'type' => 'STRATEGIC',
            'parent_uuid' => $state->uuid,
        ]);

        $unitA = Structure::create([
            'abbreviation' => 'UGP',
            'name' => 'Unité de Gestion des Projets',
            'type' => 'VIRTUAL',
            'parent_uuid' => $strategic->uuid,
        ]);

        $unitB = Structure::create([
            'abbreviation' => 'UAS',
            'name' => 'Unité d\'Appui et de Support',
            'type' => 'VIRTUAL',
            'parent_uuid' => $strategic->uuid,
        ]);

        $uasOperational = [
            ['abbr' => 'UAS-OPS1', 'name' => 'Service d\'Assistance Technique'],
            ['abbr' => 'UAS-OPS2', 'name' => 'Service de Maintenance Systèmes'],
            ['abbr' => 'UAS-OPS3', 'name' => 'Service Logistique Interne'],
        ];

        foreach ($uasOperational as $dir) {
            Structure::create([
                'abbreviation' => $dir['abbr'],
                'name' => $dir['name'],
                'type' => 'OPERATIONAL',
                'parent_uuid' => $unitB->uuid,
            ]);
        }

        $unitAChildren = [
            [
                'abbr' => 'GPRJ',
                'name' => 'Service Gestion des Projets',
                'sub_virtual' => [
                    'Cellule Analyse',
                    'Cellule Suivi-Évaluation',
                    'Cellule Planification',
                ]
            ],
            [
                'abbr' => 'GPRH',
                'name' => 'Service Ressources Humaines',
                'sub_virtual' => [
                    'Cellule Recrutement',
                    'Cellule Développement des Compétences',
                ]
            ],
        ];

        foreach ($unitAChildren as $child) {

            $operational = Structure::create([
                'abbreviation' => $child['abbr'],
                'name' => $child['name'],
                'type' => 'OPERATIONAL',
                'parent_uuid' => $unitA->uuid,
            ]);

            foreach ($child['sub_virtual'] as $index => $dirName) {
                Structure::create([
                    'abbreviation' => $child['abbr'] . '-V' . ($index + 1),
                    'name' => $dirName,
                    'type' => 'VIRTUAL',
                    'parent_uuid' => $operational->uuid,
                ]);
            }
        }
    }
}
