<?php

declare(strict_types=1);

namespace App\Livewire\Role;

use App\Helpers\Constants;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function render()
    {
        $roles = Role::query()
            ->where('name', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(Constants::PER_PAGE);

        return view('livewire.role.index', [
            'roles' => $roles,
        ]);
    }
}
