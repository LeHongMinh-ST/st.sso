<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gán vai trò dựa trên loại tài khoản (Role enum)
        $users = User::all();

        foreach ($users as $user) {
            // Gán vai trò dựa trên loại tài khoản
            switch ($user->role) {
                case RoleEnum::SuperAdmin:
                    // Quản trị viên có tất cả các vai trò
                    $roles = Role::where('code', 'super_admin')->get();
                    $user->roles()->sync($roles->pluck('id')->toArray());
                    break;

                case RoleEnum::Officer:
                    // Giảng viên - Cán bộ khoa
                    $roles = Role::whereIn('code', ['faculty_admin', 'teacher'])->get();
                    $user->roles()->sync($roles->pluck('id')->toArray());
                    break;

                case RoleEnum::Student:
                    // Sinh viên
                    $role = Role::where('code', 'student')->first();
                    if ($role) {
                        $user->roles()->sync([$role->id]);
                    }
                    break;

                case RoleEnum::Normal:
                    // Người dùng cơ bản
                    $role = Role::where('name', 'Người dùng cơ bản')->first();
                    if ($role) {
                        $user->roles()->sync([$role->id]);
                    }
                    break;

                default:
                    // Mặc định là người dùng cơ bản
                    $role = Role::where('name', 'Người dùng cơ bản')->first();
                    if ($role) {
                        $user->roles()->sync([$role->id]);
                    }
                    break;
            }
        }
    }
}
