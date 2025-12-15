<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\IdentityAccess\Infrastructure\Eloquent\Permission;
use App\IdentityAccess\Infrastructure\Eloquent\Role;
use App\SharedKernel\Domain\ValueObjects\Uuid;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Command to populate UUID columns for existing records in IdentityAccess tables.
 * This command should be run after adding UUID columns to the database.
 *
 * Tables to populate:
 * - roles
 * - permissions
 * - permission_groups (if exists)
 * - clients (Laravel Passport)
 * - user_roles (pivot table)
 * - role_permissions (pivot table)
 */
final class PopulateIdentityAccessUuids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:populate-identity-access-uuids
                            {--dry-run : Run without making changes}
                            {--force : Force the operation to run even if UUIDs already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate UUID columns for existing records in IdentityAccess tables (roles, permissions, clients, pivot tables)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($dryRun) {
            $this->warn('Running in DRY-RUN mode. No changes will be made.');
        }

        $this->info('Populating UUIDs for IdentityAccess tables...');

        try {
            DB::transaction(function () use ($dryRun, $force): void {
                // Populate core tables
                $this->populateTable(Role::class, 'roles', $dryRun, $force);
                $this->populateTable(Permission::class, 'permissions', $dryRun, $force);

                // Populate permission_groups if table exists
                if (Schema::hasTable('permission_groups')) {
                    $this->populateTable(\App\Models\PermissionGroup::class, 'permission_groups', $dryRun, $force);
                }

                // Populate clients (Laravel Passport uses string IDs, but we can add UUID)
                if (Schema::hasTable('oauth_clients') && Schema::hasColumn('oauth_clients', 'uuid')) {
                    $this->populateClientsTable($dryRun, $force);
                }

                // Populate foreign key UUIDs
                $this->populateForeignKeys($dryRun, $force);

                // Populate pivot table UUIDs
                $this->populatePivotTables($dryRun, $force);
            });

            $this->info('UUIDs populated successfully!');

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error populating UUIDs: ' . $e->getMessage());
            $this->error($e->getTraceAsString());

            return Command::FAILURE;
        }
    }

    /**
     * Populate UUIDs for a table.
     *
     * @param string $modelClass Model class name
     * @param string $tableName Table name
     * @param bool $dryRun Whether to run in dry-run mode
     * @param bool $force Whether to force update existing UUIDs
     * @return void
     */
    private function populateTable(string $modelClass, string $tableName, bool $dryRun, bool $force): void
    {
        if (!Schema::hasTable($tableName)) {
            $this->warn("Table {$tableName} does not exist. Skipping.");
            return;
        }

        if (!Schema::hasColumn($tableName, 'uuid')) {
            $this->warn("Table {$tableName} does not have uuid column. Skipping.");
            return;
        }

        $this->info("Populating UUIDs for {$tableName}...");

        $query = $modelClass::query();
        if (!$force) {
            $query->whereNull('uuid');
        }

        $count = $query->count();
        $this->line("Found {$count} records to process.");

        if (0 === $count) {
            $this->line("No records to process for {$tableName}.");
            return;
        }

        if ($dryRun) {
            $this->line("Would populate UUIDs for {$count} records in {$tableName}.");
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->chunk(100, function ($records) use ($tableName, $bar, $force): void {
            foreach ($records as $record) {
                if (null === $record->uuid || $force) {
                    DB::table($tableName)
                        ->where('id', $record->id)
                        ->update(['uuid' => Uuid::generate()->toString()]);
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
    }

    /**
     * Populate UUIDs for oauth_clients table.
     * Note: Laravel Passport uses string IDs, so we need special handling.
     *
     * @param bool $dryRun Whether to run in dry-run mode
     * @param bool $force Whether to force update existing UUIDs
     * @return void
     */
    private function populateClientsTable(bool $dryRun, bool $force): void
    {
        $this->info('Populating UUIDs for oauth_clients...');

        $query = DB::table('oauth_clients');
        if (!$force) {
            $query->whereNull('uuid');
        }

        $count = $query->count();
        $this->line("Found {$count} records to process.");

        if (0 === $count) {
            $this->line('No records to process for oauth_clients.');
            return;
        }

        if ($dryRun) {
            $this->line("Would populate UUIDs for {$count} records in oauth_clients.");
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->chunk(100, function ($clients) use ($bar, $force): void {
            foreach ($clients as $client) {
                if (null === $client->uuid || $force) {
                    DB::table('oauth_clients')
                        ->where('id', $client->id)
                        ->update(['uuid' => Uuid::generate()->toString()]);
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
    }

    /**
     * Populate foreign key UUIDs.
     *
     * @param bool $dryRun Whether to run in dry-run mode
     * @param bool $force Whether to force update existing UUIDs
     * @return void
     */
    private function populateForeignKeys(bool $dryRun, bool $force): void
    {
        $this->info('Populating foreign key UUIDs...');

        // Populate permissions.permission_group_uuid from permission_groups.uuid
        if (Schema::hasTable('permissions') &&
            Schema::hasColumn('permissions', 'permission_group_id') &&
            Schema::hasColumn('permissions', 'permission_group_uuid') &&
            Schema::hasTable('permission_groups')) {
            $this->populateForeignKey(
                'permissions',
                'permission_group_id',
                'permission_group_uuid',
                'permission_groups',
                'id',
                'uuid',
                $dryRun,
                $force,
            );
        }
    }

    /**
     * Populate pivot table UUIDs.
     *
     * @param bool $dryRun Whether to run in dry-run mode
     * @param bool $force Whether to force update existing UUIDs
     * @return void
     */
    private function populatePivotTables(bool $dryRun, bool $force): void
    {
        $this->info('Populating pivot table UUIDs...');

        // Populate user_roles pivot table
        if (Schema::hasTable('user_roles')) {
            $this->populatePivotTable(
                'user_roles',
                'user_id',
                'user_uuid',
                'users',
                'id',
                'uuid',
                $dryRun,
                $force,
            );

            $this->populatePivotTable(
                'user_roles',
                'role_id',
                'role_uuid',
                'roles',
                'id',
                'uuid',
                $dryRun,
                $force,
            );
        }

        // Populate role_permissions pivot table
        if (Schema::hasTable('role_permissions')) {
            $this->populatePivotTable(
                'role_permissions',
                'role_id',
                'role_uuid',
                'roles',
                'id',
                'uuid',
                $dryRun,
                $force,
            );

            $this->populatePivotTable(
                'role_permissions',
                'permission_id',
                'permission_uuid',
                'permissions',
                'id',
                'uuid',
                $dryRun,
                $force,
            );
        }
    }

    /**
     * Populate a foreign key UUID column.
     *
     * @param string $tableName Table name
     * @param string $foreignKeyColumn Foreign key column name (integer ID)
     * @param string $uuidColumn UUID column name
     * @param string $referencedTable Referenced table name
     * @param string $referencedIdColumn Referenced ID column name
     * @param string $referencedUuidColumn Referenced UUID column name
     * @param bool $dryRun Whether to run in dry-run mode
     * @param bool $force Whether to force update existing UUIDs
     * @return void
     */
    private function populateForeignKey(
        string $tableName,
        string $foreignKeyColumn,
        string $uuidColumn,
        string $referencedTable,
        string $referencedIdColumn,
        string $referencedUuidColumn,
        bool $dryRun,
        bool $force,
    ): void {
        if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, $uuidColumn)) {
            return;
        }

        if (!Schema::hasTable($referencedTable) || !Schema::hasColumn($referencedTable, $referencedUuidColumn)) {
            return;
        }

        $query = DB::table($tableName)
            ->join($referencedTable, "{$tableName}.{$foreignKeyColumn}", '=', "{$referencedTable}.{$referencedIdColumn}")
            ->whereNotNull("{$tableName}.{$foreignKeyColumn}");

        if (!$force) {
            $query->whereNull("{$tableName}.{$uuidColumn}");
        }

        $count = $query->count();

        if (0 === $count) {
            $this->line("No foreign key UUIDs to populate for {$tableName}.{$uuidColumn}.");
            return;
        }

        if ($dryRun) {
            $this->line("Would populate {$count} foreign key UUIDs for {$tableName}.{$uuidColumn}.");
            return;
        }

        DB::table($tableName)
            ->join($referencedTable, "{$tableName}.{$foreignKeyColumn}", '=', "{$referencedTable}.{$referencedIdColumn}")
            ->whereNotNull("{$tableName}.{$foreignKeyColumn}")
            ->when(!$force, fn ($q) => $q->whereNull("{$tableName}.{$uuidColumn}"))
            ->update([
                "{$tableName}.{$uuidColumn}" => DB::raw("{$referencedTable}.{$referencedUuidColumn}"),
            ]);

        $this->line("Populated {$count} foreign key UUIDs for {$tableName}.{$uuidColumn}.");
    }

    /**
     * Populate a pivot table UUID column.
     *
     * @param string $pivotTable Pivot table name
     * @param string $foreignKeyColumn Foreign key column name (integer ID)
     * @param string $uuidColumn UUID column name
     * @param string $referencedTable Referenced table name
     * @param string $referencedIdColumn Referenced ID column name
     * @param string $referencedUuidColumn Referenced UUID column name
     * @param bool $dryRun Whether to run in dry-run mode
     * @param bool $force Whether to force update existing UUIDs
     * @return void
     */
    private function populatePivotTable(
        string $pivotTable,
        string $foreignKeyColumn,
        string $uuidColumn,
        string $referencedTable,
        string $referencedIdColumn,
        string $referencedUuidColumn,
        bool $dryRun,
        bool $force,
    ): void {
        if (!Schema::hasColumn($pivotTable, $uuidColumn)) {
            return;
        }

        $this->populateForeignKey(
            $pivotTable,
            $foreignKeyColumn,
            $uuidColumn,
            $referencedTable,
            $referencedIdColumn,
            $referencedUuidColumn,
            $dryRun,
            $force,
        );
    }
}
