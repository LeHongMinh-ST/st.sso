<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Jobs;

use App\OrganizationalStructure\Infrastructure\Imports\StudentsImport;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Job for importing students from Excel file (OrganizationalStructure context).
 * Delegates import logic to StudentsImport which uses DDD Use Cases.
 */
final class ImportStudentsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly int $facultyId,
        private readonly int $userId,
        private readonly string $filePath
    ) {
    }

    /**
     * Execute the job.
     * StudentsImport class will handle the actual import using DDD Use Cases.
     * Note: StudentsImport is instantiated manually because it needs constructor parameters.
     */
    public function handle(): void
    {
        try {
            Log::info('ImportStudentsJob started: ' . $this->filePath);
            $studentsImport = new StudentsImport($this->facultyId, $this->userId);
            Excel::import($studentsImport, $this->filePath);
        } catch (Exception $e) {
            Log::error('ImportStudentsJob failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
