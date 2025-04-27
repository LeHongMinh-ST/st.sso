<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Exception;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class Backups extends Page
{
    public $isCreating = false;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Sao lưu & Phục hồi';

    protected static ?string $title = 'Sao lưu & Phục hồi';

    protected static ?string $slug = 'backups';

    protected static ?int $navigationSort = 70;

    protected static ?string $navigationGroup = 'Hệ thống';

    protected static string $view = 'filament.pages.backups';

    public function getBackups()
    {
        if (!Storage::disk('local')->exists('backups')) {
            return [];
        }

        $files = Storage::disk('local')->files('backups');

        return collect($files)
            ->filter(fn ($file) => str_ends_with($file, '.zip'))
            ->map(fn ($file) => [
                'name' => basename($file),
                'size' => $this->formatBytes(Storage::disk('local')->size($file)),
                'date' => date('Y-m-d H:i:s', Storage::disk('local')->lastModified($file)),
                'path' => $file,
            ])
            ->sortByDesc('date')
            ->values()
            ->toArray();
    }

    public function createBackup(): void
    {
        $this->isCreating = true;

        try {
            // Tạo thư mục backups nếu chưa tồn tại
            if (!Storage::disk('local')->exists('backups')) {
                Storage::disk('local')->makeDirectory('backups');
            }

            // Tạo tên file backup
            $filename = 'backup-' . date('Y-m-d-H-i-s') . '.zip';
            $path = storage_path('app/backups/' . $filename);

            // Tạo backup database
            Artisan::call('db:backup', [
                '--database' => 'mysql',
                '--destination' => 'local',
                '--destinationPath' => 'backups/' . $filename,
                '--compression' => 'zip',
            ]);

            $this->notify('success', 'Đã tạo bản sao lưu thành công.');
        } catch (Exception $e) {
            $this->notify('danger', 'Lỗi khi tạo bản sao lưu: ' . $e->getMessage());
        }

        $this->isCreating = false;
    }

    public function downloadBackup($path)
    {
        return Storage::disk('local')->download($path);
    }

    public function deleteBackup($path): void
    {
        try {
            Storage::disk('local')->delete($path);
            $this->notify('success', 'Đã xóa bản sao lưu thành công.');
        } catch (Exception $e) {
            $this->notify('danger', 'Lỗi khi xóa bản sao lưu: ' . $e->getMessage());
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
