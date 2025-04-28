<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = config('permissions.roles');
        $rolePermissions = config('permissions.role_permissions');

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['code' => $roleData['code']],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'status' => Status::Active,
                ]
            );

            // Assign permissions to the role
            if (isset($rolePermissions[$roleData['code']])) {
                $permissionIds = Permission::whereIn('code', $rolePermissions[$roleData['code']])
                    ->pluck('id')
                    ->toArray();

                $role->permissions()->sync($permissionIds);
            }
        }
    }
}
