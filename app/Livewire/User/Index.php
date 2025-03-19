<?php

declare(strict_types=1);

namespace App\Livewire\User;

use App\Helpers\Constants;
use App\Models\Faculty;
use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'faculty_id')]
    public string $facultyId = "";

    #[Url(as: 'role')]
    public string $role = "";

    public bool $isShowFilter = false;

    public function toogleFilter(): void
    {
        $this->isShowFilter = !$this->isShowFilter;
    }

    public function render()
    {
        $faculties = Faculty::all();

        $users = User::query()
            ->search($this->search)
            ->faculty($this->facultyId)
            ->role($this->role)
            ->with('faculty')
            ->orderBy('created_at', 'desc')
            ->paginate(Constants::PER_PAGE);

        return view('livewire.user.index', [
            'users' => $users,
            'faculties' => $faculties,
        ]);
    }

}
