<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;

class AddStrategicObjectiveStatusPermissions extends Command
{
    protected $signature = 'permissions:add-objective-status';

    protected $description = 'Add permissions for Strategic Objective Status';

    public function handle()
    {
        $permissions = [

            [
                'name' => 'OBJ_ACCESS_STATUS',
                'category' => 'Objectif — Statut',
                'description' => 'Accès au statut'
            ],

            [
                'name' => 'OBJ_MANAGE_STATUS',
                'category' => 'Objectif — Statut',
                'description' => 'Gérer le statut'
            ],
        ];

        foreach ($permissions as $perm) {

            Permission::updateOrCreate(
                ['name' => $perm['name']],
                [
                    'category' => $perm['category'],
                    'description' => $perm['description']
                ]
            );

            $this->info("Permission {$perm['name']} added or updated.");
        }

        $this->info('Strategic objective status permissions successfully added.');

        return Command::SUCCESS;
    }
}