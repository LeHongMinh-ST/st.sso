<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the user_roles table exists
        if (!Schema::hasTable('user_roles')) {
            $this->command->info('The user_roles table does not exist. Skipping role assignment.');
            return;
        }

        // Gán vai trò cho người dùng dựa trên enum Role hiện tại
        $users = User::all();

        foreach ($users as $user) {
            $roleName = match ($user->role) {
                RoleEnum::SuperAdmin => 'super-admin',
                RoleEnum::Officer => 'faculty-admin',
                RoleEnum::Student => 'student',
                RoleEnum::Normal => 'normal',
                default => 'normal',
            };

            $role = Role::where('name', $roleName)->first();
            if ($role) {
                // Check if the relationship already exists to avoid duplicates
                if (!DB::table('user_roles')->where('user_id', $user->id)->where('role_id', $role->id)->exists()) {
                    $user->roles()->attach($role->id);
                }
            }
        }
    }
}
