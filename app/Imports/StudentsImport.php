<?php

declare(strict_types=1);

namespace App\Imports;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class StudentsImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $facultyId;

    public function __construct(int $facultyId)
    {
        $this->facultyId = $facultyId;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows): void
    {
        $importedCount = 0;
        $errorCount = 0;

        foreach ($rows as $row) {
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
                    $importedCount++;
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
                    $importedCount++;
                }
            } catch (Throwable $e) {
                Log::error('Error importing student: ' . $e->getMessage());
                $errorCount++;
            }
        }

        session()->flash('import_result', [
            'imported' => $importedCount,
            'errors' => $errorCount,
        ]);
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
