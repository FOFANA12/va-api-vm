<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Structure;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::whereNotNull('id')->delete();

        $adminRole = Role::where('name', 'Administrateur')->first();
        $simpleRole = Role::firstOrCreate(
            ['name' => 'Utilisateur simple'],
            ['description' => 'Rôle par défaut pour les utilisateurs non administrateurs.']
        );

        // === ADMIN ===
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'phone' => '38086802',
            'password' => Hash::make('password'),
            'status' => true,
            'lang' => 'fr',
            'role_uuid' => $adminRole?->uuid,
        ]);

        $defaultStructure = Structure::where('abbreviation', 'MDN')->first();

        if (!$defaultStructure) {
            $defaultStructure = Structure::factory()->create([
                'abbreviation' => 'MDN',
                'name' => 'Ministère de la Défense Nationale',
            ]);
        }

        // === EMPLOYÉ TEST ===
        $employeeUser = User::create([
            'name' => 'Employé Test',
            'email' => 'employe@employe.com',
            'phone' => '01234567',
            'password' => Hash::make('password'),
            'status' => true,
            'lang' => 'fr',
            'role_uuid' => $simpleRole->uuid,
        ]);

        Employee::factory()->create([
            'user_uuid' => $employeeUser->uuid,
            'structure_uuid' => $defaultStructure->uuid,
            'can_logged_in' => true,
        ]);

        $structureUuids = Structure::where('abbreviation', '!=', 'MDN')->pluck('uuid');

        $users = User::factory()
            ->count(30)
            ->state(function () {
                $canLogin = fake()->boolean(80);

                return [
                    'email' => $canLogin ? fake()->unique()->safeEmail() : null,
                    'password' => $canLogin ? Hash::make('password') : null,
                ];
            })
            ->create();

        $users->each(function ($user) use ($structureUuids, $simpleRole) {
            Employee::factory()->create([
                'user_uuid' => $user->uuid,
                'structure_uuid' => fake()->randomElement($structureUuids),
                'can_logged_in' => $user->email !== null && $user->password !== null,
            ]);

            $user->update(['role_uuid' => $simpleRole->uuid]);
        });
    }
}
