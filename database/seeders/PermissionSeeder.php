<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the required tables exist
        if (!Schema::hasTable('permission_groups') || !Schema::hasTable('permissions') || !Schema::hasTable('roles') || !Schema::hasTable('role_permissions')) {
            $this->command->info('One or more required tables do not exist. Skipping permission seeding.');
            return;
        }

        // Get permission configuration
        $permissionGroups = Config::get('permissions.groups');
        $roles = Config::get('permissions.roles');
        $rolePermissions = Config::get('permissions.role_permissions');

        // Create permission groups and permissions
        foreach ($permissionGroups as $groupData) {
            $group = PermissionGroup::create([
                'name' => $groupData['name'],
                'code' => $groupData['code'],
                'display_name' => $groupData['name'],
            ]);

            foreach ($groupData['permissions'] as $permissionData) {
                Permission::create([
                    'name' => $permissionData['name'],
                    'code' => $permissionData['code'],
                    'display_name' => $permissionData['name'],
                    'permission_group_id' => $group->id,
                ]);
            }
        }

        // Create roles
        $roleModels = [];
        foreach ($roles as $roleData) {
            $roleModels[$roleData['name']] = Role::create([
                'name' => $roleData['name'],
                'display_name' => $roleData['display_name'],
                'description' => $roleData['description'],
            ]);
        }

        // Assign permissions to roles
        foreach ($rolePermissions as $roleName => $permissionCodes) {
            if (isset($roleModels[$roleName])) {
                $role = $roleModels[$roleName];
                $permissions = Permission::whereIn('code', $permissionCodes)->get();

                foreach ($permissions as $permission) {
                    $role->permissions()->attach($permission->id);
                }
            }
        }
    }
}
