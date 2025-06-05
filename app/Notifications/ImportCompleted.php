<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ImportCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    private int $imported;
    private int $errors;

    public function __construct(int $imported, int $errors)
    {
        $this->imported = $imported;
        $this->errors = $errors;
    }

    public function via($notifiable): array
    {
        return ['broadcast', 'database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Đã nhập {$this->imported} sinh viên thành công" . ($this->errors > 0 ? ", {$this->errors} lỗi" : ""),
            'type' => 'import_completed'
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => "Đã nhập {$this->imported} sinh viên thành công" . ($this->errors > 0 ? ", {$this->errors} lỗi" : ""),
            'type' => 'import_completed'
        ]);
    }
}
