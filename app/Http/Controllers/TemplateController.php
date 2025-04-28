<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TemplateController extends Controller
{
    public function downloadTeachersTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Đặt tiêu đề cho các cột
        $sheet->setCellValue('A1', 'ho');
        $sheet->setCellValue('B1', 'ten');
        $sheet->setCellValue('C1', 'email');
        $sheet->setCellValue('D1', 'so_dien_thoai');
        $sheet->setCellValue('E1', 'ma_can_bo');

        // Thêm một số dữ liệu mẫu
        $sheet->setCellValue('A2', 'Nguyễn');
        $sheet->setCellValue('B2', 'Văn A');
        $sheet->setCellValue('C2', 'giangvien1@example.com');
        $sheet->setCellValue('D2', '0123456789');
        $sheet->setCellValue('E2', 'CB001');

        // Thêm chú thích
        $sheet->setCellValue('A4', 'Lưu ý: Tên đăng nhập sẽ được tạo tự động từ email');
        $sheet->mergeCells('A4:E4');

        // Tạo file Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'import_teachers_template.xlsx';
        $tempPath = storage_path('app/public/' . $filename);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function downloadStudentsTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Đặt tiêu đề cho các cột
        $sheet->setCellValue('A1', 'ho');
        $sheet->setCellValue('B1', 'ten');
        $sheet->setCellValue('C1', 'email');
        $sheet->setCellValue('D1', 'so_dien_thoai');
        $sheet->setCellValue('E1', 'ma_sinh_vien');

        // Thêm một số dữ liệu mẫu
        $sheet->setCellValue('A2', 'Trần');
        $sheet->setCellValue('B2', 'Thị B');
        $sheet->setCellValue('C2', 'sinhvien1@example.com');
        $sheet->setCellValue('D2', '0987654321');
        $sheet->setCellValue('E2', 'SV001');

        // Thêm chú thích
        $sheet->setCellValue('A4', 'Lưu ý: Tên đăng nhập sẽ được tạo tự động từ mã sinh viên');
        $sheet->mergeCells('A4:E4');

        // Tạo file Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'import_students_template.xlsx';
        $tempPath = storage_path('app/public/' . $filename);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
