<?php

declare(strict_types=1);

namespace App\View\Components\User;

use App\Enums\Role;
use Illuminate\View\Component;

class RoleBadge extends Component
{
    public Role $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    public function render()
    {
        return view('components.user.role-badge');
    }

    public function getBadgeClasses(): string
    {
        return match ($this->role) {
            Role::SuperAdmin => 'badge bg-light border-start border-width-3 text-body rounded-start-0 border-primary',
            Role::Officer => 'badge bg-light border-start border-width-3 text-body rounded-start-0 border-secondary',
            Role::Student => 'badge bg-light border-start border-width-3 text-body rounded-start-0 border-warning',
            Role::Normal => 'badge bg-light border-start border-width-3 text-body rounded-start-0 border-dark',
        };
    }
}
