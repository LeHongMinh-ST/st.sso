<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use Exception;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = config('permissions.permissions');

        foreach ($permissions as $permission) {
            try {
                Permission::updateOrCreate(
                    ['code' => $permission['code']],
                    [
                        'name' => $permission['name'],
                        'group_code' => $permission['group_code'],
                        'description' => $permission['description'],
                    ]
                );
            } catch (Exception $e) {
                echo "Error creating permission {$permission['code']}: {$e->getMessage()}\n";
                // Continue with next permission
            }
        }
    }
}
