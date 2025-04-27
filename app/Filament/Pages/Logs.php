<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\File;

class Logs extends Page
{
    public $selectedLog = null;
    public $logContent = '';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Nhật ký hệ thống';

    protected static ?string $title = 'Nhật ký hệ thống';

    protected static ?string $slug = 'logs';

    protected static ?int $navigationSort = 80;

    protected static ?string $navigationGroup = 'Hệ thống';

    protected static string $view = 'filament.pages.logs';

    public function mount(): void
    {
        $this->selectedLog = $this->getLogs()[0] ?? null;
        $this->loadLogContent();
    }

    public function getLogs()
    {
        $files = File::files(storage_path('logs'));

        return collect($files)
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                'path' => $file->getPathname(),
            ])
            ->sortByDesc('modified')
            ->values()
            ->toArray();
    }

    public function loadLogContent(): void
    {
        if ($this->selectedLog) {
            $content = File::get($this->selectedLog['path']);
            $this->logContent = $content;
        }
    }

    public function selectLog($logName): void
    {
        $this->selectedLog = collect($this->getLogs())
            ->firstWhere('name', $logName);

        $this->loadLogContent();
    }

    public function clearLog(): void
    {
        if ($this->selectedLog) {
            File::put($this->selectedLog['path'], '');
            $this->logContent = '';
            $this->notify('success', 'Đã xóa nội dung nhật ký.');
        }
    }

    public function downloadLog()
    {
        if ($this->selectedLog) {
            return response()->download($this->selectedLog['path']);
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
