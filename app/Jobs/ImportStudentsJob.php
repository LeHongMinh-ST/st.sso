<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Imports\StudentsImport;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Job for importing students from Excel file.
 * Refactored to use DDD Use Cases through StudentsImport class.
 */
class ImportStudentsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
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
            Log::info("ImportStudentsJob started: " . $this->filePath);
            // Create StudentsImport instance with required parameters
            // StudentsImport will use app() helper internally to resolve Use Cases
            $studentsImport = new StudentsImport($this->facultyId, $this->userId);
            Excel::import($studentsImport, $this->filePath);
        } catch (Exception $e) {
            Log::error("ImportStudentsJob failed: " . $e->getMessage());
            throw $e;
        }
    }
}
