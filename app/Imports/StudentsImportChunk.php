<?php

declare(strict_types=1);

namespace App\Imports;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class StudentsImportChunk implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, ShouldQueue, WithBatchInserts
{
    private int $facultyId;
    private int $imported = 0;
    private int $errors = 0;
    private int $userId;

    public function __construct(int $facultyId, int $userId)
    {
        $this->facultyId = $facultyId;
        $this->userId = $userId;
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
            Log::info('Processing row: ' . json_encode($row));
            // Check if user exists by code
            $user = User::where('code', $row['ma_sinh_vien'])->first();
            if ($user) {
                // Update existing user
                $user->update([
                    'user_name' => $row['email'],
                    'first_name' => $row['ten'],
                    'last_name' => $row['ho'],
                    'email' => $row['email'],
                    'phone' => $row['so_dien_thoai'] ?? null,
                    'faculty_id' => $this->facultyId,
                    'code' => $row['ma_sinh_vien'],
                ]);
                $this->imported++;
                return null; // No need to return model for update
            }
            $this->imported++;
            // Return new User model for insert
            return new User([
                'user_name' => $row['email'],
                'first_name' => $row['ten'],
                'last_name' => $row['ho'],
                'email' => $row['email'],
                'password' => Hash::make('password'),
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

    /**
     * Specify the chunk size for reading the file.
     * This helps to process large files efficiently by splitting them into smaller parts.
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 50; // You can adjust this number based on your server capacity
    }

    /**
     * Specify the batch size for bulk inserts.
     * This improves performance when inserting multiple records.
     *
     * @return int
     */
    public function batchSize(): int
    {
        return 50; // Should match chunkSize for optimal performance
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
