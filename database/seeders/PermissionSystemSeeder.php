<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PermissionSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xoá dữ liệu cũ trong các bảng quan hệ
        if (Schema::hasTable('role_permissions')) {
            DB::table('role_permissions')->delete();
        }

        if (Schema::hasTable('user_roles')) {
            DB::table('user_roles')->delete();
        }

        // Seed the permission groups
        $this->call(PermissionGroupSeeder::class);

        // Seed the permissions
        $this->call(PermissionsSeeder::class);

        // Seed the roles and assign permissions
        $this->call(RolesSeeder::class);

        // Assign roles to users
        $this->call(UserRolesSeeder::class);
    }
}
