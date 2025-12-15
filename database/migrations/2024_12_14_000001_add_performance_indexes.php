<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add performance indexes for frequently queried columns.
 * This migration adds indexes to improve query performance.
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Users table indexes
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                // Foreign key indexes
                if (!$this->hasIndex('users', 'users_faculty_id_index')) {
                    $table->index('faculty_id');
                }
                if (!$this->hasIndex('users', 'users_department_id_index')) {
                    $table->index('department_id');
                }

                // Frequently queried columns
                if (!$this->hasIndex('users', 'users_email_index')) {
                    $table->index('email');
                }
                if (!$this->hasIndex('users', 'users_user_name_index')) {
                    $table->index('user_name');
                }
                if (!$this->hasIndex('users', 'users_code_index')) {
                    $table->index('code');
                }

                // Composite index for common queries
                if (!$this->hasIndex('users', 'users_faculty_id_status_index')) {
                    $table->index(['faculty_id', 'status']);
                }
            });
        }

        // Roles table indexes
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table): void {
                if (!$this->hasIndex('roles', 'roles_name_index')) {
                    $table->index('name');
                }
            });
        }

        // Permissions table indexes
        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table): void {
                if (!$this->hasIndex('permissions', 'permissions_code_index')) {
                    $table->index('code');
                }
                if (!$this->hasIndex('permissions', 'permissions_permission_group_id_index')) {
                    $table->index('permission_group_id');
                }
            });
        }

        // Faculties table indexes
        if (Schema::hasTable('faculties')) {
            Schema::table('faculties', function (Blueprint $table): void {
                if (!$this->hasIndex('faculties', 'faculties_name_index')) {
                    $table->index('name');
                }
                if (!$this->hasIndex('faculties', 'faculties_status_index')) {
                    $table->index('status');
                }
            });
        }

        // Departments table indexes
        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table): void {
                if (!$this->hasIndex('departments', 'departments_faculty_id_index')) {
                    $table->index('faculty_id');
                }
                if (!$this->hasIndex('departments', 'departments_name_index')) {
                    $table->index('name');
                }
            });
        }

        // User identities table indexes
        if (Schema::hasTable('user_identities')) {
            Schema::table('user_identities', function (Blueprint $table): void {
                if (!$this->hasIndex('user_identities', 'user_identities_username_index')) {
                    $table->index('username');
                }
                if (!$this->hasIndex('user_identities', 'user_identities_email_index')) {
                    $table->index('email');
                }
            });
        }

        // Outbox events table indexes (for event processing)
        if (Schema::hasTable('outbox_events')) {
            Schema::table('outbox_events', function (Blueprint $table): void {
                if (!$this->hasIndex('outbox_events', 'outbox_events_processed_at_index')) {
                    $table->index('processed_at');
                }
                if (!$this->hasIndex('outbox_events', 'outbox_events_created_at_index')) {
                    $table->index('created_at');
                }
                if (!$this->hasIndex('outbox_events', 'outbox_events_aggregate_type_aggregate_id_index')) {
                    $table->index(['aggregate_type', 'aggregate_id']);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropIndex(['faculty_id']);
                $table->dropIndex(['department_id']);
                $table->dropIndex(['email']);
                $table->dropIndex(['user_name']);
                $table->dropIndex(['code']);
                $table->dropIndex(['faculty_id', 'status']);
            });
        }

        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table): void {
                $table->dropIndex(['name']);
            });
        }

        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table): void {
                $table->dropIndex(['code']);
                $table->dropIndex(['permission_group_id']);
            });
        }

        if (Schema::hasTable('faculties')) {
            Schema::table('faculties', function (Blueprint $table): void {
                $table->dropIndex(['name']);
                $table->dropIndex(['status']);
            });
        }

        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table): void {
                $table->dropIndex(['faculty_id']);
                $table->dropIndex(['name']);
            });
        }

        if (Schema::hasTable('user_identities')) {
            Schema::table('user_identities', function (Blueprint $table): void {
                $table->dropIndex(['username']);
                $table->dropIndex(['email']);
            });
        }

        if (Schema::hasTable('outbox_events')) {
            Schema::table('outbox_events', function (Blueprint $table): void {
                $table->dropIndex(['processed_at']);
                $table->dropIndex(['created_at']);
                $table->dropIndex(['aggregate_type', 'aggregate_id']);
            });
        }
    }

    /**
     * Check if index exists.
     *
     * @param string $table Table name
     * @param string $indexName Index name
     * @return bool
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $result = $connection->select(
            "SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$database, $table, $indexName]
        );

        return $result[0]->count > 0;
    }
};
