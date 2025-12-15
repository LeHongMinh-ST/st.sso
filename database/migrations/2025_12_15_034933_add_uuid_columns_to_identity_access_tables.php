<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     * Add UUID columns to IdentityAccess tables (roles, permissions, permission_groups, clients).
     * UUID columns are nullable initially, will be populated and made required later.
     */
    public function up(): void
    {
        // Add uuid column to roles table
        Schema::table('roles', function (Blueprint $table): void {
            if (!Schema::hasColumn('roles', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id');
                $table->index('uuid');
            }
        });

        // Add uuid column to permissions table
        Schema::table('permissions', function (Blueprint $table): void {
            if (!Schema::hasColumn('permissions', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id');
                $table->index('uuid');
            }
        });

        // Add uuid column to permission_groups table
        if (Schema::hasTable('permission_groups')) {
            Schema::table('permission_groups', function (Blueprint $table): void {
                if (!Schema::hasColumn('permission_groups', 'uuid')) {
                    $table->uuid('uuid')->nullable()->after('id');
                    $table->index('uuid');
                }
            });
        }

        // Add uuid column to oauth_clients table (Laravel Passport)
        if (Schema::hasTable('oauth_clients')) {
            Schema::table('oauth_clients', function (Blueprint $table): void {
                if (!Schema::hasColumn('oauth_clients', 'uuid')) {
                    $table->uuid('uuid')->nullable()->after('id');
                    $table->index('uuid');
                }
            });
        }

        // Add uuid column to foreign key column in permissions table
        Schema::table('permissions', function (Blueprint $table): void {
            if (!Schema::hasColumn('permissions', 'permission_group_uuid')) {
                $table->uuid('permission_group_uuid')->nullable()->after('permission_group_id');
                $table->index('permission_group_uuid');
            }
        });

        // Add uuid columns to pivot tables
        if (Schema::hasTable('user_roles')) {
            Schema::table('user_roles', function (Blueprint $table): void {
                if (!Schema::hasColumn('user_roles', 'user_uuid')) {
                    $table->uuid('user_uuid')->nullable()->after('user_id');
                    $table->index('user_uuid');
                }
                if (!Schema::hasColumn('user_roles', 'role_uuid')) {
                    $table->uuid('role_uuid')->nullable()->after('role_id');
                    $table->index('role_uuid');
                }
            });
        }

        if (Schema::hasTable('role_permissions')) {
            Schema::table('role_permissions', function (Blueprint $table): void {
                if (!Schema::hasColumn('role_permissions', 'role_uuid')) {
                    $table->uuid('role_uuid')->nullable()->after('role_id');
                    $table->index('role_uuid');
                }
                if (!Schema::hasColumn('role_permissions', 'permission_uuid')) {
                    $table->uuid('permission_uuid')->nullable()->after('permission_id');
                    $table->index('permission_uuid');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove uuid columns from roles table
        Schema::table('roles', function (Blueprint $table): void {
            if (Schema::hasColumn('roles', 'uuid')) {
                $table->dropIndex(['uuid']);
                $table->dropColumn('uuid');
            }
        });

        // Remove uuid columns from permissions table
        Schema::table('permissions', function (Blueprint $table): void {
            if (Schema::hasColumn('permissions', 'uuid')) {
                $table->dropIndex(['uuid']);
                $table->dropColumn('uuid');
            }
            if (Schema::hasColumn('permissions', 'permission_group_uuid')) {
                $table->dropIndex(['permission_group_uuid']);
                $table->dropColumn('permission_group_uuid');
            }
        });

        // Remove uuid column from permission_groups table
        if (Schema::hasTable('permission_groups')) {
            Schema::table('permission_groups', function (Blueprint $table): void {
                if (Schema::hasColumn('permission_groups', 'uuid')) {
                    $table->dropIndex(['uuid']);
                    $table->dropColumn('uuid');
                }
            });
        }

        // Remove uuid column from oauth_clients table
        if (Schema::hasTable('oauth_clients')) {
            Schema::table('oauth_clients', function (Blueprint $table): void {
                if (Schema::hasColumn('oauth_clients', 'uuid')) {
                    $table->dropIndex(['uuid']);
                    $table->dropColumn('uuid');
                }
            });
        }

        // Remove uuid columns from pivot tables
        if (Schema::hasTable('user_roles')) {
            Schema::table('user_roles', function (Blueprint $table): void {
                if (Schema::hasColumn('user_roles', 'user_uuid')) {
                    $table->dropIndex(['user_uuid']);
                    $table->dropColumn('user_uuid');
                }
                if (Schema::hasColumn('user_roles', 'role_uuid')) {
                    $table->dropIndex(['role_uuid']);
                    $table->dropColumn('role_uuid');
                }
            });
        }

        if (Schema::hasTable('role_permissions')) {
            Schema::table('role_permissions', function (Blueprint $table): void {
                if (Schema::hasColumn('role_permissions', 'role_uuid')) {
                    $table->dropIndex(['role_uuid']);
                    $table->dropColumn('role_uuid');
                }
                if (Schema::hasColumn('role_permissions', 'permission_uuid')) {
                    $table->dropIndex(['permission_uuid']);
                    $table->dropColumn('permission_uuid');
                }
            });
        }
    }
};
