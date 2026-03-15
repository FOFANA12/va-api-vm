<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Permission;

class AssignAllPermissionsToAdmin extends Command
{
    protected $signature = 'permissions:assign-admin';

    protected $description = 'Assign all permissions to admin user';

    public function handle()
    {
        $user = User::where('email', 'admin@admin.com')->first();

        if (!$user) {
            $this->error('Admin user not found.');
            return Command::FAILURE;
        }

        $role = $user->role;

        if (!$role) {
            $this->error('Admin user has no role.');
            return Command::FAILURE;
        }

        $permissions = Permission::pluck('id')->toArray();

        $role->permissions()->sync($permissions);

        $this->info('All permissions assigned to admin role successfully.');

        return Command::SUCCESS;
    }
}