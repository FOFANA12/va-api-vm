<?php

namespace Database\Seeders;

use App\Models\DelegatedProjectOwner;
use App\Models\ProjectOwner;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DelegatedProjectOwnerSeeder extends Seeder
{
    public function run(): void
    {
        $projectOwner = ProjectOwner::first();
        $user = User::first();

        if (!$projectOwner || !$user) {
            echo "Please seed project owners and users first.\n";
            return;
        }

        $delegatedOwners = [
            [
                'name' => 'Maître d\'ouvrage délégué A',
                'email' => 'delegue.a@example.com',
                'phone' => '22330011',
                'status' => true,
            ],
            [
                'name' => 'Maître d\'ouvrage délégué B',
                'email' => 'delegue.b@example.com',
                'phone' => '22330022',
                'status' => false,
            ],
        ];

        foreach ($delegatedOwners as $data) {
            DelegatedProjectOwner::create([
                'uuid' => Str::uuid(),
                'project_owner_uuid' => $projectOwner->uuid,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'status' => $data['status'],
            ]);
        }
    }
}
