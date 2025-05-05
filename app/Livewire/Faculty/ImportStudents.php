<?php

declare(strict_types=1);

namespace App\Livewire\Faculty;

use App\Imports\StudentsImport;
use App\Models\Faculty;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ImportStudents extends Component
{
    use WithFileUploads;

    public Faculty $faculty;
    public $file;
    public bool $showImportForm = false;
    private bool $isLoading = false;

    public function render()
    {
        return view('livewire.faculty.import-students');
    }

    public function mount(Faculty $faculty): void
    {
        $this->faculty = $faculty;
    }

    public function toggleImportForm(): void
    {
        if (!auth()->user()->can('create', \App\Models\User::class)) {
            return;
        }
        $this->showImportForm = !$this->showImportForm;
    }

    public function import(): void
    {
        if ($this->isLoading) {
            return;
        }

        if (!auth()->user()->can('create', \App\Models\User::class)) {
            $this->dispatch('alert', type: 'error', message: 'Bạn không có quyền nhập sinh viên!');
            return;
        }

        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // max 5MB
        ], [
            'file.required' => 'Vui lòng chọn file Excel',
            'file.file' => 'Vui lòng chọn file Excel hợp lệ',
            'file.mimes' => 'File phải có định dạng xlsx, xls hoặc csv',
            'file.max' => 'Kích thước file không được vượt quá 5MB',
        ]);

        try {
            $this->isLoading = true;
            Excel::import(new StudentsImport($this->faculty->id), $this->file);

            $importResult = session('import_result', ['imported' => 0, 'errors' => 0]);
            $message = 'Đã nhập ' . $importResult['imported'] . ' sinh viên thành công';
            if ($importResult['errors'] > 0) {
                $message .= ', ' . $importResult['errors'] . ' lỗi';
            }

            $this->dispatch('alert', type: 'success', message: $message);
            $this->reset('file');
            $this->showImportForm = false;
            $this->dispatch('studentsImported');
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Nhập sinh viên thất bại: ' . $th->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function closeImportForm(): void
    {
        $this->showImportForm = false;
        $this->reset('file');
        $this->dispatch('closeImportForm');
    }
}
