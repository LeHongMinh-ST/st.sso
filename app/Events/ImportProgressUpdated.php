<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportProgressUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public array $data;
    public int $userId;

    public function __construct(int $userId, array $data)
    {
        $this->userId = $userId;
        $this->data = $data;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('import.progress.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'import.progress.updated';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}
