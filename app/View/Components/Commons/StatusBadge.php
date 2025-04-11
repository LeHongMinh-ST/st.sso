<?php

declare(strict_types=1);

namespace App\View\Components\Commons;

use App\Enums\Status;
use Illuminate\View\Component;

class StatusBadge extends Component
{
    public Status $status;

    public function __construct(Status $status)
    {
        $this->status = $status;
    }

    public function render()
    {
        return view('components.commons.status-badge');
    }

    public function getBadgeClasses(): string
    {
        return match ($this->status) {
            Status::Active => 'bg-success text-success bg-opacity-20',
            Status::Inactive => 'bg-danger text-danger bg-opacity-20',
        };
    }
}
