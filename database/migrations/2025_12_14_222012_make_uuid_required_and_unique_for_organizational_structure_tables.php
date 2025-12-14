<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     * Make UUID columns required and unique after they have been populated.
     *
     * IMPORTANT: This migration should be run AFTER PopulateOrganizationalStructureUuids command.
     */
    public function up(): void
    {
        // Check if UUIDs have been populated
        $this->validateUuidsPopulated();

        // Make uuid column required and unique for faculties
        Schema::table('faculties', function (Blueprint $table): void {
            if (Schema::hasColumn('faculties', 'uuid')) {
                $table->uuid('uuid')->nullable(false)->unique()->change();
            }
        });

        // Make uuid column required and unique for departments
        Schema::table('departments', function (Blueprint $table): void {
            if (Schema::hasColumn('departments', 'uuid')) {
                $table->uuid('uuid')->nullable(false)->unique()->change();
            }
        });

        // Make uuid column required and unique for users
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'uuid')) {
                $table->uuid('uuid')->nullable(false)->unique()->change();
            }
        });

        // Note: Foreign key UUID columns (faculty_uuid, department_uuid) remain nullable
        // because they are optional relationships
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Make uuid columns nullable again (but keep unique constraint)
        Schema::table('faculties', function (Blueprint $table): void {
            if (Schema::hasColumn('faculties', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->change();
            }
        });

        Schema::table('departments', function (Blueprint $table): void {
            if (Schema::hasColumn('departments', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->change();
            }
        });

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->change();
            }
        });
    }

    /**
     * Validate that UUIDs have been populated before making them required.
     *
     * @return void
     * @throws RuntimeException
     */
    private function validateUuidsPopulated(): void
    {
        $tables = ['faculties', 'departments', 'users'];

        foreach ($tables as $table) {
            $nullCount = DB::table($table)->whereNull('uuid')->count();

            if ($nullCount > 0) {
                throw new RuntimeException(
                    "Cannot make UUID required for {$table}: {$nullCount} records still have null UUIDs. " .
                    "Please run 'php artisan migrate:populate-organizational-structure-uuids' first."
                );
            }
        }
    }
};
