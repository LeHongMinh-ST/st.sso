<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gán vai trò cho người dùng dựa trên enum Role hiện tại
        $users = User::all();

        foreach ($users as $user) {
            switch ($user->role) {
                case RoleEnum::SuperAdmin:
                    $user->assignRole('super-admin');
                    break;
                case RoleEnum::Officer:
                    $user->assignRole('faculty-admin');
                    break;
                case RoleEnum::Student:
                    $user->assignRole('student');
                    break;
                case RoleEnum::Normal:
                    $user->assignRole('normal');
                    break;
                default:
                    $user->assignRole('normal');
                    break;
            }
        }
    }
}
