<?php

namespace Database\Seeders;

use App\Models\StrategicElement;
use App\Models\StrategicMap;
use Illuminate\Database\Seeder;

class StrategicElementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StrategicElement::whereNotNull('id')->delete();

        $stateMap = StrategicMap::whereHas('structure', fn($q) => $q->where('type', 'STATE'))->first();
        $departmentMap = StrategicMap::whereHas('structure', fn($q) => $q->where('type', 'DEPARTMENT'))->first();

        $levers = [
            [
                'name' => 'Levier de gouvernance et innovation publique',
                'abbreviation' => 'LEV-GOV',
                'description' => 'Piloter la transformation institutionnelle et la performance publique.',
                'order' => 1,
            ],
            [
                'name' => 'Levier de durabilité et environnement',
                'abbreviation' => 'LEV-DUR',
                'description' => 'Promouvoir la durabilité écologique et la responsabilité sociétale.',
                'order' => 2,
            ],
            [
                'name' => 'Levier de développement économique',
                'abbreviation' => 'LEV-ECO',
                'description' => 'Stimuler la croissance, la compétitivité et l\'innovation économique.',
                'order' => 3,
            ],
            [
                'name' => 'Levier d\'inclusion et cohésion sociale',
                'abbreviation' => 'LEV-SOC',
                'description' => 'Renforcer l\'équité, l\'inclusion et la solidarité au sein de la société.',
                'order' => 4,
            ],
        ];

        $createdLevers = [];

        foreach ($levers as $leverData) {
            $lever = StrategicElement::create([
                'type' => 'LEVER',
                'name' => $leverData['name'],
                'abbreviation' => $leverData['abbreviation'],
                'description' => $leverData['description'],
                'order' => $leverData['order'],
                'status' => true,
                'strategic_map_uuid' => $stateMap->uuid,
                'structure_uuid' => $stateMap->structure_uuid,
                'created_by' => null,
                'updated_by' => null,
            ]);

            $createdLevers[] = $lever;
        }

        $axesByLever = [
            'LEV-GOV' => [
                ['name' => 'Axe Innovation & Digitalisation', 'abbr' => 'INNO', 'desc' => 'Encourager la digitalisation et l\'innovation publique.'],
                ['name' => 'Axe Gouvernance Transparente', 'abbr' => 'GOV', 'desc' => 'Renforcer la transparence et la redevabilité.'],
            ],
            'LEV-DUR' => [
                ['name' => 'Axe Énergie Verte', 'abbr' => 'GREEN', 'desc' => 'Promouvoir les énergies renouvelables et la sobriété énergétique.'],
                ['name' => 'Axe Gestion des Ressources', 'abbr' => 'RES', 'desc' => 'Optimiser l\'utilisation des ressources naturelles.'],
            ],
            'LEV-ECO' => [
                ['name' => 'Axe Innovation Économique', 'abbr' => 'ECO-INN', 'desc' => 'Soutenir les startups et les initiatives entrepreneuriales.'],
                ['name' => 'Axe Industrie & Compétitivité', 'abbr' => 'IND', 'desc' => 'Renforcer le tissu industriel et l\'emploi local.'],
            ],
            'LEV-SOC' => [
                ['name' => 'Axe Éducation et Compétences', 'abbr' => 'EDU', 'desc' => 'Améliorer l\'accès et la qualité de l\'éducation.'],
                ['name' => 'Axe Santé & Bien-être', 'abbr' => 'SAN', 'desc' => 'Renforcer les infrastructures sanitaires et sociales.'],
            ],
        ];

        $order = 1;
        foreach ($createdLevers as $lever) {
            $axes = $axesByLever[$lever->abbreviation] ?? [];

            foreach ($axes as $axe) {
                StrategicElement::create([
                    'type' => 'AXIS',
                    'name' => $axe['name'],
                    'abbreviation' => $axe['abbr'],
                    'description' => $axe['desc'],
                    'order' => $order++,
                    'status' => true,
                    'strategic_map_uuid' => $departmentMap->uuid,
                    'structure_uuid' => $departmentMap->structure_uuid,
                    'parent_element_uuid' => $lever->uuid,
                    'parent_structure_uuid' => $lever->structure_uuid,
                    'parent_map_uuid' => $lever->strategic_map_uuid,
                    'created_by' => null,
                    'updated_by' => null,
                ]);
            }
        }
    }
}
