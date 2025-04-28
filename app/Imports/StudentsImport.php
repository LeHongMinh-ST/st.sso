<?php

declare(strict_types=1);

namespace App\Imports;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $facultyId;

    public function __construct(int $facultyId)
    {
        $this->facultyId = $facultyId;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            // Kiểm tra mã sinh viên có tồn tại không
            if (empty($row['ma_sinh_vien'])) {
                throw new Exception('Mã sinh viên không được để trống');
            }

            // Lấy mã sinh viên làm tên đăng nhập
            $userName = $row['ma_sinh_vien'];

            // Kiểm tra xem tên đăng nhập đã tồn tại chưa
            $count = 0;
            $originalUserName = $userName;
            while (User::where('user_name', $userName)->exists()) {
                $count++;
                $userName = $originalUserName . $count;
            }

            User::create([
                'user_name' => $userName,
                'first_name' => $row['ten'],
                'last_name' => $row['ho'],
                'email' => $row['email'],
                'phone' => $row['so_dien_thoai'] ?? null,
                'code' => $row['ma_sinh_vien'],
                'password' => Hash::make('password'),
                'role' => Role::Student->value,
                'status' => Status::Active->value,
                'faculty_id' => $this->facultyId,
                'is_change_password' => false,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            '*.ten' => ['required', 'string', 'max:255'],
            '*.ho' => ['required', 'string', 'max:255'],
            '*.email' => ['required', 'email', 'max:255', 'unique:users,email'],
            '*.so_dien_thoai' => ['nullable', 'string', 'max:255'],
            '*.ma_sinh_vien' => ['required', 'string', 'max:255'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.ten.required' => 'Tên là bắt buộc',
            '*.ho.required' => 'Họ là bắt buộc',
            '*.email.required' => 'Email là bắt buộc',
            '*.email.email' => 'Email không hợp lệ',
            '*.email.unique' => 'Email đã tồn tại',
            '*.ma_sinh_vien.required' => 'Mã sinh viên là bắt buộc',
        ];
    }
}
