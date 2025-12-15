<?php

declare(strict_types=1);

namespace App\Imports;

use App\Events\ImportProgressUpdated;
use App\IdentityAccess\Domain\Enums\Role;
use App\Notifications\ImportCompleted;
use App\OrganizationalStructure\Application\UseCases\ImportUsersFromExcelUseCase;
use App\OrganizationalStructure\Domain\Repositories\FacultyRepositoryInterface;
use App\OrganizationalStructure\Domain\ValueObjects\FacultyId;
use App\OrganizationalStructure\Infrastructure\Eloquent\Faculty;
use App\OrganizationalStructure\Infrastructure\Eloquent\User;
use App\SharedKernel\Domain\Enums\Status;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use RuntimeException;
use Throwable;

/**
 * Students import class refactored to use DDD Use Cases.
 * Password and Role handling is temporary until IdentityAccess context is implemented (Phase 3).
 */
class StudentsImport implements ToCollection, WithHeadingRow, WithValidation
{
    private int $facultyId;
    private int $imported = 0;
    private int $errors = 0;
    private int $userId;
    private int $totalRows = 0;
    private int $processedRows = 0;
    private ?string $facultyUuid = null;

    /**
     * Note: ImportUsersFromExcelUseCase and FacultyRepositoryInterface are resolved via app() helper
     * because Maatwebsite\Excel import classes don't support constructor injection properly.
     */
    public function __construct(
        int $facultyId,
        int $userId,
    ) {
        $this->facultyId = $facultyId;
        $this->userId = $userId;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows): void
    {
        try {
            $this->totalRows += $rows->count();
            Log::info("Total rows: " . $this->totalRows);

            // Get faculty UUID from integer ID
            $this->facultyUuid = $this->getFacultyUuid();

            if (null === $this->facultyUuid) {
                throw new RuntimeException("Faculty with ID {$this->facultyId} not found");
            }

            // Process rows in batches for progress tracking
            $batchSize = 10;
            $batches = $rows->chunk($batchSize);

            foreach ($batches as $batch) {
                try {
                    // Use Use Case to import users (OrganizationalStructure part)
                    $result = $this->getImportUsersUseCase()->execute($batch, $this->facultyUuid);

                    // Handle password and role for new users (temporary until Phase 3)
                    $this->handlePasswordAndRole($batch, $result['imported']);

                    $this->imported += $result['imported'];
                    $this->errors += $result['errors'];
                    $this->processedRows += $batch->count();

                    // Broadcast progress
                    if (0 === $this->processedRows % 10 || $this->totalRows < 10) {
                        $this->broadcastProgress();
                    }
                } catch (Throwable $e) {
                    Log::error('Error processing batch: ' . $e->getMessage());
                    $this->errors += $batch->count();
                    $this->processedRows += $batch->count();
                }
            }

            // Send notification
            $user = User::find($this->userId);
            if ($user) {
                Notification::send($user, new ImportCompleted($this->imported, $this->errors));
            }

            $this->broadcastCompletion();
        } catch (Throwable $e) {
            Log::error('Import failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'ho' => 'required|string|max:255',
            'ten' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'ma_sinh_vien' => 'required|string|max:50',
            'so_dien_thoai' => 'nullable|string|max:20',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'ho.required' => 'Họ sinh viên là bắt buộc',
            'ten.required' => 'Tên sinh viên là bắt buộc',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không đúng định dạng',
            'ma_sinh_vien.required' => 'Mã sinh viên là bắt buộc',
        ];
    }

    /**
     * Get ImportUsersFromExcelUseCase instance.
     * Using app() helper because import classes don't support constructor injection.
     *
     * @return ImportUsersFromExcelUseCase
     */
    private function getImportUsersUseCase(): ImportUsersFromExcelUseCase
    {
        return app(ImportUsersFromExcelUseCase::class);
    }

    /**
     * Get FacultyRepositoryInterface instance.
     *
     * @return FacultyRepositoryInterface
     */
    private function getFacultyRepository(): FacultyRepositoryInterface
    {
        return app(FacultyRepositoryInterface::class);
    }

    /**
     * Get faculty UUID from integer ID.
     *
     * @return string|null
     */
    private function getFacultyUuid(): ?string
    {
        try {
            // Try to find faculty by integer ID using Eloquent model
            $faculty = Faculty::find($this->facultyId);
            if (null === $faculty) {
                return null;
            }

            // Get UUID from faculty
            if (null !== $faculty->uuid) {
                return $faculty->uuid;
            }

            // If UUID column doesn't exist, convert integer ID to UUID
            // This is temporary until UUID migration is complete
            $facultyIdVO = FacultyId::fromString(
                $this->generateDeterministicUuid('faculties', $this->facultyId)
            );

            $facultyEntity = $this->getFacultyRepository()->findById($facultyIdVO);
            if (null !== $facultyEntity) {
                return $facultyEntity->id()->toString();
            }

            return null;
        } catch (Exception $e) {
            Log::error('Error getting faculty UUID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle password and role for imported users.
     * This is temporary until IdentityAccess context is implemented (Phase 3).
     *
     * @param Collection $rows Batch rows
     * @param int $importedCount Number of imported users
     * @return void
     */
    private function handlePasswordAndRole(Collection $rows, int $importedCount): void
    {
        // Get user codes from batch
        $codes = $rows->pluck('ma_sinh_vien')->filter()->unique()->toArray();

        if (empty($codes)) {
            return;
        }

        // Update password and role for users that were just created
        // Note: This is a temporary solution until IdentityAccess context is implemented
        User::whereIn('code', $codes)
            ->where('role', '!=', Role::Student->value)
            ->update([
                'password' => Hash::make('password'),
                'role' => Role::Student->value,
                'status' => Status::Active->value,
                'is_change_password' => false,
            ]);
    }

    /**
     * Generate deterministic UUID from integer ID.
     * Temporary helper until UUID migration is complete.
     *
     * @param string $table Table name
     * @param int $integerId Integer ID
     * @return string UUID string
     */
    private function generateDeterministicUuid(string $table, int $integerId): string
    {
        $namespace = \Ramsey\Uuid\Uuid::fromString('6ba7b810-9dad-11d1-80b4-00c04fd430c8');
        $name = "{$table}:{$integerId}";

        return \Ramsey\Uuid\Uuid::uuid5($namespace, $name)->toString();
    }

    private function broadcastProgress(): void
    {
        $progress = $this->totalRows > 0 ? round(($this->processedRows / $this->totalRows) * 100, 2) : 0;

        event(new ImportProgressUpdated($this->userId, [
            'type' => 'progress',
            'processed' => $this->processedRows,
            'total' => $this->totalRows,
            'percentage' => $progress,
            'imported' => $this->imported,
            'errors' => $this->errors,
        ]));
    }

    private function broadcastCompletion(): void
    {
        event(new ImportProgressUpdated($this->userId, [
            'type' => 'completed',
            'imported' => $this->imported,
            'errors' => $this->errors,
            'message' => "Đã nhập {$this->imported} sinh viên thành công" . ($this->errors > 0 ? ", {$this->errors} lỗi" : ""),
        ]));
    }
}
