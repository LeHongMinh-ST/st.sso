<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(UserTableSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RoleUserSeeder::class);
    }
}
