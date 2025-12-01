<?php

namespace Database\Seeders;

use App\Models\FundingSource;
use Illuminate\Database\Seeder;

class FoundingSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FundingSource::whereNotNull('id')->delete();

        $sources = [
            [
                'name' => 'Banque mondiale',
                'description' => 'Partenaire de financement institutionnel global',
            ],
            [
                'name' => 'Union Européenne',
                'description' => 'Soutien au développement durable',
            ],
            [
                'name' => 'Fonds propre de l’État',
                'description' => 'Financement public national',
            ],
            [
                'name' => 'Partenariat public-privé',
                'description' => 'Collaboration entre secteurs public et privé',
            ],
            [
                'name' => 'Programme des Nations Unies pour le Développement (PNUD)',
                'description' => 'Agence de l’ONU dédiée au développement',
            ],
            [
                'name' => 'Budget National',
                'description' => 'Financement interne par l’État',
            ],
        ];

        foreach ($sources as $source) {
            FundingSource::create([
                'name' => $source['name'],
                'description' => $source['description'],
                'structure_uuid' => null,
                'status' => true,
                'created_by' => null,
                'updated_by' => null,
            ]);
        }
    }
}
