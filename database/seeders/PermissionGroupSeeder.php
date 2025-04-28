<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;

class PermissionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = config('permissions.groups');

        foreach ($groups as $group) {
            PermissionGroup::updateOrCreate(
                ['code' => $group['code']],
                [
                    'name' => $group['name'],
                    'description' => $group['description'],
                ]
            );
        }
    }
}
