<?php

declare(strict_types=1);

namespace App\Livewire\Faculty;

use App\Imports\StudentsImportChunk;
use App\Jobs\ImportStudentsJob;
use App\Models\Faculty;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
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
    public bool $isImporting = false;
    public int|float $importProgress = 0;
    public int $importedCount = 0;
    public int $errorCount = 0;
    public string $importStatus = '';
    public $userId;
    private bool $isLoading = false;

    public function render()
    {
        return view('livewire.faculty.import-students');
    }

    public function mount(Faculty $faculty): void
    {
        $this->faculty = $faculty;
        $this->userId = auth()->id();
    }

    public function toggleImportForm(): void
    {
        if (!auth()->user()->can('create', \App\Models\User::class)) {
            return;
        }
        $this->showImportForm = !$this->showImportForm;
        $this->resetImportState();
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
            $this->isImporting = true;
            $this->importProgress = 0;
            $this->importStatus = 'Đang tải file lên và bắt đầu xử lý...';

            // Store file and dispatch job
            $path = $this->file->store('imports');
            ImportStudentsJob::dispatch($this->faculty->id, auth()->id(), $path)->onQueue('import');
            Excel::queueImport(
                new StudentsImportChunk($this->faculty->id, auth()->id()),
                $path
            );

            $this->dispatch('alert', type: 'success', message: 'File đã được tải lên và đang được xử lý. Theo dõi tiến trình bên dưới.');
            $this->dispatch('importStarted');
            $this->reset('file');
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('alert', type: 'error', message: 'Tải file thất bại: ' . $th->getMessage());
            $this->resetImportState();
        } finally {
            $this->isLoading = false;
        }
    }

    #[On('echo:import.progress.{userId},.import.progress.updated')]
    public function updateProgress($data): void
    {
        if ('progress' === $data['type']) {
            $this->importProgress = $data['percentage'];
            $this->importedCount = $data['imported'];
            $this->errorCount = $data['errors'];
            $this->importStatus = "Đang xử lý: {$data['processed']}/{$data['total']} bản ghi ({$data['percentage']}%)";
        } elseif ('completed' === $data['type']) {
            $this->importProgress = 100;
            $this->importedCount = $data['imported'];
            $this->errorCount = $data['errors'];
            $this->importStatus = $data['message'];
            $this->isImporting = false;

            // Dispatch events to refresh data
            $this->dispatch('studentsImported');
            $this->dispatch('refreshStudentsList');

            // Show completion alert
            $this->dispatch('alert', type: 'success', message: $data['message']);

            // Auto close form after 3 seconds
            $this->dispatch('autoCloseImportForm');
            $this->dispatch('importCompleted');
        }
    }

    public function checkImportProgress(): void
    {
        // This is a fallback method for when Echo is not available
        // You can implement polling logic here if needed
        // For now, we'll rely on the notification system
    }

    public function closeImportForm(): void
    {
        $this->showImportForm = false;
        $this->resetImportState();
        $this->reset('file');
        $this->dispatch('closeImportForm');
    }

    private function resetImportState(): void
    {
        $this->isImporting = false;
        $this->importProgress = 0;
        $this->importedCount = 0;
        $this->errorCount = 0;
        $this->importStatus = '';
    }
}
