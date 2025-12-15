<?php

declare(strict_types=1);

namespace App\OrganizationalStructure\Infrastructure\Imports;

use App\IdentityAccess\Domain\Enums\Role;
use App\OrganizationalStructure\Infrastructure\Eloquent\User;
use App\SharedKernel\Domain\Enums\Status;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterChunk;
use Maatwebsite\Excel\Events\ImportFailed;
use Throwable;

/**
 * Legacy chunk-based import (OrganizationalStructure context).
 * Kept for compatibility with Livewire import workflow.
 */
final class StudentsImportChunk implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, ShouldQueue, WithBatchInserts, WithEvents
{
    private int $facultyId;
    private int $errors = 0;
    private int $userId;

    public function __construct(int $facultyId, int $userId)
    {
        $this->facultyId = $facultyId;
        $this->userId = $userId;
    }

    public function registerEvents(): array
    {
        return [
            ImportFailed::class => function (ImportFailed $event): void {
                Log::error('Import failed: ' . $event->getException()->getMessage());
            },
            AfterChunk::class => function (AfterChunk $event): void {
                Log::info('After chunk: ' . $event->chunk()->count());
            },
        ];
    }

    /**
     * Handle each row as a model instance.
     * This method will be called for each row in the import file.
     *
     * @param array $row
     * @return User|null
     */
    public function model(array $row): ?User
    {
        try {
            // Check if user exists by code
            $user = User::where('code', $row['ma_sinh_vien'])->first();
            if ($user) {
                return null; // Skip existing
            }

            return new User([
                'user_name' => $row['email'],
                'first_name' => $row['ten'],
                'last_name' => $row['ho'],
                'email' => $row['email'],
                'password' => 'password',
                'phone' => $row['so_dien_thoai'] ?? null,
                'role' => Role::Student->value,
                'status' => Status::Active->value,
                'faculty_id' => $this->facultyId,
                'code' => $row['ma_sinh_vien'],
                'is_change_password' => false,
            ]);
        } catch (Throwable $e) {
            Log::error('Lỗi khi xử lý dòng: ' . $e->getMessage(), ['row' => $row]);
            $this->errors++;
            return null;
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 50;
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
}
