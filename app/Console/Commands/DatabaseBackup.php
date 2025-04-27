<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use ZipArchive;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup {--database=mysql} {--destination=local} {--destinationPath=} {--compression=zip}';

    protected $description = 'Backup database to a file';

    public function handle()
    {
        $database = $this->option('database');
        $destination = $this->option('destination');
        $destinationPath = $this->option('destinationPath');
        $compression = $this->option('compression');

        $this->info('Backing up database...');

        // Tạo thư mục backups nếu chưa tồn tại
        if (!Storage::disk($destination)->exists('backups') && !$destinationPath) {
            Storage::disk($destination)->makeDirectory('backups');
        }

        // Tạo tên file backup nếu không được chỉ định
        if (!$destinationPath) {
            $destinationPath = 'backups/backup-' . date('Y-m-d-H-i-s') . '.sql';
        }

        // Lấy thông tin kết nối database
        $host = config("database.connections.{$database}.host");
        $port = config("database.connections.{$database}.port");
        $dbName = config("database.connections.{$database}.database");
        $username = config("database.connections.{$database}.username");
        $password = config("database.connections.{$database}.password");

        // Tạo lệnh mysqldump
        $command = "mysqldump --host={$host} --port={$port} --user={$username} --password={$password} {$dbName} > " . storage_path("app/{$destinationPath}");

        // Thực thi lệnh
        $process = Process::fromShellCommandline($command);
        $process->run();

        // Kiểm tra kết quả
        if (!$process->isSuccessful()) {
            $this->error('Database backup failed: ' . $process->getErrorOutput());
            return 1;
        }

        // Nén file nếu cần
        if ('zip' === $compression) {
            $zipPath = str_replace('.sql', '.zip', storage_path("app/{$destinationPath}"));
            $zip = new ZipArchive();

            if (true === $zip->open($zipPath, ZipArchive::CREATE)) {
                $zip->addFile(storage_path("app/{$destinationPath}"), basename($destinationPath));
                $zip->close();

                // Xóa file .sql sau khi nén
                unlink(storage_path("app/{$destinationPath}"));

                // Cập nhật đường dẫn
                $destinationPath = str_replace('.sql', '.zip', $destinationPath);
            } else {
                $this->error('Failed to create zip file');
                return 1;
            }
        }

        $this->info('Database backup completed successfully: ' . $destinationPath);

        return 0;
    }
}
