<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'sytem_admin',
            'first_name' => 'System',
            'last_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456aA@'),
            'role' => 'super_admin',
            'status' => 'active',
            'code' => 'ADMIN001',
            'email_verified_at' => now(),
        ]);
    }
}

