<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kiểm tra xem bảng departments có tồn tại không
        if (!Schema::hasTable('departments')) {
            $this->command->info('Bảng departments không tồn tại. Bỏ qua seeding.');
            return;
        }

        // Lấy faculty đầu tiên để gán cho departments
        $faculty = Faculty::first();

        if (!$faculty) {
            $this->command->info('Không tìm thấy faculty nào. Vui lòng chạy FacultySeeder trước.');
            return;
        }

        // Tạo các departments
        $departments = [
            [
                'name' => 'Công nghệ phần mềm',
                'status' => Status::Active,
                'faculty_id' => $faculty->id,
                'description' => 'Bộ môn Công nghệ phần mềm',
            ],
            [
                'name' => 'Mạng máy tính',
                'status' => Status::Active,
                'faculty_id' => $faculty->id,
                'description' => 'Bộ môn Mạng máy tính',
            ],
        ];

        foreach ($departments as $departmentData) {
            Department::create($departmentData);
        }

        $this->command->info('Đã tạo thành công các bộ môn.');
    }
}
