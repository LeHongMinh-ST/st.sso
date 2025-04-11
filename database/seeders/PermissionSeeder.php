<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Tạo các quyền theo module

        // Module User
        Permission::create(['name' => 'user.view', 'group' => 'user']);
        Permission::create(['name' => 'user.create', 'group' => 'user']);
        Permission::create(['name' => 'user.edit', 'group' => 'user']);
        Permission::create(['name' => 'user.delete', 'group' => 'user']);
        Permission::create(['name' => 'user.reset_password', 'group' => 'user']);

        // Module Faculty
        Permission::create(['name' => 'faculty.view', 'group' => 'faculty']);
        Permission::create(['name' => 'faculty.create', 'group' => 'faculty']);
        Permission::create(['name' => 'faculty.edit', 'group' => 'faculty']);
        Permission::create(['name' => 'faculty.delete', 'group' => 'faculty']);

        // Module Client
        Permission::create(['name' => 'client.view', 'group' => 'client']);
        Permission::create(['name' => 'client.create', 'group' => 'client']);
        Permission::create(['name' => 'client.edit', 'group' => 'client']);
        Permission::create(['name' => 'client.delete', 'group' => 'client']);

        // Module Role
        Permission::create(['name' => 'role.view', 'group' => 'role']);
        Permission::create(['name' => 'role.create', 'group' => 'role']);
        Permission::create(['name' => 'role.edit', 'group' => 'role']);
        Permission::create(['name' => 'role.delete', 'group' => 'role']);
        Permission::create(['name' => 'role.assign_permissions', 'group' => 'role']);
        Permission::create(['name' => 'role.assign_users', 'group' => 'role']);

        // Module API
        Permission::create(['name' => 'api.user', 'group' => 'api']);
        Permission::create(['name' => 'api.faculty', 'group' => 'api']);
        Permission::create(['name' => 'api.client', 'group' => 'api']);
        Permission::create(['name' => 'api.role', 'group' => 'api']);
        Permission::create(['name' => 'api.auth', 'group' => 'api']);

        // Tạo các vai trò
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $adminRole = Role::create(['name' => 'admin']);
        $facultyAdminRole = Role::create(['name' => 'faculty-admin']);
        $teacherRole = Role::create(['name' => 'teacher']);
        $studentRole = Role::create(['name' => 'student']);
        $normalRole = Role::create(['name' => 'normal']);

        // Gán quyền cho vai trò super-admin (tất cả quyền)
        $superAdminRole->givePermissionTo(Permission::all());

        // Gán quyền cho vai trò admin
        $adminRole->givePermissionTo([
            'user.view', 'user.create', 'user.edit', 'user.reset_password',
            'faculty.view', 'faculty.create', 'faculty.edit',
            'client.view',
            'role.view',
            'api.user', 'api.faculty', 'api.auth'
        ]);

        // Gán quyền cho vai trò faculty-admin
        $facultyAdminRole->givePermissionTo([
            'user.view', 'user.create', 'user.edit', 'user.reset_password',
            'faculty.view',
            'api.user', 'api.faculty', 'api.auth'
        ]);

        // Gán quyền cho vai trò teacher
        $teacherRole->givePermissionTo([
            'user.view',
            'faculty.view'
        ]);

        // Gán quyền cho vai trò student
        $studentRole->givePermissionTo([
            'faculty.view'
        ]);

        // Gán quyền cho vai trò normal
        $normalRole->givePermissionTo([
            'faculty.view'
        ]);
    }
}
