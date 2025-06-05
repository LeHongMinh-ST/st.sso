<?php

declare(strict_types=1);

namespace App\Imports;

use App\Enums\Role;
use App\Enums\Status;
use App\Events\ImportProgressUpdated;
use App\Models\User;
use App\Notifications\ImportCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class StudentsImport implements ToCollection, WithHeadingRow, ShouldQueue, WithValidation, WithChunkReading
{
    private int $facultyId;
    private int $imported = 0;
    private int $errors = 0;
    private int $userId;
    private int $totalRows = 0;
    private int $processedRows = 0;

    public function __construct(int $facultyId, int $userId)
    {
        $this->facultyId = $facultyId;
        $this->userId = $userId;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows): void
    {
        $this->totalRows += $rows->count();

        foreach ($rows as $index => $row) {
            try {
                // Check if user already exists by email or code
                $existingUser = User::where('email', $row['email'])
                    ->orWhere('code', $row['ma_sinh_vien'])
                    ->first();

                if ($existingUser) {
                    // Update existing user
                    $existingUser->update([
                        'user_name' => $row['email'],
                        'first_name' => $row['ten'],
                        'last_name' => $row['ho'],
                        'email' => $row['email'],
                        'phone' => $row['so_dien_thoai'] ?? null,
                        'faculty_id' => $this->facultyId,
                        'code' => $row['ma_sinh_vien'],
                    ]);
                    $this->imported++;
                } else {
                    // Create new user
                    $user = User::create([
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

                    // Assign student role
                    $user->assignRole('student');
                    $this->imported++;
                }
            } catch (Throwable $e) {
                Log::error('Error importing student: ' . $e->getMessage());
                $this->errors++;
            }

            $this->processedRows++;

            // Broadcast progress every 10 records
            if (0 === $this->processedRows % 10) {
                $this->broadcastProgress();
            }
        }

        // Send final notification when import is complete
        $user = User::find($this->userId);
        Notification::send($user, new ImportCompleted($this->imported, $this->errors));

        // Broadcast completion
        $this->broadcastCompletion();
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

    public function chunkSize(): int
    {
        return 100;
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
            'errors' => $this->errors
        ]));
    }

    private function broadcastCompletion(): void
    {
        event(new ImportProgressUpdated($this->userId, [
            'type' => 'completed',
            'imported' => $this->imported,
            'errors' => $this->errors,
            'message' => "Đã nhập {$this->imported} sinh viên thành công" . ($this->errors > 0 ? ", {$this->errors} lỗi" : "")
        ]));
    }
}
