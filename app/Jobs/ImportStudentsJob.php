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
     */
    public function handle(): void
    {
        try {
            Log::info("ImportStudentsJob started: " . $this->filePath);
            Excel::import(
                new StudentsImport($this->facultyId, $this->userId),
                $this->filePath
            );
        } catch (Exception $e) {
            Log::error("ImportStudentsJob failed: " . $e->getMessage());
            throw $e;
        }
    }
}
