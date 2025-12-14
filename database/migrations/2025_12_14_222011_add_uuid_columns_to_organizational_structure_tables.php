<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     * Add UUID columns to OrganizationalStructure tables (users, faculties, departments).
     * UUID columns are nullable initially, will be populated and made required later.
     */
    public function up(): void
    {
        // Add uuid column to users table
        Schema::table('users', function (Blueprint $table): void {
            if (!Schema::hasColumn('users', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id');
                $table->index('uuid');
            }
        });

        // Add uuid column to faculties table
        Schema::table('faculties', function (Blueprint $table): void {
            if (!Schema::hasColumn('faculties', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id');
                $table->index('uuid');
            }
        });

        // Add uuid column to departments table
        Schema::table('departments', function (Blueprint $table): void {
            if (!Schema::hasColumn('departments', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id');
                $table->index('uuid');
            }
        });

        // Add uuid columns to foreign key columns in users table
        Schema::table('users', function (Blueprint $table): void {
            if (!Schema::hasColumn('users', 'faculty_uuid')) {
                $table->uuid('faculty_uuid')->nullable()->after('faculty_id');
                $table->index('faculty_uuid');
            }
            if (!Schema::hasColumn('users', 'department_uuid')) {
                $table->uuid('department_uuid')->nullable()->after('department_id');
                $table->index('department_uuid');
            }
        });

        // Add uuid column to foreign key column in departments table
        Schema::table('departments', function (Blueprint $table): void {
            if (!Schema::hasColumn('departments', 'faculty_uuid')) {
                $table->uuid('faculty_uuid')->nullable()->after('faculty_id');
                $table->index('faculty_uuid');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove uuid columns from users table
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'uuid')) {
                $table->dropIndex(['uuid']);
                $table->dropColumn('uuid');
            }
            if (Schema::hasColumn('users', 'faculty_uuid')) {
                $table->dropIndex(['faculty_uuid']);
                $table->dropColumn('faculty_uuid');
            }
            if (Schema::hasColumn('users', 'department_uuid')) {
                $table->dropIndex(['department_uuid']);
                $table->dropColumn('department_uuid');
            }
        });

        // Remove uuid column from faculties table
        Schema::table('faculties', function (Blueprint $table): void {
            if (Schema::hasColumn('faculties', 'uuid')) {
                $table->dropIndex(['uuid']);
                $table->dropColumn('uuid');
            }
        });

        // Remove uuid columns from departments table
        Schema::table('departments', function (Blueprint $table): void {
            if (Schema::hasColumn('departments', 'uuid')) {
                $table->dropIndex(['uuid']);
                $table->dropColumn('uuid');
            }
            if (Schema::hasColumn('departments', 'faculty_uuid')) {
                $table->dropIndex(['faculty_uuid']);
                $table->dropColumn('faculty_uuid');
            }
        });
    }
};
