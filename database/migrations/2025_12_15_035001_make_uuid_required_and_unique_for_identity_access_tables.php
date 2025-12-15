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
     * IMPORTANT: This migration should be run AFTER PopulateIdentityAccessUuids command.
     */
    public function up(): void
    {
        // Check if UUIDs have been populated
        $this->validateUuidsPopulated();

        // Make uuid column required and unique for roles
        Schema::table('roles', function (Blueprint $table): void {
            if (Schema::hasColumn('roles', 'uuid')) {
                $table->uuid('uuid')->nullable(false)->unique()->change();
            }
        });

        // Make uuid column required and unique for permissions
        Schema::table('permissions', function (Blueprint $table): void {
            if (Schema::hasColumn('permissions', 'uuid')) {
                $table->uuid('uuid')->nullable(false)->unique()->change();
            }
        });

        // Make uuid column required and unique for permission_groups
        if (Schema::hasTable('permission_groups')) {
            Schema::table('permission_groups', function (Blueprint $table): void {
                if (Schema::hasColumn('permission_groups', 'uuid')) {
                    $table->uuid('uuid')->nullable(false)->unique()->change();
                }
            });
        }

        // Make uuid column required and unique for oauth_clients
        if (Schema::hasTable('oauth_clients')) {
            Schema::table('oauth_clients', function (Blueprint $table): void {
                if (Schema::hasColumn('oauth_clients', 'uuid')) {
                    $table->uuid('uuid')->nullable(false)->unique()->change();
                }
            });
        }

        // Note: Foreign key UUID columns (permission_group_uuid) remain nullable
        // because they are optional relationships
        // Note: Pivot table UUID columns remain nullable as they are derived from foreign keys
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Make uuid columns nullable again (but keep unique constraint)
        Schema::table('roles', function (Blueprint $table): void {
            if (Schema::hasColumn('roles', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->change();
            }
        });

        Schema::table('permissions', function (Blueprint $table): void {
            if (Schema::hasColumn('permissions', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->change();
            }
        });

        if (Schema::hasTable('permission_groups')) {
            Schema::table('permission_groups', function (Blueprint $table): void {
                if (Schema::hasColumn('permission_groups', 'uuid')) {
                    $table->uuid('uuid')->nullable()->unique()->change();
                }
            });
        }

        if (Schema::hasTable('oauth_clients')) {
            Schema::table('oauth_clients', function (Blueprint $table): void {
                if (Schema::hasColumn('oauth_clients', 'uuid')) {
                    $table->uuid('uuid')->nullable()->unique()->change();
                }
            });
        }
    }

    /**
     * Validate that UUIDs have been populated before making them required.
     *
     * @return void
     * @throws RuntimeException
     */
    private function validateUuidsPopulated(): void
    {
        $tables = ['roles', 'permissions'];

        // Add permission_groups if table exists
        if (Schema::hasTable('permission_groups')) {
            $tables[] = 'permission_groups';
        }

        // Add oauth_clients if table exists and has uuid column
        if (Schema::hasTable('oauth_clients') && Schema::hasColumn('oauth_clients', 'uuid')) {
            $tables[] = 'oauth_clients';
        }

        foreach ($tables as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'uuid')) {
                continue;
            }

            $nullCount = DB::table($table)->whereNull('uuid')->count();

            if ($nullCount > 0) {
                throw new RuntimeException(
                    "Cannot make UUID required for {$table}: {$nullCount} records still have null UUIDs. " .
                    "Please run 'php artisan migrate:populate-identity-access-uuids' first."
                );
            }
        }
    }
};
