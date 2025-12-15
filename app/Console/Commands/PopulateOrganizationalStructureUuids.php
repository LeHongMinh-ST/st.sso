<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\OrganizationalStructure\Infrastructure\Eloquent\Department;
use App\OrganizationalStructure\Infrastructure\Eloquent\Faculty;
use App\OrganizationalStructure\Infrastructure\Eloquent\User;
use App\SharedKernel\Domain\ValueObjects\Uuid;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Command to populate UUID columns for existing records in OrganizationalStructure tables.
 * This command should be run after adding UUID columns to the database.
 */
final class PopulateOrganizationalStructureUuids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:populate-organizational-structure-uuids
                            {--dry-run : Run without making changes}
                            {--force : Force the operation to run even if UUIDs already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate UUID columns for existing records in OrganizationalStructure tables (users, faculties, departments)';

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

        $this->info('Populating UUIDs for OrganizationalStructure tables...');

        try {
            DB::transaction(function () use ($dryRun, $force): void {
                // Populate core tables
                $this->populateTable(Faculty::class, 'faculties', $dryRun, $force);
                $this->populateTable(Department::class, 'departments', $dryRun, $force);
                $this->populateTable(User::class, 'users', $dryRun, $force);

                // Populate foreign key UUIDs
                $this->populateForeignKeys($dryRun, $force);
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

        $query->chunk(100, function ($records) use ($tableName, $bar): void {
            foreach ($records as $record) {
                if (null === $record->uuid || $this->option('force')) {
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
     * Populate foreign key UUIDs.
     *
     * @param bool $dryRun Whether to run in dry-run mode
     * @param bool $force Whether to force update existing UUIDs
     * @return void
     */
    private function populateForeignKeys(bool $dryRun, bool $force): void
    {
        $this->info('Populating foreign key UUIDs...');

        // Populate users.faculty_uuid from faculties.uuid
        $this->populateForeignKey(
            'users',
            'faculty_id',
            'faculty_uuid',
            'faculties',
            'id',
            'uuid',
            $dryRun,
            $force,
        );

        // Populate users.department_uuid from departments.uuid
        $this->populateForeignKey(
            'users',
            'department_id',
            'department_uuid',
            'departments',
            'id',
            'uuid',
            $dryRun,
            $force,
        );

        // Populate departments.faculty_uuid from faculties.uuid
        $this->populateForeignKey(
            'departments',
            'faculty_id',
            'faculty_uuid',
            'faculties',
            'id',
            'uuid',
            $dryRun,
            $force,
        );
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
}
