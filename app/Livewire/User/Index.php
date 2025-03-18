<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Faculty;
use App\Helpers\Constants;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $facultyId = "";

    public string $role = "";

    public bool $isShowFilter = false;

    public function toogleFilter()
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
            ->paginate(Constants::PER_PAGE);

        return view('livewire.user.index', [
            'users' => $users,
            'faculties' => $faculties,
        ]);
    }

}

