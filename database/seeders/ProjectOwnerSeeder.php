<?php
namespace Database\Seeders;

use App\Models\ProjectOwner;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectOwnerSeeder extends Seeder
{
    public function run(): void
    {
        $structure = Structure::first();

        $owners = [
            [
                'name' => 'Ministère des Travaux Publics',
                'type' => 'Public',
                'email' => 'mtp@example.gov',
                'phone' => '+22212345678',
                'status' => true,
            ],
            [
                'name' => 'Entreprise BTP Sahel',
                'type' => 'Privé',
                'email' => 'contact@btpsahel.com',
                'phone' => '+22298765432',
                'status' => true,
            ],
            [
                'name' => 'ONG Développement Rural',
                'type' => 'ONG',
                'email' => 'info@ongdr.org',
                'phone' => '+22233445566',
                'status' => false,
            ],
        ];

        foreach ($owners as $data) {
            ProjectOwner::create([
                'structure_uuid' => $structure->uuid,
                'name' => $data['name'],
                'type' => $data['type'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'status' => $data['status'],
            ]);
        }
    }
}
